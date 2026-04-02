# StudAI Career — Gap Closure Sprint Plan

> **Purpose:** Track every item needed to go from 70% → 100% launch-ready
> **How to use:** Work top-to-bottom within each tier. Check off items as you complete them.
> **Created:** April 1, 2026

---

## Status Summary

| Tier | Items | Effort | Status |
|------|-------|--------|--------|
| **Tier 1** — Blocks Launch | 6 items | ~3-4 weeks | ⬜ Not Started |
| **Tier 2** — Breaks Trust | 6 items | ~2 weeks | ⬜ Not Started |
| **Tier 3** — Credibility | 7 items | ~3-4 weeks | ⬜ Not Started |
| **TOTAL** | **19 items** | **~8-10 weeks** | |

---

## TIER 1: Blocks Launch

> These items MUST be fixed before any real user touches the platform.
> Work these first, in order.

---

### T1-01: Security Hardening — Rotate All Secrets
- **Priority:** P0 — Do First
- **Effort:** 1 day
- **Status:** ⬜ Not Started

**Problem:**
The `.env` file contains real Azure OpenAI API keys, database credentials (`studai2025`), and the APP_KEY on disk. `SuperAdminSeeder.php` creates `admin@studai.com` with password `password`. `APP_DEBUG=true` exposes stack traces to users.

**Files to change:**
- [ ] `.env` — Rotate `AZURE_OPENAI_API_KEY` (line 1)
- [ ] `.env` — Rotate `AZURE_ANTHROPIC_API_KEY` (line 5)
- [ ] `.env` — Change `DB_PASSWORD` from `studai2025` to strong generated password (line 35)
- [ ] `.env` — Change `DB_PASSWORD_ANALYTICS` similarly (line 43)
- [ ] `.env` — Set `APP_DEBUG=false` (line 11)
- [ ] `database/seeders/SuperAdminSeeder.php` — Change default admin password, add comment to change after first login
- [ ] Verify `.env` is NOT in git: `git ls-files .env` should return nothing ✅ (already confirmed)
- [ ] Remove `.env.production` from git tracking: `git rm --cached .env.production`
- [ ] Consider Azure Key Vault for production secrets

**Verification:**
```bash
# Confirm .env not tracked
git ls-files .env

# Confirm debug is off
grep "APP_DEBUG" .env

# Confirm no hardcoded "password" in seeders
grep -r '"password"' database/seeders/
```

**Done when:** All keys rotated, debug off, no hardcoded credentials.

---

### T1-02: Email Delivery — Configure Real Mail Provider
- **Priority:** P0
- **Effort:** 3 days
- **Status:** ⬜ Not Started

**Problem:**
`MAIL_MAILER=log` means every email goes to `storage/logs/`. Users won't receive: verification emails, application status updates, payment receipts, password resets, or any of the 21 notification classes.

**Files to change:**
- [ ] `.env` — Set `MAIL_MAILER=smtp` (or `ses`/`mailgun`/`resend`)
- [ ] `.env` — Configure `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
- [ ] `.env` — Set `MAIL_FROM_ADDRESS` and `MAIL_FROM_NAME`
- [ ] `config/mail.php` — Verify mailer configuration

**Recommended provider:** Amazon SES (cheapest at scale for India, $0.10/1000 emails)

**Test these notification paths:**
- [ ] Registration → email verification link arrives
- [ ] Password reset → reset link arrives
- [ ] Application submitted → confirmation email arrives
- [ ] Application status changed → notification email arrives
- [ ] Payment receipt → confirmation arrives
- [ ] Subscription activated → welcome email arrives

**Verification:**
```bash
php artisan tinker
# Send test email
Mail::raw('Test email', function ($m) { $m->to('your@email.com')->subject('Test'); });
```

**Done when:** All 6 notification paths above deliver real emails.

---

### T1-03: Switch to Redis Drivers
- **Priority:** P0
- **Effort:** 2 hours
- **Status:** ⬜ Not Started

**Problem:**
`SESSION_DRIVER=file` and `CACHE_STORE=file` don't scale, break with multiple server instances, and lose sessions on deploy. Redis is already configured in `.env` but not used.

**Files to change:**
- [ ] `.env` — Change `SESSION_DRIVER=file` → `SESSION_DRIVER=redis` (line 45)
- [ ] `.env` — Change `CACHE_STORE=file` → `CACHE_STORE=redis` (line 55)
- [ ] Verify Redis connection works: `php artisan tinker` → `Cache::put('test', 'works', 60); Cache::get('test');`

**Prerequisite:** Redis server must be running. Check with:
```bash
redis-cli ping
# Should return: PONG
```

**Done when:** Sessions and cache use Redis. Verified by logging in, then checking `redis-cli KEYS *` shows session data.

---

### T1-04: Real Job Data — Integrate Job Source
- **Priority:** P0
- **Effort:** 2 weeks
- **Status:** ⬜ Not Started

**Problem:**
The LinkedIn/Indeed/Glassdoor scrapers are empty stubs. Only 3 RSS feeds work (RemoteOK, WeWorkRemotely, Jobicy) — all remote/international, not India-focused. The platform's core value proposition (find and apply to jobs) cannot function.

**Options (pick one or combine):**

| Source | Pros | Cons | Effort |
|--------|------|------|--------|
| **Indeed Publisher API** | Large Indian job inventory, pay-per-click revenue share | Requires application/approval | 1 week after approval |
| **Google Jobs API** | Comprehensive, structured data | Limited free tier | 1 week |
| **Naukri RSS / Scraping** | India's #1 job site | Legal grey area, fragile | 3-5 days |
| **Direct employer posting** | Create your own supply, no dependency | Zero jobs at launch | 1 week to build |
| **Manual curation** | Control quality, bootstrap with 100-500 real jobs | Doesn't scale | Ongoing |

**Recommended approach:**
1. **Immediate:** Add employer self-service job posting (enables organic supply)
2. **Short-term:** Apply for Indeed Publisher API (best India coverage)
3. **Keep:** RSS feeds from RemoteOK/WeWorkRemotely/Jobicy for remote jobs

**Files to change:**
- [ ] `app/Services/Agent/RSSJobFeedService.php` — Add more India-focused RSS feeds
- [ ] Create `app/Services/Agent/IndeedApiService.php` — Indeed Publisher API integration
- [ ] `app/Services/Agent/JobAggregationService.php` — Register new sources
- [ ] Create employer job posting controller and views (if not already complete)
- [ ] Delete deprecated stubs: `LinkedInScraperService.php`, `IndeedScraperService.php`, `GlassdoorScraperService.php`

**Verification:**
```bash
php artisan tinker
# Check job count
App\Models\Job::count(); // Should be > 100
App\Models\Job::where('created_at', '>', now()->subDay())->count(); // Recent jobs
```

**Done when:** Platform has 500+ real, current Indian job listings from at least 2 sources.

---

### T1-05: Payment End-to-End Testing
- **Priority:** P0
- **Effort:** 3-5 days
- **Status:** ⬜ Not Started

**Problem:**
Razorpay, PayU, and Stripe integration code exists but there's no evidence of a successful test transaction. Webhook signature verification, subscription activation, and grace periods need real testing.

**Test with Razorpay Sandbox:**
- [ ] Set Razorpay test keys in `.env` (`RAZORPAY_KEY_ID`, `RAZORPAY_KEY_SECRET`)
- [ ] Initiate payment → Razorpay checkout opens
- [ ] Complete test payment → Webhook callback fires
- [ ] Verify `PaymentTransaction` status = `success`
- [ ] Verify `UserSubscription` created/activated
- [ ] Verify Pro/Enterprise features unlock
- [ ] Test failure: declined card → graceful error message
- [ ] Test failure: duplicate webhook → idempotency works
- [ ] Test refund flow

**Test with PayU Sandbox:**
- [ ] Set PayU test keys (`PAYU_MERCHANT_KEY`, `PAYU_MERCHANT_SALT`)
- [ ] Same flow as Razorpay above

**Test with Stripe (if targeting global):**
- [ ] Set Stripe test keys
- [ ] Same flow as above

**Files involved:**
- `app/Services/Payment/PaymentGatewayService.php`
- `app/Http/Controllers/API/PaymentController.php`
- `routes/api.php` (payment routes)
- Webhook endpoint: `POST /api/webhooks/razorpay`

**Done when:** Full payment cycle works in sandbox for at least Razorpay. Screenshot/recording as evidence.

---

### T1-06: AI Predictions — Add Disclaimers
- **Priority:** P1
- **Effort:** 3-5 days
- **Status:** ⬜ Not Started

**Problem:**
S.C.O.U.T. features like "Success Probability: 87%", "Tenure Forecast: 3.2 years", "Flight Risk: 12%" are GPT prompt responses formatted as numerical scores — not trained ML models. If an employer makes a hiring decision based on a hallucinated score, that's a legal liability.

**Changes needed:**
- [ ] Add disclaimer banner to all S.C.O.U.T. prediction views: *"AI-generated insights for informational purposes only. Not a statistical prediction. Should not be the sole basis for hiring decisions."*
- [ ] Rename "Predictive Analytics" to "AI Hiring Insights" in UI and navigation
- [ ] Change numerical scores to qualitative bands where possible: "High / Medium / Low" instead of "87%"
- [ ] Add "How this works" info tooltip explaining it's AI-generated, not ML-trained
- [ ] Review `app/Services/Scout/PredictiveAnalyticsService.php` — add confidence caveat to all AI prompts

**Files to change:**
- [ ] `resources/views/scout/` — All prediction-related views (add disclaimer component)
- [ ] `resources/views/components/` — Create `<x-ai-disclaimer>` reusable component
- [ ] Navigation labels in Filament resources and web routes
- [ ] `app/Services/Scout/PredictiveAnalyticsService.php`
- [ ] `app/Services/Scout/SuccessPredictorService.php`

**Done when:** Every AI-generated score/prediction has a visible disclaimer. No raw percentage scores without context.

---

## TIER 2: Breaks Trust

> Users will leave without these. Fix after Tier 1.

---

### T2-01: Resolve Dual Brand Identity
- **Priority:** P1
- **Effort:** 2-3 days
- **Status:** ⬜ Not Started

**Problem:**
Every marketing page exists twice — "StudAI Career" and "StudAI Path" with different HTML/styling. `landing.blade.php` is standalone HTML bypassing the Blade layout system entirely.

**Decision needed:** Which brand name? _____________ (fill in before starting)

**Files to delete (assuming keeping "StudAI Career"):**
- [ ] `resources/views/pages/about-studai-path.blade.php`
- [ ] `resources/views/pages/features-studai-path.blade.php`
- [ ] `resources/views/pages/pricing-studai-path.blade.php`
- [ ] `resources/views/pages/how-it-works-studai-path.blade.php`
- [ ] `resources/views/pages/blog-studai-path.blade.php`
- [ ] `resources/views/pages/contact-studai-path.blade.php`
- [ ] `resources/views/pages/employers-studai-path.blade.php`
- [ ] `resources/views/pages/privacy-studai-path.blade.php`
- [ ] `resources/views/pages/terms-studai-path.blade.php`
- [ ] `resources/views/pages/refund-policy-studai-path.blade.php`

**Files to fix:**
- [ ] `resources/views/pages/landing.blade.php` — Convert from standalone HTML to Blade layout (`@extends('layouts.app')` or `<x-layouts.marketing>`)
- [ ] Update any route definitions pointing to deleted views
- [ ] Global search for "StudAI Path" → replace with chosen brand name

**Done when:** One brand name everywhere. No standalone HTML pages. No duplicate marketing pages.

---

### T2-02: Currency Standardization
- **Priority:** P1
- **Effort:** 1-2 days
- **Status:** ⬜ Not Started

**Problem:**
Market intelligence and salary views use ₹ (INR). Company salaries, analytics, and offer letters use $ (USD). Mixed currency within the same user journey.

**Fix:**
- [ ] Create helper function in `app/Helpers/helpers.php`:
  ```php
  function format_currency(float $amount, string $currency = 'INR'): string
  {
      return match($currency) {
          'INR' => '₹' . number_format($amount),
          'USD' => '$' . number_format($amount, 2),
          default => $currency . ' ' . number_format($amount, 2),
      };
  }
  ```
- [ ] Search all views for `$` currency symbols and replace with `format_currency()` or `₹`
- [ ] Check: `resources/views/analytics/`, `resources/views/companies/`, `resources/views/offer-letters/`, `resources/views/marketplace/`

**Verification:**
```bash
# Find all dollar signs in Blade files that look like currency
grep -rn '\$[0-9]' resources/views/
```

**Done when:** All user-facing amounts show ₹ for Indian market.

---

### T2-03: Onboarding Flow End-to-End Verification
- **Priority:** P1
- **Effort:** 2-3 days
- **Status:** ⬜ Not Started

**Problem:**
The profile wizard (6-step Livewire component) exists but the full registration → dashboard flow has not been verified end-to-end.

**Test the complete flow:**
- [ ] Visit landing page → Click "Get Started"
- [ ] Registration form → Submit → Check redirect
- [ ] Email verification → Click link → Check redirect
- [ ] 2FA setup (optional) → Skip or configure
- [ ] Profile wizard step 1: Resume upload (drag-drop) → AI parsing triggers
- [ ] Profile wizard step 2: Basic info auto-filled from resume
- [ ] Profile wizard step 3: Experience
- [ ] Profile wizard step 4: Education
- [ ] Profile wizard step 5: Skills
- [ ] Profile wizard step 6: Finish → Redirect to dashboard
- [ ] Dashboard loads with relevant content (even for sparse profile)
- [ ] First job search returns results
- [ ] Save a job → Appears in saved list
- [ ] Apply to a job → Application created

**Fix any broken transitions. Common issues:**
- Missing route definitions
- Middleware redirect loops
- Livewire component hydration failures
- Missing `$fillable` in models
- Missing relationship methods

**Done when:** A new user can go from zero to applied-for-a-job without encountering any error.

---

### T2-04: Meilisearch Provision & Index
- **Priority:** P1
- **Effort:** 1-2 days
- **Status:** ⬜ Not Started

**Problem:**
Laravel Scout is configured with Meilisearch driver in `config/scout.php`, but there's no evidence Meilisearch is running or any data is indexed.

**Steps:**
- [ ] Provision Meilisearch instance (local for dev, Azure VM or Meilisearch Cloud for prod)
- [ ] Set `MEILISEARCH_HOST` and `MEILISEARCH_KEY` in `.env`
- [ ] Run `php artisan scout:import "App\Models\Job"` (and any other searchable models)
- [ ] Test search: `php artisan tinker` → `App\Models\Job::search('react developer')->get()`
- [ ] Verify search relevance in the UI
- [ ] Add database fallback for when Meilisearch is unavailable

**Done when:** Job search returns relevant results via Meilisearch. Fallback works when Meilisearch is down.

---

### T2-05: Queue Workers (Laravel Horizon)
- **Priority:** P1
- **Effort:** 2-3 days
- **Status:** ⬜ Not Started

**Problem:**
34 background jobs are defined but `QUEUE_CONNECTION=database` with no Horizon. No visibility into job failures, no priority management, no monitoring.

**Steps:**
- [ ] Install Horizon if not in `composer.json`: `composer require laravel/horizon`
- [ ] Publish config: `php artisan horizon:install`
- [ ] Configure queue priorities in `config/horizon.php` (high, default, low, ai, search)
- [ ] Deploy Horizon on Azure workers app (`studai-workers-prod`)
- [ ] Test critical jobs execute:
  - [ ] `GenerateJobEmbeddings` — job embeddings created
  - [ ] `AnalyzeSkillGapsJob` — skill gaps populated
  - [ ] `ProcessAutoApplications` — auto-apply works (with safety guardrails)
  - [ ] `GenerateAssessmentJob` — assessments generated
- [ ] Verify Horizon dashboard accessible at `/horizon`

**Done when:** Horizon running. Failed jobs visible in dashboard. Critical jobs execute within 30 seconds.

---

### T2-06: Blade Template Anti-Patterns
- **Priority:** P2
- **Effort:** 1-2 days
- **Status:** ⬜ Not Started

**Problem:**
Direct model query in Blade, complex inline JavaScript, mixed template patterns.

**Fixes:**
- [ ] `resources/views/livewire/network/connection-manager.blade.php` — Move `\App\Models\User::find()` to the Livewire component PHP class
- [ ] `resources/views/livewire/video-interview/video-recorder.blade.php` — Extract ~180 lines of inline JS to `resources/js/video-recorder.js`, import via Vite
- [ ] Audit other Livewire views for direct model queries
- [ ] Remove `data-aos` attributes if AOS library is not consistently loaded, OR add AOS to Vite build

**Done when:** No direct model queries in Blade files. No >50-line inline JavaScript blocks.

---

## TIER 3: Credibility

> Needed before showing investors or onboarding pilot customers.

---

### T3-01: Integration Tests for Critical Paths
- **Priority:** P1
- **Effort:** 1-2 weeks
- **Status:** ⬜ Not Started

**Problem:**
65+ test files exist but likely <30% coverage. No integration tests for critical user journeys.

**Write tests for these 5 paths:**

**Path 1: Authentication Flow**
- [ ] Register → email sent → verify → login → dashboard
- [ ] Invalid registration (duplicate email, weak password)
- [ ] Password reset flow
- [ ] 2FA enable/disable/verify

**Path 2: Job Search → Apply**
- [ ] Search returns results
- [ ] Match score calculation
- [ ] Save job → appears in saved list
- [ ] Apply → application created → status = pending
- [ ] Duplicate application rejected

**Path 3: Payment → Subscription**
- [ ] Initiate payment → order created
- [ ] Webhook callback → transaction updated → subscription activated
- [ ] Invalid webhook signature → rejected
- [ ] Subscription grants features
- [ ] Expired subscription → features revoked

**Path 4: AI Service Integration**
- [ ] AIService returns valid response (mock OpenAI)
- [ ] Circuit breaker triggers on failure → falls back to Anthropic
- [ ] Prompt registry loads correct prompt
- [ ] Rate limiting enforced on AI endpoints

**Path 5: Agent Auto-Apply Safety**
- [ ] Agent respects daily hard cap
- [ ] Agent skips blacklisted companies
- [ ] Human approval required when configured
- [ ] Kill switch stops all agents
- [ ] Audit log created for every action

**Done when:** All 5 paths have passing integration tests. `php artisan test` passes.

---

### T3-02: Error Monitoring (Sentry)
- **Priority:** P1
- **Effort:** 1-2 days
- **Status:** ⬜ Not Started

**Steps:**
- [ ] Create Sentry project at sentry.io
- [ ] Install SDK: `composer require sentry/sentry-laravel`
- [ ] Set `SENTRY_LARAVEL_DSN` in `.env`
- [ ] Publish config: `php artisan sentry:publish`
- [ ] Test: `php artisan sentry:test`
- [ ] Configure in `.github/workflows/deploy.yml` (Sentry release notification already stubbed)
- [ ] Set up alert rules: error spike, P0 errors, payment failures

**Done when:** Sentry receives test event. Production errors will be captured automatically.

---

### T3-03: Health Check & Uptime Monitoring
- **Priority:** P2
- **Effort:** 3-4 hours
- **Status:** ⬜ Not Started

**Steps:**
- [ ] Verify `/health` endpoint returns 200 with status JSON
- [ ] Health check should verify: DB connection, Redis connection, queue processing, disk space
- [ ] Sign up for UptimeRobot (free tier) or Pingdom
- [ ] Configure monitors for: main app URL, `/health`, admin panel
- [ ] Set up downtime notifications (email + Slack/Discord if available)

**Done when:** External monitor pings `/health` every 60 seconds with alerting.

---

### T3-04: Delete Deprecated Scraper Stubs
- **Priority:** P2
- **Effort:** 2-3 hours
- **Status:** ⬜ Not Started

**Problem:**
Three deprecated scraper services exist as empty stubs, creating false confidence that LinkedIn/Indeed/Glassdoor integration works.

**Files to delete:**
- [ ] `app/Services/Agent/LinkedInScraperService.php`
- [ ] `app/Services/Agent/IndeedScraperService.php`
- [ ] `app/Services/Agent/GlassdoorScraperService.php`

**Files to update:**
- [ ] `app/Services/Agent/JobAggregationService.php` — Remove references to deleted scrapers
- [ ] `CLAUDE.md` — Update Section 7 (Service Architecture) to remove deprecated services
- [ ] Any architecture diagrams referencing these services

**Done when:** No deprecated stub files exist. Documentation matches reality.

---

### T3-05: PWA Device Testing
- **Priority:** P2
- **Effort:** 2-3 days
- **Status:** ⬜ Not Started

**Test on actual devices:**
- [ ] Android Chrome: PWA install prompt appears → install → app opens standalone
- [ ] Android Chrome: Offline mode → cached pages load → "offline" page for uncached
- [ ] Android Chrome: Swipe job browser → touch gestures work → save/skip/apply
- [ ] iOS Safari: Install instructions appear (iOS doesn't support native install prompt)
- [ ] iOS Safari: Offline mode works
- [ ] Desktop Chrome: Install prompt, offline mode
- [ ] Test IndexedDB storage: save jobs offline → reconnect → data persists
- [ ] Test background sync queue: apply offline → auto-submits on reconnect

**Done when:** PWA works on Android Chrome and iOS Safari. Key flows tested.

---

### T3-06: Git Hygiene — Start Feature Branches
- **Priority:** P2
- **Effort:** Ongoing
- **Status:** ⬜ Not Started

**Problem:**
Entire codebase committed in 2 commits. No feature branches, no PRs, no granular history.

**New workflow:**
- [ ] Create `develop` branch from `master`
- [ ] All work happens in feature branches: `feature/job-data-integration`, `fix/email-delivery`, etc.
- [ ] PR into `develop` → review → merge
- [ ] `develop` → `master` for releases
- [ ] Each PR should be a logical unit of work (not "fix everything")
- [ ] CI/CD already supports `develop` branch deployments to dev slot

**Done when:** Next 5 changes each have their own branch and commit message.

---

### T3-07: AI Cost Tracking
- **Priority:** P2
- **Effort:** 3-5 days
- **Status:** ⬜ Not Started

**Problem:**
Azure OpenAI charges per token. Each user's AI calls (resume analysis, interview prep, coaching, predictions) could cost $5-20/month. The ₹499/month Pro plan is ~$6 — margins could be negative.

**Steps:**
- [ ] Add `ai_usage_logs` tracking (table already exists per migration) — log every AI call with token count, cost estimate, user ID, feature
- [ ] Create Filament dashboard widget showing: total AI cost/day, cost per user, cost per feature
- [ ] Set per-user AI call limits by subscription tier:
  - Free: 10 AI calls/day
  - Pro: 50 AI calls/day
  - Enterprise: 200 AI calls/day
- [ ] Implement AI response caching — same prompts (e.g., "interview questions for React developer") return cached results
- [ ] Add token count to API rate limiting

**Done when:** Every AI call logged with cost. Per-user limits enforced by tier. Admin dashboard shows cost trends.

---

## Quick Reference: Command Cheatsheet

```bash
# Check current status of things
php artisan route:list                    # All routes
php artisan migrate:status                # Migration status
php artisan queue:failed                  # Failed jobs
php artisan tinker                        # Interactive REPL

# Common development commands
php artisan serve                         # Start dev server
php artisan queue:work                    # Process queue jobs
php artisan optimize:clear                # Clear all caches
php artisan test                          # Run tests
php artisan test --filter=TestName        # Run specific test

# Verification commands
php -l app/path/to/file.php              # Syntax check
php artisan view:cache                    # Verify Blade compiles
php artisan route:cache                   # Verify routes resolve

# Production commands
php artisan optimize                      # Cache everything
php artisan horizon                       # Start Horizon
php artisan scout:import "App\Models\Job" # Index jobs in Meilisearch
```

---

*Work through this list top-to-bottom. Each item has clear acceptance criteria ("Done when"). Check them off as you go.*
