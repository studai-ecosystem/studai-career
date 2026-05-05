# StudAI Career

> AI-powered career development and job marketplace platform built on Laravel 12

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php)](https://php.net)
[![Filament](https://img.shields.io/badge/Filament-4.x-FDAE4B?logo=data:image/svg+xml;base64,)](https://filamentphp.com)
[![License](https://img.shields.io/badge/License-Proprietary-blue)](LICENSE)

---

## What Is This

StudAI Career connects job seekers with employers through AI-powered matching, automated applications, and career development tools.

**For job seekers:** Find jobs, build resumes, practice interviews, negotiate salaries, track applications — with AI assistance at every step. An autonomous agent can even discover and apply to jobs on your behalf.

**For employers:** Post jobs, screen candidates with AI, predict hiring success, eliminate bias, manage talent pipelines — via the S.C.O.U.T. (Smart Candidate Optimization & Universal Talent) system.

**Target market:** India (primary), with INR pricing and Indian job market focus.

---

## Table of Contents

- [Platform Scale](#platform-scale)
- [Features](#features)
- [Architecture](#architecture)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Data Model](#data-model)
- [API Reference](#api-reference)
- [Workflow Diagrams](#workflow-diagrams)
- [Getting Started](#getting-started)
- [Configuration](#configuration)
- [Testing](#testing)
- [Deployment](#deployment)
- [Documentation](#documentation)
- [Current Status](#current-status)

---

## Platform Scale

| Metric | Count |
|--------|-------|
| Eloquent Models | 233 |
| Database Migrations | 83 |
| Service Classes | 120+ |
| HTTP Controllers | 79 |
| Filament Admin Resources | 93 |
| Background Jobs | 34 |
| Livewire Components | 24 |
| Blade View Files | 292 |
| API Routes | 150+ |
| Web Routes | 100+ |
| Domain Events | 28 |
| Notification Classes | 21 |
| Test Files | 65+ |
| Model Factories | 10 |
| Database Seeders | 15 |

---

## Features

### Job Seeker Features

| Feature | Description |
|---------|-------------|
| **AI Job Matching** | Semantic search using OpenAI embeddings with match scores (0-100%) and per-job skill gap identification |
| **Autonomous Job Agent** | Configurable agent that discovers jobs via RSS feeds, scores matches, and auto-applies with safety guardrails (kill switch, daily caps, human-in-the-loop approval, company blacklist) |
| **Resume Builder** | Interactive builder with AI-generated summaries, automatic skill extraction, ATS optimization analysis, PDF/DOCX export |
| **Interview Preparation** | AI-generated behavioral, technical, and role-specific questions. Answer evaluation with scoring. Video interview recording with AI analysis (content, confidence, clarity, eye contact) |
| **Salary Negotiation** | AI strategy generation, scenario-based practice, coaching sessions, counter-offer recommendations, market salary benchmarking |
| **Skill Development** | AI skill gap analysis, personalized learning paths, curated resources, assessments, verifiable certificates and badges |
| **Career Coach** | AI chat with markdown rendering, session management, voice input support |
| **Market Intelligence** | Salary insights, skill trends, competitive analysis, role predictions, job market heatmap |
| **Gamification** | Points, achievement badges, daily challenges, leaderboards, XP leveling system |
| **Social Networking** | Activity feed, connections, groups, events (virtual/in-person/hybrid), mentorship hub, direct messaging |
| **Company Reviews** | Star ratings, salary reports, interview experience sharing (Glassdoor-style) |
| **PWA Support** | Installable, offline-first architecture with IndexedDB, Tinder-style swipe job browser, background sync |

### Employer Features (S.C.O.U.T.)

| Feature | Description |
|---------|-------------|
| **ATS (Applicant Tracking)** | Job posting wizard, application funnel, bulk messaging, status management, activity logging |
| **AI Candidate Screening** | Automated resume analysis, skill extraction, experience scoring, auto-shortlisting |
| **Predictive Analytics** | AI-generated insights: success probability, tenure forecast, productivity estimate, flight risk, development plan, onboarding plan |
| **Bias Elimination** | Candidate data anonymization, bias audits, fairness metrics, proxy discrimination alerts, decision explanations, diversity analytics |
| **Talent Pipelines** | Pipeline creation, silver medalist tracking, candidate stage advancement, passive candidate discovery |
| **Assessments** | Dynamic skill assessments and behavioral intelligence assessments with AI evaluation |
| **Background Checks** | Multi-provider (Checkr, Sterling, GoodHire), FCRA-compliant workflows, adverse action management |
| **Video Interviews** | Scheduling, browser-based recording, AI-powered response analysis, transcription |
| **Corporate DNA Decoder** | Company culture analysis, hiring pattern identification, success factor extraction |

### Platform Features

| Feature | Description |
|---------|-------------|
| **Triple Payment Gateway** | Razorpay (India, UPI/Card/NetBanking), PayU (India), Stripe (Global) |
| **Subscription Plans** | Free (₹0), Pro (₹499/mo), Enterprise (₹1,499/mo) with feature gating |
| **Admin Panel** | 93 Filament resources, AI usage monitoring, agent kill switch, queue monitor, revenue analytics |
| **RBAC** | 4 roles (super_admin, admin, employer, job_seeker), 72 permissions across 13 categories |
| **2FA** | TOTP-based two-factor authentication |
| **API** | RESTful API with Sanctum auth, custom API tokens for third-party integrations, rate limiting |
| **Event System** | 28 domain events, 13 listener/subscriber classes for gamification, logging, notifications, search indexing |

---

## Architecture

### System Overview

```
┌──────────────────────────────────────────────────────────────┐
│                       CLIENT LAYER                            │
│                                                               │
│   Blade + Alpine.js    Livewire (24)    PWA (Service Worker) │
│   Filament Admin       API Clients      Mobile Views          │
└──────────────────────────┬───────────────────────────────────┘
                           │
┌──────────────────────────▼───────────────────────────────────┐
│                    APPLICATION LAYER                           │
│                                                               │
│   Controllers (79)    Filament Resources (93)    Middleware   │
│   Form Requests       Policies                   Events (28) │
└──────────────────────────┬───────────────────────────────────┘
                           │
┌──────────────────────────▼───────────────────────────────────┐
│                      SERVICE LAYER (120+)                      │
│                                                               │
│   AI Services          S.C.O.U.T. Services    Core Services  │
│   ├─ AIService         ├─ DNADecoderService   ├─ JobMatching │
│   ├─ CircuitBreaker    ├─ PredictiveAnalytics ├─ Application │
│   ├─ PromptRegistry    ├─ BiasElimination     ├─ Payment     │
│   ├─ ResumeAI          ├─ TalentPipeline      ├─ Analytics   │
│   ├─ InterviewGen      ├─ PassiveCandidate    └─ Search      │
│   ├─ NegotiationCoach  └─ ResumeAnalyzer                     │
│   └─ CareerCoach                                              │
│                                                               │
│   Agent Services       Integration Services                   │
│   ├─ JobAggregation    ├─ BackgroundCheck                    │
│   ├─ RSSJobFeed        ├─ VideoInterview                     │
│   ├─ JobSourceScoring  ├─ PushNotification                   │
│   └─ AgentLearning     └─ SocialAuth                         │
└──────────────────────────┬───────────────────────────────────┘
                           │
┌──────────────────────────▼───────────────────────────────────┐
│                       DATA LAYER                              │
│                                                               │
│   MySQL 8.0+        Redis 6.0+        Meilisearch 1.5+      │
│   (233 models)      (cache/session)   (search engine)        │
│                                                               │
│   Queue (34 jobs)   File Storage      Scheduled Tasks        │
└──────────────────────────┬───────────────────────────────────┘
                           │
┌──────────────────────────▼───────────────────────────────────┐
│                    EXTERNAL SERVICES                           │
│                                                               │
│   Azure OpenAI (GPT-5.1)     Razorpay / PayU / Stripe       │
│   Azure Anthropic (Claude)    Checkr / Sterling / GoodHire   │
│   RSS Feeds (RemoteOK, etc.)  Email Provider                 │
└──────────────────────────────────────────────────────────────┘
```

### AI Architecture

The AI layer uses a resilient multi-provider design:

```
Request → AIService
              │
              ├─ Azure OpenAI (GPT-5.1) ── PRIMARY
              │       │
              │       └─ CircuitBreakerService monitors failures
              │               │
              │               └─ On repeated failures → automatic failover
              │
              └─ Azure Anthropic (Claude Sonnet 4.5) ── FALLBACK

Prompts managed by PromptRegistryService:
  - Database-stored, version-controlled
  - Admin-editable via Filament panel
  - No hardcoded prompt strings in business logic
```

### Agent Safety Architecture

The autonomous job agent has multiple safety layers:

```
Agent Activation
    │
    ├─ AgentKillSwitchMiddleware ── Admin can disable ALL agents globally
    │
    ├─ Daily Hard Cap ── Max applications per user per day
    │
    ├─ Company Blacklist ── User-managed block list
    │       └─ JobMatchingService.isCompanyBlacklisted()
    │
    ├─ Match Threshold ── Score must exceed configured minimum
    │
    ├─ Human-in-the-Loop ── Optional approval for high-scoring matches
    │       └─ ProcessAutoApplications.requiresApprovalForMatch()
    │
    ├─ Job Source Scoring ── 6 weighted quality factors
    │
    └─ Audit Logging ── Every action logged with correlation ID
            └─ AgentAuditService → agent_audit_logs table
```

### Event-Driven Processing

28 domain events drive cross-cutting concerns:

| Event Category | Events | Subscribers |
|---------------|--------|-------------|
| **User Lifecycle** | UserRegistered, ProfileCompleted, ResumeUploaded, ResumeAnalyzed | AwardGamificationPoints, UpdateSearchIndex |
| **Applications** | ApplicationSubmitted, ApplicationStatusChanged, JobApplied, JobSaved | SendNotification, AwardPoints, UpdateSearchIndex |
| **Payments** | PaymentInitiated, PaymentSucceeded, PaymentFailed, SubscriptionActivated, SubscriptionCanceled | LogPaymentActivity, NotifyOnSubscriptionChange |
| **Agent** | AgentActivated, AgentDeactivated, AgentJobDiscovered, AgentJobMatched, AgentApplicationSubmitted | LogAgentActivity |
| **Career** | InterviewStarted, InterviewCompleted, NegotiationCompleted, SkillGapIdentified, SkillAssessmentPassed | NotifyOnCareerMilestone, AwardPoints |
| **S.C.O.U.T.** | CandidateShortlisted, PredictionGenerated, BiasAuditCompleted | LogScoutActivity |

---

## Technology Stack

### Backend

| Component | Technology | Version |
|-----------|------------|---------|
| Framework | Laravel | 12.x |
| Language | PHP | 8.2+ |
| Admin Panel | Filament | 4.x |
| Reactivity | Livewire | 3.x |
| Auth Backend | Laravel Fortify | 1.31+ |
| API Auth | Laravel Sanctum | 4.2+ |
| RBAC | Spatie Permissions | 6.22+ |
| PDF Generation | DomPDF | 3.1+ |
| Search | Laravel Scout + Meilisearch | 10.20+ / 1.5+ |

### Frontend

| Component | Technology | Version |
|-----------|------------|---------|
| Templates | Blade | (built-in) |
| JavaScript | Alpine.js | 3.4+ |
| CSS | Tailwind CSS | 3.4+ |
| Build Tool | Vite | 7.0+ |
| Charts | Chart.js | 4.x (CDN) |
| Maps | Leaflet.js | 1.9.4 (CDN) |
| Network Graph | vis-network | 9.x (CDN) |

### Database & Infrastructure

| Component | Technology | Version |
|-----------|------------|---------|
| Primary DB | MySQL | 8.0+ |
| Cache | Redis | 6.0+ |
| Search Engine | Meilisearch | 1.5+ |
| Queue | Database driver (Horizon planned) | — |
| Hosting | Azure Web Apps | — |
| CI/CD | GitHub Actions | — |
| Monitoring | Sentry (planned) | — |

### AI & ML

| Component | Technology | Details |
|-----------|------------|---------|
| Primary AI | Azure OpenAI | GPT-5.1, API v2024-12-01-preview |
| Fallback AI | Azure Anthropic | Claude Sonnet 4.5 |
| Resilience | CircuitBreakerService | Auto-failover between providers |
| Prompt Mgmt | PromptRegistryService | Database-stored, version-controlled |
| Embeddings | OpenAI | Stored in MySQL JSON columns |
| PHP SDK | openai-php/laravel | v0.17.1 |

### Payments

| Gateway | Region | Methods |
|---------|--------|---------|
| Razorpay | India | Card, UPI, NetBanking, Wallet |
| PayU | India | Card, UPI, NetBanking |
| Stripe | Global | Card, ACH, SEPA, Apple/Google Pay |

### Key Dependencies

```json
{
  "laravel/framework": "^12.0",
  "filament/filament": "^4.1",
  "laravel/scout": "^10.20",
  "meilisearch/meilisearch-php": "^1.16",
  "openai-php/laravel": "^0.17.1",
  "laravel/sanctum": "^4.2",
  "laravel/fortify": "^1.31",
  "razorpay/razorpay": "^2.9",
  "spatie/laravel-permission": "^6.22",
  "barryvdh/laravel-dompdf": "^3.1"
}
```

---

## Project Structure

```
studai-career/
├── app/
│   ├── Actions/                  # Single-purpose business actions
│   ├── Console/                  # Artisan commands
│   ├── Events/                   # 28 domain event classes
│   ├── Exceptions/               # Exception handlers
│   ├── Filament/
│   │   ├── Resources/            # 93 CRUD resources
│   │   ├── Pages/                # Custom admin pages
│   │   └── Widgets/              # Dashboard widgets
│   ├── Helpers/                  # Helper classes
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── API/              # RESTful API controllers
│   │   │   ├── Admin/            # Admin controllers
│   │   │   ├── Auth/             # Authentication controllers
│   │   │   └── Employer/         # Employer portal controllers
│   │   ├── Middleware/           # Auth, RBAC, rate limiting, agent kill switch
│   │   └── Requests/            # Form request validation classes
│   ├── Jobs/                     # 34 queue jobs
│   ├── Listeners/                # Event listeners & subscribers
│   ├── Mail/                     # Mailable classes
│   ├── Models/                   # 233 Eloquent models
│   ├── Notifications/            # 21 notification classes
│   ├── Policies/                 # Authorization policies
│   ├── Providers/                # Service providers
│   ├── Services/                 # 120+ business logic services
│   │   ├── AI/                   # AIService, CircuitBreaker, PromptRegistry
│   │   ├── Agent/                # Job aggregation, RSS feeds, agent learning
│   │   ├── ATS/                  # ATS integration services
│   │   ├── Calendar/             # Calendar integration
│   │   ├── Interview/            # Interview question generation, evaluation
│   │   ├── Payment/              # Razorpay, PayU, Stripe gateway services
│   │   ├── Scout/                # S.C.O.U.T. employer intelligence services
│   │   └── Search/               # Search services
│   └── Traits/                   # Shared traits
│
├── config/                       # 30+ configuration files
│   ├── ai.php                    # AI provider config
│   ├── payment.php               # Payment gateway config
│   ├── scout.php                 # Meilisearch config
│   └── permission.php            # RBAC config
│
├── database/
│   ├── factories/                # 10 model factories
│   ├── migrations/               # 83 database migrations
│   └── seeders/                  # 15 database seeders
│
├── docs/                         # Documentation (10+ files)
│
├── public/
│   ├── service-worker.js         # PWA service worker
│   ├── manifest.json             # PWA manifest
│   └── icons/                    # PWA app icons
│
├── resources/
│   ├── css/
│   │   ├── app.css               # Main Tailwind styles
│   │   └── filament/theme.css    # Admin theme
│   ├── js/
│   │   ├── app.js                # Main JS (Alpine.js)
│   │   ├── bootstrap.js          # Axios + CSRF setup
│   │   └── pwa.js                # PWA install handler
│   └── views/                    # 292 Blade templates
│       ├── admin/                # Admin pages
│       ├── analytics/            # Career analytics (Chart.js)
│       ├── auth/                 # Login, register, 2FA, verify
│       ├── companies/            # Company profiles, reviews
│       ├── components/           # 43 reusable Blade components
│       ├── dashboard/            # User dashboard
│       ├── employer/             # Employer ATS portal
│       ├── gamification/         # Leaderboards, badges, challenges
│       ├── interview/            # Interview prep
│       ├── jobs/                 # Job search, listings, apply
│       ├── layouts/              # 7 layout templates
│       ├── livewire/             # Livewire component views
│       ├── market/               # Market intelligence
│       ├── negotiation/          # Salary negotiation
│       ├── network/              # Social networking
│       ├── pages/                # Marketing & legal pages
│       ├── payments/             # Payment flow
│       ├── profile/              # User profile
│       ├── resume/               # Resume builder
│       ├── scout/                # S.C.O.U.T. employer views
│       └── skills/               # Skill development
│
├── routes/
│   ├── web.php                   # 100+ web routes
│   ├── api.php                   # 150+ API routes
│   ├── console.php               # Artisan commands
│   └── admin_analytics.php       # Admin analytics routes
│
├── tests/
│   ├── Feature/                  # Feature tests
│   └── Unit/                     # Unit tests
│
├── .github/
│   └── workflows/
│       └── deploy.yml            # CI/CD: GitHub Actions → Azure Web Apps
│
├── composer.json                 # PHP dependencies
├── package.json                  # Node dependencies
├── vite.config.js                # Vite build config
├── tailwind.config.js            # Tailwind CSS config (~730 lines)
└── phpunit.xml                   # Test config
```

### Entry Points

| Entry Point | URL | Purpose |
|------------|-----|---------|
| Web App | `http://localhost:8000` | Main web interface |
| Admin Panel | `/admin` | Filament administration |
| API | `/api/*` | Internal frontend API |
| Third-Party API | `/api/v1/*` | External integrations with API token auth |
| Health Check | `/health` | Infrastructure health |

---

## Data Model

### Core Entity Relationships

```
User
 ├── Profile (1:1)
 ├── Applications (1:N) ──→ Job ──→ Company
 │       ├── ApplicationNotes
 │       ├── StatusHistory
 │       └── SuccessPrediction
 ├── Resumes (1:N)
 ├── UserSkills (1:N)
 ├── SkillGaps (1:N) ──→ LearningPaths ──→ LearningResources
 ├── InterviewSessions (1:N) ──→ Questions ──→ Responses
 ├── NegotiationStrategies (1:N) ──→ Sessions ──→ Messages
 ├── AgentConfiguration (1:1) ──→ DiscoveredJobs ──→ AutoApplications
 ├── CareerCoachSessions (1:N)
 ├── UserSubscription (1:1) ──→ SubscriptionPlan
 └── PaymentTransactions (1:N)

Company
 ├── Jobs (1:N) ──→ Applications
 ├── DNAProfile (1:1)
 ├── HiringPatterns (1:N)
 ├── Reviews (1:N)
 └── BiasAuditResults (1:N)

Assessment ──→ Questions ──→ Responses
TalentPipeline ──→ PipelineCandidates
BackgroundCheck ──→ CheckItems ──→ AdverseActions
```

### Key Database Tables

| Category | Tables |
|----------|--------|
| **Users** | users, profiles, social_accounts, api_tokens, two_factor_authentications, audit_logs |
| **Jobs** | job_listings, applications, application_status_histories, application_notes, saved_jobs, job_alerts, job_embeddings |
| **Skills** | user_skills, skill_gaps, learning_paths, learning_resources, skill_assessments, skill_badges, skill_trends |
| **Interviews** | interview_sessions, interview_questions, interview_responses, video_interview_sessions |
| **S.C.O.U.T.** | company_dna_profiles, success_predictions, tenure_forecasts, flight_risk_assessments, talent_pipelines, bias_audit_results, fairness_metrics |
| **Agent** | agent_configurations, auto_applications, discovered_jobs, job_matches, agent_audit_logs, company_blacklists |
| **Payments** | payment_transactions, subscription_plans, user_subscriptions, payment_activity_logs |
| **Negotiation** | negotiation_strategies, negotiation_sessions, negotiation_messages, negotiation_scenarios |
| **Social** | posts, connections, groups, events, conversations, messages |
| **Reviews** | company_reviews, salary_reports, interview_experiences |

---

## API Reference

### Authentication

**Web (Blade/Livewire):** Session-based with Sanctum cookies + CSRF
**Internal API:** `Authorization: Bearer {sanctum_token}`
**Third-Party API:** `Authorization: Bearer {api_token}` with ability scopes (`company.read`, `jobs.write`, etc.)

### Rate Limits

| Endpoint Category | Limit |
|-------------------|-------|
| Standard operations | 60/min |
| AI generation | 10-30/min |
| Polling operations | 60/min |
| Assessment submission | 20/min |

### Endpoint Groups

<details>
<summary><strong>Job Seeker API (/api/)</strong></summary>

```
Skills & Learning
  POST   /api/skills/analyze               Analyze skill gaps
  GET    /api/skills/gaps                   List skill gaps
  POST   /api/skills/learning-path/{id}     Generate learning path
  POST   /api/skills/assessment/{id}        Generate assessment
  GET    /api/skills/trends                 Industry trends

Job Matching
  GET    /api/jobs/recommended              Personalized recommendations
  GET    /api/jobs/search                   Search with filters
  GET    /api/jobs/{job}/match-analysis     Match breakdown
  POST   /api/jobs/{job}/apply              Apply to job

Interview
  POST   /api/interview/sessions            Start session
  POST   /api/interview/sessions/{id}/answer Submit answer
  GET    /api/interview/sessions/{id}/report Performance report

Negotiation
  POST   /api/negotiation/strategy          Generate strategy
  POST   /api/negotiation/session           Start coaching session
  POST   /api/negotiation/session/{id}/message Send message

Agent
  POST   /api/agent/configure               Configure agent
  POST   /api/agent/activate                Activate auto-apply
  POST   /api/agent/pause                   Pause agent
  GET    /api/agent/status                  Agent status & metrics
  POST   /api/agent/blacklist               Blacklist company

Market Intelligence
  GET    /api/market/overview               Market overview
  GET    /api/market/salary-insights        Salary data
  GET    /api/market/skill-trends           Trending skills

Payment
  POST   /api/payment/initiate              Start payment
  POST   /api/payment/razorpay/callback     Razorpay callback
  POST   /api/webhooks/razorpay             Razorpay webhook
```

</details>

<details>
<summary><strong>Employer API - S.C.O.U.T. (/api/scout/)</strong></summary>

```
DNA Analysis
  POST   /api/scout/analyze-dna             Company DNA analysis
  GET    /api/scout/dna-profile             Get DNA profile

Screening
  POST   /api/scout/analyze-resume          Analyze resume
  POST   /api/scout/shortlist               Auto shortlist candidates

Assessments
  POST   /api/scout/assessment/generate     Generate assessment
  POST   /api/scout/assessment/{id}/submit  Submit answers
  POST   /api/scout/behavioral/generate     Behavioral assessment

Predictive Analytics
  POST   /api/scout/predictive/success      Success probability
  POST   /api/scout/predictive/tenure       Tenure forecast
  POST   /api/scout/predictive/flight-risk  Flight risk assessment
  GET    /api/scout/predictive/report/{app} Full prediction report

Bias Elimination
  POST   /api/scout/bias/anonymize          Anonymize candidate
  POST   /api/scout/bias/audit              Conduct bias audit
  GET    /api/scout/bias/diversity           Diversity analytics
  GET    /api/scout/bias/metrics            Fairness metrics

Talent Pipeline
  POST   /api/scout/pipeline/create         Create pipeline
  POST   /api/scout/passive-candidates/discover Discover passive candidates
  GET    /api/scout/silver-medalists         Silver medalist pool
```

</details>

<details>
<summary><strong>Third-Party Integration API (/api/v1/)</strong></summary>

```
Company
  GET    /api/v1/company                    Company profile
  PUT    /api/v1/company                    Update company

Jobs
  GET    /api/v1/jobs                       List jobs
  POST   /api/v1/jobs                       Create job
  PUT    /api/v1/jobs/{job}                 Update job
  DELETE /api/v1/jobs/{job}                 Delete job

Applications
  GET    /api/v1/applications               List applications
  PUT    /api/v1/applications               Update application status
  POST   /api/v1/applications/bulk-status   Bulk status update

Health
  GET    /health                            System health check
```

</details>

---

## Workflow Diagrams

### User Registration Flow

```
Landing Page → Register → Choose Role (Job Seeker / Employer)
    │
    ▼
Email Verification → Click Link → Optional 2FA Setup
    │
    ▼
Profile Wizard (6 steps):
  1. Upload Resume (drag-drop, AI parsing)
  2. Basic Info (auto-filled from resume)
  3. Experience
  4. Education
  5. Skills
  6. Finish → Dashboard
```

### Job Application Flow

```
Job Seeker → Search Jobs → JobMatchingService → AI Semantic Matching
    │
    ▼
Ranked Results with Match Scores → Select Job → View Match Analysis
    │
    ▼
Apply (Standard or AI-Enhanced)
    │
    ├── Standard: Submit with existing resume
    └── AI-Enhanced: Customize resume + Generate cover letter
            │
            ▼
        Application Created → Events Fired:
            ├── Notify Employer
            ├── Award Gamification Points
            ├── Log Activity
            └── Update Search Index
```

### Autonomous Agent Flow

```
User Configures Agent → Settings Saved → Queue Discovery Job
    │
    ▼
JobAggregationService → RSS Feeds (RemoteOK, WeWorkRemotely, Jobicy)
    │
    ▼
Filter by Criteria → Store DiscoveredJob Records
    │
    ▼
For Each Job:
    ├── JobMatchingService.calculateMatch()
    ├── Score >= Threshold? ──No──→ Skip
    ├── Company Blacklisted? ──Yes──→ Skip
    ├── Daily Cap Reached? ──Yes──→ Stop
    │
    ▼
Create JobMatch → Requires Approval?
    ├── Yes → Wait for User Approval
    └── No → Auto-Apply
            │
            ▼
        Customize Resume (optional) → Generate Cover Letter (optional)
            │
            ▼
        Submit Application → Create AutoApplication → Update Metrics
            │
            ▼
        User Feedback → AgentLearningService → Refine Future Matching
```

### Payment Flow

```
User Selects Plan → POST /api/payment/initiate
    │
    ▼
PaymentGatewayService.createOrder() → Create PaymentTransaction (pending)
    │
    ▼
Gateway Checkout:
    ├── Razorpay → Razorpay modal
    ├── PayU → PayU redirect
    └── Stripe → Stripe checkout
            │
            ▼
        Webhook Callback → Verify Signature
            │
            ├── Valid → Update PaymentTransaction (success)
            │       → Create/Update UserSubscription
            │       → Grant Features → Send Confirmation Email
            │
            └── Invalid → Log Error → Reject
```

### S.C.O.U.T. Predictive Hiring Pipeline

```
Application Received → ResumeAnalyzerService → Extract Skills & Experience
    │
    ▼
DynamicAssessmentService → Candidate Takes Assessment → AI Evaluation
    │
    ▼
BehavioralIntelligenceService → Situational Assessment → Score Competencies
    │
    ▼
PredictiveAnalyticsService:
    ├── Success Probability
    ├── Tenure Forecast
    ├── Productivity Estimate
    ├── Flight Risk
    ├── Development Plan
    └── Onboarding Plan
            │
            ▼
BiasEliminationService:
    ├── Anonymize Data
    ├── Audit for Bias
    └── Generate Fairness Metrics
            │
            ▼
Comprehensive Report → Employer Decision
    ├── Accept → Proceed with Hiring
    └── Override AI → Record Override → ContinuousLearningService
```

---

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer 2.5+
- Node.js 18+
- MySQL 8.0+
- Redis 6.0+
- Meilisearch 1.5+ (optional, for search)

### Installation

```bash
# Clone repository
git clone https://github.com/studaiedutech-ui/studai-career.git
cd studai-career

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run database migrations with seed data
php artisan migrate --seed

# Build frontend assets
npm run build

# Start development server
php artisan serve
```

The application will be available at `http://localhost:8000`.

### Default Accounts (from seeders)

| Role | Email | Password | Notes |
|------|-------|----------|-------|
| Super Admin | admin@studai.com | password | Change immediately |
| Admin Panel | `/admin` | — | Filament dashboard |

### Running Services

```bash
# Development server
php artisan serve

# Queue worker (processes background jobs)
php artisan queue:work

# Scheduler (runs scheduled tasks)
php artisan schedule:work

# Frontend dev server with hot reload
npm run dev

# All-in-one (if configured in composer.json)
composer run dev
```

---

## Configuration

### Required Environment Variables

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=studai_career
DB_USERNAME=root
DB_PASSWORD=your_secure_password

# AI Providers
AZURE_OPENAI_ENDPOINT=https://your-resource.openai.azure.com/
AZURE_OPENAI_API_KEY=your_key
AZURE_OPENAI_DEPLOYMENT_ID=gpt-5.1

AZURE_ANTHROPIC_ENDPOINT=https://your-resource.anthropic.azure.com/
AZURE_ANTHROPIC_API_KEY=your_key

# Payments (use test keys for development)
RAZORPAY_KEY_ID=rzp_test_xxx
RAZORPAY_KEY_SECRET=xxx

PAYU_MERCHANT_KEY=your_key
PAYU_MERCHANT_SALT=your_salt

# Search
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=your_master_key

# Cache & Sessions (use redis for production)
CACHE_STORE=redis
SESSION_DRIVER=redis

# Email (configure real provider for production)
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@studaicareer.com
```

### Subscription Plans (from seeder)

| Plan | Price | Key Features |
|------|-------|-------------|
| Free | ₹0/month | Basic job search, limited AI calls |
| Pro | ₹499/month | Full AI features, autonomous agent, unlimited applications |
| Enterprise | ₹1,499/month | S.C.O.U.T., predictive analytics, priority support |

---

## Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test --filter=PaymentTest

# Syntax check all PHP files
find app -name "*.php" | xargs -I {} php -l {}

# Verify Blade templates compile
php artisan view:cache

# Verify routes resolve
php artisan route:list
```

### Factories Available

```php
User::factory()->create();
Job::factory()->remote()->create();
Application::factory()->shortlisted()->create();
Company::factory()->techCompany()->create();
PaymentTransaction::factory()->completed()->create();
UserSubscription::factory()->trialing()->create();
DiscoveredJob::factory()->matched()->create();
AutoApplication::factory()->highMatch()->create();
CompanyDNAProfile::factory()->create();
SubscriptionPlan::factory()->premium()->create();
```

---

## Deployment

### CI/CD Pipeline

The project uses GitHub Actions (`.github/workflows/deploy.yml`) with three jobs:

```
Push to main/develop
    │
    ▼
Job 1: Build & Test
    ├── PHP 8.2 + Node 20
    ├── MySQL 8.0 + Redis 6 service containers
    ├── composer install (production, optimized)
    ├── npm install + npm run build
    ├── PHP syntax check
    ├── php artisan test
    └── Create deployment artifact (zip)
            │
            ├── develop branch ──→ Job 2: Deploy to Dev Slot
            │                      └── Azure Web App dev slot
            │
            └── main branch ──→ Job 3: Deploy to Production
                                ├── Deploy to dev slot
                                ├── Warm up
                                ├── Swap to production (zero-downtime)
                                ├── Deploy workers app (Horizon/Scheduler)
                                ├── Health check (10 retries)
                                └── Sentry release notification
```

### Azure Infrastructure

| Resource | Name | Purpose |
|----------|------|---------|
| Web App (main) | `studai-app-prod` | Application server |
| Web App (workers) | `studai-workers-prod` | Horizon + Scheduler |
| Resource Group | `studai-career` | All resources |
| Production URL | `https://studaicareer.com` | Live site |
| Dev Slot URL | `studai-app-prod-dev.azurewebsites.net` | Staging |

### Production Checklist

```bash
# 1. Set environment variables (Azure portal or CLI)
#    APP_DEBUG=false, APP_ENV=production, all API keys

# 2. Optimize Laravel
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Run migrations
php artisan migrate --force

# 4. Build frontend
npm run build

# 5. Index search
php artisan scout:import "App\Models\Job"

# 6. Start queue workers (via Supervisor or Horizon)
php artisan horizon

# 7. Setup cron
# * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Cache Management

```bash
php artisan optimize:clear    # Clear all caches
php artisan optimize          # Rebuild all caches
php artisan cache:clear       # Application cache only
php artisan config:clear      # Config cache only
php artisan route:clear       # Route cache only
php artisan view:clear        # View cache only
```

---

## Documentation

| Document | Description |
|----------|-------------|
| [Completeness Scorecard](docs/01-COMPLETENESS_SCORECARD.md) | Domain-by-domain feature scoring |
| [Reality vs Claims](docs/02-REALITY_VS_CLAIMS.md) | Fact-check of documentation against codebase |
| [System Blueprint](docs/03-SYSTEM_BLUEPRINT.md) | Complete runtime architecture with data flows |
| [Implementation Backlog](docs/04-IMPLEMENTATION_BACKLOG.md) | Sprint plan with 60 tickets |
| [CLAUDE.md Patch](docs/05-CLAUDE_MD_PATCH.md) | Corrections for AI assistant instructions |
| [Development Roadmap](docs/06-MASTER_DEVELOPMENT_ROADMAP.md) | 8-week roadmap from MVP to production |
| [Pending Items Audit](docs/07-PENDING_ITEMS_AUDIT.md) | Cross-reference of roadmap vs codebase |
| [Final Status Report](docs/08-FINAL_STATUS_REPORT.md) | Status report (Feb 2026) |
| [Product Intelligence Report](docs/PRODUCT_INTELLIGENCE_REPORT.md) | Full product analysis (Apr 2026) |
| [Gap Closure Sprint Plan](docs/GAP_CLOSURE_SPRINT_PLAN.md) | 19-item actionable sprint plan to reach launch readiness |
| [Runbook](docs/RUNBOOK.md) | Operations manual: health checks, incidents, monitoring |

---

## Current Status

**Version:** 2.0.0
**Last Updated:** April 2026

### What Works

- Full user registration, auth, and profile system with 2FA
- AI-powered resume builder with PDF export
- Job search with semantic matching and scores
- Application submission and tracking
- Interview preparation with AI question generation and evaluation
- Video interview recording with AI analysis
- Salary negotiation coaching
- Skill gap analysis with learning paths and certificates
- Career coach AI chat
- Gamification system
- Social networking features
- S.C.O.U.T. employer intelligence suite
- Admin panel with 93 Filament resources
- Payment gateway integration (code complete)
- CI/CD pipeline to Azure

### What Needs Work

See [Gap Closure Sprint Plan](docs/GAP_CLOSURE_SPRINT_PLAN.md) for the full actionable list. Key items:

- Job data sourcing (scrapers are stubs, only RSS feeds work)
- Email delivery (currently logging, not sending)
- Payment end-to-end testing
- Production infrastructure (Redis drivers, Horizon, Sentry)
- Test coverage for critical paths

---

## License

Proprietary — All rights reserved.

## Contact

- **Email:** admin@studai.com
- **Repository:** [github.com/studaiedutech-ui/studai-career](https://github.com/studaiedutech-ui/studai-career)
