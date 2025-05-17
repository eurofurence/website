<h1>Open Staff Positions at Eurofurence</h1>
<section>
	<p>A convention as big as Eurofurence can't simply grow without people committing some of their time and skills to it - people like you. Every now and then we are looking for creative people willing to volunteer, to help us with making Eurofurence the best possible experience for everyone! In case you find yourself addressed by one of the following job-offers, feel free to drop us a line. We will gladly have you aboard.<br/><br/>We can't offer any form of payment, but we're sure that seeing all those happy attendees will be reward enough.</p>
</section>

<section>
	<div class="uk-grid-small" uk-grid>
		<?php
			$path = "pages/jobs/";
			$files = scandir($path);
			// shuffle($files);

			foreach ($files as $file) {
				if ($file === "." || $file === "..")
					continue;

				$frontmatter = ["id" => pathinfo($file, PATHINFO_FILENAME)];
			?>
			
			<div id="ef-job-<?= $frontmatter["id"] ?>" class="uk-modal-container" uk-modal>
				<div class="uk-modal-dialog uk-modal-body">
					<button class="uk-modal-close-default" type="button" uk-close></button>
					<?php include_once($path . $file); ?>
				</div>
			</div>
			
			<div>
				<div class="uk-card uk-card-small uk-card-default uk-card-body uk-card-hover" uk-toggle="target: #ef-job-<?= $frontmatter["id"] ?>">
					<h3 class="uk-card-title">
						<?= $frontmatter["department"] ?>
					</h3>
					<?= $frontmatter["title"] ?>
				</div>
			</div>
		<?php } ?>
	</div>
</section>

<hr />

<section>
	<p>These offers are subject to constant change throughout the year. If you're interested in helping us out, make sure to check this page periodically so that you don't miss your favorite job!</p>
</section>