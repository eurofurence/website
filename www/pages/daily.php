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

	.daily-issue-card-title {
		display: block;
		margin: 4px 8px 0;
        font-size: 1.2rem;
		line-height: 1.5;
	}

	.daily-issue-card-subtitle {
		display: block;
		margin: 2px 8px 8px;
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

	@media (max-width: 960px) {
        .daily-issue-card-title {
            font-size: 1.1rem;
        }

        .daily-issue-list {
			grid-template-columns: repeat(2, minmax(0, 1fr));
		}
	}

	@media (max-width: 640px) {
        .daily-issue-card-title {
            font-size: 0.9rem;
        }

        .daily-issue-card-subtitle {
            font-size: 0.8rem;
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
	<h1>The Daily Eurofurence</h1>
	<div id="location-ef-daily"></div>
	<script>
		document.addEventListener('DOMContentLoaded', () => {
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
		});
	</script>
	<p>The Daily Eurofurence is a printed daily newsletter that is distributed for free at Eurofurence. Introduced at Eurofurence 15, it quickly found a broad readership among the attendees.</p>
	<p>The Daily features articles, reviews and interviews on various topics concerning Eurofurence and the Furry Fandom in general. It also includes stories, cartoons, announcements and the latest timetable information.</p>

	<div id="daily-current-list" class="daily-editions-list uk-margin-top"></div>
</section>

<section>
	<h2>Contact</h2>
	<p>Do you have an idea for an article? Or do you want to share some feedback? Write to the <a href="https://help.eurofurence.org/contact/daily">Daily Eurofurence Team</a>.</p>
</section>

<section>
	<h2>PDF-Archive</h2>
    <p>
        Feeling nostalgic? Lost your copies? All issues of the "Daily" are now available for download or home-printing!<br />
        Visit our <a href="https://archive.eurofurence.org" target="_blank">Archive Website</a> to browse the history of both Eurofurence and The Daily.
    </p>
    <div id="daily-archive-list" class="daily-editions-list uk-margin-top"></div>
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

<script src="js/daily.js"></script>
