/* ==========================================================================
   tilt.js  -  3D tilt for any element marked with data-tilt
   Used on the Home page and (soon) the Projects page.

   How it works: while the mouse moves over a card, we work out where the
   pointer is inside it (-0.5 to +0.5 on each axis) and rotate the card a few
   degrees toward that point. CSS "perspective" makes the rotation look 3D.

   Usage:  <div data-tilt>...</div>        default tilt strength
           <div data-tilt="4">...</div>    gentler tilt (degrees)

   Skipped automatically on touch screens, weak devices (data-perf="low")
   and for people who asked for reduced motion.
   ========================================================================== */
(function () {
  'use strict';

  var root = document.documentElement;
  var weakDevice   = root.getAttribute('data-perf') === 'low';
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var hasMouse     = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

  if (weakDevice || reduceMotion || !hasMouse) return;

  document.querySelectorAll('[data-tilt]').forEach(function (card) {
    var strength = parseFloat(card.getAttribute('data-tilt')) || 6;   /* max degrees each way */
    var frame = null;

    card.addEventListener('pointermove', function (event) {
      if (event.pointerType !== 'mouse') return;

      var box = card.getBoundingClientRect();
      var x = (event.clientX - box.left) / box.width  - 0.5;   /* -0.5 (left)  to +0.5 (right)  */
      var y = (event.clientY - box.top)  / box.height - 0.5;   /* -0.5 (top)   to +0.5 (bottom) */

      if (frame) cancelAnimationFrame(frame);
      frame = requestAnimationFrame(function () {
        card.classList.add('is-tilting');
        card.style.transform =
          'perspective(900px) rotateX(' + (-y * strength * 2).toFixed(2) + 'deg) ' +
          'rotateY(' + (x * strength * 2).toFixed(2) + 'deg)';
      });
    });

    card.addEventListener('pointerleave', function () {
      if (frame) cancelAnimationFrame(frame);
      card.classList.remove('is-tilting');
      card.style.transform = '';          /* CSS eases it back to flat */
    });
  });
})();
