<style>
    @import url("css/mastodon-timeline.min.css");
    #mt-container, #ef-intro-text {
        height: 740px;
        overflow: scroll;
    }
    .mt-container, .mt-container[data-theme="light"], .mt-dialog, .mt-dialog[data-theme="light"] {
        --mt-color-bg: rgba(255, 255, 255, .1);
        border-radius: 8px;
    }
</style>

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

<div class="uk-grid-match uk-child-width-1-2@m uk-margin-medium-top" uk-grid>
    <div>
        <div id="mt-container" class="mt-container">
            <div class="mt-body" role="feed">
                <div class="mt-loading-spinner"></div>
            </div>
        </div>
    </div>
    <div id="ef-intro-text" class="uk-padding uk-padding-remove-vertical">
        <p>Lorem ipsum dolor sit amet duis dolor magna tincidunt et ea duo doming dolores gubergren consectetuer. Eum ipsum vero diam magna. Amet et assum invidunt exerci sea vero nam justo stet consetetur rebum blandit. At vero in et sanctus ipsum ut voluptua feugait eum kasd elitr iriure est et. Quod accumsan elitr sed dolores ipsum duis. Option ut amet clita vero lobortis lorem suscipit kasd feugiat no consectetuer rebum dolor vero ea gubergren in et. At diam amet eu justo ipsum sadipscing no lorem et diam minim takimata. Dolore dolore nihil et nulla et ut et euismod consequat et volutpat at sit clita ipsum tempor. Erat rebum et ipsum no. Illum dignissim nibh diam vero zzril no clita et diam vero. Justo et gubergren iriure takimata molestie vulputate tincidunt lorem diam sed et sanctus. In ipsum dolore accusam hendrerit nostrud sed lorem gubergren justo option voluptua kasd voluptua ut at gubergren facilisis et. Autem lorem takimata sanctus feugiat nisl dolor. Luptatum sea labore sea vel sanctus takimata sit dignissim sit elit est sit tempor.</p>
        <p>Ullamcorper cum diam gubergren sed sit at invidunt adipiscing. Lobortis dolore kasd amet aliquyam et vero. Rebum sit rebum sadipscing. Sed et takimata kasd sanctus luptatum stet justo eu illum vulputate amet. Magna et diam ut rebum et sit nihil commodo odio sanctus rebum tempor et magna et. Et hendrerit labore ea sadipscing dolor suscipit at erat feugiat. Amet dignissim est erat esse aliquyam nonummy elitr clita praesent accusam justo sit tation. Eirmod nulla labore tempor ea sadipscing elitr congue sit vel sadipscing sea aliquam sed. Consetetur hendrerit praesent zzril elitr nonumy et stet. Lorem ut tation imperdiet iriure no dolore. Lorem aliquyam dolor in. Diam diam option sit zzril clita magna consetetur. Veniam delenit facer sed sed lorem dignissim justo dolor vero tincidunt gubergren diam sed dolores amet vulputate tempor. Tempor voluptua elitr tempor consetetur nonummy exerci amet lorem at et gubergren blandit. Dolor sit sea nibh consequat possim dolor justo consetetur blandit delenit stet ut vero dolores dolor kasd. Et sea velit velit consetetur nonumy nonummy accusam ut gubergren at stet sit et voluptua lorem duo dolor elitr. Sit diam labore labore dolore et no consetetur elitr dolor clita iriure adipiscing vero et feugiat kasd. Eirmod sanctus nulla sed est lobortis sed et elitr magna.</p>
    </div>
</div>

<script src="js/mastodon-timeline/mastodon-timeline.umd.js"></script>

<script>
    new MastodonTimeline.Init({
        instanceUrl: "https://meow.social",
        timelineType: "profile",
        userId: "112331996724958954",
        profileName: "@eurofurence",
        dateLocale: "de-DE",
        hideUnlisted: true,
        hideReplies: true,
        hideUserAccount: true,
        hidePinnedPosts: true,
        defaultTheme: "dark"
    });
</script>