# Changelog

All notable changes to this project are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the project uses
[Semantic Versioning](https://semver.org/).

## [Unreleased]

First version of the application: the legacy PHP project rewritten for Nette 3.3 on PHP 8.4.

### Added

- **Public part:** home page, rental offices with their employees, terms and conditions, FAQ and contact
  pages.
- **Vehicle catalog** with a filter (rental office, fuel, color, price per day and per kilometer),
  sorting and paging, and a vehicle detail with the list of active reservations.
- **Customer accounts:** registration with strong password rules, sign in with an optional "stay signed
  in", sign out, profile editing, password change, and the list of the customer's own rentals
  ("Moje půjčky", history included).
- **Reservations** of a vehicle by a signed-in customer.
- **Administration** (`/admin`) with a separate employee sign in and session: lists of rentals,
  vehicles, rental offices and customers, an employee profile with password change, and a help page.
- **Datagrid component** used by all administration lists: sortable columns, paging, page size,
  link, e-mail and color formatters, row actions with confirmation, and bulk actions.
- Placeholder pages for add, edit and delete in every administration list (they only show a notice
  that the function is not implemented yet).
- **Database:** migrations for the schema and for mock data (rental offices, employees, customers,
  vehicles with prices, rentals) and the scripts `db/migrate`, `db/reset`, `db/dump`, `db/restore` and
  `db/test-reset`.
- **Development environment:** Docker Compose with a VS Code Dev Container, PHPStan level 8, a PSR-4
  check, and an FTP deployment example.
- **Tests:** more than 240 PHPUnit tests against a real test database: repositories, mappers,
  facades, authentication, sign in and up, profiles, the catalog filter, reservations, the datagrid
  and access control, with helpers for signed-in users that need no PHP session.
- Screenshots and a rewritten README.

### Changed

- The application is rebuilt on Nette 3.3, Latte 3 and Nette Database (formerly hand-written
  controllers, models and views), with a `Front` and an `Admin` module and layered model classes
  (entity, mapper, repository, facade).
- The database uses `snake_case` table and column names, `utf8mb4`, ordered foreign keys, and unique
  e-mail addresses for customers and employees. The former "loans" are called car rentals.
- The front end uses Bootstrap 3.4.1, jQuery 3.7.1 and Naja served from the project itself, instead
  of third-party CDNs.

### Fixed

- A catalog filter without matches no longer redirects in a loop.
- The reservation form no longer fails on every submission.
- Profile forms no longer report success when the save failed (for example a duplicate e-mail), and
  the administration profile requires a rental office.
- Times in reservation lists are shown as hours and minutes (not the 12-hour clock with seconds).
- The links in the menu lead to existing pages.

### Security

- Passwords are stored only as bcrypt hashes; registration and password change require at least 10
  characters with lower case, upper case, a digit and a special character.
- Delete and sign out accept only a same-origin POST request.
- A login that has expired (for example a "stay signed in" session after 7 days) can no longer create
  reservations.
- All output is escaped by Latte, the datagrid included; sort options are enums mapped to fixed SQL.
- Customer and employee e-mail addresses are unique, so a login name cannot be shared.
- Front-end libraries are no longer loaded from third-party CDNs, and the old Bootstrap 3.1.1 and
  jQuery 1.11 are replaced by the maintained 3.4.1 and 3.7.1.
- The development web port is bound to `127.0.0.1`, so Tracy debug pages are not exposed to the
  local network.
