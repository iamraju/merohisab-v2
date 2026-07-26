# MeroHisab — Family Income & Expense Tracker

## Context for Copilot

You are helping build **MeroHisab** ("My Transactions" / "मेरो हिसाब" in Nepali), a web app for families to track income and expenses across multiple earning members and multiple expense categories. Build this as a production-quality Laravel application, not a prototype. Follow Laravel and Vue best practices at every step, write clean, typed, tested code, and explain non-obvious decisions in comments.

## Tech Stack (fixed — do not substitute)

- Backend: Laravel (latest stable)
- Bridge: Inertia.js
- Frontend: Vue 3 (Composition API, `<script setup>`)
- Styling: Tailwind CSS
- Database: MySQL (dev) / PostgreSQL-compatible schema (avoid MySQL-only syntax in migrations)
- Auth: Laravel Breeze (Inertia + Vue starter kit) as the base, extended for roles
- PWA: `vite-plugin-pwa` for installability, offline shell, and manifest/icons

## Roles & Permissions

Use a simple `role` enum column on `users` (`super_admin`, `customer`) or a lightweight package like `spatie/laravel-permission` if role logic grows. Enforce authorization with Laravel Policies/Gates — never check roles inline in controllers.

**Super Admin**

- Full CRUD on income/expense titles (the global/master list)
- Views list of all registered customers and their profile fields (name, email, phone, status) — **must never see any amount, transaction, or report data**; this is a hard business rule, enforce it at the query/policy level, not just hidden in the UI
- Can update customers' basic info and force a password reset
- Cannot create income/expense entries

**Customer**

- Self-registers (signup), signs in, resets own password
- Adds a new income/expense title only if it doesn't already exist (case-insensitive unique check across the shared/global title list); newly added titles become available to all customers and are tagged with `created_by_user_id`
- Cannot edit or delete any title (admin-managed or peer-created)
- Creates income/expense entries against titles
- Views dashboard with charts and a filterable report (day/week/month/year/custom range)

## Data Model (starting point — refine as needed)

```
users
  id, name, email, phone, password, role (enum: super_admin, customer), status, timestamps

titles
  id, name, type (enum: income, expense), created_by_user_id (nullable, null = admin-created),
  unique index on (lower(name), type), timestamps

transactions
  id, user_id, title_id, type (enum: income, expense), amount (decimal 12,2),
  occurred_at (datetime), remarks (text, nullable), timestamps
  index on (user_id, occurred_at), index on (title_id)
```

Use `decimal` (never float) for `amount`. Wrap title-creation-or-reuse logic in a DB transaction to avoid race-condition duplicates under concurrent requests.

## Pages / Routes

1. `/register` — signup
2. `/login` — default landing route for guests
3. `/forgot-password`, `/reset-password/{token}`
4. `/dashboard` — sidebar layout, top bar with notifications; summary cards + charts (income vs expense trend, breakdown by title) for the logged-in customer
5. `/titles` — list/table + form (create-if-not-exists flow), separate admin view with full CRUD
6. `/transactions/create` — single, very friendly entry form (see UX notes below)
7. `/reports` — filterable by date range (day/week/month/year/custom), by title, and by type, with export-friendly table + chart
8. Admin-only: `/admin/customers` — list, view basic info, reset password (no amounts)

## UX Requirements for the Entry Form

- One screen, minimal taps: type toggle (income/expense) first, since it filters which titles show
- Title field: searchable/combobox showing existing titles for that type, with an inline "add new" option that runs the duplicate check before creating
- Amount field: numeric keypad on mobile, large touch target
- Date/time defaults to "now" but editable
- Remarks optional, collapsed by default
- Instant validation, optimistic UI feedback, works well one-handed on a phone

## Non-Functional Requirements

- Responsive from small phones to desktop (test at 360px, 768px, 1280px+)
- Secure auth: hashed passwords, rate-limited login, CSRF via Inertia defaults, policy-enforced authorization on every route
- Installable PWA: manifest, service worker, app icons, offline fallback page
- Accessible: proper labels, focus states, sufficient color contrast, keyboard-navigable forms

## AI-Assisted Analysis (realistic scope)

Laravel itself does not ship a built-in "AI framework" — implement this as a standard integration layer instead, so scope it as:

- A dedicated `AiInsightService` that sends a summarized (not raw-dump) payload of a user's recent transactions to an LLM provider (OpenAI, Anthropic, or Gemini — keep the provider swappable behind an interface, since multiple providers have been used before in similar projects)
- Queue these calls as Laravel jobs (never block the request cycle) and cache results per user/period
- Practical features to target first:
  1. Plain-language monthly summary ("You spent 18% more on groceries than last month")
  2. Anomaly flagging (unusually large or duplicate-looking entries)
  3. Simple forecasting of next month's likely expenses by title, based on historical averages
  4. A natural-language query box ("How much did I spend on fees last quarter?") that translates to a scoped, parameterized query — never let the LLM generate raw SQL against the database directly
- Keep this behind a feature flag/config so it can be developed and tested independently of core CRUD

## Delivery Approach — ask Copilot to work in this order

1. Scaffold Laravel + Breeze (Inertia/Vue) + Tailwind, confirm auth flow works end-to-end
2. Migrations + models + factories/seeders for the schema above
3. Policies for the super-admin/customer split, with tests proving amounts are unreachable by admin
4. Titles CRUD + create-if-not-exists flow with the uniqueness check
5. Transaction entry form (mobile-first)
6. Dashboard charts and the filterable reports page
7. PWA setup (manifest, service worker, install prompt)
8. AI insight service behind a feature flag, queued jobs, caching
9. Write Pest/PHPUnit feature tests for each module as it's built, not at the end

## Definition of Done (per feature)

- Migration + model + policy + feature test committed together
- No amount/financial data ever returned to a super_admin-scoped query or endpoint
- Mobile viewport checked manually or via a quick screenshot
- No raw SQL string concatenation anywhere the AI query feature is involved
