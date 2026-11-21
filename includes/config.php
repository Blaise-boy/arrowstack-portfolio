<?php
/**
 * config.php
 * One place for the details that appear on every page (name, links, menu).
 * To change your email, LinkedIn or menu items later, edit them here only.
 */

$site = [
    'name'        => 'Chiagoziem Blaise Charles',
    'initials'    => 'CBC',
    'role'        => 'Aspiring Machine Learning Engineer',
    'description' => 'Portfolio of Chiagoziem Blaise Charles: web developer and data enthusiast building full-stack projects with PHP, MySQL, JavaScript and Python.',
    'email'       => 'chiagoziemblaise@gmail.com',
    'github'      => 'https://github.com/Blaise-boy',
    'linkedin'    => 'https://www.linkedin.com/in/chiagoziem-charles-0ba693326',
];

/**
 * Main menu. Each item: [label, file, key].
 * The key is matched against $current (set at the top of every page)
 * so the right link gets highlighted.
 */
$nav = [
    ['Home',     'index.php',    'home'],
    ['About',    'about.php',    'about'],
    ['Projects', 'projects.php', 'projects'],
    ['Contact',  'contact.php',  'contact'],
];

/** Short helper: safely print text inside HTML (stops broken or unsafe output). */
function e($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}
