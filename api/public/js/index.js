const httpClient = new HttpClient();
const cookie = new Cookie();
const auth = cookie.get('id');

function HttpClient() {
  this.get = function (uri) {
    return this.request('GET', uri);
  }

  this.sync = function (uri) {
    const request = new XMLHttpRequest();
    request.open('GET', '/' + uri, false);
    request.send(null);

    if (request.status === 200) {
      return JSON.parse(request.responseText);
    }

    throw new Error(JSON.parse(request.responseText));
  }

  this.post = function (uri, body) {
    return this.request('POST', uri, JSON.stringify(body), {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    });
  }

  this.patch = function (uri, body) {
    return this.request('PATCH', uri, JSON.stringify(body), {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    });
  }

  this.delete = function (uri, body) {
    return this.request('DELETE', uri, JSON.stringify(body), {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    });
  }

  this.upload = function (uri, body) {
    return this.request('POST', uri, body)
  }

  this.request = function (method, uri, body = null, headers = {}) {
    const request = {
      method,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        ...headers
      },
    }

    if (body) {
      request.body = body;
    }

    return new Promise((resolve, reject) => {
      fetch('/' + uri, request)
        .then((response) => {
          return response.json()
            .then((json) => {
              if (response.status === 401) {
                const refresh_token = cookie.get('refresh_token');
                const user_id = cookie.get('id');

                if (!refresh_token || !user_id) {
                  return Promise.reject(JSON.stringify({
                    message: 'Authorization error.'
                  }));
                }
                // Try to update access_token
                return fetch('/refresh_token', {
                  method: 'post',
                  headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                  },
                  body: JSON.stringify({user_id, refresh_token})
                }).then((refreshResponse) => {
                  if (refreshResponse.ok) {
                    return refreshResponse.json().then((refreshTokenData) => {
                      // retry old request with new access_token
                      cookie.set('access_token', refreshTokenData.token, 30);
                      cookie.set('refresh_token', refreshTokenData.refresh_token, 30);
                      cookie.set('id', refreshTokenData.user.id, 30);
                      cookie.set('username', refreshTokenData.user.username, 30);
                      cookie.set('roles', refreshTokenData.user.roles.join(', '), 30);

                      return fetch('/' + uri, request).then((retryResponse) => {
                        return retryResponse.json().then((retryJson) => {
                          if (retryResponse.status === 401) {
                            return Promise.reject(new Error('Unauthorized after token refresh'));
                          }
                          return retryJson;
                        });
                      });
                    });
                  } else {
                    return Promise.reject(new Error('Unauthorized and failed to refresh token'));
                  }
                });
              }

              // If request failed, reject and return modified json string as error
              if (!response.ok) return Promise.reject(JSON.stringify(json))
              // If successful, request has Content-Range, continue by returning modified json string
              if (response.headers.has('content-range')) {
                return JSON.stringify({
                  data: json,
                  total: parseInt(
                    response.headers
                      .get('content-range')
                      .split('/')
                      .pop(),
                    10
                  )
                })
              }
              // If successful, continue by returning modified json string
              return JSON.stringify(json);
            })
        })
        .then((response) => JSON.parse(response))
        .then((json) => resolve(json))
        .catch((error) => {
          try {
            reject(JSON.parse(error))
          } catch(e) {
            reject({ message: 'Internal server error.' })
          }
        })
    })
  }
}

function Cookie() {
  this.set = function (name, value, expiredAt) {
    const d = new Date();
    d.setTime(d.getTime() + (expiredAt * 24 * 60 * 60 * 1000));
    let expires = 'expires=' + d.toUTCString();
    document.cookie = name + '=' + value + ';' + expires + ";path=/;SameSite=Strict;";
  }

  this.get = function (name) {
    let ca = document.cookie.split(';');
    for(let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) === ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name + '=') === 0) {
        return c.substring((name + '=').length, c.length);
      }
    }
    return "";
  }

  this.remove = function (name) {
    document.cookie = name + '=; Max-Age=0; path=/;';

    const domain = window.location.hostname;
    document.cookie = name + '=; Max-Age=0; path=/; domain=' + domain + ';';

    const parts = domain.split('.');
    if (parts.length > 2) {
      const baseDomain = '.' + parts.slice(-2).join('.');
      document.cookie = name + '=; Max-Age=0; path=/; domain=' + baseDomain + ';';
    }
  }
}

function redirect(path) {
  window.location.href = path;
  return null;
}

function getFormValues(form) {
  const formData = {};
  const inputs = form.querySelectorAll('input, select, textarea');

  inputs.forEach(input => {
    const name = input.name;
    if (name) {
      // Handle different input types
      if (input.type === 'checkbox' || input.type === 'radio') {
        formData[name] = !!input.checked;
      } else {
        formData[name] = input.value.trim();
      }
    }
  });

  return formData;
}

function notify(message, level) {
  var notify = document.createElement('div');

  notify.id = 'snackbar';
  notify.className = 'show ' + level;
  notify.innerText = message;

  setTimeout(() => notify.classList.remove('show'), 3000);
  setTimeout(() => notify.remove(), 4000);

  document.body.appendChild(notify);
}

function logout(e) {
  e = e || window.event;

  e.preventDefault();

  cookie.remove('access_token');
  cookie.remove('refresh_token');
  cookie.remove('id');
  cookie.remove('username');
  cookie.remove('roles');
  window.location.reload();
}

function spinner(container) {
  container.classList.toggle('spinner');
}

function modal(context) {
  var modal = document.createElement('div');
  var dialog = document.createElement('div');
  var content = document.createElement('div');
  var close = document.createElement('a');
  var backdrop = document.createElement('div');

  modal.className = 'modal fade';
  dialog.className = 'modal-dialog modal-window';
  content.className = 'modal-content';
  backdrop.className = 'modal-backdrop fade';

  close.href = '#';
  close.className = 'modal-close';
  close.title = 'Close';
  close.innerText = 'Close';

  content.appendChild(close);
  if (context) content.appendChild(context);
  dialog.appendChild(content);
  modal.appendChild(dialog);

  showEvent();

  close.onclick = function (event) {
    event.preventDefault();
    closeEvent(modal, backdrop);
  }

  modal.onclick = function(event) {
    if (event.target === modal) {
      closeEvent(modal, backdrop);
    }
  }

  function showEvent() {
    document.body.appendChild(modal);
    document.body.appendChild(backdrop);
    document.body.classList.add('modal-open');
    document.body.classList.add('pr-14');
    setTimeout(() => {
      modal.classList.add('show')
      modal.classList.add('d-block')
      modal.classList.add('pr-14')
      backdrop.classList.add('show')
    }, 50);
  }
}

function closeEvent(modal, backdrop) {
  modal.classList.remove('show');
  modal.classList.remove('d-block');
  modal.classList.remove('pr-14');
  backdrop.classList.remove('show');
  document.body.classList.remove('modal-open');
  document.body.classList.remove('pr-14');
  setTimeout(() => {
    modal.remove();
    backdrop.remove();
  }, 300);
}

function resetForm(form) {
  form.classList.remove('was-validated');
  form.reset();
}

function dropdown(e) {
  e = e || window.event;

  var target = e.target || e.srcElement;
  var dropdown = target.closest('.dropdown');
  var menu = dropdown.querySelector('.dropdown-menu');

  e.preventDefault();

  menu.classList.toggle('show');
}