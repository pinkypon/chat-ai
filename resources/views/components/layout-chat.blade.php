@props(['conversations' => collect(), 'currentConversationId' => null])
<!DOCTYPE html>
<html lang="en" class="h-full" x-data="{ showModal: false, deleteUrl: '', ready: false }" x-init="ready = true">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChatAI - Chat</title>
    @vite('resources/css/app.css')
    <script>
      document.addEventListener('alpine:init', () => {
        Alpine.store('chat', {
          loading: false
        });
      });
  </script>
  </head>
  <body class="bg-gray-50 text-gray-800">
    <!-- Mobile Overlay -->
    <div id="sidebarOverlay"
         class="fixed inset-0 z-30 hidden md:hidden"
         onclick="toggleSidebar(false)">
    </div>

    <!-- Layout wrapper -->
    <div class="flex h-screen overflow-hidden">

      <!-- Authenticated Sidebar -->
      @auth
        <x-sidebar :conversations="$conversations" :currentConversationId="$currentConversationId" />
      @endauth
      <!-- Guest Sidebar -->
      @guest
        <div id="sidebar"
             class="bg-white w-64 h-screen p-4 fixed md:hidden z-40 transform transition-transform duration-300 ease-in-out -translate-x-full md:translate-x-0 flex flex-col justify-between">
          <!-- Top -->
          <div>
            <div class="flex justify-between items-center mb-6">
              <div class="text-xl font-bold flex items-center justify-between w-[100px]">
                <x-page-heading-chat>ChatAI</x-page-heading-chat>
                <x-logo />
              </div>
              <button class="text-gray-500 hover:text-black text-2xl" onclick="toggleSidebar(false)">
                &times;
              </button>
            </div>
            <p class="text-sm text-gray-400 px-2">Sign in to view saved chats.</p>
          </div>
          <!-- Bottom -->
          <div class="space-y-2 mt-8">
            <a href="/login" class="block w-full text-center bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Sign In</a>
            <a href="/register" class="block w-full text-center bg-gray-100 text-blue-600 py-2 rounded hover:bg-gray-200">Sign Up</a>
          </div>
        </div>
      @endguest

      <!-- Main Chat Panel -->
      <main class="flex-1 flex flex-col overflow-hidden">
        {{ $slot }}
      </main>
    </div>




<!-- Alpine JS Only (no Fetch plugin to avoid error) -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- Sidebar Script -->
<script>
  function toggleSidebar(show) {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (show) {
      sidebar.classList.remove('hidden', '-translate-x-full');
      overlay.classList.remove('hidden');
    } else {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    }
  }

  // Handle Back Cache
  window.addEventListener('pageshow', function (event) {
    if (event.persisted || (window.performance && performance.navigation.type === 2)) {
      window.location.reload();
    }
  });
</script>

<!-- Modal Component -->
<x-chat-delete-modal />
  </body>
</html>
