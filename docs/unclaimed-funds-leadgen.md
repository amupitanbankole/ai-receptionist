# Unclaimed Funds Lead Generation Module

This module adds a compliance-first foundation to the existing Laravel CRM.

## Current scope

- Multi-state jurisdiction registry with finder and calling review statuses.
- Official property-source directory and public-record provenance.
- Prospect and property-record tables.
- Buyer directory and state-specific authorization records.
- Buyer-specific consent evidence, revocation timestamps and DNC suppressions.
- Calling eligibility decisions with reason codes and evidence snapshots.
- Campaign, call-attempt, appointment, lead-delivery and audit tables.
- A protected dashboard and prospect listing.

## Safety defaults

- Every new state's `calling_status` defaults to `blocked`.
- Every buyer defaults to inactive and unverified.
- Consent defaults to pending verification.
- Calling eligibility defaults to blocked.
- Campaigns default to draft.
- `LEADGEN_OUTBOUND_AI_CALLING_ENABLED` defaults to false.
- The UI is protected by an operator allowlist. Until configured, access is denied.
- Public government records are not treated as consent to AI telemarketing.
- A call eligibility decision is an evaluation only; it never starts a telephone call.

## Access configuration

Configure Laravel authentication first. Then add the authenticated operator user IDs to `.env`:

```dotenv
LEADGEN_ALLOWED_USER_IDS=1
LEADGEN_OUTBOUND_AI_CALLING_ENABLED=false
```

An empty allowlist denies access. Do not enable outbound calling merely by changing the environment variable. Obtain US legal review, configure consent collection, DNC screening, calling-hour restrictions, recording disclosures and state/buyer authorization verification first.

## Migrations

Run:

```bash
php artisan migrate
```

## Routes

- `/unclaimed-funds` — dashboard
- `/unclaimed-funds/prospects` — prospect research records

Both routes require the `leadgen.access` middleware.

## Important implementation boundary

The first release intentionally does not include public-record scraping, automated cold calling, phone-number enrichment, or lead resale. Add data ingestion only for sources whose terms permit the method used. Add contact acquisition only through legally reviewed consent flows. Share a prospect with a buyer only after the prospect has explicitly authorized that specific disclosure.
