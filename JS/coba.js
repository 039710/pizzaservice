  var cart, priceTag, sum, updateSum, addPizza, pizzaElements, i, removePizzas,
  removeSelectedPizzas;

  cart = document.querySelector('#orders');
  priceTag = document.querySelector('#sum');
  sum = 0;
  updateSum = function(newSum) {
    sum = newSum;
    priceTag.innerHTML = sum.toFixed(2);
  }
  addPizza = function() {
   var id, name, price, option;

   // Get attributes from HTML
   id = this.getAttribute('data-id');
   name = this.getAttribute('data-name');
   price = parseInt(this.getAttribute('data-price'), 10);

   // Add to cart
   option = new Option(name, id);
   option.setAttribute('data-price', price);
   cart[cart.length] = option;

   // Add new price
   updateSum(sum + price / 100);
 };

 removePizzas = function() {
   cart.innerHTML = '';
   updateSum(0);
 }

 removeSelectedPizzas = function() {
   var i, option, price;

   for (i = cart.options.length - 1; i >= 0; --i) {
     option = cart.options[i];

     if (option.selected) {
       // Remove it
       price = parseInt(option.getAttribute('data-price'), 10);
       cart.options.remove(i);
       updateSum(sum - price / 100)
     }
   }
 }




  // Get all pizza-elements and add click event listener for each
 for (i = 0,
      pizzaElements = document.querySelectorAll('.add-pizza');
      i < pizzaElements.length;
      ++i) {
   pizzaElements[i].addEventListener('click', addPizza);
 }
 // add event to button delete all
 document.querySelector('#delete-all')
   .addEventListener('click', removePizzas);

   // add event to button delete
  document.querySelector('#delete-selected')
     .addEventListener('click', removeSelectedPizzas);

  document.querySelector('form').addEventListener('submit', function(e) {
       var addressField, i;

   addressField = document.querySelector('#address');
    if (cart.options.length === 0) {
         // Prevent submission of form
         e.preventDefault();

         alert('Bitte wählen Sie mindestens eine Pizza.');
         } else if (addressField.value.length <= 0) {
             // Prevent submission of form
             e.preventDefault();

             alert('Bitte geben Sie eine Adresse ein.');
             addressField.focus();
         } else {
               // Select all items
               for (i = 0; i < cart.options.length; ++i) {
                 cart.options[i].selected = true;
         }
       }
     });
