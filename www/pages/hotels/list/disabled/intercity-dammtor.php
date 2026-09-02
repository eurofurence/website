<?php
    $frontmatter["title"] = "InterCityHotel Dammtor-Messe";
    $frontmatter["price"] = "189&ndash;219";
    $frontmatter["metaprice"] = "219";
    $frontmatter['distance'] = "0.5";
    $frontmatter['badge'] = "";
    $frontmatter["breakfast"] = true;
    $frontmatter["email"] = "reservations.hamburg-dammtor@intercityhotel.com";
    $frontmatter["code"] = "Eurofurence";
?>

<h2><?= $frontmatter['title'] ?></h2>

<p>The <?= $frontmatter["title"] ?> offers <strong>Standard</strong> rooms for <strong>👥 double</strong> or <strong>👤 single</strong> room accommodation.</p>

<p>💸 Price: 219€ (double room) or 189€ (single room) per night</p>
<p>🥞 breakfast included</p>

<h3>How to Book</h3>
<p>
    Make your reserveration via eMail to <strong><?= $frontmatter["email"] ?></strong>, mentioning the booking code "<strong><?= $frontmatter["code"] ?></strong>".<br />
    To ensure a smooth booking process, we have prepared tools for you to generate a uniform eMail:
    <ul>
        <li><a target="_blank" href="hotelui/en/reservation-form.html?single=1&triple=0&keyword=<?= $frontmatter["title"]?>&mail=<?= $frontmatter["email"] ?>&code=<?= $frontmatter["code"] ?>&category=Standard">🇬🇧 English</a></li>
        <li><a target="_blank" href="hotelui/de/reservation-form.html?single=1&triple=0&keyword=<?= $frontmatter["title"]?>&mail=<?= $frontmatter["email"] ?>&code=<?= $frontmatter["code"] ?>&category=Standard">🇩🇪 German</a></li>
    </ul>
</p>