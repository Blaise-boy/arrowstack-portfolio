/* ==========================================================================
   contact.js  -  checks the contact form before it is sent

   What it does:
   1. Checks each field when you leave it, and every field again on Send.
   2. Shows a clear message under any field that needs fixing.
   3. Shows a live character count on the message box.
   4. Stops double-clicks while the message is being sent.

   The form still works without this script, because PHP checks everything
   again on the server. The messages and limits here match contact.php.
   ========================================================================== */
(function () {
  'use strict';

  var form = document.getElementById('contactForm');

  /* After a successful send, move focus to the thank-you note so screen readers announce it */
  var note = document.getElementById('sentNote');
  if (note) { note.focus(); }
  if (!form) return;

  var button = document.getElementById('sendBtn');
  var counter = document.getElementById('messageCount');
  var MAX_MESSAGE = 3000;

  form.setAttribute('novalidate', '');      /* we show our own messages instead of the browser's */

  /* Each rule returns an error message, or '' when the field is fine */
  var rules = {
    name: function (v) {
      if (v.length < 2) return 'Please enter your name.';
      if (v.length > 150) return 'Your name is too long (150 characters at most).';
      return '';
    },
    email: function (v) {
      if (v === '') return 'Please enter your email address.';
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v)) return 'That email address doesn\'t look right.';
      if (v.length > 150) return 'That email address is too long.';
      return '';
    },
    subject: function (v) {
      if (v.length > 200) return 'The subject is too long (200 characters at most).';
      return '';
    },
    message: function (v) {
      if (v.length < 10) return 'Please write a message of at least 10 characters.';
      if (v.length > MAX_MESSAGE) return 'Your message is too long (3000 characters at most).';
      return '';
    }
  };

  function show(field, text) {
    var input = form.elements[field];
    var error = document.getElementById(field + '-error');
    var wrap = input.closest('.field');
    error.textContent = text;
    wrap.classList.toggle('has-error', text !== '');
    if (text) {
      input.setAttribute('aria-invalid', 'true');
      input.setAttribute('aria-describedby', field + '-error');
    } else {
      input.removeAttribute('aria-invalid');
      input.removeAttribute('aria-describedby');
    }
  }

  function check(field) {
    var text = rules[field](form.elements[field].value.trim());
    show(field, text);
    return text === '';
  }

  Object.keys(rules).forEach(function (field) {
    var input = form.elements[field];
    input.addEventListener('blur', function () { check(field); });
    /* Once a field shows an error, re-check as the person types so it clears the moment it is fixed */
    input.addEventListener('input', function () {
      if (input.closest('.field').classList.contains('has-error')) check(field);
    });
  });

  /* Character counter */
  var countNumber = counter ? counter.querySelector('span') : null;
  function updateCount() {
    if (!countNumber) return;
    var n = form.elements.message.value.length;
    countNumber.textContent = n;
    counter.classList.toggle('is-near', n > MAX_MESSAGE * 0.9);
  }
  form.elements.message.addEventListener('input', updateCount);
  updateCount();

  /* On Send: check everything, and focus the first field that needs fixing */
  form.addEventListener('submit', function (event) {
    var firstBad = null;
    Object.keys(rules).forEach(function (field) {
      if (!check(field) && !firstBad) firstBad = form.elements[field];
    });

    if (firstBad) {
      event.preventDefault();
      firstBad.focus();
      return;
    }

    button.disabled = true;
    button.querySelector('.btn-label').textContent = 'Sending...';
  });

  /* Back button after a send can restore a disabled button: switch it back on */
  window.addEventListener('pageshow', function () {
    button.disabled = false;
    button.querySelector('.btn-label').textContent = 'Send message';
  });
})();
