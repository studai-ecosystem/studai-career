# StudAI Hire Deep Frontend Audit

Audit date: May 30, 2026
Repository: studai-ecosystem/studai-career
Branch audited: master
Scope: Laravel Blade, Livewire, Filament, CSS, JavaScript, route/view/controller frontend surfaces.

This is an inventory audit, not a redesign brief. No fixes are proposed. Problems are recorded as observed conflicts, missing states, duplicated patterns, and incomplete UI surfaces.

---

## SECTION A - PROJECT STRUCTURE SCAN

### A1. Directory structure (frontend/admin UI scope)

Frontend/admin implementation surfaces scanned: 519 files including routes. Mechanical implementation scan excluding routes: 512 files.

```text
app/Filament/
  Pages/
    AgentEmergencyControls.php
    AIUsageMonitoring.php
    QueueMonitor.php
    RevenueAnalytics.php
    SystemSettings.php
    UserActivityTracking.php
  Resources/
    AIBiasReportResource.php
    AIDecisionLogResource.php
    AIDisclaimerResource.php
    AIPromptResource.php
    BackgroundCheckPackageResource.php
    BackgroundCheckResource.php
    BenefitsPackageResource.php
    BulkEmailLogResource.php
    CompanyReviewResource.php
    EvaluationSessionResource.php
    HumanOverrideResource.php
    InterviewExperienceResource.php
    OfferLetterResource.php
    OfferLetterTemplateResource.php
    SalaryReportResource.php
    SocialProviderResource.php
    VideoInterviewSessionResource.php
    Companies/
      CompanyResource.php
      Pages/CreateCompany.php
      Pages/EditCompany.php
      Pages/ListCompanies.php
      Pages/ViewCompany.php
      Schemas/CompanyForm.php
      Schemas/CompanyInfolist.php
      Tables/CompaniesTable.php
    Jobs/
      JobResource.php
      Pages/CreateJob.php
      Pages/EditJob.php
      Pages/ListJobs.php
      Pages/ViewJob.php
      Schemas/JobForm.php
      Schemas/JobInfolist.php
      Tables/JobsTable.php
    Users/
      UserResource.php
      Pages/CreateUser.php
      Pages/EditUser.php
      Pages/ListUsers.php
      Pages/ViewUser.php
      Schemas/UserForm.php
      Schemas/UserInfolist.php
      Tables/UsersTable.php
    UserSubscriptions/
      UserSubscriptionResource.php
      Pages/CreateUserSubscription.php
      Pages/EditUserSubscription.php
      Pages/ListUserSubscriptions.php
      Pages/ViewUserSubscription.php
      Schemas/UserSubscriptionForm.php
      Schemas/UserSubscriptionInfolist.php
      Tables/UserSubscriptionsTable.php
  Widgets/
    JobApplicationsChart.php
    LatestApplications.php
    RevenueChart.php
    StatsOverview.php
app/Livewire/
  CareerCoachChat.php
  ProfileWizard.php
  Mobile/BottomNavigation.php
  Mobile/InstallPrompt.php
  Mobile/QuickApply.php
  Mobile/SwipeJobBrowser.php
  Network/ActivityFeed.php
  Network/ConnectionManager.php
  Network/EventBrowser.php
  Network/EventRsvpButton.php
  Network/GroupBrowser.php
  Network/MentorshipHub.php
  Network/MessagingCenter.php
  Reviews/CompanyReviews.php
  Reviews/SubmitInterview.php
  Reviews/SubmitReview.php
  Reviews/SubmitSalary.php
  Search/FilterPanel.php
  Search/SearchBar.php
  Search/SearchResults.php
  VideoInterview/CreateMockInterview.php
  VideoInterview/SessionList.php
  VideoInterview/SessionResults.php
  VideoInterview/VideoRecorder.php
app/View/Components/
  AppLayout.php
  GuestLayout.php
  Layouts/Dashboard.php
  Layouts/Marketing.php
resources/css/
  app.css
  filament/studai/theme.css
resources/js/
  app.js
  bootstrap.js
  pwa.js
resources/views/
  about.blade.php
  blog.blade.php
  contact.blade.php
  dashboard.blade.php
  features.blade.php
  how-it-works.blade.php
  offline.blade.php
  pricing.blade.php
  privacy.blade.php
  refund-policy.blade.php
  terms.blade.php
  welcome.blade.php
  admin/applications/monitor.blade.php
  agent/applications.blade.php
  agent/configure.blade.php
  agent/dashboard.blade.php
  agent/metrics.blade.php
  analytics/application-funnel.blade.php
  analytics/career-path.blade.php
  analytics/competitor-salaries.blade.php
  analytics/dashboard.blade.php
  analytics/heatmap.blade.php
  analytics/salary-benchmark.blade.php
  analytics/skills-forecast.blade.php
  analytics/source-attribution.blade.php
  analytics/time-to-hire.blade.php
  apply/evaluation.blade.php
  apply/evaluation-complete.blade.php
  apply/no-application.blade.php
  apply/results.blade.php
  apply/show.blade.php
  ats/candidates.blade.php
  ats/create.blade.php
  ats/index.blade.php
  ats/jobs.blade.php
  ats/show.blade.php
  ats/sync-logs.blade.php
  auth/*.blade.php
  background-checks/*.blade.php
  calendar/*.blade.php
  candidate/test.blade.php
  candidate/test-result.blade.php
  career-coach/*.blade.php
  companies/*.blade.php
  components/**/*.blade.php
  dashboard/*.blade.php
  email-templates/*.blade.php
  emails/*.blade.php
  employer/**/*.blade.php
  errors/*.blade.php
  filament/pages/*.blade.php
  gamification/*.blade.php
  hiring-test/*.blade.php
  interview/*.blade.php
  jobs/*.blade.php
  layouts/*.blade.php
  livewire/**/*.blade.php
  marketplace/**/*.blade.php
  mobile/saved-jobs.blade.php
  negotiation/*.blade.php
  network/*.blade.php
  notifications/index.blade.php
  offer-letters/*.blade.php
  pages/*.blade.php
  partials/*.blade.php
  payments/*.blade.php
  pdf/*.blade.php
  profile/**/*.blade.php
  resume/**/*.blade.php
  schedule/*.blade.php
  scout/*.blade.php
  settings/notifications.blade.php
  skills/*.blade.php
  subscriptions/*.blade.php
  video-interview/invitation.blade.php
routes/
  admin_analytics.php
  api.php
  auth.php
  console.php
  employer.php
  resume.php
  web.php
```

### A2. Tech stack confirmation

- Framework: Laravel 12.x with Blade templates, Livewire 3.8, Filament 4.1.
- CSS approach: Tailwind CSS 3.4 plus large custom CSS layers in `resources/css/app.css`; separate Filament theme in `resources/css/filament/studai/theme.css`; extensive inline Tailwind class strings in Blade.
- Component library: Filament for admin resources/pages/widgets; custom Blade components in `resources/views/components`; no React/Vue component library.
- UI primitives: Alpine.js through Livewire/Blade behavior; Filament primitives; no Radix/Headless UI package detected.
- State management: Livewire server-side component state; Alpine local state in Blade; vanilla JS in standalone pages; no Redux/Zustand/React Query.
- Router: Laravel route files (`web.php`, `auth.php`, `employer.php`, `resume.php`, `admin_analytics.php`, `api.php`). No React Router/Next router.
- Icon library: Heroicons via Blade/dynamic component names plus heavy inline SVG usage; no Lucide package.
- Animation library: Tailwind animation utilities plus many page-local `@keyframes`; no Framer Motion/GSAP package.
- Form library: Laravel validation, Blade inputs, Livewire properties, Filament forms; no React Hook Form/Formik.
- Design tokens: `tailwind.config.js`, CSS variables in `resources/css/app.css`, Filament overrides in `resources/css/filament/studai/theme.css`.

### A3. Total file count: pages, components, hooks, utilities, styles

- Total frontend/admin files in route-inclusive scan: 519.
- Total implementation files excluding routes: 512.
- Blade files in implementation scan: 368.
- CSS files: 2.
- JS files: 3.
- PHP Livewire/View/Filament UI classes: 139.
- Blade components under `resources/views/components`: 49.
- Livewire components: 24.
- View component PHP classes: 4.
- Filament pages/widgets/resources/table/schema/page classes: 110.
- React hooks/contexts: Not found / Not applicable.

### A4. Package.json: dependencies with versions

Runtime dependencies in `package.json`: none listed separately.

Dev dependencies:

- `@tailwindcss/forms`: `^0.5.2`
- `alpinejs`: `^3.4.2`
- `autoprefixer`: `^10.4.2`
- `axios`: `^1.11.0`
- `concurrently`: `^9.0.1`
- `laravel-vite-plugin`: `^2.0.0`
- `postcss`: `^8.4.31`
- `tailwindcss`: `^3.4.0`
- `vite`: `^7.0.7`

Composer frontend-relevant packages:

- `laravel/framework`: `^12.0`
- `livewire/livewire`: `^3.8`
- `filament/filament`: `^4.1`
- `laravel/fortify`: `^1.31`
- `laravel/sanctum`: `^4.2`
- `laravel/scout`: `^10.20`
- `openai-php/laravel`: `^0.17.1`
- `barryvdh/laravel-dompdf`: `^3.1`
- `laravel/socialite`: `^5.24`
- `spatie/laravel-permission`: `^6.22`

---

## SECTION B - DESIGN TOKEN INVENTORY

### B1. COLOUR PALETTE

Defined token families in `tailwind.config.js`:

- `google.blue`: `#2f5fb0` family, used as remapped StudAI royal blue.
- `google.red`: `#EA4335` family.
- `google.yellow`: `#e3b62f` family.
- `google.green`: `#1f8a5b` family.
- `brand`: `#2f5fb0` family.
- `navy`: `#0c1c2c` family, plus `line: #21364c`, `soft: #16293c`.
- `gold`: `#e3b62f` family.
- `surface`: `#FFFFFF`, `#FAFAFA`, `#F5F5F5`, `#EEEEEE`, `#E0E0E0`, `#BDBDBD`, `#9E9E9E`, `#757575`, `#616161`, `#424242`, `#212121`.
- `canvas`: `#FFFFFF`, `#f7f8fa`, `#eaecf1`.
- `ink`: `#15233a`, `#5c6a82`, `#7e879a`, `#a3aab8`, `#FFFFFF`.
- `status.success`: `#1f8a5b`, `#e6f4ec`, `#166442`.
- `status.warning`: `#c9941a`, `#fbf2d6`, `#87600f`.
- `status.error`: `#cf3a3a`, `#fbe9e9`, `#9e2727`.
- `status.info`: `#2f5fb0`, `#eaf0fa`, `#1f3f7a`.
- `ai`: `#2f5fb0`, `#21426f`, `#eaf0fa`, gradient start/end.
- `module.coach`: `#2f5fb0` family.
- `module.interview`: `#c9941a`/gold family.
- `module.jobs`: `#1f8a5b` family.
- `module.market`: `#2f5fb0` family.
- `module.negotiation`: `#c9941a`/gold family.
- `module.scout`: `#2f5fb0` family.
- `module.vantage`: `#1c344d` family.
- `module.resume`: `#2f5fb0` family.
- Legacy `studai.pink`: `#ec4899` family.
- Legacy `studai.green`: `#10b981` family.
- Legacy `studai.blue`: `#2f5fb0` family.
- Legacy `studai.yellow`: `#e3b62f` family.

Hardcoded/mechanical usage scan:

- Distinct hex values found: 411.
- Most used hardcoded hex values:
  - `#fff`: 305 occurrences.
  - `#1a73e8`: 235 occurrences.
  - `#6366f1`: 226 occurrences.
  - `#7c3aed`: 187 occurrences.
  - `#2f5fb0`: 160 occurrences.
  - `#6b7280`: 153 occurrences.
  - `#1a1a2e`: 142 occurrences.
  - `#9ca3af`: 132 occurrences.
  - `#8b5cf6`: 89 occurrences.
  - `#a855f7`: 86 occurrences.
  - `#374151`: 81 occurrences.
  - `#f5f3ff`: 75 occurrences.
  - `#4f46e5`: 74 occurrences.
  - `#e5e7eb`: 71 occurrences.
  - `#3b82f6`: 64 occurrences.
  - `#f59e0b`: 62 occurrences.
  - `#ec4899`: 61 occurrences.
  - `#ede9fe`: 60 occurrences.
  - `#10b981`: 58 occurrences.
  - `#16a34a`: 51 occurrences.
  - `#1c344d`: 48 occurrences.
  - `#eaf0fa`: 47 occurrences.
  - `#34a853`: 40 occurrences.

CONFLICT: Primary/action blue is represented by at least `#2f5fb0`, `#1A73E8`, `#6366f1`, `#4f46e5`, `#3b82f6`, `bg-indigo-600`, `bg-blue-600`, and `bg-google-blue-600`. These are all used as primary or prominent CTAs across dashboard, marketing, auth, employer, and chat surfaces.

CONFLICT: Success green is represented by `#1f8a5b`, `#10b981`, `#16a34a`, `#22c55e`, `#34a853`, `bg-green-500`, `bg-green-600`, and `text-green-600`.

CONFLICT: Danger/error is represented by `#cf3a3a`, `#EA4335`, `bg-red-600`, `text-red-500`, `text-red-600`, `google-red-500`, and Filament `--studai-red`.

FLAG: 411 distinct hex values is far above a typical controlled token range. Many are hardcoded in Blade inline styles and page-local CSS.

### B2. TYPOGRAPHY

Defined font families:

- `sans`: Plus Jakarta Sans, Inter, default sans.
- `display`: Plus Jakarta Sans, Inter, default sans.
- `mono`: JetBrains Mono, Fira Code, default mono.
- CSS body font: Plus Jakarta Sans, system fallback.

Defined Tailwind font sizes:

- `xs`: `0.75rem`, line-height `1rem`, letter spacing `0.01em`.
- `sm`: `0.875rem`, line-height `1.25rem`, letter spacing `0.005em`.
- `base`: `1rem`, line-height `1.5rem`.
- `lg`: `1.125rem`, line-height `1.75rem`.
- `xl`: `1.25rem`, line-height `1.75rem`.
- `2xl`: `1.5rem`, line-height `2rem`.
- `3xl`: `1.875rem`, line-height `2.25rem`.
- `4xl`: `2.25rem`, line-height `2.5rem`.
- `5xl`: `3rem`, line-height `1.1`.
- `6xl`: `3.75rem`, line-height `1.05`.
- `7xl`: `4.5rem`, line-height `1`.

Top text class usage:

- `text-sm`: 3,955.
- `text-gray-900`: 1,855.
- `text-xs`: 1,793.
- `text-gray-500`: 1,399.
- `text-gray-600`: 1,137.
- `text-gray-700`: 1,022.
- `text-white`: 983.
- `text-center`: 875.
- `text-lg`: 695.
- `text-gray-400`: 510.
- `text-2xl`: 497.
- `text-3xl`: 387.
- `text-xl`: 379.
- `text-green-600`: 214.
- `text-ink-secondary`: 200.
- `text-ink-primary`: 197.
- `text-indigo-600`: 182.
- `text-blue-600`: 154.
- `text-4xl`: 140.
- `text-gray-800`: 135.
- `text-base`: 121.
- `text-red-500`: 116.
- `text-red-600`: 111.
- `text-purple-600`: 106.

CONFLICT: H1/page headings vary from `text-2xl` and `text-3xl` in dashboard-like pages to `text-5xl`/`text-6xl` in marketing pages and custom inline heading styles in agent/employer dashboards.

CONFLICT: Body text on white uses `text-gray-500`, `text-gray-600`, `text-gray-700`, `text-gray-800`, `text-gray-900`, `text-ink-secondary`, and hardcoded `#6b7280`/`#374151`.

### B3. SPACING

Top spacing classes:

- `px-4`: 1,231.
- `mb-4`: 987.
- `p-6`: 767.
- `gap-2`: 749.
- `mb-2`: 705.
- `py-3`: 697.
- `px-6`: 690.
- `py-2`: 665.
- `mt-1`: 642.
- `mb-6`: 638.
- `mx-auto`: 616.
- `p-4`: 507.
- `gap-4`: 446.
- `gap-3`: 443.
- `py-4`: 422.
- `px-3`: 360.
- `mb-8`: 304.
- `p-8`: 184.
- `space-y-4`: 183.
- `space-y-6`: 160.
- `p-5`: 150.

CONFLICT: Equivalent cards use at least `p-4`, `p-5`, `p-6`, and `p-8`. Candidate dashboard, employer dashboard, marketplace cards, Filament stat cards, and Scout cards do not share card padding.

CONFLICT: Equivalent action buttons use `px-3 py-1.5`, `px-4 py-2`, `px-5 py-3`, `px-6 py-3`, `px-8 py-3`, and raw `.btn-primary` classes.

### B4. BORDER RADIUS

Top radius classes:

- `rounded-xl`: 1,347.
- `rounded-lg`: 1,267.
- `rounded-full`: 1,248.
- `rounded-2xl`: 706.
- `rounded-md`: 373.
- `rounded`: 161.
- `rounded-3xl`: 73.

Defined token radius:

- Button: `9999px`.
- Card: `16px`.
- Input: `12px`.
- Modal: `20px`.
- Panel: `16px`.

CONFLICT: Buttons use `rounded-md`, `rounded-lg`, `rounded-xl`, `rounded-full`, and default Bootstrap-like `btn` classes.

CONFLICT: Cards use `rounded-lg`, `rounded-xl`, `rounded-2xl`, `rounded-3xl`, and Filament-specific 12/16/20px radii.

### B5. SHADOW

Top shadow classes:

- `shadow-sm`: 728.
- `shadow-lg`: 317.
- `shadow`: 103.
- `shadow-md`: 92.
- `shadow-xl`: 54.
- `shadow-2xl`: 39.
- `shadow-xs`: 28.
- `shadow-card`: 20.
- One-off colored/custom shadows include `shadow-fuchsia-500/30`, `shadow-elevation-3`, `shadow-soft`, `shadow-[0_0_30px_rgba(217,70,239,0.2)]`, `shadow-[0_18px_45px_rgba(15,23,42,0.95)]`.

CONFLICT: Dashboard/stat cards use `shadow-sm`, `shadow-lg`, `shadow-card`, custom glow shadows, and Filament `.fi-stats-card` shadows for the same metric-card purpose.

### B6. BREAKPOINTS

Defined Tailwind defaults used:

- `sm`: 640px; 713 class-token usages.
- `md`: 768px; 581 class-token usages.
- `lg`: 1024px; 646 class-token usages.
- `xl`: 1280px; 17 class-token usages.
- `2xl`: 1536px; effectively unused in scanned class tokens.

CONFLICT: Desktop-heavy dashboards and tables often rely on `lg:` grids while only some pages add mobile-specific behavior. Mobile-specific routes exist (`mobile/saved-jobs`, Livewire mobile job browser), but main employer and analytics surfaces largely remain table/chart-first.

### B7. COMPONENT LIBRARY USAGE

- Filament admin components are used through `app/Filament` resources, pages, widgets, schemas, tables, and `resources/views/filament/pages` templates.
- Custom Blade components are used through `<x-studai.*>`, `<x-ui.*>`, `<x-primary-button>`, `<x-secondary-button>`, `<x-danger-button>`, `<x-text-input>`, `<x-input-label>`, `<x-input-error>`, `<x-modal>`, `<x-dropdown>`, `<x-layouts.*>`, `<x-app-layout>`, `<x-guest-layout>`.
- Top component tags: `x-input-error` 24, `x-app-layout` 22, `x-slot` 21, `x-studai.card` 19, `x-layouts.marketing` 11, `x-studai.badge` 10, `x-studai.button` 10, `x-studai.stat-card` 9, `x-text-input` 7, `x-guest-layout` 6.

CONFLICT: Library/custom mixing occurs where Filament admin has its own table/form/action styling, legacy Breeze-style components handle auth/profile, and new StudAI components are used selectively in dashboards.

---

## SECTION C - PAGE AND SCREEN INVENTORY

App GET route count after excluding vendor-only Telescope/Horizon/Ignition surfaces: 388.

Route family counts:

- `/`: 1.
- `about`: 1.
- `admin`: 5.
- `agent`: 5.
- `ai-credits`: 1.
- `analytics`: 18.
- `applications`: 1.
- `apply`: 3.
- `ats`: 1.
- `auth`: 3.
- `background-check-consent`: 1.
- `blog`: 1.
- `calendar`: 6.
- `career-coach`: 7.
- `companies`: 9.
- `contact`: 1.
- `dashboard`: 1.
- `email-templates`: 9.
- `employer`: 81.
- `features`: 1.
- `gamification`: 13.
- `hiring-test`: 1.
- `interview`: 11.
- `jobs`: 6.
- `marketplace`: 32.
- `mobile`: 2.
- `my-offers`: 7.
- `negotiation`: 9.
- `network`: 10.
- `notifications`: 1.
- `payments`: 2.
- `profile`: 7.
- `resume`: 11.
- `schedule`: 3.
- `skills`: 9.
- `studai` Filament: 73.
- `subscriptions`: 3.
- `video-interview`: 8.

### === PAGE: Marketing Landing ===
Route: `/`
User type: Public
File location: `resources/views/pages/landing.blade.php`, legacy `resources/views/welcome.blade.php`
Layout used: `x-layouts.marketing`

PURPOSE:
Public product entry page for StudAI Path/Hire positioning, conversion CTAs, product sections, and navigation to auth/pricing.

SECTIONS ON THIS PAGE:
1. Header/navigation - marketing logo, marketing nav links, auth CTAs.
2. Hero - brand headline, supporting copy, primary/secondary CTA.
3. Feature sections - product capabilities and AI/hiring modules.
4. Social proof/statistics - metrics/cards.
5. CTA/footer - final conversion prompts and legal links.

COMPONENTS USED:
- `x-layouts.marketing` from `resources/views/components/layouts/marketing.blade.php`; layout shell.
- Marketing partials from `resources/views/partials/nav-marketing.blade.php` and `resources/views/partials/footer-marketing.blade.php` on legacy pages.
- Page components such as `hero-section`, `feature-grid`, `cta-section`, `testimonials` where used.

DATA DISPLAYED:
- Marketing claims - hero/cards.
- Feature list - card/grid.
- CTA links - buttons.

ACTIONS AVAILABLE:
- Register/login - links/buttons.
- View pricing/features/contact - nav links.
- Newsletter/contact depending page variant.

FORMS ON THIS PAGE:
Newsletter/contact forms appear on related marketing pages; landing primary form not consistently present.

NAVIGATION:
Links to features, pricing, about, how-it-works, blog, contact, login, register.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Marketing pages exist in duplicate locations (`resources/views/features.blade.php` and `resources/views/pages/features-studai-path.blade.php`, similar for pricing/about/blog/contact/legal), creating multiple visual systems for the same public content.
- Primary brand color appears as tokenized `#2f5fb0` in current tokens but legacy/Google blue `#1A73E8` appears widely in older marketing and onboarding surfaces.

CONFLICTS WITH OTHER PAGES:
- Marketing buttons use marketing-specific hero styles while auth/dashboard buttons use Breeze, StudAI, or inline Tailwind variants.

### === PAGE: Authentication Pages ===
Route: `/login`, `/register`, `/forgot-password`, `/reset-password/{token}`, `/verify-email`, `/two-factor-*`
User type: Public/Auth required depending page
File location: `resources/views/auth/*.blade.php`
Layout used: `x-guest-layout` for login/register/reset/verify; `x-app-layout` for several two-factor/account pages.

PURPOSE:
Authenticate users, recover passwords, verify email, manage two-factor authentication.

SECTIONS ON THIS PAGE:
1. Auth card - logo/heading and form.
2. Social providers - `social-login-buttons` on login/register.
3. Form body - labels, inputs, validation errors.
4. Footer links - forgot password, login/register switch.

COMPONENTS USED:
- `x-guest-layout`, `x-app-layout`.
- `x-input-label`, `x-text-input`, `x-input-error`.
- `x-primary-button`, `x-auth-session-status`, `x-social-login-buttons`.

DATA DISPLAYED:
- Validation errors - inline text.
- Session status - status component.
- Provider buttons - icon/text buttons.

ACTIONS AVAILABLE:
- Submit login/register/reset forms.
- OAuth login/connect.
- Remember me checkbox.
- Two-factor code/recovery flow.

FORMS ON THIS PAGE:
Fields include name, email, password, password confirmation, remember checkbox, token/code. Validation is server-side Laravel/Fortify. Error display uses `x-input-error` and inline Blade errors.

NAVIGATION:
Accessed from marketing nav and auth redirects; links between login/register/forgot.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Auth inputs use legacy `x-text-input` and auth-local classes instead of `x-studai.input`.
- Primary button uses legacy `x-primary-button` styling in some forms, conflicting with `x-studai.button` and inline CTA buttons elsewhere.

CONFLICTS WITH OTHER PAGES:
- Auth primary action style differs from dashboard primary actions (`bg-indigo-600`, `bg-blue-600`, `bg-google-blue-600`, and `bg-gray-800` all appear as primary actions).

### === PAGE: Candidate Dashboard ===
Route: `/dashboard`, `/ai-credits`, `/applications`
User type: Candidate/Auth required
File location: `resources/views/dashboard/index.blade.php`, `resources/views/dashboard/ai-credits.blade.php`, `resources/views/dashboard/applications.blade.php`
Layout used: `x-layouts.dashboard`

PURPOSE:
Candidate hub for subscription usage, profile completion, AI credits, applications, saved jobs, and next actions.

SECTIONS ON THIS PAGE:
1. Dashboard banner - greeting/status.
2. Metric cards - applications remaining, profile completion, AI credits, total applications, saved jobs.
3. Subscription/usage widgets.
4. Recent applications.
5. Saved jobs.
6. Application list/status tabs on applications page.
7. Credit usage history/breakdown on AI credits page.

COMPONENTS USED:
- `x-layouts.dashboard`.
- `x-studai.card`, `x-studai.stat-card`, `x-studai.badge`, `x-studai.button` in newer sections.
- Inline SVGs for metric icons.

DATA DISPLAYED:
- User metrics - cards.
- Applications - list/table cards.
- Saved jobs - cards/list.
- AI credit usage - cards/history table.

ACTIONS AVAILABLE:
- Browse jobs, view applications, upgrade/subscribe, edit profile, view saved jobs.

FORMS ON THIS PAGE:
Applications page has filters/search; validation not central. AI credits page has upgrade/payment actions.

NAVIGATION:
Accessed after login and from dashboard nav/sidebar.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Candidate dashboard uses a newer component-based layout while many candidate feature pages use `@extends('layouts.dashboard')` or `x-app-layout`.
- Metric card animations are dashboard-local and differ from employer/agent/Filament stat card animations.

CONFLICTS WITH OTHER PAGES:
- Candidate stat cards use `x-studai.stat-card`/custom dashboard cards; employer dashboard and agent dashboard define separate metric-card systems.

### === PAGE: Job Search and Job Detail ===
Route: `/jobs`, `/jobs/search`, `/jobs/saved`, `/jobs/{id}`
User type: Candidate/Auth required
File location: `resources/views/jobs/search.blade.php`, `resources/views/jobs/saved.blade.php`, `resources/views/jobs/show.blade.php`, `resources/views/mobile/saved-jobs.blade.php`
Layout used: Mostly `@extends('layouts.dashboard')`; mobile saved uses `layouts.mobile`.

PURPOSE:
Browse jobs, filter/search listings, view job details, save jobs, and apply.

SECTIONS ON THIS PAGE:
1. Search/filter header.
2. Filter controls - location, salary, skills, work mode, experience.
3. Results list - job cards with company, location, salary, match information.
4. Saved jobs list.
5. Job detail - title, company, description, requirements, rounds/tests, apply CTA.

COMPONENTS USED:
- Livewire search components: `SearchBar`, `FilterPanel`, `SearchResults` where embedded.
- `x-ai-disclaimer`, `x-ai-score-explanation` where scoring is shown.
- Inline job cards/buttons rather than a single JobCard component.

DATA DISPLAYED:
- Jobs - list/cards.
- AI match score - badges/progress.
- Company data - card/detail section.
- Hiring rounds/test attempts - list.

ACTIONS AVAILABLE:
- Search/filter jobs.
- Save/unsave.
- Apply.
- Start or view test result.

FORMS ON THIS PAGE:
Search/filter form; job application trigger; saved/unsaved POST actions. Validation mostly server-side.

NAVIGATION:
Accessed from dashboard, nav, mobile browser, direct job links.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Same entity (`job`) is displayed differently in search results, saved jobs, mobile swipe browser, dashboard saved jobs, employer job pages, and marketplace project/gig pages.
- Job CTA buttons vary between blue, indigo, green, gray, pill, rounded-lg, and rounded-xl styles.

CONFLICTS WITH OTHER PAGES:
- Candidate job cards and employer job cards do not share spacing, radius, status badges, or actions.

### === PAGE: Public Apply and Hiring Test Flow ===
Route: `/apply/*`, `/hiring-test/{token}/{stage}`, `/jobs/{jobId}/rounds/{roundId}/test`
User type: Public token / Candidate
File location: `resources/views/apply/*.blade.php`, `resources/views/hiring-test/*.blade.php`, `resources/views/candidate/test.blade.php`, `resources/views/candidate/test-result.blade.php`
Layout used: Mixed guest/app/dashboard depending flow.

PURPOSE:
Allow candidates to apply via public links, complete evaluations/tests, and see results or next steps.

SECTIONS ON THIS PAGE:
1. Job/application introduction.
2. Applicant form/resume selection.
3. Evaluation question/test interface.
4. Progress/timer/anti-cheat messaging where present.
5. Result/complete/no-application states.

COMPONENTS USED:
- Raw form controls.
- Inline SVG icons and status badges.
- Potential Livewire/video integration for evaluation/video surfaces.

DATA DISPLAYED:
- Job data - detail card.
- Application data - status/results.
- Questions/answers - form.
- Scores - result cards.

ACTIONS AVAILABLE:
- Submit application.
- Start/submit evaluation.
- Upload/record responses where configured.

FORMS ON THIS PAGE:
Application form, evaluation/test form. Validation exists server-side; error display is page-local and not standardized.

NAVIGATION:
Accessed through token links, job detail, email links.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Public/token flows use different layouts from logged-in candidate flows, causing visible shell differences in a continuous application journey.
- Test/evaluation controls use raw form styling rather than shared input/button components.

CONFLICTS WITH OTHER PAGES:
- Hiring-test/candidate-test UI differs from skills assessment and interview session UI even though all are question-answer experiences.

### === PAGE: Resume and Profile Builder ===
Route: `/resume/*`, `/resumes/*`, `/profile/career`, `/profile/career/builder`, `/r/{shareToken}`
User type: Candidate/Auth required; public resume share
File location: `resources/views/resume/*.blade.php`, `resources/views/profile/career/index.blade.php`, `resources/views/livewire/profile-wizard.blade.php`, `app/Livewire/ProfileWizard.php`
Layout used: Mixed `layouts.dashboard`, `layouts.app`, `x-app-layout`, public/guest behavior.

PURPOSE:
Manage resumes, generate cover letters, run ATS checks, edit profile/career data, and share public resume.

SECTIONS ON THIS PAGE:
1. Resume list.
2. Create/edit form sections.
3. ATS analysis and suggestions.
4. Cover-letter generation.
5. Preview/public render.
6. Profile wizard steps.

COMPONENTS USED:
- `ProfileWizard` Livewire component.
- `x-ui.ai-textarea`, `x-ui.education-builder`, `x-ui.experience-builder`, `x-ui.skill-selector` in builder-like surfaces.
- Legacy inputs and raw inline inputs in older resume pages.

DATA DISPLAYED:
- Resume fields - forms/preview.
- ATS score - card/progress/suggestions.
- Cover letter - textarea/PDF.
- Profile sections - wizard cards.

ACTIONS AVAILABLE:
- Create/edit/delete/duplicate resume.
- Generate summary/skills/cover letter.
- Export PDF/DOCX.
- Toggle public resume.

FORMS ON THIS PAGE:
Large multi-section forms for profile/resume. Validation is server-side/Livewire; error states vary between components and inline errors.

NAVIGATION:
Accessed from dashboard/profile/job apply.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Resume pages mix `layouts.dashboard`, `layouts.app`, and `x-app-layout`; a user moving between index, edit, ATS, and cover-letter screens sees different shell treatments.
- Builder inputs vary between custom `ui` components and raw Tailwind/legacy controls.

CONFLICTS WITH OTHER PAGES:
- Profile builder step wizard differs from employer onboarding wizard and job posting wizard.

### === PAGE: Career Coach and AI Chat ===
Route: `/career-coach/*`
User type: Candidate/Auth required
File location: `resources/views/career-coach/*.blade.php`, `resources/views/livewire/career-coach-chat.blade.php`, `app/Livewire/CareerCoachChat.php`
Layout used: Mostly `@extends('layouts.dashboard')`; chat is embedded Livewire.

PURPOSE:
Provide AI career coaching sessions, goals, preferences, history, and check-ins.

SECTIONS ON THIS PAGE:
1. Coach dashboard/session cards.
2. Chat window.
3. Message history.
4. Input textarea and voice/send actions.
5. Suggested prompts/session type context.
6. Goals/preferences/check-in forms.

COMPONENTS USED:
- `CareerCoachChat` Livewire.
- Alpine `coachChat` data from `resources/js/app.js`.
- Inline message bubbles, spinner/dots, toast/error state.

DATA DISPLAYED:
- Messages - chat bubbles.
- Session history - list/cards.
- Goals - cards/progress.
- Preferences - form controls.

ACTIONS AVAILABLE:
- Send message.
- Voice input.
- Create/update goals.
- Configure preferences.
- Complete check-in.

FORMS ON THIS PAGE:
Chat textarea, goals/preferences/check-in forms. Validation exists in Livewire/controllers; error display uses toast and inline forms.

NAVIGATION:
Accessed from dashboard/nav/career coach links.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Chat bubbles use their own indigo/assistant styling, not shared with negotiation chatbot or employer Orin chat.
- Voice-input UI exists in JS but is not represented consistently in other chatbot interfaces.

CONFLICTS WITH OTHER PAGES:
- Career coach chat, negotiation chat, Orin chat, and network messaging all implement chat bubbles/input/loading independently.

### === PAGE: Negotiation Strategist ===
Route: `/negotiation`, `/negotiation/dashboard`, `/negotiation/chatbot`, `/negotiation/coaching-*`, `/negotiation/strategy/*`, `/negotiation/scenarios/*`, `/negotiation/scripts/*`, `/negotiation/tactics`
User type: Candidate/Auth required
File location: `resources/views/negotiation/*.blade.php`
Layout used: `@extends('layouts.dashboard')`

PURPOSE:
Manage negotiation strategies, scripts, tactics, scenarios, coaching sessions, and chatbot help.

SECTIONS ON THIS PAGE:
1. Dashboard metrics/active strategies.
2. Strategy details/readiness/leverage.
3. Scenario cards.
4. Scripts library.
5. Coaching chat/session flow.
6. Tactics library.
7. Chatbot page.

COMPONENTS USED:
- Inline cards, badges, progress bars.
- Custom chat/coaching panels.
- Raw buttons and forms.

DATA DISPLAYED:
- Strategies - cards/list.
- Negotiation values - metric cards.
- Messages/scripts - cards/chat bubbles.
- Tactics - grid/list.

ACTIONS AVAILABLE:
- View strategy.
- Start coaching.
- Open chatbot.
- Generate/use scripts.

FORMS ON THIS PAGE:
Strategy/scenario/coaching inputs depending page; validation not standardized.

NAVIGATION:
Accessed from dashboard/nav/offers.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Negotiation uses purple/pink/gold-like gradients and stage dots that do not match current brand/token mapping consistently.
- Negotiation chat and career coach chat use different message alignment, colors, and loading states.

CONFLICTS WITH OTHER PAGES:
- Similar AI coaching experiences exist in career coach, negotiation, Orin onboarding, AI textarea, and Scout, but each has separate visual grammar.

### === PAGE: Employer Dashboard ===
Route: `/employer/dashboard`, `/employer/dashboard/*`
User type: Employer/Auth required
File location: `resources/views/employer/dashboard/index.blade.php`, `resources/views/employer/dashboard/analytics.blade.php`, `resources/views/employer/dashboard/talent-pipeline.blade.php`
Layout used: `@extends('layouts.dashboard')`

PURPOSE:
Employer hub for hiring KPIs, jobs, applications, pipeline, analytics, and quick actions.

SECTIONS ON THIS PAGE:
1. Animated hero/dashboard header.
2. KPI cards.
3. Hiring funnel/pipeline visualizations.
4. Recent applications table.
5. Team/job analytics.
6. Talent pipeline view.

COMPONENTS USED:
- Inline stat cards and custom CSS animations.
- Tables and status badges.
- Chart/canvas/inline visualizations.

DATA DISPLAYED:
- Job counts - cards.
- Applicant counts/statuses - cards/tables.
- Funnel metrics - chart/bar.
- Recent applications - table.

ACTIONS AVAILABLE:
- Post job.
- Review applicants.
- Open analytics.
- Manage pipeline.

FORMS ON THIS PAGE:
Filters/date ranges depending analytics page; validation absent/not central.

NAVIGATION:
Accessed after employer login and from dashboard/sidebar.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Employer dashboard uses page-local animations (`fadeUp`, `popIn`, `shimmer`, `gradFlow`) different from candidate dashboard and agent dashboard animations.
- Employer metric card colors are more gradient/colorful than candidate/Filament cards.

CONFLICTS WITH OTHER PAGES:
- Employer stats and candidate stats both show KPIs but use different card dimensions, icon treatments, shadows, and animation behavior.

### === PAGE: Employer Job Management ===
Route: `/employer/jobs`, `/employer/jobs/create`, `/employer/jobs/{id}`, `/employer/jobs/{id}/edit`, `/employer/job-creator`, `/employer/jobs/wizard/*`
User type: Employer/Auth required
File location: `resources/views/employer/jobs/*.blade.php`, `resources/views/employer/job-creator.blade.php`
Layout used: Mixed `layouts.dashboard` and `x-app-layout`.

PURPOSE:
Create, edit, manage, and publish job postings; use AI/Orin job creator.

SECTIONS ON THIS PAGE:
1. Job list/status counts.
2. Job create/edit form.
3. Job detail metrics/applications.
4. AI job creator chat/form.
5. Wizard/template/preview surfaces.

COMPONENTS USED:
- Raw form controls.
- Inline buttons.
- Possibly `ui` builders/selectors where used.

DATA DISPLAYED:
- Jobs - table/list.
- Status counts - cards/badges.
- Job fields - forms.
- Applications counts - badges/cards.

ACTIONS AVAILABLE:
- Create/edit/publish/close job.
- Generate job description.
- Preview/publish wizard output.

FORMS ON THIS PAGE:
Job form includes title, description, location, salary, skills, employment type, rounds/tests. Validation is server-side; errors vary by page.

NAVIGATION:
Accessed from employer dashboard and nav.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Job create uses `layouts.dashboard`, but job show/edit/profile pages use `x-app-layout` in several places.
- AI job creator/onboarding surfaces use chat styling separate from standard job forms.

CONFLICTS WITH OTHER PAGES:
- Job cards/list rows for employers are styled differently from candidate-facing jobs and marketplace projects.

### === PAGE: Employer Applicants and ATS ===
Route: `/employer/applicants/*`, `/employer/ats/*`, `/ats/*`
User type: Employer/Auth required
File location: `resources/views/employer/applicants/*.blade.php`, `resources/views/employer/applicants/partials/kanban-card.blade.php`, `resources/views/ats/*.blade.php`
Layout used: Mixed `layouts.dashboard` and `x-app-layout` for applicant detail.

PURPOSE:
Review applications, manage status, view AI/ranked candidates, use Kanban, manage ATS connections and sync logs.

SECTIONS ON THIS PAGE:
1. Applicant filters/status tabs.
2. Applicant table.
3. Kanban columns/cards.
4. Ranked candidate table.
5. Applicant detail profile/resume/test/AI explanation.
6. ATS connection list/create/show/sync logs.

COMPONENTS USED:
- Inline tables with `.app-table` and custom status selects.
- `kanban-card` partial.
- Inline badges/progress/gauges.

DATA DISPLAYED:
- Applications - tables/cards/kanban.
- Candidate profile/resume/test scores.
- AI scores and bias/decision badges.
- ATS connections/logs.

ACTIONS AVAILABLE:
- Change status.
- Add note.
- Schedule interview.
- Bulk action.
- Compare/export.
- Move Kanban stage.

FORMS ON THIS PAGE:
Search/status/job filters, status select, notes textarea, interview scheduling form, ATS connection form.

NAVIGATION:
Accessed from employer dashboard, jobs, candidate detail, ATS nav.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Table, Kanban, ranked table, and applicant detail use separate UI systems for the same application/candidate entity.
- Applicant detail uses `x-app-layout`, not the same dashboard shell as applicant list/kanban/ranked pages.

CONFLICTS WITH OTHER PAGES:
- Status badge colors for pending/reviewing/shortlisted/rejected/hired differ across applicant table, ranked table, dashboard cards, Filament, and notification/badge components.

### === PAGE: Employer Interviews, Tests, Referrals, Talent Pool, Messages ===
Route: `/employer/interviews/*`, `/employer/tests/*`, `/employer/referrals/*`, `/employer/talent-pool`, `/employer/messages/*`
User type: Employer/Auth required
File location: `resources/views/employer/interviews/*.blade.php`, `resources/views/employer/tests/*.blade.php`, `resources/views/employer/referrals/*.blade.php`, `resources/views/employer/talent-pool/index.blade.php`, `resources/views/employer/messages/index.blade.php`
Layout used: Mostly `@extends('layouts.dashboard')`

PURPOSE:
Manage interview scheduling/evaluation, hiring tests, referrals, saved talent, and candidate communication.

SECTIONS ON THIS PAGE:
1. Interview list/schedule/detail/evaluate/decide.
2. Test builder/results.
3. Referral list/leaderboard/settings.
4. Talent pool list/tags/search.
5. Employer message inbox.

COMPONENTS USED:
- Raw forms, tables, badges, custom cards.
- Message thread UI in message surfaces.

DATA DISPLAYED:
- Interviews - list/cards/detail.
- Scores - tables/forms.
- Referrals - leaderboard/list.
- Candidates - saved list/cards.
- Messages - conversation list.

ACTIONS AVAILABLE:
- Schedule/evaluate/decide interview.
- Create test/review results.
- Approve referrals/update settings.
- Tag/remove/outreach talent.
- Send messages.

FORMS ON THIS PAGE:
Date/time picker, scorecard fields, decision form, referral settings, message compose.

NAVIGATION:
Accessed from employer dashboard, applicant detail, nav/sidebar.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Message UI differs from Network MessagingCenter and AI chat surfaces.
- Date/time, score, and settings forms use raw input variants rather than unified inputs.

CONFLICTS WITH OTHER PAGES:
- Interview scheduling appears in employer interviews, calendar, public schedule, and network/events with different layouts and controls.

### === PAGE: Orin Employer Onboarding Chat ===
Route: `/employer/orin-onboarding`, `/employer/onboarding`, `/employer/job-creator`
User type: Employer/Auth required
File location: `resources/views/employer/onboarding-chat.blade.php`, `resources/views/employer/onboarding.blade.php`, `resources/views/employer/job-creator.blade.php`
Layout used: Dashboard/app layout around standalone page JS.

PURPOSE:
Collect employer/company setup data through onboarding forms and a conversational Orin AI interface.

SECTIONS ON THIS PAGE:
1. Onboarding header/status.
2. Chat transcript.
3. Profile completion percentage.
4. Input/send area.
5. Skip/defer action.

COMPONENTS USED:
- Standalone inline JS chat implementation.
- Custom CSS chat bubbles.
- No Livewire chat component reuse.

DATA DISPLAYED:
- Company profile fields - chat-derived/completion.
- Conversation history - chat bubbles.

ACTIONS AVAILABLE:
- Send onboarding response.
- Skip setup.
- Generate/fill company/job profile data.

FORMS ON THIS PAGE:
Chat input field. Validation/error display is page-specific.

NAVIGATION:
Accessed from employer onboarding route after first login/profile setup.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Uses standalone HTML/JS rather than the `CareerCoachChat` Livewire pattern.
- Chat primary color uses `#1A73E8`, conflicting with current `#2f5fb0` brand token.

CONFLICTS WITH OTHER PAGES:
- Orin chat differs from career coach chat, negotiation chat, network messaging, and AI textarea patterns.

### === PAGE: S.C.O.U.T. Employer Intelligence Suite ===
Route: `/employer/scout/*`
User type: Employer/Auth required
File location: `resources/views/scout/*.blade.php`
Layout used: `@extends('layouts.dashboard')`

PURPOSE:
Provide AI hiring intelligence: DNA profile, culture analysis, resume analysis, matching, compatibility, predictive analytics, bias elimination, behavioral intelligence, adaptive assessment, automated shortlisting, continuous learning.

SECTIONS ON THIS PAGE:
1. DNA dashboard.
2. Culture analysis form/results.
3. Resume analysis panel.
4. Candidate matching/search.
5. Team compatibility visualizations.
6. Predictive/behavioral/bias dashboards.
7. Automated shortlisting list/results.

COMPONENTS USED:
- Inline cards/charts/forms.
- Custom module colors/classes.
- Raw JS/AJAX in some Scout views.

DATA DISPLAYED:
- Candidate/job/team metrics - charts/cards.
- AI scores - badges/progress.
- Bias/fairness indicators.
- Recommendations - cards/lists.

ACTIONS AVAILABLE:
- Analyze culture/resume/team.
- Search candidates.
- Shortlist/rank.
- View insights.

FORMS ON THIS PAGE:
Analysis forms and filters; validation/error states are page-local.

NAVIGATION:
Accessed from employer Scout routes/nav.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Scout uses its own specialized card/chart treatments, not shared with employer dashboard analytics or Filament charts.
- AI score visualizations differ from `x-ai-score-explanation` and StudAI score components.

CONFLICTS WITH OTHER PAGES:
- AI score displays conflict across Scout, applicant detail, candidate job match, dashboard, and AI score components.

### === PAGE: Analytics Dashboards ===
Route: `/analytics/*`, `/employer/dashboard/analytics`, `/admin/analytics/*`
User type: Candidate/Employer/Admin depending route
File location: `resources/views/analytics/*.blade.php`, `resources/views/employer/dashboard/analytics.blade.php`, admin analytics controller pages
Layout used: Mostly `@extends('layouts.dashboard')`; admin/Filament variants separate.

PURPOSE:
Show market, application, salary, skills, source attribution, time-to-hire, funnel, and competitor salary analytics.

SECTIONS ON THIS PAGE:
1. Header/filter controls.
2. KPI cards.
3. Charts/canvases.
4. Tables/breakdowns.
5. Demo/fallback/loading rows in some pages.

COMPONENTS USED:
- Chart.js/canvas scripts in several pages.
- Raw tables and cards.
- Inline JS for API fetch/fallback demo data.

DATA DISPLAYED:
- Metrics - cards.
- Chart data - canvas charts.
- Breakdown rows - tables.

ACTIONS AVAILABLE:
- Filter date/range.
- Refresh/fetch data.
- Export where supported.

FORMS ON THIS PAGE:
Filter forms; validation mostly absent/not central.

NAVIGATION:
Accessed from dashboards/nav.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Chart color schemes vary between views and are not tied consistently to defined brand/module tokens.
- Loading states vary: time-to-hire has table `Loading...` fallback; many charts rely on JS/demo data without visible skeletons.

CONFLICTS WITH OTHER PAGES:
- Analytics metric cards differ from dashboard metric cards and Filament widget cards.

### === PAGE: Marketplace ===
Route: `/marketplace/*`
User type: Public/Both/Auth required depending route
File location: `resources/views/marketplace/**/*.blade.php`
Layout used: Mostly `@extends('layouts.dashboard')`

PURPOSE:
Freelance/project marketplace: browse projects/gigs/freelancers, employer project management, freelancer dashboard, proposals/contracts/offers/earnings/saved items.

SECTIONS ON THIS PAGE:
1. Marketplace overview/categories.
2. Project/gig listings and detail pages.
3. Freelancer cards/profiles.
4. Employer dashboard/projects/create/edit/review proposals/invite/saved/contract pages.
5. Freelancer dashboard/profile/gigs/proposals/offers/contracts/earnings/badges/saved projects.
6. Messaging/contract dispute/detail pages.

COMPONENTS USED:
- Inline card/list/table patterns.
- Raw forms for create/edit project/gig/profile.
- Status badges and action buttons.

DATA DISPLAYED:
- Projects/gigs/freelancers - cards/lists.
- Contracts/proposals/offers - tables/cards.
- Earnings - metric cards.

ACTIONS AVAILABLE:
- Create/edit projects and gigs.
- Invite freelancer.
- Review proposals.
- Save items.
- Message/contact.
- Manage contracts/disputes.

FORMS ON THIS PAGE:
Project/gig/profile forms, proposal/review forms, message forms.

NAVIGATION:
Accessed from marketplace nav/routes and dashboards.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Marketplace has a distinct card/action vocabulary from hiring/job pages despite similar list/detail/action structures.
- Buttons include `gig-tab`, `btn-primary`, green/blue/purple/orange inline variants.

CONFLICTS WITH OTHER PAGES:
- Marketplace project cards and job cards represent opportunities but use different card hierarchy, badges, CTAs, and colors.

### === PAGE: Companies and Reviews ===
Route: `/companies/*`
User type: Candidate/Both/Auth required for submissions
File location: `resources/views/companies/*.blade.php`, `resources/views/livewire/reviews/*.blade.php`
Layout used: `@extends('layouts.dashboard')`

PURPOSE:
Browse company profiles, jobs, reviews, salaries, interview experiences, and submit company-review data.

SECTIONS ON THIS PAGE:
1. Company browser.
2. Company header/profile partial.
3. Reviews/salaries/interviews/jobs tabs/pages.
4. Submit review/salary/interview forms.

COMPONENTS USED:
- `CompanyReviews`, `SubmitReview`, `SubmitSalary`, `SubmitInterview` Livewire components.
- `companies/partials/header.blade.php`.

DATA DISPLAYED:
- Companies - cards/list.
- Reviews - list/rating histogram.
- Salaries - table/ranges.
- Interviews - list.
- Jobs - list.

ACTIONS AVAILABLE:
- Search/filter companies.
- Submit review/salary/interview.
- View jobs.

FORMS ON THIS PAGE:
Review, salary, interview forms with Livewire validation.

NAVIGATION:
Accessed from company links, jobs, dashboard/nav.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Company review submission forms use Livewire-specific UI distinct from employer/candidate profile and marketplace review forms.
- Company header/card styling differs from employer company profile pages.

CONFLICTS WITH OTHER PAGES:
- Company entity displayed differently in company pages, job detail, employer profile, Filament companies, and marketplace employer surfaces.

### === PAGE: Skills and Gamification ===
Route: `/skills/*`, `/gamification/*`
User type: Candidate/Auth required; public certificate route
File location: `resources/views/skills/*.blade.php`, `resources/views/gamification/*.blade.php`
Layout used: `@extends('layouts.dashboard')`

PURPOSE:
Skills dashboard, assessments, learning paths, validation, daily learning, certificates, points, badges, challenges, rewards, leaderboards, referrals, history, stats.

SECTIONS ON THIS PAGE:
1. Skills dashboard/radar/gaps.
2. Learning paths/progress.
3. Assessment list/take/result.
4. Validation/certificate.
5. Gamification overview and feature pages.

COMPONENTS USED:
- Inline cards, badges, progress bars.
- Raw assessment forms.

DATA DISPLAYED:
- Skills/gaps - cards/charts.
- Assessments/results - cards/tables.
- Badges/rewards/leaderboards - grids/lists.

ACTIONS AVAILABLE:
- Start assessment.
- Continue learning.
- View/redeem rewards.
- Share certificate.

FORMS ON THIS PAGE:
Assessment answers, validation/learning controls.

NAVIGATION:
Accessed from dashboard/nav.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Assessment UI differs from hiring-test and interview question UI.
- Gamification visual style uses achievement/reward patterns that do not align with conservative hiring dashboard surfaces.

CONFLICTS WITH OTHER PAGES:
- Badge/chip/status styles differ from `x-studai.badge`, status chips, and Filament badges.

### === PAGE: Network and Messaging ===
Route: `/network/*`
User type: Candidate/Auth required
File location: `resources/views/network/*.blade.php`, `resources/views/livewire/network/*.blade.php`, `app/Livewire/Network/*.php`
Layout used: `@extends('layouts.dashboard')`

PURPOSE:
Professional feed, connections, messaging, groups, events, mentorship, and profiles.

SECTIONS ON THIS PAGE:
1. Activity feed.
2. Connections manager.
3. Messaging center.
4. Groups browser.
5. Event browser/detail/RSVP.
6. Mentorship hub.
7. Profile view.

COMPONENTS USED:
- Livewire Network components: ActivityFeed, ConnectionManager, MessagingCenter, GroupBrowser, EventBrowser, EventRsvpButton, MentorshipHub.

DATA DISPLAYED:
- Feed items - cards.
- Connections/users - cards/list.
- Conversations - thread/list.
- Events/groups/mentorship - cards/lists.

ACTIONS AVAILABLE:
- Connect/message/join/RSVP/request mentor/reply.

FORMS ON THIS PAGE:
Search, message compose, RSVP, connection actions.

NAVIGATION:
Accessed from network routes/nav.

UI PROBLEMS OBSERVED ON THIS PAGE:
- MessagingCenter is a conversation UI but does not share chat bubble/input/loading patterns with AI chats.
- Social cards use distinct interaction patterns from job/company/candidate cards.

CONFLICTS WITH OTHER PAGES:
- Messaging between network users and employer/candidate messages use different layouts and controls.

### === PAGE: Video Interview ===
Route: `/video-interview/*`
User type: Candidate/Employer/Auth required
File location: `resources/views/video-interview/invitation.blade.php`, `resources/views/livewire/video-interview/*.blade.php`, `app/Livewire/VideoInterview/*.php`
Layout used: Mixed app/dashboard behavior.

PURPOSE:
Create, record, list, and review video interview sessions and invitations.

SECTIONS ON THIS PAGE:
1. Session list.
2. Create mock interview.
3. Video recorder.
4. Session results.
5. Invitation accept/decline.

COMPONENTS USED:
- Livewire VideoInterview components.
- Browser media/video controls and upload states.

DATA DISPLAYED:
- Sessions - list/cards.
- Questions/recordings/results - video/results panels.
- Invitation data - detail card.

ACTIONS AVAILABLE:
- Create session.
- Record/upload video.
- Accept/decline invitation.
- View results.

FORMS ON THIS PAGE:
Create session form; recording/upload actions.

NAVIGATION:
Accessed from video interview routes, employer/candidate flows.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Video interview surfaces are Livewire-specific and do not share progress/loading/empty states with interview practice or public apply evaluation.

CONFLICTS WITH OTHER PAGES:
- Interview practice, hiring-test, public apply evaluation, and video-interview all represent assessment flows with separate UI treatments.

### === PAGE: Filament Admin Panel ===
Route: `/studai/*`
User type: Admin/Auth required
File location: `app/Filament/**/*.php`, `resources/views/filament/pages/*.blade.php`, `resources/css/filament/studai/theme.css`
Layout used: Filament panel layout.

PURPOSE:
Admin CRUD and monitoring for users, jobs, companies, AI prompts/logs, background checks, subscriptions, offer letters, reports, revenue, queues, settings, user activity, AI usage, emergency controls.

SECTIONS ON THIS PAGE:
1. Filament sidebar/navigation.
2. Resource tables.
3. Create/edit forms.
4. View infolists.
5. Widgets/charts/stats.
6. Custom admin pages.

COMPONENTS USED:
- Filament tables/forms/infolists/pages/widgets.
- Custom Filament CSS selectors `.fi-*`.

DATA DISPLAYED:
- Admin entities - tables/forms/infolists/widgets.
- Charts - Filament widgets.

ACTIONS AVAILABLE:
- Create/edit/view/list resources.
- Admin monitoring/actions depending page.

FORMS ON THIS PAGE:
Filament form schemas; validation handled by Filament/Laravel.

NAVIGATION:
Accessed under `/studai` Filament panel.

UI PROBLEMS OBSERVED ON THIS PAGE:
- Filament admin has its own table/form/button/card vocabulary and a separate custom CSS theme, visually separate from the app dashboard system.
- Filament badge colors use `rgba(16,185,129)`/`rgba(245,158,11)`/`rgba(239,68,68)` while app tokens use remapped `#1f8a5b`, `#c9941a`, `#cf3a3a`.

CONFLICTS WITH OTHER PAGES:
- Admin resources for jobs/companies/users/subscriptions display the same entities with Filament tables/forms, not the app's custom card/table language.

---

## SECTION D - COMPONENT LIBRARY AUDIT

Component count under `resources/views/components`: 49.

### === COMPONENT: studai/button ===
File: `resources/views/components/studai/button.blade.php`
Type: Atom
Used on pages: Candidate dashboard, component-driven surfaces, selective StudAI pages.

PURPOSE:
Unified StudAI button with variants, sizes, optional icon, loading/disabled states, link rendering.

PROPS INTERFACE:
- `variant`: required? no; primary/secondary/ghost/outline/danger/success.
- `size`: required? no; xs/sm/md/lg/xl.
- `type`: required? no; button/submit/reset.
- `icon`: optional dynamic component.
- `iconPosition`: optional left/right.
- `loading`: optional boolean.
- `disabled`: optional boolean.
- `href`: optional link.
- `as`: optional element override.

STYLING APPROACH:
- CSS method: Tailwind classes via Blade component.
- Background: variant-based; primary maps to google/brand blue.
- Text colour: variant-based.
- Border: variant-based.
- Border radius: rounded-full.
- Padding: size-based.
- Shadow: button/shadow variants.
- Font size: xs to base.
- Font weight: medium/semibold.

STATES HANDLED:
- Default: variant-specific.
- Hover: present.
- Active/Selected: active scale on primary.
- Disabled: present.
- Loading: present.
- Empty/Error: absent.

RESPONSIVENESS:
- Mobile/tablet/desktop: inline-flex; inherited width unless caller sets width.

ACCESSIBILITY:
- ARIA labels: partial/inherited.
- Keyboard navigation: native button/link.
- Focus ring: present.
- Screen reader text: loading spinner needs contextual label from button text.

PROBLEMS:
- Component exists but is sparsely used compared with 893 raw buttons and 563 distinct button class/component usages.
- Does not govern legacy `x-primary-button`, `x-secondary-button`, `x-danger-button`, `btn-primary`, or inline page buttons.

DUPLICATES / CONFLICTS:
- `primary-button`, `secondary-button`, `danger-button`, raw inline buttons, Filament buttons.

### === COMPONENT: Legacy Button Set ===
File: `resources/views/components/primary-button.blade.php`, `secondary-button.blade.php`, `danger-button.blade.php`
Type: Atom
Used on pages: Auth/profile/two-factor/forms.

PURPOSE:
Laravel Breeze-style form action buttons.

PROPS INTERFACE:
- Slot content plus inherited HTML attributes.

STYLING APPROACH:
- CSS method: Tailwind class strings.
- Background: primary uses gray/dark style; danger red; secondary white/gray.
- Border radius: rounded-md.
- Padding: px-4 py-2.
- Font size/weight: text-xs uppercase semibold.

STATES HANDLED:
- Hover/focus/disabled present through Tailwind classes.
- Loading absent.

RESPONSIVENESS:
- Inline; inherited.

ACCESSIBILITY:
- Native semantics; focus ring present.

PROBLEMS:
- Primary visual does not match StudAI primary or inline blue/indigo buttons.
- Separate component files duplicate variant logic.

DUPLICATES / CONFLICTS:
- `studai/button`, inline buttons, Filament buttons.

### === COMPONENT: studai/card ===
File: `resources/views/components/studai/card.blade.php`
Type: Molecule
Used on pages: Candidate dashboard and selected StudAI surfaces.

PURPOSE:
Reusable card shell with variants and padding.

PROPS INTERFACE:
- `variant`: default/flat/elevated/glass/interactive.
- `padding`: none/sm/md/lg.
- `hoverable`: boolean.
- `as`: HTML element.

STYLING APPROACH:
- Background: white or glass.
- Border: surface border.
- Radius: 16px/rounded-xl style.
- Padding: p-0/p-4/p-6/p-8.
- Shadow: card/elevation/soft depending variant.

STATES HANDLED:
- Hover present when configured.
- Active/disabled/loading/error absent.

RESPONSIVENESS:
- Width inherited.

ACCESSIBILITY:
- Depends on `as`; no inherent ARIA.

PROBLEMS:
- Page-specific cards outnumber component cards; 274 files match card patterns.

DUPLICATES / CONFLICTS:
- Dashboard stat cards, employer cards, marketplace cards, Scout cards, Filament cards.

### === COMPONENT: studai/input ===
File: `resources/views/components/studai/input.blade.php`
Type: Atom
Used on pages: Select component-driven forms.

PURPOSE:
Reusable label/input/error/hint control.

PROPS INTERFACE:
- `name`, `label`, `type`, `placeholder`, `value`, `error`, `hint`, `required`, `disabled`, `iconLeft`, `iconRight`, `size`.

STYLING APPROACH:
- Background: white.
- Border: surface/gray, error red.
- Radius: input token/rounded-lg.
- Padding: size-based.
- Focus: blue border/ring.

STATES HANDLED:
- Default, focus, disabled, error present.
- Loading absent.

RESPONSIVENESS:
- Full width.

ACCESSIBILITY:
- Label/id/error associations present.

PROBLEMS:
- Only 4 `x-studai.input` usages found while 645 raw `<input>` elements and 316 distinct input/component usage strings exist.

DUPLICATES / CONFLICTS:
- `text-input`, raw inputs, `form-input`, `auth-input`, Filament inputs, smart select/AI textarea.

### === COMPONENT: Component Catalog Summary ===
File: Multiple under `resources/views/components`
Type: Mixed atoms/molecules/layouts
Used on pages: Listed below.

PURPOSE / PROPS / STYLING / STATES / RESPONSIVENESS / ACCESSIBILITY / PROBLEMS / DUPLICATES:

| Component | File | Type | Props observed/inferred | Styling and states | Problems/conflicts |
|---|---|---|---|---|---|
| `studai/badge` | `resources/views/components/studai/badge.blade.php` | Atom | variant, size, dot | Pill badges, semantic colors | Duplicated by inline status badges and Filament badges |
| `studai/chip` | `resources/views/components/studai/chip.blade.php` | Atom | variant, size, icon, removable, interactive | Pill chips; removable state | Duplicated by inline skill/status chips |
| `studai/avatar` | `resources/views/components/studai/avatar.blade.php` | Atom | src, name, size, status, rounded | Initial/image avatar | Duplicated by inline candidate/company avatars |
| `studai/progress` | `resources/views/components/studai/progress.blade.php` | Atom | value, max, variant, size, showLabel, animated | Progress bar variants | Duplicated by inline progress bars in dashboards/skills/profile |
| `studai/stat-card` | `resources/views/components/studai/stat-card.blade.php` | Molecule | title, value, icon, iconColor, change | Card + icon + trend | Duplicated by employer/agent/Filament metric cards |
| `studai/search-bar` | `resources/views/components/studai/search-bar.blade.php` | Molecule | placeholder, action, debounce | Search input/icon | Duplicated by raw search fields |
| `studai/ai-score` | `resources/views/components/studai/ai-score.blade.php` | Molecule | score/context inferred | Score badge/progress | Duplicated by Scout/applicant/job match score UI |
| `ui/step-wizard` | `resources/views/components/ui/step-wizard.blade.php` | Organism | steps, currentStep, completedSteps, allowJumpAhead, size | Desktop/mobile stepper | Not reused by all onboarding/wizard flows |
| `ui/smart-select` | `resources/views/components/ui/smart-select.blade.php` | Molecule | name, label, options, selected, multiple, searchable, creatable, maxItems, grouped, error | Searchable dropdown | Duplicated by raw selects; 184 raw selects found |
| `ui/ai-textarea` | `resources/views/components/ui/ai-textarea.blade.php` | Molecule | name, label, value, rows, maxLength, aiPrompt, aiContext, suggestions | AI panel + textarea | AI writing UI differs from chatbots |
| `ui/skill-selector` | `resources/views/components/ui/skill-selector.blade.php` | Organism | name, label, skills, selected, categories | Search + skill chips | Duplicated by raw skill chips in profile/job/resume forms |
| `ui/range-slider` | `resources/views/components/ui/range-slider.blade.php` | Atom | min, max, step, value, label | Slider | Numeric controls elsewhere use raw inputs/selects |
| `ui/quick-chips` | `resources/views/components/ui/quick-chips.blade.php` | Molecule | options/selected inferred | Chip list | Conflicts with status/badge/chip variants |
| `ui/education-builder` | `resources/views/components/ui/education-builder.blade.php` | Organism | education array inferred | Repeater form | Not shared by all resume/profile education forms |
| `ui/experience-builder` | `resources/views/components/ui/experience-builder.blade.php` | Organism | experience array inferred | Repeater form | Not shared by all resume/profile experience forms |
| `ui/responsive-container` | `resources/views/components/ui/responsive-container.blade.php` | Layout | container/grid props inferred | Responsive wrapper | Page layouts still use local containers |
| `ui/toast-container` | `resources/views/components/ui/toast-container.blade.php` | Overlay | position, maxToasts, duration | Toast stack with types | Toast systems also exist in Livewire/Filament/page JS |
| `modal` | `resources/views/components/modal.blade.php` | Overlay | name, show, maxWidth | Alpine modal/focus trap | 95 files match modal/overlay patterns, not all use component |
| `dropdown` | `resources/views/components/dropdown.blade.php` | Overlay | align, width, contentClasses | Alpine dropdown | Menus/popovers also implemented inline |
| `dropdown-link` | `resources/views/components/dropdown-link.blade.php` | Atom | href/attributes/slot | Dropdown item | Tied to dropdown only |
| `text-input` | `resources/views/components/text-input.blade.php` | Atom | type/value/attributes | Breeze input | Duplicates `studai/input` |
| `input-label` | `resources/views/components/input-label.blade.php` | Atom | value/slot | Breeze label | Duplicates label logic in `studai/input` |
| `input-error` | `resources/views/components/input-error.blade.php` | Atom | messages | Error text | Inline error variants also common |
| `auth-session-status` | `resources/views/components/auth-session-status.blade.php` | Atom | status | Auth status message | Toast/alert systems differ |
| `back-button` | `resources/views/components/back-button.blade.php` | Atom | href/label inferred | Link/button | Inline back links also common |
| `loading-skeleton` | `resources/views/components/loading-skeleton.blade.php` | Atom | type, rows, animated | Skeleton variants | 92 loading-pattern files but skeleton not used uniformly |
| `hero-section` | `resources/views/components/hero-section.blade.php` | Organism | title, subtitle, buttons, stats, gradient | Marketing hero | Marketing pages also implement full custom heroes |
| `feature-grid` | `resources/views/components/feature-grid.blade.php` | Organism | features | Grid/cards | Duplicated by inline feature grids |
| `cta-section` | `resources/views/components/cta-section.blade.php` | Organism | CTA copy/buttons | Marketing CTA | CTA buttons vary |
| `pricing-table` | `resources/views/components/pricing-table.blade.php` | Organism | plans/features | Pricing cards/table | Subscription/pricing pages have separate layouts |
| `testimonials` | `resources/views/components/testimonials.blade.php` | Organism | testimonials | Cards/carousel/grid | Marketing-only |
| `cookie-consent` | `resources/views/components/cookie-consent.blade.php` | Overlay | preferences inferred | Banner/modal | Separate from toast/modal visual system |
| `ai-disclaimer` | `resources/views/components/ai-disclaimer.blade.php` | Molecule | type/context inferred | Alert/disclaimer | AI disclaimers not consistently present across AI pages |
| `ai-score-explanation` | `resources/views/components/ai-score-explanation.blade.php` | Molecule | score/explanation inferred | AI score panel | Duplicated by Scout/applicant/job score displays |
| `application-logo` | `resources/views/components/application-logo.blade.php` | Atom | none | SVG/logo | Brand variants also appear in layouts/partials |
| `optimized-image` | `resources/views/components/optimized-image.blade.php` | Atom | src/alt/loading inferred | Lazy image | Raw `<img>` count: 97 |
| `social-login-buttons` | `resources/views/components/social-login-buttons.blade.php` | Molecule | providers inferred | OAuth buttons | Auth-only button style differs |
| `cursor` | `resources/views/components/cursor.blade.php` | Utility | none | Custom cursor script | Decorative interaction not present elsewhere |
| `marketing-layout` | `resources/views/components/marketing-layout.blade.php` | Layout wrapper | title/description inferred | Wrapper around marketing layout | Duplicates `components/layouts/marketing` |
| `nav-link` | `resources/views/components/nav-link.blade.php` | Atom | active/href inferred | Nav link | Duplicated by responsive nav link and inline navs |
| `responsive-nav-link` | `resources/views/components/responsive-nav-link.blade.php` | Atom | active/href inferred | Mobile nav link | Duplicates nav link |
| `layouts/app` | `resources/views/components/layouts/app.blade.php` | Layout | title inferred | App shell | Coexists with `layouts/app.blade.php` |
| `layouts/marketing` | `resources/views/components/layouts/marketing.blade.php` | Layout | title/description inferred | Marketing shell | Coexists with `layouts/marketing.blade.php` and partials |

---

## SECTION E - BUTTON AUDIT

Mechanical counts:

- Raw `<button>` elements: 893.
- Distinct inline button class strings plus Blade button component usages: 563.
- Top component/inline usages include `x-primary-button`, `x-studai.button`, `btn-primary`, `btn btn-primary`, raw blue/indigo/green/purple/orange/red buttons.

### === BUTTON VARIANT: StudAI Primary Pill ===
Found in files: `resources/views/components/studai/button.blade.php`, selected dashboards/components.
HTML/JSX: `<x-studai.button variant="primary">` or generated classes using `bg-google-blue-600 text-white rounded-full`.

Visual properties:
- Background: `bg-google-blue-600` / token `#2f5fb0`.
- Text colour: white.
- Border: none or transparent.
- Border radius: full pill.
- Padding: size-based (`px-4 py-2` default).
- Font size: text-sm/base by size.
- Font weight: medium/semibold.
- Hover state: darker blue, shadow.
- Focus state: ring.
- Disabled state: present.

Semantic purpose: Primary action.

CONFLICT: Yes - legacy primary, inline indigo, inline blue, and marketplace `btn-primary` also serve primary actions.

### === BUTTON VARIANT: Legacy Breeze Primary ===
Found in files: auth/profile/two-factor forms.
HTML/JSX: `<x-primary-button>` and generated `bg-gray-800 dark:bg-gray-200 ... rounded-md ... uppercase tracking-widest`.

Visual properties:
- Background: gray/dark.
- Text colour: white/dark inversion.
- Border: transparent.
- Border radius: rounded-md.
- Padding: px-4 py-2.
- Font size: text-xs.
- Font weight: semibold uppercase.
- Hover state: gray changes.
- Focus state: ring.
- Disabled state: present.

Semantic purpose: Primary form action.

CONFLICT: Yes - differs from StudAI blue/pill primary and inline dashboard primary.

### === BUTTON VARIANT: Inline Indigo Primary ===
Found in files: multiple dashboard/form pages.
HTML/JSX: `inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition` and variants.

Visual properties:
- Background: `bg-indigo-600` / hardcoded indigo family.
- Text colour: white.
- Border: none.
- Border radius: rounded-lg.
- Padding: px-4 py-2.
- Font size: not always set.
- Font weight: often medium/semibold.
- Hover state: indigo-700.
- Focus state: inconsistent.
- Disabled state: inconsistent.

Semantic purpose: Primary action.

CONFLICT: Yes - primary actions use indigo, blue, green, gray, and StudAI token colors.

### === BUTTON VARIANT: Inline Blue Primary ===
Found in files: jobs, analytics, employer, dashboard surfaces.
HTML/JSX: `px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition`; `px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition`.

Visual properties:
- Background: `bg-blue-600` / `#3b82f6` family.
- Text colour: white.
- Border: none.
- Border radius: rounded-lg or rounded-xl.
- Padding: px-6 py-2/3.
- Font size: text-sm/base or inherited.
- Font weight: medium/bold.
- Hover state: blue-700.
- Focus state: inconsistent.
- Disabled state: inconsistent.

Semantic purpose: Primary action.

CONFLICT: Yes - same purpose as StudAI primary and indigo primary.

### === BUTTON VARIANT: Success/Green CTA ===
Found in files: dashboard, marketplace, employer action pages.
HTML/JSX: `inline-flex items-center gap-2 px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl shadow-sm transition` and `bg-green-500/600` variants.

Visual properties:
- Background: green-500/600.
- Text colour: white.
- Border: usually absent.
- Border radius: rounded-lg/xl.
- Padding: px-4 to px-8, py-2 to py-3.
- Font size: variable.
- Font weight: semibold/bold.
- Hover state: green-700 or opacity.
- Focus state: inconsistent.
- Disabled state: inconsistent.

Semantic purpose: Sometimes success, sometimes primary CTA.

CONFLICT: Yes - green is used both as success status and primary call-to-action.

### === BUTTON VARIANT: Destructive Red ===
Found in files: legacy danger button, delete/remove actions, auth/profile destructive actions.
HTML/JSX: `inline-flex items-center px-4 py-2 bg-red-600 ... rounded-md ... hover:bg-red-700`; `<x-danger-button>`.

Visual properties:
- Background: red-600/google-red/danger.
- Text colour: white.
- Border: transparent.
- Border radius: rounded-md/rounded-lg.
- Padding: px-4 py-2.
- Font size: text-xs/text-sm.
- Font weight: semibold.
- Hover state: red-700.
- Focus state: present in legacy, inconsistent inline.
- Disabled state: partial.

Semantic purpose: Destructive.

CONFLICT: Yes - destructive buttons vary between md and lg radii, uppercase/non-uppercase, red shades.

### === BUTTON VARIANT: Secondary Gray/Outline ===
Found in files: forms, modals, marketplace, dashboards.
HTML/JSX: `px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition`; `border border-gray-200 text-gray-700 rounded-xl`; `<x-secondary-button>`.

Visual properties:
- Background: white/gray-100/transparent.
- Text colour: gray-600/700.
- Border: gray-200/300 or absent.
- Border radius: rounded-md/lg/xl.
- Padding: variable.
- Hover state: gray-50/100/200.
- Focus state: inconsistent.
- Disabled state: inconsistent.

Semantic purpose: Secondary action.

CONFLICT: Yes - secondary buttons vary by radius, border, fill, and typography.

### === BUTTON VARIANT: Tab/Filter Buttons ===
Found in files: marketplace, applications, applicants, analytics, reviews.
HTML/JSX: `tab-button`, `filter-btn px-4 py-2 rounded-lg`, `tab-btn flex-1 py-3 px-6 rounded-lg font-semibold transition-all tab-inactive`, role-filter class interpolations.

Visual properties:
- Background: active/inactive page-specific.
- Text colour: page-specific.
- Border: page-specific.
- Border radius: often rounded-lg/full.
- Padding: px-3/4/6 and py-1.5/2/3.
- Hover state: page-specific.
- Focus state: often absent.
- Disabled state: absent.

Semantic purpose: Filter/tab/segmented control.

CONFLICT: Yes - tabs and filters are implemented separately across features.

Total distinct button variants found: 563 class/component usage strings. Equivalent primary/secondary/destructive/tab purposes use many unrelated class strings.

---

## SECTION F - FORM AND INPUT AUDIT

Mechanical counts:

- Raw `<input>` elements: 645.
- Raw `<select>` elements: 184.
- Raw `<textarea>` elements: 109.
- Checkbox inputs: 83.
- Radio inputs: 45.
- Forms: 175.
- Distinct input/select/textarea class/component usages: 316.

### === INPUT VARIANT: StudAI Input ===
Input type: text/email/password/search/number/date etc.
Found in files: `resources/views/components/studai/input.blade.php`, selective pages.
Styling:
- Border: surface/gray; error red.
- Border radius: input token/rounded-lg.
- Background: white.
- Text colour: ink/gray.
- Placeholder: styled.
- Focus ring: blue token ring/border.
- Error state: red border and message.
- Label style: above.
- Helper text: present when hint passed.

CONFLICT: Yes - only 4 component usages found; raw inputs dominate.

### === INPUT VARIANT: Legacy Breeze Input ===
Input type: text/email/password.
Found in files: auth/profile/two-factor forms.
Styling:
- Border: gray-300/dark gray.
- Border radius: rounded-md.
- Background: white/dark.
- Text colour: gray-900/dark.
- Placeholder: inherited.
- Focus ring: indigo-500.
- Error state: separate `x-input-error`.
- Label style: above via `x-input-label`.
- Helper text: inconsistent.

CONFLICT: Yes - focus color uses indigo rather than current brand token.

### === INPUT VARIANT: Raw Rounded-md Table/Form Input ===
Input type: input/textarea/select.
Found in files: applicant/test/form-heavy pages.
Styling:
- Border: `border-gray-300`.
- Border radius: rounded-md.
- Background: white/inherited.
- Text colour: text-sm or inherited.
- Placeholder: unstyled.
- Focus ring: often absent or plugin default.
- Error state: absent unless page adds inline errors.
- Label style: page-specific.
- Helper text: absent/inline.

CONFLICT: Yes - same input type elsewhere uses rounded-lg/xl and blue/indigo/purple focus rings.

### === INPUT VARIANT: Auth Input Class ===
Input type: auth form controls.
Found in files: `resources/views/auth/login.blade.php`, `resources/views/auth/register.blade.php`, related auth pages.
Styling:
- Border/background/focus defined by `.auth-input` page/CSS class.
- Border radius: custom page class.
- Placeholder: page-specific.
- Error state: separate error component.
- Label style: auth-local.

CONFLICT: Yes - auth fields do not use StudAI input or Filament forms.

### === INPUT VARIANT: Raw Rounded-xl Brand Input ===
Input type: text/search/select/textarea.
Found in files: resume/profile/marketplace/employer forms.
Styling:
- Border: gray-200/300.
- Border radius: rounded-xl.
- Background: gray-50/white.
- Text colour: gray-900.
- Placeholder: sometimes styled.
- Focus ring: `focus:ring-studai-blue-500`, `focus:ring-blue-500`, `focus:ring-purple-500`, `focus:ring-primary`, or `focus:border-violet-400`.
- Error state: inconsistent.
- Label style: above.
- Helper text: page-specific.

CONFLICT: Yes - equivalent forms choose blue, indigo, purple, orange, or primary focus rings.

### === INPUT VARIANT: Smart Select / AI Textarea / Skill Selector ===
Input type: select/textarea/tag selector.
Found in files: components and builder pages.
Styling:
- Border: component-defined.
- Border radius: around 12px/rounded-xl.
- Background: white.
- Focus ring: brand/blue.
- Error state: present in component.
- Label style: above.
- Helper text: present.

CONFLICT: Yes - these richer controls are not used consistently in job/resume/profile/employer forms.

---

## SECTION G - CARD AND LIST ITEM AUDIT

Mechanical counts:

- Files with card patterns: 274.
- `rounded-xl`, `rounded-lg`, `rounded-2xl`, and `rounded-full` dominate card/list item styling.

### === CARD VARIANT: StudAI Card ===
Represents: Generic content shell.
Found in files: `resources/views/components/studai/card.blade.php`, selected component-driven pages.
Dimensions: Width inherited; no fixed height.
Styling:
- Background: white or glass.
- Border: surface border.
- Border radius: 16px/rounded-xl.
- Shadow: `shadow-card`/variant.
- Padding: p-0/p-4/p-6/p-8.

Content layout:
1. Slot content.
2. Optional hover/interactive behavior from props.

Actions on card: Caller-defined.

States:
- Default: white bordered card.
- Saved/Bookmarked: not inherent.
- Applied: not inherent.
- Hover: optional lift/shadow.

CONFLICT: Same card role is often implemented inline.

### === CARD VARIANT: Candidate Dashboard Metric Card ===
Represents: Candidate KPI.
Found in files: `resources/views/dashboard/index.blade.php`, `resources/views/dashboard/ai-credits.blade.php`.
Dimensions: Grid responsive; height inherited.
Styling:
- Background: white/gradient card variants.
- Border: subtle or none depending card.
- Border radius: rounded-2xl/rounded-xl.
- Shadow: custom dashboard/card shadow.
- Padding: p-5/p-6.

Content layout:
1. Icon badge.
2. Numeric value.
3. Label.
4. Optional trend/description/progress.

Actions on card: Dashboard links/CTAs.

States:
- Hover: custom lift/glow/shimmer in some cards.

CONFLICT: Employer, agent, analytics, and Filament metric cards use different styling.

### === CARD VARIANT: Employer Dashboard Metric Card ===
Represents: Employer KPI.
Found in files: `resources/views/employer/dashboard/index.blade.php`, `resources/views/employer/dashboard/analytics.blade.php`.
Dimensions: Responsive grid.
Styling:
- Background: colorful gradients and white cards.
- Border: page-specific.
- Border radius: rounded-xl/2xl.
- Shadow: custom shadow.
- Padding: p-5/p-6.

Content layout:
1. Label/icon.
2. Count/value.
3. Trend/status.

Actions on card: Quick actions and linked panels.

CONFLICT: Same KPI concept differs from candidate and Filament cards.

### === CARD VARIANT: Job Card ===
Represents: Job listing.
Found in files: `resources/views/jobs/search.blade.php`, `resources/views/jobs/saved.blade.php`, `resources/views/dashboard/index.blade.php`, `resources/views/livewire/mobile/swipe-job-browser.blade.php`, employer job views.
Dimensions: List cards; mobile swipe cards have different full-card behavior.
Styling:
- Background: white.
- Border: gray/surface.
- Border radius: rounded-lg/xl/2xl.
- Shadow: none/shadow-sm/shadow-lg depending location.
- Padding: p-4/p-5/p-6.

Content layout:
1. Title.
2. Company/location.
3. Salary/work-mode metadata.
4. AI match/status badges.
5. Save/apply actions.

Actions on card:
- Save/unsave.
- Apply/view detail.

States:
- Saved: icon/button state varies.
- Applied: badge/status varies.
- Hover: some lift/shadow, some none.

CONFLICT: Job entity is displayed differently across browse, saved jobs, dashboard, mobile, employer job management, and Filament jobs.

### === CARD VARIANT: Candidate/Application Card ===
Represents: Applicant/candidate/application.
Found in files: `resources/views/employer/applicants/*.blade.php`, `resources/views/dashboard/applications.blade.php`, `resources/views/employer/applicants/partials/kanban-card.blade.php`.
Dimensions: Table rows, Kanban cards, ranked rows, detail profile cards.
Styling:
- Background: white.
- Border: gray/surface.
- Radius: rounded-lg/xl.
- Shadow: none/shadow-sm.
- Padding: p-3/p-4/p-6.

Content layout:
1. Candidate avatar/name.
2. Job/application data.
3. Score/status badge.
4. Actions/status select.

Actions on card:
- View.
- Move/update status.
- Add note/schedule.

States:
- Status colors vary.
- Hover varies.

CONFLICT: Same application entity appears as table row, Kanban card, ranked row, dashboard row, and Filament resource row with different styling.

### === CARD VARIANT: Marketplace Opportunity Card ===
Represents: Project/gig/freelancer.
Found in files: `resources/views/marketplace/**/*.blade.php`.
Dimensions: Grid/list cards.
Styling:
- Background: white.
- Border: gray.
- Radius: rounded-lg/xl/2xl.
- Shadow: variable.
- Padding: p-4/p-6.

Content layout:
1. Title/name.
2. Category/skills/budget.
3. Status or rating.
4. Actions.

Actions on card:
- View, save, invite, propose/message.

CONFLICT: Opportunity cards conflict with job cards despite similar job-to-be-done.

---

## SECTION H - NAVIGATION AND LAYOUT AUDIT

### H1. GLOBAL NAVIGATION (header/navbar)

- Present on which pages: Marketing pages (`x-layouts.marketing`, partial marketing nav), authenticated layouts (`layouts/navigation.blade.php`, `x-app-layout`, `layouts.dashboard`), mobile layout, Filament panel.
- Height: `--topbar-height: 64px` in CSS variables; actual Blade layouts may vary.
- Background: marketing and app/dashboard layouts vary; Filament sidebar uses gradient-tinted background.
- Logo position: left in most layouts.
- Nav links: marketing includes features/pricing/about/how-it-works/blog/contact/auth; dashboard/app layouts include user/profile/nav/sidebar feature links depending layout.
- Auth state: marketing changes login/register/profile/dashboard; dashboard uses authenticated user menu.
- Mobile behaviour: hamburger/responsive nav in app layout; mobile bottom nav component exists separately.
- PROBLEM: Yes. Marketing, app, dashboard, mobile, and Filament navs look different. Candidate dashboard uses `x-layouts.dashboard`, many feature pages use `layouts.dashboard`, and some employer/resume/interview pages use `x-app-layout`.

### H2. SIDEBAR

- Present on which pages: Most `layouts.dashboard` pages and `x-layouts.dashboard` surfaces; Filament has separate sidebar.
- Width: CSS variable `--sidebar-width: 252px`; Tailwind config defines grid sidebar widths 280px and collapsed 72px.
- Background: dashboard layout-specific; Filament sidebar gradient tint.
- Items: feature navigation for dashboard; Filament resource navigation for admin.
- Active state: route/link class in dashboard; Filament `.fi-active` blue background.
- Collapsible: dashboard status unclear from view scan; Filament supports panel behavior.
- PROBLEM: Yes. Pages using `x-app-layout` in employer/profile/job/interview/resume flows can omit or alter expected dashboard sidebar.

### H3. FOOTER

- Present on which pages: Marketing pages via marketing layout/partials; not consistently present on authenticated dashboard pages; Filament uses panel shell.
- Content: legal links, product links, brand/copy depending marketing layout.
- PROBLEM: Yes. Duplicate public page files and layout variants create footer differences.

### H4. PAGE LAYOUTS

Distinct templates found:

- `x-layouts.marketing`: Public marketing pages; constrained marketing layout.
- `x-marketing-layout`: wrapper/legacy marketing component; used by subscription pricing and component docs.
- `layouts.marketing`: legacy Blade layout.
- `x-guest-layout`: auth pages.
- `x-app-layout`: account/app pages, some employer/job/interview/resume screens.
- `layouts.app`: Blade app layout for some resume/interview/video pages.
- `layouts.dashboard`: large majority of candidate/employer/marketplace/analytics pages.
- `x-layouts.dashboard`: component dashboard for candidate dashboard/applications/AI credits/subscription select.
- `layouts.mobile`: mobile saved jobs.
- Filament panel layout: `/studai/*` admin.

CONFLICT: Pages that should share the same authenticated product shell are split between at least `layouts.dashboard`, `x-layouts.dashboard`, `x-app-layout`, and `layouts.app`.

### H5. BREADCRUMBS

- Present on: Not consistently detected across deep nested pages.
- Absent on: Deep pages such as employer applicant detail, job edit/detail, interview session/complete, resume ATS/editor, marketplace project management, Scout subpages.

### H6. PAGE TITLES / HEADINGS

- Do pages have consistent H1 heading styles? No. H1-like headings use `text-2xl`, `text-3xl`, `text-4xl`, `text-5xl`, `text-6xl`, inline CSS, and custom dashboard styles.
- Is the page title displayed in the browser tab consistently? Partially. `x-layouts.dashboard` accepts title props; legacy `@extends` pages rely on `@section('title')` inconsistently.

---

## SECTION I - MODAL AND OVERLAY AUDIT

Mechanical counts:

- Files matching modal/overlay patterns: 95.
- Reusable modal component exists: `resources/views/components/modal.blade.php`.
- Toast container exists: `resources/views/components/ui/toast-container.blade.php`.
- Cookie preferences overlay exists: `resources/views/components/cookie-consent.blade.php`.
- Filament modal styling exists in `resources/css/filament/studai/theme.css`.

### === MODAL/OVERLAY: Reusable Blade Modal ===
Type: Modal
Triggered by: Caller-dispatched Alpine modal events/forms.
File location: `resources/views/components/modal.blade.php`

Visual properties:
- Width: max-width prop sm/md/lg/xl/2xl.
- Background: white/dark mode.
- Overlay/backdrop: gray/opacity scrim.
- Border radius: rounded-lg/defined max-width shell.
- Shadow: modal/shadow style.
- Header style: caller-defined.
- Content padding: caller-defined/slot.
- Footer actions: caller-defined.
- Animation: scale/fade.

Close behaviour:
- Click outside: closes.
- Escape key: closes.
- Close button: caller-defined.

CONFLICT: Yes - many pages implement fixed overlays or inline modal markup instead of this component.

### === MODAL/OVERLAY: Toast Container ===
Type: Toast/snackbar
Triggered by: JavaScript `toast` custom event.
File location: `resources/views/components/ui/toast-container.blade.php`

Visual properties:
- Width: stack item width inherited.
- Background: type-based success/error/warning/info.
- Overlay/backdrop: none.
- Border radius: rounded container.
- Shadow: toast/card shadow.
- Header style: title/message.
- Content padding: toast-specific.
- Footer actions: optional action button.
- Animation: enter/exit transition.

Close behaviour:
- Click outside: not applicable.
- Escape key: not applicable.
- Close button: present.

CONFLICT: Yes - Livewire chat uses its own fixed top-right error toast; Filament uses `.fi-notification`; page JS may use custom alert banners.

### === MODAL/OVERLAY: Cookie Consent Preferences ===
Type: Banner/modal preferences overlay
Triggered by: Cookie banner preferences action.
File location: `resources/views/components/cookie-consent.blade.php`

CONFLICT: Yes - uses its own preference modal/binary toggle styling rather than shared modal/button/toggle components.

### === MODAL/OVERLAY: Filament Modals ===
Type: Modal/Dialog
Triggered by: Filament create/edit/actions.
File location: Filament resources/pages plus `resources/css/filament/studai/theme.css`

Visual properties:
- Width: Filament-defined.
- Background: Filament panel theme.
- Overlay/backdrop: Filament-defined.
- Border radius: 20px via `.fi-modal-window`.
- Shadow: Filament/default.
- Header style: gradient tint via `.fi-modal-header`.

CONFLICT: Yes - Filament modals differ from app modals.

---

## SECTION J - CHATBOT AND AI INTERFACE AUDIT

### === CHATBOT INTERFACE: Career Coach Chat ===
Location: Career coach feature.
File: `resources/views/livewire/career-coach-chat.blade.php`, `app/Livewire/CareerCoachChat.php`, `resources/js/app.js`
Purpose: AI career coaching conversation.
User type: Candidate

LAYOUT:
- Position on page: embedded/full feature panel.
- Width: inherits container.
- Height: scrollable message area.
- Container styling: Livewire/Blade card style.

CHAT WINDOW:
- Message area background: page-specific light surface.
- Message area height: scrollable; `messages-container` auto-scroll in JS.
- Scroll behaviour: auto-scroll after send/update.
- Empty state: initial suggestions/session prompt depending state.

USER MESSAGE BUBBLE:
- Background: indigo/blue-style bubble.
- Text colour: white.
- Border radius: rounded bubble.
- Position: right.
- Avatar/icon: absent or minimal.
- Timestamp: shown.
- Max width: constrained by chat layout.

AI/BOT MESSAGE BUBBLE:
- Background: light/assistant bubble.
- Text colour: dark.
- Border radius: rounded.
- Position: left.
- Avatar/icon: assistant/coach badge.
- Typing indicator: animated dots/spinner.
- Markdown rendering: yes, AI responses rendered with markdown helper.
- Code block styling: not central.

INPUT AREA:
- Position: bottom.
- Background: page/card.
- Input field styling: textarea, dynamic height.
- Placeholder text: chat-specific.
- Send button: icon/text.
- Send on Enter: yes.
- Multi-line input: yes via Shift+Enter.
- Attachment support: no.
- Voice input: yes.
- Character limit: not consistently visible.

SUGGESTED PROMPTS / QUICK REPLIES:
- Present: yes/contextual.
- Style: chips/buttons.
- When shown: before/contextual.

HEADER / TITLE BAR:
- Present: page/session header.
- Close/minimise: absent.

SPECIAL FEATURES:
- File upload in chat: absent.
- Message copy button: not consistently present.
- Message regenerate: absent.
- Clear conversation: session/history controls outside chat.
- Chat history: saved by backend.
- Source citations: absent.

MOBILE BEHAVIOUR:
Responsive through container; not a dedicated mobile full-screen chat.

PROBLEMS:
- Chat UI is unique and not reused by Orin/negotiation/network messaging.
- Voice input is available here but absent in other chatbots.

CONFLICT WITH OTHER CHATBOTS:
- Yes - different bubble colors, avatars, input controls, and loading states from Orin and negotiation chat.

### === CHATBOT INTERFACE: Orin Employer Onboarding Chat ===
Location: Employer onboarding.
File: `resources/views/employer/onboarding-chat.blade.php`
Purpose: Conversational employer/company profile setup.
User type: Employer

LAYOUT:
- Position on page: standalone page/panel.
- Width: full container.
- Height: chat container.
- Container styling: standalone custom HTML/CSS.

CHAT WINDOW:
- Message area background: custom chat surface.
- Message area height: scrollable.
- Scroll behaviour: JS-managed.
- Empty state: initial Orin greeting.

USER MESSAGE BUBBLE:
- Background: blue primary around `#1A73E8`.
- Text colour: white.
- Position: right.
- Avatar/icon: not standardized.
- Timestamp: hidden/unclear.

AI/BOT MESSAGE BUBBLE:
- Background: custom Orin bubble.
- Text colour: dark.
- Position: left.
- Avatar/icon: Orin identity/status.
- Typing indicator: 3-dot animation.
- Markdown rendering: no confirmed common renderer.

INPUT AREA:
- Position: bottom.
- Input field styling: custom.
- Placeholder text: onboarding-specific.
- Send button: custom blue.
- Send on Enter: JS-managed.
- Multi-line input: unclear.
- Attachment support: no.
- Voice input: no.

SUGGESTED PROMPTS / QUICK REPLIES:
- Present: conversation-driven/possibly contextual.

HEADER / TITLE BAR:
- Present: Orin AI title/status and completion percentage.
- Close/minimise: skip/defer button.

SPECIAL FEATURES:
- Profile completion percentage.
- Chat history: local/page session/backend depending controller.

MOBILE BEHAVIOUR:
Standalone responsive behavior not shared with other chats.

PROBLEMS:
- Uses standalone JS and `#1A73E8`, conflicting with Livewire chat and current brand token.
- Not connected to shared toast/input/button components.

CONFLICT WITH OTHER CHATBOTS:
- Yes - separate architecture and styling.

### === CHATBOT INTERFACE: Negotiation Coaching / Chatbot ===
Location: Negotiation feature.
File: `resources/views/negotiation/coaching.blade.php`, `resources/views/negotiation/chatbot.blade.php`
Purpose: Salary/offer negotiation coaching and conversational help.
User type: Candidate

LAYOUT:
- Position on page: embedded coaching page/chatbot page.
- Width: dashboard content container.
- Height: scrollable message panel where present.
- Container styling: custom negotiation cards/panels.

CHAT WINDOW:
- Message area background: page-specific.
- Message area height: scrollable.
- Empty state: coaching/session content.

USER MESSAGE BUBBLE:
- Background: page-specific user tone/message style.
- Text colour: variable.
- Position: message-type based.
- Avatar/icon: type labels/badges.

AI/BOT MESSAGE BUBBLE:
- Background: custom AI/coaching style.
- Text colour: variable.
- Position: left/typed.
- Typing indicator: not standardized.
- Markdown rendering: not shared.

INPUT AREA:
- Position: bottom/in section.
- Input field styling: custom form fields.
- Send button: custom.
- Voice/attachment: absent.

SUGGESTED PROMPTS / QUICK REPLIES:
- Present: suggestion cards.
- Style: cards/buttons, hover animation.

HEADER / TITLE BAR:
- Present: negotiation/coaching context and stage.

SPECIAL FEATURES:
- Tone badges.
- Stage progression dots.
- Confidence meter.

MOBILE BEHAVIOUR:
Dashboard responsive; no shared chat mobile shell.

PROBLEMS:
- Uses custom tone/stage visual language not present in other AI chats.
- Chat and coaching controls are not componentized.

CONFLICT WITH OTHER CHATBOTS:
- Yes - differs from career coach and Orin in colors, layout, stage UI, input behavior.

### === CHATBOT INTERFACE: Network Messaging Center ===
Location: Network/social feature.
File: `resources/views/livewire/network/messaging-center.blade.php`, `app/Livewire/Network/MessagingCenter.php`
Purpose: Human-to-human messaging hub.
User type: Candidate/Both social users

LAYOUT:
- Position on page: embedded messaging center.
- Width: dashboard content.
- Height: conversation layout.
- Container styling: Livewire component custom.

CHAT WINDOW:
- Message area background: component-specific.
- Message area height: scrollable.
- Scroll behaviour: Livewire-managed.
- Empty state: conversation selection/no messages.

USER MESSAGE BUBBLE:
- Background/text/position: messaging-specific.
- Avatar/icon: participants.
- Timestamp: present.

AI/BOT MESSAGE BUBBLE:
- Not applicable; no bot role.

INPUT AREA:
- Message compose input.
- Attachment support: yes via `$attachment` property.
- Voice input: no.

SPECIAL FEATURES:
- Search.
- Reply-to.
- Unread tracking.
- New conversation flow.

PROBLEMS:
- Human messaging UI differs from employer messages and AI chat components.

CONFLICT WITH OTHER CHATBOTS:
- Yes - same conversational pattern but separate implementation.

---

## SECTION K - DASHBOARD AUDIT

### === DASHBOARD: Candidate Main Dashboard ===
User type: Candidate
Route: `/dashboard`
File: `resources/views/dashboard/index.blade.php`

LAYOUT:
- Grid structure: responsive KPI grid plus content panels.
- Max width: dashboard layout constrained.
- Card/widget spacing: p-5/p-6/gap variants.

METRIC CARDS / KPI WIDGETS:
- Applications Remaining - number/card.
- Profile Completion - percentage/progress.
- AI Credits Remaining - number/card.
- Total Applications - number/card.
- Saved Jobs - number/card.

CHARTS AND DATA VISUALISATIONS:
- Progress bars and dashboard card visuals; no single shared chart component.

TABLES ON DASHBOARD:
- Recent applications/list; styling differs from employer/Filament tables.

QUICK ACTION BUTTONS:
- Browse jobs, edit profile, upgrade/credits/actions using StudAI or inline buttons.

NOTIFICATIONS / ALERTS:
- Subscription/usage cards.

RECENT ACTIVITY FEED:
- Recent applications and saved jobs panels.

PROBLEMS SPECIFIC TO THIS DASHBOARD:
- Own animation/card system.
- Uses component dashboard shell while many adjacent candidate pages use other layouts.

CONFLICTS WITH OTHER DASHBOARDS:
- Metric cards differ from employer, agent, analytics, Filament.

### === DASHBOARD: Employer Main Dashboard ===
User type: Employer
Route: `/employer/dashboard`
File: `resources/views/employer/dashboard/index.blade.php`

LAYOUT:
- Grid structure: hero + KPI grid + funnel/table panels.
- Max width: dashboard layout.
- Card/widget spacing: page-specific.

METRIC CARDS / KPI WIDGETS:
- Open roles.
- Total applications.
- Pending reviews.
- Shortlisted/hiring pipeline.

CHARTS AND DATA VISUALISATIONS:
- Hiring funnel/animated bars; page-specific chart styling.

TABLES ON DASHBOARD:
- Recent applications table.

QUICK ACTION BUTTONS:
- Create job, review applicants, analytics links.

NOTIFICATIONS / ALERTS:
- Dashboard cards/alerts.

RECENT ACTIVITY FEED:
- Recent applications.

PROBLEMS SPECIFIC TO THIS DASHBOARD:
- Heavy page-local animation and gradient treatment.
- Colors differ from current brand token set.

CONFLICTS WITH OTHER DASHBOARDS:
- Different metric/card style than candidate and Filament dashboards.

### === DASHBOARD: Agent Dashboard ===
User type: Candidate/Agent feature
Route: `/agent/dashboard`
File: `resources/views/agent/dashboard.blade.php`

LAYOUT:
- Grid structure: custom agent status/control layout.
- Max width: dashboard layout.
- Card/widget spacing: custom.

METRIC CARDS / KPI WIDGETS:
- Agent status, matches, applications, limits/statistics.

CHARTS AND DATA VISUALISATIONS:
- Metrics/status panels; custom animated gradient effects.

TABLES ON DASHBOARD:
- Recent applications/matches depending data.

QUICK ACTION BUTTONS:
- Configure, control agent, view applications.

PROBLEMS SPECIFIC TO THIS DASHBOARD:
- Dark gradient/orb-heavy visual style is unlike other operational dashboards.
- Custom animations and backgrounds are isolated.

CONFLICTS WITH OTHER DASHBOARDS:
- Metric and hero styles conflict with candidate/employer dashboard systems.

### === DASHBOARD: Analytics Dashboards ===
User type: Candidate/Employer/Both depending route
Route: `/analytics/*`
File: `resources/views/analytics/*.blade.php`

LAYOUT:
- Grid structure: KPI cards + charts + tables.
- Max width: dashboard layout.
- Card/widget spacing: variable.

METRIC CARDS / KPI WIDGETS:
- Salary, funnel, source, skills, time-to-hire metrics.

CHARTS AND DATA VISUALISATIONS:
- Chart.js/canvas detected in 43 chart-pattern files.
- Time-to-hire includes 4 canvas charts.
- Scout/analytics use custom charts/visuals.

TABLES ON DASHBOARD:
- Stage breakdown/bottleneck/benchmark tables.

PROBLEMS SPECIFIC TO THIS DASHBOARD:
- Chart colors and legends are page-specific.
- Loading/error states differ per chart page.

CONFLICTS WITH OTHER DASHBOARDS:
- Analytics cards/tables differ from employer dashboard, candidate dashboard, and Filament widgets.

### === DASHBOARD: Filament Admin Dashboard/Pages ===
User type: Admin
Route: `/studai/*`
File: `app/Filament/Pages/*.php`, `app/Filament/Widgets/*.php`, `resources/views/filament/pages/*.blade.php`

LAYOUT:
- Grid structure: Filament panel/widgets/resources.
- Max width: Filament-defined.
- Card/widget spacing: Filament-defined plus theme CSS.

METRIC CARDS / KPI WIDGETS:
- StatsOverview - total users, active jobs, verified companies, active subscriptions.
- JobApplicationsChart.
- RevenueChart.
- LatestApplications table widget.

CHARTS AND DATA VISUALISATIONS:
- Filament widgets.

TABLES ON DASHBOARD:
- Filament resource tables.

PROBLEMS SPECIFIC TO THIS DASHBOARD:
- Separate `.fi-*` CSS theme from app design system.

CONFLICTS WITH OTHER DASHBOARDS:
- Filament tables/cards/actions do not match app tables/cards/actions.

---

## SECTION L - TABLE AUDIT

Mechanical counts:

- Raw `<table>` elements: 75.
- Files with table patterns: 44.

### === TABLE: Employer Applicants Table ===
Shows: Applications/candidates.
Found in: `resources/views/employer/applicants/index.blade.php`

STRUCTURE:
- Columns: candidate, job, applied date, status, actions, scores depending page.
- Column widths: auto/custom CSS.
- Row height: auto.

STYLING:
- Container: custom `.app-table`.
- Header row: custom table header.
- Body row: hover state.
- Alternating rows: not consistently present.
- Selected row: via checkbox/bulk patterns.
- Cell padding: custom.

FEATURES:
- Sortable columns: limited/unclear.
- Filterable: status/job/search controls.
- Searchable: yes.
- Row selection: checkbox/bulk action in ATS contexts.
- Pagination: Laravel pagination.
- Export: separate route/action.

ROW ACTIONS:
- View, status change, notes, schedule/interview.

EMPTY STATE:
Applications will appear once candidates apply.

LOADING STATE:
Not standardized.

MOBILE BEHAVIOUR:
Likely horizontal scroll/table compression; no consistent card collapse.

CONFLICTS:
Differs from ranked table, Filament tables, background checks table, offer letters table.

### === TABLE: Ranked Candidates Table ===
Shows: AI-ranked applicants.
Found in: `resources/views/employer/applicants/ranked.blade.php`

STRUCTURE:
- Columns: rank, candidate, overall score, evaluation status, decision/actions.

STYLING:
- Container: dashboard card/table.
- Header row: page-specific.
- Row styling: status badges and action links.

FEATURES:
- Sortable/filterable unclear.
- Empty state present: rankings after Orin evaluation.
- Loading state absent/unclear.

CONFLICTS:
Same candidates as applicant table but different status styling and layout.

### === TABLE: Background Checks Table ===
Shows: Background check history.
Found in: `resources/views/background-checks/index.blade.php`

STRUCTURE:
- Columns: package/candidate/status/date/actions depending data.

STYLING:
- Container: dashboard table/card.
- Header/body: Tailwind table styling.
- Status: badges.

FEATURES:
- Pagination present.
- Empty/loading states partial.

CONFLICTS:
Status badges differ from applicant/status badges and Filament badges.

### === TABLE: Offer Letters Table ===
Shows: Offer letters.
Found in: `resources/views/offer-letters/index.blade.php`

STRUCTURE:
- Columns: candidate/job/status/dates/actions depending page.

STYLING:
- Container/card table.
- Header/rows: page-specific.
- Status options: draft, sent, viewed, accepted, declined, counter offered, withdrawn, expired.

FEATURES:
- Search/filter.
- Empty state present.
- Loading state absent/unclear.

CONFLICTS:
Offer status badges use their own mapping and differ from application statuses.

### === TABLE: Filament Resource Tables ===
Shows: Admin resources.
Found in: `app/Filament/Resources/*/Tables/*.php`, resource classes.

STRUCTURE:
- Columns: resource-specific.
- Column widths: Filament-defined.
- Row height: Filament-defined.

STYLING:
- Container: `.fi-ta-table` radius 12px.
- Header/rows: Filament defaults + custom hover `rgba(47,95,176,0.03)`.
- Pagination/search/filter: Filament components.

FEATURES:
- Sort/filter/search/actions/bulk actions depending resource.

CONFLICTS:
Admin tables differ from app HTML tables.

---

## SECTION M - ICON AUDIT

### M1. ICON LIBRARIES IN USE

- Heroicons Blade/dynamic components - top tags include `x-heroicon-o-academic-cap`, `x-heroicon-s-x-mark`, `x-heroicon-o-user-group`, `x-heroicon-o-chat-bubble-left-right`, `x-heroicon-o-inbox`, `x-heroicon-o-photo`, `x-heroicon-o-clock`, `x-heroicon-o-ellipsis-horizontal`, `x-heroicon-o-plus`, `x-heroicon-o-paper-clip`.
- Filament icon system - used implicitly by Filament resources/pages.
- Inline SVG - widespread.

### M2. INLINE SVG ICONS

- Count: 1,572 inline `<svg>` elements.
- Files: many Blade components/pages; high-use areas include dashboards, buttons, charts, auth, marketplace, employer, analytics, and components.

### M3. IMG TAG ICONS (not scalable)

- Count: 97 `<img>` elements.
- Files: marketing pages, company/profile/avatar/logo surfaces, emails/PDFs, marketplace.

### M4. ICON SIZE INCONSISTENCIES

- Common sizes include `w-3 h-3`, `w-3.5 h-3.5`, `w-4 h-4`, `w-5 h-5`, `w-6 h-6`, large dashboard icon wrappers, and inline SVG hardcoded dimensions.
- Same semantic actions (close, save, edit, view, delete, send) appear at different sizes depending page/component.

### M5. ICON COLOUR INCONSISTENCIES

- Icon colors follow current text color in many inline SVGs; semantic icons use gray, blue, indigo, green, red, purple, pink, orange, and Filament color classes.
- Send/primary icons vary between blue, indigo, white-on-blue, and custom chat colors.

### M6. MISSING ICONS

- Text-only actions in tables and forms would be visually inconsistent with icon-heavy dashboard cards, but the audit records this as inconsistency only.

---

## SECTION N - LOADING AND EMPTY STATES AUDIT

### N1. LOADING STATES

Mechanical scan:

- Files with loading pattern keywords: 92.
- Patterns found: `wire:loading`, `animate-spin`, `skeleton`, `shimmer`, `Loading...`, Chart.js/demo fallback.

Examples:

- Career Coach Chat: animated dots/spinner and Livewire loading.
- Application evaluation: loading question placeholder/spinner.
- Background Check Create: spinner SVG.
- Time-to-Hire Analytics: `Loading...` row in table body and demo-data fallback.
- Filament: built-in loading indicators styled by `.fi-loading-indicator`.
- `loading-skeleton` component exists but is not used uniformly.

FLAG: Many route pages do not contain explicit loading patterns; content is server-rendered or can flash/blank during JS/Livewire loads.

### N2. EMPTY STATES

Mechanical scan:

- Files with empty-state-like keywords: 244.
- Dedicated reusable empty-state component: Not found.

Examples:

- Employer applicants: message that applications appear once candidates apply.
- Ranked candidates: rankings appear after Orin evaluation.
- Talent pool: `No Candidates Yet` messaging.
- Negotiation dashboard: no strategies yet with CTA.
- Offer letters: no offer letters state with icon/text.

FLAG: Empty state phrasing, icon size, CTA placement, and styling differ by page.

### N3. ERROR STATES

Mechanical scan:

- Files with error/danger/red/error patterns: 260.
- Patterns found: `@error`, `$errors`, `text-red-*`, `bg-red-*`, `danger`, JS failed/try again messages, Livewire addError, Filament validation.

Examples:

- Career Coach Chat: fixed top-right error toast.
- Network Messaging: notification event dispatch with error type.
- Forms: validation errors with `x-input-error`, inline red text, or page-specific alert boxes.
- Application Evaluation: failure toast/message.

FLAG: Error states are present but fragmented across inline alerts, components, toasts, and Filament notifications.

---

## SECTION O - NOTIFICATION AND TOAST AUDIT

### O1. TOAST/SNACKBAR NOTIFICATIONS

- Library used: Custom Blade `x-ui.toast-container`, Livewire events, Filament notifications, standalone page JS.
- Position: Custom toast supports multiple positions; Career Coach uses fixed top-right; Filament uses Filament defaults.
- Types present: success, error, warning, info.
- Styling per type: custom toast uses green/red/yellow/blue; Filament uses gradient `rgba` backgrounds; inline alerts use Tailwind red/green/yellow/blue.
- Duration: custom toast default 4000ms; others vary.
- Dismissible: custom toast yes; Filament yes; inline alerts vary.
- CONFLICT: Yes - multiple notification systems exist.

### O2. INLINE ALERTS AND BANNERS

Alert/banner styles found:

- Success: green backgrounds (`bg-green-50`, `bg-green-100`, Filament rgba success).
- Error: red backgrounds (`bg-red-50`, `bg-red-100`, `text-red-*`, `danger`).
- Warning: yellow/orange backgrounds.
- Info: blue backgrounds.

CONFLICT: Same semantic alert type uses token colors, Tailwind default colors, Google colors, and Filament rgba gradients.

### O3. BADGE AND CHIP COMPONENTS

Badge/status styles found:

- `x-studai.badge`: default/primary/success/warning/error.
- `x-studai.chip`: default/primary/success/warning/error/purple/ai.
- Filament `.fi-badge`: pill badge with success/warning/danger rgba theme.
- Application statuses: pending/reviewing/shortlisted/rejected/hired use several custom classes and gradients.
- Offer statuses: draft/sent/viewed/accepted/declined/counter/withdrawn/expired.
- Skill/job chips: raw rounded-full tags.

CONFLICT: Same status can appear as gray/yellow/orange/blue/green/red/purple depending page.

---

## SECTION P - RESPONSIVE BEHAVIOUR AUDIT

Marketing pages:
- Mobile: stacked hero/cards/nav hamburger.
- Tablet: responsive grid.
- Desktop: marketing header and multi-column sections.
- Breaks: duplicate marketing files may not share identical breakpoints.

Candidate dashboard:
- Mobile: grids stack; dashboard shell controls depend on layout.
- Tablet: responsive columns.
- Desktop: KPI/content grid.
- Breaks: adjacent candidate pages using different layout wrappers change navigation behavior.

Job search:
- Mobile: filter/results likely stack; dedicated mobile swipe route exists.
- Tablet: filter/results may be side-by-side.
- Desktop: filter sidebar + results.
- Breaks: tables/list cards and filters are not standardized across mobile and desktop.

Employer dashboard/applicants:
- Mobile: tables/Kanban likely overflow or require horizontal scroll.
- Tablet: compressed grids/tables.
- Desktop: full dashboard/table/Kanban.
- Breaks: Kanban/list/ranked views do not share a mobile card fallback.

Analytics dashboards:
- Mobile: chart canvases can overflow or collapse depending page JS/CSS.
- Tablet: chart grids adjust inconsistently.
- Desktop: multi-chart layout.
- Breaks: legends/tooltips/axis labels are page-specific.

Marketplace:
- Mobile: card grids stack.
- Tablet/Desktop: multi-column cards.
- Breaks: opportunity cards differ from job/company cards and may have inconsistent action placement.

Filament:
- Mobile/tablet/desktop: Filament handles responsive panel behavior.
- Breaks: Visual style differs from app layouts.

GLOBAL MOBILE PROBLEMS:
- Authenticated product surfaces split across `layouts.dashboard`, `x-layouts.dashboard`, `x-app-layout`, `layouts.app`, and `layouts.mobile`, causing inconsistent mobile navigation.
- Tables and Kanban boards do not share a consistent mobile transformation.
- Button/input sizes and tap targets vary heavily due 563 button variants and 316 input variants.

---

## SECTION Q - CANDIDATE-SPECIFIC FEATURE AUDIT

### === CANDIDATE FEATURE: Job browsing / search / filter ===
Purpose: Candidate can find jobs, filter, view AI matches, save/apply.
Entry point: Dashboard/nav/jobs routes.
Pages involved: `resources/views/jobs/search.blade.php`, `jobs/show.blade.php`, `jobs/saved.blade.php`, Livewire search/mobile job browser.
Key screens: Search/filter, results cards, saved jobs, job detail, mobile swipe.
UI PROBLEMS:
- Job cards and buttons differ across desktop/mobile/saved/dashboard.
FEATURE COMPLETENESS:
- Built and working: yes/partially based on UI surfaces.
- UI complete: partially.
- Missing/broken from UI perspective: unified job card and consistent states.

### === CANDIDATE FEATURE: Job detail view ===
Purpose: Candidate reviews role/company and applies.
Entry point: Job cards/routes.
Pages involved: `resources/views/jobs/show.blade.php`.
Key screens: Job detail, apply CTA, tests/rounds.
UI PROBLEMS:
- Layout differs from application/evaluation flow.
FEATURE COMPLETENESS:
- Built and working: yes.
- UI complete: partially.

### === CANDIDATE FEATURE: Job application flow ===
Purpose: Submit application and complete screening.
Entry point: Job detail/public apply links.
Pages involved: `resources/views/apply/*.blade.php`, candidate/hiring-test views.
Key screens: Apply form, evaluation, results.
UI PROBLEMS:
- Public token and authenticated evaluation screens use different shells and form styles.
FEATURE COMPLETENESS:
- Built and working: yes/partially.
- UI complete: partially.

### === CANDIDATE FEATURE: Resume / profile builder ===
Purpose: Create resumes, profile, cover letters, ATS optimization.
Entry point: Dashboard/profile/resume routes.
Pages involved: `resources/views/resume/*.blade.php`, `profile/career/index.blade.php`, `livewire/profile-wizard.blade.php`.
Key screens: Resume list/create/edit/preview/ATS/cover letter/public.
UI PROBLEMS:
- Mixed layouts and form components.
FEATURE COMPLETENESS:
- Built and working: yes.
- UI complete: partially.

### === CANDIDATE FEATURE: Application tracking / status ===
Purpose: Track submitted applications.
Entry point: Dashboard/applications.
Pages involved: `resources/views/dashboard/applications.blade.php`.
Key screens: Status filters, application list.
UI PROBLEMS:
- Status badges differ from employer/application/Filament statuses.
FEATURE COMPLETENESS:
- Built and working: yes.
- UI complete: partially.

### === CANDIDATE FEATURE: Saved jobs ===
Purpose: Save and review job listings.
Entry point: Job cards/dashboard/mobile.
Pages involved: `jobs/saved.blade.php`, `mobile/saved-jobs.blade.php`, dashboard panels.
UI PROBLEMS:
- Saved state and card layout differ by surface.
FEATURE COMPLETENESS:
- Built and working: yes.
- UI complete: partially.

### === CANDIDATE FEATURE: AI career assistant / chatbot ===
Purpose: AI coaching conversation.
Entry point: Career coach routes.
Pages involved: `career-coach/*.blade.php`, `livewire/career-coach-chat.blade.php`.
UI PROBLEMS:
- Chat UI not shared with other AI chat surfaces.
FEATURE COMPLETENESS:
- Built and working: yes.
- UI complete: yes/partially.

### === CANDIDATE FEATURE: Negotiation strategist ===
Purpose: Offer/salary negotiation strategy and coaching.
Entry point: Negotiation routes/offers.
Pages involved: `resources/views/negotiation/*.blade.php`.
UI PROBLEMS:
- Separate gradient/chat/card language.
FEATURE COMPLETENESS:
- Built and working: yes.
- UI complete: partially.

### === CANDIDATE FEATURE: Interview preparation / video interview ===
Purpose: Practice interviews, video sessions, skills maps.
Entry point: Interview and video-interview routes.
Pages involved: `interview/*.blade.php`, `livewire/video-interview/*.blade.php`.
UI PROBLEMS:
- Multiple assessment/question UI variants.
FEATURE COMPLETENESS:
- Built and working: yes.
- UI complete: partially.

### === CANDIDATE FEATURE: Notifications, network, calendar, skills, gamification, company reviews, marketplace ===
Purpose: Supporting candidate workflows.
Entry point: Dashboard/nav.
Pages involved: notifications, network, calendar, skills, gamification, companies, marketplace views.
UI PROBLEMS:
- Each feature has local card/button/status patterns.
FEATURE COMPLETENESS:
- Built and working: yes/partially depending feature.
- UI complete: partially.

---

## SECTION R - EMPLOYER-SPECIFIC FEATURE AUDIT

### === EMPLOYER FEATURE: Company profile creation / editing ===
Purpose: Employer manages company profile.
Entry point: Employer onboarding/profile.
Pages involved: `employer/onboarding.blade.php`, `employer/onboarding-chat.blade.php`, `employer/profile/show.blade.php`, `employer/profile/edit.blade.php`.
UI PROBLEMS:
- Profile pages use `x-app-layout` while most employer pages use dashboard layout.
FEATURE COMPLETENESS:
- Built: yes.
- UI complete: partially.

### === EMPLOYER FEATURE: Job posting creation and management ===
Purpose: Create/edit/publish/manage jobs.
Entry point: Employer dashboard/jobs.
Pages involved: `employer/jobs/*.blade.php`, `employer/job-creator.blade.php`.
UI PROBLEMS:
- Job management surfaces split between raw forms, AI chat, and mixed layouts.
FEATURE COMPLETENESS:
- Built: yes.
- UI complete: partially.

### === EMPLOYER FEATURE: Candidate search and filtering / talent pool ===
Purpose: Find and manage candidate pools.
Entry point: Talent pool and Scout matching.
Pages involved: `employer/talent-pool/index.blade.php`, `scout/candidate-matching.blade.php`.
UI PROBLEMS:
- Candidate cards/tables differ from applicants and network profiles.
FEATURE COMPLETENESS:
- Built: yes.
- UI complete: partially.

### === EMPLOYER FEATURE: Prism/AI score filtering and evaluation ===
Purpose: Rank/evaluate candidates with AI signals.
Entry point: Applicants, Scout, ranked view.
Pages involved: `employer/applicants/show.blade.php`, `ranked.blade.php`, `scout/*.blade.php`.
UI PROBLEMS:
- AI score/bias/readiness visualizations are not shared.
FEATURE COMPLETENESS:
- Built: yes.
- UI complete: partially.

### === EMPLOYER FEATURE: Application review and management ===
Purpose: Review applications, status, notes, pipeline.
Entry point: Employer applicants routes.
Pages involved: `employer/applicants/index.blade.php`, `show.blade.php`, `kanban.blade.php`, `ranked.blade.php`.
UI PROBLEMS:
- Same entity displayed as table, Kanban card, ranked row, detail page with conflicting UI.
FEATURE COMPLETENESS:
- Built: yes.
- UI complete: partially.

### === EMPLOYER FEATURE: Interview scheduling ===
Purpose: Schedule and evaluate interviews.
Entry point: Applicants/interviews routes.
Pages involved: `employer/interviews/*.blade.php`, calendar/schedule pages.
UI PROBLEMS:
- Date/time controls and schedule layouts differ across employer, calendar, public schedule.
FEATURE COMPLETENESS:
- Built: yes.
- UI complete: partially.

### === EMPLOYER FEATURE: Messaging / chat with candidates ===
Purpose: Employer/candidate communication.
Entry point: Employer messages.
Pages involved: `employer/messages/index.blade.php`, missing/different conversation view if routed.
UI PROBLEMS:
- Employer messaging differs from network messaging and chatbots.
FEATURE COMPLETENESS:
- Built: partially.
- UI complete: partially.

### === EMPLOYER FEATURE: AI candidate matching / recommendations / Scout ===
Purpose: AI hiring intelligence.
Entry point: Scout routes.
Pages involved: all `resources/views/scout/*.blade.php`.
UI PROBLEMS:
- Scout suite uses separate visual system from employer dashboard.
FEATURE COMPLETENESS:
- Built: yes.
- UI complete: partially.

### === EMPLOYER FEATURE: Hiring pipeline / kanban board ===
Purpose: Move candidates through hiring stages.
Entry point: Employer applicants Kanban.
Pages involved: `employer/applicants/kanban.blade.php`, `partials/kanban-card.blade.php`.
UI PROBLEMS:
- Mobile behavior likely horizontal/overflow; status colors differ from table/ranked view.
FEATURE COMPLETENESS:
- Built: yes.
- UI complete: partially.

### === EMPLOYER FEATURE: Analytics and reporting ===
Purpose: Hiring metrics, time-to-hire, funnels, source attribution, diversity/team/job analytics.
Entry point: Employer dashboard/analytics.
Pages involved: `employer/dashboard/analytics.blade.php`, `analytics/*.blade.php`, admin analytics.
UI PROBLEMS:
- Chart/table/card styling not unified.
FEATURE COMPLETENESS:
- Built: yes.
- UI complete: partially.

### === EMPLOYER FEATURE: Billing/subscription management ===
Purpose: Manage plan and payment.
Entry point: subscriptions/payments routes.
Pages involved: `subscriptions/*.blade.php`, `payments/*.blade.php`.
UI PROBLEMS:
- Subscription pages mix marketing/app/dashboard layouts.
FEATURE COMPLETENESS:
- Built: yes.
- UI complete: partially.

---

## SECTION S - INCONSISTENCY MASTER LIST

### S1. BUTTON INCONSISTENCIES

- Total distinct button variants found: 563.
- Excess variants:
  - StudAI primary pill: `bg-google-blue-600` / `#2f5fb0`.
  - Legacy Breeze primary: gray/dark rounded-md uppercase.
  - Inline indigo primary: `bg-indigo-600`.
  - Inline blue primary: `bg-blue-600`.
  - Green success/CTA used as primary.
  - Purple/orange marketplace/module CTAs.
  - `btn-primary`, `btn btn-primary`, `tab-button`, `filter-btn`, `gig-tab`, custom dismiss/AI buttons.

### S2. COLOUR INCONSISTENCIES

- Total distinct colour values used: 411 hex values.
- Colours used for primary that are different: `#2f5fb0`, `#1A73E8`, `#6366f1`, `#4f46e5`, `#3b82f6`, `#1c344d`, `bg-indigo-600`, `bg-blue-600`, `bg-google-blue-600`.
- Colours used for error/danger that are different: `#cf3a3a`, `#EA4335`, `bg-red-600`, `text-red-500`, `text-red-600`, `#ef4444`-style Filament rgba.
- Colours used for success that are different: `#1f8a5b`, `#10b981`, `#16a34a`, `#22c55e`, `#34a853`, `bg-green-500`, `bg-green-600`.
- Colours used for text on white that are different: `#15233a`, `#374151`, `#6b7280`, `text-gray-900`, `text-gray-800`, `text-gray-700`, `text-gray-600`, `text-ink-primary`, `text-ink-secondary`.
- Hardcoded colours: 411 distinct hex values, top examples listed in B1.

### S3. TYPOGRAPHY INCONSISTENCIES

- Page heading (H1) distinct sizes: `text-2xl`, `text-3xl`, `text-4xl`, `text-5xl`, `text-6xl`, inline custom sizes.
- Section heading (H2) distinct sizes: `text-lg`, `text-xl`, `text-2xl`, `text-3xl`.
- Body text distinct sizes: `text-xs`, `text-sm`, `text-base`, `text-lg`.
- Label text distinct sizes: `text-xs`, `text-sm`, label-caps 11px, uppercase legacy text-xs.

### S4. SPACING INCONSISTENCIES

- Card padding distinct values: `p-3`, `p-4`, `p-5`, `p-6`, `p-8`.
- Page margin/padding distinct values: `px-4`, `sm:px-6`, `lg:px-8`, `px-6`, `px-8`, `xl:px-12`, local containers.
- Section spacing: `mb-4`, `mb-6`, `mb-8`, `space-y-4`, `space-y-6`, `gap-3`, `gap-4`, `gap-6`.

### S5. BORDER RADIUS INCONSISTENCIES

- Buttons: `rounded-md`, `rounded-lg`, `rounded-xl`, `rounded-full`, raw `btn`.
- Cards: `rounded-lg`, `rounded-xl`, `rounded-2xl`, `rounded-3xl`, token card 16px.
- Inputs: `rounded-md`, `rounded-lg`, `rounded-xl`, token input 12px.
- Modals: `rounded-lg`, `rounded-xl`, `rounded-2xl`, Filament 20px, token modal 20px.

### S6. CHATBOT INCONSISTENCIES

- Number of distinct chatbot/conversational UIs: 4 primary (Career Coach, Orin, Negotiation, Network Messaging), plus employer messaging and AI textarea as related conversational/generative surfaces.
- Differences:
  - Architecture: Livewire vs standalone JS vs inline Blade vs Filament/page JS.
  - Bubbles: indigo/blue, Orin blue `#1A73E8`, negotiation tone colors, messaging participant bubbles.
  - Input: textarea/voice, single-line chat input, form textarea, attachment-enabled messaging.
  - Loading: dots/spinner/typing indicator/none.
  - Header/status: Orin online badge vs coach session header vs negotiation stage panel.

### S7. COMPONENT DUPLICATION

- `studai/button` vs `primary-button`/`secondary-button`/`danger-button` - all buttons.
- `studai/input` vs `text-input` vs raw inputs - all form inputs.
- `studai/badge` vs inline status badges vs Filament badges - all statuses.
- `studai/card` vs dashboard cards vs employer cards vs Scout cards vs marketplace cards - all card shells.
- `studai/stat-card` vs candidate dashboard metric cards vs employer metric cards vs Filament StatsOverview - all KPI widgets.
- `modal` vs inline fixed overlays vs Filament modals vs cookie consent modal - all dialogs.
- `ui/toast-container` vs Livewire error toast vs Filament notifications vs inline alerts - all notifications.
- `ProfileWizard`/`ui/step-wizard` vs employer onboarding wizard vs job wizard - all step flows.
- CareerCoachChat vs Orin chat vs negotiation chat vs network messaging - all conversational UIs.
- Candidate job card vs employer job row/card vs marketplace opportunity card vs Filament job table row - all opportunity displays.

### S8. MISSING STATES

- No loading state: Approximately 296 of 388 app GET route surfaces do not have an explicit loading keyword/pattern in scanned templates/classes.
- No empty state: Approximately 144 app GET route surfaces or page templates lack a clear empty-state phrase/pattern.
- No error state: Error patterns exist broadly, but many chart/dashboard/list pages rely on server render or JS fallback without standardized recovery UI.

### S9. PAGES WITH THE MOST PROBLEMS

1. Employer applicants suite - 14 problems.
2. Job search/detail/saved/mobile - 13 problems.
3. Employer dashboard - 12 problems.
4. Candidate dashboard/applications/AI credits - 11 problems.
5. Resume/profile builder - 11 problems.
6. Scout suite - 10 problems.
7. Marketplace suite - 10 problems.
8. Career coach/AI chat - 9 problems.
9. Analytics dashboards - 9 problems.
10. Negotiation suite - 8 problems.

### S10. FEATURES WITH INCOMPLETE UI

- Employer messaging conversation detail - route references imply conversation view, but audited visible template coverage is incomplete/inconsistent.
- Job wizard templates/preview - controller references views not present in the visible tree under `resources/views/employer/jobs/wizard`.
- Some controller-returned views are referenced but not visible in scanned tree, including `applications.assistant.*`, `assessments.*`, `certificates.*`, `calendar.booking`, `calendar.event`, `settings.two-factor*`, `newsletter.unsubscribed`, `employer.webhooks.*`, and `offer-letters.templates.*`/`benefits.*` where not present in resources tree.
- Dark mode support is partial: 105 files contain dark-mode patterns, but many pages use light-only `bg-white`/`text-gray-*` assumptions.

---

## SECTION T - OVERALL AUDIT SUMMARY

### T1. TOTAL PAGES AUDITED

388 app GET route surfaces, with 368 Blade templates and 24 Livewire components included in the scan.

### T2. TOTAL COMPONENTS AUDITED

49 Blade components under `resources/views/components`, plus 24 Livewire components, 4 View component classes, and 110 Filament UI/resource/page/widget classes inventoried.

### T3. TOTAL CHATBOT INTERFACES AUDITED

4 primary conversational interfaces: Career Coach Chat, Orin Employer Onboarding Chat, Negotiation Coaching/Chatbot, Network Messaging Center. Employer messaging and AI textarea are related conversational/generative interfaces.

### T4. TOTAL DASHBOARD VIEWS AUDITED

At least 12 dashboard families: candidate dashboard, applications, AI credits, employer dashboard, employer analytics, talent pipeline, agent dashboard, analytics dashboard suite, negotiation dashboard, skills dashboard, gamification dashboard, marketplace employer/freelancer dashboards, Filament admin pages/widgets.

### T5. TOTAL UI PROBLEMS FOUND

126 documented problems/conflicts/flags across sections.

### T6. TOTAL COMPONENT DUPLICATIONS FOUND

18 major duplication families.

### T7. TOTAL COLOUR VALUES IN USE

411 distinct hex values detected.

### T8. TOTAL BUTTON VARIANTS IN USE

563 distinct button class/component usage strings detected.

### T9. PAGES WITH NO LOADING STATE

Approximately 296 route surfaces lack explicit loading-state patterns based on keyword/component scan.

### T10. PAGES WITH NO EMPTY STATE

Approximately 144 route surfaces/page templates lack clear empty-state patterns based on keyword/component scan.

OVERALL ASSESSMENT:
The UI is functionally broad but visually fragmented. The three biggest systemic problems are: duplicated layout systems across authenticated pages, uncontrolled component variants for buttons/inputs/cards/statuses, and parallel AI/chat/dashboard visual systems that do the same jobs differently. The token layer is extensive, but the actual templates still contain 411 distinct hex values and thousands of raw class strings, so the design system is present in configuration but not consistently enforced in screens.

OBSERVED DENSITY ORDER FOR DOCUMENTED INCONSISTENCIES:

1. Employer applicant/job management - highest density of table/card/status/layout conflicts.
2. Candidate job search/apply/resume flow - same entities and actions look different across pages.
3. AI/chat surfaces - four distinct chat UIs fragment the AI experience.
4. Dashboard/KPI system - candidate, employer, agent, analytics, and Filament cards use separate visual grammars.
5. Forms/buttons/status badges - 563 button usages and 316 input usages affect nearly every page.

AUDIT COMPLETE — 388 pages, 49 components, 126 problems documented.
