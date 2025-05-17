class Partners
{
    static async fetch()
    {
        var partners_data = null;

        try {
            partners_data = await (await fetch("config/partners.json")).json();
            if (!partners_data)
            {
                console.info("[ef-partners] partners_data", partners_data);
                throw "malformed partners_data";
            }
        }
        catch(ex)
        {
            console.error(`[ef-partners] failed to load "partners.json", reason: ${ex}`);
            return;
        }

        return partners_data.conventions;
    }

    static async fill(items)
    {
        for (const key in items)
        {
            if (!items[key].enable)
                continue;

            partners.innerHTML += `<li><a href="${items[key].target}" target="_blank" class="hide-ext"><img src="${items[key].file}" alt="${items[key].target}" /></a></li>`
        }
    }
}

window.addEventListener("load", async () =>
{
	// indicate loading activity
	partners.classList.remove("js-disabled");
	partners.innerText = "loading latest partners …";

	// fetch partners
	try
	{
		const items = await Partners.fetch();
		
		// prepare partners div
		if (!items.length !== 0)
		{
			partners.innerText = "";
		}
		else
		{
			partners.innerText = "<error loading partners>";
		}

		// fill news div
		Partners.fill(items);
	}
	catch(ex)
	{
		console.warn(ex);
		partners.innerText = "<error loading partners>";
	}

	document.body.dispatchEvent(new CustomEvent("partnersLoaded"));
});