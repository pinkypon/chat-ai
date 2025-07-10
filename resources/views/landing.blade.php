<x-layout-landing>
<div class="flex flex-col-reverse md:flex-row items-center gap-8">
  <!-- Left content -->
  <div class="w-full md:w-1/2 text-center md:text-left">
    <x-page-heading>Chat with ChatAI</x-page-heading>
    <x-paragraph-text>Experience natural conversations powered by cutting-edge AI. ChatAI adapts to your context and helps you communicate better, faster.</x-paragraph-text>
    <!-- {{ config('app.url') }} -->
    <a href="/chat" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
      Get Started
    </a>
  </div>

  <div class="w-full md:w-1/2 flex justify-center">
    <img
      src="{{ asset('images/chat-bot.png') }}"
      alt="Neural graphic"
      class="w-[80%] max-w-[250px] md:max-w-full max-h-[300px] object-contain"
    />
  </div>

</div>

<!-- Features section -->
<section class="mt-16 grid gap-10 md:grid-cols-2">
  <div>
    <x-page-heading>Natural Language Processing</x-page-heading>
    <x-paragraph-text>ChatAI understands your intent and delivers precise responses with human-like clarity.</x-paragraph-text>
  </div>
  <div>
    <x-page-heading>Context-Aware Memory</x-page-heading>
    <x-paragraph-text>It remembers what you say and responds in a way that feels natural, helpful, and intuitive.</x-paragraph-text>
  </div>
</section>
</x-layout-landing>
