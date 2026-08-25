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