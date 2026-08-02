# Eurofurence Website

Version 4.3, last updated: 2026-08-01

## Requirements

either
* docker, or
* Apache Web Server with PHP 7.4 + modrewrite

## Setup

* Navigate a cli to the root directory and run `docker compose up`, or
* deploy all files from `www` to `/var/html` to be served through Apache Web Server.

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

## Static Site Generation

* To use the static site generation feature, toggle the `staticOut.enabled` option in core.config.json.
* To automate an export of all pages, call any page with [?export](http://localhost/?export) attached to the url.
* The static html output will be saved to the path configured under `staticOut.path`.