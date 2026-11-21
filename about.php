<?php
/**
 * about.php  -  About page
 * Sections: intro (story + portrait), skills, experience, education and certificates, contact call-to-action.
 *
 * The lists below are plain PHP arrays. To add a job, skill or certificate later,
 * copy one line inside the right array and change the text. The HTML is built from them.
 */
$page_title = 'About | Chiagoziem Blaise Charles';
$page_desc  = 'Chiagoziem Blaise Charles: web developer and aspiring machine learning engineer in Abuja, Nigeria, with a BSc in Human Anatomy, a Public Health PGD in progress and a Data Science and AI internship behind him.';
$current    = 'about';

$extra_head    = '<link rel="stylesheet" href="assets/css/about.css">';
$extra_scripts = '<script src="assets/js/tilt.js" defer></script>';

/* Skills: [group title, short note, [skills...]] */
$skills = [
    ['Web development', 'What I build complete websites with.',
        ['HTML5', 'CSS3', 'JavaScript', 'PHP', 'MySQL', 'Responsive design']],
    ['Data science and machine learning', 'Foundations from my training and internship.',
        ['Python', 'Pandas', 'NumPy', 'Scikit-learn', 'Data cleaning', 'Exploratory data analysis']],
    ['NLP and AI', 'From my medical chatbot work.',
        ['Retrieval-augmented generation (RAG)', 'Text preprocessing', 'Model evaluation']],
    ['Tools and deployment', 'How I work and ship.',
        ['Git', 'GitHub', 'Jupyter Notebook', 'Google Colab', 'Streamlit', 'Gradio', 'Hugging Face']],
    ['Health and laboratory', 'Where I started.',
        ['Histopathology', 'Tissue analysis', 'Microscopy', 'Specimen handling', 'Public health principles']],
];

/* Experience, newest first: [role, organisation, dates, is it current?, [points...]] */
$experience = [
    ['Web Development Intern', 'Arrowstack', 'Current', true, [
        'Building a responsive developer portfolio with 3D elements, and a weather and forecast app that uses a live API.',
        'Working in a GitHub workflow with a README, regular commits and a work log for each project.',
    ]],
    ['Data Science and AI Intern', 'OMEIFE Technologies, a UNICCON Group subsidiary (remote)', 'April 2025 to July 2025', false, [
        'Built a retrieval-augmented generation (RAG) pipeline for a medical chatbot prototype that supports preliminary pediatric pulmonary symptom checks.',
        'Collected, cleaned and preprocessed medical datasets for natural language processing using Python.',
        'Applied and evaluated machine learning models with Scikit-learn, working in an agile remote team under senior data scientists.',
        'Received a formal Letter of Recommendation for technical contributions.',
    ]],
    ['NYSC service year', 'National Hospital', 'November 2023 to October 2024', false, [
        'Assisted in autopsies and histopathological examinations under the supervision of pathologists.',
        'Carried out microscopic tissue analysis to support diagnosis and research.',
        'Kept accurate records of specimen handling and mortuary operations, following laboratory safety and quality control protocols.',
    ]],
    ['Industrial training', 'National Hospital', 'April 2021 to October 2021', false, [
        'Gained hands-on experience in embalming, tissue preservation and autopsy procedures.',
        'Assisted with specimen sectioning, staining and embedding for histological study, and supported laboratory operations.',
    ]],
];

/* Education: [title, institution, when, in progress?] */
$education = [
    ['Postgraduate Diploma, Public Health', 'University of Abuja', 'In progress', true],
    ['BSc Human Anatomy', 'Bingham University', '2019 to 2023', false],
];

/* Certificates: [title, issuer, year] */
$certificates = [
    ['Web Design and Development (HTML5, CSS3, PHP, MySQL)', 'Estatecafe School of Technology', '2026'],
    ['Data Science Level 5 Certificate (NSQ)', 'National Skills Qualification, via UNICCON Group and Intelbox Solutions', '2025'],
    ['Diploma in Machine Learning with Python', 'Alison', '2025'],
    ['Machine Learning for Absolute Beginners: Python and Pandas', 'Alison', '2025'],
];

require __DIR__ . '/includes/header.php';
?>

<!-- INTRO ------------------------------------------------------------------ -->
<section class="about-hero">
  <div class="container about-hero-grid">

    <div class="about-copy">
      <p class="eyebrow" data-reveal>About me</p>
      <h1 data-reveal data-reveal-delay="80">From anatomy to algorithms.</h1>

      <p class="about-lead" data-reveal data-reveal-delay="160">
        I'm Chiagoziem Blaise Charles, a web developer and aspiring machine learning engineer
        based in Abuja, Nigeria.
      </p>
      <p data-reveal data-reveal-delay="220">
        I studied Human Anatomy at Bingham University and spent a service year in histopathology
        at a national hospital before moving into technology. In 2025 I joined OMEIFE Technologies
        as a Data Science and AI intern, where I built a retrieval-augmented chatbot prototype for
        pediatric pulmonary symptoms. Since then I've trained in full-stack web development and built
        a hotel booking platform and an e-learning platform with PHP and MySQL.
      </p>
      <p data-reveal data-reveal-delay="280">
        I'm now completing a postgraduate diploma in Public Health and a web development internship
        at Arrowstack. I'm looking to build in health-tech, where my medical background and my code
        can work on the same problem.
      </p>

      <dl class="glance" data-reveal data-reveal-delay="340">
        <div><dt>Based in</dt><dd>Abuja, Nigeria</dd></div>
        <div><dt>Focus</dt><dd>Web development and machine learning</dd></div>
        <div><dt>Studying</dt><dd>PGD Public Health (in progress)</dd></div>
        <div><dt>Currently</dt><dd>Web development intern at Arrowstack</dd></div>
      </dl>

      <div class="about-actions" data-reveal data-reveal-delay="400">
        <a class="btn btn-primary" href="contact.php">Contact me</a>
        <a class="btn btn-ghost" href="<?= e($site['linkedin']) ?>" target="_blank" rel="noopener noreferrer">
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
          LinkedIn<span class="sr-only"> (opens in a new tab)</span>
        </a>
      </div>
    </div>

    <div class="portrait-wrap" data-reveal data-reveal-delay="200">
      <figure class="portrait" data-tilt="4">
        <img src="assets/img/about/blaise.webp" width="640" height="960"
             alt="Portrait of Chiagoziem Blaise Charles" fetchpriority="high" decoding="async">
        <figcaption>Abuja, Nigeria</figcaption>
      </figure>
    </div>

  </div>
</section>


<!-- SKILLS (light band) ------------------------------------------------------- -->
<section class="section section-light">
  <div class="container">
    <div class="section-head" data-reveal>
      <div>
        <h2>Tools I work with</h2>
        <p class="prose">From the browser to the notebook.</p>
      </div>
    </div>

    <div class="skills-grid">
      <?php foreach ($skills as $i => [$title, $note, $items]): ?>
        <article class="skill-group" data-reveal data-reveal-delay="<?= $i * 80 ?>">
          <h3><?= e($title) ?></h3>
          <p><?= e($note) ?></p>
          <ul class="tags">
            <?php foreach ($items as $item): ?><li><?= e($item) ?></li><?php endforeach; ?>
          </ul>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>


<!-- EXPERIENCE ------------------------------------------------------------------ -->
<section class="section">
  <div class="container split">

    <div class="split-intro" data-reveal>
      <h2>Where I've worked</h2>
      <p class="prose">Hospital laboratories, a remote AI team and now a web development internship.</p>
    </div>

    <ol class="timeline">
      <?php foreach ($experience as $i => [$role, $org, $dates, $isCurrent, $points]): ?>
        <li class="timeline-item<?= $isCurrent ? ' is-current' : '' ?>" data-reveal data-reveal-delay="<?= $i * 60 ?>">
          <div class="timeline-head">
            <h3><?= e($role) ?></h3>
            <span class="timeline-date"><?= e($dates) ?></span>
          </div>
          <p class="timeline-org"><?= e($org) ?></p>
          <ul class="timeline-points">
            <?php foreach ($points as $point): ?><li><?= e($point) ?></li><?php endforeach; ?>
          </ul>
        </li>
      <?php endforeach; ?>
    </ol>

  </div>
</section>


<!-- EDUCATION AND CERTIFICATES (light band) ---------------------------------------- -->
<section class="section section-light">
  <div class="container">
    <div class="section-head" data-reveal>
      <div>
        <h2>Education and certificates</h2>
        <p class="prose">Formal study and the training behind my skills.</p>
      </div>
    </div>

    <div class="credentials-grid">

      <div data-reveal>
        <h3 class="list-title">Education</h3>
        <ul class="credential-list">
          <?php foreach ($education as [$title, $school, $when, $inProgress]): ?>
            <li>
              <div>
                <h4><?= e($title) ?></h4>
                <p><?= e($school) ?></p>
              </div>
              <?php if ($inProgress): ?>
                <span class="status"><?= e($when) ?></span>
              <?php else: ?>
                <span class="credential-when"><?= e($when) ?></span>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
          <li>
            <div>
              <h4>Undergraduate research</h4>
              <p>Histological changes in the testes of adult Wistar rats administered with <i>Lawsonia inermis</i> extract. Department of Anatomy.</p>
            </div>
            <span class="credential-when">2023</span>
          </li>
        </ul>
      </div>

      <div data-reveal data-reveal-delay="100">
        <h3 class="list-title">Certificates</h3>
        <ul class="credential-list">
          <?php foreach ($certificates as [$title, $issuer, $year]): ?>
            <li>
              <div>
                <h4><?= e($title) ?></h4>
                <p><?= e($issuer) ?></p>
              </div>
              <span class="credential-when"><?= e($year) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

    </div>
  </div>
</section>


<!-- CONTACT CALL-TO-ACTION (shared: includes/cta.php) -->
<?php require __DIR__ . '/includes/cta.php'; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
