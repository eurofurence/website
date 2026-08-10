import { mkdir, readdir, stat, writeFile } from "node:fs/promises";
import path from "node:path";
import { fileURLToPath } from "node:url";
import { compile } from "sass";

const scriptDir = path.dirname(fileURLToPath(import.meta.url));
export const projectRoot = path.resolve(scriptDir, "..");
export const wwwRoot = path.join(projectRoot, "www");
const scssSourceRoot = path.join(wwwRoot, "scss");
const cssOutputRoot = path.join(wwwRoot, "css");

function isWithin(basePath, targetPath) {
    const relative = path.relative(basePath, targetPath);
    return relative && !relative.startsWith("..") && !path.isAbsolute(relative);
}

function isScssFileName(fileName) {
    return fileName.endsWith(".scss");
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
    const scssEntries = await collectEntryScssFiles();
    if (scssEntries.length === 0) {
        console.log("No SCSS entry files found under www. Nothing to build.");
        return 0;
    }

    for (const scssFile of scssEntries) {
        const outFile = getOutputPath(scssFile);
        const result = compile(scssFile, {
            style: "expanded",
            sourceMap: false,
            loadPaths: [path.dirname(scssFile), scssSourceRoot, wwwRoot, path.join(projectRoot, "node_modules")],
        });

        await mkdir(path.dirname(outFile), { recursive: true });
        await writeFile(outFile, result.css, "utf8");
        console.log(`Built ${path.relative(projectRoot, scssFile)} -> ${path.relative(projectRoot, outFile)}`);
    }

    return scssEntries.length;
}
