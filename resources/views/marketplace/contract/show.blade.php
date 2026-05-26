@extends('layouts.dashboard')
@section('title', 'Contract - ' . ($contract->project?->title ?? 'Contract'))
@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ $isFreelancer ? route('marketplace.freelancer.contracts') : route('marketplace.employer.contracts') }}" class="text-gray-400 hover:text-gray-600">← Contracts</a>
            <h1 class="text-xl font-bold text-gray-900 truncate">{{ $contract->project?->title ?? 'Contract' }}</h1>
            <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                {{ match($contract->status) {
                    'active'    => 'bg-green-100 text-green-700',
                    'completed' => 'bg-blue-100 text-blue-700',
                    'cancelled' => 'bg-red-100 text-red-700',
                    default     => 'bg-yellow-100 text-yellow-700'
                } }}">{{ ucfirst($contract->status) }}</span>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: Milestones & Messages --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Milestones --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-gray-900 mb-4">Milestones</h2>

                    @forelse($contract->milestones as $milestone)
                        <div class="flex gap-4 mb-6 last:mb-0">
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shrink-0
                                    {{ match($milestone->status) {
                                        'approved'   => 'bg-green-600 text-white',
                                        'submitted'  => 'bg-blue-600 text-white',
                                        'revision'   => 'bg-amber-500 text-white',
                                        'funded'     => 'bg-purple-600 text-white',
                                        default      => 'bg-gray-200 text-gray-500'
                                    } }}">
                                    {{ $loop->iteration }}
                                </div>
                                @if(!$loop->last) <div class="w-0.5 flex-1 bg-gray-100 my-1"></div> @endif
                            </div>
                            <div class="flex-1 pb-4">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $milestone->title }}</h3>
                                        <p class="text-gray-500 text-sm mt-0.5">{{ $milestone->description }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="font-bold text-gray-900">₹{{ number_format($milestone->amount ?? 0) }}</span>
                                        <span class="block text-xs text-gray-400 mt-0.5">{{ ucfirst($milestone->status) }}</span>
                                    </div>
                                </div>

                                @if($milestone->note)
                                    <div class="mt-2 p-3 bg-blue-50 rounded-xl text-sm text-blue-800">
                                        <strong>Submission Note:</strong> {{ $milestone->note }}
                                    </div>
                                @endif

                                {{-- Freelancer actions --}}
                                @if($isFreelancer && $milestone->status === 'funded')
                                    <div class="mt-3 flex items-center gap-2">
                                        <input type="text" id="note-{{ $milestone->id }}" placeholder="Submission notes (optional)"
                                               class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                        <button onclick="submitMilestone({{ $milestone->id }}, this)"
                                                class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                                            Submit Work
                                        </button>
                                    </div>
                                @endif

                                {{-- Employer actions --}}
                                @if($isEmployer && $milestone->status === 'submitted')
                                    <div class="mt-3 flex gap-2">
                                        <button onclick="approveMilestone({{ $milestone->id }}, this)"
                                                class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition">
                                            Approve &amp; Release
                                        </button>
                                        <button onclick="requestRevision({{ $milestone->id }}, this)"
                                                class="px-4 py-2 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                                            Request Revision
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm text-center py-4">No milestones added yet.</p>
                    @endforelse

                    {{-- Complete contract button --}}
                    @if($isEmployer && $contract->status === 'active' && $contract->milestones->every(fn($m) => $m->status === 'approved'))
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <form action="{{ route('marketplace.contracts.complete', $contract) }}" method="POST">
                                @csrf
                                <button type="submit" onclick="return confirm('Mark this contract as complete?')"
                                        class="w-full py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition">
                                    Mark Project Complete
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                {{-- Review (if completed and no review yet) --}}
                @if($contract->status === 'completed' && $contract->reviews->where('reviewer_id', auth()->id())->isEmpty())
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h2 class="font-bold text-gray-900 mb-4">Leave a Review</h2>
                        <form action="{{ route('marketplace.contracts.review', $contract) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                <div class="flex gap-2" id="starRating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" onclick="setRating({{ $i }})" class="text-2xl text-gray-300 hover:text-yellow-400 transition star" data-val="{{ $i }}">★</button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="ratingVal" value="5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Review</label>
                                <textarea name="review" rows="3" placeholder="Share your experience..." required
                                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                            </div>
                            <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition">Submit Review</button>
                        </form>
                    </div>
                @endif

                {{-- Messages --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="font-bold text-gray-900">Messages</h2>
                    </div>
                    <div id="messages-container" class="p-5 space-y-4 max-h-80 overflow-y-auto">
                        @forelse($contract->messages->reverse() as $msg)
                            <div class="flex gap-3 {{ $msg->sender_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($msg->sender?->name ?? 'U') }}&background=1A73E8&color=fff&size=36"
                                     class="w-9 h-9 rounded-xl shrink-0" alt="">
                                <div class="max-w-xs">
                                    <div class="px-4 py-3 rounded-2xl text-sm
                                        {{ $msg->sender_id === auth()->id() ? 'bg-blue-600 text-white ml-auto' : 'bg-gray-100 text-gray-900' }}">
                                        {{ $msg->message }}
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1 {{ $msg->sender_id === auth()->id() ? 'text-right' : '' }}">
                                        {{ $msg->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm text-center py-4">No messages yet. Start the conversation!</p>
                        @endforelse
                    </div>
                    <div class="p-5 border-t border-gray-100 flex gap-3">
                        <input type="text" id="messageInput" placeholder="Type a message..." class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm">
                        <button onclick="sendMessage()" class="px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition text-sm">Send</button>
                    </div>
                </div>
            </div>

            {{-- Right: Contract Details --}}
            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Contract Details</h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-gray-500">Total Value</dt>
                            <dd class="font-bold text-2xl text-gray-900">₹{{ number_format($contract->total_amount ?? 0) }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ $isFreelancer ? 'Client' : 'Freelancer' }}</dt>
                            <dd class="font-semibold">{{ $isFreelancer ? $contract->employer?->name : $contract->freelancer?->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Start Date</dt>
                            <dd class="font-semibold">{{ $contract->created_at->format('M d, Y') }}</dd>
                        </div>
                        @if($contract->deadline)
                            <div>
                                <dt class="text-gray-500">Due Date</dt>
                                <dd class="font-semibold">{{ \Carbon\Carbon::parse($contract->deadline)->format('M d, Y') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Milestone Progress --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="font-bold text-gray-900 mb-3">Progress</h3>
                    @php
                        $total = $contract->milestones->count();
                        $done  = $contract->milestones->where('status', 'approved')->count();
                        $pct   = $total ? round($done / $total * 100) : 0;
                    @endphp
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>{{ $done }}/{{ $total }} milestones</span>
                        <span>{{ $pct }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                    </div>
                </div>

                {{-- Active disputes warning --}}
                @if($contract->disputes && $contract->disputes->isNotEmpty())
                    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 text-sm">
                        <p class="font-semibold text-red-700 mb-1">⚠️ Active Dispute</p>
                        <p class="text-red-600">{{ $contract->disputes->first()->reason }}</p>
                    </div>
                @endif

                {{-- Dispute button --}}
                @if($contract->status === 'active' && ($contract->disputes ?? collect())->isEmpty())
                    <button onclick="document.getElementById('disputePanel').classList.toggle('hidden')"
                            class="w-full py-2 border border-red-200 text-red-500 font-medium rounded-xl hover:bg-red-50 transition text-sm">
                        Raise Dispute
                    </button>
                    <div id="disputePanel" class="hidden bg-white rounded-2xl border border-red-200 p-4 space-y-3">
                        <textarea id="disputeReason" rows="3" placeholder="Describe the issue..." class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-400 resize-none"></textarea>
                        <button onclick="raiseDispute()" class="w-full py-2 bg-red-500 text-white font-semibold rounded-xl hover:bg-red-600 transition text-sm">Submit Dispute</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name=csrf-token]').content;

async function submitMilestone(id, btn) {
    const note = document.getElementById(`note-${id}`)?.value ?? '';
    btn.disabled = true; btn.textContent = 'Submitting...';
    const res = await fetch(`/marketplace/contracts/milestones/${id}/submit`, {
        method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ note })
    });
    const json = await res.json();
    if (json.success) location.reload();
    else { alert(json.message); btn.disabled = false; btn.textContent = 'Submit Work'; }
}

async function approveMilestone(id, btn) {
    if (!confirm('Approve this milestone and release payment?')) return;
    btn.disabled = true; btn.textContent = 'Approving...';
    const res = await fetch(`/marketplace/contracts/milestones/${id}/approve`, {
        method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({})
    });
    const json = await res.json();
    if (json.success) location.reload();
    else { alert(json.message); btn.disabled = false; btn.textContent = 'Approve & Release'; }
}

async function requestRevision(id, btn) {
    const reason = prompt('Describe what needs to be revised:');
    if (!reason) return;
    btn.disabled = true;
    const res = await fetch(`/marketplace/contracts/milestones/${id}/revision`, {
        method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ reason })
    });
    const json = await res.json();
    if (json.success) location.reload();
    else { alert(json.message); btn.disabled = false; }
}

async function sendMessage() {
    const input = document.getElementById('messageInput');
    const msg = input.value.trim();
    if (!msg) return;
    const res = await fetch('{{ route('marketplace.contracts.messages.send', $contract) }}', {
        method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ message: msg })
    });
    const json = await res.json();
    if (json.success) { input.value = ''; location.reload(); }
    else alert(json.message);
}

async function raiseDispute() {
    const reason = document.getElementById('disputeReason').value.trim();
    if (!reason) { alert('Please describe the issue.'); return; }
    const res = await fetch('{{ route('marketplace.contracts.dispute', $contract) }}', {
        method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ reason })
    });
    const json = await res.json();
    if (json.success) location.reload();
    else alert(json.message);
}

function setRating(val) {
    document.getElementById('ratingVal').value = val;
    document.querySelectorAll('.star').forEach(s => {
        s.className = s.className.replace(/text-(gray|yellow)-\d+/, parseInt(s.dataset.val) <= val ? 'text-yellow-400' : 'text-gray-300');
    });
}
// Init rating display to 5
document.addEventListener('DOMContentLoaded', () => { if (document.getElementById('starRating')) setRating(5); });
</script>
@endsection
