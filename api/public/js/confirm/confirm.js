(function() {
  'use strict';
  var params = (new URL(location)).searchParams;
  var token = params.get('token');
  var user_id = params.get('user_id');

  if (token && user_id) {
    httpClient
      .post('confirm/email', {token, user_id})
      .then((response) => {
        cookie.set('access_token', response.token, 30);
        cookie.set('refresh_token', response.refresh_token, 30);
        cookie.set('id', response.user.id, 30);
        cookie.set('username', response.user.username, 30);
        cookie.set('roles', response.user.roles.join(', '), 30);
        redirect('/');
      })
      .catch((error) => notify(error.message, 'error'))
  }
})()