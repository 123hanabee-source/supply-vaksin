# SupplyVaksin — Laravel backend, rebuilt

## What changed
The zip you uploaded had two incompatible things layered on top of each other:
a full Laravel skeleton, plus a separate set of plain-PHP "API" scripts dropped
into `app/Models/` that `require_once`'d files/classes that didn't exist in
that form (`config/database.php` as a `Database` class, `controllers/...` as
a folder). None of it was reachable through Laravel's router, since
`routes/web.php` only had the default welcome route.

This version is restructured as a normal Laravel API:

- **`database/migrations/`** — one migration per table (suppliers, facilities,
  vaccines, distribution, stock, users), in dependency order, matching your
  schema exactly (including the `role` CHECK constraint).
- **`database/seeders/DatabaseSeeder.php`** — your seed data, inserted in the
  correct parent-before-child order.
- **`app/Models/`** — Eloquent models (`Supplier`, `Facility`, `Vaccine`,
  `Distribution`, `Stock`, `User`) with the right primary keys and relationships.
- **`app/Http/Controllers/`** — your original controller logic, kept almost
  line-for-line, but now namespaced `App\Http\Controllers`, using Eloquent /
  the query builder instead of raw PDO, and returning `response()->json()`.
- **`app/Http/Middleware/RequireLogin.php` / `RequireAdmin.php`** — replace the
  old `includes/auth.php` functions.
- **`routes/api.php`** — every endpoint, registered properly (`Route::apiResource`
  for the CRUD ones, plus `/login`, `/logout`, `/me`, `/dashboard`,
  `/distribution/request`).
- **`bootstrap/app.php`** — registers `routes/api.php`, and adds session
  middleware to the `api` group (your auth is cookie/session-based, so the API
  needs a session even though it's JSON).
- **`config/cors.php`** — new file, `supports_credentials => true` so the
  session cookie survives a cross-origin request from `vaccine.html`.

Old `includes/` and the broken `app/Models/*.php` endpoint scripts were removed.

## Locked-in setup: same-origin via Laragon
After some trial and error, the setup is: `vaccine.html` lives in `public/`
and is served by the same Laragon vhost as the API (e.g.
`http://supplyvaksin.test/vaccine.html`, API at `http://supplyvaksin.test/api/...`).
No second server, no CORS juggling, no `file://` cookie problems.

`config/cors.php` and `.env` session settings are set back to same-origin
defaults (`SESSION_SAME_SITE=lax`, no special CORS origins needed beyond
`http://localhost` / `http://supplyvaksin.test`).

## To run it in Laragon

1. Create the Postgres database named `SupplyVaksin` (matches your `.env`).
2. `composer install` if `vendor/` isn't already populated (it is, in this zip).
3. `php artisan migrate:fresh --seed`
4. Visit `http://supplyvaksin.test/vaccine.html`.
5. Log in with `admin / admin123 / admin` or `client / client123 / client`.

## What's wired up in vaccine.html
- Real login/logout/session-restore against `/api/login`, `/api/logout`, `/api/me`.
- Dashboard counts and the expiry table, loaded from `/api/dashboard` and `/api/vaccines`.
- Vaccines, Suppliers, Facilities, Distribution, Stock, and (admin) Users
  panels render live data, lazy-loaded the first time you open each panel.
- The "My Facility" panel (client role) shows the logged-in client's own
  facility info, stock, and distributions.
- **Full CRUD (admin only):** every "＋ Add ..." button now opens a modal form
  that POSTs to the matching `/api/...` endpoint. Each table row has Edit /
  Delete buttons (admin only — clients see "—" instead) that PUT/DELETE the
  same way. After any save/delete, that panel's data and the dashboard
  counts refresh automatically.

## Known limitations
- The generic modal/edit-button rendering embeds row data into an inline
  `onclick` attribute. If a name/contact field ever contains a single-quote
  character (e.g. `O'Brien`), that row's Edit button would break — fine for
  the current seed data, but worth knowing if you add real-world data later.
- No client-side restock-request UI yet for the `/api/distribution/request`
  endpoint (clients can view distributions but not request more) — the
  backend supports it, just not wired into the UI.
- Passwords remain plaintext, matching your original schema/seed data.

## Known intentional simplifications (carried over from your design)
- Passwords are stored and compared in plaintext, matching your seed data —
  flagged in the code with a comment where you'd swap in `Hash::make()` /
  `Hash::check()`.
- Restock "requests" from clients are stored as `distribution` rows with
  `distribution_date = NULL`, same as your original design.
