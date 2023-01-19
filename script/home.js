const login_modal = document.getElementById('open-login-modal');
const modal_bg = document.getElementById('login-modal-bg');
const modal_close_btn = document.getElementById('login-message-btn');

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

const loginClicked = () => {
  const params = {
  	method: 'POST',
  	mode: 'cors',
  	headers: {
		'Accept': 'application/json',
		'Content-Type': 'application/json'
  	}
  }

  fetch('http://localhost/sdev485/login', params)
    .then(res => {
        return res.json();
    })
    .then(json => {
        console.log('returned res:', json);
    })
    .catch(err => {
        // if errCallback is supplied, use it, otherwise just console log
        console.log('xhr error:', err);
		});

	return false;
}