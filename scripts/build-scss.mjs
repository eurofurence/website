import { buildAllScss } from "./scss-tooling.mjs";

buildAllScss().catch((error) => {
    console.error("SCSS build failed.");
    console.error(error);
    process.exitCode = 1;
});
