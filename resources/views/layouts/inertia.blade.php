<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @vite('resources/js/app-inertia.tsx')
        @inertiaHead
    </head>

    <body>
        @inertia
        @vite('resources/css/app.css')
    </body>
</html>
