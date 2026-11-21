<?php
/**
 * contact.php  -  Contact page
 * Sections: page head, message form (saved to MySQL), contact details.
 *
 * How sending works:
 * 1. The browser checks the form first (assets/js/contact.js) for quick feedback.
 * 2. PHP checks it again below, because browser checks can be skipped by bots.
 * 3. A valid message is saved to the contact_messages table with a prepared statement.
 * 4. The page then reloads (Post/Redirect/Get), so refreshing never sends it twice.
 * No email is sent: read your messages in phpMyAdmin (portfolio > contact_messages).
 */
session_start();
require_once __DIR__ . '/includes/config.php';

$page_title = 'Contact | Chiagoziem Blaise Charles';
$page_desc  = 'Get in touch with Chiagoziem Blaise Charles about web development work, collaborations or opportunities.';
$current    = 'contact';

$extra_head    = '<link rel="stylesheet" href="assets/css/contact.css">';
$extra_scripts = '<script src="assets/js/contact.js" defer></script>';

/* Limits: these match the contact_messages table, and contact.js uses the same numbers */
const LIMITS = ['name' => 150, 'email' => 150, 'subject' => 200, 'message' => 3000];

/* Security token: proves the form came from this site */
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$values = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];
$errors = [];
$failed = false;                                   // true when saving fails for a non-visitor reason

/* A message was just sent (set below, shown once) */
$sent = !empty($_SESSION['sent']);
unset($_SESSION['sent']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /* Read and tidy every field */
    foreach ($values as $key => $_) {
        $values[$key] = trim((string) ($_POST[$key] ?? ''));
    }
    $honeypot = trim((string) ($_POST['website'] ?? ''));   // real visitors never see this field, bots fill it in
    $token    = (string) ($_POST['csrf'] ?? '');

    if (!hash_equals($_SESSION['csrf'], $token)) {
        $errors['form'] = 'Your session expired. Please try sending again.';
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    } elseif ($honeypot !== '') {
        /* A bot. Pretend it worked, save nothing. */
        $_SESSION['sent'] = true;
        header('Location: contact.php#form-area');
        exit;
    } else {
        /* Field checks */
        $len = fn($s) => mb_strlen($s, 'UTF-8');

        if ($len($values['name']) < 2)                      $errors['name'] = 'Please enter your name.';
        elseif ($len($values['name']) > LIMITS['name'])     $errors['name'] = 'Your name is too long (150 characters at most).';

        if ($values['email'] === '')                                          $errors['email'] = 'Please enter your email address.';
        elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL))         $errors['email'] = 'That email address doesn\'t look right.';
        elseif ($len($values['email']) > LIMITS['email'])                     $errors['email'] = 'That email address is too long.';

        if ($len($values['subject']) > LIMITS['subject'])   $errors['subject'] = 'The subject is too long (200 characters at most).';

        if ($len($values['message']) < 10)                  $errors['message'] = 'Please write a message of at least 10 characters.';
        elseif ($len($values['message']) > LIMITS['message']) $errors['message'] = 'Your message is too long (3000 characters at most).';

        /* Slow down repeat sends: one message every 30 seconds per visitor */
        if (!$errors && isset($_SESSION['last_sent']) && time() - $_SESSION['last_sent'] < 30) {
            $errors['form'] = 'Please wait a few seconds before sending another message.';
        }

        /* All good: save it */
        if (!$errors) {
            require_once __DIR__ . '/includes/db.php';
            $db = db_connect();
            try {
                if ($db === null) throw new RuntimeException('No database connection');
                $stmt = $db->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)');
                $subject = $values['subject'] === '' ? null : $values['subject'];   // empty subject is stored as NULL
                $stmt->bind_param('ssss', $values['name'], $values['email'], $subject, $values['message']);
                $stmt->execute();
                $stmt->close();
                $db->close();

                $_SESSION['sent'] = true;
                $_SESSION['last_sent'] = time();
                $_SESSION['csrf'] = bin2hex(random_bytes(32));
                header('Location: contact.php#form-area');
                exit;
            } catch (Throwable $ex) {
                error_log('Portfolio contact save failed: ' . $ex->getMessage());
                $failed = true;
                $errors['form'] = 'Sorry, your message could not be sent just now. Please email me directly instead.';
            }
        }
    }
}

$has = fn($f) => isset($errors[$f]);
require __DIR__ . '/includes/header.php';
?>

<!-- PAGE HEAD ------------------------------------------------------------------ -->
<section class="page-head">
  <div class="container">
    <p class="eyebrow" data-reveal>Contact</p>
    <h1 data-reveal data-reveal-delay="80">Let's talk.</h1>
    <p class="page-lead" data-reveal data-reveal-delay="160">
      Have a project in mind, a role to discuss or a question? Send me a message and I'll get back to you.
    </p>
  </div>
</section>


<!-- FORM + DETAILS -------------------------------------------------------------- -->
<section class="contact-section">
  <div class="container contact-grid">

    <div class="contact-card glass" id="form-area" data-reveal>
      <?php if ($sent): ?>
        <div class="form-success" role="status" tabindex="-1" id="sentNote">
          <span class="success-mark" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>
          </span>
          <h2>Message sent.</h2>
          <p>Thank you for reaching out. I'll reply to the email address you gave me.</p>
          <a class="btn btn-ghost" href="contact.php">Send another message</a>
        </div>
      <?php else: ?>
        <h2 class="form-title">Send a message</h2>

        <form id="contactForm" method="post" action="contact.php#form-area" novalidate>
          <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">

          <?php if ($has('form')): ?>
            <p class="form-alert" role="alert" id="formAlert">
              <?= e($errors['form']) ?>
              <?php if ($failed): ?><a href="mailto:<?= e($site['email']) ?>"><?= e($site['email']) ?></a><?php endif; ?>
            </p>
          <?php endif; ?>

          <div class="field <?= $has('name') ? 'has-error' : '' ?>">
            <label for="name">Your name</label>
            <input type="text" id="name" name="name" autocomplete="name" maxlength="<?= LIMITS['name'] ?>" required
                   value="<?= e($values['name']) ?>" <?= $has('name') ? 'aria-invalid="true" aria-describedby="name-error"' : '' ?>>
            <p class="field-error" id="name-error"><?= $has('name') ? e($errors['name']) : '' ?></p>
          </div>

          <div class="field <?= $has('email') ? 'has-error' : '' ?>">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" autocomplete="email" maxlength="<?= LIMITS['email'] ?>" required
                   value="<?= e($values['email']) ?>" <?= $has('email') ? 'aria-invalid="true" aria-describedby="email-error"' : '' ?>>
            <p class="field-error" id="email-error"><?= $has('email') ? e($errors['email']) : '' ?></p>
          </div>

          <div class="field <?= $has('subject') ? 'has-error' : '' ?>">
            <label for="subject">Subject <span class="optional">(optional)</span></label>
            <input type="text" id="subject" name="subject" maxlength="<?= LIMITS['subject'] ?>"
                   value="<?= e($values['subject']) ?>" <?= $has('subject') ? 'aria-invalid="true" aria-describedby="subject-error"' : '' ?>>
            <p class="field-error" id="subject-error"><?= $has('subject') ? e($errors['subject']) : '' ?></p>
          </div>

          <div class="field <?= $has('message') ? 'has-error' : '' ?>">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="6" maxlength="<?= LIMITS['message'] ?>" required
                      <?= $has('message') ? 'aria-invalid="true" aria-describedby="message-error"' : '' ?>><?= e($values['message']) ?></textarea>
            <p class="field-error" id="message-error"><?= $has('message') ? e($errors['message']) : '' ?></p>
            <p class="counter" id="messageCount" aria-hidden="true"><span>0</span> / <?= LIMITS['message'] ?></p>
          </div>

          <!-- Spam trap: hidden from people, bots fill it in -->
          <div class="trap" aria-hidden="true">
            <label for="website">Leave this empty</label>
            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
          </div>

          <button class="btn btn-primary btn-with-icon" type="submit" id="sendBtn">
            <span class="btn-label">Send message</span>
            <span class="btn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7M8 7h9v9"/></svg></span>
          </button>
        </form>
      <?php endif; ?>
    </div>

    <aside class="contact-side" aria-label="Other ways to reach me">
      <h2 class="side-title" data-reveal>Prefer another way?</h2>
      <ul class="side-list">
        <li data-reveal data-reveal-delay="80">
          <a class="side-link glass glass-hover" href="mailto:<?= e($site['email']) ?>">
            <span class="side-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="m3 7 9 6.5L21 7"/></svg></span>
            <span><strong>Email</strong><small><?= e($site['email']) ?></small></span>
          </a>
        </li>
        <li data-reveal data-reveal-delay="160">
          <a class="side-link glass glass-hover" href="<?= e($site['linkedin']) ?>" target="_blank" rel="noopener noreferrer">
            <span class="side-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></span>
            <span><strong>LinkedIn</strong><small>Chiagoziem Charles<span class="sr-only"> (opens in a new tab)</span></small></span>
          </a>
        </li>
        <li data-reveal data-reveal-delay="240">
          <a class="side-link glass glass-hover" href="<?= e($site['github']) ?>" target="_blank" rel="noopener noreferrer">
            <span class="side-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg></span>
            <span><strong>GitHub</strong><small>Blaise-boy<span class="sr-only"> (opens in a new tab)</span></small></span>
          </a>
        </li>
      </ul>
      <p class="side-note" data-reveal data-reveal-delay="320">Based in Abuja, Nigeria. Open to internships, junior roles and collaborations.</p>
    </aside>

  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
