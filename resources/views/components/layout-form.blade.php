<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'ChatAI')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/test.png') }}">
    @vite('resources/css/app.css')
    
    <!-- Include Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  </head>
  <body class="bg-gray-100 text-gray-800">
    <main class="flex items-center justify-center min-h-screen p-6">
      {{ $slot }}
    </main>
  </body>
</html>
