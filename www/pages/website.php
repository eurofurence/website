<section>
	<h1>Featured Artist: Rudzik</h1>
</section>
<section class="uk-column-1-2@l">
	<div>
		<p class="uk-text-italic">Hey there, fellow travelers of imagination! I'm Rudzik, a digital artist hailing from Germany. 
		<p class="uk-text-italic">I'm all about furry and fantasy art - think adorable fur creatures and majestic dragons! 
		<p class="uk-text-italic">Since 2020, Rudzik's been making magic happen full-time. </p>
		<p class="uk-text-italic">Let's embark on this creative journey together and explore the wonders of imagination in my enchanting art! </p>
	
		<h2>Links &amp; Projects</h2>
		<ul>
			<li><a href="https://rudzik.art/" target="_blank">Rudzik's Homepage</a></li>
			<li><a href="https://coffeehousecreatures.com" target="_blank">Coffee House Creatures</a></li>
		</ul>
	</div>

	<div>
		<?php
			$images = [
				"ef2024_sample1_by_rudzik.jpg",
				"ef2024_sample2_by_rudzik.jpg",
				"ef2024_sample3_by_rudzik.jpg",
				"ef2024_sample4_by_rudzik.jpg"
			]
		?>
		<div uk-slideshow="autoplay: true; autoplay-interval: 5000; animation: pull; ratio: 3:1">
			<ul class="uk-slideshow-items" uk-lightbox>
			<?php foreach ($images as $img) { ?>
				<li><a href="img/art/<?= $img ?>"><img src="img/art/thumbs/<?= $img ?>" width="600" height="315" alt="Art by Rudzik" /></a></li>
			<?php } ?>
			</ul>
		</div>
	</div>
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
			<li><a href="http://www.myfont.de/fonts/infos/3586-Hemi-Head-426.html" target="_blank">Hemi Head 426</a> by Larabie Fonts (<a href="fonts/read_me.html" target="_blank">license</a>)</li>
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
			['draconigen', 'Director &amp; Main Website', 'draconigen.png', 'https://www.dogpixels.net/draconigen/'],
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
			['Xenor', 'Survey', 'xenor.png', 'https://twitter.com/XenorThesergal'],
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