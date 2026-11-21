/* ==========================================================================
   projects.js  -  the stacked 3D cards on the Projects page

   The sticky "pile up" itself is pure CSS (see projects.css). This script adds
   the depth: while the next card slides up over a card, that card shrinks
   slightly, tips back a touch and darkens, so the deck looks like it has layers.

   How it works, per scroll frame:
   1. For every card after the first, work out an "arrival" value from 0 to 1:
      0 = still far below the screen, 1 = fully arrived at its resting spot.
   2. A card is pushed back by the arrivals of every card stacked on top of it.
      Two cards on top push it back twice as far, so the pile gets deeper.
   3. That push becomes a scale, a small tilt and a shade (dimming amount).

   Phones, short windows, weak devices and people who prefer reduced motion
   get a plain list, so this script does nothing for them.
   ========================================================================== */
(function () {
  'use strict';

  var cards = [].slice.call(document.querySelectorAll('[data-stack-card]'));
  if (cards.length < 2) return;

  var root = document.documentElement;
  var weak = root.getAttribute('data-perf') === 'low';
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var wide = window.matchMedia('(min-width: 900px) and (min-height: 640px)');
  if (weak || reduceMotion) return;

  var restingTop = [];           /* where each card sticks, in px from the top of the window */
  var ticking = false;

  function measure() {
    restingTop = cards.map(function (card) {
      return parseFloat(window.getComputedStyle(card).top) || 0;
    });
  }

  function clear() {
    cards.forEach(function (card) {
      card.style.transform = '';
      card.style.removeProperty('--shade');
    });
  }

  function update() {
    ticking = false;
    if (!wide.matches) { clear(); return; }

    var travel = window.innerHeight * 0.55;        /* how far a card travels while it "arrives" */

    /* arrival[j]: how close card j is to its resting spot (0 to 1) */
    var arrival = cards.map(function (card, j) {
      if (j === 0) return 0;
      var distance = card.getBoundingClientRect().top - restingTop[j];
      return Math.max(0, Math.min(1, 1 - distance / travel));
    });

    cards.forEach(function (card, i) {
      var pushedBack = 0;                          /* total arrival of every card on top of this one */
      for (var j = i + 1; j < cards.length; j++) pushedBack += arrival[j];

      if (pushedBack === 0) { card.style.transform = ''; card.style.setProperty('--shade', '0'); return; }

      var scale = 1 - 0.04 * pushedBack;
      var tilt = -2.5 * Math.min(pushedBack, 1);   /* negative: the bottom edge tips away from you */
      var shade = Math.min(0.6, 0.2 * pushedBack);
      card.style.transform = 'perspective(1400px) rotateX(' + tilt.toFixed(2) + 'deg) scale(' + scale.toFixed(4) + ')';
      card.style.setProperty('--shade', shade.toFixed(3));
    });
  }

  function request() {
    if (!ticking) { ticking = true; window.requestAnimationFrame(update); }
  }

  window.addEventListener('scroll', request, { passive: true });
  window.addEventListener('resize', function () { measure(); request(); });
  wide.addEventListener('change', function () { measure(); request(); });

  measure();
  update();
})();
