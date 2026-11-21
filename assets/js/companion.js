/* ==========================================================================
   companion.js  -  a small 3D object that follows your mouse (or your finger)

   What you see: a small robot with a glowing amber visor, a bobbing antenna and a
   hover glow, plus a short glowing trail and a little drifting dust. It chases
   the pointer with a springy, slightly overshooting motion, turns its head toward
   where it is going and blinks now and then. On a phone it follows your finger
   while you drag across the hero. When nobody is touching anything it floats
   around the avatar. Click or tap and it does a quick spin.

   How it works (the short version)
   1. Three.js (a JavaScript 3D library) draws the scene onto <canvas id="companion">.
   2. Each frame we work out a TARGET point (the pointer, or a spot near the avatar).
   3. A spring pulls the object toward the target. A spring (rather than a plain
      "move 10% closer") gives that soft, alive overshoot.
   4. Screen pixels are converted into 3D world units so the object lands
      exactly where the pointer is.

   Who gets it
   - Three.js is only downloaded on devices that can handle it. Weak devices,
     people who prefer reduced motion and "data saver" users never download it
     and simply see the normal page.
   - If the device turns out to be slow once it is running (under about 25
     frames a second), the object lowers its sharpness once, and if that still
     isn't enough it fades out and switches itself off.

   Testing tip: add ?perf=high, ?perf=mid or ?perf=low to the page address.
   ========================================================================== */

/* Exact version, so an update on the CDN can never change how the site behaves */
const THREE_URL = 'https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js';

const root   = document.documentElement;
const hero   = document.getElementById('hero');
const canvas = document.getElementById('companion');
const stage  = document.getElementById('heroStage');

if (hero && canvas) init();


/* ---- 1. DECIDE WHETHER THIS DEVICE SHOULD GET IT -------------------------- */
function webglAvailable() {
  try {
    const probe = document.createElement('canvas');
    return !!(probe.getContext('webgl2') || probe.getContext('webgl'));
  } catch (err) {
    return false;
  }
}

function whenIdle(fn) {
  const go = () => ('requestIdleCallback' in window
    ? window.requestIdleCallback(fn, { timeout: 2500 })
    : window.setTimeout(fn, 300));
  if (document.readyState === 'complete') go();
  else window.addEventListener('load', go);
}

function init() {
  const forced       = /[?&]perf=/.test(location.search);       /* testing override */
  const tier         = root.getAttribute('data-perf') || 'high'; /* set in header.php */
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const saveData     = !!(navigator.connection && navigator.connection.saveData);
  const memory       = navigator.deviceMemory || 4;              /* GB, not reported by every browser */
  const cores        = navigator.hardwareConcurrency || 4;

  if (tier === 'low' || reduceMotion || saveData) return;
  if (!forced && (memory < 3 || cores < 4)) return;
  if (!webglAvailable()) return;

  /* Wait until the page has finished loading, so the 3D library never slows the first paint */
  whenIdle(async () => {
    let THREE;
    try {
      THREE = await import(THREE_URL);
    } catch (err) {
      return;                              /* offline or blocked: the page works without it */
    }
    run(THREE, tier, forced);
  });
}


/* ---- 2. THE SCENE ---------------------------------------------------------- */
function run(THREE, tier, forced) {
  const mid = tier === 'mid';              /* phones and mid-range laptops: lighter settings */

  const AMBER = 0xff9e0d;
  const BONE  = 0xefe7da;
  const FOV = 40, DIST = 10;               /* camera field of view and distance */

  const renderer = new THREE.WebGLRenderer({
    canvas, alpha: true, antialias: !mid, powerPreference: 'low-power'
  });
  let pixelRatio = Math.min(window.devicePixelRatio || 1, mid ? 1 : 1.75);
  renderer.setPixelRatio(pixelRatio);

  const scene  = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(FOV, 1, 0.1, 100);
  camera.position.z = DIST;

  /* Lights: soft Bone from the top-right, warm Amber from the bottom-left */
  scene.add(new THREE.AmbientLight(BONE, 0.75));
  const key = new THREE.DirectionalLight(BONE, 1.7);
  key.position.set(3, 5, 8);
  scene.add(key);
  const warm = new THREE.PointLight(AMBER, 45, 30);
  warm.position.set(-4, -3, 5);
  scene.add(warm);

  /* A soft round dot, drawn once, reused for the glow, the trail and the dust */
  function makeDot() {
    const c = document.createElement('canvas');
    c.width = c.height = 64;
    const g = c.getContext('2d');
    const grad = g.createRadialGradient(32, 32, 0, 32, 32, 32);
    grad.addColorStop(0, 'rgba(255,255,255,1)');
    grad.addColorStop(0.35, 'rgba(255,255,255,0.35)');
    grad.addColorStop(1, 'rgba(255,255,255,0)');
    g.fillStyle = grad;
    g.fillRect(0, 0, 64, 64);
    const texture = new THREE.CanvasTexture(c);
    texture.colorSpace = THREE.SRGBColorSpace;
    return texture;
  }
  const dot = makeDot();

  /* The robot: a few simple shapes grouped together.
     The numbers are in arbitrary units (the whole robot is about 2.4 tall);
     buddy.scale, set in resize(), shrinks it to the right size on screen.
     Two groups: "buddy" only moves around, "robot" is what turns, tilts and spins. */
  const buddy = new THREE.Group();
  scene.add(buddy);
  const robot = new THREE.Group();
  buddy.add(robot);

  const boneMat  = new THREE.MeshStandardMaterial({ color: BONE, roughness: 0.38, metalness: 0.08 });
  const visorMat = new THREE.MeshStandardMaterial({ color: 0x0b1233, roughness: 0.18, metalness: 0.6 });
  const amberMat = new THREE.MeshStandardMaterial({
    color: AMBER, roughness: 0.4, metalness: 0.2, emissive: AMBER, emissiveIntensity: 0.55
  });
  const eyeMat = new THREE.MeshBasicMaterial({ color: AMBER });

  /* Soft glow behind the robot (stays upright, does not turn) */
  const glow = new THREE.Sprite(new THREE.SpriteMaterial({
    map: dot, color: AMBER, transparent: true, opacity: 0.32,
    blending: THREE.AdditiveBlending, depthWrite: false
  }));
  glow.scale.set(3.2, 3.2, 1);
  buddy.add(glow);

  /* Head: a slightly squashed sphere */
  const head = new THREE.Mesh(new THREE.SphereGeometry(0.62, 28, 20), boneMat);
  head.scale.set(1.15, 0.88, 0.95);
  head.position.y = 0.35;

  /* Visor: a dark flattened sphere on the front of the head */
  const visor = new THREE.Mesh(new THREE.SphereGeometry(0.5, 24, 16), visorMat);
  visor.scale.set(1.1, 0.66, 0.5);
  visor.position.set(0, 0.35, 0.38);

  /* Eyes: two glowing amber ovals. They slide around the visor to "look" and shrink to blink. */
  const eyes = new THREE.Group();
  eyes.position.set(0, 0.38, 0.64);
  [-1, 1].forEach((side) => {
    const eye = new THREE.Mesh(new THREE.SphereGeometry(0.085, 12, 10), eyeMat);
    eye.scale.set(1, 1.25, 0.35);
    eye.position.x = side * 0.2;
    eyes.add(eye);
  });

  /* Ears: two amber discs on the sides of the head */
  const ears = [-1, 1].map((side) => {
    const ear = new THREE.Mesh(new THREE.CylinderGeometry(0.17, 0.17, 0.1, 20), amberMat);
    ear.rotation.z = Math.PI / 2;
    ear.position.set(side * 0.73, 0.35, 0);
    return ear;
  });

  /* Body: a small egg shape under the head, with a chest light */
  const body = new THREE.Mesh(new THREE.SphereGeometry(0.42, 24, 18), boneMat);
  body.scale.set(1, 1.05, 0.85);
  body.position.y = -0.5;
  const chest = new THREE.Mesh(new THREE.SphereGeometry(0.07, 12, 10), amberMat);
  chest.position.set(0, -0.42, 0.34);

  /* Antenna: its pivot is at the base, so rotating the group makes it sway */
  const antenna = new THREE.Group();
  antenna.position.set(0, 0.88, 0);
  const stem = new THREE.Mesh(new THREE.CylinderGeometry(0.025, 0.025, 0.34, 8), boneMat);
  stem.position.y = 0.17;
  const tip = new THREE.Mesh(new THREE.SphereGeometry(0.075, 12, 10), amberMat);
  tip.position.y = 0.38;
  antenna.add(stem, tip);

  /* Hover glow under the body, like a tiny thruster */
  const jet = new THREE.Sprite(new THREE.SpriteMaterial({
    map: dot, color: AMBER, transparent: true, opacity: 0.75,
    blending: THREE.AdditiveBlending, depthWrite: false
  }));
  jet.scale.set(1.1, 0.7, 1);
  jet.position.y = -1.05;

  robot.add(head, visor, eyes, ...ears, body, chest, antenna, jet);

  /* Trail: a chain of dots, each following the one before it */
  const TRAIL = mid ? 12 : 26;
  const trailPos = new Float32Array(TRAIL * 3);
  const trailCol = new Float32Array(TRAIL * 3);
  for (let i = 0; i < TRAIL; i++) {
    const fade = Math.pow(1 - i / TRAIL, 1.7) * 0.9;   /* tail end is dimmer */
    trailCol[i * 3] = 1 * fade;
    trailCol[i * 3 + 1] = 0.62 * fade;
    trailCol[i * 3 + 2] = 0.05 * fade;
  }
  const trailGeo = new THREE.BufferGeometry();
  trailGeo.setAttribute('position', new THREE.BufferAttribute(trailPos, 3));
  trailGeo.setAttribute('color', new THREE.BufferAttribute(trailCol, 3));
  const trail = new THREE.Points(trailGeo, new THREE.PointsMaterial({
    size: 9, sizeAttenuation: false, map: dot, vertexColors: true, transparent: true,
    blending: THREE.AdditiveBlending, depthWrite: false
  }));
  trail.frustumCulled = false;
  scene.add(trail);

  /* Dust: tiny specks drifting slowly across the hero, for depth */
  const DUST = mid ? 22 : 56;
  const dustSeed = [];
  const dustPos = new Float32Array(DUST * 3);
  for (let i = 0; i < DUST; i++) {
    dustSeed.push({ nx: Math.random(), ny: Math.random(), speed: 0.4 + Math.random() * 0.9, phase: Math.random() * 6.28 });
  }
  const dustGeo = new THREE.BufferGeometry();
  dustGeo.setAttribute('position', new THREE.BufferAttribute(dustPos, 3));
  const dust = new THREE.Points(dustGeo, new THREE.PointsMaterial({
    size: 3.2, sizeAttenuation: false, map: dot, color: BONE, transparent: true, opacity: 0.4, depthWrite: false
  }));
  dust.frustumCulled = false;
  dust.position.z = -1;
  scene.add(dust);


  /* ---- 3. SIZE AND SCREEN-TO-WORLD CONVERSION ------------------------------
     The canvas covers the whole hero. wpp = "world units per pixel": how much
     3D space one screen pixel covers at the distance where the object floats.
     Multiply a pixel position by wpp and you get its position in the 3D scene. */
  let w = 1, h = 1, wpp = 0.01;
  const sizePx = mid ? 52 : 62;            /* how big the object looks on screen */

  function resize() {
    const r = hero.getBoundingClientRect();
    w = Math.max(1, Math.round(r.width));
    h = Math.max(1, Math.round(r.height));
    renderer.setSize(w, h, false);         /* false = leave the CSS size alone */
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
    wpp = (2 * Math.tan(FOV * Math.PI / 360) * DIST) / h;
    buddy.scale.setScalar(wpp * sizePx / 2);   /* robot is about 2 units wide at scale 1 */
  }
  resize();
  new ResizeObserver(resize).observe(hero);


  /* ---- 4. POINTER INPUT ------------------------------------------------------ */
  const ptr = { x: 0, y: 0, type: 'mouse', last: -1e9, down: false };

  function onMove(event) {
    ptr.x = event.clientX;
    ptr.y = event.clientY;
    ptr.type = event.pointerType || 'mouse';
    ptr.last = performance.now();
  }
  window.addEventListener('pointermove', onMove, { passive: true });
  window.addEventListener('pointerdown', (event) => { onMove(event); pulse = 1; spinT = 1; }, { passive: true });
  window.addEventListener('pointerup', (event) => { if (event.pointerType === 'touch') ptr.last = performance.now(); }, { passive: true });
  document.documentElement.addEventListener('mouseleave', () => { ptr.last = -1e9; });   /* cursor left the window */

  /* Mouse: follows until it has been still for 6 seconds. Finger: only while dragging, plus a moment after. */
  function following(now) {
    return now - ptr.last < (ptr.type === 'touch' ? 1800 : 6000);
  }


  /* ---- 5. THE LOOP ------------------------------------------------------------ */
  let px = 0, py = 0, vx = 0, vy = 0;      /* position and speed, in screen pixels */
  let lean = 0, tilt = 0;                  /* smoothed leaning angles */
  let pulse = 0;                           /* 1 right after a click, fades to 0 */
  let spinT = 0;                           /* 1 right after a click, counts down to 0 over about a second */
  let yaw = 0, pitch = 0;                  /* where the head is turned (radians) */
  let antennaAngle = 0, antennaSpeed = 0;  /* antenna sway, driven by its own little spring */
  let blinkAt = 2500, blinkT = 0;          /* time of the next blink, and how closed the eyes are */
  let started = false;
  let dead = false, raf = 0, last = 0;
  let samples = 0, sampleSum = 0;          /* for the speed check */

  /* Public state, only for testing (when ?perf= is in the address) */
  const state = { x: 0, y: 0, vx: 0, vy: 0, w: 0, h: 0, frames: 0 };
  if (forced) window.__companion = state;

  /* Where should the object be heading, in pixels measured from the top-left of the hero? */
  function target(now, r) {
    if (following(now)) {
      const touch = ptr.type === 'touch';
      return { x: ptr.x - r.left + (touch ? 0 : 30), y: ptr.y - r.top + (touch ? -58 : 26) };   /* beside the cursor, above a finger */
    }
    /* Idle: float in a wide loop around the avatar's head */
    const s = stage ? stage.getBoundingClientRect() : { left: r.left + w * 0.5, top: r.top + h * 0.3, width: 0, height: 0 };
    const t = now / 1000;
    return {
      x: s.left - r.left + s.width * (0.5 + Math.sin(t * 0.7) * 0.46),
      y: s.top - r.top + s.height * (0.36 + Math.sin(t * 1.1 + 1) * 0.15)
    };
  }

  function frame(now) {
    raf = window.requestAnimationFrame(frame);

    const raw = (now - last) / 1000;       /* seconds since last frame */
    last = now;
    const dt = Math.min(raw, 1 / 30);      /* never take a giant step after a lag spike */
    const t = now / 1000;

    const r = canvas.getBoundingClientRect();
    const goal = target(now, r);
    if (!started) { px = goal.x; py = goal.y; }        /* start in place, don't fly in from the corner */

    /* Spring: the further from the goal, the harder it pulls; damping stops it bouncing forever.
       K = pull strength, D = damping. These two numbers set the whole "feel". */
    const K = 62, D = 11;
    vx += ((goal.x - px) * K - vx * D) * dt;
    vy += ((goal.y - py) * K - vy * D) * dt;
    px += vx * dt;
    py += vy * dt;

    /* Pixels to world units. Screen y points down, 3D y points up, hence the minus. */
    const wx = (px - w / 2) * wpp;
    const wy = -(py - h / 2) * wpp + Math.sin(t * 2.1) * 0.05 * buddy.scale.x;   /* tiny float bob */
    buddy.position.set(wx, wy, 0);

    /* Lean into the direction of travel (roll) and nod with vertical speed */
    lean += (Math.max(-0.6, Math.min(0.6, vx / 1700)) - lean) * 0.12;
    tilt += (Math.max(-0.6, Math.min(0.6, vy / 1700)) - tilt) * 0.12;

    /* Turn the head toward where the goal is: left/right (yaw) and up/down (pitch) */
    const toX = Math.max(-1, Math.min(1, (goal.x - px) / (w * 0.22)));
    const toY = Math.max(-1, Math.min(1, (goal.y - py) / (h * 0.22)));
    yaw   += (toX * 0.6  - yaw)   * 0.1;
    pitch += (toY * 0.35 - pitch) * 0.1;

    /* A click makes the robot spin once. smoothstep eases it in and out;
       a full turn (2 x pi) looks identical to no turn, so it lands seamlessly. */
    spinT = Math.max(0, spinT - dt / 0.9);
    const u = 1 - spinT;
    const spin = (u * u * (3 - 2 * u)) * Math.PI * 2;

    robot.rotation.set(tilt * 0.6 + pitch * 0.5, yaw + spin, -lean * 0.8);

    /* Eyes glance the same way as the head, and blink every few seconds */
    if (now > blinkAt) { blinkT = 1; blinkAt = now + 2200 + Math.random() * 3200; }
    blinkT = Math.max(0, blinkT - dt * 9);
    eyes.position.x = (yaw / 0.6) * 0.1;
    eyes.position.y = 0.38 - (pitch / 0.35) * 0.05;
    eyes.scale.y = Math.max(0.12, 1 - blinkT);

    /* Antenna: a small spring that lags behind the movement, so it whips the other way */
    const antennaGoal = Math.max(-0.7, Math.min(0.7, -vx / 1400));
    antennaSpeed += ((antennaGoal - antennaAngle) * 90 - antennaSpeed * 7) * dt;
    antennaAngle += antennaSpeed * dt;
    antenna.rotation.z = antennaAngle;

    /* Click feedback: lights flare and the robot swells for a moment */
    pulse = Math.max(0, pulse - dt * 2.4);
    amberMat.emissiveIntensity = 0.55 + pulse * 1.6;
    robot.scale.setScalar(1 + pulse * pulse * 0.25);

    /* Thruster flickers gently */
    jet.material.opacity = 0.6 + Math.sin(t * 9) * 0.12;

    /* Trail: dot 0 chases the object, each later dot chases the one before it */
    const follow = 1 - Math.exp(-dt * 16);
    let prevX = wx, prevY = wy;
    for (let i = 0; i < TRAIL; i++) {
      const j = i * 3;
      if (!started) { trailPos[j] = wx; trailPos[j + 1] = wy; }
      trailPos[j]     += (prevX - trailPos[j]) * follow;
      trailPos[j + 1] += (prevY - trailPos[j + 1]) * follow;
      trailPos[j + 2] = -0.2;
      prevX = trailPos[j];
      prevY = trailPos[j + 1];
    }
    trailGeo.attributes.position.needsUpdate = true;

    /* Dust drifts slowly to the right and bobs a little */
    for (let i = 0; i < DUST; i++) {
      const d = dustSeed[i];
      const nx = (d.nx + t * 0.012 * d.speed) % 1;
      dustPos[i * 3]     = (nx - 0.5) * w * wpp;
      dustPos[i * 3 + 1] = -((d.ny - 0.5) + Math.sin(t * 0.5 * d.speed + d.phase) * 0.012) * h * wpp;
      dustPos[i * 3 + 2] = 0;
    }
    dustGeo.attributes.position.needsUpdate = true;

    renderer.render(scene, camera);

    if (!started) {
      started = true;
      canvas.classList.add('is-on');       /* fade the canvas in once the first frame is drawn */
    }

    state.x = px; state.y = py; state.vx = vx; state.vy = vy; state.w = w; state.h = h; state.frames++;

    watchSpeed(raw);
  }


  /* ---- 6. SPEED CHECK ----------------------------------------------------------
     After a short warm-up, measure the average frame time. Below about 25 fps:
     first try a lower pixel ratio, and if it is still slow, switch the object off. */
  function watchSpeed(raw) {
    if (forced) return;
    samples++;
    if (samples <= 40) return;             /* ignore the warm-up */
    sampleSum += raw;
    if (samples < 160) return;

    const average = sampleSum / 120;
    samples = 0;
    sampleSum = 0;
    if (average <= 0.04) return;           /* fast enough */

    if (pixelRatio > 1) {
      pixelRatio = 1;
      renderer.setPixelRatio(1);
      resize();
    } else {
      shutDown();
    }
  }

  function shutDown() {
    dead = true;
    window.cancelAnimationFrame(raf);
    raf = 0;
    canvas.classList.remove('is-on');
    window.setTimeout(() => { renderer.dispose(); renderer.forceContextLoss(); }, 900);
  }
  canvas.addEventListener('webglcontextlost', (event) => { event.preventDefault(); shutDown(); });


  /* ---- 7. RUN ONLY WHILE VISIBLE ------------------------------------------------ */
  let visible = true;
  function update() {
    if (dead) return;
    const shouldRun = visible && !document.hidden;
    if (shouldRun && !raf) { last = performance.now(); raf = window.requestAnimationFrame(frame); }
    if (!shouldRun && raf) { window.cancelAnimationFrame(raf); raf = 0; }
  }
  new IntersectionObserver((entries) => { visible = entries[0].isIntersecting; update(); }).observe(hero);
  document.addEventListener('visibilitychange', update);
  update();
}
