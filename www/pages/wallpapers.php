<h2>Wallpapers</h2>

<?php
    $landscape = [
        ['img' =>  'ef30_wallpaper_1_pc.png', 'cap' => 'Art by x'],
    ];

    $mobile = [
        ['img' =>  'ef30_wallpaper_1_mobile.png', 'cap' => 'Art by x'],
        ['img' =>  'ef30_wallpaper_6_mobile.png', 'cap' => 'Art by x'],
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