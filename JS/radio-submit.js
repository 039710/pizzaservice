'use strict';

// Wait until DOM content is ready
window.addEventListener('load', function() {
  var submitForm, radioButtons, i;

  // Function to submit form
  submitForm = function() {
    document.querySelector('form').submit();
  }

  // Get radio buttons and add event listeners to submit form on click
  radioButtons = document.querySelectorAll('.submit-form');
  for (i = 0; i < radioButtons.length; ++i) {
    radioButtons[i].addEventListener('click', submitForm);
  }
});
