class EFnavModal {
    constructor() {
        this.element = document.getElementById("efnav-modal");
        this.titleEl = this.element?.querySelector(".efnav-modal-title");
        this.subtitleEl = this.element?.querySelector(".efnav-modal-subtitle");
        this.tabsWrapEl = this.element?.querySelector(".efnav-modal-tabs-wrap");
        this.tabsEl = this.element?.querySelector(".efnav-modal-tabs");
        this.iframeEl = this.element?.querySelector(".efnav-modal-iframe");
        this.locations = [];
        this.activeIndex = 0;

        this.tabsEl?.addEventListener("click", (event) => {
            const tab = event.target.closest("a[data-efnav-index]");
            if (!tab) return;

            event.preventDefault();
            this.showLocation(Number(tab.dataset.efnavIndex));
        });
    }

    open(locations) {
        if (!this.element || locations.length === 0) return;

        this.locations = locations;
        this.activeIndex = 0;
        this.renderTabs();
        this.showLocation(0, true);
        UIkit.modal(this.element).show();
    }

    renderTabs() {
        const showTabs = this.locations.length > 1;
        this.tabsWrapEl.hidden = !showTabs;
        if (!showTabs) {
            this.tabsEl.innerHTML = "";
            return;
        }

        this.tabsEl.innerHTML = this.locations
            .map((location, index) => {
                const label = location.title;
                const tooltip = location.tooltip || label;
                const tooltipAttributes = label.length > 25 ? ` title="${escapeHtml(tooltip)}" uk-tooltip="pos: top"` : "";
                return `<li><a href="#" data-efnav-index="${index}"${tooltipAttributes}>${escapeHtml(label)}</a></li>`;
            })
            .join("");
    }

    showLocation(index, forceReload = false) {
        const location = this.locations[index];
        if (!location) return;

        this.activeIndex = index;
        this.titleEl.textContent = location.title;
        this.subtitleEl.textContent = location.subtitle;
        this.subtitleEl.hidden = !location.subtitle;
        this.iframeEl.title = `${location.title} - EFnav Map`;

        const embedUrl = `https://nav.eurofurence.org/embed/l/${location.slug}`;
        if (forceReload || this.iframeEl.src !== embedUrl) {
            this.iframeEl.src = embedUrl;
        }

        this.tabsEl.querySelectorAll("li").forEach((tab, tabIndex) => {
            tab.classList.toggle("uk-active", tabIndex === index);
        });
    }
}

function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

function normalizeLocations(locations) {
    const values = Array.isArray(locations) ? locations : [locations];
    return values
        .map((location) => ({
            title: location?.title || "Location",
            subtitle: location?.subtitle || "",
            slug: location?.slug || "",
            tooltip: typeof location?.tooltip === "string" ? location.tooltip.trim() : "",
        }))
        .filter((location) => location.slug);
}

function getEFnavContainer(containerId) {
    const container = document.getElementById(containerId);
    if (!container) {
        console.error(`Container with ID "${containerId}" not found`);
        return null;
    }
    return container;
}

function prepareEFnavContainer(container) {
    const section = container.closest("section");
    container.classList.remove("uk-column-span");
    container.classList.add("efnav-container");
    if (section) {
        section.classList.add("efnav-section");
        if (section.firstElementChild !== container) {
            section.insertBefore(container, section.firstElementChild);
        }
    }
}

const efnavModal = new EFnavModal();

/**
 * Helper to render an icon button in provided container that opens up EFnav modal for provided location(s).
 *
 * @param {string} containerId - ID of the button container
 * @param {Object|Array} locations - Location (or a list of locations) configuration
 * @param {Object} [options] - Button options: icon and tooltip
 */
function createEFnavTrigger(containerId, locations, options = {}) {
    return null; // Fuvii: I'm sorry for this dirty, dirty hack until you implement a global config. Cheers, Flam <3
    const container = getEFnavContainer(containerId);
    const normalizedLocations = normalizeLocations(locations);
    if (!container || normalizedLocations.length === 0) {
        if (container) {
            console.error(`No valid locations provided for container "${containerId}".`);
        }
        return null;
    }

    prepareEFnavContainer(container);

    const icon = (typeof options.icon === "string" && options.icon.trim()) || "location";
    const defaultTooltip = `Location${normalizedLocations.length > 1 ? "s" : ""} on map`;
    const tooltip = (typeof options.tooltip === "string" && options.tooltip.trim()) || defaultTooltip;
    const trigger = document.createElement("button");
    trigger.type = "button";
    trigger.className = "efnav-launcher";
    trigger.setAttribute("aria-label", `${defaultTooltip} - Open location map`);
    trigger.setAttribute("title", tooltip);
    trigger.setAttribute("uk-tooltip", "pos: left");
    trigger.innerHTML = `<span class="efnav-icon" uk-icon="icon: ${escapeHtml(icon)}"></span>`;
    trigger.addEventListener("click", () => efnavModal.open(normalizedLocations));

    container.appendChild(trigger);
}
