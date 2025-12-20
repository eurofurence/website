<style>
.ef-people img {
	display: inline;
	float: left;
	width: 80px;
	height: 80px;
	border-radius: 100%;
	margin-right: 12px;
}

.ef-people h3 {
	margin: 0;
}

.attributions-icon {
	width: 80px;
	height: 80px;
	text-decoration: none;
}
</style>

<section>
	<h1>Featured Artist: Jukajo</h1>

	[TODO]
</section>

<section>
	<h2>Third Party Attributions</h2>
	<div class="uk-grid-small" uk-grid>
		
		<div>
			<div class="uk-card uk-card-default uk-card-small uk-card-body">
				<div uk-grid>
					<div>
						<a href="https://getuikit.com" target="_blank" class="hide-ext">
							<img src="img/pages/website/uikit.jpg" alt="UIkit Logo" class="rounded attributions-icon" />
						</a>
					</div>
					<div>
						<h4><a href="https://getuikit.com" target="_blank">UIkit</a> by <a href="https://yootheme.com/" target="_blank">YOOtheme GmbH</a></h4>
						<a href="https://github.com/uikit/uikit/blob/develop/LICENSE.md" target="_blank"><span uk-icon="file-text"></span>License</a>
					</div>
				</div>
			</div>
		</div>

		<div>
			<div class="uk-card uk-card-default uk-card-small uk-card-body">
				<div uk-grid>
					<div>
						<a href="https://getuikit.com" target="_blank" class="hide-ext">
							<img src="img/pages/website/chartjs.svg" alt="Chart.js Logo" class="rounded attributions-icon" />
						</a>
					</div>
					<div>
						<h4><a href="https://www.chartjs.org/" target="_blank">Chart.js</a> by Chart.js Contributors</h4>
						<a href="https://github.com/chartjs/Chart.js/blob/master/LICENSE.md" target="_blank"><span uk-icon="file-text"></span>License</a>
					</div>
				</div>
			</div>
		</div>
		
		<div>
			<div class="uk-card uk-card-default uk-card-small uk-card-body">
				<div uk-grid>
					<div>
						<a href="https://gitlab.com/idotj/mastodon-embed-timeline/" target="_blank" class="hide-ext">
							<img src="img/pages/website/mastodon.svg" alt="Mastodon Logo" class="rounded attributions-icon" />
						</a>
					</div>
					<div>
						<h4><a href="https://gitlab.com/idotj/mastodon-embed-timeline/" target="_blank">Mastodon embed timeline widget</a> by i.j</h4>
						<a href="https://gitlab.com/idotj/mastodon-embed-timeline/-/blob/master/LICENSE" target="_blank"><span uk-icon="file-text"></span>License</a>
						<br />
						compliance notice: this website's source is on <a href="https://github.com/eurofurence/website" target="_blank">github.com/eurofurence/website</a>.
					</div>
				</div>
			</div>
		</div>

	</div>
</section>

<section class="uk-margin-top">
	<h2>Tech Support &amp; Bug Report</h2>
	
	<p>Layout broken? Pages display weird content? You don't like the colors? We're grateful for every bug report (and feedback) you file in.<br/>
	If you would like to include a screenshot, please upload it to any host and include a link in your report. After all, a picture says more than thousand words.<br/>
	When doing so, please ensure that you include every detail about the circumstances, under which the bug occurred.</p>	
	<p><a href="https://help.eurofurence.org/contact/web/bugreport" target="_blank">Contact the Website Team</a></p>
</section>

<section>
	<h2>Website Team</h2>
	<p>Please direct any comments, ideas or critique about our website to the following folks:</p>

	<div class="ef-people uk-grid-match" uk-grid>
	<?php
		$members = [
			// ['Name', 'Title', 'Image', 'Link'],
			['Flam', 'Director &amp; Main Website', 'flam.png', 'https://draconigen.dogpixels.net/'],
			['fafnyr', 'Vice Director &amp; System Administration', 'fafnyr.png', 'https://www.furaffinity.net/user/fafnyr/'],
			['Fenmar', 'Archive', 'fenmar.png', 'https://fenmar.de/'],
			//['Fenrikur', 'Nosecount Intro', 'fenrikur.png', 'https://twitter.com/Fenrikur/'],
			['Fleeks', 'Logo Design', 'fleeks.png', 'https://fleeks.art/'],
			['Lio', 'Writing', 'lio.png', 'https://lio.to/'],
			['OxySynth', 'Fursuit Photoshoot Gallery', 'oxy.png', 'https://bsky.app/profile/oxysynth.bsky.social'],
			//['Sebin', 'Accessibility', 'sebin.png', 'https://twitter.com/SebinNyshkim'],
			['Vinaru', 'Banner Exchange', 'vinaru.png', 'https://www.furaffinity.net/user/vinaru'],
			['Xenor', 'Survey', 'xenor.png', 'https://bsky.app/profile/xenor.bsky.social'],
		];

		foreach ($members as $m) { ?>
			<a href="<?= $m[3] ?>" target="_blank" class="hide-ext uk-width-medium"<?= empty($m[3])? 'onclick="return false;"' : '' ?>>
				<div>
					<img src="img/pages/website/<?= $m[2] ?>" alt="<?= $m[2] ?>" />
					<h3><?= $m[0] ?></h3>
					<span><?= $m[1] ?></span>
				</div>
			</a>
		<?php } ?>
	</div>
</section>

<section>
	<h2>Banner Exchange</h2>
	<p><strong>Dear Convention Owner</strong>,<br />you want your convention to appear in the partners slide on the bottom of our website? <a href="https://help.eurofurence.org/contact/web" target="_blank">Drop us a message</a>.

	<h3>We will need two things from you:</h3>
	<ul>
		<li>Our banner on your website. You'll find our file under the permanent URL <strong>https://www.eurofurence.org/ef_banner.gif</strong> - When placing it onto your page, feel free to use this direct link. Our hosting is very stable, and very unlikely to disappear anytime in the far future. Plus, we'll eventually maintain a more theme-specific picture according to the annual convention theme.</li>
		<li>Your banner. It should be 200x80 px in size. Static pictures are preferred, but not a must. If you decide to animate your banner, please don't make it too annoying or load-heavy. We reserve the right to reject your submission if we find it too flashy, quick-changing, unfitting or exceedingly big as a file; In such case, we'd get back at you for a proper version, of course.</li>
	</ul>
	<h3>There are two ways to submit your banner:</h3>
	<ol>
		<li><strong>Preferred:</strong> You give us an URL, under which your banner is going to be permanently available. That way, whenever you decide to change your banner (e.g. because it's theme-based), it'll change on our website as well. Downside: You'll have to make sure that your banner really IS available - preferably forever! We periodically (every 14 days) check all banners. If yours is no longer reachable it will temporarily be removed from the slider. If it is updated, the new one will show after the next check-cycle - no need to notify us of updates. Also don't worry: We cache the banners and only access them every 2 weeks for re-checking, so no permanent access to your website is made.</li>
		<li><strong>Fallback:</strong> You send us a file. Done. Easy as that.</li>
	</ol>

	<br/>And this is how our current banner looks like: <br/><br/>
	<img class="framedimage" src="https://www.eurofurence.org/ef_banner.gif" alt="Eurofurence Banner" /><br/><br/>
</section>