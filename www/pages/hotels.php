<?php $page = [
    "owner"       => "@draconigen",
    "editor"      => "",
    "title"       => "Venue - Nearby Hotel Options",
    "description" => "Get a room! Here are some official options for you.",
    "keywords"    => "Hotel, Accommodation, Rooms",
    "ogpImage"    => "",
    "robots"      => ""
]; ?>

<section>
    <h1>Hotel Options</h1>
    <p class="uk-text-center">We are preparing some deals for you. Stay tuned.</p>
</section>

<?php /*
<section>
    <h1>Hotel Options</h1>

	<p>
        We have prepared special offers with some hotels around the venue. To get you the best possible deal, consider the following tips:
        <ul>
            <li>Booking instructions may differ for each hotel! Click on the hotel of your choice to find instructions on how to book the room.</li>
            <li>We strongly recommend booking a <strong>flexible / refundable</strong> option. Prices may vary (downwards!) until a day before the con.</li>
            <li>The hotels presented here are a selection. Check out our partner deals below, other hotels, hostels and AirBnBs for more options.</li>
            <!--
            <li>As for the dates to book, consider this:</li>
            <ul>
                <li>The Summerboat party is taking place a day before the con, on Tuesday, September 17th</li>
                <li>Check-in starts on Wednesday, September 18th with the Opening Ceremony happening that same day</li>
                <li>Closing Ceremony is on Saturday, September 21st</li>
                <li>The Big Blue Dance is held from Saturday (Sep 21st) evening till Sunday (Sep 22nd) morning</li>
                <li>There's no further program planned for Sunday September 22nd, save for Dead Dog Day</li>
            </ul>
            -->
        </ul>
    </p>

    <hr />

    <div uk-filter="target: .js-filter">

    <div class="uk-grid-small uk-grid-divider uk-child-width-auto" uk-grid>
        <div>
            <ul class="uk-subnav uk-subnav-pill">
                <li uk-filter-control="sort: data-price"><a href="#">Sort by Price <span uk-icon="icon: chevron-up"></span></a></li>
                <li uk-filter-control="sort: data-price; order: desc"><a href="#">Sort by Price <span uk-icon="icon: chevron-down"></span></a></li>
                <li uk-filter-control="sort: data-distance"><a href="#">Sort by Distance <span uk-icon="icon: chevron-up"></span></a></li>
                <li uk-filter-control="sort: data-distance; order: desc"><a href="#">Sort by Distance <span uk-icon="icon: chevron-down"></span></a></li>
            </ul>
        </div>
        <div>
            <ul class="uk-subnav uk-subnav-pill">
                <li uk-filter-control><a href="#">No Filters</a>
                <li uk-filter-control="[data-breakfast='yes']"><a href="#"> <span uk-icon="icon: check"></span> With Breakfast</a></li>
                <li uk-filter-control="[data-breakfast='no']"><a href="#"> <span uk-icon="icon: close"></span> No Breakfast</a></li>
            </ul>
        </div>
    </div>

	<div class="uk-grid-small uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l uk-margin-bottom js-filter" uk-grid>
		<?php
			$path = "pages/hotels/list/";
			$files = scandir($path);
			// shuffle($files);

			foreach ($files as $file) {
                if (is_dir($path . $file))
                    continue;

				$frontmatter = ["id" => pathinfo($file, PATHINFO_FILENAME)];
			?>
			
			<div id="ef-hotel-<?= $frontmatter["id"] ?>" class="uk-modal-container" data-price="0" data-distance="0" uk-modal>
				<div class="uk-modal-dialog uk-modal-body">
					<button class="uk-modal-close-default" type="button" uk-close></button>
					<?php include_once($path . $file); ?>
				</div>
			</div>

			<div data-breakfast="<?= ($frontmatter["breakfast"]? "yes": "no") ?>" data-price="<?= $frontmatter["metaprice"] ?>" data-distance="<?= $frontmatter["distance"] ?>">
				<div class="uk-card uk-card-small uk-card-default uk-card-hover" uk-toggle="target: #ef-hotel-<?= $frontmatter["id"] ?>">
                    <div class="uk-card-media-top">
                        <img src="img/pages/hotels/<?= $frontmatter["id"] ?>.jpg" alt="<?= $frontmatter['title'] ?> room" />
                    </div>
                
                    <div class="uk-card-body">
                        <?php if (!empty($frontmatter["badge"])) { ?>
                        <div class="uk-card-badge uk-label"><?= $frontmatter["badge"] ?></div>
                        <?php } ?>

                        <h3 class="uk-card-title">
                            <?= $frontmatter["title"] ?>
                        </h3>
                        💸 <strong><?= $frontmatter["price"] ?>€</strong> / night; ↔️ <strong><?= $frontmatter["distance"] ?> km</strong> to CCH
                    </div>
				</div>
			</div>
		<?php } ?>
	</div>
    <hr />
    &copy; All image rights belong to their respective Hotel.
</section>
*/ ?>

<?php /*
<section>
    <h1>Partner Deals</h1>
    <p>More options provided by our partner, Kuoni:</p>
	<!-- by the order of Cheetah, we are to allow all external domains; implemented by disabling CSP headers completely on vserver level -->
    <div
        class="consent-cover uk-width-1-1"
        data-element-type="iframe"
        data-src="pages/hotels/kuoni.html"
        data-class="uk-width-1-1"
        data-title="Eurofurence Hotels"
        data-uk-height-viewport="offset-bottom: 120px"
    >Consent required: Click here to allow external contents.</div>

    <hr />

    <div class="uk-child-width-1-2@m uk-grid-divider uk-text-left" uk-grid>
        <div>
            <h4><span uk-icon="warning"></span>Notice regarding hotel booking:</h4>
            <p>The booking of accommodations is offered through a third-party provider. The contracting party for the hotel booking is solely the respective provider/hotelier. The convention organizer and the ticketing portal act only as intermediaries and are not contracting parties for the accommodation service. No packaged tour or linked travel arrangement within the meaning of §§ 651a et seq. BGB is created. Any claims in connection with the hotel booking must be made directly with the hotel or the booking portal.</p>
        </div>
        <div>
            <h4><span uk-icon="warning"></span>Hinweis zur Hotelbuchung:</h4>
            <p>Die angebotene Unterkunftsbuchung erfolgt über einen Drittanbieter. Vertragspartner für die Hotelbuchung ist ausschließlich der jeweilige Anbieter/Hotelier. Der Convention-Veranstalter sowie das Ticketing-Portal treten hierbei lediglich als Vermittler auf und sind nicht Vertragspartner der Unterkunftsleistung. Es entsteht keine Pauschalreise oder verbundene Reiseleistung im Sinne der §§ 651a ff. BGB. Etwaige Ansprüche im Zusammenhang mit der Hotelbuchung sind direkt gegenüber dem Hotel bzw. dem Buchungsportal geltend zu machen.</p>
        </div>
    </div>
</section>
*/ ?>