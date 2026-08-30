<?php $page = [
    "owner"       => "@draconigen",
    "editor"      => "@draconigen",
    "title"       => "Page Ratings",
    "description" => "Import feedback feed and visualize all given feedback through the EF Website Feedback Feature.",
    "ogpImage"    => "",
    "keywords"    => "",
    "robots"      => ""
]; ?>

<style>
	#file-input {
		position: relative;
		padding: 12px 8px;
		border: 2px solid var(--ef-green);
		border-radius: var(--ef-border-radius);
	}
	.rating {
		position: relative;
		margin-bottom: 8px;
		font-size: 1.5rem;
		color: darkgray;
	}
	.rating > div {
		position: absolute;
		top: -2px;
		left: 0;
		overflow: hidden;
		color: gold;
	}
</style>

<section>
	<h1>Page Ratings</h1>
	<p>Select a telegram JSON feed export file from the page ratings reporting channel.</p>
	<input id="file-input" type="file" accept=".json,application/json" />
</section>

<section>
	<div id="results" class="uk-grid-small uk-child-width-1-3@l uk-child-width-1-2@m uk-margin-bottom" uk-grid="masonry: pack"></div>
</section>

<script>
const fileInput = document.getElementById('file-input');
const results = document.getElementById('results');

fileInput.addEventListener('change', (e) => {
	const file = e.target.files[0];
	if (!file) return;
	results.innerHTML = '';

	const reader = new FileReader();
	reader.onload = (e) => {
		try {
			const data = JSON.parse(e.target.result);
			processData(data);
		} catch (ex) {
			console.error('parse error: ' + ex.message);
		}
	};
	reader.onerror = () => console.error('read error');
	reader.readAsText(file);
});

// Pulls the plain text out of a Telegram-style "text" field,
// which can be a plain string or an array of strings / {type, text} parts.
function extractText(message) {
	const t = message.text;
	if (typeof t === 'string') return t;
	if (Array.isArray(t)) {
		return t.map(part => (typeof part === 'string' ? part : (part && part.text) || '')).join('');
	}
	return '';
}

function processData(data) {
	const messages = Array.isArray(data) ? data : data.messages;
	if (!Array.isArray(messages)) {
		console.error('No "messages" array found in json.');
		return;
	}

	const pages = new Map(); // page name -> { count, sum }
	let totalRatings = 0;

	for (const msg of messages) {
	const text = extractText(msg);
	if (!text) continue;

	const pageMatch = text.match(/Page:[ \t]*([^\n\r]+)/);
	const ratingMatch = text.match(/Rating:[ \t]*(\d+(?:\.\d+)?)[ \t]*\/[ \t]*5/);
	if (!pageMatch || !ratingMatch) continue;

	const page = pageMatch[1].trim();
	const rating = parseFloat(ratingMatch[1]);
	if (!page || isNaN(rating)) continue;

	const nameMatch = text.match(/Name:[ \t]*([^\n\r]*)/);
	const commentMatch = text.match(/Comment:[ \t]*([^\n\r]*)/);
	const name = nameMatch ? nameMatch[1].trim() : '';
	const comment = commentMatch ? commentMatch[1].trim() : '';
	const hasName = name.length > 0;

	if (!pages.has(page)) pages.set(page, { count: 0, sum: 0, comments: [] });
	const entry = pages.get(page);
	entry.count += 1;
	entry.sum += rating;
	if (comment) entry.comments.push({ name: hasName ? name : null, hasName, comment, rating });

	totalRatings += 1;
  }

	if (totalRatings === 0) {
		results.innerHTML = '<div id="empty">No "Page Rating Receipt" entries were found in this file.</div>';
		return;
	}

	const rows = [...pages.entries()]
		.map(([page, e]) => ({ page, count: e.count, avg: e.sum / e.count, comments: e.comments }))
		.sort((a, b) => b.count - a.count || a.page.localeCompare(b.page));

	const overallAvg = rows.reduce((s, r) => s + r.avg * r.count, 0) / totalRatings;
	const totalComments = rows.reduce((s, r) => s + r.comments.length, 0);

	rows.forEach(row => {
		console.log(row);

		// prepare comments
		console.log(row.comments.length);
		let comments = row.comments.length > 0? '<hr />' : '';
		row.comments.forEach(cmt => {
			comments += `<p>„<span class="uk-text-italic">${cmt.comment}</span>“ ${cmt.hasName? '':''}</p>`; // anon indicator
		});

		const div = document.createElement('div');
		div.innerHTML = `
			<div class="uk-card uk-card-default uk-card-body">
				<h3 class="uk-card-title uk-margin-remove">/${row.page}</h3>
				<span class="rating">
					★★★★★
					<div style="max-width: ${row.avg * 20 - 5}%">★★★★★</div>
				</span>
				<div><strong>${row.avg.toFixed(1)}</strong> / 5 based on <strong>${row.count}</strong> rating${row.count > 1? 's':''}</div>
				<div>${comments}</div>
			</div>
		`;
		results.appendChild(div);
	});

	results.innerHTML += `
		<div>
			<div class="uk-card uk-card-default uk-card-body">
				<h3 class="uk-card-title uk-margin-remove">Total</h3>
				<div>Average of <strong>${overallAvg.toFixed(1)}</strong> / 5 based on <strong>${totalRatings}</strong> rating${totalRatings > 1? 's':''} with <strong>${totalComments}</strong> comment${totalComments > 1? 's':''}</div>
			</div>
		</div>
	`
}
</script>