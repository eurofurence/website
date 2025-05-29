<div id="ef-home-banner">
    <h1>Eurofurence <?= $this->current->number ?></h1>
    <p>
        <?= $this->current->theme ?> <br />
        <?= $this->current->dates ?> <br />
        <?= $this->current->location ?> <span class="ef-uk-icon-lift" uk-icon="question" uk-tooltip="<?= $this->current->datesAnnotation ?>"></span>
    </p>
</div>

<div id="ef-home-photo">
    <p>
        Lorem ipsum dolor sit amet adipiscing et te duo dolores justo sed consequat feugiat amet elitr amet at amet lorem et et et. Stet accusam eum dolores no duo ipsum sit clita illum aliquyam nonumy tempor elit sadipscing lorem amet. Cum justo possim kasd et et duo elitr suscipit et. Magna facilisis dolor diam et qui ipsum dolores. Dolor facilisi justo qui magna justo amet tempor.
        <br /><br />
        <a href="https://identity.eurofurence.org/" class="uk-button uk-button-primary" target="_blank">REGISTER NOW</a>
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