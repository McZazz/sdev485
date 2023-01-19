
// get text areas for quarters
const fall_input = document.getElementById('fall_input');
const winter_input = document.getElementById('winter_input');
const spring_input = document.getElementById('spring_input');
const summer_input = document.getElementById('summer_input');

// get table cells for quarters
const fall_table_cell = document.getElementById('fall_table_cell');
const winter_table_cell = document.getElementById('winter_table_cell');
const spring_table_cell = document.getElementById('spring_table_cell');
const summer_table_cell = document.getElementById('summer_table_cell');

// set of keys pressed
let keys = new Set();

const print_format = () => {
	// put inner text in print tables
	fall_table_cell.innerText = fall_input.value;
	winter_table_cell.innerText = winter_input.value;
	spring_table_cell.innerText = spring_input.value;
	summer_table_cell.innerText = summer_input.value;

	// call up print preview
	window.print();
}

window.addEventListener('keydown', (event) => {
	// add key to set
	keys.add(event.which);

	// cehck for "ctrl + p", hijack and fil fields
	if (keys.size === 2) {
		if (keys.has(80) && keys.has(17)) {
			event.preventDefault();

			// load stuff in fields in case plan is unsaved (php had no chance to template it in)
			print_format();
		}
	}

});

// remove keys on keyup
window.addEventListener('keyup', (event) => {
	keys.delete(event.which);
});

