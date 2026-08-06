const Status = Object.freeze({
    LOST: "L",
    FOUND: "F",
    RETURNED: "R", // items with this status shouldn't be returned by the API but we filter them out just in case
    UNKNOWN: "U", // not returned by API, used as fallback
});

const StatusMeta = Object.freeze({
    [Status.LOST]: { label: "Lost", cssClass: "lf-label--lost" },
    [Status.FOUND]: { label: "Found", cssClass: "lf-label--found" },
    [Status.RETURNED]: { label: "Returned", cssClass: "lf-label--returned" },
    [Status.UNKNOWN]: { label: "Unknown", cssClass: "lf-label--unknown" },
});

const StateType = Object.freeze({
    LOADING: "loading",
    ERROR: "error",
    EMPTY: "empty",
});

const StatusFilter = Object.freeze({
    ALL: "all",
    LOST: Status.LOST,
    FOUND: Status.FOUND,
    RETURNED: Status.RETURNED,
    UNKNOWN: Status.UNKNOWN,
});

const YearFilter = Object.freeze({
    ALL: "all",
});

const LFPageData = Object.freeze({
    PAGE_SIZE_KEY: "eflf.pageSize",
    DEFAULT_PAGE_SIZE: 36,
    SEARCH_DEBOUNCE_MS: 256,
    VALID_PAGE_SIZES: [12, 24, 36, 48, 60, 120],
    MAX_PAGES_TO_SHOW: 5,

    loadPageSize() {
        const stored = parseInt(localStorage.getItem(this.PAGE_SIZE_KEY), 10);
        return this.VALID_PAGE_SIZES.includes(stored) ? stored : this.DEFAULT_PAGE_SIZE;
    },

    savePageSize(size) {
        localStorage.setItem(this.PAGE_SIZE_KEY, size);
    },
});

class LostAndFound {
    #config = {
        baseUrl: "https://www.eurofurence.org/data/lf",
        noImageUrl: "img/pages/lostandfound/no-photo.png",
    };
    #env = window.__EF_ENVIRONMENT__ || { USE_MOCK_DATA: false, MOCK_LF_DATA: "__mocks__/lostandfound.mock.json" };
    #container = null;
    #itemsContainer = null;
    #stateContainer = null;
    #searchInput = null;
    #searchSuggestions = null;
    #statusFilter = null;
    #yearFilter = null;
    #summaryContainer = null;
    #pageSizeSelect = null;
    #paginationTop = null;
    #paginationBottom = null;
    #modalRoot = null;
    #modalImageLink = null;
    #modalImage = null;
    #modalStatus = null;
    #modalTitle = null;
    #modalId = null;
    #modalDescription = null;
    #modalTimeline = null;
    #items = [];
    #filteredItems = [];
    #currentPage = 1;
    #pageSize = LFPageData.loadPageSize();
    #searchDebounceTimer = null;
    #selectedItemId = "";
    #eventsBound = false;

    constructor(options) {
        this.#container = options.container;
        this.#itemsContainer = options.itemsContainer;
        this.#stateContainer = options.stateContainer;
        this.#searchInput = options.searchInput;
        this.#searchSuggestions = options.searchSuggestions;
        this.#statusFilter = options.statusFilter;
        this.#yearFilter = options.yearFilter;
        this.#summaryContainer = options.summaryContainer;
        this.#pageSizeSelect = options.pageSizeSelect;
        this.#paginationTop = options.paginationTop;
        this.#paginationBottom = options.paginationBottom;
        this.#modalRoot = options.modalRoot;
        this.#modalImageLink = options.modalImageLink;
        this.#modalImage = options.modalImage;
        this.#modalStatus = options.modalStatus;
        this.#modalTitle = options.modalTitle;
        this.#modalId = options.modalId;
        this.#modalDescription = options.modalDescription;
        this.#modalTimeline = options.modalTimeline;
    }

    async build() {
        if (!this.#itemsContainer) {
            console.error("[eflf] Items target container not found.");
            return;
        }

        this.#renderState(StateType.LOADING, "Loading lost and found items...");

        const payload = await this.#fetch(`${this.#config.baseUrl}/data.json`);
        if (!payload || !Array.isArray(payload.data)) {
            this.#itemsContainer.replaceChildren();
            this.#renderState(StateType.ERROR, "Could not load lost and found items. Please try again later.");
            return;
        }

        this.#items = payload.data
            .map((item) => this.#normalizeItem(item))
            .filter((item) => item.status !== Status.RETURNED) // make sure to not show returned items
            .sort((left, right) => {
                if (left.sortTimestamp !== right.sortTimestamp) {
                    return right.sortTimestamp - left.sortTimestamp;
                }
                return right.numericId - left.numericId;
            });

        this.#syncPageSizeSelect();
        this.#bindEvents();
        this.#bindModalEvents();
        this.#applyFilters();
        this.#openModalFromUrl();
    }

    #bindEvents() {
        if (this.#eventsBound) {
            return;
        }

        const clearSearchDebounce = () => {
            if (this.#searchDebounceTimer !== null) {
                clearTimeout(this.#searchDebounceTimer);
                this.#searchDebounceTimer = null;
            }
        };

        const resetAndFilter = () => {
            this.#currentPage = 1;
            this.#applyFilters();
        };

        if (this.#searchInput) {
            this.#searchInput.addEventListener("input", () => {
                clearSearchDebounce();
                this.#searchDebounceTimer = setTimeout(() => {
                    this.#searchDebounceTimer = null;
                    resetAndFilter();
                }, LFPageData.SEARCH_DEBOUNCE_MS);
            });
        }

        if (this.#statusFilter) {
            this.#statusFilter.addEventListener("change", () => {
                clearSearchDebounce();
                resetAndFilter();
            });
        }

        if (this.#yearFilter) {
            this.#yearFilter.addEventListener("change", () => {
                clearSearchDebounce();
                resetAndFilter();
            });
        }

        if (this.#pageSizeSelect) {
            this.#pageSizeSelect.addEventListener("change", () => {
                clearSearchDebounce();
                this.#pageSize = parseInt(this.#pageSizeSelect.value, 10);
                LFPageData.savePageSize(this.#pageSize);
                resetAndFilter();
            });
        }

        this.#eventsBound = true;
    }

    #applyFilters() {
        const query = this.#searchInput ? this.#searchInput.value.trim().toLowerCase() : "";
        const status = this.#statusFilter ? this.#statusFilter.value : StatusFilter.ALL;
        const year = this.#yearFilter ? this.#yearFilter.value : "all";

        const syncedFilters = this.#syncFilterOptions(query, status, year);
        const activeStatus = syncedFilters.status;
        const activeYear = syncedFilters.year;

        if (this.#statusFilter && this.#statusFilter.value !== activeStatus) {
            this.#statusFilter.value = activeStatus;
        }
        if (this.#yearFilter && this.#yearFilter.value !== activeYear) {
            this.#yearFilter.value = activeYear;
        }

        this.#filteredItems = this.#filterItems(query, activeStatus, activeYear);

        this.#renderItems();
        this.#renderPagination();
        this.#renderSummary();
        this.#renderSuggestions(query);

        if (this.#filteredItems.length === 0) {
            this.#renderState(StateType.EMPTY, "No items match your current filters.");
            return;
        }

        this.#renderState(null, null);
    }

    #filterItems(query, status, year) {
        return this.#items.filter((item) => {
            const statusMatches = status === StatusFilter.ALL || item.status === status;
            const queryMatches = !query || item.searchBlob.includes(query);
            const yearMatches = year === "all" || item.years.has(year);
            return statusMatches && queryMatches && yearMatches;
        });
    }

    #syncFilterOptions(query, status, year) {
        const statusFacetItems = this.#filterItems(query, StatusFilter.ALL, year);
        const yearFacetItems = this.#filterItems(query, status, YearFilter.ALL);
        this.#renderStatusFilterOptions(this.#countStatuses(statusFacetItems), status);
        this.#renderYearFilterOptions(this.#countYears(yearFacetItems), year);

        return { status, year };
    }

    #countStatuses(items) {
        const counts = new Map([
            [Status.LOST, 0],
            [Status.FOUND, 0],
            [Status.UNKNOWN, 0],
        ]);

        items.forEach((item) => {
            counts.set(item.status, (counts.get(item.status) ?? 0) + 1);
        });

        return counts;
    }

    #countYears(items) {
        const counts = new Map();
        items.forEach((item) => {
            item.years.forEach((itemYear) => {
                counts.set(itemYear, (counts.get(itemYear) ?? 0) + 1);
            });
        });

        return counts;
    }

    #allYearsSorted() {
        const years = new Set();
        this.#items.forEach((item) => {
            item.years.forEach((itemYear) => {
                years.add(itemYear);
            });
        });

        return [...years].sort((left, right) => right.localeCompare(left));
    }

    #renderStatusFilterOptions(counts, selectedStatus) {
        if (!this.#statusFilter) {
            return;
        }

        const previous = selectedStatus || this.#statusFilter.value || StatusFilter.ALL;
        const hasUnknownInDataset = this.#items.some((item) => item.status === Status.UNKNOWN);
        this.#statusFilter.replaceChildren();

        const allOption = document.createElement("option");
        allOption.value = StatusFilter.ALL;
        allOption.textContent = `All statuses`;
        this.#statusFilter.appendChild(allOption);

        [Status.LOST, Status.FOUND, Status.UNKNOWN].forEach((filterStatus) => {
            const count = counts.get(filterStatus) ?? 0;
            if (filterStatus === Status.UNKNOWN && !hasUnknownInDataset) {
                return;
            }

            const option = document.createElement("option");
            option.value = filterStatus;
            option.textContent = `${StatusMeta[filterStatus].label} (${count})`;
            option.disabled = count === 0 && filterStatus !== previous;
            this.#statusFilter.appendChild(option);
        });

        const hasPrevious = [...this.#statusFilter.options].some((option) => option.value === previous);
        this.#statusFilter.value = hasPrevious ? previous : StatusFilter.ALL;
    }

    #renderYearFilterOptions(counts, selectedYear) {
        if (!this.#yearFilter) {
            return;
        }

        const previous = selectedYear || this.#yearFilter.value || "all";
        this.#yearFilter.replaceChildren();

        const allOption = document.createElement("option");
        allOption.value = "all";
        allOption.textContent = `All years`;
        this.#yearFilter.appendChild(allOption);

        this.#allYearsSorted().forEach((yearOption) => {
            const count = counts.get(yearOption) ?? 0;
            const option = document.createElement("option");
            option.value = yearOption;
            option.textContent = `${yearOption} (${count})`;
            option.disabled = count === 0 && yearOption !== previous;
            this.#yearFilter.appendChild(option);
        });

        const hasPrevious = [...this.#yearFilter.options].some((option) => option.value === previous);
        this.#yearFilter.value = hasPrevious ? previous : "all";
    }

    #renderItems() {
        this.#itemsContainer.replaceChildren();

        const start = (this.#currentPage - 1) * this.#pageSize;
        const pageItems = this.#filteredItems.slice(start, start + this.#pageSize);

        pageItems.forEach((item) => {
            this.#itemsContainer.appendChild(this.#createCard(item));
        });

        if (this.#selectedItemId) {
            const stillVisible = this.#filteredItems.some((item) => String(item.id) === this.#selectedItemId);
            if (!stillVisible) {
                this.#closeModal(true);
            }
        }
    }

    #renderPagination() {
        const totalPages = Math.ceil(this.#filteredItems.length / this.#pageSize);

        const render = (container) => {
            if (!container) return;
            container.replaceChildren();

            if (totalPages <= 1) return;

            const isFirstPage = this.#currentPage === 1;
            container.appendChild(this.#createPageItem(1, null, "chevron-double-left", false, isFirstPage));
            container.appendChild(this.#createPageItem(this.#currentPage - 1, null, "chevron-left", false, isFirstPage));

            const startPage = Math.min(
                Math.max(1, this.#currentPage - Math.floor(LFPageData.MAX_PAGES_TO_SHOW / 2)),
                totalPages - LFPageData.MAX_PAGES_TO_SHOW + 1,
            );
            for (let i = 0; i < LFPageData.MAX_PAGES_TO_SHOW; i++) {
                const page = startPage + i;
                if (page > 0 && page <= totalPages) {
                    container.appendChild(this.#createPageItem(page, page, null, page === this.#currentPage));
                }
            }

            const isLastPage = this.#currentPage === totalPages;
            container.appendChild(this.#createPageItem(this.#currentPage + 1, null, "chevron-right", false, isLastPage));
            container.appendChild(this.#createPageItem(totalPages, null, "chevron-double-right", false, isLastPage));
        };

        render(this.#paginationTop);
        render(this.#paginationBottom);
    }

    #createPageItem(targetPage, label, icon, isActive = false, isDisabled = false) {
        const li = document.createElement("li");
        if (isActive) {
            li.classList.add("uk-active");
        }
        if (isDisabled) {
            li.classList.add("uk-disabled");
        }

        const a = document.createElement("a");
        a.setAttribute("href", "#");
        if (!isActive && !isDisabled) {
            a.addEventListener("click", (e) => {
                e.preventDefault();
                this.#currentPage = targetPage;
                this.#renderItems();
                this.#renderPagination();
                this.#renderSummary();
                this.#container.scrollIntoView({ behavior: "smooth", block: "start" });
            });
        }

        if (icon) {
            const span = document.createElement("span");
            span.setAttribute("uk-icon", `icon: ${icon}`);
            a.appendChild(span);
            a.classList.add("uk-padding-remove");
        } else {
            a.textContent = label;
        }

        li.appendChild(a);
        return li;
    }

    #renderSummary() {
        if (!this.#summaryContainer) return;

        const total = this.#filteredItems.length;
        if (total === 0) {
            this.#summaryContainer.textContent = "";
            return;
        }

        const start = (this.#currentPage - 1) * this.#pageSize + 1;
        const end = Math.min(this.#currentPage * this.#pageSize, total);
        this.#summaryContainer.textContent = `Showing ${start}-${end} of ${total} item${total !== 1 ? "s" : ""}`;
    }

    #createCard(item) {
        const outerDiv = document.createElement("div");

        const card = document.createElement("article");
        card.classList.add("uk-card", "uk-card-default", "uk-flex", "uk-flex-column", "lf-card");
        card.setAttribute("uk-scrollspy", "cls: uk-animation-slide-bottom-small; repeat: true");
        card.setAttribute("role", "button");
        card.setAttribute("tabindex", "0");
        card.setAttribute("aria-label", `Open details for ${item.title}`);
        card.addEventListener("click", () => this.#openModalForItem(item));
        card.addEventListener("keydown", (event) => {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                this.#openModalForItem(item);
            }
        });
        outerDiv.appendChild(card);

        const media = document.createElement("div");
        media.classList.add("lf-media");
        card.appendChild(media);
        media.appendChild(this.#createImage(item));

        const body = document.createElement("div");
        body.classList.add("uk-card-body", "uk-flex", "uk-flex-column", "uk-flex-1", "lf-card-body");
        card.appendChild(body);

        const { label, cssClass } = StatusMeta[item.status] ?? StatusMeta[Status.UNKNOWN];
        const badge = document.createElement("span");
        badge.classList.add("uk-card-badge", "uk-label", "lf-label", cssClass);
        badge.textContent = label;
        body.appendChild(badge);

        const bodyHeader = document.createElement("div");
        bodyHeader.classList.add("uk-flex", "uk-flex-column", "uk-flex-between", "uk-margin-small-bottom");
        body.appendChild(bodyHeader);

        const title = document.createElement("h3");
        title.classList.add("uk-card-title", "reset-font", "lf-title");
        title.textContent = item.title;
        bodyHeader.appendChild(title);

        const idText = document.createElement("span");
        idText.classList.add("uk-text-meta", "uk-margin-small-bottom", "lf-item-id");
        idText.textContent = `Item ID: ${item.id}`;
        bodyHeader.appendChild(idText);

        const description = document.createElement("span");
        description.classList.add("lf-item-description");
        description.textContent = item.description;
        body.appendChild(description);

        const timelineList = document.createElement("ul");
        timelineList.classList.add("uk-list", "uk-list-collapse", "lf-meta-list");
        this.#appendTimelineRow(timelineList, StatusMeta[Status.LOST].label, item.lostTimestamp);
        this.#appendTimelineRow(timelineList, StatusMeta[Status.FOUND].label, item.foundTimestamp);
        this.#appendTimelineRow(timelineList, StatusMeta[Status.RETURNED].label, item.returnTimestamp);
        if (timelineList.childElementCount === 0) {
            this.#appendTimelineRow(timelineList, "Timeline", "Not available yet");
        }
        body.appendChild(timelineList);

        return outerDiv;
    }

    #appendTimelineRow(list, label, value) {
        if (!value) {
            return;
        }

        const row = document.createElement("li");

        const labelNode = document.createElement("span");
        labelNode.classList.add("lf-meta-label");
        labelNode.textContent = label;
        row.appendChild(labelNode);

        const valueNode = document.createElement("span");
        valueNode.textContent = this.#formatTimestamp(value);
        row.appendChild(valueNode);

        list.appendChild(row);
    }

    #createImage(item) {
        const image = document.createElement("img");
        image.classList.add("lf-image");
        image.src = item.thumbUrl || this.#config.noImageUrl;
        image.alt = item.title;
        image.loading = "lazy";
        image.decoding = "async";
        image.onerror = () => {
            image.onerror = null;
            image.src = this.#config.noImageUrl;
        };
        return image;
    }

    #syncPageSizeSelect() {
        if (!this.#pageSizeSelect) return;
        this.#pageSizeSelect.value = String(this.#pageSize);
    }

    #renderSuggestions(query) {
        if (!this.#searchSuggestions) {
            return;
        }

        this.#searchSuggestions.replaceChildren();
        if (!query || query.length < 2) {
            return;
        }

        const titleStats = new Map();
        this.#filteredItems.forEach((item) => {
            const key = item.title.trim();
            const prev = titleStats.get(key) ?? { count: 0, latest: 0, label: key };
            prev.count += 1;
            prev.latest = Math.max(prev.latest, item.sortTimestamp);
            titleStats.set(key, prev);
        });

        const normalizedQuery = query.toLowerCase();
        const suggestions = [...titleStats.values()]
            .filter((entry) => entry.label.toLowerCase().includes(normalizedQuery))
            .sort((a, b) => {
                if (a.count !== b.count) {
                    return b.count - a.count;
                }
                if (a.latest !== b.latest) {
                    return b.latest - a.latest;
                }
                return a.label.localeCompare(b.label);
            })
            .slice(0, 10);

        suggestions.forEach((entry) => {
            const option = document.createElement("option");
            option.value = entry.label;
            this.#searchSuggestions.appendChild(option);
        });
    }

    #bindModalEvents() {
        if (!this.#modalRoot) {
            return;
        }

        this.#modalRoot.addEventListener("hidden", () => {
            this.#closeModal(true);
        });

        window.addEventListener("popstate", () => {
            this.#openModalFromUrl();
        });
    }

    #getModalInstance() {
        if (!this.#modalRoot || typeof UIkit === "undefined" || !UIkit.modal) {
            return null;
        }
        return UIkit.modal(this.#modalRoot);
    }

    #openModalFromUrl() {
        const params = new URLSearchParams(window.location.search);
        const itemId = params.get("item");
        if (!itemId) {
            this.#closeModal(false);
            return;
        }

        const item = this.#items.find((entry) => String(entry.id) === itemId);
        if (!item) {
            this.#closeModal(true);
            return;
        }

        this.#openModalForItem(item, false);
    }

    #openModalForItem(item, pushState = true) {
        if (!this.#modalRoot || !item) {
            return;
        }

        this.#clearModal();
        this.#selectedItemId = String(item.id);
        this.#fillModal(item);

        if (pushState) {
            const params = new URLSearchParams(window.location.search);
            params.set("item", this.#selectedItemId);
            const query = params.toString();
            const next = `${window.location.pathname}${query ? `?${query}` : ""}${window.location.hash}`;
            history.pushState({}, "", next);
        }

        const modal = this.#getModalInstance();
        if (modal) {
            modal.show();
        }
    }

    #closeModal(removeParam) {
        if (removeParam) {
            const params = new URLSearchParams(window.location.search);
            if (params.has("item")) {
                params.delete("item");
                const query = params.toString();
                const next = `${window.location.pathname}${query ? `?${query}` : ""}${window.location.hash}`;
                history.replaceState({}, "", next);
            }
        }

        this.#selectedItemId = "";
        this.#clearModal();
        const modal = this.#getModalInstance();
        if (modal && modal.isActive()) {
            modal.hide();
        }
    }

    #allModalElementsPresent() {
        return (
            this.#modalImage &&
            this.#modalImageLink &&
            this.#modalStatus &&
            this.#modalTitle &&
            this.#modalId &&
            this.#modalDescription &&
            this.#modalTimeline
        );
    }

    #clearModal() {
        if (!this.#allModalElementsPresent()) return;

        this.#modalImage.onerror = null;
        this.#modalImage.src = this.#config.noImageUrl;
        this.#modalImage.alt = "";
        this.#modalImageLink.href = "#";
        this.#modalImageLink.style.pointerEvents = "none";
        this.#modalImageLink.tabIndex = -1;
        this.#modalImageLink.setAttribute("aria-disabled", "true");
        this.#modalStatus.className = "uk-label lf-label";
        this.#modalStatus.textContent = "";
        this.#modalTitle.textContent = "";
        this.#modalId.textContent = "";
        this.#modalDescription.textContent = "";
        this.#modalTimeline.replaceChildren();
    }

    #fillModal(item) {
        if (!this.#allModalElementsPresent()) return;

        const { label, cssClass } = StatusMeta[item.status] ?? StatusMeta[Status.UNKNOWN];
        const source = item.imageUrl || item.thumbUrl;
        const hasRealImage = Boolean(source) && source !== this.#config.noImageUrl;

        this.#modalImage.src = hasRealImage ? source : this.#config.noImageUrl;
        this.#modalImage.alt = item.title;
        this.#modalImage.onerror = () => {
            this.#modalImage.onerror = null;
            this.#modalImage.src = this.#config.noImageUrl;
        };

        if (hasRealImage) {
            this.#modalImageLink.href = source;
            this.#modalImageLink.style.pointerEvents = "";
            this.#modalImageLink.removeAttribute("tabindex");
            this.#modalImageLink.removeAttribute("aria-disabled");
        } else {
            this.#modalImageLink.href = "#";
            this.#modalImageLink.style.pointerEvents = "none";
            this.#modalImageLink.tabIndex = -1;
            this.#modalImageLink.setAttribute("aria-disabled", "true");
        }

        this.#modalStatus.className = "uk-label lf-label";
        this.#modalStatus.classList.add(cssClass);
        this.#modalStatus.textContent = label;
        this.#modalTitle.textContent = item.title;
        this.#modalId.textContent = `Item ID: ${item.id}`;
        this.#modalDescription.textContent = item.description;
        this.#modalTimeline.replaceChildren();
        this.#appendTimelineRow(this.#modalTimeline, StatusMeta[Status.LOST].label, item.lostTimestamp);
        this.#appendTimelineRow(this.#modalTimeline, StatusMeta[Status.FOUND].label, item.foundTimestamp);
        this.#appendTimelineRow(this.#modalTimeline, StatusMeta[Status.RETURNED].label, item.returnTimestamp);

        if (this.#modalTimeline.childElementCount === 0) {
            this.#appendTimelineRow(this.#modalTimeline, "Timeline", "Not available yet");
        }
    }

    #normalizeItem(item) {
        const normalizedStatus = this.#normalizeStatus(item.status);
        const title = this.#asText(item.title, "Untitled item");
        const description = this.#asText(item.description, "No description provided.");
        const id = this.#asText(item.id, "N/A");
        const lostTimestamp = this.#asText(item.lost_timestamp);
        const foundTimestamp = this.#asText(item.found_timestamp);
        const returnTimestamp = this.#asText(item.return_timestamp);
        const sortTimestamp = this.#latestTimestamp(lostTimestamp, foundTimestamp, returnTimestamp);
        const numericId = Number.parseInt(id, 10);
        let thumbUrl = this.#config.noImageUrl;
        let imageUrl = "";
        if (item.thumb) {
            thumbUrl = this.#env.USE_MOCK_DATA ? item.thumb : `${this.#config.baseUrl}/thumb/${item.thumb}`;
        }
        if (item.image) {
            imageUrl = this.#env.USE_MOCK_DATA ? item.image : `${this.#config.baseUrl}/image/${item.image}`;
        }

        return {
            id,
            numericId: Number.isNaN(numericId) ? 0 : numericId,
            status: normalizedStatus,
            title,
            description,
            lostTimestamp,
            foundTimestamp,
            returnTimestamp,
            thumbUrl,
            imageUrl,
            sortTimestamp,
            years: this.#extractYears(lostTimestamp, foundTimestamp, returnTimestamp),
            searchBlob: `${id} ${title} ${description}`.toLowerCase(),
        };
    }

    #latestTimestamp(...values) {
        let latest = 0;
        values.forEach((value) => {
            if (!value) {
                return;
            }

            const parsed = Date.parse(value);
            if (!Number.isNaN(parsed) && parsed > latest) {
                latest = parsed;
            }
        });
        return latest;
    }

    #extractYears(...values) {
        const years = new Set();
        values.forEach((value) => {
            if (!value) return;
            const parsed = Date.parse(value);
            if (!Number.isNaN(parsed)) {
                years.add(String(new Date(parsed).getFullYear()));
            }
        });
        return years;
    }

    #normalizeStatus(status) {
        const text = this.#asText(status, Status.UNKNOWN).toUpperCase();
        if (text === Status.LOST || text === Status.FOUND || text === Status.RETURNED) {
            return text;
        }
        return Status.UNKNOWN;
    }

    #formatTimestamp(value) {
        const parsed = Date.parse(value);
        if (Number.isNaN(parsed)) {
            return value;
        }

        return new Date(parsed).toLocaleString([], {
            year: "numeric",
            month: "short",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
        });
    }

    #asText(value, fallback = "") {
        if (value === null || value === undefined) {
            return fallback;
        }

        const text = String(value).trim();
        return text || fallback;
    }

    #renderState(type, message) {
        if (!this.#stateContainer) {
            return;
        }
        this.#stateContainer.replaceChildren();

        if (!message) {
            this.#stateContainer.hidden = true;
            return;
        }
        this.#stateContainer.hidden = false;

        const alert = document.createElement("div");
        alert.classList.add("uk-alert", "uk-flex", "lf-state");
        if (type === StateType.ERROR) {
            alert.classList.add("uk-alert-danger");
        } else if (type === StateType.EMPTY) {
            alert.classList.add("uk-alert-warning");
        } else {
            alert.classList.add("uk-alert-primary");
        }

        if (type === StateType.LOADING) {
            const spinner = document.createElement("span");
            spinner.setAttribute("uk-spinner", "ratio: 0.8");
            spinner.classList.add("uk-margin-right");
            alert.appendChild(spinner);
        }

        const text = document.createElement("span");
        text.textContent = message;
        alert.appendChild(text);

        this.#stateContainer.appendChild(alert);
    }

    async #fetch(url) {
        const fetchUrl = this.#env.USE_MOCK_DATA ? this.#env.MOCK_LF_DATA : url;
        const requestUrl = `${fetchUrl}?${Date.now()}`;

        try {
            const response = await fetch(requestUrl, { cache: "no-store" });
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            if (!data) {
                throw new Error("Malformed data payload");
            }

            return data;
        } catch (ex) {
            console.error(`[eflf] failed to load ${requestUrl}, reason:`, ex);
            return null;
        }
    }
}

const lostandfound = new LostAndFound({
    container: document.getElementById("ef-lostandfound"),
    itemsContainer: document.getElementById("ef-lostandfound-items"),
    stateContainer: document.getElementById("ef-lostandfound-state"),
    searchInput: document.getElementById("ef-lostandfound-search"),
    searchSuggestions: document.getElementById("ef-lostandfound-suggestions"),
    statusFilter: document.getElementById("ef-lostandfound-status"),
    yearFilter: document.getElementById("ef-lostandfound-year"),
    summaryContainer: document.getElementById("ef-lostandfound-summary"),
    pageSizeSelect: document.getElementById("ef-lostandfound-pagesize"),
    paginationTop: document.getElementById("ef-lostandfound-pagination-top"),
    paginationBottom: document.getElementById("ef-lostandfound-pagination-bottom"),
    modalRoot: document.getElementById("ef-lostandfound-modal"),
    modalImageLink: document.getElementById("ef-lostandfound-modal-image-link"),
    modalImage: document.getElementById("ef-lostandfound-modal-image"),
    modalStatus: document.getElementById("ef-lostandfound-modal-status"),
    modalTitle: document.getElementById("ef-lostandfound-modal-title"),
    modalId: document.getElementById("ef-lostandfound-modal-id"),
    modalDescription: document.getElementById("ef-lostandfound-modal-description"),
    modalTimeline: document.getElementById("ef-lostandfound-modal-timeline"),
});

lostandfound.build();
