@props(['conversationId' => null])

<div
  x-data="{
    prompt: '',
    conversationId: '{{ $conversationId }}',
    hasMessages: {{ !empty($messages) ? 'true' : 'false' }},

    scrollToBottom() {
      this.$nextTick(() => {
        const el = document.querySelector('[x-ref=chatEnd]');
        if (el) el.scrollIntoView({ behavior: 'smooth' });
      });
    },

    async submit() {
      if (!this.prompt.trim()) return;
      Alpine.store('chat').loading = true;
      this.hasMessages = true;

      const chatBox = document.getElementById('chatMessages');
      const placeholder = document.getElementById('chatPlaceholder');
      if (placeholder) placeholder.remove();

      // 👤 Add user message
      chatBox.insertAdjacentHTML('beforeend', `
        <div class='bg-blue-100 text-blue-900 p-4 rounded-lg max-w-xl ml-auto'>
          ${this.prompt}
        </div>
      `);
      this.scrollToBottom();

      // ⏳ Add AI typing indicator
      const typing = document.createElement('div');
      typing.id = 'aiTyping';
      typing.innerHTML = `
        <div class='bg-gray-200 text-gray-900 p-4 rounded-lg max-w-xl mr-auto animate-pulse'>
          <div class='flex items-center space-x-2'>
            <span class='h-2.5 w-2.5 bg-gray-500 rounded-full animate-bounce [animation-delay:.1s]'></span>
            <span class='h-2.5 w-2.5 bg-gray-500 rounded-full animate-bounce [animation-delay:.2s]'></span>
            <span class='h-2.5 w-2.5 bg-gray-500 rounded-full animate-bounce [animation-delay:.3s]'></span>
            <span class='ml-2 text-xs text-gray-600'>AI is typing...</span>
          </div>
        </div>
      `;
      chatBox.appendChild(typing);
      this.scrollToBottom();

      try {
        const response = await fetch('{{ route('chat.send') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
          },
          body: JSON.stringify({
            prompt: this.prompt,
            conversation_id: this.conversationId,
          }),
        });

        if (!response.ok) throw new Error('Network error');
        const data = await response.json();
        typing.remove();

        if (data.conversation_id) {
          if (!this.conversationId) {
            window.location.href = `/chat/${data.conversation_id}`;
            return;
          }
          this.conversationId = data.conversation_id;
        }

        // 🤖 Add AI message
        chatBox.insertAdjacentHTML('beforeend', `
          <div class='bg-gray-200 text-gray-900 p-4 rounded-lg max-w-xl mr-auto animate-fade-in'>
            ${data.response}
          </div>
        `);
        this.prompt = '';
        Alpine.store('chat').loading = false;
        this.scrollToBottom();

      } catch (err) {
        typing.remove();
        alert('Error: ' + err.message);
        Alpine.store('chat').loading = false;
      }
    }
  }"
  x-init="scrollToBottom()"
>
  <form @submit.prevent="submit"
        class="bg-gray-400/20 rounded-2xl p-3 shadow flex flex-col gap-2">
    <input type="hidden" :value="conversationId">

    <div class="relative">
      <textarea
        x-model="prompt"
        rows="1"
        placeholder="Type your message..."
        class="w-full resize-none overflow-y-auto max-h-[160px] bg-transparent border-none focus:outline-none px-3 py-2 rounded-md disabled:opacity-50"
        @input="event.target.style.height = 'auto'; event.target.style.height = event.target.scrollHeight + 'px';"
        :disabled="Alpine.store('chat').loading"
        required
      ></textarea>
    </div>

    <div class="flex justify-end">
      <button
        type="submit"
        class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50"
        :disabled="Alpine.store('chat').loading || !prompt.trim()"
      >
        <span x-show="!Alpine.store('chat').loading">Send</span>
        <span x-show="Alpine.store('chat').loading" x-cloak class="flex items-center gap-2 text-sm text-white">
          <svg class="w-5 h-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <circle class="opacity-75" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                    stroke-linecap="round" stroke-dasharray="80" stroke-dashoffset="60" />
          </svg>
          Sending...
        </span>
      </button>
    </div>
  </form>
</div>
