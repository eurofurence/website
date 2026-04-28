<?php
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
        ]
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
    <p>If you have an established online presence and are planning to post Eurofurence related content for your audience, you are invited to apply for the badge <a href=" https://help.eurofurence.org/contact/press-media-relations/creator" target="_blank">here</a>.</p>
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
