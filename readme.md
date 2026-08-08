# Eurofurence Website

Version 4.2, last updated: 2024-02-21

## Requirements

either
* docker, or
* Apache Web Server with PHP 7.4 + modrewrite

## Setup

* Navigate a cli to the root directory and run `docker compose up`, or
* deploy all files from `www` to `/var/html` to be served through Apache Web Server.

> To run mock enviroment use `docker compose -f docker-compose.mock.yml up` instead of `docker compose up`

## SCSS Support

Docker starts an additional `scss-watch` service that compiles SCSS changes automatically.
The watcher uses standard file change events and rebuilds on SCSS add/change/remove (fallbacks to polling mode for local docker container).

* Place SCSS sources anywhere in `www/`.
* Output CSS behavior:
  * All compiled files are saved in `www/css/`.
  * Files in `www/scss/` compile to `www/css/` directly.
  * Files elsewhere in `www/` compile to mirrored subpaths in `www/css/`.
* [package.json](package.json) contains some helper scripts to make sure there are no `.scss` references:
  * `npm run style:check` fails if a runtime file still points at `.scss`.
  * `npm run style:fix` rewrites obvious `.scss` references to `.css` in runtime files.
  * `npm run style:ensure` checks, tries to fix, and then re-checks (useful for continuous deploy)

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