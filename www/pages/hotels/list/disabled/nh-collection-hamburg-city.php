<?php
    $frontmatter["title"] = "nh Collection Hamburg-City";
    $frontmatter["price"] = "239&ndash;256";
    $frontmatter["metaprice"] = "256";
    $frontmatter['distance'] = "1.4";
    $frontmatter['badge'] = "";
    $frontmatter["breakfast"] = true;
    $frontmatter["email"] = "nhcollectionhamburgcity@nh-hotels.com";
    $frontmatter["code"] = "";
?>

<h2><?= $frontmatter['title'] ?></h2>

<p>The <?= $frontmatter["title"] ?> offers <strong>Suites</strong> for <strong>👥 double</strong> or <strong>👤 single</strong> room accommodation.</p>

<p>💸 Price: 256€ (double room) or 239€ (single room) per night</p>
<p>🥞 breakfast included</p>

<h3>How to Book</h3>
<p>
    Make your reserveration via eMail to <strong><?= $frontmatter["email"] ?></strong>, mentioning the booking code "<strong><?= $frontmatter["code"] ?></strong>".<br />
    To ensure a smooth booking process, we have prepared tools for you to generate a uniform eMail:
    <ul>
        <li><a target="_blank" href="hotelui/en/reservation-form.html?single=1&triple=0&keyword=<?= $frontmatter["title"]?>&mail=<?= $frontmatter["email"] ?>&code=<?= $frontmatter["code"] ?>&category=Suite">🇬🇧 English</a></li>
        <li><a target="_blank" href="hotelui/de/reservation-form.html?single=1&triple=0&keyword=<?= $frontmatter["title"]?>&mail=<?= $frontmatter["email"] ?>&code=<?= $frontmatter["code"] ?>&category=Suite">🇩🇪 German</a></li>
    </ul>
</p>
