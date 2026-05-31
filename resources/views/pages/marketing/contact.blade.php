@extends('layouts.site')

@php $brand = config('site.brand'); @endphp

@push('styles')
<style>
    .contact{display:grid;grid-template-columns:1fr 1.2fr;gap:48px;align-items:start;}
    .contact__info .item{display:flex;gap:14px;align-items:flex-start;padding:18px 0;border-bottom:1px solid var(--line);}
    .contact__info .ic{width:44px;height:44px;flex:0 0 44px;border-radius:13px;background:var(--mist);display:flex;align-items:center;justify-content:center;color:var(--violet);}
    .contact__info .ic svg{width:20px;height:20px;}
    .contact__info b{display:block;font-size:15px;font-weight:700;color:var(--ink);}
    .contact__info a,.contact__info span{font-size:15px;color:var(--ink-2);font-weight:450;}
    .form{background:#fff;border:1px solid var(--line);border-radius:var(--r-lg);padding:34px 32px;box-shadow:var(--shadow-sm);}
    .form .row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
    .field{margin-bottom:18px;}
    .field label{display:block;font-size:13.5px;font-weight:700;color:var(--ink-2);margin-bottom:7px;}
    .field input,.field textarea{width:100%;font-family:var(--font);font-size:15.5px;color:var(--ink);background:var(--mist);
        border:1px solid var(--line);border-radius:14px;padding:13px 15px;transition:.2s;}
    .field input:focus,.field textarea:focus{outline:0;border-color:var(--violet);background:#fff;box-shadow:0 0 0 4px rgba(124,58,237,.1);}
    .field textarea{min-height:130px;resize:vertical;}
    .form .btn{width:100%;justify-content:center;}
    .form__err{color:#DC2626;font-size:13px;margin-top:6px;}
    .form__ok{background:#ECFDF5;color:#0E9F6E;border:1px solid #A7F3D0;border-radius:14px;padding:14px 16px;font-size:15px;font-weight:600;margin-bottom:20px;}
    @media(max-width:980px){.contact{grid-template-columns:1fr;} .form .row{grid-template-columns:1fr;}}
</style>
@endpush

@section('no_cta')
@section('content')
    <section class="phero" style="padding-bottom:24px">
        <div class="phero__grid"></div>
        <div class="phero__bg"><span class="blob b1"></span><span class="blob b2"></span></div>
        <div class="wrap">
            <span class="eyebrow rv">Contact</span>
            <h1 class="rv" data-d="1">Let’s talk</h1>
            <p class="lede rv" data-d="2">Questions, partnerships or press — we’d love to hear from you.</p>
        </div>
    </section>

    <section class="sec" style="padding-top:24px">
        <div class="wrap">
            <div class="contact rv">
                <div class="contact__info">
                    <div class="item">
                        <span class="ic">@include('partials.icon', ['name' => 'mail'])</span>
                        <div><b>Email us</b><a href="mailto:{{ $brand['email'] }}">{{ $brand['email'] }}</a></div>
                    </div>
                    <div class="item">
                        <span class="ic">@include('partials.icon', ['name' => 'shield'])</span>
                        <div><b>Support</b><a href="mailto:{{ $brand['support'] }}">{{ $brand['support'] }}</a></div>
                    </div>
                    <div class="item">
                        <span class="ic">@include('partials.icon', ['name' => 'pin'])</span>
                        <div><b>Office</b><span>{{ $brand['address'] ?? 'Bengaluru, India' }}</span></div>
                    </div>
                </div>

                <form class="form" method="POST" action="{{ route('contact.submit') }}">
                    @csrf
                    @if(session('success'))
                        <div class="form__ok">{{ session('success') }}</div>
                    @endif
                    <div class="row">
                        <div class="field">
                            <label for="name">Your name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')<div class="form__err">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')<div class="form__err">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="field">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required>
                        @error('subject')<div class="form__err">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" required>{{ old('message') }}</textarea>
                        @error('message')<div class="form__err">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Send message @include('partials.icon', ['name' => 'arrow'])</button>
                </form>
            </div>
        </div>
    </section>
@endsection
