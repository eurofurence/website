<?php
    $frontmatter["title"] = "Hotel Hafen Hamburg";
    $frontmatter["price"] = "220&ndash;250";
    $frontmatter["metaprice"] = "250";
    $frontmatter['distance'] = "2.6";
    $frontmatter['badge'] = "🚫 🥞";
    $frontmatter["breakfast"] = false;
    $frontmatter["email"] = "reservierung@hotel-hamburg.de";
    $frontmatter["code"] = "Eurofurence150924";
?>

<h2><?= $frontmatter['title'] ?></h2>

<p>The <?= $frontmatter["title"] ?> offers <strong>Standard</strong> rooms for <strong>👥 double</strong> or <strong>👤 single</strong> room accommodation.</p>

<p>💸 Price: 250€ (double room) or 220€ (single room) per night</p>
<p>ℹ️ Note: to take advantage of our deal, you must book at least two nights</p>

<h3>How to Book</h3>
<p>
    Make your reserveration via eMail to <strong><?= $frontmatter["email"] ?></strong>, mentioning the booking code "<strong><?= $frontmatter["code"] ?></strong>".<br />
    To ensure a smooth booking process, we have prepared tools for you to generate a uniform eMail:
    <ul>
        <li><a target="_blank" href="hotelui/en/reservation-form.html?single=1&triple=0&keyword=<?= $frontmatter["title"]?>&mail=<?= $frontmatter["email"] ?>&code=<?= $frontmatter["code"] ?>&category=Standard">🇬🇧 English</a></li>
        <li><a target="_blank" href="hotelui/de/reservation-form.html?single=1&triple=0&keyword=<?= $frontmatter["title"]?>&mail=<?= $frontmatter["email"] ?>&code=<?= $frontmatter["code"] ?>&category=Standard">🇩🇪 German</a></li>
    </ul>
</p>