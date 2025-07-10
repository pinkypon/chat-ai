<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChatAI</title>
    @vite('resources/css/app.css')
  </head>
  <body class="bg-white text-gray-800">

    <!-- Navigation -->
    <nav class="w-full px-6 py-4 flex justify-between items-center">
      <div class="text-xl font-bold w-fit">
        <a href="/"
          class="flex items-center justify-between rounded-xl px-3 py-1 -mx-2 transition hover:bg-gray-200">
          <x-page-heading-chat>ChatAI</x-page-heading-chat>
          <x-logo />
        </a>
      </div>
      <ul class="flex gap-6 text-md">
        @guest
        <li>
          <a href="/login" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Sign In
          </a>
        </li>
        <li>
          <a href="/register" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Sign Up
          </a>
        </li>
        @endguest
      </ul>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-12">
      {{ $slot }}
    </main>

    <!-- Chat Summary Section -->
    <section class="bg-gray-100 mt-16 py-10">
      <div class="max-w-4xl mx-auto px-6">
        <x-page-heading>Need help or stuck?</x-page-heading>
        <x-paragraph-text>Try rephrasing your question, starting a new chat, or checking our tips below.</x-paragraph-text>
        <div class="grid md:grid-cols-3 gap-4 mt-6">
          <x-landing-tips>
            <h3 class="font-semibold mb-2">🔁 Start Fresh</h3>
            <p>Begin a new chat for better focus or a new topic.</p>
          </x-landing-tips>
          <x-landing-tips>
            <h3 class="font-semibold mb-2">💡 Tips</h3>
            <p>Be clear and concise — ChatAI works best that way.</p>
          </x-landing-tips>
          <x-landing-tips>
            <h3 class="font-semibold mb-2">❓ Need Help?</h3>
            <p>Visit our FAQ or contact us if something’s not working.</p>
          </x-landing-tips>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="text-center text-sm text-gray-500 py-6">
      <a href="#" class="hover:underline">Privacy Policy</a> •
      <a href="#" class="hover:underline">Terms of Service</a>
    </footer>
  </body>
</html>
