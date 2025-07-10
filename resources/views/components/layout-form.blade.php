<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'ChatAI')</title>
    @vite('resources/css/app.css')
  </head>
  <body class="bg-gray-100 text-gray-800">
    <main class="flex items-center justify-center min-h-screen p-6">
      {{ $slot }}
    </main>
  </body>
</html>
