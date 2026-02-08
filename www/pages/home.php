<style>
@media (min-width: 640px) {
    .ef-text-large-m {
        font-size: 1.5rem;
        line-height: 1.5;
    }
}
.consent-cover {
    color: #fff;
    font-weight: normal;
}
iframe {
    background-color: #181821;
    border-radius: var(--ef-border-radius);
}
</style>

<div id="ef-home-banner">
    <div>
        <!-- <h1>Eurofurence <?= $this->current->number ?></h1> -->
        <h1>Eurofurence <img src="img/pages/home/ef30logo_50px.png" alt="30" /></h1>
        <p class="ef-text-large-m">
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

<div class="uk-position-relative">
    <div id="ef-home-intro-text" class="uk-margin">
        <p>EUROFURENCE IS A YEARLY, INTERNATIONAL FURRY CONVENTION HELD IN HAMBURG, GERMANY.</p>
        <p>WE TAKE OVER AN ENTIRE CONVENTION CENTER, WITH OVER 100 HOTELS ACROSS HAMBURG TO CHOOSE FROM.</p>
        <p>YOUR FRIENDS WILL BE HERE AND SO SHOULD YOU!</p>
        
        <div class="uk-grid-small" uk-grid>
            <!-- <div><a href="https://identity.eurofurence.org/" class="uk-button hide-ext uk-button-secondary uk-disabled" target="_blank">REGISTRATION OPENS FEB 8</a></div> -->
            <div><a href="https://identity.eurofurence.org/" class="uk-button hide-ext uk-button-primary" target="_blank">REGISTER NOW</a></div>
            <!-- <div><a href="about" class="uk-button uk-button-primary" target="_blank">LEARN MORE</a></div> -->
        </div>
    </div>
    <div id="ef-home-photos" tabindex="-1" uk-slideshow="ratio: 1280:400; autoplay: true; autoplay-interval: 5000">
        <div class="uk-slideshow-items">
            <?php 
            $dir = 'img/pages/home/photos/';
            foreach (scandir($dir) as $file) {
                if (in_array($file, ['.', '..']))
                    continue;
            ?>
            <div><img src="<?= $dir . $file ?>" alt="<?= $file ?>" uk-cover /></div>
            <?php } ?>
        </div>
    </div>
</div>

<!-- <div id="ef-home-goh" class="ef-background uk-margin uk-text-center uk-padding">
    <h2>Guest of Honor: <?= $this->current->goh ?></h2>
</div> -->

<!-- <div id="ef-home-charity" class="ef-background uk-margin uk-text-center uk-padding">
    <h2>Charity: <?= $this->current->charity ?></h2>
</div> -->

<div class="uk-grid-match uk-grid-small uk-child-width-1-2@m uk-margin-top" uk-grid>
    <div>
        <div
            class="consent-cover uk-width-1-1"
            data-element-type="iframe"
            data-src="pages/home/mastodon-timeline.html"
            data-class="uk-width-1-1"
            data-title="Eurofurence Hotels"
            data-uk-height-viewport="offset-bottom: 120px"
        ><h3>External Contents</h3><p>- click to accept -</p><p>subject to meow.social/privacy-policy</p></div>
    </div>
    <div>
        <div id="ef-home-flavor-text" class="uk-padding">
            <p class="uk-margin-remove-bottom">The desert stretches endlessly before you. Rising from the dust and dunes, like a mirage made real, stands the <span class="uk-text-italic">Fantastic Furry Festival</span>. You can faintly hear the pulse of drums, see the glimmer of light refracting off wild shapes and colors, and taste the air heavy with smoke, spice, and promise.</p>
            <p class="uk-margin-remove-bottom">At the gate, a volunteer with glowing fur and amber eyes hands you a badge with your name on it. “Welcome.” they say.</p>
            <p class="uk-margin-remove-bottom">Stepping forward, the world explodes into color. Glittering domes, patchwork tents, glowing towers built from scrap and dreams. Towering art installations shimmer: beasts of metal and fire that move with the wind, sculptures that breathe smoke, holograms whispering riddles as you pass.</p>
            <p class="uk-margin-remove-bottom">Wings trailing sparks, LED-furred coats, masks of bone and glass, scales glittering under neon lights. Everywhere you turn, beings in impossible costumes laugh, dance, and embrace.</p>
            <p class="uk-margin-remove-bottom">At the festival’s heart, the fire circle beats with life. Drummers pound rhythms into the night, bodies twist and leap around the flames. Beyond, games spring up in chaotic arenas. Contests of wit, dance, and sheer absurdity. Laughter and music carry far across the desert air.</p>
            <p>Food stalls spill scents of spice and sweetness, the kind you can’t quite name but can’t resist. Conversations flow like rivers between strangers who already feel like family. The deeper you wander, the more the noise and lights fold around you, pulling you into their orbit.</p>
        </div>
    </div>
</div>
