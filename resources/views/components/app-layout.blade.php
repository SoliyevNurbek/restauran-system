@props(['title' => null, 'pageTitle' => null, 'pageSubtitle' => null])

<x-layouts.app :title="$title" :pageTitle="$pageTitle" :pageSubtitle="$pageSubtitle">
    {{ $slot }}
</x-layouts.app>

