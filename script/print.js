/**
 * @author: Kevin Price
 * @date: Jan 19, 2023
 * @filename: print.js
 * @description: javascript for print formatting and ctrl + p hijacking
 */


// get text areas for quarters
const fall_inputs = document.getElementsByClassName('fall_input');
const winter_inputs = document.getElementsByClassName('winter_input');
const spring_inputs = document.getElementsByClassName('spring_input');
const summer_inputs = document.getElementsByClassName('summer_input');
const advisor_input = document.getElementById('advisor_input');

// get table cells for quarters
const fall_table_cells = document.getElementsByClassName('fall_table_cell');
const winter_table_cells = document.getElementsByClassName('winter_table_cell');
const spring_table_cells = document.getElementsByClassName('spring_table_cell');
const summer_table_cells = document.getElementsByClassName('summer_table_cell');
const advisor_table_cell = document.getElementById('advisor_table_cell');

// set of keys pressed
let keys = new Set();


/**
 * Formats tables of print data in case php is not disabled
 * or in case plan was not saved after changes were made
 */
const print_format = () => {
	// put inner text in print tables
	for (let i = 0; i < fall_inputs.length; i++) {
		fall_table_cells[i].innerText = fall_inputs[i].value;
		winter_table_cells[i].innerText = winter_inputs[i].value;
		spring_table_cells[i].innerText = spring_inputs[i].value;
		summer_table_cells[i].innerText = summer_inputs[i].value;
	}

	advisor_table_cell.innerText = advisor_input.value;

	// call up print preview
	window.print();
}


// get keys and add to set on keydown
window.addEventListener('keydown', (event) => {
	// add key to set
	keys.add(event.keyCode);

	// cehck for "ctrl + p", hijack and fil fields
	if (keys.size === 2) {
		if (keys.has(80) && keys.has(17)) {
			event.preventDefault();

			// print dialog prevents keyup form firing, remove all now
			keys = new Set();
			// load stuff in fields in case plan is unsaved (php had no chance to template it in)
			print_format();
		}
	}
});


// remove keys on keyup
window.addEventListener('keyup', (event) => {
	keys.delete(event.keyCode);
});

