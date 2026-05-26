@extends('layouts.dashboard')

@section('title', $company->name . ' Reviews | StudAI Hire')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    {{-- Company Header --}}
    @include('companies.partials.header', ['company' => $company, 'activeTab' => 'reviews'])

    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <livewire:reviews.company-reviews :company="$company" />
    </div>
</div>
@endsection
