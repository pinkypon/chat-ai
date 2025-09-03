import React from 'react';

export default function NewChatButton() {
  const handleClick = async (e) => {
    e.preventDefault(); // 🚫 Prevent traditional form submit

    try {
      const response = await fetch('/chat/new', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
      });

      if (!response.ok) throw new Error('Request failed');

      const data = await response.json();

      // 🔁 Do the redirect manually
      window.location.href = data.redirect;
    } catch (err) {
      console.error('Failed to start new chat:', err);
    }
  };

  return (
    <button
      onClick={handleClick}
      className="w-full text-left flex items-center gap-2 hover:bg-gray-100 p-2 rounded-xl"
    >
      <img src="https://www.svgrepo.com/show/506731/new.svg" alt="New" className="w-4 h-4" />
      <p>New Chat</p>
    </button>
  );
}
