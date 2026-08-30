<?php
	header("Content-Type: text/html; charset=UTF-8");
	include("src/core.php");
	include("src/telegram/telegram.php");
	$web = new EFWebCore("config/core.json");
?>

<!DOCTYPE html>

<html prefix="og: http://ogp.me/ns#" lang="en">
	<head>
		<title><?= $web->page->title ?></title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="HandheldFriendly" content="true" /> 
		<meta name="mobile-web-app-capable" content="yes" />
		<meta name="description" content="<?= $web->page->description ?>" />
		<meta name="keywords" content="<?= $web->page->keywords ?>" />		
		<meta name="robots" content="<?= $web->page->robots ?>" />
		<meta name="author" content="web@eurofurence.org" />
		<meta name="rating" content="general" />
		<meta name="theme-color" content="<?= $web->config->convention->themeColor ?>" />
		<meta name="google" content="notranslate" /><!-- prevent Edge/Bing from "translating" this page -->

		<base href="<?= $web->config->base ?>" />

		<link rel="apple-touch-icon" sizes="57x57" href="img/icon/apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="img/icon/apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="img/icon/apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="img/icon/apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="img/icon/apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="img/icon/apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="img/icon/apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="img/icon/apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="img/icon/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="192x192" href="img/icon/android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="img/icon/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="img/icon/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="img/icon/favicon-16x16.png">
		<link rel="shortcut icon" href="favicon.ico">
		<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">

		<meta name="twitter:card" content="summary_large_image" />
		<meta name="twitter:site" content="@eurofurence" />
		<meta name="twitter:creator" content="@eurofurence" />
		<meta name="twitter:title" content="<?= $web->page->title ?>" />
		<meta name="twitter:description" content="<?= $web->page->description ?>" />
		<meta name="twitter:image" content="<?= $web->page->ogpImage ?>" />

		<meta property="og:image" content="<?= $web->page->ogpImage ?>" />
		<meta property="og:image:width" content="<?= $web->page->ogpImageWidth ?>" />
		<meta property="og:image:height" content="<?= $web->page->ogpImageHeight ?>" />
		<meta property="og:title" content="<?= $web->page->title ?>" />
		<meta property="og:description" content="<?= $web->page->description ?>" />
		<meta property="og:type" content="website" />
		<meta property="og:url" content="<?= $web->get_full_url() ?>" />
		<meta property="og:site_name" content="Eurofurence <?= $web->config->convention->number ?> - <?= $web->config->convention->theme ?>" />

		<link rel="canonical" href="<?= $web->get_full_url() ?>" />

		<?php 
		if (isset($web->page->previous)) 
			echo '<link rel="prev" href="https://www.eurofurence.org/EF' . $web->config->convention->number . '/' . $web->page->previous . '" />' . "\n";
		
		if (isset($web->page->next)) 
			echo '<link rel="next" href="https://www.eurofurence.org/EF' . $web->config->convention->number . '/' . $web->page->next . '" />' . "\n";
		?>

		<?php 
		$bcdata = $web->get_breadcrumb_data();
		$pos = 2;
		?>

		<script type="application/ld+json">
		{
			"@context": "http://schema.org",
			"@type": "BreadcrumbList",
			"itemListElement": 
			[
				{
					"@type": "ListItem",
					"position": 1,
					"item": 
					{
						"@id": "https://www.eurofurence.org",
						"name": "Eurofurence <?= $web->config->convention->number ?>" 
					}
				}
			<?php foreach ($bcdata as $key => $bc) { ?>
				,{
					"@type": "ListItem",
					"position": <?= $pos++ ?>,
					"item":
					{
						"@id": "<?= $bc->url ?>",
						"name": "<?= $bc->name ?>"
					}
				}
			<?php } // end of foreach loop ?>
			]
		}
		</script>

		<script type='application/ld+json'> 
		{
			"@context": "http://www.schema.org",
			"@type": "Event",
			"name": "Eurofurence <?= $web->config->convention->number ?>",
			"url": "https://www.eurofurence.org",
			"organizer": {
				"@name": "Eurofurence e.V.",
				"url": "https://www.eurofurence.de/"
			},
			"description": "The <?= $web->config->convention->ordinal ?> edition of Europe's largest furry convention, themed '<?= $web->config->convention->theme ?>'",
			"startDate": "<?= $web->config->convention->start ?>",
			"endDate": "<?= $web->config->convention->end ?>",
			"eventStatus": "https://schema.org/EventScheduled",
			"eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
			"image": "<?= $web->config->base . $web->page->ogpImage ?>",
			"location": {
				"@type": "Place",
				"name": "CCH Hamburg",
				"sameAs": "https://www.cch.de/",
				"address": {
					"@type": "PostalAddress",
					"streetAddress": "Congresspl. 1",
					"addressLocality": "Hamburg",
					"postalCode": "20355",
					"addressCountry": "Germany"
				}
			}
		}
		</script>

		<script type="application/ld+json">
		{
			"@context": "http://schema.org",
			"@type": "Organization",
			"name": "Eurofurence",
			"url": "https://www.eurofurence.org",
			"logo": "<?= $web->config->base ?>apple_favicon.png",
			"sameAs": [
				"https://twitter.com/eurofurence",
				"https://www.facebook.com/Eurofurence",
				"https://vimeo.com/eurofurence"
			]
		}
		</script>

        <script src="src/env-config.php"></script>

		<link rel="stylesheet" href="css/uikit.min.css" />
		<link rel="stylesheet" href="css/main.css" />
		<link rel="stylesheet" href="css/responsive.css" />
		<link rel="stylesheet" href="css/theme.css" />
		<link rel="stylesheet" href="css/efnav.css" />
	</head>

	<body>
		<header>
			<button id="nav-toggle" aria-label="Toggle navigation bar" aria-expanded="false" tabindex="0">
				<img src="img/menu-icon.svg" alt="menu icon" />
			</button>

			<nav>
				<div id="ef-nav-home"><a href="home"></a></div>
				<div id="ef-nav-menu"><?= $web->get_menu() ?></div>
			</nav>
		</header>

		<main <?php ($web->page->key === 'home'? ' class="ef-landingpage"' : '') ?>>
			<div id="content">
				<?= $web->page->content ?>
			</div>
		</main>

		<footer>
			<h2 id="ef-footer-title">
				Eurofurence <?= $web->config->convention->number ?></br />
				<span class="uk-text-meta uk-text-italic"><?= $web->config->convention->theme ?></span>
			</h2>
			<div class="uk-child-width-1-3@l" uk-grid>
				<div>
					<div class="uk-margin-medium-bottom">
						<?= $web->config->convention->location ?><br />
						<?= $web->config->convention->dates ?>
					</div>
					<div class="uk-button-group uk-width-1-1 uk-margin-small-bottom">					
						<a href="home" class="uk-icon-button uk-icon" uk-tooltip="pos:top" title="Homepage" uk-icon="home"></a>
						<a target="_blank" href="https://t.me/s/efnotifications" class="ef-hide-ext uk-icon-button uk-icon" uk-tooltip="pos:top" title="Telegram" uk-icon="telegram"></a>
						<a target="_blank" href="https://meow.social/@eurofurence" class="ef-hide-ext uk-icon-button uk-icon" uk-tooltip="pos:top" title="Mastodon" uk-icon="mastodon" rel="me"></a>
						<a target="_blank" href="https://bsky.app/profile/eurofurence.org" class="ef-hide-ext uk-icon-button uk-icon" uk-tooltip="pos:top" title="Bluesky" uk-icon="bluesky"></a>
						<a target="_blank" href="https://vimeo.com/eurofurence" class="ef-hide-ext uk-icon-button uk-icon" uk-tooltip="pos:top" title="Vimeo" uk-icon="vimeo"></a>
						<a target="_blank" href="https://discord.com/invite/VMESBMM" class="ef-hide-ext uk-icon-button uk-icon" uk-tooltip="pos:top" title="Discord" uk-icon="discord"></a>
					</div>
					<div>
						<a href="https://itunes.apple.com/us/app/eurofurence-convention/id1112547322" target="_blank" class="ef-hide-ext ef-app-badge"><img src="img/apple-appstore.svg" alt="iOS App" class=" uk-margin-small-bottom" /></a>
						<a href="https://play.google.com/store/apps/details?id=org.eurofurence.connavigator" target="_blank" class="ef-hide-ext ef-app-badge"><img src="img/google-playstore.png" alt="Android App" class=" uk-margin-small-bottom" /></a>
					</div>
				</div>

				<div>
					<h3>Convention Network</h3>
					<div id="links">
						<div uk-slideshow="autoplay: true; autoplay-interval: 3000; animation: pull; ratio: 5:2">
							<div class="uk-slideshow-items js-disabled" id="partners">
								<div>JavaScript required to view links to other conventions.</div>
							</div>
						</div>
					</div>
				</div>

				<div>
					<h3 class="uk-margin-remove-bottom">Rate this Page</h3>
					<div class="page-rating-stars">
						<button class="ef-page-rating 1" uk-toggle="target: #page-rating" data-rating="1">★</button>
						<button class="ef-page-rating 2" uk-toggle="target: #page-rating" data-rating="2">★</button>
						<button class="ef-page-rating 3" uk-toggle="target: #page-rating" data-rating="3">★</button>
						<button class="ef-page-rating 4" uk-toggle="target: #page-rating" data-rating="4">★</button>
						<button class="ef-page-rating 5" uk-toggle="target: #page-rating" data-rating="5">★</button>
					</div>
					<h3>Help &amp; Legal</h3>
					
					<ul class="uk-list">
						<li><a href="https://help.eurofurence.org/contact" target="_blank"><span uk-icon="icon:mail" class="ef-uk-icon-lift"></span>Contact Us</a></li>
						<!-- <li><a href="https://help.eurofurence.org/faq" target="_blank"><span uk-icon="icon:question" class="ef-uk-icon-lift"></span>Frequently Asked Questions (FAQ)</a></li> -->
						<!-- <li><a href="https://help.eurofurence.org/legal/imprint" target="_blank"><span uk-icon="icon:bookmark" class="ef-uk-icon-lift"></span>Imprint &amp; Legal Notice</a></li> -->
						<li><a href="https://help.eurofurence.org/legal/privacy" target="_blank"><span uk-icon="icon:bookmark" class="ef-uk-icon-lift"></span>Legal &amp; Privacy Statement</a></li>
						<li><a href="website"><span uk-icon="icon:heart" class="ef-uk-icon-lift"></span>Site Attributions</a></li>
					</ul>
				</div>
			</div>
		</footer>

		<!-- page rating modal dialog -->
		<div id="page-rating" uk-modal>
			<div class="uk-modal-dialog uk-modal-body">
				<form action="" method="POST">
					<h2 class="uk-modal-title">Rate This Page</h2>
					<button class="uk-modal-close-default" type="button" uk-close></button>
					<p>
						You are rating <span class="uk-text-bold"><?= $web->page->title ?></span>.<br />
						Your input will not be published, but manually reviewed and passed on to the responsible department within Eurofurence.<br />
						Please <strong>do not include any links</strong> in your comment.<br />
						If you are affiliated with this department, please be fair and abstain.
					</p>

					<div class="uk-margin-bottom">
						Your Rating:
						<div class="page-rating-stars">
							<button type="button" class="ef-page-rating 1" data-rating="1">★</button>
							<button type="button" class="ef-page-rating 2" data-rating="2">★</button>
							<button type="button" class="ef-page-rating 3" data-rating="3">★</button>
							<button type="button" class="ef-page-rating 4" data-rating="4">★</button>
							<button type="button" class="ef-page-rating 5" data-rating="5">★</button>
						</div>
					</div>

					<input type="text" name="page" value="<?= $web->page->key ?>" hidden />
					<input type="number" min="1" max="5" name="rating" id="rating-rating" hidden /> 

					<div class="uk-margin-bottom">
						<label for="rating-name" class="uk-margin-bottom">
							Your Name (optional):
							<input type="text" id="rating-name" name="name" maxlength="255" placeholder="Anonymous" class="uk-input" />
						</label>
					</div>

					<div class="uk-margin-bottom">
						<label for="rating-comment" class="uk-margin-bottom">
							Your Comment (optional):
							<textarea id="rating-comment" name="comment" placeholder="No Comment" class="uk-textarea"></textarea>
						</label>
					</div>

					<?php 
					if ($web->config->ratings->enabled)
						echo '<button type="submit" class="uk-button uk-button-primary">Submit</button>';
					else
						echo '<button class="uk-button uk-text-muted uk-text-strikethrough" disabled uk-tooltip="This website is archived!">Submit</button>'
					?>
				</form>
			</div>
		</div>

		<!-- EFnav modal dialog -->
		<div id="efnav-modal" class="uk-modal uk-modal-full efnav-modal" uk-modal>
			<div class="uk-modal-dialog uk-modal-body uk-flex uk-flex-column efnav-modal-dialog">
				<button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
				<div class="efnav-modal-content uk-flex uk-flex-column uk-flex-1">
					<h2 class="uk-modal-title efnav-modal-title"></h2>
					<p class="uk-text-meta efnav-modal-subtitle"></p>
					<div class="efnav-modal-tabs-wrap" hidden>
						<ul class="uk-tab uk-tab-small efnav-modal-tabs"></ul>
					</div>
					<div class="efnav-modal-map">
						<iframe class="efnav-modal-iframe" title="EFnav Map" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>
					</div>
				</div>
			</div>
		</div>

		<script src="js/uikit.min.js"></script>
		<script src="js/uikit-icons.min.js"></script>
		<script src="js/efnav.js"></script>
		<script src="js/partners.js"></script>
		<script src="js/main.js"></script>

		<?php /* Page Rating Submit Handling */
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			if (
				!empty($_POST['rating']) && 
				!str_contains(htmlspecialchars($_POST['comment']), 'http') &&
				intval(htmlspecialchars($_POST['rating'])) > 0 && intval(htmlspecialchars($_POST['rating'])) < 6 &&
				property_exists($web->config->pages, htmlspecialchars($_POST['page']))) {
				if (Telegram::report(sprintf("Page Rating Receipt\nPage: %s\nRating: %s / 5\nName: %s\nComment: %s",
					htmlspecialchars($_POST['page']),
					htmlspecialchars($_POST['rating']),
					htmlspecialchars($_POST['name']),
					htmlspecialchars($_POST['comment'])
				))) {
					header("Location: " . $_SERVER['REQUEST_URI'] . '#rate-success');
				}
				else {
					header("Location: " . $_SERVER['REQUEST_URI'] . '#rate-failure');
				}
			}
		}
		?>
	</body>

	<script defer>
		// Accessible navbar toggle mechanism
		const navButton = document.querySelector("#nav-toggle")

		navButton.addEventListener("click", e => {
			let navExpanded = navButton.getAttribute("aria-expanded")

			if(navExpanded === "false") {
				navButton.setAttribute("aria-expanded", "true")
				document.querySelector("#nav-toggle ~ nav").style.maxWidth = "100vw"
				return
			}

			navButton.setAttribute("aria-expanded", "false")
			document.querySelector("#nav-toggle ~ nav").style.maxWidth = "0"
			return
		})
	</script>
</html>
<?php $web->end(); ?>
