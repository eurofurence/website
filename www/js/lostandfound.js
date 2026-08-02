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

class LostAndFound {
    #config = {
        baseUrl: "https://www.eurofurence.org/data/lf",
        noImageUrl: "img/pages/lostandfound/no-photo.png",
    };
    #env = window.__EF_ENVIRONMENT__ || { USE_MOCK_DATA: false, MOCK_LF_DATA: "__mocks__/lostandfound.mock.json" };
    #targetContainer = null;
    #stateContainer = null;
    #searchInput = null;
    #statusFilter = null;
    #items = [];
    #filteredItems = [];
    #eventsBound = false;

    constructor(options) {
        this.#targetContainer = options.targetContainer;
        this.#stateContainer = options.stateContainer;
        this.#searchInput = options.searchInput;
        this.#statusFilter = options.statusFilter;
    }

    async build() {
        if (!this.#targetContainer) {
            console.error("[eflf] Target container not found.");
            return;
        }

        this.#renderState(StateType.LOADING, "Loading lost and found items...");

        const payload = await this.#fetch(`${this.#config.baseUrl}/data.json`);
        if (!payload || !Array.isArray(payload.data)) {
            this.#targetContainer.replaceChildren();
            this.#renderState(
                StateType.ERROR,
                "Could not load lost and found items. Please try again later.",
            );
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

        this.#bindEvents();
        this.#applyFilters();
    }

    #bindEvents() {
        if (this.#eventsBound) {
            return;
        }

        if (this.#searchInput) {
            this.#searchInput.addEventListener("input", () => this.#applyFilters());
        }

        if (this.#statusFilter) {
            this.#statusFilter.addEventListener("change", () => this.#applyFilters());
        }

        this.#eventsBound = true;
    }

    #applyFilters() {
        const query = this.#searchInput ? this.#searchInput.value.trim().toLowerCase() : "";
        const status = this.#statusFilter ? this.#statusFilter.value : StatusFilter.ALL;

        this.#filteredItems = this.#items.filter((item) => {
            const statusMatches = status === StatusFilter.ALL || item.status === status;
            const queryMatches = !query || item.searchBlob.includes(query);
            return statusMatches && queryMatches;
        });

        this.#renderItems();

        if (this.#filteredItems.length === 0) {
            this.#renderState(StateType.EMPTY, "No items match your current filters.");
            return;
        }

        this.#renderState(null, null);
    }

    #renderItems() {
        this.#targetContainer.replaceChildren();

        this.#filteredItems.forEach((item) => {
            this.#targetContainer.appendChild(this.#createCard(item));
        });
    }

    #createCard(item) {
        const outerDiv = document.createElement("div");

        const card = document.createElement("article");
        card.classList.add("uk-card", "uk-card-default", "uk-flex", "uk-flex-column", "lf-card");
        card.setAttribute("uk-scrollspy", "cls: uk-animation-slide-bottom-small; repeat: true");
        outerDiv.appendChild(card);

        const media = document.createElement("div");
        media.classList.add("lf-media");
        card.appendChild(media);

        if (item.imageUrl) {
            media.setAttribute("uk-lightbox", "animation: slide");
            const link = document.createElement("a");
            link.classList.add("hide-ext", "lf-image-link");
            link.href = item.imageUrl;
            media.appendChild(link);
            link.appendChild(this.#createImage(item));
        } else {
            media.appendChild(this.#createImage(item));
        }

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
    targetContainer: document.getElementById("ef-lostandfound"),
    stateContainer: document.getElementById("ef-lostandfound-state"),
    searchInput: document.getElementById("ef-lostandfound-search"),
    statusFilter: document.getElementById("ef-lostandfound-status"),
});

lostandfound.build();
