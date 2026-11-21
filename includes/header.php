<?php
/**
 * header.php
 * Opens the page: <head>, performance check, navigation bar.
 * Every page includes this first.
 *
 * Before including it, a page can set:
 *   $page_title  - text for the browser tab (default: your name)
 *   $page_desc   - search-engine description (default: from config.php)
 *   $current     - 'home' | 'about' | 'projects' | 'contact' (highlights the menu link)
 *   $body_class  - optional extra class on <body>, for page-specific styling
 *   $extra_head  - optional extra HTML for <head>
 */
require_once __DIR__ . '/config.php';

$page_title = $page_title ?? $site['name'];
$page_desc  = $page_desc  ?? $site['description'];
$current    = $current    ?? '';
$body_class = $body_class ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?= e($page_title) ?></title>
  <meta name="description" content="<?= e($page_desc) ?>">
  <meta name="theme-color" content="#070c29">

  <!-- Link previews (WhatsApp, LinkedIn and so on) -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= e($page_title) ?>">
  <meta property="og:description" content="<?= e($page_desc) ?>">
  <meta name="twitter:card" content="summary">

  <!-- Tab icon: a small "CB" tile drawn as SVG, so no extra file is needed -->
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='14' fill='%23070c29'/%3E%3Ctext x='32' y='42' font-size='28' font-family='Arial' font-weight='700' text-anchor='middle' fill='%23ff9e0d'%3ECB%3C/text%3E%3C/svg%3E">

  <!-- Fonts: Syne for headings, Manrope for text -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap">

  <link rel="stylesheet" href="assets/css/style.css">
  <?= $extra_head ?? '' ?>

  <script>
    /* Runs before the page paints. Two jobs:
       1. Adds the "js" class so CSS knows JavaScript is available.
       2. Rates the device (high / mid / low) and stores it as data-perf on <html>.
          The CSS and later the 3D hero use it to switch effects down on weaker devices.
       Testing tip: add ?perf=low (or mid / high) to any page address to force a tier. */
    (function () {
      var root = document.documentElement;
      root.classList.add('js');

      var forced = (location.search.match(/[?&]perf=(high|mid|low)/) || [])[1];
      var memory = navigator.deviceMemory || 4;          /* GB; not reported by every browser */
      var cores  = navigator.hardwareConcurrency || 4;
      var saveData = navigator.connection && navigator.connection.saveData;
      var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      var tier = 'high';
      if (reduceMotion || saveData || memory <= 1 || cores <= 2) {
        tier = 'low';
      } else if (memory <= 2 || cores <= 4 || window.innerWidth < 768) {
        tier = 'mid';
      }
      root.setAttribute('data-perf', forced || tier);
    })();
  </script>
</head>
<body class="<?= e($body_class) ?>">
  <a class="skip-link" href="#main">Skip to content</a>

  <header class="site-header" id="siteHeader">
    <div class="container nav-wrap">

      <a class="brand" href="index.php" aria-label="<?= e($site['name']) ?>, home">
        <span class="brand-name">Chiagoziem <small>Blaise Charles</small></span>
      </a>

      <nav class="main-nav" id="mainNav" aria-label="Main">
        <ul>
          <?php foreach ($nav as [$label, $file, $key]): ?>
            <li>
              <a class="nav-link" href="<?= e($file) ?>"<?= $key === $current ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
        <!-- Shown only inside the phone menu -->
        <a class="btn btn-ghost btn-with-icon nav-cta-mobile" href="projects.php">
          View projects
          <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
        </a>
      </nav>

      <a class="btn btn-ghost btn-with-icon nav-cta" href="projects.php">
        View projects
        <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
      </a>

      <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="mainNav">
        <span class="sr-only">Menu</span>
        <span class="nav-toggle-bars" aria-hidden="true"></span>
      </button>

    </div>
  </header>

  <main id="main">
