<?php
/**
 * projects.php  -  Projects page
 * Sections: page head, stacked project cards, "how I build" band, contact call-to-action.
 * The projects come from includes/projects.php, so adding one there adds a card here.
 */
$page_title = 'Projects | Chiagoziem Blaise Charles';
$page_desc  = 'Live web projects by Chiagoziem Blaise Charles: Velora Suites, a hotel booking platform, and El Gato\'s Academy, an e-learning platform. Built with HTML, CSS, JavaScript, PHP and MySQL.';
$current    = 'projects';

$extra_head    = '<link rel="stylesheet" href="assets/css/projects.css">';
$extra_scripts = '<script src="assets/js/tilt.js" defer></script>
  <script src="assets/js/projects.js" defer></script>';

$projects = require __DIR__ . '/includes/projects.php';
$total    = count($projects);

/* How I build: [step title, text] */
$steps = [
    ['Database first', 'I plan the tables and how they connect before a single page exists, so the data is right from the start.'],
    ['Admin side second', 'The admin dashboard comes next, so everything on the site can be managed from day one.'],
    ['Public pages last', 'The pages visitors see are built on data that already works, which keeps them simple and reliable.'],
];

require __DIR__ . '/includes/header.php';
?>

<!-- PAGE HEAD ------------------------------------------------------------------ -->
<section class="page-head">
  <div class="container">
    <p class="eyebrow" data-reveal>Projects</p>
    <h1 data-reveal data-reveal-delay="80">Things I've built.</h1>
    <p class="page-lead" data-reveal data-reveal-delay="160">
      <?= $total === 1 ? 'A live website' : 'Live websites' ?> designed and built from scratch,
      from the database to the last page. Open any of them and try it.
    </p>
  </div>
</section>


<!-- STACKED PROJECT CARDS --------------------------------------------------------- -->
<section class="stack" aria-label="Projects">
  <div class="container">
    <?php foreach ($projects as $i => $p): ?>
      <?php $host = parse_url($p['url'], PHP_URL_HOST); ?>
      <article class="stack-card" data-stack-card style="--i: <?= $i ?>">

        <div class="stack-media" data-tilt="3">
          <a class="frame" href="<?= e($p['url']) ?>" target="_blank" rel="noopener noreferrer" tabindex="-1" aria-hidden="true">
            <span class="frame-bar">
              <span class="dots" aria-hidden="true"><i></i><i></i><i></i></span>
              <span class="frame-url"><?= e($host) ?></span>
            </span>
            <img class="frame-img" src="<?= e($p['image']) ?>" alt="" width="1000" height="496"
                 loading="<?= $i === 0 ? 'eager' : 'lazy' ?>" decoding="async">
          </a>
        </div>

        <div class="stack-body">
          <p class="stack-index"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?> <span>/ <?= str_pad((string) $total, 2, '0', STR_PAD_LEFT) ?></span></p>
          <p class="stack-type"><?= e($p['type']) ?></p>
          <h2><?= e($p['name']) ?></h2>
          <ul class="stack-features">
            <?php foreach ($p['features'] as $feature): ?><li><?= e($feature) ?></li><?php endforeach; ?>
          </ul>

          <ul class="tags" aria-label="Technologies used">
            <?php foreach ($p['tags'] as $tag): ?><li><?= e($tag) ?></li><?php endforeach; ?>
          </ul>

          <div class="stack-actions">
            <a class="btn btn-primary btn-with-icon" href="<?= e($p['url']) ?>" target="_blank" rel="noopener noreferrer">
              Visit live site<span class="sr-only"> for <?= e($p['name']) ?> (opens in a new tab)</span>
              <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7M8 7h9v9"/></svg></span>
            </a>
          </div>
        </div>

      </article>
    <?php endforeach; ?>

    <!-- Empty spacer: gives the last card room to stay pinned for a moment before the page moves on -->
    <div class="stack-end" aria-hidden="true"></div>
  </div>
</section>


<!-- HOW I BUILD (light band) --------------------------------------------------------- -->
<section class="section section-light">
  <div class="container">
    <div class="section-head" data-reveal>
      <div>
        <h2>How I build</h2>
        <p class="prose">The same order on every project.</p>
      </div>
    </div>

    <ol class="steps">
      <?php foreach ($steps as $n => [$title, $text]): ?>
        <li data-reveal data-reveal-delay="<?= $n * 100 ?>">
          <span class="step-num"><?= $n + 1 ?></span>
          <h3><?= e($title) ?></h3>
          <p><?= e($text) ?></p>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>


<!-- CONTACT CALL-TO-ACTION (shared: includes/cta.php) -->
<?php require __DIR__ . '/includes/cta.php'; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
