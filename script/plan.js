/**
 * @author: Kevin Price
 * @date: Jan 12, 2023
 * @filename: plan.js
 * @description: javascript for message to indicate plan was saved
 */

// get nodes
const message_btn = document.getElementById('message-btn');
const message = document.getElementById('message');
const is_visible = document.getElementById('_is_visible');

// if container of message received _is_visible custom attribute from php, show message
if (is_visible.value === 't') {
	message.style.visibility = 'visible';
}

// hide message when clicked
message_btn.addEventListener('click', () => {
	message.style.visibility = 'hidden';
	is_visible.value = 'f';
});