(function() {
  'use strict';
  var form = document.querySelector('form');
  var button = document.querySelector('button');

  form.addEventListener('submit', function (event) {
    event.preventDefault();
    event.stopPropagation();

    form.classList.add('was-validated');

    if (form.checkValidity() === false) {
      return false;
    }

    var formValues = getFormValues(this);
    var action = new URL(this.action);
    var endpoint = action.pathname.substring(1);

    button.disabled = true;
    httpClient
      .post(endpoint, formValues)
      .then(() => notify('Email was sent.', 'success'))
      .catch((error) => notify(error.message, 'error'))
      .finally(() => button.disabled = false);
  })
})()