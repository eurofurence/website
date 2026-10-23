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
    #showAllPastButton = null;
    #archiveMoreLink = null;
    #miniPreviewObserver = null;
    #modalHiddenHookBound = false;
    #eventsBound = false;
    #allGroups = [];
    #showAllPastEditions = false;
    #settings = {
        currentEditionYear: null,
        showPreviousEditions: null,
        prevEditionsToShowWhenCollapsed: null,
        prevEditionsToShowWhenExpanded: null,
    };

    constructor(options) {
        this.#currentEditionList = options.currentEditionList;
        this.#pastEditionsList = options.pastEditionsList;
        this.#modalRoot = options.modalRoot;
        this.#modalTitle = options.modalTitle;
        this.#modalFrame = options.modalFrame;
        this.#showAllPastButton = options.showAllPastButton || null;
        this.#archiveMoreLink = options.archiveMoreLink || null;
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

        this.#loadSettingsFromPageConfig();
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

        if (this.#showAllPastButton) {
            this.#showAllPastButton.addEventListener("click", () => {
                this.#showAllPastEditions = !this.#showAllPastEditions;
                const visibleState = this.#getVisibleGroups(this.#allGroups);
                this.#renderPastEditionGroups(visibleState.pastGroups);
                this.#syncPastToggle(visibleState.totalPastAvailable, visibleState.shownPastCount);
            });
        }

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

    #loadSettingsFromPageConfig() {
        const pageConfig = window.__EF_DAILY_CONFIG__ || {};
        const currentEditionYear = this.#toIntegerOrNull(pageConfig.currentEditionYear);
        const prevEditionsToShowWhenCollapsedRaw = this.#toIntegerOrNull(pageConfig.prevEditionsToShowWhenCollapsed);
        const prevEditionsToShowWhenExpandedRaw = this.#toIntegerOrNull(pageConfig.prevEditionsToShowWhenExpanded);
        const showPreviousEditions =
            typeof pageConfig.showPreviousEditions === "boolean" ? pageConfig.showPreviousEditions : null;

        this.#settings = {
            currentEditionYear,
            showPreviousEditions,
            prevEditionsToShowWhenCollapsed:
                prevEditionsToShowWhenCollapsedRaw !== null ? Math.max(0, prevEditionsToShowWhenCollapsedRaw) : null,
            prevEditionsToShowWhenExpanded:
                prevEditionsToShowWhenExpandedRaw !== null && prevEditionsToShowWhenExpandedRaw > 0
                    ? prevEditionsToShowWhenExpandedRaw
                    : null,
        };
    }

    #groupItemsByEdition(items) {
        const groupsMap = new Map();

        items.forEach((item) => {
            const normalizedUrl = this.#normalizePdfUrl(item.url);
            if (!normalizedUrl) {
                return;
            }

            const groupKeyRaw = typeof item.edition === "string" ? item.edition.trim() : "";
            const groupName = groupKeyRaw || "Archive";
            const issue = this.#toIntegerOrNull(item.issue);
            const year = this.#toIntegerOrNull(item.year);
            const groupYear = year || 0;
            const groupKey = `${groupName}_${groupYear}`;
            const sortDate = this.#toIntegerOrNull(item.sortDate) || 0;
            const date = typeof item.date === "string" ? item.date : "";
            const displayDate = typeof item.displayDate === "string" ? item.displayDate : "";
            const day = typeof item.day === "string" ? item.day : "";

            if (!groupsMap.has(groupKey)) {
                groupsMap.set(groupKey, {
                    key: groupKey,
                    name: groupName,
                    issues: [],
                    year: groupYear,
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
            return {
                currentGroup: null,
                pastGroups: [],
                totalPastAvailable: 0,
                shownPastCount: 0,
            };
        }

        const yearToShowAsCurrentInt = this.#settings.currentEditionYear;
        const currentEditionGroup =
            yearToShowAsCurrentInt !== null
                ? groups.find((group) => group && group.year === yearToShowAsCurrentInt) || null
                : null;
        const additionalGroupsToShow = this.#settings.prevEditionsToShowWhenCollapsed;
        const pastGroupsLimit = this.#showAllPastEditions
            ? this.#settings.prevEditionsToShowWhenExpanded
            : additionalGroupsToShow;

        if (this.#settings.showPreviousEditions === false) {
            return {
                currentGroup: currentEditionGroup,
                pastGroups: [],
                totalPastAvailable: 0,
                shownPastCount: 0,
            };
        }

        if (currentEditionGroup) {
            const olderGroups = groups.filter((group) => group.year < currentEditionGroup.year);
            olderGroups.sort((left, right) => right.year - left.year);

            const visiblePastGroups =
                pastGroupsLimit === null ? olderGroups : olderGroups.slice(0, Math.max(0, pastGroupsLimit));

            return {
                currentGroup: currentEditionGroup,
                pastGroups: visiblePastGroups,
                totalPastAvailable: olderGroups.length,
                shownPastCount: visiblePastGroups.length,
            };
        }

        const sorted = [...groups].sort((left, right) => right.year - left.year);
        const visiblePastGroups = pastGroupsLimit === null ? sorted : sorted.slice(0, Math.max(0, pastGroupsLimit));

        return {
            currentGroup: null,
            pastGroups: visiblePastGroups,
            totalPastAvailable: sorted.length,
            shownPastCount: visiblePastGroups.length,
        };
    }

    #setElementHidden(element, isHidden) {
        if (!element) {
            return;
        }

        element.hidden = isHidden;
        element.setAttribute("aria-hidden", String(isHidden));
        element.toggleAttribute("inert", isHidden);
    }

    #syncPastToggle(totalPastAvailable, shownPastCount) {
        const editionLimitsMatch =
            this.#settings.prevEditionsToShowWhenCollapsed !== null &&
            this.#settings.prevEditionsToShowWhenExpanded !== null &&
            this.#settings.prevEditionsToShowWhenCollapsed === this.#settings.prevEditionsToShowWhenExpanded;
        let shouldShowToggle = false;

        if (
            this.#showAllPastButton &&
            this.#settings.showPreviousEditions !== false &&
            this.#settings.prevEditionsToShowWhenCollapsed !== null &&
            !editionLimitsMatch
        ) {
            const expandedPastCount =
                this.#settings.prevEditionsToShowWhenExpanded === null
                    ? totalPastAvailable
                    : Math.min(totalPastAvailable, this.#settings.prevEditionsToShowWhenExpanded);
            const additionalPastCount = Math.max(0, expandedPastCount - shownPastCount);
            shouldShowToggle = this.#showAllPastEditions ? totalPastAvailable > 0 : additionalPastCount > 0;
            this.#showAllPastButton.textContent = this.#showAllPastEditions
                ? "Show fewer past editions"
                : `Show more past editions (${additionalPastCount} more)`;
        }

        this.#setElementHidden(this.#showAllPastButton, !shouldShowToggle);
        this.#setElementHidden(this.#archiveMoreLink, shouldShowToggle);
    }

    #createIssueCard(issue, useLivePreview, isPastEdition, year = 0) {
        const card = document.createElement("button");
        card.className = "uk-card uk-card-default daily-issue-card";
        if (isPastEdition) {
            card.classList.add("daily-issue-card-past");
            if (Number.isFinite(year) && year > 0) {
                card.classList.add(`daily-issue-card-year-${year}`);
            }
        }
        card.dataset.url = issue.url;
        card.dataset.label = issue.label;

        const title = document.createElement("h3");
        const formattedDate = issue.displayDate || issue.date;
        title.className = "uk-card-title daily-issue-card-title";
        title.textContent =
            issue.day && formattedDate ? `${issue.day}, ${formattedDate}` : issue.day || formattedDate || issue.label;

        const subtitle = document.createElement("span");
        subtitle.className = "uk-text-meta daily-issue-card-subtitle";
        subtitle.textContent = Number.isFinite(issue.issue) ? `Issue ${issue.issue}` : "";

        const content = document.createElement("span");
        content.className = "daily-issue-card-content";
        content.appendChild(title);
        content.appendChild(subtitle);

        if (useLivePreview) {
            const miniPreview = document.createElement("span");
            miniPreview.className = "daily-issue-mini-preview";
            miniPreview.appendChild(this.#createLivePreview(issue));
            card.appendChild(miniPreview);
        }

        card.appendChild(content);

        return card;
    }

    #createLivePreview(issue) {
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

        return miniFrame;
    }

    #renderLoadingState() {
        this.#currentEditionList.replaceChildren();
        this.#pastEditionsList.replaceChildren();
        this.#syncPastToggle(0, 0);

        const alert = document.createElement("div");
        alert.classList.add("uk-alert", "uk-flex", "uk-alert-primary", "daily-state");

        const spinner = document.createElement("span");
        spinner.setAttribute("uk-spinner", "ratio: 0.8");
        spinner.classList.add("uk-margin-right");
        alert.appendChild(spinner);

        const text = document.createElement("span");
        text.textContent = "Loading Daily issues...";
        alert.appendChild(text);

        this.#currentEditionList.appendChild(alert.cloneNode(true));

        if (this.#settings.showPreviousEditions !== false) {
            this.#pastEditionsList.appendChild(alert.cloneNode(true));
        }
    }

    #renderState(type, message) {
        if (type === DailyStateType.EMPTY) {
            this.#currentEditionList.replaceChildren();
            this.#pastEditionsList.replaceChildren();
            this.#syncPastToggle(0, 0);
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
        this.#syncPastToggle(0, 0);
    }

    #buildEditionSection(group, isCurrentEdition) {
        const edition = document.createElement("section");
        edition.className = "daily-edition";
        if (Number.isFinite(group.year) && group.year > 0) {
            edition.classList.add(`daily-edition-year-${group.year}`);
        }

        const heading = document.createElement("h3");
        const issueCountLabel = `${group.issues.length} ${group.issues.length === 1 ? "issue" : "issues"}`;
        heading.classList.add("uk-margin-remove-bottom", "daily-edition-heading");

        const headingTitle = document.createElement("span");
        headingTitle.textContent = group.name;

        const headingMeta = document.createElement("span");
        headingMeta.className = "uk-text-meta uk-text-default";
        headingMeta.textContent = group.year > 0 ? `(${group.year} - ${issueCountLabel})` : `(${issueCountLabel})`;

        heading.appendChild(headingTitle);
        heading.appendChild(headingMeta);

        const issueList = document.createElement("div");
        issueList.className = "daily-issue-list";
        if (Number.isFinite(group.year) && group.year > 0) {
            issueList.classList.add(`daily-issue-list-year-${group.year}`);
        }
        if (!isCurrentEdition) {
            issueList.classList.add("daily-issue-list-past");
        }

        group.issues.forEach((issue) => {
            issueList.appendChild(this.#createIssueCard(issue, isCurrentEdition, !isCurrentEdition, group.year));
        });

        edition.appendChild(heading);
        edition.appendChild(issueList);

        return edition;
    }

    #renderCurrentEditionGroup(currentGroup) {
        this.#currentEditionList.replaceChildren();
        if (!currentGroup) {
            return;
        }

        this.#currentEditionList.appendChild(this.#buildEditionSection(currentGroup, true));
    }

    #renderPastEditionGroups(pastGroups) {
        this.#pastEditionsList.replaceChildren();
        if (!Array.isArray(pastGroups) || pastGroups.length === 0) {
            return;
        }

        pastGroups.forEach((group) => {
            this.#pastEditionsList.appendChild(this.#buildEditionSection(group, false));
        });
    }

    #renderEditionGroups(visibleState) {
        if (
            !visibleState ||
            (!visibleState.currentGroup && (!Array.isArray(visibleState.pastGroups) || visibleState.pastGroups.length === 0))
        ) {
            this.#currentEditionList.replaceChildren();
            this.#pastEditionsList.replaceChildren();
            this.#renderState(DailyStateType.EMPTY, "No issues available at the moment.");
            return;
        }

        this.#renderCurrentEditionGroup(visibleState.currentGroup);
        this.#renderPastEditionGroups(visibleState.pastGroups);
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
            this.#allGroups = groups;
            this.#showAllPastEditions = false;

            const visibleState = this.#getVisibleGroups(groups);
            this.#renderEditionGroups(visibleState);
            this.#syncPastToggle(visibleState.totalPastAvailable, visibleState.shownPastCount);
        } catch (error) {
            const message = error instanceof Error ? error.message : "Could not load Daily issues at the moment.";
            this.#renderState(DailyStateType.ERROR, message);
        }
    }
}

const daily = new Daily({
    currentEditionList: document.getElementById("daily-current-list"),
    pastEditionsList: document.getElementById("daily-archive-list"),
    showAllPastButton: document.getElementById("daily-show-all-past-btn"),
    archiveMoreLink: document.getElementById("daily-archive-more-link"),
    modalRoot: document.getElementById("daily-pdf-modal"),
    modalTitle: document.getElementById("daily-pdf-modal-title"),
    modalFrame: document.getElementById("daily-pdf-modal-frame"),
});

daily.build();
