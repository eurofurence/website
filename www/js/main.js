// introduced by Xenor in 2023: make the background page unscrollable while the nav overlay is open
// document.getElementById("nav-state").addEventListener('change', function() {
//     document.getElementsByTagName("body")[0].style = "overflow: " + (this.checked? "hidden" : "auto") + ";"
// })

/* Consent Required - Click to Allow External Contents */
function initializeConsentCovers() {
    document.querySelectorAll(".consent-cover").forEach((container) => {
        if (container.__efConsentInitialized) {
            return;
        }

        container.__efConsentInitialized = true;
        container.addEventListener("click", () => {
            const elem = document.createElement(container.dataset.elementType);
            for (const attr in container.dataset) {
                if (attr === "elementType") continue;
                // console.info(`[consent cover] attr: setting ${attr.replace(/[A-Z]/g, m => "-" + m.toLowerCase())}="${container.dataset[attr]}"`);
                elem.setAttribute(
                    attr.replace(/[A-Z]/g, (m) => "-" + m.toLowerCase()),
                    container.dataset[attr],
                );
            }
            container.replaceWith(elem);
        });
    });
}

initializeConsentCovers();
document.addEventListener("ef:page-load", initializeConsentCovers);

/* Page Rating */
const rating = document.getElementById('rating-rating');
const stars = document.querySelectorAll('.page-rating-stars > *');
stars.forEach(star => {
    star.addEventListener('mouseenter', () => {
        stars.forEach(s => {
            s.classList.toggle('glowing', s.dataset.rating <= star.dataset.rating);
        });
    });

    star.addEventListener('click', () => {
        rating.value = star.dataset.rating;
    });

    star.addEventListener('mouseleave', () => {
        stars.forEach(s => {s.classList.toggle('glowing', s.dataset.rating <= rating.value)});
    });
});

/* Page Rating Success or Failure Message */
if (document.location.hash.substring(1) === 'rate-success') {
    UIkit.notification('Thank you! We will forward your rating to the appropriate department.', 'success');
}
if (document.location.hash.substring(1) === 'rate-failure') {
    UIkit.notification('Uhoh, something went wrong on our side. Please tell @draconigen on Telegram.', 'danger');
}

/* Back To Top (UIkit Totop) */
const toTopButton = document.getElementById('ef-to-top');
if (toTopButton) {
    toTopButton.addEventListener('click', (event) => {
        event.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    const configuredThreshold = Number.parseInt(toTopButton.dataset.threshold || '100', 10);
    const threshold = Number.isFinite(configuredThreshold) && configuredThreshold > 0 ? configuredThreshold : 100;
    const fadeRange = 48;
    let toTopTicking = false;

    const updateToTopVisibility = () => {
        toTopTicking = false;

        const scrollY = window.scrollY || document.documentElement.scrollTop || 0;
        const fadeStart = Math.max(0, threshold - fadeRange);
        const fadeEnd = threshold + fadeRange;
        const raw = (scrollY - fadeStart) / (fadeEnd - fadeStart);
        const opacity = Math.max(0, Math.min(1, raw));

        toTopButton.style.opacity = String(opacity);
        if (opacity > 0.01) {
            toTopButton.classList.add('ef-visible');
        } else {
            toTopButton.classList.remove('ef-visible');
        }
    };

    const scheduleToTopUpdate = () => {
        if (toTopTicking) {
            return;
        }
        toTopTicking = true;
        window.requestAnimationFrame(updateToTopVisibility);
    };

    window.addEventListener('scroll', scheduleToTopUpdate, { passive: true });
    window.addEventListener('resize', scheduleToTopUpdate);
    scheduleToTopUpdate();
}

/* Page navigation */
const efNavigation = (() => {
    const content = document.getElementById('content');
    const main = document.querySelector('main');
    let currentPageIdentity = getPageIdentity(new URL(window.location.href));
    let navigationController = null;

    if (!window.EFPageLifecycle) {
        window.EFPageLifecycle = new EventTarget();
    }

    if (!content || !main || !window.fetch || !window.history || !window.history.pushState) {
        return null;
    }

    function getPageIdentity(url) {
        return `${url.pathname}${url.search}`;
    }

    function isJavaScriptScript(script) {
        const type = (script.type || '').toLowerCase();
        return !type || type === 'text/javascript' || type === 'application/javascript' || type === 'module';
    }

    function isNavigableLink(link, event) {
        if (
            event.defaultPrevented ||
            event.button !== 0 ||
            event.metaKey ||
            event.ctrlKey ||
            event.shiftKey ||
            event.altKey ||
            link.target && link.target.toLowerCase() !== '_self' ||
            link.hasAttribute('download')
        ) {
            return false;
        }

        const url = new URL(link.href, document.baseURI);
        const lastSegment = url.pathname.split('/').pop() || '';

        if (
            url.origin !== window.location.origin ||
            url.protocol !== window.location.protocol ||
            url.searchParams.has('export') ||
            lastSegment.includes('.') && lastSegment !== 'index.php'
        ) {
            return false;
        }

        return getPageIdentity(url) !== currentPageIdentity;
    }

    function setMetaContent(selector, value) {
        const element = document.querySelector(selector);
        if (element && value !== null && value !== undefined) {
            element.setAttribute('content', String(value));
        }
    }

    function setLinkHref(selector, value) {
        const element = document.querySelector(selector);
        if (element && value) {
            element.setAttribute('href', value);
        }
    }

    function replaceRelLink(rel, value) {
        document.querySelectorAll(`link[rel="${rel}"]`).forEach((element) => element.remove());

        if (value) {
            const element = document.createElement('link');
            element.rel = rel;
            element.href = value;
            document.head.appendChild(element);
        }
    }

    function updatePageMetadata(page) {
        document.title = page.title;
        setMetaContent('meta[name="description"]', page.description);
        setMetaContent('meta[name="keywords"]', page.keywords);
        setMetaContent('meta[name="robots"]', page.robots);
        setMetaContent('meta[name="twitter:title"]', page.title);
        setMetaContent('meta[name="twitter:description"]', page.description);
        setMetaContent('meta[name="twitter:image"]', page.ogpImage);
        setMetaContent('meta[property="og:image"]', page.ogpImage);
        setMetaContent('meta[property="og:image:width"]', page.ogpImageWidth);
        setMetaContent('meta[property="og:image:height"]', page.ogpImageHeight);
        setMetaContent('meta[property="og:title"]', page.title);
        setMetaContent('meta[property="og:description"]', page.description);
        setMetaContent('meta[property="og:url"]', page.canonical);
        setLinkHref('link[rel="canonical"]', page.canonical);
        replaceRelLink('prev', page.previous);
        replaceRelLink('next', page.next);
    }

    async function loadExternalScriptSource(scriptUrl, requestController) {
        const response = await window.fetch(scriptUrl.href, {
            credentials: 'same-origin',
            signal: requestController.signal,
        });

        if (!response.ok) {
            throw new Error(`Failed to load page script: ${scriptUrl.href}`);
        }

        return response.text();
    }

    function wrapPageScript(scriptText, scriptUrl) {
        return `(function () {\n${scriptText}\n}).call(window);\n//# sourceURL=${scriptUrl.href}`;
    }

    function createAbortError() {
        const error = new Error('Page navigation was aborted');
        error.name = 'AbortError';
        return error;
    }

    function waitForScript(script, replacement, requestController) {
        if (!replacement.src && replacement.type !== 'module') {
            script.replaceWith(replacement);
            return Promise.resolve();
        }

        return new Promise((resolve, reject) => {
            let settled = false;
            const abortSignal = requestController.signal;
            const finish = () => {
                if (!settled) {
                    settled = true;
                    abortSignal.removeEventListener('abort', abort);
                    resolve();
                }
            };
            const fail = () => {
                if (!settled) {
                    settled = true;
                    abortSignal.removeEventListener('abort', abort);
                    reject(new Error(`Failed to load page script: ${script.src || 'inline script'}`));
                }
            };
            const abort = () => {
                replacement.remove();
                if (!settled) {
                    settled = true;
                    reject(createAbortError());
                }
            };

            replacement.addEventListener('load', finish, { once: true });
            replacement.addEventListener('error', fail, { once: true });
            abortSignal.addEventListener('abort', abort, { once: true });
            script.replaceWith(replacement);
        });
    }

    function ensureNavigationIsCurrent(requestController) {
        if (requestController.signal.aborted || navigationController !== requestController) {
            throw createAbortError();
        }
    }

    async function runPageScripts(requestController) {
        const scripts = [...content.querySelectorAll('script')];

        for (const script of scripts) {
            ensureNavigationIsCurrent(requestController);

            if (!isJavaScriptScript(script)) {
                continue;
            }

            const replacement = document.createElement('script');
            for (const attribute of script.attributes) {
                if (attribute.name !== 'src' && attribute.name !== 'type') {
                    replacement.setAttribute(attribute.name, attribute.value);
                }
            }

            if (script.src) {
                const scriptUrl = new URL(script.src, document.baseURI);
                const scriptType = (script.type || '').toLowerCase();
                const canIsolateScript =
                    scriptType !== 'module'
                    && scriptUrl.origin === window.location.origin
                    && !script.integrity;

                if (canIsolateScript) {
                    const source = await loadExternalScriptSource(scriptUrl, requestController);
                    ensureNavigationIsCurrent(requestController);
                    replacement.type = 'text/javascript';
                    replacement.textContent = wrapPageScript(source, scriptUrl);
                } else {
                    replacement.src = scriptUrl.href;
                    if (script.type) {
                        replacement.type = script.type;
                    }
                }
            } else {
                const scriptText = script.textContent || '';
                if ((script.type || '').toLowerCase() === 'module') {
                    replacement.type = 'module';
                    replacement.textContent = scriptText;
                } else {
                    replacement.type = 'text/javascript';
                    replacement.textContent = `(function () {\n${scriptText}\n})();`;
                }
            }

            await waitForScript(script, replacement, requestController);
            ensureNavigationIsCurrent(requestController);
        }
    }

    function scrollToPagePosition(url) {
        window.requestAnimationFrame(() => {
            if (url.hash) {
                const targetId = decodeURIComponent(url.hash.substring(1));
                const target = document.getElementById(targetId);
                if (target) {
                    target.scrollIntoView();
                    return;
                }
            }

            window.scrollTo(0, 0);
        });
    }

    function dispatchPageLoad(page) {
        window.EFPageLifecycle.dispatchEvent(new CustomEvent('load', { detail: page }));
        document.dispatchEvent(new CustomEvent('ef:page-load', { detail: page }));
        window.dispatchEvent(new CustomEvent('ef:page-load', { detail: page }));
    }

    async function loadPage(url, pushState) {
        if (getPageIdentity(url) === currentPageIdentity) {
            scrollToPagePosition(url);
            return;
        }

        if (navigationController) {
            navigationController.abort();
        }

        const requestController = new AbortController();
        navigationController = requestController;
        main.setAttribute('aria-busy', 'true');

        try {
            const response = await window.fetch(url.href, {
                credentials: 'same-origin',
                headers: {
                    'X-EF-Fragment': 'content',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: requestController.signal
            });

            if (!response.ok || !(response.headers.get('content-type') || '').includes('application/json')) {
                throw new Error(`Fragment request failed with status ${response.status}`);
            }

            const page = await response.json();
            if (!page || typeof page.content !== 'string') {
                throw new Error('Fragment response did not contain page content');
            }

            ensureNavigationIsCurrent(requestController);

            if (pushState) {
                window.history.pushState({ efPage: page.key }, '', url.href);
            }
            currentPageIdentity = getPageIdentity(url);
            window.EFPageLifecycle.dispatchEvent(new CustomEvent('unload', { detail: page }));
            window.EFPageLifecycle = new EventTarget();
            updatePageMetadata(page);
            document.getElementById('ef-nav-menu').innerHTML = page.menu;
            main.className = page.mainClass || '';
            content.innerHTML = page.content;
            await runPageScripts(requestController);
            ensureNavigationIsCurrent(requestController);

            if (window.UIkit && typeof window.UIkit.update === 'function') {
                window.UIkit.update(content);
            }

            dispatchPageLoad(page);
            scrollToPagePosition(url);
        } catch (error) {
            if (error.name !== 'AbortError') {
                window.location.assign(url.href);
            }
        } finally {
            if (navigationController === requestController) {
                main.removeAttribute('aria-busy');
                navigationController = null;
            }
        }
    }

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link || !isNavigableLink(link, event)) {
            return;
        }

        event.preventDefault();
        loadPage(new URL(link.href, document.baseURI), true);
    });

    window.addEventListener('popstate', () => {
        loadPage(new URL(window.location.href), false);
    });

    dispatchPageLoad({ initial: true });

    return { loadPage };
})();
