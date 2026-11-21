<?php
/**
 * projects.php  -  the list of projects, in one place.
 * The Home page, the Projects page and the footer all read this list.
 * To add a project later (for example Atlas Construction), copy one block,
 * add its screenshot to assets/img/projects/, and fill in the details.
 *
 * Keys:
 *   name, summary, image, alt, url, tags   used on the Home page and in the footer
 *   type, features                         used on the Projects page
 */
return [
    [
        'name'    => 'Velora Suites',
        'type'    => 'Hotel booking platform',
        'summary' => 'A hotel booking system where guests check room availability and reserve a stay, with a backend that manages reservations, rooms and users.',
        'features' => [
            'Guests check which rooms are available and reserve one for the length of stay they choose.',
            'A backend that manages reservations, room inventory and user data.',
            'An admin side, kept separate from the public site, for running rooms and bookings.',
        ],
        'image'   => 'assets/img/projects/velora-suites.webp',
        'alt'     => 'Velora Suites home page showing a luxury hotel lobby and a booking bar',
        'url'     => 'https://velorasuites.kesug.com',
        'tags'    => ['HTML', 'CSS', 'JavaScript', 'PHP', 'MySQL'],
    ],
    [
        'name'    => "El Gato's Academy",
        'type'    => 'E-learning platform',
        'summary' => 'An e-learning platform where students register and enroll in courses, with role-based access that assigns instructors and tracks progress.',
        'features' => [
            'A public site with courses, instructors, blog, admissions and contact pages, all driven by live MySQL data.',
            'A student portal with secure login (hashed passwords), a personal dashboard and a My Courses page.',
            'An admin dashboard with live stats and charts, where applications are approved (which creates the student account) and courses, instructors, enrollments and blog posts are managed.',
        ],
        'image'   => 'assets/img/projects/el-gatos-academy.webp',
        'alt'     => "El Gato's Academy home page with the headline Learn Today. Build Tomorrow.",
        'url'     => 'https://elgatosacademy.kesug.com',
        'tags'    => ['HTML', 'CSS', 'JavaScript', 'PHP', 'MySQL'],
    ],
];
