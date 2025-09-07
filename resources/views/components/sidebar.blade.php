@props(['conversations' => collect(), 'currentConversationId' => null])
<aside id="sidebar"
       class="bg-white w-64 h-screen p-4
              fixed md:static z-40
              transform transition-transform duration-300 ease-in-out
              -translate-x-full md:translate-x-0
              md:block flex flex-col">

  <!-- Top: Logo + Close -->
  <div class="flex justify-between items-center mb-3">
    <div class="font-bold items-center">
      <a href="/" class="inline-block px-2 py-1 rounded-lg transition hover:bg-gray-200">
        <x-logo />
      </a>
    </div>
    <button class="md:hidden text-gray-500 hover:text-black text-2xl" onclick="toggleSidebar(false)">
      &times;
    </button>
  </div>

  <!-- New Chat Button -->
  @auth
    <div class="mb-4" x-data>
      <form action="{{ route('chat.new') }}" method="POST"
            :class="{ 'pointer-events-none opacity-50': Alpine.store('chat').loading }">
        @csrf
        <button type="submit"
                class="w-full text-left flex items-center gap-2 hover:bg-gray-100 p-2 rounded-xl">
          <x-icon employer="https://www.svgrepo.com/show/506731/new.svg" />
          <p>New Chat</p>
        </button>
      </form>
    </div>
  @endauth


  <!-- Scrollable Conversation List -->
  @auth
  <div class="overflow-y-auto pr-1 space-y-2 text-sm rounded-xl h-full md:h-[750px]">

      @if ($conversations->isNotEmpty())
        @foreach ($conversations as $conv)
          @php $isActive = $currentConversationId == $conv->id; @endphp

          <div
            x-data
            :class="{
              'pointer-events-none opacity-50': Alpine.store('chat').loading
            }"
            class="group flex items-center justify-between px-2 py-1 rounded-xl
                  hover:bg-gray-100 {{ $isActive ? 'bg-gray-100 font-semibold' : '' }}"
          >
            <a href="{{ route('chat.show', $conv->id) }}" class="flex-1 block w-full text-left">
              <x-conversation>{{ $conv->title ?? 'Untitled Chat' }}</x-conversation>
            </a>

            <button type="button"
                    class="ml-2 opacity-0 group-hover:opacity-100 transition"
                    :disabled="Alpine.store('chat').loading"
                    :class="{ 'cursor-not-allowed': Alpine.store('chat').loading }"
                    @click="showModal = true;
                            deleteUrl = '{{ route('chat.delete', $conv->id) }}';">

                <!-- Trash icon -->
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 text-gray-500 hover:text-red-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 
                          00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>

            </button>

          </div>
        @endforeach
      @else
        <p class="text-sm text-gray-400 px-2">No conversations yet.</p>
      @endif

  </div>
  @endauth

  <!-- Mobile Profile / Logout -->
  <div class="md:hidden pt-4 border-t border-gray-200 relative" x-data="{ open: false }">
    <button @click="open = !open"
            class="w-full flex items-center px-4 py-2 text-sm hover:bg-gray-100 rounded-lg text-gray-700">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500"
          fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
      <span class="truncate block max-w-[12rem]" title="{{ Auth::user()->email }}">
        {{ Auth::user()->email }}
      </span>
    </button>
    <div x-show="open" x-cloak @click.outside="open = false"
         class="absolute left-0 bottom-[calc(100%+0.5rem)] w-full bg-white rounded shadow-md z-50">
        <form x-ref="logoutForm" action="{{ route('logout') }}" method="POST">
            @csrf
            @method('DELETE')
            <button
                type="submit"
                class="flex items-center gap-3 w-full text-left px-4 py-2 text-sm hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                @click.prevent="
                    $el.disabled = true;
                    $refs.logoutText.innerText = 'Logging out…';
                    $refs.logoutForm.requestSubmit();
                "
            >
                <!-- Logout Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 11-4 0v-1m0-8v1a2 2 0 104 0V7" />
                </svg>
                <span x-ref="logoutText">Log out</span>
            </button>
        </form>
    </div>
  </div>
</aside>

