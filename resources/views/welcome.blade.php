<!DOCTYPE html>
<html lang="pt-br" class="dark-mode-forced">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dark Mode Portal</title>
    
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