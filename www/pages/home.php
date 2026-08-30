<?php $page = [
    "owner"       => "@draconigen",
    "editor"      => "@draconigen",
    "title"       => "Home",
    "description" => "Eurofurence {number} will take place from {dates}. Eurofurence is Europe's largest and longest-running annual gathering, celebrating the furry community across the continent.",
    "ogpImage"    => "",
    "keywords"    => "",
    "robots"      => ""
]; ?>

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
.ef-photos-credits {
    color: rgba(255,255,255,.7);
    background: rgba(34,34,34,.75);
    padding: 4px 10px;
    font-size: .8rem;
    border-radius: var(--ef-border-radius);
}
</style>

<div id="ef-home-banner">
    <div>
        <h1>Eurofurence <?= $this->config->convention->number ?></h1>
        <!-- <h1>Eurofurence <img src="img/pages/home/ef31logo_50px.png" alt="31" /></h1> -->
        <p class="ef-text-large-m">
            <?= $this->config->convention->theme ?> <br />
            <?= $this->config->convention->dates ?> <br />
            <?= $this->config->convention->location ?>
        </p>
    </div>
</div>

<div id="ef-home-countdown" class="ef-background uk-margin uk-text-center">
<!--    
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
-->
</div>

<div class="uk-position-relative">
    <div id="ef-home-intro-text" class="uk-margin">
        <p>Eurofurence is a yearly, international furry convention held in Hamburg, Germany.</p>
        <p>We take over an entire convention center, with 100 hotels across Hamburg to choose from.</p>
        <p>Your friends will be here and so should you!</p>
        
        <div class="uk-grid-small" uk-grid>
            <div><a href="https://identity.eurofurence.org/" class="uk-button hide-ext uk-button-secondary" target="_blank" uk-tooltip="Registration begins early 2027!">LOGIN</a></div>
            <!-- <div><a href="https://identity.eurofurence.org/" class="uk-button hide-ext uk-button-primary" target="_blank">REGISTER NOW</a></div> -->
            <!-- <div><a href="about" class="uk-button uk-button-primary" target="_blank">LEARN MORE</a></div> -->
        </div>
    </div>
    <div id="ef-home-photos" tabindex="-1" uk-slideshow="ratio: 1280:400; autoplay: true; autoplay-interval: 5000">
        <div class="uk-slideshow-items">
            <?php foreach (get_photos('img/pages/home/photos/') as $photo) { ?>
            <div>
                <img src="<?= $photo['path'] ?>" alt="<?= $photo['cred'] ?>" uk-cover />
                <div class="uk-position-bottom-left uk-position-small uk-overlay ef-photos-credits"><?= $photo['cred'] ?></div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<!-- <div id="ef-home-goh" class="ef-background uk-margin uk-text-center uk-padding">
    <h2>Guest of Honor: <?= $this->config->convention->goh ?></h2>
</div> -->

<!-- <div id="ef-home-charity" class="ef-background uk-margin uk-text-center uk-padding">
    <h2>Charity: <?= $this->config->convention->charity ?></h2>
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
            <p class="uk-margin-remove-bottom">You push open the doors of the Lab. Everything is eerily quiet, save for the hum and whirring of machines left running late into the night. Workbenches lie covered in half-finished experiments, scattered notes, and strange devices whose purposes are probably better left unknown. Empty mugs sit beside hastily scribbled calculations, while cables snake across the floor and disappear beneath desks and behind heavy machinery.</p>
            <p class="uk-margin-remove-bottom">You've never been in the Lab this late, and somehow, sneaking around at this time feels weird... different.</p>
            <p class="uk-margin-remove-bottom">As you step into the room, all you can hear are the machines and the sounds of your own body. Then, from a barely used corner of the Lab, you hear a sound.</p>
            <p class="uk-margin-remove-bottom">Curiosity gets the better of you as you squeeze behind the shelves of forgotten experiments, catching a glimpse of a creature moving between workbenches hidden behind.</p>
            <p class="uk-margin-remove-bottom">Black and white, dimly lit by the glow of a forgotten monitor, before disappearing behind a corner. You follow, finding them waiting for you further down the old Lab, seemingly unsurprised by your presence. The feline gives you a knowing look before gesturing toward a dimly lit passage behind them. Without a word, they turn and head inside, occasionally glancing back to make sure you're still following.</p>
            <p>The further you follow, the less empty the Lab begins to feel. The hum of machinery is joined by muffled voices, then laughter, and, unless your ears are deceiving you, music. A faint glow spills into the passage ahead as your mysterious guide rounds one final corner and disappears from view. Whatever is happening down here, it seems you were meant to find it. You continue toward the light, leaving the quiet Lab behind as tonight's experiment begins.</p>
        </div>
    </div>
</div>

<?php
function get_photos($dir) {
    $ret = [];

    foreach (scandir($dir) as $file) {
        if (in_array($file, ['.', '..', '.DS_Store'])) {
            continue;
        }

        // rm ext
        $name = pathinfo($file, PATHINFO_FILENAME);

        // split photographer & fursuiters
        $parts = explode('_feat_', $name, 2);

        // get photographer
        $photographer = preg_replace('/^\d{4,}_by_/', '', $parts[0]);

        // get fursuiters, if present
        $featuring = [];

        if (isset($parts[1])) {
            $featuring = explode('_', $parts[1]);
        }

        $credits = 'Photo: ' . $photographer;

        if (!empty($featuring)) {
            $credits .= '; Featuring: ' . implode(', ', $featuring);
        }

        $ret[] = [
            'file' => $file,
            'path' => $dir . $file,
            'cred' => $credits
        ];
    }

    return $ret;
}