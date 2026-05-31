<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Vantage Skill Map</h2>
                <p class="text-sm text-gray-500">
                    {{ $session['job_title'] ?? 'Interview' }} &middot; Future-ready competency assessment
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('interview.complete', $sessionId) }}"
                   class="inline-flex items-center gap-1 text-sm text-gray-600 hover:text-indigo-700 font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back to Report
                </a>
                <button onclick="window.print()"
                        class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Export PDF
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if(empty($skillMap) || ($skillMap['overall'] ?? 0) == 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-indigo-50 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-indigo-500 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Generating your Skill Map&hellip;</h3>
                <p class="text-sm text-gray-500 max-w-sm mx-auto mb-6">
                    The Vantage AI evaluator is analysing your full transcript. This usually takes less than 30 seconds.
                </p>
                <button onclick="location.reload()"
                        class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-semibold transition shadow-sm">
                    Refresh now
                </button>
            </div>
        @else
            @php
                $overall  = (float) ($skillMap['overall'] ?? 0);
                $standout = $skillMap['standout_moment'] ?? '';
                $growth   = $skillMap['growth_focus']    ?? '';

                $skills = [
                    'critical_thinking' => [
                        'label'  => 'Critical Thinking',
                        'icon'   => '&#129504;',
                        'hex'    => '#1B57C4',
                        'bg'     => 'bg-indigo-50',
                        'badge'  => 'bg-indigo-100 text-indigo-700',
                    ],
                    'collaboration' => [
                        'label'  => 'Collaboration',
                        'icon'   => '&#129309;',
                        'hex'    => '#2D6CDF',
                        'bg'     => 'bg-cyan-50',
                        'badge'  => 'bg-cyan-100 text-cyan-700',
                    ],
                    'communication' => [
                        'label'  => 'Communication',
                        'icon'   => '&#128172;',
                        'hex'    => '#1E8E3E',
                        'bg'     => 'bg-emerald-50',
                        'badge'  => 'bg-emerald-100 text-emerald-700',
                    ],
                    'creativity' => [
                        'label'  => 'Creativity',
                        'icon'   => '&#128161;',
                        'hex'    => '#E37400',
                        'bg'     => 'bg-amber-50',
                        'badge'  => 'bg-amber-100 text-amber-700',
                    ],
                    'adaptability' => [
                        'label'  => 'Adaptability',
                        'icon'   => '&#128260;',
                        'hex'    => '#2D6CDF',
                        'bg'     => 'bg-purple-50',
                        'badge'  => 'bg-purple-100 text-purple-700',
                    ],
                ];

                $tierBadge = [
                    'Not Demonstrated' => 'bg-gray-100 text-gray-500',
                    'Emerging'         => 'bg-yellow-100 text-yellow-700',
                    'Developing'       => 'bg-blue-100 text-blue-700',
                    'Proficient'       => 'bg-green-100 text-green-700',
                    'Advanced'         => 'bg-purple-100 text-purple-800',
                ];

                $radarLabels = [];
                $radarScores = [];
                $radarColors = [];
                foreach ($skills as $key => $meta) {
                    $radarLabels[] = $meta['label'];
                    $radarScores[] = (float) ($skillMap[$key]['score'] ?? 0);
                    $radarColors[] = $meta['hex'];
                }

                $circumference = round(2 * M_PI * 54, 2);
                $dashOffset    = round($circumference * (1 - $overall / 5), 2);
            @endphp

            {{-- ── HERO BANNER ──────────────────────────────────────────── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 overflow-hidden relative">
                <div class="absolute -top-12 -right-12 w-56 h-56 rounded-full bg-indigo-50 pointer-events-none"></div>
                <div class="absolute -bottom-20 left-1/3 w-72 h-72 rounded-full bg-purple-50 pointer-events-none"></div>

                <div class="relative flex flex-col md:flex-row items-center gap-8">
                    {{-- SVG ring score --}}
                    <div class="flex-shrink-0 flex flex-col items-center gap-1">
                        <svg width="144" height="144" viewBox="0 0 144 144">
                            <circle cx="72" cy="72" r="54" fill="none" stroke="#E2E2E0" stroke-width="10"/>
                            <circle cx="72" cy="72" r="54" fill="none" stroke="#1B57C4" stroke-width="10"
                                    stroke-linecap="round"
                                    stroke-dasharray="{{ $circumference }}"
                                    stroke-dashoffset="{{ $dashOffset }}"
                                    transform="rotate(-90 72 72)"/>
                            <text x="72" y="68" text-anchor="middle" fill="#0C2E72" font-size="30" font-weight="700" font-family="system-ui, sans-serif">{{ number_format($overall, 1) }}</text>
                            <text x="72" y="88" text-anchor="middle" fill="#737373" font-size="12" font-family="system-ui, sans-serif">out of 5.0</text>
                        </svg>
                        <span class="text-xs font-bold text-indigo-600 tracking-widest uppercase">Vantage Score</span>
                    </div>

                    <div class="flex-1 space-y-4">
                        @if($standout)
                        <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                            <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-1.5">&#10024; Standout Moment</p>
                            <p class="text-gray-800 text-sm leading-relaxed font-medium">{{ $standout }}</p>
                        </div>
                        @endif

                        @if($growth)
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-purple-50 border border-purple-200 text-sm font-semibold text-purple-800">
                            &#128640; Growth Focus: <span class="font-extrabold text-purple-900">{{ ucfirst(str_replace('_', ' ', $growth)) }}</span>
                        </div>
                        @endif

                        {{-- Score pills --}}
                        <div class="flex flex-wrap gap-2">
                            @foreach($skills as $key => $meta)
                                @php $s = (float)($skillMap[$key]['score'] ?? 0); @endphp
                                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full"
                                     style="background:#EBF2FF; border:2px solid {{ $meta['hex'] }}">
                                    <span class="text-base leading-none">{!! $meta['icon'] !!}</span>
                                    <span class="text-xs font-extrabold" style="color:{{ $meta['hex'] }}">{{ number_format($s, 1) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── RADAR + BREAKDOWN ─────────────────────────────────────── --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Competency Radar</h3>
                    <div class="flex justify-center">
                        <canvas id="vantageRadar" width="300" height="300" style="max-width:300px; max-height:300px"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-5">Score Breakdown</h3>
                    <div class="space-y-4">
                        @foreach($skills as $key => $meta)
                            @php
                                $s   = (float)($skillMap[$key]['score'] ?? 0);
                                $pct = round(($s / 5) * 100);
                                $lv  = $skillMap[$key]['level'] ?? 'Not Demonstrated';
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-sm font-semibold text-gray-700">{!! $meta['icon'] !!}&nbsp;{{ $meta['label'] }}</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $tierBadge[$lv] ?? 'bg-gray-100 text-gray-500' }}">{{ $lv }}</span>
                                        <span class="text-sm font-bold" style="color:{{ $meta['hex'] }}">{{ number_format($s, 1) }}</span>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5">
                                    <div class="h-2.5 rounded-full transition-all duration-700"
                                         style="width:{{ $pct }}%; background-color:{{ $meta['hex'] }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ── SKILL CARDS ───────────────────────────────────────────── --}}
            <div class="space-y-5">
                @foreach($skills as $key => $meta)
                    @php
                        $data  = $skillMap[$key] ?? [];
                        $score = (float)($data['score'] ?? 0);
                        $level = $data['level'] ?? 'Not Demonstrated';
                        $sub   = $data['sub_scores'] ?? [];
                        $ev    = $data['evidence'] ?? '';
                        $tips  = $data['improvement'] ?? [];
                        $pct   = round(($score / 5) * 100);
                        $tc    = $tierBadge[$level] ?? 'bg-gray-100 text-gray-500';
                        $circ2 = round(2 * M_PI * 22, 2);
                        $off2  = round($circ2 * (1 - $score / 5), 2);
                    @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                        {{-- Card header --}}
                        <div class="{{ $meta['bg'] }} px-6 py-5 flex items-center gap-4 border-b border-white/60">
                            <div class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center text-3xl flex-shrink-0">
                                {!! $meta['icon'] !!}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-2">
                                    <h4 class="text-base font-bold text-gray-900">{{ $meta['label'] }}</h4>
                                    <span class="text-xs px-2.5 py-0.5 rounded-full font-bold {{ $tc }}">{{ $level }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-white/70 rounded-full h-3 overflow-hidden">
                                        <div class="h-3 rounded-full transition-all duration-700"
                                             style="width:{{ $pct }}%; background-color:{{ $meta['hex'] }}"></div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700 whitespace-nowrap">{{ number_format($score, 1) }} / 5.0</span>
                                </div>
                            </div>
                            {{-- Mini ring --}}
                            <div class="flex-shrink-0 hidden sm:flex">
                                <svg width="56" height="56" viewBox="0 0 56 56">
                                    <circle cx="28" cy="28" r="22" fill="none" stroke="#E2E2E0" stroke-width="5"/>
                                    <circle cx="28" cy="28" r="22" fill="none" stroke="{{ $meta['hex'] }}" stroke-width="5"
                                            stroke-linecap="round"
                                            stroke-dasharray="{{ $circ2 }}"
                                            stroke-dashoffset="{{ $off2 }}"
                                            transform="rotate(-90 28 28)"/>
                                    <text x="28" y="32" text-anchor="middle" fill="{{ $meta['hex'] }}"
                                          font-size="11" font-weight="700" font-family="system-ui, sans-serif">{{ number_format($score, 1) }}</text>
                                </svg>
                            </div>
                        </div>

                        {{-- Card body: sub-scores + evidence | tips --}}
                        <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

                            {{-- Left: sub-competencies + evidence --}}
                            <div class="space-y-5">
                                @if(!empty($sub))
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Sub-competencies</p>
                                    <div class="space-y-3">
                                        @foreach($sub as $subLabel => $subScore)
                                            @php $sp = round(($subScore / 5) * 100); @endphp
                                            <div>
                                                <div class="flex justify-between text-xs mb-1">
                                                    <span class="font-semibold text-gray-700">{{ $subLabel }}</span>
                                                    <span class="font-bold" style="color:{{ $meta['hex'] }}">{{ $subScore }}/5</span>
                                                </div>
                                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                                    <div class="h-1.5 rounded-full"
                                                         style="width:{{ $sp }}%; background-color:{{ $meta['hex'] }}; opacity:0.75"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if($ev)
                                <div class="rounded-xl p-4 bg-gray-50 border-l-4"
                                     style="border-color:{{ $meta['hex'] }}">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Evidence Quote</p>
                                    <p class="text-sm text-gray-600 italic leading-relaxed">&ldquo;{{ $ev }}&rdquo;</p>
                                </div>
                                @endif
                            </div>

                            {{-- Right: tips --}}
                            @if(!empty($tips))
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Improvement Tips</p>
                                <ol class="space-y-3">
                                    @foreach((array)$tips as $i => $tip)
                                    <li class="flex items-start gap-3">
                                        <span class="flex-shrink-0 w-6 h-6 rounded-full text-white text-xs font-bold flex items-center justify-center mt-0.5"
                                              style="background-color:{{ $meta['hex'] }}">{{ $i + 1 }}</span>
                                        <p class="text-sm text-gray-700 leading-relaxed">{{ $tip }}</p>
                                    </li>
                                    @endforeach
                                </ol>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ── CTA ───────────────────────────────────────────────────── --}}
            <div class="rounded-2xl overflow-hidden shadow-lg" style="background: #1B57C4;">
                <div class="px-8 py-8 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="text-white">
                        <p class="text-lg font-extrabold mb-1">Want to improve your Vantage score?</p>
                        <p class="text-indigo-200 text-sm">Practice with the Career Coach Skills Practice module for targeted, personalised coaching.</p>
                    </div>
                    <a href="{{ route('career-coach.index') }}"
                       class="flex-shrink-0 inline-flex items-center gap-2 px-6 py-3 bg-white text-indigo-700 rounded-xl text-sm font-bold hover:bg-indigo-50 transition shadow-md whitespace-nowrap">
                        Start Skills Practice
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

        @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
    (function () {
        var labels = @json($radarLabels ?? []);
        var scores = @json($radarScores ?? []);
        var colors = @json($radarColors ?? []);
        var ctx = document.getElementById('vantageRadar');
        if (!ctx || !labels.length) return;

        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Your Score',
                    data: scores,
                    backgroundColor: 'rgba(15, 55, 153,0.10)',
                    borderColor: 'rgba(15, 55, 153,0.80)',
                    borderWidth: 2.5,
                    pointBackgroundColor: colors,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    r: {
                        suggestedMin: 0,
                        suggestedMax: 5,
                        ticks: {
                            stepSize: 1,
                            font: { size: 10 },
                            color: '#A8A8A8',
                            backdropColor: 'transparent',
                        },
                        grid: { color: 'rgba(0,0,0,0.06)' },
                        angleLines: { color: 'rgba(0,0,0,0.06)' },
                        pointLabels: {
                            font: { size: 12, weight: '600' },
                            color: '#3D3D3D'
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (c) {
                                return ' ' + parseFloat(c.raw).toFixed(1) + ' / 5.0';
                            }
                        }
                    }
                }
            }
        });
    })();
    </script>
    @endpush
</x-app-layout>
