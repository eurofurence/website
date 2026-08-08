import { spawnSync } from "node:child_process";
import path from "node:path";
import { fileURLToPath } from "node:url";

const scriptDir = path.dirname(fileURLToPath(import.meta.url));
const projectRoot = path.resolve(scriptDir, "..");

function runScript(scriptName) {
    const result = spawnSync(process.execPath, [path.join("scripts", scriptName)], {
        cwd: projectRoot,
        stdio: "inherit",
    });

    return typeof result.status === "number" ? result.status : 1;
}

function main() {
    const initialCheck = runScript("style-reference-check.mjs");
    if (initialCheck === 0) {
        return;
    }

    console.warn("Style reference check failed. Attempting automatic fix.");
    const fixStatus = runScript("style-reference-fix.mjs");
    if (fixStatus !== 0) {
        console.error("Automatic style reference fix failed. Manual fix required.");
        process.exitCode = fixStatus;
        return;
    }

    const finalCheck = runScript("style-reference-check.mjs");
    if (finalCheck !== 0) {
        console.error("Style reference issues remain after automatic fix. Manual fix required.");
        process.exitCode = finalCheck;
    }
}

main();
