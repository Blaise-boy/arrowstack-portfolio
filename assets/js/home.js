/* ==========================================================================
   home.js  -  the avatar in the Home page hero

   The avatar is ONE picture. Instead of swapping between pictures (which
   always looks jumpy), the whole picture turns a few degrees toward your
   mouse or finger, and the giant "Blaise" behind it slides the other way.
   Because the movement is continuous, it is smooth by nature.

   How the smoothness works:
   - The pointer sets a TARGET (where the avatar should lean).
   - Every frame, the CURRENT value moves a fraction of the way to the target.
     That "ease toward the target" is called lerp (linear interpolation).
   - The result is written to two CSS variables, --px and --py (each between
     -1 and +1). home.css turns them into rotation and movement.

   When nobody is touching anything for a moment, the avatar sways gently on
   its own so the page never looks frozen.

   Weak devices (data-perf="low") and people who prefer reduced motion get a
   still picture and none of this runs.
   (The small 3D object that follows the pointer lives in companion.js.)
   ========================================================================== */
(function () {
  'use strict';

  var stage = document.getElementById('heroStage');
  if (!stage) return;

  var root = document.documentElement;
  var weak = root.getAttribute('data-perf') === 'low';
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (weak || reduceMotion) return;                    /* still picture only */

  var targetX = 0, targetY = 0;     /* where the avatar should lean (-1 to +1) */
  var x = 0, y = 0;                 /* where it is right now */
  var lastInput = 0;                /* time of the last pointer movement */
  var IDLE_AFTER = 2500;            /* ms of stillness before the gentle sway starts */

  function clamp(n) { return Math.max(-1, Math.min(1, n)); }

  /* Pointer position, measured from the middle of the stage.
     Works for mouse and for a finger dragging on a phone. */
  window.addEventListener('pointermove', function (event) {
    var box = stage.getBoundingClientRect();
    targetX = clamp((event.clientX - (box.left + box.width / 2)) / (window.innerWidth / 2));
    targetY = clamp((event.clientY - (box.top + box.height / 2)) / (window.innerHeight / 2));
    lastInput = performance.now();
  }, { passive: true });

  /* Only animate while the hero is actually on screen */
  var visible = true, raf = 0, previous = 0;

  function frame(now) {
    raf = window.requestAnimationFrame(frame);

    var dt = Math.min(now - previous, 50);             /* ms since last frame (capped) */
    previous = now;

    if (now - lastInput > IDLE_AFTER) {                /* idle: slow figure-of-eight sway */
      targetX = Math.sin(now / 2300) * 0.35;
      targetY = Math.cos(now / 3100) * 0.18;
    }

    /* Ease toward the target. Using dt keeps the speed the same on 60 Hz and 120 Hz screens. */
    var k = 1 - Math.exp(-dt / 130);
    x += (targetX - x) * k;
    y += (targetY - y) * k;

    stage.style.setProperty('--px', x.toFixed(4));
    stage.style.setProperty('--py', y.toFixed(4));
  }

  function update() {
    var shouldRun = visible && !document.hidden;
    if (shouldRun && !raf) { previous = performance.now(); raf = window.requestAnimationFrame(frame); }
    if (!shouldRun && raf) { window.cancelAnimationFrame(raf); raf = 0; }
  }

  if ('IntersectionObserver' in window) {
    new IntersectionObserver(function (entries) {
      visible = entries[0].isIntersecting;
      update();
    }).observe(stage);
  }
  document.addEventListener('visibilitychange', update);
  update();
})();
