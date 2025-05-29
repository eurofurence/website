<div id="ef-home-banner">
    <h1>Eurofurence <?= $this->current->number ?></h1>
    <p>
        <?= $this->current->theme ?> <br />
        <?= $this->current->dates ?> <br />
        <?= $this->current->location ?> <span class="ef-uk-icon-lift" uk-icon="question" uk-tooltip="<?= $this->current->datesAnnotation ?>"></span>
    </p>
</div>

<div class="uk-margin-top">
    <a href="#content" id="scrolldown" uk-scroll="offset: 50"><strong>Scroll Down<br /><span uk-icon="icon: chevron-down"></span></strong></a>

    <h1><?= $this->current->title ?></h1>
</div>

<strong>Deployment Quick Start:</strong>
<ul>
    <li>update <em>partners.json</em> and run <a href="updatepartners.php">updatepartners.php</a> to update / load banners</li>
    <li>run <a href="?export">/?export</a> to deploy to <em><?= $this->config->staticOut->path ?></em></li>
</ul>

<?php debug($this->current) ?>