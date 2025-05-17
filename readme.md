# Eurofurence Website

Version 4.2, last updated: 2024-02-21

## Requirements

either
* docker, or
* Apache Web Server with PHP 7.4 + modrewrite

## Setup

* Navigate a cli to the root directory and run `docker compose up`, or
* deploy all files from `www` to `/var/html` to be served through Apache Web Server.

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