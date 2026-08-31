import { existsSync } from "node:fs";
import { readdir, readFile } from "node:fs/promises";
import path from "node:path";
import { fileURLToPath } from "node:url";

const scriptDir = path.dirname(fileURLToPath(import.meta.url));
const projectRoot = path.resolve(scriptDir, "..");
const wwwRoot = path.join(projectRoot, "www");
const scanExtensions = new Set([".html", ".php", ".js", ".mjs", ".ts", ".tsx"]);
const linkTagRegex = /<link\b[^>]*>/gi;
const quotedStringRegex = /(['"`])([^'"`\r\n]*)\1/g;
const unquotedImportUrlRegex = /@import\s+url\(\s*([^)]+?)\s*\)/g;

function getScanRoot() {
    const scanRootArg = process.argv.find((arg) => arg.startsWith("--scan-root="));
    if (!scanRootArg) {
        return wwwRoot;
    }

    const rawRoot = scanRootArg.slice("--scan-root=".length).trim();
    if (!rawRoot) {
        return wwwRoot;
    }

    // Resolve from project root so commands work the same on Windows and Linux
    return path.resolve(projectRoot, rawRoot);
}

function shouldScanFile(filePath) {
    return scanExtensions.has(path.extname(filePath).toLowerCase());
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

function toLineColumn(text, index) {
    const before = text.slice(0, index);
    const lines = before.split(/\r?\n/);
    return { line: lines.length, column: lines[lines.length - 1].length + 1 };
}

function isStylesheetLinkTag(tag) {
    return /\brel\s*=\s*(['"])stylesheet\1/i.test(tag);
}

function getHrefAttribute(tag) {
    const hrefMatch = /\bhref\s*=\s*(['"])([^'"]+)\1/i.exec(tag);
    return hrefMatch ? hrefMatch[2] : null;
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

function isInsideWww(candidatePath) {
    const relativeToWww = path.relative(wwwRoot, candidatePath);
    return !relativeToWww.startsWith("..") && !path.isAbsolute(relativeToWww);
}

function pathExists(filePath) {
    try {
        return existsSync(filePath) && true;
    } catch {
        return false;
    }
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
        const relativeToWww = path.relative(wwwRoot, exactPath);
        const insideWww = isInsideWww(exactPath);
        const cssEquivalentRelativePath = insideWww ? getCssEquivalentRelativePath(relativeToWww.replace(/\\/g, "/")) : null;
        const cssEquivalentPath = cssEquivalentRelativePath ? path.join(wwwRoot, cssEquivalentRelativePath) : null;
        const exactExists = pathExists(exactPath);

        if (exactExists && !normalizedPath.toLowerCase().endsWith(".scss")) {
            return {
                status: "ok",
                targetHref: href,
            };
        }

        if (cssEquivalentPath) {
            if (pathExists(cssEquivalentPath)) {
                return {
                    status: "rewrite",
                    targetHref: `${pathname.startsWith("/") ? "/" : ""}${cssEquivalentRelativePath}${suffix}`,
                };
            }
        }

        if (exactExists && normalizedPath.toLowerCase().endsWith(".scss") && cssEquivalentRelativePath) {
            return {
                status: "rewrite",
                targetHref: `${pathname.startsWith("/") ? "/" : ""}${cssEquivalentRelativePath}${suffix}`,
            };
        }
    }

    return {
        status: "missing",
        targetHref: null,
    };
}

function isIndexInRanges(index, ranges) {
    return ranges.some((range) => index >= range.start && index < range.end);
}

function collectQuotedScssReferenceIssues(filePath, content, ignoredRanges) {
    const issues = [];

    for (const stringMatch of content.matchAll(quotedStringRegex)) {
        const stringValue = stringMatch[2];
        if (!isLikelyScssPath(stringValue)) {
            continue;
        }

        const referenceIndex = stringMatch.index + stringMatch[0].indexOf(stringValue);
        // Avoid duplicate reports for hrefs that are already handled by <link> tags
        if (isIndexInRanges(referenceIndex, ignoredRanges)) {
            continue;
        }

        const resolved = resolveStylesheetReference(filePath, stringValue);
        if (!resolved || resolved.status === "ok") {
            continue;
        }

        const { line, column } = toLineColumn(content, referenceIndex);
        issues.push({
            kind: "string",
            href: stringValue,
            line,
            column,
            status: resolved.status,
            targetHref: resolved.targetHref,
        });
    }

    return issues;
}

function collectUnquotedImportUrlIssues(filePath, content) {
    const issues = [];

    for (const importMatch of content.matchAll(unquotedImportUrlRegex)) {
        const importPath = getUnquotedImportScssPath(importMatch[1]);
        if (!importPath) {
            continue;
        }

        const resolved = resolveStylesheetReference(filePath, importPath);
        if (!resolved || resolved.status === "ok") {
            continue;
        }

        const referenceIndex = importMatch.index + importMatch[0].indexOf(importPath);
        const { line, column } = toLineColumn(content, referenceIndex);
        issues.push({
            kind: "import",
            href: importPath,
            line,
            column,
            status: resolved.status,
            targetHref: resolved.targetHref,
        });
    }

    return issues;
}

function collectStylesheetIssues(filePath, content) {
    const issues = [];
    const linkTagRanges = [];

    for (const tagMatch of content.matchAll(linkTagRegex)) {
        const tag = tagMatch[0];
        linkTagRanges.push({
            start: tagMatch.index,
            end: tagMatch.index + tag.length,
        });

        if (!isStylesheetLinkTag(tag)) {
            continue;
        }

        const href = getHrefAttribute(tag);
        if (!href) {
            continue;
        }

        const resolved = resolveStylesheetReference(filePath, href);
        if (!resolved || resolved.status === "ok") {
            continue;
        }

        const hrefIndex = tagMatch.index + tag.indexOf(href);
        const { line, column } = toLineColumn(content, hrefIndex);
        issues.push({
            kind: "link",
            href,
            line,
            column,
            status: resolved.status,
            targetHref: resolved.targetHref,
        });
    }

    issues.push(...collectQuotedScssReferenceIssues(filePath, content, linkTagRanges));
    issues.push(...collectUnquotedImportUrlIssues(filePath, content));

    return issues;
}

async function main() {
    const scanRoot = getScanRoot();
    const files = await walk(scanRoot);
    const violations = [];

    for (const filePath of files) {
        const content = await readFile(filePath, "utf8");
        const issues = collectStylesheetIssues(filePath, content);

        for (const issue of issues) {
            const replacementProposal = issue.targetHref ? ` -> ${issue.targetHref}` : "";
            const issueTypeLabel =
                issue.kind === "link" ? "stylesheet href" : issue.kind === "import" ? "stylesheet import" : "scss reference";
            violations.push(
                `${path.relative(projectRoot, filePath)}:${issue.line}:${issue.column}` +
                `${issueTypeLabel} (${issue.href}${replacementProposal})`,
            );
        }
    }

    if (violations.length > 0) {
        console.error("Found references that do not resolve to the compiled CSS output:");
        for (const violation of violations) {
            console.error(`- ${violation}`);
        }
        process.exitCode = 1;
        return;
    }

    console.log("No runtime scss reference issues found.");
}

main().catch((error) => {
    console.error("Style reference check failed.");
    console.error(error);
    process.exitCode = 1;
});
