import { existsSync } from "node:fs";
import path from "node:path";
import { watch } from "chokidar";
import { buildAllScss, wwwRoot } from "./scss-tooling.mjs";

const isRunningInDocker = existsSync("/.dockerenv");

let buildInProgress = false;
let buildQueued = false;

function isScssPath(filePath) {
    return filePath.toLowerCase().endsWith(".scss");
}

async function runBuild(reason) {
    if (buildInProgress) {
        buildQueued = true;
        return;
    }

    buildInProgress = true;

    try {
        console.log(`[scss:watch] ${reason}`);
        await buildAllScss();
    } catch (error) {
        console.error("[scss:watch] Build failed.");
        console.error(error);
    } finally {
        buildInProgress = false;

        if (buildQueued) {
            buildQueued = false;
            await runBuild("Running queued rebuild");
        }
    }
}

async function start() {
    await runBuild("Initial build");

    watch(wwwRoot, {
        ignoreInitial: true,
        usePolling: isRunningInDocker,
        interval: isRunningInDocker ? 512 : undefined,
        ignored: (watchPath, stats) => {
            if (!stats || !stats.isFile()) {
                return false;
            }

            return !isScssPath(watchPath);
        },
    })
        .on("add", (filePath) => {
            if (!isScssPath(filePath)) {
                return;
            }

            void runBuild(`Entry added: ${filePath}`);
        })
        .on("change", (filePath) => {
            if (!isScssPath(filePath)) {
                return;
            }

            void runBuild(`Changed: ${filePath}`);
        })
        .on("unlink", (filePath) => {
            if (!isScssPath(filePath)) {
                return;
            }

            void runBuild(`Entry removed: ${filePath}`);
        })
        .on("error", (error) => {
            console.error("[scss:watch] Watcher failed.");
            console.error(error);
            process.exitCode = 1;
        });

    console.log(
        `[scss:watch] Watching ${path.join(wwwRoot, "**", "*.scss")} (mode=${isRunningInDocker ? "polling" : "events"})`,
    );
}

start().catch((error) => {
    console.error("[scss:watch] Watch startup failed.");
    console.error(error);
    process.exitCode = 1;
});
