@props(['title' => null, 'pageTitle' => null])

<x-layouts.app :title="$title" :pageTitle="$pageTitle">
    {{ $slot }}
</x-layouts.app>
