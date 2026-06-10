<?php
require_once __DIR__ . '/config.php';

// ── SEO helpers ─────────────────────────────────────────────
$_seo_title = isset($page_title)
    ? htmlspecialchars($page_title) . ' | Talent Pool Academy'
    : 'Talent Pool Academy | 11 Plus, SATs & Expert Tuition in Chadwell Heath & Chelmsford';

$_seo_desc = isset($meta_description)
    ? htmlspecialchars($meta_description)
    : 'Expert tuition for 11 Plus, SATs, KS1–KS3, GCSE & A-Level at Talent Pool Academy. Small classes, proven results. Chadwell Heath & Chelmsford.';

// Canonical: strip /tpaAG prefix so both local and production resolve correctly
$_raw_path   = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$_clean_path = preg_replace('#^/tpaAG#', '', $_raw_path);
$_canonical  = rtrim(SITE_CANONICAL, '/') . ($_clean_path ?: '/');

$_og_image = isset($og_image)
    ? $og_image
    : SITE_CANONICAL . '/images/tpa-og-image.jpg';

// ── LocalBusiness / EducationalOrganization JSON-LD ─────────
$_ld_org = [
  '@context'     => 'https://schema.org',
  '@type'        => ['EducationalOrganization', 'LocalBusiness'],
  'name'         => 'Talent Pool Academy',
  'alternateName'=> 'TPA',
  'url'          => SITE_CANONICAL,
  'logo'         => SITE_CANONICAL . '/images/logo-blue.png',
  'image'        => SITE_CANONICAL . '/images/tpa-og-image.jpg',
  'description'  => 'Expert tuition for 11 Plus, SATs, KS1–KS3, GCSE & A-Level. Small classes, proven results. Chadwell Heath & Chelmsford.',
  'telephone'    => '+44' . ltrim(PHONE, '0'),
  'email'        => EMAIL,
  'foundingDate' => '2008',
  'priceRange'   => '££',
  'location'     => [
    ['@type' => 'Place',
     'name'  => 'Chadwell Heath Centre',
     'address' => ['@type'=>'PostalAddress','streetAddress'=>'60 High Road, Chadwell Heath','addressLocality'=>'Romford','postalCode'=>'RM6 6PP','addressCountry'=>'GB']],
    ['@type' => 'Place',
     'name'  => 'Chelmsford Centre',
     'address' => ['@type'=>'PostalAddress','streetAddress'=>'4B Corporation Road','addressLocality'=>'Chelmsford','postalCode'=>'CM1 2AR','addressCountry'=>'GB']],
  ],
  'openingHoursSpecification' => [
    ['@type'=>'OpeningHoursSpecification','dayOfWeek'=>['Monday','Tuesday','Wednesday','Thursday','Friday'],'opens'=>'16:00','closes'=>'19:00'],
    ['@type'=>'OpeningHoursSpecification','dayOfWeek'=>['Saturday','Sunday'],'opens'=>'09:00','closes'=>'17:00'],
  ],
  'sameAs' => [
    'https://www.facebook.com/talentpoolacademy',
    'https://www.instagram.com/talentpoolacademy',
    'https://www.youtube.com/talentpoolacademy',
  ],
];
?>
<!DOCTYPE html>
<html lang="en-GB">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
  <meta name="description" content="<?= $_seo_desc ?>">
  <link rel="canonical" href="<?= htmlspecialchars($_canonical) ?>">

  <!-- Open Graph -->
  <meta property="og:type"        content="<?= isset($og_type) ? $og_type : 'website' ?>">
  <meta property="og:site_name"   content="Talent Pool Academy">
  <meta property="og:locale"      content="en_GB">
  <meta property="og:title"       content="<?= $_seo_title ?>">
  <meta property="og:description" content="<?= $_seo_desc ?>">
  <meta property="og:url"         content="<?= htmlspecialchars($_canonical) ?>">
  <meta property="og:image"       content="<?= htmlspecialchars($_og_image) ?>">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height"content="630">
  <meta property="og:image:alt"   content="Talent Pool Academy — tuition centre">

  <!-- Twitter Card -->
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="<?= $_seo_title ?>">
  <meta name="twitter:description" content="<?= $_seo_desc ?>">
  <meta name="twitter:image"       content="<?= htmlspecialchars($_og_image) ?>">

  <title><?= $_seo_title ?></title>

  <!-- Structured Data: Organisation -->
  <script type="application/ld+json"><?= json_encode($_ld_org, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
  <?php if (isset($schema_extra)) echo $schema_extra; ?>

  <!-- Favicons -->
  <link rel="icon" type="image/x-icon" href="<?= SITE_URL ?>/images/favicon.ico">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_URL ?>/images/favicon-32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= SITE_URL ?>/images/favicon-16.png">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= SITE_URL ?>/images/apple-touch-icon.png">
  <link rel="manifest" href="<?= SITE_URL ?>/site.webmanifest">

  <!-- Fonts & Libraries -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
  <link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">
  <link rel="stylesheet" href="<?= SITE_URL ?>/css/star-carousel.css">
  <?= isset($extra_css) ? $extra_css : '' ?>
  <script>window.tpaApiUrl = '<?= SITE_URL ?>/api/contact-form.php';</script>
</head>

<body>

  <!-- SCROLL PROGRESS BAR -->
  <div id="scroll-progress"></div>

  <!-- ANNOUNCEMENT BAR — rotates every 5 s -->
  <div class="announcement-bar" id="announcementBar">
    <div class="container">
      <span class="ann-slide ann-slide-active">
        <i class="fas fa-sun me-1" aria-hidden="true"></i>
        <strong>Summer Holiday Camp 2026 — 27 Jul to 23 Aug!</strong> Ages 5–14 · Only £200 · Limited seats.
        <a href="<?= SITE_URL ?>/summer-camp.php" style="margin-left:.4rem;">Book Now →</a>
      </span>
      <span class="ann-slide" aria-hidden="true">
        <i class="fas fa-handshake me-1" aria-hidden="true"></i>
        <strong>TPA × Parkwood Academy Partnership</strong> — Free assessment for all Parkwood Academy families.
        <a href="<?= SITE_URL ?>/parkwood-academy.php" style="margin-left:.4rem;">Find Out More →</a>
      </span>
    </div>
    <button class="announcement-bar-close" id="announcementClose" aria-label="Dismiss announcement"><i class="fas fa-times" aria-hidden="true"></i></button>
  </div>
  <style>
    .ann-slide { display:none; }
    .ann-slide.ann-slide-active { display:inline; }
  </style>
  <script>
  (function(){
    var slides = document.querySelectorAll('.ann-slide'), i = 0;
    if(slides.length > 1) setInterval(function(){
      slides[i].classList.remove('ann-slide-active');
      slides[i].setAttribute('aria-hidden','true');
      i = (i + 1) % slides.length;
      slides[i].classList.add('ann-slide-active');
      slides[i].removeAttribute('aria-hidden');
    }, 5000);
  })();
  </script>

  <!-- TOP BAR -->
  <div class="tpa-topbar d-none d-md-block">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-7">
          <i class="fas fa-phone-alt me-2"></i><a href="tel:<?= PHONE ?>"> <?= PHONE ?></a>
          &nbsp;|&nbsp;
          <i class="fas fa-envelope me-2"></i><a href="mailto:<?= EMAIL ?>"><?= EMAIL ?></a>
          &nbsp;|&nbsp;
          <i class="fab fa-whatsapp me-1"></i><a href="https://wa.me/<?= WHATSAPP ?>">WhatsApp</a>
        </div>
        <div class="col-md-5 text-end d-flex align-items-center justify-content-end gap-3">
          <span style="color:rgba(255,255,255,0.55);"><i class="fas fa-map-marker-alt me-1" style="color:var(--gold);"></i>Chadwell Heath &amp; Chelmsford</span>
          <span class="topbar-login-sep">|</span>
          <div class="topbar-login-links">
            
            <a href="http://boxmusicacademy.com/" target="_blank" style="color:var(--gold);font-weight:700;" title="Box Music Academy"><i class="fas fa-music"></i> Box Music</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- NAVIGATION -->
  <nav class="navbar navbar-expand-lg tpa-navbar" id="mainNav">
    <div class="container">
      <a class="navbar-brand" href="<?= SITE_URL ?>/index.php">
        <img src="<?= SITE_URL ?>/images/logo-blue.png" alt="Talent Pool Academy" height="48" width="auto">
        <span>TALENT <em>POOL</em> ACADEMY</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item"><a class="nav-link <?= isActive('index.php') ?>" href="<?= SITE_URL ?>/index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link <?= isActive('about.php') ?>" href="<?= SITE_URL ?>/about.php">About Us</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?= (in_array(basename($_SERVER['PHP_SELF']), ['courses.php', 'course-11plus.php', 'course-sats.php', 'course-ks1.php', 'course-ks2.php', 'course-ks3.php', 'course-gcse.php', 'course-alevel.php', 'course-adult.php'])) ? 'active' : '' ?>" href="<?= SITE_URL ?>/courses.php" role="button" data-bs-toggle="dropdown">Courses <i class="fas fa-chevron-down"></i></a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="<?= SITE_URL ?>/course-11plus.php"><i class="fas fa-star"></i> 11 Plus Preparation</a></li>
              <li><a class="dropdown-item" href="<?= SITE_URL ?>/course-sats.php"><i class="fas fa-pencil-alt"></i> SATs (Year 2 &amp; Year 6)</a></li>
              <li><a class="dropdown-item" href="<?= SITE_URL ?>/course-ks1.php"><i class="fas fa-book"></i> Key Stage 1 (Year 1–2)</a></li>
              <li><a class="dropdown-item" href="<?= SITE_URL ?>/course-ks2.php"><i class="fas fa-graduation-cap"></i> Key Stage 2 (Year 3–6)</a></li>
              <li><a class="dropdown-item" href="<?= SITE_URL ?>/course-ks3.php"><i class="fas fa-flask"></i> Key Stage 3 (Year 7–9)</a></li>
              <li><a class="dropdown-item" href="<?= SITE_URL ?>/course-gcse.php"><i class="fas fa-book-open"></i> GCSE (Year 10–11)</a></li>
              <li><a class="dropdown-item" href="<?= SITE_URL ?>/course-alevel.php"><i class="fas fa-university"></i> A-Level (Year 12–13)</a></li>
              <li><a class="dropdown-item" href="<?= SITE_URL ?>/course-adult.php"><i class="fas fa-user-graduate"></i> Adult Learning</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="<?= SITE_URL ?>/courses.php"><i class="fas fa-th-list"></i> All Courses</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link <?= isActive('books.php') ?>" href="<?= SITE_URL ?>/books.php">Books</a></li>
          <li class="nav-item"><a class="nav-link <?= isActive('events.php') ?>" href="<?= SITE_URL ?>/events.php">Events</a></li>
          <li class="nav-item"><a class="nav-link <?= isActive('summer-camp.php') ?>" href="<?= SITE_URL ?>/summer-camp.php" style="color:#e65100;font-weight:700;"><i class="fas fa-sun me-1" aria-hidden="true"></i>Summer Camp</a></li>
          <li class="nav-item"><a class="nav-link <?= isActive('contact.php') ?>" href="<?= SITE_URL ?>/contact.php">Contact</a></li>
          <li class="nav-item"><a class="btn-nav-cta ms-2" href="<?= SITE_URL ?>/contact.php#assessment">✦ Free Assessment</a></li>
        </ul>
      </div>
    </div>
  </nav>