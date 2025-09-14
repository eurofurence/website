<section>
	<h1>Featured Artist: Jukajo</h1>

	<p>Hallo Jukajo, hier sind die Rahmenbedingungen für den Banner, den wir von dir benötigen:</p>
	<p>Das Bild zeigt den gesamten Banner, in seiner vollen Höhe. Diese volle Höhe ist nur in der mobilen Ansicht (schmale Bildschirme) sichtbar, und nahezu vollständig von dem dunklen Overlay mit EF-Thema & Datum verdeckt. Das Ergegnis kannst du dir auch anschauen, wenn du <a href="home">die Home-Seite</a> auf einem Smartphone öffnest.</p>
	<p>Der Bereich oberhalb der Linie zeigt einen "mindestens sichtbaren" Abschnitt mitsamt Overlay, wie er auf größeren Bildschirmen (PC, Fernseher) oberhalb einer Grenze von 1200 Pixeln zu sehen sein sollte. AB 1200 Pixeln abwärts gilt: desto schmaler der Bildschirm (oder das Browser-Fenster), umso schmaler auch der Banner, und umso mehr Banner unterhalt der Linie wird sichtbar. Du kannst diesen Effekt auch testen, indem du <a href="home">die Home-Seite</a> auf einem großen Bildschirm öffnest und den Browser langsam schmaler ziehst.</p>

	<img src="img/pages/website/banner_ratio_example.png" alt="Banner ratio example" />

	<p>Es ist also wichtig, dass der Banner irgendwie so gestaltet ist, dass der Teil oberhalb der Linie eigenständig funktioniert, und der Rest in jeglicher Höhe funktioniert (weil zwischen Desktop und Mobil ja auch noch kleinere Bildschirme, Tablets, große Smartphones, usw. liegen).</p>
</section>

<hr />

<section id="ef-badger" class="uk-column-1-2@l">
	<div>
		<h2>Eurofurence Badger Addon</h2>
		<p>This brand-new browser addon will enhance your visit to the Eurofurence website by adding <span class="ef-new"></span> badges to pages that have been updated since you last viewed them!</p>
		<p>Available for <a href="https://addons.mozilla.org/firefox/addon/eurofurence-badger/" target="_blank">Mozilla Firefox</a> and <a href="https://chrome.google.com/" target="_blank">Google Chrome</a>.</p>
	</div>
	<div>
		<h2>Third Party Attributions</h2>
		<ul>
			<li><a href="https://getuikit.com" target="_blank">UIkit</a> by <a href="https://yootheme.com/" target="_blank">YOOtheme GmbH</a> (<a href="https://github.com/uikit/uikit/blob/develop/LICENSE.md" target="_blank">license</a>)</li>
			<li><a href="" target="_blank">Mastodon Embed Timeline Widget</a> by i.j (<a href="https://gitlab.com/idotj/mastodon-embed-timeline/" target="_blank">source</a>, <a href="https://gitlab.com/idotj/mastodon-embed-timeline/-/blob/master/LICENSE" target="_blank">license</a>)
		</ul>
	</div>
</section>

<hr />

<section>
	<h2>Website Team</h2>
	<p>Please direct any comments, ideas or critique about our website to the following folks:</p>

	<div class="ef-people uk-grid-match" uk-grid>
	<?php
		$members = [
			// ['Name', 'Title', 'Image', 'Link'],
			['Flam', 'Director &amp; Main Website', 'flam.png', 'https://draconigen.dogpixels.net/'],
			['fafnyr', 'Vice Director &amp; System Administration', 'fafnyr.png', 'https://www.furaffinity.net/user/fafnyr/'],
			['Alex Dax', 'Writing', 'sithy.png', 'https://twitter.com/MxSithy'],
			['Fenmar', 'Archive', 'fenmar.png', 'https://fenmar.de/'],
			['Fenrikur', 'Nosecount Intro', 'fenrikur.png', 'https://twitter.com/Fenrikur/'],
			['Fleeks', 'Logo Design', 'fleeks.png', 'https://fleeks.art/'],
			['Lio', 'Writing', 'lio.jpg', 'https://lio.to/'],
			['OxySynth', 'Fursuit Photoshoot Gallery', 'oxy.png', 'https://bsky.app/profile/oxysynth.bsky.social'],
			['Rig', 'HelpCenter', 'rig.jpg', ''],
			['Sebin', 'Accessibility', 'sebin.png', 'https://twitter.com/SebinNyshkim'],
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

<hr />

<section class="uk-margin-top">
	<h2>Tech Support &amp; Bug Report</h2>
	
	<p>Layout broken? Pages display weird content? You don't like the colors? We're grateful for every bug report (and feedback) you file in.<br/>
	If you would like to include a screenshot, please upload it to any host and include a link in your report. After all, a picture says more than thousand words.<br/>
	When doing so, please ensure that you include every detail about the circumstances, under which the bug occurred.</p>	
	<p><a href="https://help.eurofurence.org/contact/web/bugreport" target="_blank">Contact the Website Team</a></p>
</section>