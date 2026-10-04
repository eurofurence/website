const JobsPageData = Object.freeze({
    SEARCH_DEBOUNCE_MS: 256,
});

class JobsPage {
    #modalRoot = null;
    #modalContent = null;
    #modalMeta = null;
    #foundCount = null;
    #searchInput = null;
    #emptyState = null;
    #accordionRoot = null;
    #expandAllButton = null;
    #collapseAllButton = null;
    #modal = null;
    #accordion = null;
    #selectedJobId = "";
    #searchDebounceTimer = null;
    #cardDataById = new Map();
    #eventsBound = false;

    constructor(options) {
        this.#modalRoot = options.modalRoot;
        this.#modalContent = options.modalContent;
        this.#modalMeta = options.modalMeta;
        this.#foundCount = options.foundCount;
        this.#searchInput = options.searchInput;
        this.#emptyState = options.emptyState;
        this.#accordionRoot = options.accordionRoot;
        this.#expandAllButton = options.expandAllButton;
        this.#collapseAllButton = options.collapseAllButton;
    }

    build() {
        if (!this.#requiredElementsPresent()) {
            return;
        }

        if (typeof UIkit === "undefined" || !UIkit.modal || !UIkit.accordion) {
            return;
        }

        this.#modal = UIkit.modal(this.#modalRoot);
        this.#accordion = UIkit.accordion(this.#accordionRoot);
        this.#indexCards();
        this.#bindEvents();
        this.#applySearchFilter();
        this.#syncModalWithHash();
    }

    #requiredElementsPresent() {
        return (
            this.#modalRoot &&
            this.#modalContent &&
            this.#modalMeta &&
            this.#foundCount &&
            this.#searchInput &&
            this.#emptyState &&
            this.#accordionRoot &&
            this.#expandAllButton &&
            this.#collapseAllButton
        );
    }

    #indexCards() {
        this.#cardDataById.clear();
        this.#accordionRoot.querySelectorAll(".ef-job-card").forEach((card) => {
            const jobId = card.getAttribute("data-job-id") || "";
            if (!jobId) {
                return;
            }

            this.#cardDataById.set(jobId, {
                card,
                modified: card.getAttribute("data-job-modified") || "",
            });
        });
    }

    #getAllAccordionItems() {
        return Array.from(this.#accordionRoot.children).filter((child) => child.tagName === "LI");
    }

    #getVisibleAccordionItems() {
        return this.#getAllAccordionItems().filter((item) => !item.hidden);
    }

    #formatPositionCount(positions, newPositions) {
        const suffix = positions === 1 ? "" : "s";
        const newPositionsText = newPositions > 0 ? ` (${newPositions} new)` : "";
        return `${positions} position${suffix}${newPositionsText}`;
    }

    #updateFoundCount(visibleTotal) {
        const suffix = visibleTotal === 1 ? "" : "s";
        this.#foundCount.textContent = `Showing ${visibleTotal} position${suffix}`;
    }

    #applySearchFilter() {
        const query = (this.#searchInput.value || "").trim().toLowerCase();
        const hasQuery = query.length > 0;
        let visibleTotal = 0;

        this.#getAllAccordionItems().forEach((departmentItem) => {
            let visibleDepartmentCount = 0;
            let visibleDepartmentNewCount = 0;

            departmentItem.querySelectorAll(".ef-job-card").forEach((card) => {
                const tile = card.closest(".ef-job-tile");
                if (!tile) {
                    return;
                }

                const searchBlob = (card.getAttribute("data-job-search") || "").toLowerCase();
                const matches = !query || searchBlob.includes(query);
                tile.hidden = !matches;

                if (matches) {
                    visibleDepartmentCount += 1;
                    visibleTotal += 1;
                    if (card.getAttribute("data-job-new") === "1") {
                        visibleDepartmentNewCount += 1;
                    }
                }
            });

            const countNode = departmentItem.querySelector(".ef-job-count");
            if (countNode) {
                countNode.textContent = this.#formatPositionCount(visibleDepartmentCount, visibleDepartmentNewCount);
            }

            departmentItem.hidden = visibleDepartmentCount === 0;
        });

        if (hasQuery) {
            this.#getVisibleAccordionItems().forEach((section) => {
                if (!section.classList.contains("uk-open")) {
                    const sectionTitle = section.querySelector(".uk-accordion-title");
                    sectionTitle?.click();
                }
            });
        }

        this.#updateFoundCount(visibleTotal);
        this.#emptyState.hidden = visibleTotal !== 0;
        this.#updateToggleState();
    }

    #updateToggleState() {
        const items = this.#getVisibleAccordionItems();
        if (items.length === 0) {
            this.#expandAllButton.disabled = true;
            this.#collapseAllButton.disabled = true;
            return;
        }

        const allExpanded = items.every((item) => item.classList.contains("uk-open"));
        const allCollapsed = items.every((item) => !item.classList.contains("uk-open"));
        this.#expandAllButton.disabled = allExpanded;
        this.#collapseAllButton.disabled = allCollapsed;
    }

    #getJobIdFromHash() {
        const rawHash = window.location.hash;
        if (!rawHash || rawHash === "#") {
            return "";
        }

        const remainder = rawHash.slice(1).trim();
        if (!remainder || remainder.includes("/")) {
            return "";
        }

        try {
            return decodeURIComponent(remainder);
        } catch {
            return "";
        }
    }

    #updateJobHash(jobId, replace = false) {
        if (!window.history || !window.history.pushState || !window.history.replaceState) {
            return;
        }

        const nextHash = `#${encodeURIComponent(jobId)}`;
        const nextUrl = `${window.location.pathname}${window.location.search}${nextHash}`;
        if (replace) {
            window.history.replaceState({}, "", nextUrl);
            return;
        }

        window.history.pushState({}, "", nextUrl);
    }

    #clearJobHash() {
        if (!window.location.hash) {
            return;
        }

        if (window.history && window.history.replaceState) {
            window.history.replaceState({}, "", window.location.pathname + window.location.search);
        }
    }

    #openModalForJobId(jobId, pushHistory) {
        if (!jobId) {
            return;
        }

        const cardData = this.#cardDataById.get(jobId);
        const template = document.getElementById(`ef-job-content-${jobId}`);
        if (!cardData || !template) {
            return;
        }

        const departmentSection = cardData.card.closest("li");
        if (departmentSection && !departmentSection.classList.contains("uk-open")) {
            this.#accordion.toggle(departmentSection, false);
        }

        this.#modalContent.innerHTML = template.innerHTML;
        this.#modalMeta.textContent = cardData.modified ? `Last updated: ${cardData.modified}` : "";
        this.#selectedJobId = jobId;

        if (pushHistory && this.#getJobIdFromHash() !== jobId) {
            this.#updateJobHash(jobId);
        }

        this.#modal.show();
    }

    #syncModalWithHash() {
        const jobIdFromHash = this.#getJobIdFromHash();
        if (!jobIdFromHash) {
            if (this.#modalRoot.classList.contains("uk-open")) {
                this.#modal.hide();
            }
            return;
        }

        if (jobIdFromHash === this.#selectedJobId && this.#modalRoot.classList.contains("uk-open")) {
            return;
        }

        this.#openModalForJobId(jobIdFromHash, false);
    }

    #bindEvents() {
        if (this.#eventsBound) {
            return;
        }

        this.#searchInput.addEventListener("input", () => {
            if (this.#searchDebounceTimer !== null) {
                clearTimeout(this.#searchDebounceTimer);
            }

            this.#searchDebounceTimer = window.setTimeout(() => {
                this.#searchDebounceTimer = null;
                this.#applySearchFilter();
            }, JobsPageData.SEARCH_DEBOUNCE_MS);
        });

        this.#expandAllButton.addEventListener("click", () => {
            if (this.#expandAllButton.disabled) {
                return;
            }

            this.#getVisibleAccordionItems().forEach((item) => {
                if (!item.classList.contains("uk-open")) {
                    this.#accordion.toggle(item, false);
                }
            });
            this.#updateToggleState();
        });

        this.#collapseAllButton.addEventListener("click", () => {
            if (this.#collapseAllButton.disabled) {
                return;
            }

            this.#getVisibleAccordionItems().forEach((item) => {
                if (item.classList.contains("uk-open")) {
                    this.#accordion.toggle(item, false);
                }
            });
            this.#updateToggleState();
        });

        this.#accordionRoot.addEventListener("shown", () => this.#updateToggleState());
        this.#accordionRoot.addEventListener("hidden", () => this.#updateToggleState());

        this.#accordionRoot.addEventListener("click", (event) => {
            const title = event.target.closest(".uk-accordion-title");
            if (title) {
                event.preventDefault();
            }

            const card = event.target.closest(".ef-job-card");
            if (!card) {
                return;
            }

            const jobId = card.getAttribute("data-job-id") || "";
            event.preventDefault();
            event.stopPropagation();
            this.#openModalForJobId(jobId, true);
        });

        this.#accordionRoot.addEventListener("keydown", (event) => {
            if (event.key !== "Enter" && event.key !== " ") {
                return;
            }

            const card = event.target.closest(".ef-job-card");
            if (!card) {
                return;
            }

            const jobId = card.getAttribute("data-job-id") || "";
            event.preventDefault();
            this.#openModalForJobId(jobId, true);
        });

        this.#modalRoot.addEventListener("hidden", () => {
            this.#selectedJobId = "";
            this.#modalMeta.textContent = "";
            this.#clearJobHash();
        });

        this.#modalRoot.addEventListener("shown", () => {
            if (this.#selectedJobId && this.#getJobIdFromHash() !== this.#selectedJobId) {
                this.#updateJobHash(this.#selectedJobId, true);
            }
        });

        window.addEventListener("popstate", () => this.#syncModalWithHash());

        this.#eventsBound = true;
    }
}

const jobsPage = new JobsPage({
    modalRoot: document.getElementById("ef-job-modal"),
    modalContent: document.getElementById("ef-job-modal-content"),
    modalMeta: document.getElementById("ef-job-modal-meta"),
    foundCount: document.getElementById("ef-jobs-found-count"),
    searchInput: document.getElementById("ef-jobs-search"),
    emptyState: document.getElementById("ef-jobs-empty"),
    accordionRoot: document.querySelector(".ef-jobs-accordion"),
    expandAllButton: document.getElementById("ef-jobs-expand-all"),
    collapseAllButton: document.getElementById("ef-jobs-collapse-all"),
});

window.addEventListener("load", () => jobsPage.build(), { once: true });
