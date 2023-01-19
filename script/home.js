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

const loginClicked = () => {

	let user = username.value.trim();
	let pass = password.value.trim();

	let body = {
		'username': user,
		'password': pass
	}

	// console.log('adsfasdfasfasfa',body);


  // const params = {
  // 	method: 'POST',
  // 	mode: 'cors',
  // 	headers: {
	// 		'Accept': 'application/json',
	// 		'Content-Type': 'application/json'
  // 	},
  // 	body:data
  // }
	const form = new FormData(document.getElementById('login-modal-card'));

  fetch('http://localhost/sdev485/login', {
  	method: 'POST',
  	body: form
  })
    .then(res => {
        return res.json();
    })
    .then(json => {
    	console.log('resssssss',json);
    	// if (json['res'] === 'invalid_creds') {
      //   console.log('invalid creds');
    	// }
    	// else if (json['res'] === 'login') {
    	// 	console.log('login');
    	// }
    })
    .catch(err => {
        // if errCallback is supplied, use it, otherwise just console log
        console.log('xhr error:', err);
		});


  // fetch('http://localhost/sdev485/login', params)
  //   .then(res => {
  //       return res.json();
  //   })
  //   .then(json => {
  //   	console.log(json);
  //   	if (json['res'] === 'invalid_creds') {
  //       console.log('invalid creds');
  //   	}
  //   	else if (json['res'] === 'login') {
  //   		console.log('login');
  //   	}
  //   })
  //   .catch(err => {
  //       // if errCallback is supplied, use it, otherwise just console log
  //       console.log('xhr error:', err);
	// 	});

	return false;
}