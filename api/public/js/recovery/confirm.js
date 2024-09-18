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
    var params = (new URL(location)).searchParams;
    var code = params.get('code');
    var user_id = params.get('user_id');

    if (code && user_id) {
      button.disabled = true
      httpClient
        .post(endpoint, {
          ...formValues,
          code,
          user_id
        })
        .then((response) => {
          cookie.set('access_token', response.token, 30);
          cookie.set('refresh_token', response.refresh_token, 30);
          cookie.set('id', response.user.id, 30);
          cookie.set('username', response.user.username, 30);
          cookie.set('roles', response.user.roles.join(', '), 30);
          redirect('/');
        })
        .catch((error) => notify(error.message, 'error'))
        .finally(() => button.disabled = false)
    }
  })
})()