<!DOCTYPE html>
<html lang="pt-br" class="dark-mode-forced">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dark Mode Portal</title>
    
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $meta['url'] ?? url()->current() }}">
    <meta property="og:title" content="{{ $meta['title'] ?? 'Dark Mode' }}">
    <meta property="og:description" content="{{ $meta['description'] ?? '' }}">
    <meta property="og:image" content="{{ $meta['image'] ?? asset('images/background.jpg') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $meta['url'] ?? url()->current() }}">
    <meta name="twitter:title" content="{{ $meta['title'] ?? 'Dark Mode' }}">
    <meta name="twitter:description" content="{{ $meta['description'] ?? '' }}">
    <meta name="twitter:image" content="{{ $meta['image'] ?? asset('images/background.jpg') }}">

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <style>
        :root {
            --bg-url: url("{{ asset('images/background.jpg') }}");
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app"></div>
</body>
</html>