<?php $sponsors = [
	[
		'name' => 'Flexoptix',
		'txt' => 'Network equipment',
		'url' => 'https://www.flexoptix.net/',
		'img' => 'flexoptix.jpg'
	],
	[
		'name' => 'IPHH',
		'txt' => 'Datacenter Services & Darkfibre Sponsoring',
		'url' => 'https://www.iphh.net/',
		'img' => 'iphh.png'
	],
	[
		'name' => 'Deutsche Telekom',
		'txt' => 'IP Connectivity and onsite support',
		'url' => 'https://www.telekom.de/start',
		'img' => 'favicon-512x512-png-data.png'
	],
	[
		'name' => 'fux eg',
		'txt' => 'IP Connectivity',
		'url' => 'https://www.fux-eg.org/',
		'img' => 'fuxeg.svg'
	],
	[
		'name' => 'Eventinfra',
		'txt' => 'Equipment Loan',
		'url' => 'https://eventinfra.org/',
		'img' => 'eventinfra.png'
	],
	[
		'name' => 'myLoc a WIIT Company',
		'txt' => 'IP Connectivity',
		'url' => 'https://www.myloc.de/',
		'img' => 'myloc_logo25.svg'
	],
	[
		'name' => 'OpenProject',
		'txt' => 'Project Management',
		'url' => 'https://www.openproject.org',
		'img' => 'openproject-logo-original-color-7266162e.png'
	]
]; 

usort($sponsors, function ($a, $b) {
    return strtolower($a['name']) <=> strtolower($b['name']);
}); ?>

<style>
	.sp_link {
		text-decoration: none;
		transition: box-shadow .25s;
	}
	.sp_link:hover {
		text-decoration: none;
		box-shadow: 0 5px 15px rgba(0,0,0,.2);
	}
	.sp_bg {
		height: 100px;
		background-size: contain;
		background-position: center;
		background-repeat: no-repeat;
	}
</style>

<section>
	<h1>Supporters</h1>

	<p>This is a list of organizations providing network hardware and connectivity. We couldn’t build the network without them – plus a few unlisted - and we thank them their support.</p>

	<div class="uk-child-width-1-3@m uk-grid-match" uk-grid>
		<?php foreach ($sponsors as $sponsor) {
			echo sprintf('
				<div>
					<a href="%s" target="_blank" class="hide-ext sp_link uk-card uk-card-default uk-card-small uk-card-body uk-text-center">
						<div class="sp_bg" style="background-image: url(img/pages/sponsors/%s)"></div>
						<p>%s</p>
					</a>
				</div>',
				$sponsor['url'],
				$sponsor['img'],
				$sponsor['txt'],
				$sponsor['name']
			);
		} ?>
	</div>
</section>
