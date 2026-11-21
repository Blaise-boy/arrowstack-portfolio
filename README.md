# Developer Portfolio: Chiagoziem Blaise Charles

A responsive, semi-3D portfolio website built for **Task 1 of the Arrowstack web development internship**.

**Live site:** _add the subdomain link here after deployment_

## What it is

A four-page website that introduces me, shows my work and lets visitors contact me.

| Page | What it shows |
|---|---|
| Home | Hero with a small 3D robot that follows the mouse or finger, a short about section, selected work |
| About | My story, skills, experience timeline, education and certificates |
| Projects | Stacked 3D cards for my two live projects: Velora Suites and El Gato's Academy |
| Contact | A message form saved to MySQL, plus email, LinkedIn and GitHub links |

## Technologies

HTML, CSS, JavaScript, PHP and MySQL, developed on XAMPP.

Third-party pieces (everything else is written by me):
- [Three.js](https://threejs.org) r0.160.0 (MIT licence), loaded from the jsDelivr CDN for the 3D robot
- Google Fonts: Syne and Manrope

## Features

- **Semi-3D design:** a Three.js robot companion, 3D tilt on hover, and project cards that stack and tilt as you scroll.
- **Works on weak devices:** a small script rates the device as high, mid or low. Low-end phones and old laptops get lighter effects. The 3D robot only loads on capable devices, and it switches itself off if the frame rate drops below 25 fps. Visitors who prefer reduced motion get no animation.
- **Responsive:** tested from phone width to wide desktop.
- **Contact form:** checked in the browser first (JavaScript), checked again on the server (PHP), saved with prepared statements, a hidden spam trap, a security token and a 30-second limit between messages.
- **Accessible basics:** skip link, labelled form fields, error messages tied to their fields, visible focus, keyboard-friendly menu.
- **Shared page parts:** header, footer and the call-to-action band live in `includes/`, so a change is made once.

## Project structure

```
index.php  about.php  projects.php  contact.php
includes/   config.php (name, links, menu)  header.php  footer.php  cta.php
            projects.php (project data)  db.php (database connection)
assets/css/ style.css (shared) + one file per page
assets/js/  main.js  tilt.js  companion.js  home.js  projects.js  contact.js
assets/img/ portrait, project screenshots, hero image
README.md   this file (work log, validation and limitations are below)
```

To add a project, add one entry to `includes/projects.php`. The Home page, Projects page and footer pick it up.

## Run it locally (XAMPP)

1. Copy the folder into `htdocs` (for example `htdocs/portfolio`).
2. Start Apache and MySQL in XAMPP.
3. In phpMyAdmin, create a database named `portfolio`, then run the SQL under **Database table** below in its SQL tab.
4. Open `http://localhost/portfolio/`.

The default connection (`localhost`, `root`, empty password, `portfolio`) is in `includes/db.php`.

## Database table

The Contact form saves to one table:

```sql
id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL,
  subject VARCHAR(200) NULL,
  message TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Deploy

1. Upload all files to the subdomain's folder.
2. Create a MySQL database in the hosting panel and run the SQL under **Database table** below in its phpMyAdmin.
3. Put the host, user, password and database name from the panel into `includes/db.php` on the server.
   **Do not commit real passwords to GitHub.**
4. Read messages in the hosting phpMyAdmin: database > `contact_messages` > Browse.

## Testing and validation

How the portfolio was checked. Sections 1 to 3 were run and passed. Section 4 lists checks I have **not run yet** because of my study schedule. I will record the results here when I do.

### 1. Code checks (done)

| Check | How | Result |
|---|---|---|
| PHP has no syntax errors | `php -l` on every PHP file | Pass |
| JavaScript has no syntax errors | `node --check` on the JS files | Pass |

### 2. Contact form (done)

Run in a browser with Playwright against the PHP server, and by hand on XAMPP.

| Test | Expected | Result |
|---|---|---|
| Send the empty form | Errors under name, email and message; focus moves to the first bad field | Pass |
| Type "bad" as the email | "That email address doesn't look right." | Pass |
| JavaScript turned off, submit bad data | Server shows the same errors | Pass |
| Hidden spam field filled in | Nothing saved | Pass |
| Wrong security token | "Your session expired" message | Pass |
| Database unavailable | Friendly message with my email, typed text kept | Pass |
| Valid message on XAMPP | "Message sent." and a new row in `contact_messages` | Pass (row id 1 saved, see phpMyAdmin screenshot) |
| Phone width | No sideways scrolling | Pass |

### 3. Layout and performance modes (done)

| Test | Result |
|---|---|
| Pages checked at 375 px (phone) and 1280 px (desktop) in Chromium | Pass |
| `?perf=low` forces the light mode: no stacking cards, no 3D robot | Pass |

### 4. Not yet run

| Check | How | Result |
|---|---|---|
| HTML validity | Paste each page's source into https://validator.w3.org/nu/ | Not yet run |
| Lighthouse (Performance, Accessibility, Best Practices, SEO) | Chrome DevTools > Lighthouse, mobile, on the live site | Not yet run |
| Real phone | Open the live site on my phone: menu, robot swipe, contact form | Not yet run |
| Old laptop frame rate | Watch the robot on my old laptop. Does it stay smooth? | Not yet run |
| Reduced motion | Turn on "reduce motion" in my device settings and reload: no animations | Not yet run |
| Another browser | Open in Firefox or Edge | Not yet run |
| Live contact form | Send a message on the live site and find it in the hosting phpMyAdmin | Not yet run |
| Links | Click every link: nav, footer, project "Visit live site", GitHub, LinkedIn | Not yet run |

Screenshots to attach: Lighthouse scores, the phpMyAdmin row, the site on a phone.

## Work log

One entry per working step. Dates before 15 Nov are approximate: planning began on 7 Nov, and I worked on the build in between my postgraduate studies.


| Step | Date | What I did | Result |
|---|---|---|---|
| Planning | 2026-11-07 | Chose the stack (HTML, CSS, JavaScript, PHP, MySQL). Decided on a multi-page hybrid design: bold type, a hero that reacts to the cursor, stacked 3D project cards, dark and light bands. Palette: Indigo Black #070C29, Bone #EFE7DA, Amber #FF9E0D. | Design agreed |
| 1. Foundation | 7–10 Nov (approx.) | Built the shared parts: `config.php`, header, footer, base CSS with design tokens, buttons, glass panels, scroll reveal. Added the device performance check (high / mid / low). | Shared layout working |
| 2. Home page | 7–10 Nov (approx.) | Hero, about preview and selected work. First version swapped avatar images with the cursor, but the transition was not smooth. | Replaced in step 3 |
| 3. Robot companion | 10–12 Nov (approx.) | Replaced the avatar images with a small Three.js robot that follows the mouse or a finger swipe, with spring motion, a trail, and a frame-rate governor that switches it off on slow devices. | Smooth in testing; kept |
| 4. Home fixes | 10–12 Nov (approx.) | Removed the grid-line background, changed the footer to four columns, applied the new palette, removed the logo mark, corrected the Public Health PGD wording to "in progress", removed a data analytics claim I cannot back up. | Fixed |
| 5. About page | 12–14 Nov (approx.) | Wrote the story, skills, experience timeline and education from my CVs. Fixed spacing between the buttons and the light band after reviewing screenshots. | Done |
| 6. Projects page | 14–15 Nov (approx.) | Built the sticky stacked cards with browser-frame screenshots, plus the "How I build" section. Fixed the last card not staying pinned (added a spacer) and cards being too tall on 720px screens. | Done |
| Footer wording | 2026-11-15 | Changed the credit line to "Built with HTML, CSS, JavaScript and PHP." | Done |
| 7. Contact page | 2026-11-15 | Created the `contact_messages` table in phpMyAdmin. Built the form with browser checks, server checks, prepared-statement save, spam trap, security token and a send limit. Decided against `mail()` because the subdomain cannot send email. | Tested on XAMPP: message saved and visible in phpMyAdmin |
| Docs | 2026-11-15 | Wrote the README, this log, the validation document and the limitations section. | Done |

### Still to do

- Run the checks in section 4 of Testing and validation and record the results
- Deploy to the subdomain and create the live database table
- Put the live link in the README
- Push to GitHub with regular commits

## Limitations

Being honest about what this project does not do:

- **No email notification.** The free subdomain cannot send email, so messages are only stored in the database. I have to check the table myself. There is no admin page.
- **Spam protection is basic.** A hidden field, a security token and a 30-second limit stop simple bots. A determined bot or a very large volume of spam would need more (for example CAPTCHA or limits per IP address).
- **The 3D robot needs internet and a capable device.** Three.js loads from a CDN. Offline, or on a low-end device, visitors see the site without the robot. Nothing else depends on it.
- **Some checks not yet run.** The W3C validator, Lighthouse, real-phone and other-browser checks (section 4 of Testing and validation) have not been done yet.
- **Limited device testing.** Layouts were checked in Chromium at phone, tablet and desktop widths, and on my own laptop in Chrome. I have not tested on a range of real phones, Safari or Firefox.
- **Frame rate on old hardware is not guaranteed.** The speed governor reduces the risk, but the smoothness of the effects on very old devices was only checked on one laptop.
- **No automated test suite.** Checks were manual and scripted browser checks, listed in the validation document.
- **Content is mine and static.** Page text and project details live in the PHP files and are edited by hand. There is no content management.
- **Only two featured projects.** Two further projects are not ready to show yet.

## Author

Chiagoziem Blaise Charles, Abuja, Nigeria
[GitHub](https://github.com/Blaise-boy) · [LinkedIn](https://www.linkedin.com/in/chiagoziem-charles-0ba693326) · chiagoziemblaise@gmail.com
