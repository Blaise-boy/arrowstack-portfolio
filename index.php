<?php
/**
 * index.php  -  Home page
 * Sections: hero (headline + avatar), about preview, featured projects, contact call-to-action.
 */
$page_title = 'Chiagoziem Blaise Charles | Web Developer and Aspiring Machine Learning Engineer';
$page_desc  = 'I build with code, solve with data and create impact. Explore the web projects of Chiagoziem Blaise Charles, a developer with a BSc in Human Anatomy and an aspiring machine learning engineer.';
$current    = 'home';

/* Home-only files. header.php and footer.php print these in the right places. */
$extra_head = '<link rel="preload" as="image" href="assets/img/avatar/center.webp" type="image/webp">
  <link rel="stylesheet" href="assets/css/home.css">';
$extra_scripts = '<script src="assets/js/tilt.js" defer></script>
  <script src="assets/js/home.js" defer></script>
  <script type="module" src="assets/js/companion.js"></script>';

$projects = require __DIR__ . '/includes/projects.php';

require __DIR__ . '/includes/header.php';
?>

<!-- HERO ---------------------------------------------------------------- -->
<section class="hero" id="hero">
  <!-- 3D companion: companion.js draws a small object here that follows your mouse or finger -->
  <canvas class="companion" id="companion" aria-hidden="true"></canvas>

  <div class="container hero-grid">

    <div class="hero-copy">
      <p class="eyebrow rise"><?= e($site['role']) ?></p>
      <h1>
        <span class="line rise" style="--d:80ms">I build with code.</span>
        <span class="line rise" style="--d:160ms">I solve with data.</span>
        <span class="line rise" style="--d:240ms">I create impact.</span>
      </h1>
      <p class="hero-intro rise" style="--d:340ms">
        I'm a developer with a BSc in Human Anatomy, currently completing a postgraduate
        diploma in Public Health. I build intelligent systems, meaningful experiences
        and data-driven solutions.
      </p>
      <div class="hero-actions rise" style="--d:440ms">
        <a class="btn btn-primary" href="projects.php">View projects</a>
        <a class="btn btn-ghost" href="about.php">About me</a>
      </div>
    </div>

    <!-- Avatar stage: glow, giant name, avatar. home.js tilts the avatar toward the pointer. -->
    <div class="stage" id="heroStage">
      <div class="stage-orb" aria-hidden="true"></div>
      <div class="stage-name" aria-hidden="true">Blaise</div>
      <div class="avatar-stack">
        <img class="avatar-img" src="assets/img/avatar/center.webp" width="640" height="857"
             alt="Illustrated portrait of Chiagoziem Blaise Charles" fetchpriority="high" decoding="async">
      </div>
    </div>

  </div>
</section>


<!-- ABOUT PREVIEW --------------------------------------------------------- -->
<section class="section section-light home-about">
  <div class="container">

    <div class="about-head" data-reveal>
      <h2>Where science meets technology.</h2>
      <div>
        <p class="prose">
          My journey bridges health sciences and technology. I combine analytical thinking,
          problem-solving and creative development to build solutions that make a difference.
        </p>
        <a class="btn btn-ghost btn-with-icon" href="about.php">
          More about me
          <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
        </a>
      </div>
    </div>

    <ul class="expertise" data-reveal data-reveal-delay="100">
      <li>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12h4l2-6 4 12 2-6h6"/></svg>
        <h3>Health science</h3>
        <p>BSc in Human Anatomy, a Public Health PGD currently in progress, and hospital experience in histopathology.</p>
      </li>
      <li>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="5" cy="6" r="2"/><circle cx="19" cy="6" r="2"/><circle cx="12" cy="19" r="2"/><circle cx="12" cy="11.5" r="1.6"/><path d="M6.6 7.4l4.2 3M17.4 7.4l-4.2 3M12 13.1V17"/></svg>
        <h3>Machine learning</h3>
        <p>Python, Pandas and Scikit-learn, plus a medical chatbot built during a Data Science and AI internship.</p>
      </li>
      <li>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 8l-5 4 5 4M16 8l5 4-5 4M14 5l-4 14"/></svg>
        <h3>Web development</h3>
        <p>Full-stack websites with HTML, CSS, JavaScript, PHP and MySQL.</p>
      </li>
    </ul>

  </div>
</section>


<!-- FEATURED PROJECTS ------------------------------------------------------ -->
<section class="section" id="work">
  <div class="container">

    <div class="section-head" data-reveal>
      <div>
        <h2>Selected work</h2>
        <p class="prose">Live websites I designed and built from scratch.</p>
      </div>
      <a class="btn btn-ghost btn-with-icon" href="projects.php">
        See all projects
        <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
      </a>
    </div>

    <div class="projects-grid">
      <?php foreach ($projects as $i => $p): ?>
        <div class="project-wrap" data-reveal data-reveal-delay="<?= $i * 120 ?>">
          <article class="project glass glass-hover" data-tilt>
            <a class="project-media" href="<?= e($p['url']) ?>" target="_blank" rel="noopener noreferrer" tabindex="-1" aria-hidden="true">
              <img src="<?= e($p['image']) ?>" alt="" width="1000" height="496" loading="lazy" decoding="async">
            </a>
            <div class="project-body">
              <h3><?= e($p['name']) ?></h3>
              <p><?= e($p['summary']) ?></p>
              <ul class="tags" aria-label="Technologies used">
                <?php foreach ($p['tags'] as $tag): ?><li><?= e($tag) ?></li><?php endforeach; ?>
              </ul>
              <a class="project-link" href="<?= e($p['url']) ?>" target="_blank" rel="noopener noreferrer">
                Visit <?= e($p['name']) ?><span class="sr-only"> (opens in a new tab)</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/></svg>
              </a>
            </div>
          </article>
        </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>


<!-- CONTACT CALL-TO-ACTION (shared: includes/cta.php) -->
<?php require __DIR__ . '/includes/cta.php'; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
