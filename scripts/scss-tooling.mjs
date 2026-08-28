import { mkdir, readdir, stat, writeFile } from "node:fs/promises";
import path from "node:path";
import { fileURLToPath } from "node:url";
import { config as loadDotEnv } from "dotenv";
import { compile } from "sass";

const scriptDir = path.dirname(fileURLToPath(import.meta.url));
export const projectRoot = path.resolve(scriptDir, "..");
export const wwwRoot = path.join(projectRoot, "www");
const scssSourceRoot = path.join(wwwRoot, "scss");
const cssOutputRoot = path.join(wwwRoot, "css");
const dotEnvPath = path.join(projectRoot, ".env");

loadDotEnv({ path: dotEnvPath, quiet: true });

function isTruthy(value) {
    return typeof value === "string" && /^(1|true)$/i.test(value.trim());
}

function isWithin(basePath, targetPath) {
    const relative = path.relative(basePath, targetPath);
    return relative && !relative.startsWith("..") && !path.isAbsolute(relative);
}

function isScssFileName(fileName) {
    return fileName.endsWith(".scss");
}

function toPosixPath(value) {
    return value.replace(/\\/g, "/");
}

function toWebSourcePath(filePath) {
    if (!isWithin(wwwRoot, filePath)) {
        return null;
    }

    const relativePath = toPosixPath(path.relative(wwwRoot, filePath));
    return `/${relativePath}`;
}

function normalizeSourceMapPaths(sourceMap, outFile) {
    if (!sourceMap || !Array.isArray(sourceMap.sources)) {
        return sourceMap;
    }

    const outDir = path.dirname(outFile);

    function toCssRelativeSourcePath(filePath) {
        const webSourcePath = toWebSourcePath(filePath);
        if (!webSourcePath) {
            return null;
        }

        const sourceAbsPath = path.join(wwwRoot, webSourcePath.slice(1));
        const relativePath = path.relative(outDir, sourceAbsPath);
        return toPosixPath(relativePath);
    }

    const normalizedSources = sourceMap.sources.map((source) => {
        if (typeof source !== "string") {
            return source;
        }

        // Keep package imports untouched. DevTools can still render them from sourcesContent
        if (/^[a-z]+:/i.test(source) && !source.startsWith("file:")) {
            return source;
        }

        if (source.startsWith("file:")) {
            try {
                const filePath = fileURLToPath(source);
                return toCssRelativeSourcePath(filePath) ?? source;
            } catch {
                return source;
            }
        }

        if (path.isAbsolute(source)) {
            return toCssRelativeSourcePath(source) ?? source;
        }

        return toPosixPath(source);
    });

    return {
        ...sourceMap,
        sourceRoot: "",
        sources: normalizedSources,
    };
}

function isEntryFileName(fileName) {
    return isScssFileName(fileName) && !fileName.startsWith("_");
}

export async function directoryExists(dirPath) {
    try {
        const info = await stat(dirPath);
        return info.isDirectory();
    } catch {
        return false;
    }
}

async function walkScssFiles(dirPath) {
    const files = [];
    const dirItems = await readdir(dirPath, { withFileTypes: true });

    for (const item of dirItems) {
        const fullPath = path.join(dirPath, item.name);

        if (item.isDirectory()) {
            files.push(...(await walkScssFiles(fullPath)));
            continue;
        }

        if (!item.isFile() || !isScssFileName(item.name)) {
            continue;
        }

        files.push(fullPath);
    }

    return files;
}

export async function collectAllScssFiles() {
    if (!(await directoryExists(wwwRoot))) {
        return [];
    }

    return walkScssFiles(wwwRoot);
}

export async function collectEntryScssFiles() {
    const allScssFiles = await collectAllScssFiles();
    return allScssFiles.filter((filePath) => isEntryFileName(path.basename(filePath))).sort((a, b) => a.localeCompare(b));
}

export function getOutputPath(scssFilePath) {
    if (isWithin(scssSourceRoot, scssFilePath)) {
        const relativePath = path.relative(scssSourceRoot, scssFilePath);
        return path.join(cssOutputRoot, relativePath.replace(/\.scss$/i, ".css"));
    }

    const relativeToWww = path.relative(wwwRoot, scssFilePath);
    return path.join(cssOutputRoot, relativeToWww.replace(/\.scss$/i, ".css"));
}

export async function buildAllScss() {
    const enableDevtoolsSourceMap = isTruthy(process.env.SCSS_DEVTOOLS);
    const scssEntries = await collectEntryScssFiles();
    if (scssEntries.length === 0) {
        console.log("No SCSS entry files found under www. Nothing to build.");
        return 0;
    }

    for (const scssFile of scssEntries) {
        const outFile = getOutputPath(scssFile);
        const result = compile(scssFile, {
            style: "expanded",
            sourceMap: enableDevtoolsSourceMap,
            sourceMapIncludeSources: enableDevtoolsSourceMap,
            silenceDeprecations: ["import"],
            loadPaths: [path.dirname(scssFile), scssSourceRoot, wwwRoot, path.join(projectRoot, "node_modules")],
        });

        await mkdir(path.dirname(outFile), { recursive: true });
        let cssOutput = result.css;

        if (enableDevtoolsSourceMap && result.sourceMap) {
            const normalizedMap = normalizeSourceMapPaths(result.sourceMap, outFile);
            await writeFile(`${outFile}.map`, JSON.stringify(normalizedMap), "utf8");
            cssOutput = `${cssOutput}\n/*# sourceMappingURL=${path.basename(outFile)}.map */\n`;
        }

        await writeFile(outFile, cssOutput, "utf8");

        console.log(`Built ${path.relative(projectRoot, scssFile)} -> ${path.relative(projectRoot, outFile)}`);
    }

    return scssEntries.length;
}
