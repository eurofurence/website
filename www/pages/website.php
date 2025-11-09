<section>
	<h1>Featured Artist: Jukajo</h1>

	<p>Hallo Jukajo, hier sind die Rahmenbedingungen für den Banner, den wir von dir benötigen:</p>
	<p>Das Bild zeigt den gesamten Banner, in seiner vollen Höhe. Diese volle Höhe ist nur in der mobilen Ansicht (schmale Bildschirme) sichtbar, und nahezu vollständig von dem dunklen Overlay mit EF-Thema & Datum verdeckt. Das Ergegnis kannst du dir auch anschauen, wenn du <a href="home">die Home-Seite</a> auf einem Smartphone öffnest.</p>
	<p>Der Bereich oberhalb der Linie zeigt einen "mindestens sichtbaren" Abschnitt mitsamt Overlay, wie er auf größeren Bildschirmen (PC, Fernseher) oberhalb einer Grenze von 1200 Pixeln zu sehen sein sollte. AB 1200 Pixeln abwärts gilt: desto schmaler der Bildschirm (oder das Browser-Fenster), umso schmaler auch der Banner, und umso mehr Banner unterhalt der Linie wird sichtbar. Du kannst diesen Effekt auch testen, indem du <a href="home">die Home-Seite</a> auf einem großen Bildschirm öffnest und den Browser langsam schmaler ziehst.</p>

	<img src="img/pages/website/banner_ratio_example.png" alt="Banner ratio example" />

	<p>Es ist also wichtig, dass der Banner irgendwie so gestaltet ist, dass der Teil oberhalb der Linie eigenständig funktioniert, und der Rest in jeglicher Höhe funktioniert (weil zwischen Desktop und Mobil ja auch noch kleinere Bildschirme, Tablets, große Smartphones, usw. liegen).</p>
</section>

<hr />

<h2>Banner Exchange</h2>
<section>
	<p>Dear Convention Owner,<br />you want your convention to appear in the partners slide on the bottom of our website? <a href="https://help.eurofurence.org/contact/web" target="_blank">Drop us a message</a>.
</section>

<section>
	<h3>We will need two things from you:</h3>
	<ul>
		<li>Our banner on your website. You'll find our file under the permanent URL <strong>https://www.eurofurence.org/ef_banner.gif</strong> - When placing it onto your page, feel free to use this direct link. Our hosting is very stable, and very unlikely to disappear anytime in the far future. Plus, we'll eventually maintain a more theme-specific picture according to the annual convention theme.</li>
		<li>Your banner. It should be 200x80 px in size. Static pictures are preferred, but not a must. If you decide to animate your banner, please don't make it too annoying or load-heavy. We reserve the right to reject your submission if we find it too flashy, quick-changing, unfitting or exceedingly big as a file; In such case, we'd get back at you for a proper version, of course.</li>
	</ul><br/>
	<h3>There are two ways to submit your banner:</h3>
	<ol>
		<li><strong>Preferred:</strong> You give us an URL, under which your banner is going to be permanently available. That way, whenever you decide to change your banner (e.g. because it's theme-based), it'll change on our website as well. Downside: You'll have to make sure that your banner really IS available - preferably forever! We periodically (every 14 days) check all banners. If yours is no longer reachable it will temporarily be removed from the slider. If it is updated, the new one will show after the next check-cycle - no need to notify us of updates. Also don't worry: We cache the banners and only access them every 2 weeks for re-checking, so no permanent access to your website is made.</li>
		<li><strong>Fallback:</strong> You send us a file. Done. Easy as that.</li>
	</ol>
</section>

<section>
	<br/>And this is how our current banner looks like: <br/><br/>
	<img class="framedimage" src="https://www.eurofurence.org/ef_banner.gif" alt="Eurofurence Banner" /><br/><br/>
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