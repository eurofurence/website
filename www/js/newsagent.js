class NewsAgent
{
	static url = "https://forum.eurofurence.org/index.php/board,6.0.html?action=.xml;sa=recent;limit=6";
	static subjectLength = 32;

	static async fetch()
	{
		let ret = [];
		
		const response = await fetch(NewsAgent.url);
		
		if (!response.ok)
		{
			console.error("[EF-Web NewsAgent] Fetching news from EF Forum failed.");
			return ret;
		}

		const responseXML = (new window.DOMParser()).parseFromString(await (response).text(), "text/xml");
		if (responseXML === null)
		{
			console.error("[EF-Web NewsAgent] Parsing news from EF Forum failed.");
			return ret;
		}

		for (let i = 0; i < responseXML.documentElement.children.length; i++)
		{
			const item = responseXML.documentElement.children[i];

			const time_raw = item.children[0].textContent;
			const subject_raw = item.children[2].textContent;
			const link_raw = item.children[8].textContent;

			// define time
			let time = time_raw.substr(0, 17); // DD.MM.YYYY, HH:mm:ss => DD.MM.YYYY, HH:mm

			let timestamp = Date.UTC(
				+time.substr(6, 4),		// year
				+time.substr(3, 2) - 1,	// month
				+time.substr(0, 2),		// day
				+time.substr(12, 2),	// hour
				+time.substr(15, 2)		// minute
			) / 1000;

			// define subject
			let subject =
				subject_raw.length <= NewsAgent.subjectLength ? 
				subject_raw : 
				subject_raw.substr(0, subject_raw.lastIndexOf(" ", NewsAgent.subjectLength)) + "…";
			
			// define link
			let link = link_raw;
			
			ret.push({time: time, timestamp: timestamp, subject: subject, link: link});
		}

		// console.info("[EF-Web NewsAgent] Results:", ret);

		return ret;
	}

	static fill(items) {
		for (let i = 0; i < items.length; i++)
		{
			const item = items[i];
			news.innerHTML += `<div role="listitem"><a href="${item.link}" target="_blank" data-lastmodified="${item.timestamp}"><cite>${item.subject}</cite><time>${item.time}</time></a></div>`;
		}
	}
}

// must be here because EF severs don't allow inline scripts
window.addEventListener("load", async () => 
{
	// indicate loading activity
	news.classList.remove("js-disabled");
	news.innerText = "loading latest announcements …";

	// fetch news
	try
	{
		const items = await NewsAgent.fetch();
		
		// prepare news div
		if (items.length !== 0)
		{
			news.innerText = "";
		}
		else
		{
			news.innerText = "<error loading announcements>";
		}

		// fill news div
		NewsAgent.fill(items);
	}
	catch(ex)
	{
		console.warn(ex);
		news.innerText = "<error loading announcements>";
	}

	document.body.dispatchEvent(new CustomEvent("newsLoaded"));
});

/* for testing purposes only

setTimeout(() => {
	news.innerText = "";
	NewsAgent.fill(
	[
		{
		  "time": "28.02.2022, 16:05",
		  "timestamp": 1646064300,
		  "subject": "Never gonna give you up…",
		  "link": "https://www.dogpixels.net/draconigen/"
		},
		{
		  "time": "26.02.2022, 23:08",
		  "timestamp": 1645916880,
		  "subject": "Never gonna let you down…",
		  "link": "https://www.dogpixels.net/draconigen/"
		},
		{
		  "time": "26.02.2022, 19:37",
		  "timestamp": 1645904220,
		  "subject": "Never gonna run around and desert you…",
		  "link": "https://www.dogpixels.net/draconigen/"
		},
		{
		  "time": "25.01.2022, 21:18",
		  "timestamp": 1643145480,
		  "subject": "Never gonna make you cry…",
		  "link": "https://www.dogpixels.net/draconigen/"
		},
		{
		  "time": "22.01.2021, 19:05",
		  "timestamp": 1611342300,
		  "subject": "Never gonna say goodbye",
		  "link": "https://www.dogpixels.net/draconigen/"
		},
		{
		  "time": "01.05.2020, 09:45",
		  "timestamp": 1588326300,
		  "subject": "Never gonna tell a lie and hurt you…",
		  "link": "https://www.dogpixels.net/draconigen/"
		}
	])
}, 1000);

*/