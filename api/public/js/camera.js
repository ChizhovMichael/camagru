(function () {
  'use strict';
  var gallery = document.querySelector('[data-gallery]');
  var stickers = document.querySelector('[data-stickers]');
  var poster = document.querySelector('[data-poster]');
  var canvas = document.querySelector('canvas');
  var video = document.querySelector('[data-stream]');
  var upload = document.querySelector('[data-upload]');
  var button = document.getElementById('save');
  var ctx = canvas.getContext('2d');
  var images = [], selectedImage = null, offsetX = 0, offsetY = 0, startAngle = 0, stream;

  navigator.mediaDevices
    .getUserMedia({
      video: { width: screen.width, height: screen.height }
    })
    .then((mediaStream) => {
      stream = mediaStream;
      video.srcObject = mediaStream;
      video.onloadedmetadata = () => {
        video.play();
      };
    })
    .catch((err) => console.error(`${err.name}: ${err.message}`));

  fetchGallery();
  fetchStickers();
  addUploadButton();

  function stopVideoStream() {
    if (stream) {
      stream.getTracks().forEach(track => track.stop());
      stream = null;
    }
  }

  function fetchGallery() {
    httpClient
      .get(`gallery?items_per_page=0&author=${auth}`)
      .then((response) => {
        response.data.map(
          (element) => gallery.appendChild(createSideImage(element))
        )
        showGallery()
      })
      .catch((error) => notify(error.message, 'error'))
  }

  function fetchStickers() {
    httpClient
      .get('stickers')
      .then((response) => {
        response.map((file) => {
          var sticker = createSticker(file);
          sticker.onclick = pushToCanvas;
          stickers.appendChild(sticker)
        })
        stickers.appendChild(
          createUploadButton(handleUpload)
        )
      })
      .catch((error) => notify(error.message, 'error'))
  }

  function addUploadButton() {
    upload.appendChild(
      createUploadButton(handleReplaceStream)
    )
  }

  function drawCanvas() {
    var el = poster.style.display === 'block' ? poster : video;
    var width = el.offsetWidth;
    var height = el.offsetHeight;
    var dpr = 1;

    canvas.width = width;
    canvas.height = height;

    ctx.scale(dpr, dpr);
    drawImages();
  }

  function pushToCanvas(event) {
    event.preventDefault();
    event.stopPropagation();

    var img = this;
    var width = img.naturalWidth;
    var height = img.naturalHeight;
    var maxSize = 300;

    if (width > maxSize || height > maxSize) {
      var widthRatio = maxSize / width;
      var heightRatio = maxSize / height;
      var scaleFactor = Math.min(widthRatio, heightRatio);

      width = width * scaleFactor;
      height = height * scaleFactor;
    }

    images.push({
      img: img,
      x: 50,
      y: 50,
      width: width,
      height: height,
      angle: 0
    });

    drawCanvas();
  }

  function drawImages() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    images.forEach((image) => {
      ctx.save();
      // center of image
      ctx.translate(image.x + image.width / 2, image.y + image.height / 2);
      ctx.rotate(image.angle);
      // render image with center in x = width / 2
      ctx.drawImage(image.img, -image.width / 2, -image.height / 2, image.width, image.height);
      ctx.restore();
    });
    button.disabled = images.length === 0;
  }

  function startDrag(e) {
    var rect = getCursorPosition(canvas, e);
    var x = rect[0];
    var y = rect[1];

    selectedImage = images.find(image =>
      x > image.x && x < image.x + image.width &&
      y > image.y && y < image.y + image.height
    );

    if (selectedImage) {
      offsetX = x - selectedImage.x;
      offsetY = y - selectedImage.y;
      startAngle = selectedImage.angle;
    }
  }

  function drag(e) {
    if (selectedImage) {
      var rect = getCursorPosition(canvas, e);
      var x = rect[0];
      var y = rect[1];

      selectedImage.x = x - offsetX;
      selectedImage.y = y - offsetY;

      drawImages();
    }
  }

  function endDrag() {
    selectedImage = null;
  }

  function keyDownEvent(event) {
    if (selectedImage) {
      if (event.key === 'ArrowRight') {
        selectedImage.angle += 0.1;
      } else if (event.key === 'ArrowLeft') {
        selectedImage.angle -= 0.1;
      } else if (event.key === 'Backspace') {
        images = images.filter(image => image !== selectedImage);
        selectedImage = null;
      } else if (event.key === 'ArrowUp') {
        selectedImage.width *= 1.1;
        selectedImage.height *= 1.1;
      } else if (event.key === 'ArrowDown') {
        selectedImage.width *= 0.9;
        selectedImage.height *= 0.9;
      }
    }

    drawImages();
  }

  function getCursorPosition(canvas, event) {
    var rect = canvas.getBoundingClientRect()
    var x = event.clientX - rect.left;
    var y = event.clientY - rect.top;

    return [x, y];
  }

  function handlePhoto(event) {
    event.preventDefault();

    if (!images.length) {
      return false;
    }

    modal(
      createPreviewForm(
        poster.style.display === 'block' ? poster : video
      )
    );
  }

  function handleUpload(event) {
    event.preventDefault();
    event.stopPropagation();

    const input = this;
    const file = this.files[0];
    if (!file) {
      return;
    }

    const formData = new FormData();
    formData.append('image', file);

    httpClient
      .upload('upload/sticker', formData)
      .then((response) => {
        var sticker = createSticker(response.message);
        sticker.onclick = pushToCanvas;
        input.parentNode.insertAdjacentElement('beforebegin', sticker);
      })
      .catch((err) => notify(err.message, 'error'))
  }

  function handleReplaceStream(event) {
    event.preventDefault();
    event.stopPropagation();

    const file = this.files[0];
    if (!file) {
      return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
      stopVideoStream();

      video.style.display = 'none';
      poster.src = e.target.result;
      poster.style.display = 'block';
      poster.dataset.mimeType = file.type;
    };
    reader.readAsDataURL(file);
  }

  function createCanvas(source) {
    var sourceCanvas = document.createElement('canvas');
    var sourceContext = sourceCanvas.getContext('2d');

    sourceCanvas.width = source.videoWidth || source.naturalWidth;
    sourceCanvas.height = source.videoHeight || source.naturalHeight;

    sourceContext.drawImage(source, 0, 0, sourceCanvas.width, sourceCanvas.height);

    return sourceCanvas;
  }

  function dataURItoBlob(dataURI) {
    var byteString = atob(dataURI.split(',')[1]);
    var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

    var ab = new ArrayBuffer(byteString.length);
    var ia = new Uint8Array(ab);
    for (let i = 0; i < byteString.length; i++) {
      ia[i] = byteString.charCodeAt(i);
    }

    return new Blob([ab], { type: mimeString });
  }

  function send(sourceCanvas, mimeType = 'image/png') {
    var canvasDataURL = canvas.toDataURL('image/png');
    var sourceDataURL = sourceCanvas.toDataURL(mimeType);

    var canvasBlob = dataURItoBlob(canvasDataURL);
    var sourceBlob = dataURItoBlob(sourceDataURL);

    var formData = new FormData();
    formData.append('canvas', canvasBlob, 'canvas.png');
    formData.append('video', sourceBlob, 'video.png');

    httpClient
      .upload('upload/gallery', formData)
      .then((response) => {
        gallery.insertAdjacentElement('afterbegin', createSideImage(response));
        showGallery();
      })
      .catch((err) => notify(err.message, 'error'));
  }

  function showGallery() {
    var parent = gallery.parentNode;
    if (gallery.children.length) {
      parent.style.display = 'block';
    } else {
      parent.style.display = 'none';
    }
  }

  function createPreviewForm(source) {
    var context = document.createElement('div');
    var footer = document.createElement('div');
    var previewCanvas = document.createElement('canvas');
    var previewContext = previewCanvas.getContext('2d');
    var sourceWidth = source.videoWidth || source.naturalWidth;
    var sourceHeight = source.videoHeight || source.naturalHeight;
    var canvasWidth = canvas.width;
    var canvasHeight = canvas.height;
    var canvasOffsetX = 0, canvasOffsetY = 0, sourceOffsetX = 0, sourceOffsetY = 0;
    var accept = document.createElement('button');
    var close = document.createElement('button');
    var header = document.createElement('h2');
    var description = document.createElement('p');

    // stickers width > image width
    if (canvasWidth > sourceWidth) {
      canvasHeight = sourceWidth * canvasHeight / canvasWidth;
      canvasWidth = sourceWidth;
    }

    // stickers height > image height
    if (canvasHeight > sourceHeight) {
      canvasWidth = sourceHeight * canvasWidth / canvasHeight;
      canvasHeight = sourceHeight;
    }

    // image width > stickers width
    if (sourceWidth > canvasWidth) {
      sourceHeight = canvasWidth * sourceHeight / sourceWidth;
      sourceWidth = canvasWidth;
    }

    if (sourceHeight > canvasHeight) {
      sourceWidth = canvasHeight * sourceWidth / sourceHeight;
      sourceHeight = canvasHeight;
    }

    // center canvas by height
    if (canvasHeight < sourceHeight) {
      canvasOffsetY = (sourceHeight - canvasHeight) / 2;
    }

    // center canvas by width
    if (canvasWidth < sourceWidth) {
      canvasOffsetX = (sourceHeight - canvasWidth) / 2;
    }

    // center image by height
    if (sourceHeight < canvasHeight) {
      sourceOffsetY = (canvasHeight - sourceHeight) / 2;
    }

    // center image by width
    if (sourceWidth < canvasWidth) {
      sourceOffsetX = (canvasWidth - sourceWidth) / 2;
    }

    previewCanvas.width = Math.max(canvasWidth, sourceWidth);
    previewCanvas.height = Math.max(canvasHeight, sourceHeight);

    previewContext.drawImage(source, sourceOffsetX, sourceOffsetY, sourceWidth, sourceHeight);
    previewContext.drawImage(canvas, canvasOffsetX, canvasOffsetY, canvasWidth, canvasHeight);

    previewCanvas.classList.add('w-100');
    previewCanvas.classList.add('my-3');
    footer.classList.add('modal-footer');

    header.innerText = 'Image Preview';
    description.innerText = 'Preview the merged image of your ' +
      'video or uploaded picture with ' +
      'overlayed elements before uploading. ' +
      'View the final result directly in your browser.';

    accept.type = 'button';
    accept.className = 'btn btn-primary';
    accept.innerText = 'Accept';
    accept.onclick = function (event) {
      handleAcceptImage(event, source);
    };

    close.type = 'button';
    close.className = 'btn btn-secondary';
    close.innerText = 'Cancel';
    close.onclick = handleCancelImage;

    context.appendChild(header);
    context.appendChild(description);
    footer.appendChild(close);
    footer.appendChild(accept);
    context.appendChild(previewCanvas);
    context.appendChild(footer);

    return context;
  }

  function handleAcceptImage(event, source) {
    event.preventDefault();
    event.stopPropagation();

    var button = event.target;
    var modal = button.closest('.modal');
    var backdrop = document.querySelector('.modal-backdrop');

    if (poster.style.display === 'block') {
      var image = new Image();
      image.src = poster.src;
      image.onload = function () {
        send(createCanvas(source), poster.dataset.mimeType);
        closeEvent(modal, backdrop);
      };
      return true;
    }

    send(createCanvas(source));
    closeEvent(modal, backdrop);
    return true;
  }

  function handleCancelImage(event) {
    event.preventDefault();
    event.stopPropagation();

    var button = this;
    var modal = button.closest('.modal');
    var backdrop = document.querySelector('.modal-backdrop');
    closeEvent(modal, backdrop);
  }

  function createSideImage(element) {
    var inner = document.createElement('div');
    var img = document.createElement('img');
    var button = createButton('/public/img/trash.svg', 'danger', handleDelete);

    inner.className = 'position-relative clickable';
    img.className = 'w-100 rounded mb-3';
    img.alt = element.id;
    img.src = element.file;
    img.dataset.gallery = element.id;
    img.onclick = handleModal;

    button.classList.add('position-absolute');
    button.dataset.gallery = element.id;

    inner.appendChild(img);
    inner.appendChild(button);

    return inner;
  }

  function handleDelete(event) {
    event.preventDefault();
    event.stopPropagation();

    var button = this;
    var gallery = button.dataset.gallery;

    button.disabled = true;
    httpClient
      .delete('gallery/' + gallery, {})
      .then(() => {
        button.parentNode.remove();
        showGallery();
      })
      .catch((error) => notify(error.message, 'error'))
      .finally(() => button.disabled = false);
  }

  button.disabled = true;
  button.addEventListener('click', handlePhoto);
  window.addEventListener('resize', drawCanvas);
  canvas.addEventListener('mousedown', startDrag);
  canvas.addEventListener('mousemove', drag);
  canvas.addEventListener('mouseup', endDrag);
  window.addEventListener('keydown', keyDownEvent);
})();