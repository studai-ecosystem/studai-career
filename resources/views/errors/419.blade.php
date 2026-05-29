@extends('errors.layout')

@section('title', '419 — Session Expired')
@section('code', '419')
@section('heading', 'Session expired')
@section('message', 'Your session has expired. Please click the button below to return to the page and try again.')
@section('action_text', 'Go Back & Retry')
@section('action_url', 'javascript:void(0)')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.querySelector('[href="javascript:void(0)"]');
        if (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                // Use document.referrer for GET navigation (avoids POST re-submission 404)
                var back = document.referrer || '/';
                window.location.href = back;
            });
        }
    });
</script>
@endpush
