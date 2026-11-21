/* ==========================================================================
   main.js  -  behaviour shared by every page
   1. Header style and back-to-top button while scrolling
   2. Phone menu (open, close, keyboard)
   3. Scroll reveal for elements marked data-reveal
   4. Short fade-out when moving between pages
   ========================================================================== */
(function () {
  'use strict';

  var header   = document.getElementById('siteHeader');
  var toggle   = document.querySelector('.nav-toggle');
  var nav      = document.getElementById('mainNav');
  var toTop    = document.getElementById('toTop');
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;


  /* 1. SCROLL STATE -------------------------------------------------------
     requestAnimationFrame lets the browser run this at most once per frame,
     so scrolling stays smooth even on slow devices. */
  var ticking = false;

  function updateOnScroll() {
    var y = window.scrollY || window.pageYOffset;
    if (header) header.classList.toggle('is-scrolled', y > 12);
    if (toTop)  toTop.classList.toggle('is-visible', y > 600);
    ticking = false;
  }

  window.addEventListener('scroll', function () {
    if (!ticking) {
      ticking = true;
      window.requestAnimationFrame(updateOnScroll);
    }
  }, { passive: true });
  updateOnScroll();

  if (toTop) {
    toTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });
  }


  /* 2. PHONE MENU --------------------------------------------------------- */
  function setMenu(open) {
    if (!toggle || !nav) return;
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    nav.classList.toggle('is-open', open);
  }

  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      setMenu(toggle.getAttribute('aria-expanded') !== 'true');
    });

    /* Close after choosing a link */
    nav.addEventListener('click', function (event) {
      if (event.target.closest('a')) setMenu(false);
    });

    /* Close with Escape, and return focus to the button */
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
        setMenu(false);
        toggle.focus();
      }
    });

    /* Close when tapping outside the header */
    document.addEventListener('click', function (event) {
      if (header && !header.contains(event.target)) setMenu(false);
    });

    /* Reset if the window is widened to desktop size */
    window.matchMedia('(min-width: 900px)').addEventListener('change', function (m) {
      if (m.matches) setMenu(false);
    });
  }


  /* 3. SCROLL REVEAL ------------------------------------------------------
     Elements with data-reveal fade in once, the first time they appear.
     Optional: data-reveal-delay="150" staggers items (milliseconds). */
  var revealItems = document.querySelectorAll('[data-reveal]');

  revealItems.forEach(function (item) {
    var delay = item.getAttribute('data-reveal-delay');
    if (delay) item.style.setProperty('--delay', delay + 'ms');
  });

  if ('IntersectionObserver' in window && !reduceMotion) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    revealItems.forEach(function (item) { observer.observe(item); });
  } else {
    revealItems.forEach(function (item) { item.classList.add('is-visible'); });
  }


  /* 4. PAGE-TO-PAGE FADE --------------------------------------------------
     Clicking an internal link fades the page out for a moment, then goes there.
     The next page fades in on its own (see CSS: page-in). */
  if (!reduceMotion) {
    document.addEventListener('click', function (event) {
      var link = event.target.closest('a[href]');
      if (!link) return;

      var sameSite   = link.origin === window.location.origin;
      var webLink    = link.protocol === 'http:' || link.protocol === 'https:';
      var newTab     = link.target === '_blank' || link.hasAttribute('download');
      var modified   = event.metaKey || event.ctrlKey || event.shiftKey || event.altKey;
      var samePageHash = link.pathname === window.location.pathname && link.hash;

      if (!sameSite || !webLink || newTab || modified || samePageHash || event.defaultPrevented) return;

      event.preventDefault();
      document.body.classList.add('is-leaving');
      window.setTimeout(function () { window.location.href = link.href; }, 180);
    });

    /* Coming back with the browser's Back button: undo the fade-out */
    window.addEventListener('pageshow', function (event) {
      if (event.persisted) document.body.classList.remove('is-leaving');
    });
  }
})();
