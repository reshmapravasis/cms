@extends('layouts.app')

@section('content')
    @if($page->content)
        @include('partials.blocks_loop', ['content' => $page->content])
    @else
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $page->title }}</h1>
            <p class="text-gray-600">This page has no content yet.</p>
        </div>
    @endif
@endsection
