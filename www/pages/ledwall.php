<?php $page = [
    "owner"       => "@gingerwolf",
    "editor"      => "@mondanzo",
    "title"       => "LED Wall",
    "description" => "Your art on the really big screen. Find out everything you need to know to submit your animations for the Video Wall above the entrance, as well as some examples from the last year. Deadline August 10.",
    "keywords"    => "Venue, Video, LED, Wall, Entrance, Big, Large, Screen, Animation",
    "ogpImage"    => "",
    "robots"      => ""
]; ?>

<?php
$deadline = 'August 2, 2026';
$examples = [
    '2025 Example by Calie' => 'CALIE-greeny_EF29_welcome_vulcan_salute.gif',
    '2025 Example by Kenny' => 'KENNY-V_02_LED_WALL_K_Venturry.gif',
    '2025 Example by Luke' => 'LUKE-Ef_Signage_2025_B.gif',
    '2025 Example by Sal' => 'SAL-efscreen-crf7.gif'
];
$formUrl = 'https://cloud.eurofurence.org/index.php/apps/forms/s/P2SG7WnzGizyP3e3GpnREQiF';
?>

<section>
    <h1>Video-Wall Submission</h1>
    <div class="uk-text-center"><strong>Submission Deadline: <?= $deadline ?></strong></div>

    <div style="max-width: 800px; margin: 1rem auto 0;">
        <ul uk-accordion="active: 0; collapsible: false">
            <?php foreach ($examples as $title => $file) { ?>
                <li>
                    <a class="uk-accordion-title" href="ledwall/#"><?= $title ?></a>
                    <div class="uk-accordion-content">
                        <img src="img/pages/ledwall/<?= $file ?>" alt="<?= $title ?>"<?= $title === array_key_first($examples) ? '' : ' loading="lazy"' ?> />
                    </div>
                </li>
            <?php } ?>
            <li>
                <a class="uk-accordion-title" href="ledwall/#">Real Life Result</a>
                <div class="uk-accordion-content">
                    <a href="img/pages/ledwall/IMG_0126.MOV" target="_blank"><span uk-icon="link-external"></span> Watch a video of the LED panel in action to get an impression of the final result</a>.<br />
                    File size warning: 21 MB
                </div>
            </li>
        </ul>
    </div>
</section>

<section>
    <!-- <p class="uk-text-bold">Story Blurb</p> -->

    <div class="uk-column-1-2@l">
        <p>A pack of wolves stands together, laughing. Dragons pose for photos. There are a few others,  foxes, but also deer, otters, cats, and creatures whose names I don't even know. Some wear colorful costumes and accessories. Others don't. Someone is performing on the Open Stage. A lizard walks past me carrying a stack of sketchbooks under their arm. Some birds dance to music I can already hear from the other side of the convention. I almost turn around and head home three different times before I even make it to the entrance.</p>

        <p>It's not that I feel lost. Well, not really.</p>

        <p>But I'm no longer sure whether I belong here at all.</p>

        <p>Everyone around me seems so... confident. They move through the sea of feathers, scales, and fur as if they've done this hundreds of times before. Old friends greet one another. People talk about art projects, stage performances, and plans for the entire week.</p>

        <p>And then there's me.</p>

        <p>I'm standing at the entrance, wondering whether coming here is a huge mistake. Everyone around me seems to know exactly what they want to do this week and which friends they're excited to meet.</p>

        <p>And I am still trying to figure out where I fit into all of this.</p>

        <p>A moose waves at me.</p>

        <p>Nothing spectacular. Just a simple gesture. Followed by a friendly, "Hey. Welcome to Eurofurence!"</p>

        <p>And yet, somehow, it changes everything.</p>

        <p>I step inside the large building and begin exploring the convention stretching out before me. I don't really have a destination. I still don't know what I'm going to do here.</p>

        <p>But every hallway promises a new adventure.</p>

        <p>Every room is filled with laughing furries discussing something exciting. Artists exchange ideas with one another. Music seems to come from everywhere at once. The entire place is alive with color, energy, and the feeling of finally being among friends.</p>

        <p>And nobody expects me to be anyone else.</p>

        <p>I'm simply... me.</p>

        <p>For the first time in a long while, I don't have to worry about myself fitting in. I know now that this place is full of friends I simply haven't met yet. And that’s definitely going to change!</p>

        <p>I'm already here. And soon you'll be here too!</p>

        <p>But first, I need your help.</p>

        <p>Eurofurence is made brighter by the people who fill it with their creativity. Their art. Their stories. Their imagination.</p>

        <p>Send us your animated creations and fill the outside video wall with everything that makes this community special.</p>

        <p>I can't wait to see what we'll create together and make this the most wonderful Furry Festival the world has ever seen.</p>

    </div>
</section>

<section>
    <h3>Eurofurence Video-Wall Submission Guidelines:</h3>
    <div class="uk-column-1-2@l">
        <p>📐 Format: 10:1 aspect ratio (3840 x 384 pixels)</p>
        <p>⏱️ Length: Maximum 20 seconds</p>
        <p>💾 File type: .mp4</p>
        <p>🔈 Audio: No audio playback available</p>
        <p>📝 Third party materials shall only be used if under License: CC-BY-SA 4.0</p>
        <p>
            🏆 Why participate?<br />
            Selected entries will be showcased in a spectacular ultra-wide format on a panoramic screen at the entrance of the CCH, Hamburg– giving your work the cinematic stage it deserves. It’s a chance to inspire, to awe, and to be part of a collective voyage into the unknown.
        </p>

        <p>🎯 Theme: Furry Festival</p>
        <p>
            ✨ Your video could feature:
            <ul>
                <li>The moment a newcomer discovers Eurofurence for the very first time</li>
                <li>An artist bringing a sketch, painting, or sculpture to life</li>
                <li>Musicians performing on stage</li>
                <li>What Eurofurence feels like to you</li>
            </ul>
        </p>
        <p>... or a surprise appearance by Greeny, joining the festival as an artist, performer, musician, dancer, or simply a friend enjoying the festival.</p>
    </div>

    <p class="uk-text-center">
        📅 Submission Deadline: <?= $deadline ?>
    </p>
    <p class="uk-text-center">
        <a href="<?= $formUrl ?>" target="_blank" class="uk-button uk-button-primary">Access the Form</a>
    </p>
</section>