@extends('layouts.public')

@section('title', $page->meta_title ?: $page->title)

@if ($page->meta_description)
    @section('meta_description', $page->meta_description)
@endif

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-10">
        @include('public.partials.builder-content', ['pageModel' => $page])
    </section>
@endsection
