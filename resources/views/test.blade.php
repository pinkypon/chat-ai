<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ChatAI</title>
  <script>
    function toggleSidebar(show) {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('-translate-x-full', !show);
    }
  </script>
  @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 text-gray-800">

  <div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside id="sidebar"
           class="bg-white w-64 h-full p-4 border-r fixed md:static z-40 transition-transform transform -translate-x-full md:translate-x-0 md:block hidden">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">ChatAI</h2>
        <button class="md:hidden text-gray-500 hover:text-black text-2xl" onclick="toggleSidebar(false)">
          &times;
        </button>
      </div>
      <button class="w-full p-3 rounded hover:bg-blue-100 cursor-pointer text-sm mb-6 text-left">+ New Chat</button>
      <div class="space-y-4 overflow-y-auto max-h-[calc(100vh-150px)]">
        <div class="p-3 bg-gray-100 rounded hover:bg-blue-100 cursor-pointer">Conversation 1</div>
        <div class="p-3 bg-gray-100 rounded hover:bg-blue-100 cursor-pointer">Conversation 2</div>
        <div class="p-3 bg-gray-100 rounded hover:bg-blue-100 cursor-pointer">Conversation 3</div>
      </div>
    </aside>

    <!-- Chat Panel -->
    <main class="flex-1 flex flex-col justify-between">
      
      <!-- Top bar (Mobile only) -->
      <div class="md:hidden p-4 border-b bg-white flex items-center relative">
        <button onclick="toggleSidebar(true)" class="text-xl z-10">&#9776;</button>
        <h2 class="absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 text-lg font-bold">
          ChatAI
        </h2>
      </div>

      <!-- Chat messages -->
      <div class="flex-1 overflow-y-auto px-4 py-8">
        <div class="max-w-[680px] mx-auto space-y-6">
          <!-- User -->
          <div class="flex">
            <div class="bg-blue-100 text-blue-900 p-4 rounded-lg max-w-full ml-auto">
              Hi there, how do I reset my password?
            </div>
          </div>
          <!-- AI -->
          <div class="flex">
            <div class="bg-gray-200 text-gray-900 p-4 rounded-lg max-w-full mr-auto">
              To reset your password, go to the settings page and click "Forgot Password".
            </div>
          </div>
        </div>
      </div>

      <!-- Chat Input Section -->
      <div class="px-4 py-4">
        <div class="max-w-[680px] mx-auto">
          <form class="bg-gray-400/20 rounded-2xl p-3 shadow flex flex-col gap-2">
            
            <!-- Textarea -->
            <div class="relative">
              <textarea
                rows="1"
                placeholder="Type your message..."
                class="w-full resize-none overflow-y-auto max-h-[160px] bg-transparent border-none focus:outline-none px-3 py-2 rounded-md"
                oninput="this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px';"
              ></textarea>
            </div>

            <!-- Send Button (fixed position within form) -->
            <div class="flex justify-end">
              <button
                type="submit"
                class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700"
              >
                Send
              </button>
            </div>
            
          </form>
        </div>
      </div>
    </main>
  </div>

</body>
</html>
