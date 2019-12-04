'use strict';

// wait until DOMcontent is readyState
window.addEventListener('load', function() {
	var cart, priceTag, sum, updateSum, addPizza, pizzaElements, i, removePizzas, removeSelectedPizzas;
	
	// initialize variables
	cart = document.querySelector('#orders');
	priceTag = document.querySelector('#sum');
	sum = 0;
	
	/** 
	 * update the sum and its element with the new value
	 * @param {integer} newSum The new sum
	 */
	updateSum = function(newSum) {
		sum = newSum;
		priceTag.innerHTML = sum.toFixed(2);	
	}
	
	/**
	 * add pizzas to the cart
	 */
	addPizza = function() {
		var id, name, price, option;
		
		// get attributes from html
		id = this.getAttribute('data-id');
		name = this.getAttribute('data-name');
		price = parseInt(this.getAttribute('data-price'), 10);
		
		// add to cart
		option = new Option(name, id);
		option.setAttribute('data-price', price);
		cart[cart.length] = option;
		
		// add new price
		updateSum(sum + price / 100);
	};
	
	/**
	 * reset the selected pizzas
	 */
	removePizzas = function() {
		cart.innerHTML = '';
		updateSum(0);
	}
	
	/**
	 * remove the selected selected pizzas
	 */
	removeSelectedPizzas = function() {
		var i, option, price;
		for (i = cart.options.length - 1; i >= 0; --i) {
			option = cart.options[i];
			if (option.selected) {
				// remove it
				price = parseInt(option.getAttribute('data-price'), 10);
				cart.options.remove(i);
				updateSum(sum - price / 100)
			}
		}
	}
	
	// get all pizza-elements and add click event listener for each
	for (i = 0, pizzaElements = document.querySelectorAll('.add-pizza'); i < pizzaElements.length; i++) {
		pizzaElements[i].addEventListener('click', addPizza);
	}
	
	// delete all button
	document.querySelector('#delete-all').addEventListener('click', removePizzas);
	
	// delete selected button
	document.querySelector('#delete-selected').addEventListener('click', removeSelectedPizzas);
	
	document.querySelector('form').addEventListener('submit', function(e) {
		var addressField, i;
		
		addressField = document.querySelector('#address');
		
		if (cart.options.length == 0) {
			// prevent submission of form
			e.preventDefault();
			
			alert('Bitte wählen Sie mindestens eine Pizza.');
		} else if (addressField.value.length <= 0) {
			// prevent submission of form
			e.preventDefault();
		} else {
			// select all items
			for (i = 0; i < cart.options.length; ++i) {
				cart.options[i].selected = true;
			}
		}
		
	});
});