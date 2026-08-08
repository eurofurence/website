import { existsSync } from "node:fs";
import { readdir, readFile, writeFile } from "node:fs/promises";
import path from "node:path";
import { fileURLToPath } from "node:url";

const scriptDir = path.dirname(fileURLToPath(import.meta.url));
const projectRoot = path.resolve(scriptDir, "..");
const wwwRoot = path.join(projectRoot, "www");
const scanExtensions = new Set([".html", ".php", ".js", ".mjs", ".ts", ".tsx"]);
const linkTagRegex = /<link\b[^>]*>/gi;
const quotedStringRegex = /(['"`])([^'"`\r\n]*)\1/g;
const unquotedImportUrlRegex = /@import\s+url\(\s*([^)]+?)\s*\)/g;

function shouldScanFile(filePath) {
    return scanExtensions.has(path.extname(filePath).toLowerCase());
}

function isStylesheetLinkTag(tag) {
    return /\brel\s*=\s*(['"])stylesheet\1/i.test(tag);
}

function getHrefAttribute(tag) {
    const hrefMatch = /\bhref\s*=\s*(['"])([^'"]+)\1/i.exec(tag);
    return hrefMatch ? { quote: hrefMatch[1], href: hrefMatch[2] } : null;
}

function splitHref(href) {
    const splitIndex = href.search(/[?#]/);
    if (splitIndex === -1) {
        return { pathname: href, suffix: "" };
    }

    return {
        pathname: href.slice(0, splitIndex),
        suffix: href.slice(splitIndex),
    };
}

function normalizeHrefPathname(pathname) {
    return pathname.replace(/^\/+/, "").replace(/^\.\//, "");
}

function getCssEquivalentRelativePath(relativePath) {
    const cssPath = relativePath.replace(/\.scss$/i, ".css");

    if (cssPath.startsWith("css/")) {
        return cssPath;
    }

    if (cssPath.startsWith("scss/")) {
        return path.posix.join("css", cssPath.slice(5));
    }

    return path.posix.join("css", cssPath);
}

function isLikelyScssPath(value) {
    const normalizedValue = value.trim();
    return /(?:^|\/)[^/?#]+\.scss(?:[?#][^\s]*)?$/i.test(normalizedValue);
}

function getUnquotedImportScssPath(rawImportPath) {
    const candidate = rawImportPath.trim();
    if (!candidate) {
        return null;
    }

    if (/^['"`]|['"`]$/.test(candidate)) {
        return null;
    }

    return isLikelyScssPath(candidate) ? candidate : null;
}

function isInsideWWW(candidatePath) {
    const relativeToWWW = path.relative(wwwRoot, candidatePath);
    return !relativeToWWW.startsWith("..") && !path.isAbsolute(relativeToWWW);
}

function pathExists(filePath) {
    return existsSync(filePath);
}

function resolveStylesheetReference(filePath, href) {
    const { pathname, suffix } = splitHref(href);
    const normalizedPath = normalizeHrefPathname(pathname);
    if (!normalizedPath || /^[a-z]+:/i.test(pathname) || pathname.startsWith("//")) {
        return null;
    }

    const candidatePaths = [];
    if (pathname.startsWith("/")) {
        candidatePaths.push(path.join(wwwRoot, normalizedPath));
    } else {
        candidatePaths.push(path.resolve(path.dirname(filePath), normalizedPath));
        candidatePaths.push(path.join(wwwRoot, normalizedPath));
    }

    for (const exactPath of candidatePaths) {
        const relativeToWWW = path.relative(wwwRoot, exactPath);
        const insideWWW = isInsideWWW(exactPath);
        const cssEquivalentRelativePath = insideWWW ? getCssEquivalentRelativePath(relativeToWWW.replace(/\\/g, "/")) : null;
        const cssEquivalentPath = cssEquivalentRelativePath ? path.join(wwwRoot, cssEquivalentRelativePath) : null;
        const exactExists = pathExists(exactPath);

        if (exactExists && !normalizedPath.toLowerCase().endsWith(".scss")) {
            return { status: "ok", targetHref: href };
        }

        if (cssEquivalentPath && pathExists(cssEquivalentPath)) {
            return {
                status: "rewrite",
                targetHref: `${pathname.startsWith("/") ? "/" : ""}${cssEquivalentRelativePath}${suffix}`,
            };
        }

        if (exactExists && normalizedPath.toLowerCase().endsWith(".scss") && cssEquivalentRelativePath) {
            return {
                status: "rewrite",
                targetHref: `${pathname.startsWith("/") ? "/" : ""}${cssEquivalentRelativePath}${suffix}`,
            };
        }
    }

    return { status: "missing", targetHref: null };
}

function isIndexInRanges(index, ranges) {
    return ranges.some((range) => index >= range.start && index < range.end);
}

function rewriteQuotedScssReferences(filePath, content, ignoredRanges) {
    let changed = false;

    const updatedContent = content.replace(quotedStringRegex, (match, quote, stringValue, offset) => {
        if (!isLikelyScssPath(stringValue)) {
            return match;
        }

        const stringIndex = offset + match.indexOf(stringValue);
        if (isIndexInRanges(stringIndex, ignoredRanges)) {
            return match;
        }

        const resolved = resolveStylesheetReference(filePath, stringValue);
        if (!resolved || resolved.status !== "rewrite") {
            return match;
        }

        changed = true;
        return `${quote}${resolved.targetHref}${quote}`;
    });

    return { changed, updatedContent };
}

function rewriteUnquotedImportUrlReferences(filePath, content) {
    let changed = false;

    const updatedContent = content.replace(unquotedImportUrlRegex, (match, importPathRaw) => {
        const importPath = getUnquotedImportScssPath(importPathRaw);
        if (!importPath) {
            return match;
        }

        const resolved = resolveStylesheetReference(filePath, importPath);
        if (!resolved || resolved.status !== "rewrite") {
            return match;
        }

        changed = true;
        return match.replace(importPath, resolved.targetHref);
    });

    return { changed, updatedContent };
}

function hasUnresolvedNonLinkScssReference(filePath, content, ignoredRanges) {
    for (const stringMatch of content.matchAll(quotedStringRegex)) {
        const stringValue = stringMatch[2];
        if (!isLikelyScssPath(stringValue)) {
            continue;
        }

        const stringIndex = stringMatch.index + stringMatch[0].indexOf(stringValue);
        if (isIndexInRanges(stringIndex, ignoredRanges)) {
            continue;
        }

        const resolved = resolveStylesheetReference(filePath, stringValue);
        if (resolved && resolved.status === "missing") {
            return true;
        }
    }

    for (const importMatch of content.matchAll(unquotedImportUrlRegex)) {
        const importPath = getUnquotedImportScssPath(importMatch[1]);
        if (!importPath) {
            continue;
        }

        const resolved = resolveStylesheetReference(filePath, importPath);
        if (resolved && resolved.status === "missing") {
            return true;
        }
    }

    return false;
}

function rewriteStylesheetLinks(filePath, content) {
    let changed = false;
    const linkTagRanges = [];

    const updatedContent = content.replace(linkTagRegex, (tag, offset) => {
        linkTagRanges.push({ start: offset, end: offset + tag.length });

        if (!isStylesheetLinkTag(tag)) {
            return tag;
        }

        const hrefInfo = getHrefAttribute(tag);
        if (!hrefInfo) {
            return tag;
        }

        const resolved = resolveStylesheetReference(filePath, hrefInfo.href);
        if (!resolved || resolved.status === "ok") {
            return tag;
        }

        if (resolved.status === "missing") {
            return tag;
        }

        changed = true;
        return tag.replace(/\bhref\s*=\s*(['"])([^'"]+)\1/i, `href=${hrefInfo.quote}${resolved.targetHref}${hrefInfo.quote}`);
    });

    return { changed, updatedContent, linkTagRanges };
}

async function walk(dirPath) {
    const files = [];
    const entries = await readdir(dirPath, { withFileTypes: true });

    for (const entry of entries) {
        if (entry.isDirectory() && entry.name === "__mocks__") {
            continue;
        }

        const fullPath = path.join(dirPath, entry.name);

        if (entry.isDirectory()) {
            files.push(...(await walk(fullPath)));
            continue;
        }

        if (entry.isFile() && shouldScanFile(fullPath)) {
            files.push(fullPath);
        }
    }

    return files;
}

async function main() {
    const files = await walk(wwwRoot);
    let changedFiles = 0;
    let unresolvedFiles = 0;

    for (const filePath of files) {
        const content = await readFile(filePath, "utf8");
        const linkRewrite = rewriteStylesheetLinks(filePath, content);
        const quotedRewrite = rewriteQuotedScssReferences(filePath, linkRewrite.updatedContent, linkRewrite.linkTagRanges);
        const importRewrite = rewriteUnquotedImportUrlReferences(filePath, quotedRewrite.updatedContent);
        const changed = linkRewrite.changed || quotedRewrite.changed || importRewrite.changed;
        const updatedContent = importRewrite.updatedContent;

        if (changed) {
            await writeFile(filePath, updatedContent, "utf8");
            changedFiles += 1;
            console.log(`Updated ${path.relative(projectRoot, filePath)}`);
        } else {
            const unresolved = (content.match(linkTagRegex) ?? []).some((tag) => {
                if (!isStylesheetLinkTag(tag)) {
                    return false;
                }

                const hrefInfo = getHrefAttribute(tag);
                if (!hrefInfo) {
                    return false;
                }

                const resolved = resolveStylesheetReference(filePath, hrefInfo.href);
                return resolved && resolved.status === "missing";
            });

            const unresolvedNonLink = hasUnresolvedNonLinkScssReference(filePath, content, linkRewrite.linkTagRanges);

            if (unresolved || unresolvedNonLink) {
                unresolvedFiles += 1;
                console.warn(`Manual attention required in ${path.relative(projectRoot, filePath)}`);
            }
        }
    }

    console.log(`Finished. Updated ${changedFiles} file(s).`);
    if (unresolvedFiles > 0) {
        console.error(`Unable to resolve stylesheet targets in ${unresolvedFiles} file(s).`);
        process.exitCode = 1;
    }
}

main().catch((error) => {
    console.error("Style reference fix failed.");
    console.error(error);
    process.exitCode = 1;
});
