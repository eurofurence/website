<style>
    @import url("css/mastodon-timeline.min.css");
    #mt-container, #ef-flavor-text {
        height: 740px;
        overflow: scroll;
    }
    .mt-container, .mt-container[data-theme="light"], .mt-dialog, .mt-dialog[data-theme="light"] {
        --mt-color-bg: rgba(255, 255, 255, .1);
        border-radius: 8px;
    }

    /* Changes for feed contrasts */
    time,
    .mt-post-counter-bar,
    .mt-post-counter-bar-replies {
        color: #fff;
    }
    .mt-post-counter-bar svg path {
        fill: #fff;
    }
    .mt-post-preview-title {
        color: #a8a8ffff;
    }
    .mt-post-preview-description {
        color: #7a7ae2ff;
    }
</style>

<div id="ef-home-banner">
    <div>
        <h1>Eurofurence <?= $this->current->number ?></h1>
        <p>
            <?= $this->current->theme ?> <br />
            <?= $this->current->dates ?> <br />
            <?= $this->current->location ?>
        </p>
    </div>
</div>

<div id="ef-home-countdown" class="ef-background uk-margin uk-text-center">
    <h2>Starting in</h2>
    <div class="uk-grid-small uk-child-width-auto uk-flex-center uk-flex-middle" uk-grid uk-countdown="date: <?= $this->config->convention->opening ?>">
        <div>
            <div class="uk-countdown-number uk-countdown-days"></div>
            <div class="uk-countdown-label uk-margin-small uk-text-center">Days</div>
        </div>
        <div class="uk-countdown-separator">:</div>
        <div>
            <div class="uk-countdown-number uk-countdown-hours"></div>
            <div class="uk-countdown-label uk-margin-small uk-text-center">Hours</div>
        </div>
        <div class="uk-countdown-separator">:</div>
        <div>
            <div class="uk-countdown-number uk-countdown-minutes"></div>
            <div class="uk-countdown-label uk-margin-small uk-text-center">Minutes</div>
        </div>
        <div class="uk-countdown-separator">:</div>
        <div>
            <div class="uk-countdown-number uk-countdown-seconds"></div>
            <div class="uk-countdown-label uk-margin-small uk-text-center">Seconds</div>
        </div>
    </div>
</div>

<div id="ef-home-photo" class="uk-position-relative" tabindex="-1" uk-slideshow="ratio: 1280:400; autoplay: true; autoplay-interval: 5000">
    <div class="uk-slideshow-items">
        <?php 
        $dir = 'img/pages/home/photo/';
        foreach (scandir($dir) as $file) {
            if (in_array($file, ['.', '..']))
                continue;
        ?>
        <div><img src="<?= $dir . $file ?>" alt="<?= $file ?>" uk-cover /></div>
        <?php } ?>
    </div>

    <div id="ef-intro-text">
        <p>Eurofurence is a unique furry convention held in Hamburg every year at the CCH</p>
        <p>Unlike other cons we have a whole convention centre and over 100 hotels in the city to choose from</p>
        <p>this makes a con like no other, your friends will be there so what's stopping you?</p>
        
        <p class="uk-margin-remove-bottom">
            <a href="about" class="uk-button uk-button-primary" target="_blank">LEARN MORE</a>
            <a href="https://identity.eurofurence.org/" class="uk-button uk-button-primary" target="_blank">REGISTER NOW</a>
        </p>
    </div>
</div>

<div class="uk-grid-match uk-child-width-1-2@m uk-margin-top" uk-grid>
    <div>
        <div id="mt-container" class="mt-container">
            <div class="mt-body" role="feed" tabindex="0">
                <div class="mt-loading-spinner"></div>
            </div>
        </div>
    </div>
    <div id="ef-flavor-text" class="uk-padding uk-padding-remove-vertical">
        <p class="uk-margin-remove-bottom">The desert stretches endlessly before you. Rising from the dust and dunes, like a mirage made real, stands the <span class="uk-text-italic">Fantastic Furry Festival</span>. You can faintly hear the pulse of drums, see the glimmer of light refracting off wild shapes and colors, and taste the air heavy with smoke, spice, and promise.</p>
        <p class="uk-margin-remove-bottom">At the gate, a volunteer with glowing fur and amber eyes hands you a badge with your name on it. “Welcome.” they say.</p>
        <p class="uk-margin-remove-bottom">Stepping forward, the world explodes into color. Glittering domes, patchwork tents, glowing towers built from scrap and dreams. Towering art installations shimmer: beasts of metal and fire that move with the wind, sculptures that breathe smoke, holograms whispering riddles as you pass.</p>
        <p class="uk-margin-remove-bottom">Wings trailing sparks, LED-furred coats, masks of bone and glass, scales glittering under neon lights. Everywhere you turn, beings in impossible costumes laugh, dance, and embrace.</p>
        <p class="uk-margin-remove-bottom">At the festival’s heart, the fire circle beats with life. Drummers pound rhythms into the night, bodies twist and leap around the flames. Beyond, games spring up in chaotic arenas. Contests of wit, dance, and sheer absurdity. Laughter and music carry far across the desert air.</p>
        <p>Food stalls spill scents of spice and sweetness, the kind you can’t quite name but can’t resist. Conversations flow like rivers between strangers who already feel like family. The deeper you wander, the more the noise and lights fold around you, pulling you into their orbit.</p>
    </div>
</div>

<script src="js/mastodon-timeline/mastodon-timeline.umd.js"></script>

<script>
    new MastodonTimeline.Init({
        instanceUrl: "https://meow.social",
        timelineType: "profile",
        userId: "112331996724958954",
        profileName: "@eurofurence",
        dateLocale: "de-DE",
        hideUnlisted: true,
        hideReplies: true,
        hideUserAccount: true,
        hidePinnedPosts: true,
        defaultTheme: "dark"
    });
</script>