<?php $page = [
    "owner"       => "@Draugvorn",
    "editor"      => "@FuviiPeshu",
    "title"       => "The Daily Eurofurence",
    "description" => "The Daily Eurofurence is a printed daily newsletter that is distributed for free during Eurofurence.",
    "keywords"    => "Daily, Daily Eurofurence, News, Newspaper, Printed news",
    "ogpImage"    => "",
    "robots"      => ""
]; ?>

<style>
    .daily-with-background {
        position: relative;
    }
    .daily-with-background::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url("img/pages/daily/DailyEF.webp");
        background-size: contain;
        background-position: right;
        background-repeat: no-repeat;
        opacity: 0.08;
    }

	.daily-editions-list {
		display: grid;
		gap: 16px;
	}

	.daily-state {
		margin: 0;
		align-items: center;
	}

	.daily-issue-list {
		display: grid;
		grid-template-columns: repeat(4, minmax(0, 1fr));
		gap: 8px;
	}
    .daily-archive-section .daily-issue-list {
		grid-template-columns: repeat(5, minmax(0, 1fr));
	}

	.daily-edition-heading {
		display: flex;
		align-items: center;
		gap: 8px;
		flex-wrap: wrap;
        line-height: 1.6;
	}

	.daily-issue-card {
		display: flex;
		flex-direction: column;
		gap: 0;
		padding: 0;
		min-width: 0;
		border: none;
		border-radius: var(--ef-border-radius);
		background: var(--ef-palette-2);
		text-align: left;
		cursor: pointer;
		overflow: hidden;
		transition: transform 128ms ease;
	}
	.daily-issue-card:hover,
	.daily-issue-card:focus-visible,
	.daily-issue-card.is-selected {
		transform: translateY(-2px);
		outline: none;
	}

	.daily-archive-section .daily-issue-card-past {
		position: relative;
		min-height: 84px;
		--daily-logo-right: 10px;
		--daily-logo-size: 58px;
		--daily-ring-size: 37px;
		--daily-ring-inset-right: 1px;
		--daily-ring-inset-top: 10px;
	}
	.daily-archive-section .daily-issue-card-past::before {
		content: "";
		position: absolute;
		right: calc(var(--daily-logo-right) + var(--daily-ring-inset-right));
		top: calc(50% - (var(--daily-logo-size) / 2) + var(--daily-ring-inset-top));
		width: var(--daily-ring-size);
		height: var(--daily-ring-size);
		border-radius: 50%;
		border: 4px solid var(--daily-ring-color, #eea938);
		pointer-events: none;
		z-index: 0;
	}
	.daily-archive-section .daily-issue-card-past::after {
		content: "";
		position: absolute;
		right: var(--daily-logo-right);
		top: calc(50% - (var(--daily-logo-size) / 2));
		width: var(--daily-logo-size);
		height: var(--daily-logo-size);
		background-image: url("img/pages/daily/DailyEF_logo.webp");
		background-repeat: no-repeat;
		background-position: center;
		background-size: contain;
		pointer-events: none;
		z-index: 1;
	}

	.daily-issue-card-content {
		display: flex;
		flex-direction: column;
		min-width: 0;
        padding: 8px;
	}
	.daily-archive-section .daily-issue-card-past .daily-issue-card-content {
		position: relative;
		z-index: 2;
		padding: 8px;
		justify-content: center;
	}

	.daily-edition-year-2009, .daily-issue-list-year-2009, .daily-issue-card-year-2009 { --daily-ring-color: #646464; }
	.daily-edition-year-2010, .daily-issue-list-year-2010, .daily-issue-card-year-2010 { --daily-ring-color: #746A51; }
	.daily-edition-year-2011, .daily-issue-list-year-2011, .daily-issue-card-year-2011 { --daily-ring-color: #9C885C; }
	.daily-edition-year-2012, .daily-issue-list-year-2012, .daily-issue-card-year-2012 { --daily-ring-color: #B49552; }
	.daily-edition-year-2013, .daily-issue-list-year-2013, .daily-issue-card-year-2013 { --daily-ring-color: #e0b14e; }
	.daily-edition-year-2014, .daily-issue-list-year-2014, .daily-issue-card-year-2014 { --daily-ring-color: #f2aa31; }
	.daily-edition-year-2015, .daily-issue-list-year-2015, .daily-issue-card-year-2015 { --daily-ring-color: #c2fb42; }
	.daily-edition-year-2016, .daily-issue-list-year-2016, .daily-issue-card-year-2016 { --daily-ring-color: #b76da6; }
	.daily-edition-year-2017, .daily-issue-list-year-2017, .daily-issue-card-year-2017 { --daily-ring-color: #3d88bd; }
	.daily-edition-year-2018, .daily-issue-list-year-2018, .daily-issue-card-year-2018 { --daily-ring-color: #b13736; }
	.daily-edition-year-2019, .daily-issue-list-year-2019, .daily-issue-card-year-2019 { --daily-ring-color: #252478; }
	.daily-edition-year-2020, .daily-issue-list-year-2020, .daily-issue-card-year-2020 { --daily-ring-color: #c73883; }
	.daily-edition-year-2021, .daily-issue-list-year-2021, .daily-issue-card-year-2021 { --daily-ring-color: #b8d7f1; }
	.daily-edition-year-2022, .daily-issue-list-year-2022, .daily-issue-card-year-2022 { --daily-ring-color: #f5e4bf; }
	.daily-edition-year-2023, .daily-issue-list-year-2023, .daily-issue-card-year-2023 { --daily-ring-color: #7ebeaa; }
	.daily-edition-year-2024, .daily-issue-list-year-2024, .daily-issue-card-year-2024 { --daily-ring-color: #b76da6; }
	.daily-edition-year-2025, .daily-issue-list-year-2025, .daily-issue-card-year-2025 { --daily-ring-color: #565472; }

	.daily-issue-mini-preview {
        position: relative;
		display: block;
		overflow: hidden;
		aspect-ratio: 16 / 10;
		height: auto;
		border-radius: var(--ef-border-radius) var(--ef-border-radius) 0 0;
	}

	.daily-issue-mini-preview iframe {
		display: block;
		position: absolute;
		top: -10%;
		left: 52%;
		width: 100%;
		height: 200px;
		border: 0;
		transform: translateX(-50%) scale(1.28);
		transform-origin: top center;
        pointer-events: none;
    }

	.daily-issue-mini-placeholder {
		display: block;
		width: 100%;
		height: 100%;
		padding: 8px;
		object-fit: contain;
		background: #ffffff;
		pointer-events: none;
	}

    .daily-issue-card-title {
        margin: 0;
    }
    .daily-archive-section .daily-issue-card-title {
		font-size: 1rem;
		line-height: 1.4;
	}

	.daily-issue-card-subtitle {
		display: block;
		margin: 2px 8px 8px 0;
	}
	.daily-archive-section .daily-issue-card-subtitle {
		font-size: 0.8rem;
		margin: 2px 0 0;
	}

	#daily-pdf-modal .daily-pdf-modal-dialog {
		width: min(1200px, 90vw);
		padding: 0;
        background: var(--ef-green);
		display: flex;
		flex-direction: column;
		overflow: hidden;

	}

    #daily-pdf-modal-title {
        text-align: center;
        padding: 4px 32px 0 4px;
        font-weight: 600;
        color: var(--ef-palette-2);
    }

	#daily-pdf-modal .daily-pdf-modal-frame-wrap {
		height: 82vh;
		min-height: 0;
	}

	#daily-pdf-modal-frame {
		width: 100%;
		height: 100%;
		border: 0;
        border-radius: 0 0 var(--ef-border-radius) var(--ef-border-radius);
		background: #ffffff;
	}

    @media (max-width: 1200px) {
        .daily-archive-section .daily-issue-list {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

	@media (max-width: 960px) {
        .daily-with-background::before {
            height: 50%;
        }

        .daily-issue-card-title {
            font-size: 1.1rem;
        }

        .daily-issue-list,
        .daily-archive-section .daily-issue-list {
			grid-template-columns: repeat(2, minmax(0, 1fr));
		}

		.daily-archive-section .daily-issue-card-past {
			min-height: 78px;
			--daily-logo-right: 8px;
			--daily-logo-size: 52px;
			--daily-ring-size: 33px;
			--daily-ring-inset-right: 0px;
			--daily-ring-inset-top: 9px;
		}
	}

	@media (max-width: 640px) {
        .daily-with-background::before {
            background-image: none;
            display: none;
        }

		.daily-archive-section .daily-issue-card-past {
			min-height: 72px;
			--daily-logo-right: 8px;
			--daily-logo-size: 44px;
			--daily-ring-size: 27px;
			--daily-ring-inset-right: 1px;
			--daily-ring-inset-top: 7px;
		}

        .daily-issue-card-title {
            font-size: 0.9rem;
        }
        .daily-archive-section .daily-issue-card-title {
			font-size: 0.85rem;
		}

        .daily-issue-card-subtitle {
            font-size: 0.8rem;
        }
        .daily-archive-section .daily-issue-card-subtitle {
			font-size: 0.75rem;
		}

        #daily-pdf-modal {
            padding: 0;
        }

        #daily-pdf-modal-title {
            font-size: 1.2rem;
            padding-top: 8px;
        }

		#daily-pdf-modal .daily-pdf-modal-dialog {
			width: 100vw;
            height: 100vh;
		}

		#daily-pdf-modal .daily-pdf-modal-frame-wrap {
			height: auto;
			flex: 1;
			min-height: 0;
		}
	}
</style>

<section>
    <div class="daily-with-background">
        <h1>The Daily Eurofurence</h1>
        <div id="location-ef-daily"></div>
        <script>
            window.EFPageLifecycle.addEventListener("load", () => {
                createEFnavTrigger('location-ef-daily', [
                    {
                        id: 'ef-daily-entrance-location',
                        title: 'EF Daily (Entrance)',
                        subtitle: 'CCH: Level 0, Entrance Hall, Area near the main entrance',
                        slug: 'ef-daily-point-entrance',
                    },
                    {
                        id: 'ef-daily-level1-location',
                        title: 'EF Daily (Level 1)',
                        subtitle: 'CCH: Level 1, Foyer X, Area near stairs and infodesk',
                        slug: 'ef-daily-point-level-1',
                    },
                    {
                        id: 'ef-daily-level2-location',
                        title: 'EF Daily (Level 2)',
                        subtitle: 'CCH: Level 2, Foyer Y, Area near stairs',
                        slug: 'ef-daily-point-level-2',
                    }
                ], { tooltip: 'EF Daily locations on map' });
            }, { once: true });
        </script>
        <p>
            The&nbsp;Daily&nbsp;Eurofurence is your four-page newspaper crafted by a&nbsp;team of avid photographers, writers, and typesetters, freshly printed each day of&nbsp;the&nbsp;con!
            Look&nbsp;out for the&nbsp;standees scattered across the&nbsp;con site to grab the&nbsp;latest issue.
        </p>
        <p>
            Unlike all those dry-nose newspapers, the&nbsp;Daily&nbsp;Eurofurence is jam-packed with essential information about the&nbsp;con and the&nbsp;myriad&nbsp;of exciting events happening each and every&nbsp;day.
            Our dedicated team aims to provide you&nbsp;with exclusive behind-the-scenes glimpses&nbsp;of&nbsp;Eurofurence, alert you to upcoming events you don't want to miss, and share captivating snapshots of&nbsp;the&nbsp;con's most thrilling moments.
            Collect each issue throughout the&nbsp;convention and cherish a&nbsp;delightful stack&nbsp;of unforgettable memories!
        </p>
        <p>
            So whether you prefer the&nbsp;tangible joy of flipping through pages of paper or the&nbsp;ease&nbsp;of browsing online, the&nbsp;Daily&nbsp;Eurofurence has something for&nbsp;everyone!
            Stay informed, entertained, and connected throughout&nbsp;the&nbsp;con with your daily dose of Eurofurence&nbsp;awesomeness.
        </p>
    </div>
	<div id="daily-current-list" class="daily-editions-list uk-margin-top"></div>
</section>

<section>
	<h2>Contact</h2>
	<p>
        Do you have an&nbsp;idea for an&nbsp;article?
        Or do you want&nbsp;to share some feedback?
        <br/>
        Write to the&nbsp;<a href="https://help.eurofurence.org/contact/daily">Daily&nbsp;Eurofurence&nbsp;Team</a>.
    </p>
</section>

<section class="daily-archive-section">
	<h2>PDF-Archive</h2>
    <p>
        For those who prefer digital convenience, fear not!
        All editions of the&nbsp;Daily&nbsp;Eurofurence can be found online at <a href="https://archive.eurofurence.org" target="_blank">Archive&nbsp;Website</a>.
        There, you'll discover a&nbsp;treasure trove of past&nbsp;Eurofurence newspapers, allowing you to take a&nbsp;nostalgic trip down memory&nbsp;lane.
        Who knows, you might stumble upon a&nbsp;picture of that furry friend you know, frozen in time from a&nbsp;decade ago!
        Feel free to take a&nbsp;peek and relive the&nbsp;magic of&nbsp;the&nbsp;past.
    </p>
	<div id="daily-archive-list" class="daily-editions-list uk-margin-top"></div>
	<div class="uk-margin-top uk-flex uk-flex-center">
		<button id="daily-show-all-past-btn" class="uk-button uk-button-default" type="button" hidden>Show all past editions</button>
	</div>
</section>

<div id="daily-pdf-modal" uk-modal>
	<div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical daily-pdf-modal-dialog">
		<button class="uk-modal-close-default" type="button" uk-close aria-label="Close Daily issue viewer"></button>
		<h3 id="daily-pdf-modal-title" class="uk-margin-small-bottom"></h3>
		<div class="daily-pdf-modal-frame-wrap">
			<iframe
				id="daily-pdf-modal-frame"
				title="The Daily Eurofurence PDF Viewer"
				loading="lazy"
				referrerpolicy="no-referrer"
			></iframe>
		</div>
	</div>
</div>

<script>
window.__EF_DAILY_CONFIG__ = {
	currentEditionYear: <?= isset($this->config->convention->year) ? (int) $this->config->convention->year : 'null' ?>,
    showPreviousEditions: <?= isset($this->config->dailyEF->showPreviousEditions) ? ($this->config->dailyEF->showPreviousEditions ? 'true' : 'false') : 'null' ?>,
	prevEditionsToShowWhenCollapsed: <?= isset($this->config->dailyEF->prevEditionsToShowWhenCollapsed) ? (int) $this->config->dailyEF->prevEditionsToShowWhenCollapsed : 'null' ?>,
};
</script>

<script src="js/daily.js"></script>
