@extends('layouts.site', ['seo' => ['title' => $page->seo['meta_title'] ?? $page->title, 'description' => $page->seo['meta_description'] ?? $page->subtitle, 'image' => $page->hero_image]])

@section('content')
    @if(($page->blocks[0]['type'] ?? null) !== 'hero')
        <x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="media($page->hero_image)" :crumbs="[$page->title => null]"/>
    @endif
    <x-blocks :blocks="$page->blocks ?? []"/>
@endsection
