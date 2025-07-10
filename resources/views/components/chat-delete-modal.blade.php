<div
  x-show="showModal"
  x-cloak
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
>
  <div
    class="bg-white rounded-lg shadow-lg p-6 w-80 text-center"
    x-transition
  >
    <h2 class="text-lg font-semibold mb-4">Delete conversation?</h2>
    <p class="text-gray-600 mb-6">This can’t be undone.</p>

    <div class="flex justify-center gap-4">
      <button
        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
        @click="showModal = false"
      >
        Cancel
      </button>

      <form :action="deleteUrl" method="POST">
        @csrf
        @method('DELETE')
        <button
          type="submit"
          class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
        >
          Delete
        </button>
      </form>
    </div>
  </div>
</div>
