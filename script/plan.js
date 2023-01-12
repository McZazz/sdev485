const message_btn = document.getElementById('message-btn');
const message = document.getElementById('message');

if (message.getAttribute('_is_visible') === 't') {
	message.style.visibility = 'visible';
}

message_btn.addEventListener('click', () => {
	message.style.visibility = 'hidden';
	message.setAttribute('_is_visible', 'f');
});