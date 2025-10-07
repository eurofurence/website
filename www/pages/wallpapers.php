<h2>Wallpapers</h2>

<?php
    $landscape = [
        ['img' =>  'ef29_wallpaper_1_pc.png', 'cap' => 'Art by @Tel_McGrew on Telegram'],
        ['img' =>  'ef29_wallpaper_2_pc.png', 'cap' => 'Art by https://raironu.art/'],
        ['img' =>  'ef29_wallpaper_3_pc.png', 'cap' => 'Art by @mona_thehoofeddragon on Telegram'],
        ['img' => 'ef29_wallpaper_4a_pc.png', 'cap' => 'Art by @mona_thehoofeddragon on Telegram'],
        ['img' => 'ef29_wallpaper_4b_pc.png', 'cap' => 'Art by @mona_thehoofeddragon on Telegram'],
        ['img' => 'ef29_wallpaper_5a_pc.png', 'cap' => 'Art by @mona_thehoofeddragon on Telegram'],
        ['img' => 'ef29_wallpaper_5b_pc.png', 'cap' => 'Art by @mona_thehoofeddragon on Telegram'],
        ['img' =>  'ef29_wallpaper_6_pc.png', 'cap' => 'Art by @Tel_McGrew on Telegram'],
        ['img' =>  'ef29_wallpaper_7_pc.png', 'cap' => 'Art by https://raironu.art/'],
        ['img' =>  'ef29_wallpaper_8_pc.png', 'cap' => 'Art by https://raironu.art/'],
        ['img' =>  'ef29_wallpaper_9_pc.png', 'cap' => 'Art by https://raironu.art/'],
        ['img' => 'ef29_wallpaper_10_pc.png', 'cap' => 'Art by https://raironu.art/'],
        ['img' => 'ef29_wallpaper_11_pc.png', 'cap' => 'Art by https://raironu.art/'],
        ['img' => 'ef29_wallpaper_12_pc.png', 'cap' => 'Art by https://raironu.art/'],
    ];

    $mobile = [
        ['img' =>  'ef29_wallpaper_1_mobile.png', 'cap' => 'Art by @Tel_McGrew on Telegram'],
        ['img' =>  'ef29_wallpaper_6_mobile.png', 'cap' => 'Art by @Tel_McGrew on Telegram'],
    ]
?>

<section>
    <h3>Desktop</h3>
    <div uk-grid uk-lightbox class="uk-child-width-1-2@m">
        <?php foreach ($landscape as $e) { ?>
            <div uk-scrollspy="cls:uk-animation-fade">
                <a href="img/pages/wallpapers/<?= $e['img'] ?>" data-caption="<?= $e['cap'] ?>"><img src="img/pages/wallpapers/thumbs/<?= $e['img'] ?>" alt="<?= $e['cap'] ?>" /></a>
            </div>
        <?php } ?>
    </div>

    <h3>Mobile</h3>
    <div uk-grid uk-lightbox class="uk-child-width-1-4@m uk-child-width-1-2@s uk-child-width-1-1">
        <?php foreach ($mobile as $e) { ?>
            <div uk-scrollspy="cls:uk-animation-fade">
                <a href="img/pages/wallpapers/<?= $e['img'] ?>" data-caption="<?= $e['cap'] ?>"><img src="img/pages/wallpapers/thumbs/<?= $e['img'] ?>" alt="<?= $e['cap'] ?>" /></a>
            </div>
        <?php } ?>
    </div>
</section>