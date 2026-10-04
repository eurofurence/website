<?php $page = [
    "owner"       => "@draconigen",
    "editor"      => "@draconigen",
    "title"       => "Website Art Details",
    "description" => "Visual Guide for the Website Banner.",
    "keywords"    => "",
    "ogpImage"    => "",
    "robots"      => "noindex, nofollow, noodp"
]; ?>

<section>
	<h1>Eurofurence 30+ Website Banner Art Details</h1>

	<p>Dear Darbaras, thank you for volunteering to contribute next year's Website Banner Art. The following guide attempts to explain the format required by the website design introduced with Eurofurence 30.</p>

	<div class="uk-text-center">
		<img src="img/pages/website/banner_ratio_example.png" alt="Banner ratio example" />
		<!-- <img src="img/pages/website/darbaras.png" alt="Banner ratio example" /> -->
	</div>

	<p>Above is a banner example in its full height. This full height would only be visible on mobile devices (or narrow windows) and covered almost entirely by the dark box with the Eurofurence theme & date that grows with the narrow view. This is how it looks live:</p>
	
	<!-- copy from home.php -->
	 
	<div id="ef-home-banner" style="background-image: url(img/pages/website/darbaras.png)">
		<div>
			<h1>Eurofurence <?= $this->current->number ?></h1>
			<p class="ef-text-large-m">
				<?= $this->current->theme ?> <br />
				<?= $this->current->dates ?> <br />
				<?= $this->current->location ?>
			</p>
		</div>
	</div>

	<!-- end of copy -->

	<p>If you are on a PC, you can resize the window width to see how the banner shrinks and widens.<br />
	This whole behavior caps above <strong>1200 px</strong> viewport size, the website does not grow beyond that. (Yes, it breaks below 400 px, but who has a screen smaller than that?!)</p>

	<p>Please account for this varying viewport in your composition. It's best of the part above the line works well on its own, while the rest below the line works at any given height (because there's a near-endless variety between the most narrow mobile screen and a widescreen pc, which is capped at 1200 px).</p>

	<p>I hope this was clear enough. If in doubt, don't hesitate to poke me and I will try to rephrase.</p>
	<p>Cheers,<br />Flam &lt;3</p>
</section>
