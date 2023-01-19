/**
 * @author: Kevin Price
 * @date: Jan 19, 2023
 * @filename: home.js
 * @description: javascript for homepage login modal
 */


const login_modal = document.getElementById('open-login-modal');
const modal_bg = document.getElementById('login-modal-bg');
const modal_close_btn = document.getElementById('login-message-btn');


// inputs
const username = document.getElementById('login_username');
const password = document.getElementById('login_password');


// open modal
login_modal.addEventListener('click', (event) => {
	if (event.target === login_modal) {
		modal_bg.classList.add('login-modal-bg_visible');
		modal_bg.classList.remove('login-modal-bg_hidden');
	}
});


// close modal background
modal_bg.addEventListener('click', (event) => {
	if (event.target === modal_bg) {
		modal_bg.classList.add('login-modal-bg_hidden');
		modal_bg.classList.remove('login-modal-bg_visible');
	}
});


// close modal btn
modal_close_btn.addEventListener('click', (event) => {
	modal_bg.classList.add('login-modal-bg_hidden');
	modal_bg.classList.remove('login-modal-bg_visible');
});

