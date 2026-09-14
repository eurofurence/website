(() => {
    const pageLifecycle = window.EFPageLifecycle || window;
    let requestController = null;
    let intervalIds = [];
    let injectedScript = null;
    let clearPageIntervals = () => {};

    function cleanup() {
        requestController?.abort();
        clearPageIntervals();

        if (injectedScript?.isConnected) {
            injectedScript.remove();
        }
        injectedScript = null;
    }

    async function loadStatisticsScript() {
        requestController = new AbortController();

        try {
            const response = await fetch("js/regstats.min.js", {
                credentials: "same-origin",
                signal: requestController.signal,
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const source = await response.text();
            if (requestController.signal.aborted) {
                return;
            }

            const pageIntervals = [];
            const originalSetInterval = window.setInterval;
            const originalClearInterval = window.clearInterval;
            window.setInterval = (...args) => {
                const id = originalSetInterval(...args);
                pageIntervals.push(id);
                return id;
            };

            try {
                const script = document.createElement("script");
                script.textContent = `(function () {\n${source}\n})();`;
                injectedScript = script;
                document.head.appendChild(script);
            } finally {
                window.setInterval = originalSetInterval;
                intervalIds = pageIntervals;
                clearPageIntervals = () => {
                    intervalIds.forEach((id) => originalClearInterval(id));
                    intervalIds = [];
                };
            }
        } catch (error) {
            if (error.name !== "AbortError") {
                console.error("[ef-regstats] failed to load page script", error);
            }
        }
    }

    pageLifecycle.addEventListener("load", loadStatisticsScript, { once: true });
    pageLifecycle.addEventListener("unload", cleanup, { once: true });
})();
