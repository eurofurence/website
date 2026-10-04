<?php
    $frontmatter["title"] = "Ruby Lotti Hotel Hamburg";
    $frontmatter["price"] = "199&ndash;249";
    $frontmatter["metaprice"] = "237";
    $frontmatter['distance'] = "1.6";
    $frontmatter['badge'] = "🚫 🥞";
    $frontmatter["breakfast"] = false;
    $frontmatter["email"] = "";
    $frontmatter["code"] = "EURO120924";
?>

<h2><?= $frontmatter['title'] ?></h2>

<p>The <?= $frontmatter["title"] ?> offers <strong>NEST ROOMS</strong> (their name for a Standard room) for 👤 single</strong> room accommodation.</p>

<p>💸 Price varies between 249€ for Wedneday, Thursday and Friday, 199€ for Saturday and 149€ for Sunday.</p>
<p>ℹ️ These conditions can be booked until 10.02.2024; prices may vary beyond that date.</p>

<h3>How to Book</h3>
<p>
    Go to <a href="https://www.ruby-hotels.com" target="_blank">www.ruby-hotels.com</a> and book your desired room using the Ruby Code "<strong><?= $frontmatter["code"] ?></strong>".<br />
    You will need a valid credit card.
</p>