<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Primary Meta Tags -->
        <title inertia>{{ config('app.name', 'Score Beyond Leadership') }}</title>
        <meta name="title" content="Score Beyond Leadership - Transforming Lives Through Sport Development">
        <meta name="description" content="Score Beyond Leadership Organization is a sports and development organization dedicated to empowering women and girls through education, health, livelihood, and skill development. Join us in creating lasting change.">
        <meta name="keywords" content="sports development, women empowerment, girls education, Uganda, volunteer, donate, community development, leadership, skill development, health and wellbeing">
        <meta name="author" content="Score Beyond Leadership Organization">
        <meta name="robots" content="index, follow">
        <meta name="language" content="English">
        <meta name="revisit-after" content="7 days">
        <meta name="theme-color" content="#a01d62">

        <!-- Canonical URL -->
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="Score Beyond Leadership - Transforming Lives Through Sport Development">
        <meta property="og:description" content="Score Beyond Leadership Organization is a sports and development organization dedicated to empowering women and girls through education, health, livelihood, and skill development.">
        <meta property="og:image" content="{{ asset('images/scorebeyond-white.webp') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="Score Beyond Leadership Organization Logo">
        <meta property="og:site_name" content="Score Beyond Leadership">
        <meta property="og:locale" content="en_US">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="https://x.com/score_beyond">
        <meta name="twitter:title" content="Score Beyond Leadership - Transforming Lives Through Sport Development">
        <meta name="twitter:description" content="Score Beyond Leadership Organization is a sports and development organization dedicated to empowering women and girls through education, health, livelihood, and skill development.">
        <meta name="twitter:image" content="{{ asset('images/scorebeyond-white.webp') }}">
        <meta name="twitter:image:alt" content="Score Beyond Leadership Organization Logo">

        <!-- Additional SEO Tags -->
        <meta name="geo.region" content="UG">
        <meta name="geo.placename" content="Kampala, Uganda">
        <meta name="contact" content="info@scorebeyondleadership.org">
        <meta name="copyright" content="Score Beyond Leadership Organization">

        <!-- Favicon -->
        <link rel="icon" type="image/webp" href="{{ asset('images/scorebeyond-white.webp') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/scorebeyond-white.webp') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
