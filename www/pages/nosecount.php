<h1>Registration Statistics</h1>

<style>
	@import url('css/countries.css');

	#ef-regstats a {
		color: #000;
		text-decoration: none;
	}
	#ef-regstats a:hover {
		text-decoration: underline;
	}
	#ef-regstats div[uk-grid] > div > div {
		position: relative;
	}
	#ef-regstats h3 {
		margin-bottom: 0;
	}
	#ef-regstats h2 {
		margin-top: 0;
		margin-bottom: 0;
	}
	div.ef-rs-barchart ~ button {
		position: absolute;
		top: 40px;
		right: 40px;
	}
	.ef-rs-piechart {
		width: 7rem;
		height: 7rem;
	}
	.ef-rs-barchart {
		height: 200px;
	}

	#ef-rs-country-list > article {
		padding: 0 6px 1px;
		border: 2px solid #fff;
		border-radius: 6px;
		background-color: #ddd;
	}
	#ef-rs-country-list > article > div {
		display: inline-block;
		position: relative;
		top: 2px;
		width: 1.66em;
		margin-right: .33em;
		background-position: center left;
		background-repeat: no-repeat;
	}
	#ef-rs-country-list > article > h4 {
		display: inline;
		margin: 0;
		font-size: 1em;
		font-family: monospace;
		font-weight: bold;
		color: #999;
	}
	#ef-rs-country-list > article > span {
		float: right;
		font-weight: bold;
	}

	/* looks better without step-end, 
	but causes +15% cpu load in Firefox,
	last tested in version 123 */
	.ef-rs-reg-opening-indicator-animation {
		animation: blink 2.5s infinite step-end;
	}
	@keyframes blink {
		0%, 100% {opacity: 1}
		50% { opacity: 0}
	}

	.ef-rs-legend {
		padding-top: 10px;
	}
	.ef-rs-legend > div ~ div {
		margin-left: 30px;
	}
	.ef-rs-legend tr {
		cursor: pointer;
	}
	.ef-rs-legend td {
		padding: 0 1px;
		vertical-align: top;
	}
	.ef-rs-legend td:nth-of-type(2),
	.ef-rs-legend td:nth-of-type(5) {
		text-align: right;
		font-weight: bold;
		vertical-align: top;
	}
	.ef-rs-legend table div {
		position: relative;
		top: 6px;
		width: 20px;
		height: 1em;
		margin-right: 4px;
	}
	.ef-rs-legend table tr:has(> :last-child:nth-child(6)) > :nth-child(3) {
		border-right: 1em solid transparent;
	}
	#ef-rs-age-avg-container {
		margin-top: calc(30px + .33em);
	}
</style>

<section>
	<p>Lorem Ipsum. Available placeholder IDs:</p>
	<ul>
		<li>#ef-rs-intro-timestamp: <span id="ef-rs-intro-timestamp"></span></li>
		<li>#ef-rs-intro-total: <span id="ef-rs-intro-total"></span></li>
		<li>#ef-rs-intro-new: <span id="ef-rs-intro-new"></span></li>
		<li>#ef-rs-intro-approved: <span id="ef-rs-intro-approved"></span></li>
		<li>#ef-rs-intro-partially-paid: <span id="ef-rs-intro-partially-paid"></span></li>
		<li>#ef-rs-intro-paid: <span id="ef-rs-intro-paid"></span></li>
		<li>#ef-rs-intro-checked-in: <span id="ef-rs-intro-checked-in"></span></li>
	</ul>
</section>

<section id="ef-regstats">
	<div class="uk-grid-small uk-grid-match uk-child-width-1-3@l uk-child-width-1-2@m uk-margin-bottom" uk-grid>
		<div>
			<div class="uk-card uk-card-default uk-card-body">
				<h3 class="uk-card-title">Total Registrations</h3>
				<h2 class="uk-heading-large" id="ef-rs-reg-total"></h2>
				<span id="ef-rs-reg-opening-indicator">&#9679;</span>
				<span id="ef-rs-reg-opening"></span>
			</div>
		</div>

		<div>
			<div class="uk-card uk-card-default uk-card-body">
				<h3 class="uk-card-title">Registration State</h3>
				<div class="ef-rs-legend uk-flex">
					<div class="ef-rs-piechart"><canvas id="ef-rs-reg-status"></canvas></div>
					<div id="ef-rs-reg-status-legend"></div>
				</div>
			</div>
		</div>

		<div>
			<div class="uk-card uk-card-default uk-card-body">
				<h3 class="uk-card-title">Ticket Types</h3>
				<div class="ef-rs-legend uk-flex">
					<div class="ef-rs-piechart"><canvas id="ef-rs-reg-types"></canvas></div>
					<div id="ef-rs-reg-types-legend"></div>
				</div>
			</div>
		</div>
	</div>

	<div class="uk-card uk-card-default uk-card-body uk-margin-bottom uk-width-1-1">
		<h3 class="uk-card-title uk-margin-bottom">Age Distribution</h3>
		<div id="ef-rs-age-avg-container" class="uk-position-right uk-margin-medium-right">
			<span class="uk-visible@s">average:</span>
			<span class="uk-hidden@s">avg:</span>
			<span id="ef-rs-age-avg" class="uk-text-bold"></span>
		</div>
		<div class="ef-rs-barchart"><canvas id="ef-rs-age"></canvas></div>
	</div>

	<div class="uk-card uk-card-default uk-card-body uk-margin-bottom" uk-filter="#ef-rs-country-list">
		<h3 class="uk-card-title">Origins by Country</h3>
		<p>sort by:
			<span uk-filter-control="sort: data-iso"><a href="#">country code</a></span> |
			<span uk-filter-control="sort: data-name"><a href="#">country name</a></span> |
			<span uk-filter-control="sort: data-count; order: desc"><a href="#">attendee count</a></span>
		</p>
		<div id="ef-rs-country-list" class="uk-column-1-4@l uk-columns-1-3@m uk-column-1-2@s"></div>
	</div>

	<div class="uk-grid-small uk-grid-match uk-child-width-1-3@l uk-child-width-1-2@m uk-margin-bottom" uk-grid>
		<div class="uk-width-2-3@m">
			<div class="uk-card uk-card-default uk-card-body">
				<h3 class="uk-card-title">Shirt Preorders</h3>
				<div class="ef-rs-legend uk-flex">
					<div class="ef-rs-piechart"><canvas id="ef-rs-reg-size"></canvas></div>
					<div id="ef-rs-reg-size-legend"></div>
				</div>
			</div>
		</div>
		<div class="uk-width-1-3@m">
			<div class="uk-card uk-card-default uk-card-body">
				<h3 class="uk-card-title uk-margin-bottom">Interests</h3>
				<table>
					<tr>
						<td class="uk-text-right"><span id="ef-rs-reg-interests-art" class="uk-text-bold"></span></td>
						<td><span uk-icon="image"></span>Art</td>
					</tr>
					<tr>
						<td class="uk-text-right"><span id="ef-rs-reg-interests-animation" class="uk-text-bold"></span></td>
						<td><span uk-icon="play-circle"></span>Animation</td>
					</tr>
					<tr>
						<td class="uk-text-right"><span id="ef-rs-reg-interests-fursuiting" class="uk-text-bold"></span></td>
						<td><span uk-icon="users"></span>Fursuiting</td>
					</tr>
					<tr>
						<td class="uk-text-right"><span id="ef-rs-reg-interests-music" class="uk-text-bold"></span></td>
						<td><span uk-icon="microphone"></span>Music</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</section>

<section>
	<p class="uk-text-right">
		data timestamp: <span id="ef-rs-timestamp"></span><br />
		next update in: <span id="ef-rs-update">-</span> seconds<br />
		<a href="regstats#raw" uk-toggle>show raw data</a>
	</p>
</section>

<div id="raw" class="uk-modal-container" uk-modal>
	<div class="uk-modal-dialog uk-modal-body">
		<h2 class="uk-modal-title">Raw Data</h2>
		<pre id="ef-rs-data-raw" class="uk-textarea uk-height-large"></pre>
		<p class="uk-text-right">
            <button class="uk-button uk-button-default uk-modal-close" type="button">Close</button>
        </p>
	</div>
</div>

<script src="js/chart.js"></script>
<script src="js/regstats.min.js"></script>
