<?php
    $deadline = 'August 5th, 2026';
    $creators = [
        [
            'title' => 'BBF TV',
            'image' => 'bbftv.jpg',
            'links' => [
                [
                    'name' => 'Youtube',
                    'icon' => 'youtube',
                    'link' => 'https://www.youtube.com/@BBFTV'
                ]
            ]
        ],
		[
            'title' => 'xfireFX -Josh',
            'image' => 'xfirefx_josh.jpg',
            'links' => [
                [
                    'name' => 'Youtube',
                    'icon' => 'youtube',
                    'link' => 'https://www.youtube.com/@xfirefx_josh'
                ]
            ]
        ],
		[
            'title' => 'Tai Enigma',
            'image' => 'Tai_Enigma.jpg',
            'links' => [
                [
                    'name' => 'Youtube',
                    'icon' => 'youtube',
                    'link' => 'https://www.youtube.com/@Tai_Enigma'
                ]
            ]
        ],
		[
            'title' => 'Contigo',
            'image' => 'ContigoVR.jpg',
            'links' => [
                [
                    'name' => 'Youtube',
                    'icon' => 'youtube',
                    'link' => 'https://www.youtube.com/@ContigoVR'
                ]
            ]
        ],
		[
            'title' => 'Kana Mau',
            'image' => 'KanaMau.jpg',
            'links' => [
                [
                    'name' => 'Youtube',
                    'icon' => 'youtube',
                    'link' => 'https://www.youtube.com/@KanaMau'
                ]
            ]
        ],
		[
            'title' => 'KodaWolf_IRL Chaos',
            'image' => 'KodaWolf_IRL.jpg',
            'links' => [
                [
                    'name' => 'Youtube',
                    'icon' => 'youtube',
                    'link' => 'https://www.youtube.com/@KodaWolf_IRL'
                ]
            ]
        ],
        [
            'title' => 'SojaSoosse',
            'image' => 'sojasoosse.jpg',
            'links' => [
                [
                    'name' => 'Twitch',
                    'icon' => 'twitch',
                    'link' => 'https://www.twitch.tv/sojasoosse'
                ]
            ]
        ],
		[
            'title' => 'JankoGoo',
            'image' => 'JankoGoo.jpg',
            'links' => [
                [
                    'name' => 'Twitch',
                    'icon' => 'twitch',
                    'link' => 'https://www.twitch.tv/JankoGoo'
                ]
            ]
        ],
		[
            'title' => 'Orr-Tastic',
            'image' => 'OrrTastic.jpg',
            'links' => [
                [
                    'name' => 'Youtube',
                    'icon' => 'youtube',
                    'link' => 'https://www.youtube.com/@OrrTastic'
                ]
            ]
        ],
		[
            'title' => 'DRAGONIX FANTASY',
            'image' => 'dragonixfantasy.jpg',
            'links' => [
                [
                    'name' => 'Instagram',
                    'icon' => 'instagram',
                    'link' => 'https://www.instagram.com/dragonixfantasy/'
                ]
            ]
        ],
		[
            'title' => 'Hako',
            'image' => 'Hakogori.jpg',
            'links' => [
                [
                    'name' => 'TikTok',
                    'icon' => 'tiktok',
                    'link' => 'https://www.tiktok.com/@Hakogori'
                ]
            ]
        ],
		[
            'title' => 'Alice',
            'image' => 'fenalice.jpg',
            'links' => [
                [
                    'name' => 'TikTok',
                    'icon' => 'tiktok',
                    'link' => 'https://www.tiktok.com/@fenalice_'
                ]
            ]
        ],
		[
            'title' => 'Leok 64',
            'image' => 'leok_64.jpg',
            'links' => [
                [
                    'name' => 'Youtube',
                    'icon' => 'youtube',
                    'link' => 'https://www.youtube.com/@leok_64'
                ]
            ]
        ],
		[
            'title' => 'Eugen',
            'image' => 'Eugen.jpg',
            'links' => [
                [
                    'name' => 'Twitch',
                    'icon' => 'twitch',
                    'link' => 'https://www.twitch.tv/eugen45_'
                ]
            ]
        ],
		[
            'title' => 'Wispaw',
            'image' => 'Wispaw.jpg',
            'links' => [
                [
                    'name' => 'Twitch',
                    'icon' => 'twitch',
                    'link' => 'https://www.twitch.tv/wispaw_'
                ]
            ]
        ],
		[
            'title' => 'Akira.Floof05',
            'image' => 'Akira_Floof.jpg',
            'links' => [
                [
                    'name' => 'Youtube',
                    'icon' => 'youtube',
                    'link' => 'https://www.youtube.com/@akira.floof05'
                ]
            ]
        ],
		[
            'title' => 'Hoshino',
            'image' => 'tschipaw.jpg',
            'links' => [
                [
                    'name' => 'Instagram',
                    'icon' => 'instagram',
                    'link' => 'https://www.instagram.com/tschipaw'
                ]
            ]
        ],
		[
            'title' => 'SLASH',
            'image' => 'slashfurr.jpg',
            'links' => [
                [
                    'name' => 'Instagram',
                    'icon' => 'instagram',
                    'link' => 'https://www.instagram.com/slashfurr'
                ]
            ]
        ],
		[
            'title' => 'StardustGamez',
            'image' => 'StardustGamez.jpg',
            'links' => [
                [
                    'name' => 'Twitch',
                    'icon' => 'twitch',
                    'link' => 'https://www.twitch.tv/stardustgamez'
                ]
            ]
        ],
		[
            'title' => 'DevollyChan',
            'image' => 'DevollyChan.jpg',
            'links' => [
                [
                    'name' => 'Twitch',
                    'icon' => 'twitch',
                    'link' => 'https://www.twitch.tv/devollychan'
                ]
            ]
        ],
    ];

    usort($creators, function($a, $b) {
        return strcmp(strtolower($a['title']), strtolower($b['title']));
    });
?>

<section>
    <h1>Video Creators</h1>
    <p>Be an ambassador for Eurofurence!</p>
    <p>Do you make vlogs or streams for a furry audience? Do you have a sizeable community that loves to experience conventions through the screen? Are you planning to publish special video content at or about Eurofurence 30? Then the Video Creator Badge is for you.</p>
    <p>The Video Creator Badge is Eurofurence's seal of approval for video creators like streamers or vloggers. It is meant to recognize the dedication and hard work our creators invest in making Eurofurence special, and it also serves as a symbol of your commitment to ethical media creation.</p>
    <p>If you have an established online presence and are planning to post Eurofurence related content for your audience, you are invited to apply for the badge <a href=" https://help.eurofurence.org/contact/press-media-relations/creator" target="_blank">here</a>. Application deadline is <strong><?= $deadline ?></strong>.</p>
    <p>Please make sure to check our <a href="https://help.eurofurence.org/faq/view/108" target="_blank">FAQ</a> to see if the badge is right for you: </p>
    <p>Once checked and approved, your channel will be added to the list of official video creators below.</p>
    <p>Thank you for spreading the joy of the Fantastic Furry Festival!</p>
    <p>Officially approved video creators at Eurofurence 30 (this will be updated weekly):</p>
</section>

<section>
    <div class="ef-people uk-grid-match uk-child-width-1-3@l uk-child-width-1-2@m uk-grid-small" uk-grid>
        <?php foreach ($creators as $creator) { ?>
            <div>
                <div class="uk-padding-small">
                    <div>
                        <img src="img/pages/creators/<?= $creator['image'] ?>" alt="<?= $creator['title'] ?>" />
                        <h3><?= $creator['title'] ?></h3>
                    </div>
                    <?php foreach ($creator['links'] as $link) { ?>
                        <a href="<?= $link['link'] ?>" class="hide-ext" target="_blank"><span uk-icon="<?= $link['icon'] ?>"></span> <?= $link['name'] ?></a>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
</section>
