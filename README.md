# Nette skeleton

A starting point for a Nette 3.3 web application on PHP 8.4, distilled from a finished project.
It has no application code beyond a welcome page and a tiny example feature, but everything around
the code is already set up and tested.

## What is included

- **Docker Compose development environment** with a VS Code Dev Container: PHP 8.4 with Apache,
  MySQL 8.4, Xdebug, the MySQL client, a separate test database `<MYSQL_DATABASE>_test`.
- **Configuration** in layers: `config/common.neon`, `services.neon`, and one `local.<env>.neon` per
  environment selected by `APP_ENV`. `.env` is the single source for the database settings in
  development (`%env.NAME%` works in NEON). `NETTE_DEBUG` switches debug mode on its own.
- **Database migrations**: numbered SQL files in `db/migrations` and the scripts `db/migrate`,
  `db/dump`, `db/restore`, `db/test-reset`.
- **Quality tools**: PHPStan level 8, a PSR-4 check (`composer run psr`), and PHPUnit 13 with
  `DatabaseTestCase` (transaction per test, rolled back, refuses non-`*_test` databases) and
  `PresenterTestCase` (run presenters, render complete pages, submit forms, fire signals).
- **Safe defaults**: `.htaccess` files that deny `temp/`, `log/`, the project root and script
  execution in `www/resources/`; `temp/` and `log/` tracked as empty folders.
- **Deployment to FTP hosting**: `config/deployment.example.ini` for
  [dg/ftp-deployment](https://github.com/dg/ftp-deployment).
- **Docs for people and agents**: this README, `AGENTS.md` (conventions for AI coding agents),
  `CHANGELOG.md` (Keep a Changelog), `LICENSE` (MIT).
- **One example feature** (`app/Model/Example`, the `example_items` migrations, `ExampleFacadeTest`)
  that shows the model conventions. Delete it when you have your own model.

## Starting a new project from this skeleton

Do these once, in this order. Nothing here is hidden elsewhere; this list is the complete set of
names and values that belong to "this skeleton" and must become "my project".

1. **Copy the folder**, delete any `.git` in it, then `git init` and commit the unchanged copy first.
2. **`composer.json`**: change `name`, `description`, `keywords` and `authors`. Then run
   `composer update --lock` to refresh the lock file hash (or `composer update` to also pick up new
   package versions).
3. **`LICENSE`**: set the year and the copyright holder, or replace the licence.
4. **`.env`**: `cp .env.example .env`, then choose `MYSQL_DATABASE`, `MYSQL_USER`,
   `MYSQL_PASSWORD` and `WEB_PORT` (default 8080, change it if another project already uses it).
   The test database is called `<MYSQL_DATABASE>_test` and is created automatically.
5. **`config/local.dev.neon`**: `cp config/local.example.neon config/local.dev.neon` (it reads `.env`).
6. **`.devcontainer/devcontainer.json`**: change `name`. The Docker Compose project name follows the
   folder name.
7. **Text and branding**: `<title>` in `app/Presentation/@layout.latte`, the welcome page
   `app/Presentation/Home/default.latte`, `www/favicon.ico`, and the English error pages in
   `app/Presentation/Error/Error4xx` (translate them if needed).
8. **Docs**: rewrite this README for the new project, reset `CHANGELOG.md`, adjust the first
   paragraph of `AGENTS.md`.
9. **Remove what you do not need**: the Example feature (see above), `config/deployment.example.ini`
   if you do not deploy by FTP, and in `docker/php/Dockerfile` the tools you will not use (`gd`,
   `intl`, `openssh-client`, `sudo`, `nano`) and the PHP extensions, then rebuild the container.
10. **Build and check** (inside the container):

    ```bash
    composer install        # first time; or composer update
    db/migrate
    composer run check && composer run test
    ```

    Open `http://localhost:<WEB_PORT>`.

Not part of the skeleton on purpose: authentication, an admin, an AJAX library (Naja), a file
manager, a front-end build. Add them in the project.

## Getting started (after the checklist)

Open the folder in VS Code and run **Dev Containers: Reopen in Container**. Without VS Code:

```bash
cp .env.example .env
cp config/local.example.neon config/local.dev.neon
docker compose up -d
docker compose exec -u dev web bash
```

Inside the container, as the `dev` user: `composer install`, `db/migrate`.

## Commands

| Command | What it does |
|---|---|
| `composer run test` | Run the PHPUnit suite |
| `composer run phpstan` | Static analysis, level 8, on `app/` and `tests/` |
| `composer run psr` | Every class matches its file path (PSR-4), no duplicates |
| `composer run check` | `psr` and `phpstan` |
| `composer run test:db` | Drop and recreate the test database from the migrations |
| `db/migrate` | Apply pending migrations to the development database |
| `db/dump [file]`, `db/restore <file>` | Dump / restore the development database |

VS Code tasks with the same names are in `.vscode/tasks.json`. Run commands as the `dev` user.

## Configuration

| File | In git | Used when |
|---|---|---|
| `config/common.neon`, `services.neon` | yes | always |
| `config/local.example.neon` | yes | template, not loaded |
| `config/local.dev.neon` | no | `APP_ENV=dev` (the dev container) |
| `config/local.test.neon` | yes | `APP_ENV=test` (PHPUnit); the throwaway test database |
| `config/local.prod.neon` | no | `APP_ENV=prod` (the default when the variable is unset) |

`Bootstrap` stops with a clear error when the file for the current `APP_ENV` is missing. Production
without environment variables (shared hosting) uses literal values in `local.prod.neon`.

## Tests

Tests run against `<MYSQL_DATABASE>_test`, built from the same migrations (`composer run test:db`).
Each test runs in a transaction that is rolled back. After changing the mock data in a migration,
rebuild the test database. The container has Xdebug on; the composer script switches it off for tests.

## Deployment

Only `www/` may be web-accessible. For Docker, mount or bake in `config/local.prod.neon` and leave
`APP_ENV` and `NETTE_DEBUG` unset. For FTP hosting use `config/deployment.example.ini`: copy it to
`config/deployment.ini` (git-ignored), deploy from a clean `git archive` export with
`composer install --no-dev --optimize-autoloader`, preview with `-t`, and apply new migrations by
hand. Never apply the mock-data migrations in production.

## Layout

```
app/                      Bootstrap, Core (router), Model (BaseFacade, BaseEntity, Example), Presentation
config/                   common.neon, services.neon, local.*.neon, deployment.example.ini
db/                       migrations/, dumps/ (git-ignored), migrate dump restore test-reset
docker/                   PHP/Apache image, MySQL init script
tests/                    bootstrap, DatabaseTestCase, PresenterTestCase, examples
www/                      Public document root (assets/, resources/ for uploads)
log/ temp/                Runtime directories (contents git-ignored)
```

## Licence

MIT, see `LICENSE`.
