const DailyPageConfig = Object.freeze({
    yearToShowAsCurrentEdition: 2026,
    previousEditionsToShow: 1,
});

const DailyStateType = Object.freeze({
    LOADING: "loading",
    ERROR: "error",
    EMPTY: "empty",
});

class Daily {
    #config = {
        archiveEndpoint: "src/daily-archive.php",
        archivePrefix: "https://archive.eurofurence.org/daily/",
    };
    #currentEditionList = null;
    #pastEditionsList = null;
    #modalRoot = null;
    #modalTitle = null;
    #modalFrame = null;
    #miniPreviewObserver = null;
    #modalHiddenHookBound = false;
    #eventsBound = false;

    constructor(options) {
        this.#currentEditionList = options.currentEditionList;
        this.#pastEditionsList = options.pastEditionsList;
        this.#modalRoot = options.modalRoot;
        this.#modalTitle = options.modalTitle;
        this.#modalFrame = options.modalFrame;
    }

    async build() {
        if (
            !this.#currentEditionList ||
            !this.#pastEditionsList ||
            !this.#modalRoot ||
            !this.#modalTitle ||
            !this.#modalFrame
        ) {
            return;
        }

        this.#renderLoadingState();

        this.#miniPreviewObserver = this.#createMiniPreviewObserver();

        this.#bindEvents();
        await this.#loadArchiveOptions();
    }

    #bindEvents() {
        if (this.#eventsBound) {
            return;
        }

        const handleCardClick = (event) => {
            const target = event.target;
            if (!(target instanceof Element)) {
                return;
            }

            const card = target.closest(".daily-issue-card");
            if (!card) {
                return;
            }

            const url = card.dataset.url;
            const label = card.dataset.label || card.textContent;
            this.#openPdfModal(url, label);
        };

        this.#currentEditionList.addEventListener("click", handleCardClick);
        this.#pastEditionsList.addEventListener("click", handleCardClick);

        this.#eventsBound = true;
    }

    #normalizePdfUrl(value) {
        if (!value || typeof value !== "string") {
            return null;
        }

        const trimmed = value.trim();
        if (!trimmed.startsWith(this.#config.archivePrefix) || !trimmed.toLowerCase().includes(".pdf")) {
            return null;
        }

        return trimmed;
    }

    #createMiniPreviewObserver() {
        if (!("IntersectionObserver" in window)) {
            return null;
        }

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    const frame = entry.target;
                    if (frame && frame.dataset && frame.dataset.src && !frame.getAttribute("src")) {
                        frame.setAttribute("src", frame.dataset.src);
                    }

                    observer.unobserve(entry.target);
                });
            },
            {
                rootMargin: "128px 0px",
                threshold: 0.01,
            },
        );

        return observer;
    }

    #getModalInstance() {
        if (!this.#modalRoot || typeof UIkit === "undefined" || !UIkit.modal) {
            return null;
        }
        return UIkit.modal(this.#modalRoot);
    }

    #openPdfModal(url, label) {
        const normalizedUrl = this.#normalizePdfUrl(url);
        if (!normalizedUrl) {
            return;
        }

        this.#modalTitle.textContent = label || normalizedUrl.split("/").pop();
        this.#modalFrame.removeAttribute("src");
        this.#modalFrame.src = normalizedUrl;

        const modalInstance = this.#getModalInstance();
        if (modalInstance) {
            modalInstance.show();
        }
    }

    #toIntegerOrNull(value) {
        const parsed = Number.parseInt(value, 10);
        return Number.isFinite(parsed) ? parsed : null;
    }

    #groupItemsByEdition(items) {
        const groupsMap = new Map();

        items.forEach((item) => {
            const normalizedUrl = this.#normalizePdfUrl(item.url);
            if (!normalizedUrl) {
                return;
            }

            const groupKeyRaw = typeof item.edition === "string" ? item.edition.trim() : "";
            const groupKey = groupKeyRaw || "Archive";
            const issue = this.#toIntegerOrNull(item.issue);
            const year = this.#toIntegerOrNull(item.year);
            const sortDate = this.#toIntegerOrNull(item.sortDate) || 0;
            const date = typeof item.date === "string" ? item.date : "";
            const displayDate = typeof item.displayDate === "string" ? item.displayDate : "";
            const day = typeof item.day === "string" ? item.day : "";

            if (!groupsMap.has(groupKey)) {
                groupsMap.set(groupKey, {
                    key: groupKey,
                    issues: [],
                    year: year || 0,
                    latestSortDate: 0,
                });
            }

            const group = groupsMap.get(groupKey);
            group.issues.push({
                url: normalizedUrl,
                label: item.label || item.filename || normalizedUrl,
                filename: item.filename || "",
                date,
                displayDate,
                day,
                year,
                issue,
                sortDate,
            });

            if (sortDate > group.latestSortDate) {
                group.latestSortDate = sortDate;
            }
        });

        const groups = Array.from(groupsMap.values());
        groups.sort((left, right) => {
            if (left.latestSortDate !== right.latestSortDate) {
                return right.latestSortDate - left.latestSortDate;
            }
            return left.key.localeCompare(right.key);
        });

        return groups;
    }

    #getVisibleGroups(groups) {
        if (!Array.isArray(groups) || groups.length === 0) {
            return [];
        }

        const yearToShowAsCurrentInt = this.#toIntegerOrNull(DailyPageConfig.yearToShowAsCurrentEdition);
        const currentEditionGroup =
            yearToShowAsCurrentInt !== null
                ? groups.find((group) => group && group.year === yearToShowAsCurrentInt) || null
                : null;
        const additionalGroupsToShow = this.#toIntegerOrNull(DailyPageConfig.previousEditionsToShow) || 0;

        if (currentEditionGroup) {
            const visible = [currentEditionGroup];
            if (additionalGroupsToShow > 0) {
                // Filter groups older than current edition year
                const olderGroups = groups.filter((group) => group.year < currentEditionGroup.year);
                olderGroups.sort((left, right) => right.year - left.year);
                visible.push(...olderGroups.slice(0, additionalGroupsToShow));
            }
            return visible;
        } else if (additionalGroupsToShow > 0) {
            const sorted = [...groups].sort((left, right) => right.year - left.year);
            return sorted.slice(0, additionalGroupsToShow);
        }
        return [];
    }

    #createIssueCard(issue) {
        const card = document.createElement("button");
        card.className = "daily-issue-card uk-card uk-card-default";
        card.dataset.url = issue.url;
        card.dataset.label = issue.label;

        const miniPreview = document.createElement("span");
        miniPreview.className = "daily-issue-mini-preview";

        const miniFrame = document.createElement("iframe");
        miniFrame.setAttribute("title", `Preview ${issue.label}`);
        miniFrame.setAttribute("loading", "lazy");
        miniFrame.setAttribute("referrerpolicy", "no-referrer");
        miniFrame.setAttribute("scrolling", "no");
        miniFrame.dataset.src = `${issue.url}#page=1&view=FitH&toolbar=0&navpanes=0&scrollbar=0`;

        if (this.#miniPreviewObserver) {
            this.#miniPreviewObserver.observe(miniFrame);
        } else {
            miniFrame.setAttribute("src", miniFrame.dataset.src);
        }

        miniPreview.appendChild(miniFrame);

        const title = document.createElement("h3");
        const formattedDate = issue.displayDate || issue.date;
        title.className = "uk-card-title daily-issue-card-title";
        title.textContent =
            issue.day && formattedDate ? `${issue.day}, ${formattedDate}` : issue.day || formattedDate || issue.label;

        const subtitle = document.createElement("span");
        subtitle.className = "daily-issue-card-subtitle uk-text-meta";
        subtitle.textContent = Number.isFinite(issue.issue) ? `Issue ${issue.issue}` : "";

        card.appendChild(miniPreview);
        card.appendChild(title);
        card.appendChild(subtitle);

        return card;
    }

    #renderLoadingState() {
        this.#currentEditionList.replaceChildren();
        this.#pastEditionsList.replaceChildren();

        const alert = document.createElement("div");
        alert.classList.add("uk-alert", "uk-flex", "daily-state", "uk-alert-primary");

        const spinner = document.createElement("span");
        spinner.setAttribute("uk-spinner", "ratio: 0.8");
        spinner.classList.add("uk-margin-right");
        alert.appendChild(spinner);

        const text = document.createElement("span");
        text.textContent = "Loading Daily issues...";
        alert.appendChild(text);

        this.#currentEditionList.appendChild(alert.cloneNode(true));

        const additionalGroupsToShow = this.#toIntegerOrNull(DailyPageConfig.previousEditionsToShow) || 0;
        if (additionalGroupsToShow > 0) {
            this.#pastEditionsList.appendChild(alert.cloneNode(true));
        }
    }

    #renderState(type, message) {
        if (type === DailyStateType.EMPTY) {
            this.#currentEditionList.replaceChildren();
            this.#pastEditionsList.replaceChildren();
            return;
        }

        const alert = document.createElement("div");
        alert.classList.add("uk-alert", "uk-flex", "daily-state");

        if (type === DailyStateType.ERROR) {
            alert.classList.add("uk-alert-danger");
        } else {
            alert.classList.add("uk-alert-primary");
        }

        const text = document.createElement("span");
        text.textContent = message;
        alert.appendChild(text);

        this.#currentEditionList.replaceChildren(alert.cloneNode(true));
        this.#pastEditionsList.replaceChildren(alert.cloneNode(true));
    }

    #renderEditionGroups(groups) {
        this.#currentEditionList.replaceChildren();
        this.#pastEditionsList.replaceChildren();

        if (!Array.isArray(groups) || groups.length === 0) {
            this.#renderState(DailyStateType.EMPTY, "No issues available at the moment.");
            return;
        }

        const yearToShowAsCurrentInt = this.#toIntegerOrNull(DailyPageConfig.yearToShowAsCurrentEdition);
        groups.forEach((group) => {
            const edition = document.createElement("section");
            edition.className = "daily-edition";

            const heading = document.createElement("h3");
            const issueCountLabel = `${group.issues.length} ${group.issues.length === 1 ? "issue" : "issues"}`;
            heading.classList.add("uk-margin-remove-bottom");
            heading.textContent = `${group.key} (${group.year} - ${issueCountLabel})`;

            const issueList = document.createElement("div");
            issueList.className = "daily-issue-list";

            group.issues.forEach((issue) => {
                issueList.appendChild(this.#createIssueCard(issue));
            });

            edition.appendChild(heading);
            edition.appendChild(issueList);

            if (yearToShowAsCurrentInt !== null && group.year === yearToShowAsCurrentInt) {
                this.#currentEditionList.appendChild(edition);
            } else {
                this.#pastEditionsList.appendChild(edition);
            }
        });
    }

    async #loadArchiveOptions() {
        try {
            const response = await fetch(this.#config.archiveEndpoint, {
                headers: {
                    Accept: "application/json",
                },
            });

            if (!response.ok) {
                throw new Error("Failed to load archive listing.");
            }

            const payload = await response.json();
            if (!payload || payload.ok !== true || !Array.isArray(payload.items) || payload.items.length === 0) {
                throw new Error((payload && payload.message) || "Archive listing is empty.");
            }

            const groups = this.#groupItemsByEdition(payload.items);
            const visibleGroups = this.#getVisibleGroups(groups);
            this.#renderEditionGroups(visibleGroups);
        } catch (error) {
            const message = error instanceof Error ? error.message : "Could not load Daily issues at the moment.";
            this.#renderState(DailyStateType.ERROR, message);
        }
    }
}

const daily = new Daily({
    currentEditionList: document.getElementById("daily-current-list"),
    pastEditionsList: document.getElementById("daily-archive-list"),
    modalRoot: document.getElementById("daily-pdf-modal"),
    modalTitle: document.getElementById("daily-pdf-modal-title"),
    modalFrame: document.getElementById("daily-pdf-modal-frame"),
});

daily.build();
