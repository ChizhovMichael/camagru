function handleAuthentication(event) {
  event.preventDefault();
  event.stopPropagation();

  var form = this;
  var button = this.querySelector('button');
  var email = this.querySelector('input[name=email]');

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
    .then((response) => {
      cookie.set('access_token', response.token, 30);
      cookie.set('refresh_token', response.refresh_token, 30);
      cookie.set('id', response.user.id, 30);
      cookie.set('username', response.user.username, 30);
      cookie.set('roles', response.user.roles.join(', '), 30);
      redirect('/');
    })
    .catch((error) => {
      notify(error.message, 'error');
      if (email) email.setCustomValidity('Invalid field.');
    })
    .finally(() => button.disabled = false);
}

function handleLike(event) {
  event.preventDefault();

  var galleryId = this.dataset.gallery;
  var htmlElement = this;
  var active = !this.classList.contains('is-active');

  httpClient
    .post('relation', {
      gallery_id: galleryId,
      action: active ? 'add' : 'remove'
    })
    .then(() => htmlElement.classList.toggle('is-active'))
    .catch((error) => notify(error.message, 'error'))
}

function handleComment(event) {
  event.preventDefault();
  event.stopPropagation();

  var form = this;
  var button = this.querySelector('button');

  form.classList.add('was-validated');

  if (form.checkValidity() === false) {
    return false;
  }

  var gallery_id = form.dataset.gallery;
  var formValues = getFormValues(this);

  button.disabled = true;
  httpClient
    .post('comment', Object.assign(formValues, { gallery_id }))
    .then((response) => {
      form.insertAdjacentElement(
        'afterend',
        createComment(response.username, response.message, response.created_at)
      );
      resetForm(form);
    })
    .catch((error) => notify(error.message, 'error'))
    .finally(() => button.disabled = false);
}

function handleSetting(event) {
  event.preventDefault();
  event.stopPropagation();

  var form = this;
  var button = this.querySelector('button');

  form.classList.add('was-validated');

  if (form.checkValidity() === false) {
    return false;
  }

  var formValues = getFormValues(this);
  var action = new URL(this.action);
  var endpoint = action.pathname.substring(1);

  button.disabled = true;
  httpClient
    .patch(endpoint, formValues)
    .then(() => window.location.reload())
    .catch((error) => notify(error.message, 'error'))
    .finally(() => button.disabled = false);
}

function handleModal(event) {
  event.preventDefault();

  var context = document.createElement('div');
  var title = document.createElement('h1');
  var description = document.createElement('p');
  var body;

  if (auth) {
    body = createCommentForm(event.target.dataset.gallery);
  } else {
    title.innerText = 'Sign in';
    description.innerText = 'Log in to securely access your account and explore all available features.';
    body = createLoginForm();
    context.appendChild(title);
    context.appendChild(description);
  }

  context.appendChild(body);

  modal(context);
}

