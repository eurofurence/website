<?php $page = [
    "owner"       => "",
    "editor"      => "@Syntaxfur",
    "title"       => "Open Staff Positions",
    "description" => "You want to help Eurofurence and become staff at Europe's biggest furry convention? Read on for currently open positions.",
    "keywords"    => "Jobs, Volunteer, Staff, Crew, Help, Gopher, Angel",
    "ogpImage"    => "",
    "robots"      => ""
]; ?>


<style>
	.ef-jobs-accordion {
		margin: 0;
	}

	.ef-jobs-accordion > li {
		padding: 8px 0;
		border-bottom: 1px solid rgba(0, 0, 0, 0.16);
		transition: border-color .15s ease;
	}
	.ef-jobs-accordion > li.uk-open {
		border-bottom-color: rgba(0, 0, 0, 0);
	}

	.ef-jobs-accordion > li:first-child {
		border-top: 0;
	}

	.ef-jobs-accordion > li:last-child {
		border-bottom: 0;
	}

	.ef-jobs-accordion > :nth-child(n+2) {
		margin-top: 0;
	}

	.ef-jobs-accordion .uk-accordion-title {
		display: flex;
		align-items: center;
		position: relative;
		padding-left: 24px;
		padding-right: 0;
		font-size: 1.4rem;
		font-weight: 600;
		text-decoration: none;
		border-bottom: 0;
	}

	.ef-jobs-accordion .uk-accordion-title::before {
		content: none;
	}

	.ef-jobs-accordion .uk-accordion-title::after {
		content: "";
		position: absolute;
		left: 4px;
		top: 50%;
		width: 8px;
		height: 8px;
		border-right: 1px solid currentColor;
		border-bottom: 1px solid currentColor;
		transform: translateY(-60%) rotate(-45deg);
		transition: transform .15s ease;
	}

	.ef-jobs-accordion > li.uk-open > .uk-accordion-title::after {
		transform: translateY(-75%) rotate(45deg);
	}

	.ef-jobs-accordion .uk-accordion-title:hover,
	.ef-jobs-accordion .uk-accordion-title:focus {
		text-decoration: none;
	}

	.ef-job-count {
		font-size: 0.9rem;
		font-weight: 400;
		margin-left: auto;
		padding-left: 12px;
		text-align: right;
		white-space: nowrap;
	}

	.ef-jobs-toolbar-card {
        padding: 32px;
		margin-bottom: 20px;
	}

	.ef-jobs-actions {
		display: flex;
		gap: 8px;
		flex-wrap: wrap;
		justify-content: flex-end;
	}

	.ef-jobs-controls {
		display: flex;
		flex-direction: column;
		align-items: flex-end;
		gap: 6px;
	}

    .ef-jobs-button {
        padding-left: 16px;
        padding-right: 16px;
    }

	.ef-jobs-found-count {
		margin: 0;
	}

	.ef-jobs-toggle {
		white-space: nowrap;
	}

	.ef-jobs-search .uk-form-label {
		display: block;
		margin-bottom: 6px;
	}

	.ef-job-grid {
		display: flex;
		flex-direction: row;
		flex-wrap: wrap;
		gap: 8px;
	}

	.ef-jobs-accordion .uk-accordion-content {
		margin-top: 8px;
	}

	.ef-job-tile {
		flex: 1 1 260px;
		min-width: 220px;
		max-width: 320px;
		opacity: 0;
		transform: translateY(8px);
		transition: opacity .15s ease, transform .15s ease;
	}

	.ef-jobs-accordion > li.uk-open .ef-job-tile {
		opacity: 1;
		transform: translateY(0);
	}

    .ef-job-card {
        transition: transform .15s ease;
        cursor: pointer;
		width: 100%;
        min-width: 200px;
		max-width: 400px;
    }
    .ef-job-card:hover {
        transform: translateY(-2px);
    }

	.ef-job-card .uk-card-body {
		position: relative;
		padding: 8px 12px;
		justify-content: space-between;
	}

    .ef-job-title {
        margin: 0 8px 0 0;
    }

	.ef-job-date {
		align-self: flex-end;
		margin-top: 8px;
		font-size: 0.8rem;
		line-height: 1;
	}

	.ef-job-new {
		margin: 0 0 auto auto;
		padding: 2px 6px;
		border-radius: 999px;
		font-size: 0.7rem;
		letter-spacing: 0.04em;
		text-transform: uppercase;
		background: var(--ef-palette-1);
		color: var(--ef-palette-2);
	}

	.ef-job-modal-meta {
		margin-top: 8px;
		text-align: right;
	}

	.ef-jobs-empty {
		margin-top: 12px;
	}

	@media (max-width: 639px) {
		.ef-jobs-toolbar-card {
			font-size: 0.8rem;
			padding: 20px;
		}

		.ef-jobs-toolbar-card input {
			padding-right: 8px !important;
		}

		.ef-jobs-actions {
			justify-content: flex-start;
		}

		.ef-jobs-controls {
			align-items: flex-start;
		}

        .ef-job-tile {
			flex-basis: 100%;
			max-width: 100%;
		}
	}
</style>

<section>
    <h1>Open Staff Positions at Eurofurence</h1>
    <p>A convention as big as Eurofurence can't simply grow without people committing some of their time and skills to it - people like you. Every now and then we are looking for creative people willing to volunteer, to help us with making Eurofurence the best possible experience for everyone! In case you find yourself addressed by one of the following job-offers, feel free to drop us a line. We will gladly have you aboard.<br/><br/>We can't offer any form of payment, but we're sure that seeing all those happy attendees will be reward enough.</p>
    <p>If you don't find a job offer that suits you or peaks your interest, or you have any questions regarding staffing, please reach out to <a href="mailto:recruitment@eurofurence.org">recruitment@eurofurence.org</a>.</p>
</section>

<section>
    <?php
    $newLabelDays = $this->config->jobs->newLabelDays ?? 14;
    $newLabelMaxAgeSeconds = $newLabelDays * 24 * 60 * 60;
    $nowTimestamp = time();

    $path = "pages/jobs/";
    $files = array_values(array_filter(scandir($path), static function ($file) use ($path) {
        $fullPath = $path . $file;
        return is_file($fullPath) && strtolower(pathinfo($fullPath, PATHINFO_EXTENSION)) === "php";
    }));
    $jobs = [];
    $jobsByDepartment = [];

    foreach ($files as $file) {
        $frontmatter = ["id" => pathinfo($file, PATHINFO_FILENAME)];
        $fullPath = $path . $file;
        $modifiedTimestamp = @filemtime($fullPath);
        $modifiedLabel = "";
        $isNew = false;
        if ($modifiedTimestamp !== false) {
            $modifiedLabel = date("M j, Y", $modifiedTimestamp);
            $isNew = ($nowTimestamp - $modifiedTimestamp) <= $newLabelMaxAgeSeconds;
        }

        ob_start();
        include($fullPath);
        $contentHtml = ob_get_clean();
        $searchBlob = strtolower(trim(preg_replace('/\s+/', ' ', implode(' ', [
            $frontmatter["id"],
            $frontmatter["title"] ?? "",
            $frontmatter["department"] ?? "General",
            strip_tags($contentHtml),
        ]))));

        $job = [
            "id" => $frontmatter["id"],
            "title" => $frontmatter["title"] ?? "",
            "department" => $frontmatter["department"] ?? "General",
            "modified" => $modifiedLabel,
            "isNew" => $isNew,
            "search" => $searchBlob,
            "content" => $contentHtml,
        ];

        $jobs[] = $job;

        $department = $job["department"] ?: "General";
        if (!array_key_exists($department, $jobsByDepartment)) {
            $jobsByDepartment[$department] = [];
        }
        $jobsByDepartment[$department][] = $job;
    }

    $totalOpenPositions = count($jobs);
    ?>

    <div class="uk-card uk-card-default uk-card-body uk-margin-bottom ef-jobs-toolbar-card">
        <div class="uk-grid-small uk-flex-middle" uk-grid>
            <div class="uk-width-expand@m">
                <div class="ef-jobs-search">
                    <label id="ef-jobs-search-label" for="ef-jobs-search" class="uk-form-label">Search Positions</label>
                    <div class="uk-inline uk-width-1-1">
                        <span class="uk-form-icon" uk-icon="icon: search"></span>
                        <input id="ef-jobs-search" type="search" class="uk-input"
                            placeholder="Search by title, description, or department" />
                    </div>
                </div>
            </div>
            <div class="uk-width-auto@m">
                <div class="ef-jobs-controls">
                    <p id="ef-jobs-found-count" class="uk-text-meta ef-jobs-found-count">
                        <?= $totalOpenPositions ?> found
                    </p>
                    <div class="ef-jobs-actions" role="group" aria-label="Job section controls">
                        <button id="ef-jobs-expand-all" type="button"
                            class="uk-button uk-button-default ef-jobs-button">Expand All</button>
                        <button id="ef-jobs-collapse-all" type="button"
                            class="uk-button uk-button-default ef-jobs-button">Collapse All</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <ul class="uk-accordion ef-jobs-accordion" uk-accordion="multiple: true">
        <?php foreach ($jobsByDepartment as $department => $departmentJobs) {
            $departmentHasNew = false;
            $departmentPositionCount = count($departmentJobs);
            foreach ($departmentJobs as $departmentJob) {
                if ($departmentJob["isNew"]) {
                    $departmentHasNew = true;
                }
            }
            ?>
            <li<?= $departmentHasNew ? " class=\"uk-open\"" : "" ?>>
                <a class="uk-accordion-title" href="#">
                    <?= htmlspecialchars($department, ENT_QUOTES) ?>
                    <span class="uk-text-meta ef-job-count">
                        <?= $departmentPositionCount ?>
                        position<?= $departmentPositionCount === 1 ? "" : "s" ?>
                    </span>
                </a>
                <div class="uk-accordion-content">
                    <div class="ef-job-grid">
                        <?php foreach ($departmentJobs as $job) { ?>
                            <div class="ef-job-tile">
                                <article class="uk-card uk-card-small uk-card-default uk-flex uk-flex-column ef-job-card"
                                    data-job-id="<?= htmlspecialchars($job["id"], ENT_QUOTES) ?>"
                                    data-job-modified="<?= htmlspecialchars($job["modified"], ENT_QUOTES) ?>"
                                    data-job-search="<?= htmlspecialchars($job["search"], ENT_QUOTES) ?>"
                                    data-job-new="<?= $job["isNew"] ? "1" : "0" ?>" title="View details" role="button"
                                    tabindex="0"
                                    aria-label="Open details for <?= htmlspecialchars($job["title"], ENT_QUOTES) ?> in <?= htmlspecialchars($department, ENT_QUOTES) ?>">
                                    <div class="uk-card-body uk-flex uk-flex-column uk-flex-1">
                                        <div class="uk-flex uk-flex-row">
                                            <h4 class="ef-job-title"><?= htmlspecialchars($job["title"], ENT_QUOTES) ?></h4>
                                            <?php if ($job["isNew"]) { ?>
                                                <span class="ef-job-new">New</span>
                                            <?php } ?>
                                        </div>
                                        <?php if (!empty($job["modified"])) { ?>
                                            <span class="uk-text-meta ef-job-date">Last updated:
                                                <?= htmlspecialchars($job["modified"], ENT_QUOTES) ?></span>
                                        <?php } ?>
                                    </div>
                                </article>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </li>
        <?php } ?>
    </ul>

    <div id="ef-jobs-empty" class="uk-text-meta ef-jobs-empty" hidden>No positions match your search.</div>

    <div id="ef-job-modal" class="uk-modal uk-modal-container" uk-modal="container: body">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <div id="ef-job-modal-content"></div>
            <div id="ef-job-modal-meta" class="uk-text-meta ef-job-modal-meta"></div>
        </div>
    </div>

    <?php foreach ($jobs as $job) { ?>
        <template id="ef-job-content-<?= htmlspecialchars($job["id"], ENT_QUOTES) ?>">
            <?= $job["content"] ?>
        </template>
    <?php } ?>
</section>

<section>
    <p>These positions are subject to constant change throughout the year. If you're interested in helping us out, make sure to check this page periodically so that you don't miss your favorite job!</p>
</section>

<script src="js/jobs.js"></script>
