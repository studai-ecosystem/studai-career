@extends('layouts.dashboard')
@section('title', 'Browse Categories')
@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Browse by Category</h1>
        <p class="text-gray-500 mb-10">Find the perfect freelancer for your project needs.</p>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($categories as $category)
                <a href="{{ route('marketplace.projects', ['category' => $category['slug'] ?? $category]) }}"
                   class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 text-center hover:border-blue-400 hover:shadow-md transition group">
                    <div class="text-4xl mb-3">{{ $category['icon'] ?? '💼' }}</div>
                    <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition">{{ $category['label'] ?? $category }}</h3>
                    @if(isset($category['count']))
                        <p class="text-gray-400 text-sm mt-1">{{ number_format($category['count']) }} projects</p>
                    @endif
                </a>
            @empty
                @foreach([
                    ['slug' => 'web_development', 'label' => 'Web Development', 'icon' => '🌐'],
                    ['slug' => 'mobile_development', 'label' => 'Mobile Apps', 'icon' => '📱'],
                    ['slug' => 'design_creative', 'label' => 'Design & Creative', 'icon' => '🎨'],
                    ['slug' => 'writing_translation', 'label' => 'Writing & Translation', 'icon' => '✍️'],
                    ['slug' => 'digital_marketing', 'label' => 'Digital Marketing', 'icon' => '📣'],
                    ['slug' => 'data_science', 'label' => 'Data Science & AI', 'icon' => '🤖'],
                    ['slug' => 'finance_accounting', 'label' => 'Finance & Accounting', 'icon' => '💰'],
                    ['slug' => 'legal', 'label' => 'Legal Services', 'icon' => '⚖️'],
                ] as $cat)
                    <a href="{{ route('marketplace.projects', ['category' => $cat['slug']]) }}"
                       class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 text-center hover:border-blue-400 hover:shadow-md transition group">
                        <div class="text-4xl mb-3">{{ $cat['icon'] }}</div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition">{{ $cat['label'] }}</h3>
                    </a>
                @endforeach
            @endforelse
        </div>
    </div>
</div>
@endsection
