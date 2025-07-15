<x-layout-chat :conversations="$conversations" :currentConversationId="$currentConversationId">
<!-- Top bar (Mobile only) -->
<div class="md:hidden relative p-4 bg-white flex items-center justify-start">
  <!-- Hamburger -->
  <button onclick="toggleSidebar(true)" class="text-xl z-10">&#9776;</button>
</div>


<nav class="hidden md:flex justify-between items-center px-6 py-4 bg-gray-100">
  <a href="/"><x-page-heading-chat>ChatAI</x-page-heading-chat></a>
    <div x-data="{ open: false }" class="relative">
      @auth
        <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none hover:bg-black/10 p-1 rounded-2xl">
          <svg class="w-6 h-6 text-gray-600 hover:text-black" fill="none" stroke="currentColor"
              stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.657 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </button>
        <!-- Dropdown -->
        <div x-show="open" x-cloak @click.outside="open = false"
            class="absolute right-0 mt-2 w-56 bg-white rounded shadow-md z-50">

          <!-- Email -->
          <div class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor"
                stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.657 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="truncate block max-w-[12rem]" title="{{ Auth::user()->email }}">
              {{ Auth::user()->email }}
            </span>
          </div>
          <!-- Log out -->
          <form action="/logout" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="flex items-center gap-3 w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500 shrink-0" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 11-4 0v-1m0-8v1a2 2 0 104 0V7" />
              </svg>
              Log out
            </button>
          </form>
        </div>
      @endauth

      @guest
        <div class="space-x-2">
          <a href="/login" class="text-blue-600 hover:underline text-sm">Sign In</a>
          <a href="/register" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Sign Up</a>
        </div>
      @endguest
    </div>
</nav>





<div  
  id="chatBox" 
  class="flex-1 overflow-y-auto px-4 py-8 bg-gray-100"
  x-data="{ hasMessages: {{ !empty($messages) ? 'true' : 'false' }} }"
  x-init="() => {
    if (hasMessages) {
      $nextTick(() => {
        setTimeout(() => {
          $refs.chatEnd?.scrollIntoView({ behavior: 'smooth' });
        }, 100);
      });
    }
  }"
>
  <div id="chatMessages" class="max-w-[680px] mx-auto space-y-6">

    {{-- ✅ Show messages if available --}}
    @if (!empty($messages))
      @foreach ($messages as $msg)
        @if ($msg['role'] === 'user')
          <x-chat-user>{{ $msg['content'] }}</x-chat-user>
        @else
          <x-chat-ai>{{ $msg['content'] }}</x-chat-ai>
        @endif
      @endforeach

    {{-- ✅ Show placeholder if no messages --}}
    @else
      <div id="chatPlaceholder"
           x-show="!hasMessages"
           x-cloak
           class="text-center text-gray-400 mt-10">
        @auth
          <p>Start a new conversation or select one from the sidebar.</p>
        @endauth
        @guest
          <p>Start a new conversation</p>
        @endguest
      </div>
    @endif
    {{-- 👇 Scroll anchor --}}
    <div x-ref="chatEnd"></div>
  </div>
</div>



<!-- Chat input -->
<div class="px-4 py-4 bg-gray-100">
  <div class="max-w-[680px] mx-auto">
    <x-chat-form :conversation-id="$currentConversationId" >
    </x-chat-form>
  </div>
</div>
</x-layout-chat>