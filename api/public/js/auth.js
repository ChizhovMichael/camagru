(function() {
  'use strict';
  var form = document.querySelector('form');
  var password = document.querySelector('input[type=password]');
  var progress = document.querySelector('.progress');

  form.onsubmit = handleAuthentication;

  password.addEventListener('input', function () {
    if (!progress) return;

    var strength = 0;
    var bar = document.createElement('div');
    var map = { 0: 0, 1: 25, 2: 50, 3: 75, 4: 100 };

    if (this.value.match(/[a-z]+/)) {
      strength += 1;
    }
    if (this.value.match(/[A-Z]+/)) {
      strength += 1;
    }
    if (this.value.match(/[0-9]+/)) {
      strength += 1;
    }
    if (this.value.match(/[$@#&!]+/)) {
      strength += 1;
    }

    bar.className = 'progress-bar progress-bar-striped progress-bar-animated';
    bar.role = 'progressbar';
    bar.ariaValueNow = map[strength];
    bar.ariaValueMin = 0;
    bar.ariaValueMax = 100;
    bar.style = `width: ${map[strength]}%`;

    progress.innerHTML = '';
    progress.appendChild(bar);
  })
})()
