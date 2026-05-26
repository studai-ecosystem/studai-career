@extends('layouts.marketing')

@section('title', 'Security — StudAI Hire | How We Protect Your Data')

@section('content')
{{-- Hero --}}
<section class="bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-emerald-100 px-6 py-3 rounded-full border border-emerald-300 mb-8">
            <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span class="text-sm font-medium text-emerald-800">Enterprise-Grade Security</span>
        </div>
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">Security at StudAI Hire</h1>
        <p class="text-xl text-gray-600 leading-relaxed max-w-2xl mx-auto">
            Your data security is our top priority. Here's how we protect your information with enterprise-grade security measures.
        </p>
    </div>
</section>

{{-- Security highlights --}}
<section class="py-12 bg-white border-b border-gray-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            @foreach([
                ['256-bit', 'AES Encryption'],
                ['TLS 1.3', 'In-Transit Security'],
                ['99.9%', 'Uptime SLA'],
                ['SOC 2', 'Compliance Target'],
            ] as [$stat, $label])
            <div class="bg-emerald-50 rounded-2xl border border-emerald-200 p-5">
                <div class="text-2xl font-bold text-emerald-700 mb-1">{{ $stat }}</div>
                <div class="text-sm text-gray-600">{{ $label }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Content --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">

        {{-- Data Encryption --}}
        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-6">🔐 Data Encryption</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Data at Rest</h3>
                    <ul class="list-disc list-inside text-gray-600 text-sm space-y-2">
                        <li>AES-256 encryption for all stored data</li>
                        <li>Encrypted database backups</li>
                        <li>Key management via AWS KMS</li>
                        <li>Resume and document files encrypted on disk</li>
                    </ul>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Data in Transit</h3>
                    <ul class="list-disc list-inside text-gray-600 text-sm space-y-2">
                        <li>TLS 1.3 for all web traffic</li>
                        <li>HTTPS enforced site-wide</li>
                        <li>Secure WebSocket connections</li>
                        <li>Certificate pinning on mobile apps</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Access Control --}}
        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-6">🛡️ Access Control</h2>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                @foreach([
                    ['Multi-Factor Authentication', 'MFA required for all admin accounts and optional for users'],
                    ['Role-Based Access Control', 'Employees access only the minimum data needed for their role'],
                    ['Zero-Trust Architecture', 'Every request is verified regardless of network location'],
                    ['Session Management', 'Automatic session expiry with secure token rotation'],
                    ['IP Allowlisting', 'Administrative access restricted to approved IP ranges'],
                ] as [$title, $desc])
                <div class="flex items-start gap-4 py-3 border-b border-gray-100 last:border-0">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 text-sm">{{ $title }}</h4>
                        <p class="text-gray-600 text-sm">{{ $desc }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Infrastructure --}}
        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-6">☁️ Infrastructure Security</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach([
                    ['🌐', 'Cloud Provider', 'Hosted on Microsoft Azure with enterprise SLAs and physical security'],
                    ['🔄', 'Auto Backups', 'Daily encrypted backups with 30-day retention and point-in-time recovery'],
                    ['📡', 'DDoS Protection', 'Azure DDoS Standard protection with automatic attack mitigation'],
                    ['🔥', 'Web Application Firewall', 'WAF rules to block SQL injection, XSS, and OWASP Top 10 threats'],
                    ['📊', '24/7 Monitoring', 'Continuous security monitoring with automated anomaly detection'],
                    ['🔁', 'Disaster Recovery', 'Multi-region failover with RTO < 4 hours and RPO < 1 hour'],
                ] as [$icon, $title, $desc])
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-start gap-4">
                    <span class="text-2xl">{{ $icon }}</span>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-1">{{ $title }}</h4>
                        <p class="text-sm text-gray-600">{{ $desc }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Application Security --}}
        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-6">⚙️ Application Security</h2>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Development Practices</h3>
                        <ul class="list-disc list-inside text-gray-600 text-sm space-y-2">
                            <li>OWASP Top 10 compliance</li>
                            <li>Code reviews for all changes</li>
                            <li>Dependency vulnerability scanning</li>
                            <li>Automated security testing in CI/CD</li>
                            <li>Secrets management via Azure Key Vault</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Regular Testing</h3>
                        <ul class="list-disc list-inside text-gray-600 text-sm space-y-2">
                            <li>Annual third-party penetration tests</li>
                            <li>Quarterly vulnerability assessments</li>
                            <li>Bug bounty program (contact security@studai.careers)</li>
                            <li>Security training for all engineers</li>
                            <li>Incident response drills</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Compliance --}}
        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-6">📋 Compliance & Certifications</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach([
                    ['GDPR', 'EU data protection regulation compliance'],
                    ['DPDP Act', 'India Digital Personal Data Protection Act 2023'],
                    ['ISO 27001', 'Information security management (in progress)'],
                    ['SOC 2 Type II', 'Security, availability & confidentiality (in progress)'],
                    ['PCI DSS', 'Payment card data security via Razorpay'],
                    ['VAPT', 'Vulnerability assessment & pen testing annually'],
                ] as [$badge, $desc])
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
                    <div class="text-sm font-bold text-emerald-700 mb-2">{{ $badge }}</div>
                    <p class="text-xs text-gray-600">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Incident Response --}}
        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-6">🚨 Incident Response</h2>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <p class="text-gray-600 mb-4">In the event of a security incident, we follow a structured response process:</p>
                <div class="space-y-3">
                    @foreach(['Detection & Triage (within 1 hour)', 'Containment & Investigation (within 4 hours)', 'User Notification (within 72 hours if required by law)', 'Remediation & Recovery', 'Post-Incident Review & Improvements'] as $i => $step)
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 bg-emerald-600 text-white rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">{{ $i + 1 }}</div>
                        <span class="text-gray-700 text-sm">{{ $step }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Responsible Disclosure --}}
        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-6">🔍 Responsible Disclosure</h2>
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl border border-emerald-200 p-8">
                <p class="text-gray-600 mb-4">
                    We take all security reports seriously. If you discover a security vulnerability, please report it responsibly:
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">How to Report</h3>
                        <div class="space-y-2 text-gray-600 text-sm">
                            <p>Email: <strong>security@studai.careers</strong></p>
                            <p>PGP Key available on request</p>
                            <p>Include: description, reproduction steps, impact</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Our Commitment</h3>
                        <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                            <li>Acknowledge within 24 hours</li>
                            <li>Provide regular status updates</li>
                            <li>Credit researchers in our hall of fame</li>
                            <li>No legal action for good-faith reports</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact --}}
        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-6">📧 Security Contact</h2>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Security Team</h3>
                        <p class="text-gray-600 text-sm">security@studai.careers</p>
                        <p class="text-gray-600 text-sm">Response time: &lt; 24 hours</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">General Privacy</h3>
                        <p class="text-gray-600 text-sm">privacy@studai.careers</p>
                        <p class="text-gray-600 text-sm">+91-80-4567-8900</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@push('styles')
<style>html { scroll-behavior: smooth; }</style>
@endpush
