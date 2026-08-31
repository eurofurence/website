# Eurofurence Website

Version 4.3, last updated: 2026-08-01

## Requirements

either
* docker, or
* Apache Web Server with PHP 8.4 + modrewrite

## Setup

* Navigate a cli to the root directory and run `docker compose up`, or
* deploy all files from `www` to `/var/html` to be served through Apache Web Server.

Run `npm install` to fetch necessary packages and then enable the repository hooks with `npm run hooks:setup`.
The pre-commit hook rebuilds SCSS with production settings and stages the generated CSS before allowing the commit to continue.

> To run mock environment, set `USE_MOCK_DATA=true` in [.env](.env.example) file and run `docker compose up`.

## SCSS Support

Docker starts an additional `scss-watch` service that compiles SCSS changes automatically.
The watcher uses standard file change events and rebuilds on SCSS add/change/remove (fallbacks to polling mode for local docker container).
SCSS output defaults to compressed CSS for deployment. Set `SCSS_DEVTOOLS=true` for expanded CSS and source maps (for development and debug).

* Place SCSS sources anywhere in `www/`.
* Output CSS behavior:
  * All compiled files are saved in `www/css/`.
  * Files in `www/scss/` compile to `www/css/` directly.
  * Files elsewhere in `www/` compile to mirrored subpaths in `www/css/`.
  * When `SCSS_DEVTOOLS` is disabled, generated CSS is compressed (optimised for production builds).
* [UIkit Sass integration](www/scss/uikit.scss) follows the official docs https://getuikit.com/docs/sass
* [package.json](package.json) contains some helper scripts to make sure there are no `.scss` references:
  * `npm run style:check` fails if a runtime file still points at `.scss`.
  * `npm run style:fix` rewrites obvious `.scss` references to `.css` in runtime files.
  * `npm run style:ensure` checks, tries to fix, and then re-checks (useful for continuous deploy).
* Shared variables, mixins are located in the [_ef-variables.scss](www/scss/_ef-variables.scss) and [_ef-mixins.scss](www/scss/_ef-mixins.scss).
* [countries (generated flag data)](www/css/countries.css) and [mastodon-timeline](www/css/mastodon-timeline.min.css) stay as plain CSS.

#### Telegram Configuration

The *Page Rating* feature requires a Telegram bot to be configured. If, upon the attempt to use this feature, the file does not exist, you will run into an Exception saying "*Telegram config missing in FILE*", *FILE* usually being ´telegram.config.php´. The script will attempt to create the file for you, but this will most likely fail due to filesystem permissions within the container, so you will have to create the *FILE* yourself with the following contents:

```php
<?php
define('TELEGRAM_BOT_API_TOKEN', ''); # insert your Telegram BOT API token here
define('TELEGRAM_TARGET_USERID', ''); # insert the Telegram Chat ID the bot shall post updates to
```

> Read the [Telegram bot tutorial](https://core.telegram.org/bots/tutorial#obtain-your-bot-token) on how to create a bot and obtain its API token.

> In order to obtain either your own Chat ID or that of a group your bot is part of, there are various 3rd party tutorials and clients that help you with that.

#### Create modified.json

If `StaticOut.lastModifiedEnabled` is set to `true`, the website will seek out a `modified.json` file to insert when page files have last been modified based on their reported filesystem modification timestamp. If the file is not present or cannot be written, a warning message will be displayed at the bottom of the page. To fix this, simply create the file:

```bash
echo "{}" > www/modified.json 
```

Don't forget to add writing permission for the user running the web server is being run as!

## Continuous Deployment

GitHub Workflows described in `.github/workflows/` allows for automatic updates to the EF Server. To enable that, the following steps are necessary:

> Note to self: details to be found in `$storage/private/auth/github.com-eurofurence/deployment-setup.md`

* Initialize directory on EF server.

* Set up the following [Action Secrets and Variables](https://github.com/dogpixels/efXX/settings/secrets/actions):

| type     | name                  |
| ---      | ---                   |
| variable | `PROD_PATH`           |
| variable | `STAGE_PATH`          |
| secret   | `DEPLOY_KEY_WWW`      |
| secret   | `DEPLOY_KEY_WWWTEST`  |

Finally, when a stable routine has been established, enable auto-triggering the workflow for **production** by editing `.github/workflow/deploy_www.yml`:
```yml
on:
  push:
      branches: [main]
  workflow_dispatch:
```

> `on push branches` is triggered on pushes to the listed branch(es), while `workflow_dispatch` allows manual running of the workflow.

## Archival

* Create a new branch on [GitHub](https://github.com/eurofurence/website/branches), name it `archive/efxx`.
* In *www/pages/restaurants/ef-map.json*, change `sprite` and `glyphs` from `www.eurofurence.org` to `archive.eurofurence.org` (this change can be discarded later).
* In *config/core.json*, enable `staticOut.enabled` (this change can be discarded later).
* Run the local development environment: 
  ```
  docker compose up
  ```
* Open [localhost/?export](http://localhost/?export) in a browser and wait for the script to populate *www/_archive*.
* create an additional pages folder:
  ```
  mkdir www/_archive/pages
  ```
* copy all directories with special embeds into the pages folder:
  ```
  cp -r www/pages/home/ www/_archive/pages/home &&  cp -r www/pages/hotels/ www/_archive/pages/hotels &&  cp -r www/pages/restaurants/ www/_archive/pages/restaurants &&  cp -r www/pages/glympse/ www/_archive/pages/glympse
  ```
* Copy archive to EF Archive server:
  ```
  scp -r www/_archive ef:/home/ef-web/archive/EFxx
  ```

> TODO: The last few tasks covering pages with embeds *could* be automated with PHP.