# To My Agents!

It is my fervent wish that this file guide every AI coding agent working with code in this repository.

This is a **Nette 3.3 web project skeleton** with a Docker development environment, SQL migrations,
PHPUnit tests against a real test database, and PHPStan level 8. It contains one welcome page and one
tiny example feature; the application is built on top of it. `README.md` explains setup and the
checklist for starting a new project from this skeleton.

## First: equip your agent

Deep, up-to-date knowledge of the Nette ecosystem (conventions, APIs, gotchas) and automatic
validation of the files you write ship as a set of **skills**, not baked into this file.

**Install them before writing any real code.** If the developer has not done so yet, ask them
to run the steps below first.

### Claude Code

Add the Nette marketplace (with auto-update), then install the plugins:

```
/plugin marketplace add nette/claude-code
/plugin install nette@nette
```

Strongly recommended. Validates PHP, Latte, NEON, JSON and JS after every edit and reports
errors straight back to the agent:

```
/plugin install nette-lint@nette
```

Optional, for automatic PHP code-style fixing:

```
/plugin install php-fixer@nette
/install-php-fixer
```

Details and the full skill list: https://github.com/nette/claude-code

### Other agents

The plugins above are Claude Code specific. For any other agent, the authoritative source is the
official manual at https://doc.nette.org. Consult it for the area you are working on
(architecture, forms, database, Latte, NEON, Tracy) before editing, rather than relying on this
file. This file is a signpost, not the manual.

## Project at a glance

- **Backend:** PHP 8.4 (requires 8.2+), Nette 3.3, Latte 3
- **Database:** MySQL 8.4 through Nette Database Explorer. Schema and mock data are SQL files in
  `db/migrations/`.
- **Testing:** PHPUnit 13 (`tests/**/*Test.php`)
- **Static analysis:** PHPStan level 8 on `app` and `tests`
- **Environment:** Docker Compose and a VS Code Dev Container. PHP and MySQL are **not** installed
  on the host.

```
app/
├── Bootstrap.php              # Debug mode, config loading (config/local.$APP_ENV.neon)
├── Core/RouterFactory.php     # URL routing
├── Model/                     # BaseFacade, BaseEntity, one folder per feature (Example/ is a demo)
└── Presentation/
    ├── @layout.latte
    ├── Home/                  # the welcome page
    └── Error/                 # Error4xx (templates per status code) and Error5xx (standalone page)
config/
├── common.neon                # framework configuration, non-secret defaults
├── services.neon              # DI services + auto-discovery (*Facade, *Factory, …)
└── local.<dev|test|prod>.neon # per-environment values; dev and prod are git-ignored
db/                            # migrations/ plus the scripts migrate, dump, restore, test-reset
tests/                         # bootstrap + DatabaseTestCase, PresenterTestCase and examples
www/index.php                  # HTTP entry point (the only web-accessible folder)
```

## Essential commands

Run these **inside the dev container, as the `dev` user**:

```bash
docker compose exec -u dev web bash      # from the host, if you are not in the Dev Container

composer run test        # PHPUnit
composer run phpstan     # static analysis, level 8
composer run psr         # every class matches its file path, no duplicate classes
composer run check       # psr + phpstan
composer run test:db     # rebuild the test database (<MYSQL_DATABASE>_test) from the migrations

db/migrate               # apply pending migrations to the development database
```

Before you call a change done, `composer run check` and `composer run test` must both pass.

Do not run commands as root (`docker compose exec` without `-u dev`): the files and folders they
create (for example in `temp/` and `log/`) become unwritable for the `dev` user.

## Conventions

**Autoloading**
- One class per file, the file name equals the class name, and the namespace equals the path below
  `app/`. Nette's DI scan fails for a class that autoloading cannot find. There is no RobotLoader.

**Model**
- Facades extend `BaseFacade` and are **not `final`**, so tests can mock them. Entities are `final`,
  extend `BaseEntity` and implement `fromActiveRow(): static`.
- `BaseEntity::fromSelection()` returns a **list**, not an array keyed by id. Never use array keys as
  database ids.
- Entity ids are `?int` (null until saved). Check for `null` before using a looked-up entity.

**Presenters and templates**
- Concrete presenters use constructor injection; shared dependencies of a base presenter use
  `#[Inject]` properties.
- State set in an `action*()` method goes into a non-nullable, uninitialized property. The action
  redirects when the record is missing; `redirect()` never returns.
- Links from controls and components to presenters use `{plink}`, not `n:href`.
- Signals (`handleXxx`) and forms require a same-origin request (Nette 3.3). Anything destructive
  must check that the record exists.
- AJAX: use Naja (add it yourself), mark links/forms with `class="ajax"` and wrap the redrawn part
  in a `{snippet}`.

**Configuration**
- No secrets in `common.neon`. Environment-specific values go in `config/local.<env>.neon`.
- `APP_ENV` selects the file (`dev`, `test`, `prod`; default `prod`). `NETTE_DEBUG=1` turns on debug
  mode. They are independent; never derive one from the other.
- Environment variables are available in NEON as `%env.NAME%`. In development `.env` is the single
  source of truth for the database settings; `.env` is git-ignored, `.env.example` is its template.
  `local.prod.neon` uses literal values because shared hosting cannot set environment variables.

**Database**
- Change the schema or the mock data with a **new** numbered migration. Never edit an applied one.
  Do not let an editor auto-format the SQL files in `db/migrations/`.
- Tests use the separate `<MYSQL_DATABASE>_test` database. Never point tests at the development database.

**Uploads**
- If you add uploads, they go below `www/resources`, which is web-accessible: never store a file with
  an extension taken from the client unless it is on an allowlist (images get their real extension
  from Nette), and keep `www/resources/.htaccess` (denies script execution). Add a test that a `.php`
  file is refused.

**Changelog**
- Record every user-visible change, fix or security fix in `CHANGELOG.md` under `## [Unreleased]`
  (sections Added, Changed, Deprecated, Removed, Fixed, Security). On release, rename the section to
  the version and date and add a fresh empty `[Unreleased]`.

**Code style**
- PHP: 4 spaces. NEON: tabs. Both are set in `.editorconfig`.
- Tracy must stay off in production.

## Tests

- Any behaviour change needs a test. Run `composer run test` and make sure a new test **fails** when
  the code under test is broken.
- Base classes: `Tests\DatabaseTestCase` wraps each test in a rolled-back transaction and refuses to
  run when the database name does not end in `_test`. `Tests\PresenterTestCase` adds
  `runPresenter()`, `renderPage()`, `submitForm()` and `signal()`.
- Forms only accept same-origin submissions; `tests/bootstrap.php` sets `Sec-Fetch-Site: same-origin`
  for that reason.
- Many assertions depend on the mock data in the migrations. After changing it, run `composer run test:db`.
- PHPUnit 13 specifics: use `expectExceptionMessageIs()` (the plain `expectExceptionMessage()` is
  deprecated), and do not call `with()` on a mock method without `expects()`.

## Ground rules

- **Directories `app/`, `config/`, `log/`, `temp/` must never be web-accessible.**
  Only `www/` is the document root. See https://nette.org/security-warning.
- Add Nette packages as you need them: `composer require nette/<package>`
  (catalog at https://nette.org/packages).
- Check `vendor/` before trusting a snippet from the web: for example `Route::ONE_WAY` (not `OneWay`)
  in the installed `nette/routing`.

When in doubt about a Nette convention, defer to the installed skills. They are the source of
truth, and they are kept current in a way this file is not.
