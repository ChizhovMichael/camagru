function createLoginForm() {
  var form = document.createElement('form');
  var button = document.createElement('button');

  var inputs = [
    {
      type: 'email',
      name: 'email',
      placeholder: 'Email',
      error: 'Please provide a valid email.',
      validation: {
        required: true
      }
    },
    {
      type: 'password',
      name: 'password',
      placeholder: 'Password',
      error: 'Password must be more than 8 characters.',
      validation: {
        required: true,
        minlength: 8,
      }
    }
  ]

  form.action = '/login';
  form.setAttribute('novalidate', '');
  button.type = 'submit';
  button.className = 'btn btn-primary btn-block mb-4';
  button.innerText = 'Sign in';

  inputs.forEach((input) => {
    form.appendChild(
      createInput(
        input.type,
        input.name,
        input.placeholder,
        input.error,
        input.validation
      )
    )
  })

  form.appendChild(button);
  form.onsubmit = handleAuthentication;

  return form;
}

function createInput(type, name, placeholder, error, validation = {}) {
  var container = document.createElement('div');
  var label = document.createElement('label');
  var input = document.createElement('input');
  var feedback = document.createElement('div');

  container.className = 'mb-3';
  label.for = name;
  label.innerText = placeholder;
  input.name = name;
  input.type = type;
  input.className = 'form-control';
  input.id = name;
  input.placeholder = placeholder;
  feedback.className = 'invalid-feedback';
  feedback.innerText = error;

  Object.keys(validation).forEach((key) => {
    input[key] = validation[key];
  })

  container.appendChild(label);
  container.appendChild(input);
  container.appendChild(feedback);

  return container;
}

function createLike(gallery, active) {
  var like = document.createElement('div');
  var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
  var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
  var span = document.createElement('span');

  like.dataset.gallery = gallery;
  like.classList.add('like');
  if (active) {
    like.classList.add('is-active');
  }

  svg.setAttribute('width', '23');
  svg.setAttribute('height', '23');
  svg.setAttribute('viewBox', '0 0 23 20');
  svg.setAttribute('fill', 'none');

  path.setAttribute('d', 'M11.9649 3.12832C8.29171 -2.5454 0.857422 0.545461 0.857422 6.72603C0.857422 11.3672 11.0494 18.6272 11.9649 19.5712C12.8866 18.6272 22.5717 11.3672 22.5717 6.72603C22.5717 0.592318 15.6449 -2.5454 11.9649 3.12832Z');
  path.setAttribute('fill', '#3E4373');

  svg.appendChild(path);
  like.appendChild(svg);
  like.appendChild(span);

  return like;
}

function createCommentForm(galleryId) {
  var container = document.createElement('div');

  try {
    var response = httpClient.sync(`gallery/${galleryId}`);
  } catch (e) {
    notify(e.message, 'error');
    throw e;
  }

  var avatar = createAvatar(response.username);
  var preview = createPreview(response.file);
  var form = document.createElement('form');
  var button = document.createElement('button');
  var input = createInput(
    'text',
    'comment',
    'Add comment',
    'Please provide a valid comment.',
    {
      required: true
    }
  );

  avatar.classList.add('mb-3');
  form.setAttribute('novalidate', '');
  form.className = 'mb-4';
  form.onsubmit = handleComment;
  form.dataset.gallery = galleryId;
  button.type = 'submit';
  button.className = 'btn btn-primary btn-sm';
  button.innerText = 'Post comment';

  form.appendChild(input);
  form.appendChild(button);

  container.appendChild(avatar);
  container.appendChild(preview);
  container.appendChild(form);
  response.comments.map((comment) => {
    container.appendChild(
      createComment(comment.username, comment.message, comment.created_at)
    );
  })

  return container;
}

function createAvatar(username) {
  var avatarOuter = document.createElement('div');
  var avatar = document.createElement('img');
  var span = document.createElement('span');

  if (!username) {
    return avatarOuter;
  }

  avatarOuter.className = 'd-flex align-items-center';
  avatar.src = '/public/img/avatar.jpg';
  avatar.className = 'rounded-circle';
  avatar.style = 'width: 35px;';
  avatar.alt = 'Avatar';
  span.className = 'text-dark btn';
  span.innerText = username;

  avatarOuter.appendChild(avatar);
  avatarOuter.appendChild(span);

  return avatarOuter;
}

function createPreview(src) {
  var outer = document.createElement('div');
  var img = document.createElement('img');
  var facebook = document.createElement('a');
  var facebookImage = document.createElement('img');

  outer.className = 'mb-3';

  img.className = 'w-100 rounded';
  img.src = src;
  img.alt = 'Preview';

  facebook.href = 'https://www.facebook.com/sharer/sharer.php?u=http://localhost:8000';
  facebook.target = '_blank';

  facebookImage.src = '/public/img/facebook.png';
  facebookImage.alt = 'Facebook';
  facebookImage.className = 'my-2';
  facebookImage.width = 100;

  facebook.appendChild(facebookImage);
  outer.appendChild(img);
  outer.appendChild(facebook);

  return outer;
}

function createComment(username, message, date) {
  var card = document.createElement('div');
  var cardBody = document.createElement('div');
  var info = document.createElement('div');
  var infoInner = document.createElement('div');
  var createdAt = document.createElement('p');
  var comment = document.createElement('p');
  var avatar = createAvatar(username)

  card.className = 'mb-4';
  info.className = 'd-flex flex-start align-items-center mb-3';
  createdAt.className = 'text-muted small mb-0';
  createdAt.innerText = `Shared - ${(new Date(date)).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false
  })}`;
  comment.className = 'mb-0';
  comment.style = 'line-height: 1.15';
  comment.innerText = message;

  infoInner.appendChild(createdAt);
  info.appendChild(avatar);
  info.appendChild(infoInner);
  cardBody.appendChild(info);
  cardBody.appendChild(comment);
  card.appendChild(cardBody);

  return card;
}

function createGallery(element) {
  var inner = document.createElement('div');
  var img = document.createElement('img');

  inner.className = 'position-relative clickable';
  inner.role = 'button';
  img.className = 'w-100 rounded mb-4';
  img.alt = element.id;
  img.dataset.gallery = element.id;
  img.src = element.file;

  if (auth) {
    var like = createLike(element.id, element.is_liked);
    like.onclick = handleLike;
    inner.appendChild(like);
  }

  img.onclick = handleModal;
  inner.appendChild(img);

  return inner;
}

function createButton(image, state, handle = null) {
  var button = document.createElement('button');
  var icon = new Image(16);

  icon.src = image;
  button.type = 'button';
  button.className = `btn btn-${state} btn-sm`;
  button.style = 'top: 10px; right: 10px';
  if (handle) button.onclick = handle;

  button.appendChild(icon);

  return button;
}

function createUploadButton(handle) {
  var container = document.createElement('div');
  var input = document.createElement('input');
  var button = createButton('/public/img/upload.svg', 'primary');

  input.type = 'file';
  input.name = 'image';
  input.style = 'display: none';

  container.appendChild(input);
  container.appendChild(button);

  button.addEventListener('click', function() {
    input.click();
  });

  input.onchange = handle;

  return container;
}

function createSticker(file) {
  var img = new Image(70, 70);
  img.src = file + '?direction=stickers';
  img.alt = (Math.random() + 1).toString(36).substring(7);
  img.className = 'mr-4 rounded-circle border border-light object-fit-cover';

  return img;
}