<?php

$lineup = [
    [
        'name' => 'Patchmonster (Live)',
        'time' => 'Wednesday, 3 Sept. 19:00',
        'img'  => 'default.svg',
        'txt'  => 'Trance-inspired sound performed live on keyboards! From cute piano lofi tracks to high-energy eurodance, Patchmonster knows how to make a crowd have a good time!'
    ],
    [
        'name' => 'Theron’s Psychill Journey (DJ)',
        'time' => 'Thursday, 4 Sept. 18:00',
        'img'  => 'Theron.jpg',
        'txt'  => 'Experienced Trance DJ Theron will take us on a trip through his Psychill universe.'
    ],
    [
        'name' => 'Quinnexe (Live)',
        'time' => 'Thursday, 4 Sept. 19:30',
        'img'  => 'quinn.jpg',
        'txt'  => 'Quinnexe is a trans fem solo artist. She plays Alternative Rock/Electronic inspired music and usually performs in VR. This will be her first live gig at a RL furry convention!'
    ],
    [
        'name' => 'Lighthase’s Bnuy Lounge (DJ)',
        'time' => 'Friday, 5 Sept. 15:00',
        'img'  => 'lighthase.png',
        'txt'  => 'Lighthase will treat our ears to wonderful, relaxing downtempo music.'
    ],
    [
        'name' => 'Words Unleashed (Open Mic)',
        'time' => 'Saturday, 6 Sept 13:00',
        'img'  => 'default.svg',
        'txt'  => 'Whether it’s slam poetry, stand-up comedy, storytelling, or just a powerful thought – this is your time on stage. No backing tracks, no musical setup – just you, a microphone, and the audience. Your voice matters. Spontaneous acts welcome – or sign up in advance to secure your slot at <a href="https://t.me/+MoOWTASoOn5hZDk0" target="_blank">https://t.me/+MoOWTASoOn5hZDk0</a>'
    ],
    [
        'name' => 'Burstup - Deep Paws (Live)',
        'time' => 'Saturday, 6 Sept 15:00',
        'img'  => 'burstep.gif',
        'txt'  => 'Deep Paws is a one-hour live Dub Techno experience, performed entirely on a single electronic instrument! Expect hypnotic rhythms, spacious delays, and deep, fuzzy grooves designed to ground you in the moment while your mind floats freely. Whether you’re lounging with friends, swaying to the beat, or just letting the vibes wash over you, this set invites you to sink into the groove and let the world melt away. Relax. Unwind. Dance. Let the pulse of Deep Paws carry you home.'
    ],
]; ?>

<h1>Open Stage Lineup</h1>
<strong>CCH Foyer Y</strong>
<p>Join us at the Open Stage, where musicians and bands can spontaneously jam and perform, creating a dynamic and ever-evolving soundscape. Join the <a href="https://t.me/+zS98iO-gAGpiYWNk" target="_blank">Telegram channel</a> to find slots and others to play with. In additon to the open track, the stage also features a showcase track of bands and solo artists, two Fur-E-Oke nights, the Open Mic event Words Unleashed, and a track of electronic music DJs, showcasing the best in synth-driven chillout sounds, lo-fi beats and psychill. The relaxed, loungey atmosphere of the Open Stage is the purrfect place to hang out, connect with friends, and make new memories. </p>

<h2>Curated Lineup:</h2>

<div class="uk-grid-match uk-child-width-1-2@l" uk-grid>
    <?php foreach ($lineup as $i) { ?>
    <div>
        <div class="uk-card uk-card-body uk-card-small uk-card-default">
            <article class="uk-comment" role="article">
                <div class="uk-comment-header">
                    <div class="uk-grid-medium uk-flex-middle" uk-grid>
                        <div class="uk-width-auto">
                            <img class="uk-comment-avatar" src="img/pages/openstage/<?= $i['img']?>" width="100" height="100" alt="">
                        </div>
                        <div class="uk-width-expand">
                            <h4 class="uk-comment-title uk-margin-remove"><?= $i['name']?></h4>
                            <ul class="uk-comment-meta uk-subnav uk-subnav-divider uk-margin-remove-top">
                                <li><?= $i['time']?></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="uk-comment-body">
                    <p><?= $i['txt']?></p>
                </div>
            </article>
        </div>
    </div>
    <?php } ?>
</div>