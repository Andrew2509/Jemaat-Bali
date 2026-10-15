@php
    $hasCustomLogo = !empty($programInfo?->logo_url);
    $initials = $programInfo->initials ?? 'JB';
    $defaultSvg = asset('favicon.svg');
    $faviconUrl = $hasCustomLogo ? $programInfo->logo_url : $defaultSvg;
@endphp

<link id="app-favicon" rel="icon" href="{{ $faviconUrl }}">
<link rel="shortcut icon" href="{{ $faviconUrl }}">
@if($hasCustomLogo)
    <link rel="apple-touch-icon" href="{{ $programInfo->logo_url }}">
@endif
