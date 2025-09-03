import React from 'react';
import ReactDOM from 'react-dom/client';
import NewChatButton from './components/newchatbutton';
import '../css/app.css';

// Mount to the sidebar div
const newChatRoot = document.getElementById('new-chat-react-root');
if (newChatRoot) {
  ReactDOM.createRoot(newChatRoot).render(<NewChatButton />);
}
