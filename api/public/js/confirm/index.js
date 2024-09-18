(function() {
  'use strict';
  var button = document.querySelector('button');
  button.addEventListener('click', function (event) {
    event.preventDefault();

    button.disabled = true;
    httpClient
      .post('confirm/resend', {})
      .then(() => notify('Email resend successful'))
      .catch((error) => notify(error.message, 'error'))
      .finally(() => button.disabled = false)
  })
})()