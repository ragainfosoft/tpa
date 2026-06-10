<?php
$page_title = '11 Plus, SATs & KS1–KS3 Tutoring in Chadwell Heath & Chelmsford';
$meta_description = 'Talent Pool Academy — Expert tuition for 11 Plus, SATs, KS1, KS2, KS3, GCSE & A-level in Chadwell Heath and Chelmsford. 16+ years of proven results and grammar school placements.';
require_once 'includes/config.php';
$schema_extra = '<script type="application/ld+json">' . json_encode([
  '@context'=>'https://schema.org','@type'=>'WebSite',
  'name'=>'Talent Pool Academy',
  'url'=>'https://www.talentpoolacademy.com',
  'description'=>'Expert tuition centre for 11 Plus, SATs, KS1–KS3, GCSE and A-Level. Two centres in Chadwell Heath and Chelmsford. Small classes, proven results since 2008.',
  'potentialAction'=>['@type'=>'SearchAction','target'=>['@type'=>'EntryPoint','urlTemplate'=>'https://www.talentpoolacademy.com/courses.php?q={search_term_string}'],'query-input'=>'required name=search_term_string'],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . '</script>';
require_once 'includes/announcements-data.php';
require_once 'includes/star-students-data.php';
require_once 'includes/feedback-data.php';
require_once 'includes/header.php';
$star_students = get_star_students(); // all students for homepage carousel
?>

<!-- ================================================
       HERO
       ================================================ -->
<section class="hero-section" id="hero">
  <div class="hero-bg-image"></div>
  <div class="hero-overlay"></div>
  <div class="container position-relative py-5">
    <div class="row align-items-center g-5">
      <div class="col-lg-6 hero-content">
        <div class="hero-badge"><i class="fas fa-award"></i> 16+ Years of Excellence in UK Education</div>
        <h1 class="hero-title">Building <span>Brighter Futures</span> for Every Child</h1>
        <p class="hero-subtitle">Expert tuition for <strong style="color:var(--gold);">11 Plus, SATs, KS1, KS2, KS3, GCSE &amp; A-level</strong>. Small classes, experienced teachers, and proven results — giving your child the confidence to succeed.</p>
        <div class="hero-ctas">
          <a href="<?= SITE_URL ?>/contact.php#assessment" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Assessment</a>
          <a href="<?= SITE_URL ?>/courses.php" class="btn-secondary-tpa"><i class="fas fa-book-open"></i> Explore Courses</a>
        </div>
        <div class="hero-trust">
          <div class="hero-trust-item"><i class="fas fa-check-circle"></i> No-obligation assessment</div>
          <div class="hero-trust-item"><i class="fas fa-check-circle"></i> Small group classes</div>
          <div class="hero-trust-item"><i class="fas fa-check-circle"></i> Online &amp; in-centre</div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="hero-graphic">
          <div class="hero-img-wrap">
            <img src="<?= SITE_URL ?>/images/classroom.webp" alt="Talent Pool Academy tutoring session" loading="eager">
          </div>
          <div class="hero-float-card hero-float-card-1">
            <i class="fas fa-users"></i>
            <div><span class="val">5,000+</span><span class="lbl">Students Taught</span></div>
          </div>
          <div class="hero-float-card hero-float-card-2">
            <i class="fas fa-trophy"></i>
            <div><span class="val">98%</span><span class="lbl">Parent Satisfaction</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ================================================
       STATS BAR
       ================================================ -->
<section class="stats-bar">
  <div class="container">
    <div class="row g-4 text-center gsap-stagger">
      <div class="col-6 col-md-3 stat-item">
        <div class="stat-value"><span class="stat-number counter" data-target="16">0</span><span class="stat-suffix">+</span></div>
        <div class="stat-label">Years of Experience</div>
      </div>
      <div class="col-6 col-md-3 stat-item">
        <div class="stat-value"><span class="stat-number counter" data-target="5000">0</span><span class="stat-suffix">+</span></div>
        <div class="stat-label">Students Taught</div>
      </div>
      <div class="col-6 col-md-3 stat-item">
        <div class="stat-value"><span class="stat-number counter" data-target="90">0</span><span class="stat-suffix">%</span></div>
        <div class="stat-label">11+ Pass Rate 2024</div>
      </div>
      <div class="col-6 col-md-3 stat-item">
        <div class="stat-value"><span class="stat-number counter" data-target="2">0</span></div>
        <div class="stat-label">Learning Centres</div>
      </div>
    </div>
  </div>
</section>

<!-- ================================================
       SCHOOL LOGO SCROLL
       ================================================ -->
<section class="logo-scroll-section">
  <div class="container-fluid px-4">
    <div class="logo-scroll-label"><i class="fas fa-graduation-cap me-2" aria-hidden="true"></i>Our students have secured places at these prestigious schools</div>
    <div class="logo-track-wrap">
      <div class="logo-track" id="logoTrack">
        <!-- London Barnet -->
        <a class="logo-item" href="https://www.hbschool.org.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#8B0000;"><i class="fas fa-shield-alt" style="color:var(--gold);"></i></div>
          <div class="school-name">Henrietta Barnett School</div>
        </a>
        <a class="logo-item" href="https://www.qebarnet.co.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#0A1628;"><i class="fas fa-crown" style="color:var(--gold);"></i></div>
          <div class="school-name">Queen Elizabeth's School</div>
        </a>
        <!-- London Enfield -->
        <a class="logo-item" href="https://www.latymer.co.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#1a2a5e;"><i class="fas fa-book-open" style="color:var(--gold);"></i></div>
          <div class="school-name">The Latymer School</div>
        </a>
        <!-- London Redbridge -->
        <a class="logo-item" href="https://www.ichs.org.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#0A1628;"><i class="fas fa-shield-alt" style="color:var(--gold);"></i></div>
          <div class="school-name">Ilford County High School</div>
        </a>
        <a class="logo-item" href="https://www.woodford.redbridge.sch.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#1a3a1a;"><i class="fas fa-tree" style="color:var(--gold);"></i></div>
          <div class="school-name">Woodford County High School</div>
        </a>
        <!-- London Sutton -->
        <a class="logo-item" href="https://www.nonsuchschool.org/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#3d0055;"><i class="fas fa-star" style="color:var(--gold);"></i></div>
          <div class="school-name">Nonsuch High School for Girls</div>
        </a>
        <a class="logo-item" href="https://cchs.co.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#4a0000;"><i class="fas fa-landmark" style="color:var(--gold);"></i></div>
          <div class="school-name">Wallington County Grammar School</div>
        </a>
        <a class="logo-item" href="https://www.wallingtongirls.org.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#003a3a;"><i class="fas fa-graduation-cap" style="color:var(--gold);"></i></div>
          <div class="school-name">Wallington High School for Girls</div>
        </a>
        <!-- Essex -->
        <a class="logo-item" href="https://www.cchs.co.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#002244;"><i class="fas fa-university" style="color:var(--gold);"></i></div>
          <div class="school-name">Chelmsford County High School for Girls</div>
        </a>
        <a class="logo-item" href="https://www.ccsg.com/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#5a0000;"><i class="fas fa-book" style="color:var(--gold);"></i></div>
          <div class="school-name">Colchester High School for Girls</div>
        </a>
        <a class="logo-item" href="https://www.crgs.co.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#2d0045;"><i class="fas fa-crown" style="color:var(--gold);"></i></div>
          <div class="school-name">Colchester Royal Grammar School</div>
        </a>
        <a class="logo-item" href="https://www.kegs.org.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#3d0000;"><i class="fas fa-chess-king" style="color:var(--gold);"></i></div>
          <div class="school-name">King Edward VI Grammar School</div>
        </a>
        <a class="logo-item" href="https://www.shsb.org.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#001f3d;"><i class="fas fa-shield-alt" style="color:var(--gold);"></i></div>
          <div class="school-name">Southend High School for Boys</div>
        </a>
        <a class="logo-item" href="https://www.whsb.essex.sch.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#1a2040;"><i class="fas fa-anchor" style="color:var(--gold);"></i></div>
          <div class="school-name">Westcliff High School for Boys</div>
        </a>
        <a class="logo-item" href="https://www.whsg.info/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#00334d;"><i class="fas fa-star" style="color:var(--gold);"></i></div>
          <div class="school-name">Westcliff High School for Girls</div>
        </a>
        <!-- Kent -->
        <a class="logo-item" href="https://www.dartfordgrammargirls.org.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#002800;"><i class="fas fa-leaf" style="color:var(--gold);"></i></div>
          <div class="school-name">Dartford Grammar School for Girls</div>
        </a>
        <a class="logo-item" href="https://www.dartfordgrammarschool.org.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#003320;"><i class="fas fa-university" style="color:var(--gold);"></i></div>
          <div class="school-name">Dartford Grammar School</div>
        </a>
        <a class="logo-item" href="https://www.dgsb.co.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#001a33;"><i class="fas fa-anchor" style="color:var(--gold);"></i></div>
          <div class="school-name">Dover Grammar School for Boys</div>
        </a>
        <a class="logo-item" href="https://www.dggs.kent.sch.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#001f4d;"><i class="fas fa-star" style="color:var(--gold);"></i></div>
          <div class="school-name">Dover Grammar School for Girls</div>
        </a>
        <a class="logo-item" href="https://gravesendgrammar.com/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#1a1a2e;"><i class="fas fa-landmark" style="color:var(--gold);"></i></div>
          <div class="school-name">Gravesend Grammar School</div>
        </a>
        <a class="logo-item" href="https://www.highsted.kent.sch.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#003320;"><i class="fas fa-graduation-cap" style="color:var(--gold);"></i></div>
          <div class="school-name">Highsted Grammar School</div>
        </a>
        <a class="logo-item" href="https://www.mggs.org.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#4a0022;"><i class="fas fa-graduation-cap" style="color:var(--gold);"></i></div>
          <div class="school-name">Maidstone Grammar School for Girls</div>
        </a>
        <a class="logo-item" href="https://mgs.kent.sch.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#1e3a4a;"><i class="fas fa-book" style="color:var(--gold);"></i></div>
          <div class="school-name">Maidstone Grammar School</div>
        </a>
        <a class="logo-item" href="https://www.queenelizabeths.kent.sch.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#4a0000;"><i class="fas fa-crown" style="color:var(--gold);"></i></div>
          <div class="school-name">Queen Elizabeth's Grammar School</div>
        </a>
        <a class="logo-item" href="https://www.tgs.kent.sch.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#0a0a3d;"><i class="fas fa-chess-queen" style="color:var(--gold);"></i></div>
          <div class="school-name">Tonbridge Grammar School</div>
        </a>
        <a class="logo-item" href="https://wgsb.co.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#00274d;"><i class="fas fa-compass" style="color:var(--gold);"></i></div>
          <div class="school-name">Wilmington Grammar School for Boys</div>
        </a>
        <a class="logo-item" href="https://www.wgsg.co.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#003344;"><i class="fas fa-star" style="color:var(--gold);"></i></div>
          <div class="school-name">Wilmington Grammar School for Girls</div>
        </a>
        <!-- Independent -->
        <a class="logo-item" href="https://www.brentwoodschool.co.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#4a1c00;"><i class="fas fa-fire" style="color:var(--gold);"></i></div>
          <div class="school-name">Brentwood School</div>
        </a>
        <a class="logo-item" href="https://www.stbons.org/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#1a1a4a;"><i class="fas fa-cross" style="color:var(--gold);"></i></div>
          <div class="school-name">St Bonaventure's School</div>
        </a>
        <a class="logo-item" href="https://www.forest.org.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#003300;"><i class="fas fa-leaf" style="color:var(--gold);"></i></div>
          <div class="school-name">Forest School</div>
        </a>
        <!-- Duplicate set for seamless loop -->
        <a class="logo-item" href="https://www.hbschool.org.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#8B0000;"><i class="fas fa-shield-alt" style="color:var(--gold);"></i></div>
          <div class="school-name">Henrietta Barnett School</div>
        </a>
        <a class="logo-item" href="https://www.qebarnet.co.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#0A1628;"><i class="fas fa-crown" style="color:var(--gold);"></i></div>
          <div class="school-name">Queen Elizabeth's School</div>
        </a>
        <a class="logo-item" href="https://www.latymer.co.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#1a2a5e;"><i class="fas fa-book-open" style="color:var(--gold);"></i></div>
          <div class="school-name">The Latymer School</div>
        </a>
        <a class="logo-item" href="https://www.ichs.org.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#0A1628;"><i class="fas fa-shield-alt" style="color:var(--gold);"></i></div>
          <div class="school-name">Ilford County High School</div>
        </a>
        <a class="logo-item" href="https://www.woodford.redbridge.sch.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#1a3a1a;"><i class="fas fa-tree" style="color:var(--gold);"></i></div>
          <div class="school-name">Woodford County High School</div>
        </a>
        <a class="logo-item" href="https://www.nonsuchschool.org/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#3d0055;"><i class="fas fa-star" style="color:var(--gold);"></i></div>
          <div class="school-name">Nonsuch High School for Girls</div>
        </a>
        <a class="logo-item" href="https://cchs.co.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#4a0000;"><i class="fas fa-landmark" style="color:var(--gold);"></i></div>
          <div class="school-name">Wallington County Grammar School</div>
        </a>
        <a class="logo-item" href="https://www.wallingtongirls.org.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#003a3a;"><i class="fas fa-graduation-cap" style="color:var(--gold);"></i></div>
          <div class="school-name">Wallington High School for Girls</div>
        </a>
        <a class="logo-item" href="https://www.cchs.co.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#002244;"><i class="fas fa-university" style="color:var(--gold);"></i></div>
          <div class="school-name">Chelmsford County High School for Girls</div>
        </a>
        <a class="logo-item" href="https://www.ccsg.com/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#5a0000;"><i class="fas fa-book" style="color:var(--gold);"></i></div>
          <div class="school-name">Colchester High School for Girls</div>
        </a>
        <a class="logo-item" href="https://www.crgs.co.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#2d0045;"><i class="fas fa-crown" style="color:var(--gold);"></i></div>
          <div class="school-name">Colchester Royal Grammar School</div>
        </a>
        <a class="logo-item" href="https://www.kegs.org.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#3d0000;"><i class="fas fa-chess-king" style="color:var(--gold);"></i></div>
          <div class="school-name">King Edward VI Grammar School</div>
        </a>
        <a class="logo-item" href="https://www.shsb.org.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#001f3d;"><i class="fas fa-shield-alt" style="color:var(--gold);"></i></div>
          <div class="school-name">Southend High School for Boys</div>
        </a>
        <a class="logo-item" href="https://www.whsb.essex.sch.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#1a2040;"><i class="fas fa-anchor" style="color:var(--gold);"></i></div>
          <div class="school-name">Westcliff High School for Boys</div>
        </a>
        <a class="logo-item" href="https://www.whsg.info/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#00334d;"><i class="fas fa-star" style="color:var(--gold);"></i></div>
          <div class="school-name">Westcliff High School for Girls</div>
        </a>
        <a class="logo-item" href="https://www.dartfordgrammargirls.org.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#002800;"><i class="fas fa-leaf" style="color:var(--gold);"></i></div>
          <div class="school-name">Dartford Grammar School for Girls</div>
        </a>
        <a class="logo-item" href="https://www.dartfordgrammarschool.org.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#003320;"><i class="fas fa-university" style="color:var(--gold);"></i></div>
          <div class="school-name">Dartford Grammar School</div>
        </a>
        <a class="logo-item" href="https://www.dgsb.co.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#001a33;"><i class="fas fa-anchor" style="color:var(--gold);"></i></div>
          <div class="school-name">Dover Grammar School for Boys</div>
        </a>
        <a class="logo-item" href="https://www.dggs.kent.sch.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#001f4d;"><i class="fas fa-star" style="color:var(--gold);"></i></div>
          <div class="school-name">Dover Grammar School for Girls</div>
        </a>
        <a class="logo-item" href="https://gravesendgrammar.com/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#1a1a2e;"><i class="fas fa-landmark" style="color:var(--gold);"></i></div>
          <div class="school-name">Gravesend Grammar School</div>
        </a>
        <a class="logo-item" href="https://www.highsted.kent.sch.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#003320;"><i class="fas fa-graduation-cap" style="color:var(--gold);"></i></div>
          <div class="school-name">Highsted Grammar School</div>
        </a>
        <a class="logo-item" href="https://www.mggs.org.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#4a0022;"><i class="fas fa-graduation-cap" style="color:var(--gold);"></i></div>
          <div class="school-name">Maidstone Grammar School for Girls</div>
        </a>
        <a class="logo-item" href="https://mgs.kent.sch.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#1e3a4a;"><i class="fas fa-book" style="color:var(--gold);"></i></div>
          <div class="school-name">Maidstone Grammar School</div>
        </a>
        <a class="logo-item" href="https://www.queenelizabeths.kent.sch.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#4a0000;"><i class="fas fa-crown" style="color:var(--gold);"></i></div>
          <div class="school-name">Queen Elizabeth's Grammar School</div>
        </a>
        <a class="logo-item" href="https://www.tgs.kent.sch.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#0a0a3d;"><i class="fas fa-chess-queen" style="color:var(--gold);"></i></div>
          <div class="school-name">Tonbridge Grammar School</div>
        </a>
        <a class="logo-item" href="https://wgsb.co.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#00274d;"><i class="fas fa-compass" style="color:var(--gold);"></i></div>
          <div class="school-name">Wilmington Grammar School for Boys</div>
        </a>
        <a class="logo-item" href="https://www.wgsg.co.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#003344;"><i class="fas fa-star" style="color:var(--gold);"></i></div>
          <div class="school-name">Wilmington Grammar School for Girls</div>
        </a>
        <a class="logo-item" href="https://www.brentwoodschool.co.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#4a1c00;"><i class="fas fa-fire" style="color:var(--gold);"></i></div>
          <div class="school-name">Brentwood School</div>
        </a>
        <a class="logo-item" href="https://www.stbons.org/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#1a1a4a;"><i class="fas fa-cross" style="color:var(--gold);"></i></div>
          <div class="school-name">St Bonaventure's School</div>
        </a>
        <a class="logo-item" href="https://www.forest.org.uk/" target="_blank" rel="noopener noreferrer">
          <div class="school-crest" style="background:#003300;"><i class="fas fa-leaf" style="color:var(--gold);"></i></div>
          <div class="school-name">Forest School</div>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ================================================
       ANNOUNCEMENTS
       ================================================ -->
<section class="announcements-section">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <div class="section-tag"><i class="fas fa-bullhorn"></i> Latest News</div>
      <h2 class="section-title">Announcements &amp; <span>Upcoming Events</span></h2>
      <div class="divider-gold"></div>
    </div>
    <div class="row g-4">
      <?php foreach (array_slice($announcements, 0, 6) as $a): ?>
        <div class="col-md-6 col-lg-4" data-aos="fade-up">
          <div class="announcement-card h-100" style="display:flex;flex-direction:column;">
            <div class="announcement-icon" style="background:<?= $a['icon_bg'] ?>;color:<?= $a['icon_color'] ?>;"><i class="fas <?= $a['icon'] ?>"></i></div>
            <div>
              <div class="announcement-tag" style="background:<?= $a['tag_color'] ?>;color:<?= $a['tag_text'] ?>;"><?= htmlspecialchars($a['tag']) ?></div>
              <div class="announcement-title"><?= htmlspecialchars($a['title']) ?></div>
              <div class="announcement-desc"><?= htmlspecialchars($a['short']) ?></div>
              <div class="announcement-date"><i class="fas <?= $a['date_icon'] ?>"></i> <?= htmlspecialchars($a['date']) ?></div>
            </div>
            <?php if ($a['has_more']): ?>
              <div style="margin-top:auto;padding-top:1rem;">
                <?php if (!empty($a['more_url'])): ?>
                  <a href="<?= $a['more_url'] ?>" class="btn-outline-tpa" style="font-size:.82rem;padding:.4rem .9rem;"><i class="fas fa-arrow-right me-1"></i>Know More</a>
                <?php else: ?>
                  <a href="<?= SITE_URL ?>/announcements.php#<?= $a['id'] ?>" class="btn-outline-tpa" style="font-size:.82rem;padding:.4rem .9rem;"><i class="fas fa-arrow-right me-1"></i>Know More</a>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-5">
      <a href="<?= SITE_URL ?>/announcements.php" class="btn-outline-tpa"><i class="fas fa-bell me-2"></i>View All Announcements</a>
    </div>
  </div>
</section>

<!-- ================================================
       COURSES OVERVIEW
       ================================================ -->
<section class="section-pad" style="background:var(--white);">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <div class="section-tag"><i class="fas fa-graduation-cap"></i> Our Programmes</div>
      <h2 class="section-title">Expert Tuition for <span>Every Stage</span></h2>
      <p class="section-subtitle mx-auto">From Year 1 through to GCSE, our tailored programmes help children gain confidence and achieve their full potential.</p>
      <div class="divider-gold"></div>
    </div>
    <div class="row g-4">

      <!-- KEY STAGE 1 -->
      <div class="col-sm-6 col-lg-4" data-aos="fade-up">
        <div class="course-card">
          <div class="course-card-body">
            <div class="course-card-icon" style="background:#e8f5e9;"><i class="fas fa-book-open" aria-hidden="true"></i></div>
            <span class="course-year-label">Year 1 &amp; Year 2</span>
            <h4 style="font-weight:700;color:var(--navy);margin-bottom:.4rem;">Key Stage 1</h4>
            <p style="color:var(--text-muted);font-size:.9rem;">Phonics, reading, early maths and writing foundations — building confidence for the whole primary journey.</p>
            <div class="gap-pill"><span class="pill">Phonics</span><span class="pill">Reading</span><span class="pill">Maths</span><span class="pill">Writing</span></div>
            <a href="<?= SITE_URL ?>/course-ks1.php" class="btn-outline-tpa" style="font-size:.88rem;padding:.6rem 1.5rem;">Learn More →</a>
          </div>
        </div>
      </div>

      <!-- KEY STAGE 2 -->
      <div class="col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="50">
        <div class="course-card">
          <div class="course-card-body">
            <div class="course-card-icon" style="background:#e3f2fd;"><i class="fas fa-calculator" aria-hidden="true"></i></div>
            <span class="course-year-label">Year 3, 4 &amp; 5</span>
            <h4 style="font-weight:700;color:var(--navy);margin-bottom:.4rem;">Key Stage 2</h4>
            <p style="color:var(--text-muted);font-size:.9rem;">Curriculum enrichment strengthening core Maths and English skills — building towards SATs confidence.</p>
            <div class="gap-pill"><span class="pill">Maths</span><span class="pill">English</span><span class="pill">Reading</span></div>
            <a href="<?= SITE_URL ?>/course-ks2.php" class="btn-outline-tpa" style="font-size:.88rem;padding:.6rem 1.5rem;">Learn More →</a>
          </div>
        </div>
      </div>

      <!-- SATs -->
      <div class="col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
        <div class="course-card">
          <div class="course-card-body">
            <div class="course-card-icon" style="background:#fff8e7;"><i class="fas fa-pencil-alt" aria-hidden="true"></i></div>
            <span class="course-year-label">Year 2 &amp; Year 6</span>
            <h4 style="font-weight:700;color:var(--navy);margin-bottom:.4rem;">SATs Preparation</h4>
            <p style="color:var(--text-muted);font-size:.9rem;">Targeted SATs preparation for Year 2 and Year 6 — covering English and Maths to the latest exam format.</p>
            <div class="gap-pill"><span class="pill">SATs Prep</span><span class="pill">Maths</span><span class="pill">English</span><span class="pill">Grammar</span></div>
            <a href="<?= SITE_URL ?>/course-sats.php" class="btn-outline-tpa" style="font-size:.88rem;padding:.6rem 1.5rem;">Learn More →</a>
          </div>
        </div>
      </div>

      <!-- 11 PLUS — FEATURED -->
      <div class="col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="50">
        <div class="course-card course-card-11plus" style="border:2px solid var(--gold);box-shadow:0 8px 40px rgba(245,166,35,0.18);position:relative;">
          <div style="position:absolute;top:-13px;left:50%;transform:translateX(-50%);background:var(--gold);color:var(--navy);font-size:.75rem;font-weight:800;padding:.3rem 1.2rem;border-radius:20px;white-space:nowrap;letter-spacing:.05em;"><i class="fas fa-star me-1" aria-hidden="true"></i>MOST POPULAR</div>
          <div class="course-card-body">
            <div class="course-card-icon" style="background:#fff8e7;"><i class="fas fa-trophy" aria-hidden="true"></i></div>
            <span class="course-year-label" style="font-size:.9rem;">Year 3 – Year 6</span>
            <h4 style="font-weight:800;font-size:1.3rem;color:var(--navy);margin-bottom:.4rem;">11 Plus Preparation</h4>
            <p style="color:var(--text-muted);font-size:.9rem;">Comprehensive entry preparation for <strong style="color:var(--navy);">Grammar &amp; Independent schools</strong> — VR, NVR, Maths, English and mock exams.</p>
            <div class="gap-pill"><span class="pill">Grammar School</span><span class="pill">Independent School</span><span class="pill">VR</span><span class="pill">NVR</span><span class="pill">Mock Exams</span></div>
            <a href="<?= SITE_URL ?>/course-11plus.php" class="btn-primary-tpa" style="font-size:.88rem;padding:.6rem 1.5rem;">Learn More →</a>
          </div>
        </div>
      </div>

      <!-- KEY STAGE 3 -->
      <div class="col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
        <div class="course-card">
          <div class="course-card-body">
            <div class="course-card-icon" style="background:#f3e5f5;"><i class="fas fa-flask" aria-hidden="true"></i></div>
            <span class="course-year-label">Year 7, 8 &amp; 9</span>
            <h4 style="font-weight:700;color:var(--navy);margin-bottom:.4rem;">Key Stage 3</h4>
            <p style="color:var(--text-muted);font-size:.9rem;">Secondary support in Maths, English and Science — building solid foundations for GCSE success.</p>
            <div class="gap-pill"><span class="pill">Maths</span><span class="pill">English</span><span class="pill">Science</span></div>
            <a href="<?= SITE_URL ?>/course-ks3.php" class="btn-outline-tpa" style="font-size:.88rem;padding:.6rem 1.5rem;">Learn More →</a>
          </div>
        </div>
      </div>

      <!-- GCSE -->
      <div class="col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="150">
        <div class="course-card">
          <div class="course-card-body">
            <div class="course-card-icon" style="background:#e8eaf6;"><i class="fas fa-file-alt" aria-hidden="true"></i></div>
            <span class="course-year-label">Year 10 &amp; Year 11</span>
            <h4 style="font-weight:700;color:var(--navy);margin-bottom:.4rem;">GCSE</h4>
            <p style="color:var(--text-muted);font-size:.9rem;">Expert GCSE tuition in Maths, English and Science — targeted exam preparation to maximise grades.</p>
            <div class="gap-pill"><span class="pill">Maths</span><span class="pill">English</span><span class="pill">Science</span><span class="pill">Exam Prep</span></div>
            <a href="<?= SITE_URL ?>/course-gcse.php" class="btn-outline-tpa" style="font-size:.88rem;padding:.6rem 1.5rem;">Learn More →</a>
          </div>
        </div>
      </div>

      <!-- A-LEVEL -->
      <div class="col-sm-6 col-lg-4" data-aos="fade-up">
        <div class="course-card">
          <div class="course-card-body">
            <div class="course-card-icon" style="background:#e0f2f1;"><i class="fas fa-graduation-cap" aria-hidden="true"></i></div>
            <span class="course-year-label">Year 12 &amp; Year 13</span>
            <h4 style="font-weight:700;color:var(--navy);margin-bottom:.4rem;">A-Level</h4>
            <p style="color:var(--text-muted);font-size:.9rem;">Specialist A-level tuition preparing students for university — with expert subject teachers and exam technique.</p>
            <div class="gap-pill"><span class="pill">Maths</span><span class="pill">English</span><span class="pill">Sciences</span><span class="pill">Uni Prep</span></div>
            <a href="<?= SITE_URL ?>/course-alevel.php" class="btn-outline-tpa" style="font-size:.88rem;padding:.6rem 1.5rem;">Learn More →</a>
          </div>
        </div>
      </div>

      <!-- ADULT LEARNING -->
      <div class="col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="50">
        <div class="course-card">
          <div class="course-card-body">
            <div class="course-card-icon" style="background:#fce4ec;"><span style="font-size:1.6rem;">🌱</span></div>
            <span class="course-year-label" style="color:#c62828;">Adults &amp; Professionals</span>
            <h4 style="font-weight:700;color:var(--navy);margin-bottom:.4rem;">Adult Learning</h4>
            <p style="color:var(--text-muted);font-size:.9rem;">Flexible adult learning programmes — Maths, English, literacy and numeracy for career development or personal growth.</p>
            <div class="gap-pill"><span class="pill">Literacy</span><span class="pill">Numeracy</span><span class="pill">Functional Skills</span></div>
            <a href="<?= SITE_URL ?>/course-adult.php" class="btn-outline-tpa" style="font-size:.88rem;padding:.6rem 1.5rem;">Learn More →</a>
          </div>
        </div>
      </div>

      <!-- NOT SURE -->
      <div class="col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
        <div class="course-card" style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border:none;">
          <div class="course-card-body">
            <div style="font-size:2.2rem;margin-bottom:1rem;">🎯</div>
            <h4 style="font-weight:700;color:var(--white);margin-bottom:.5rem;">Not Sure Where to Start?</h4>
            <p style="color:rgba(255,255,255,.75);font-size:.9rem;">Book a free diagnostic assessment and let our expert team recommend the perfect programme.</p>
            <a href="<?= SITE_URL ?>/contact.php#assessment" class="btn-primary-tpa mt-3" style="font-size:.88rem;padding:.6rem 1.5rem;">Book Free Assessment →</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ================================================
       WHY CHOOSE US
       ================================================ -->
<section class="section-pad section-bg">
  <div class="container">
    <div class="row align-items-center gy-5">
      <div class="col-lg-6" data-aos="fade-right">
        <div class="section-tag"><i class="fas fa-star"></i> Why Choose Us</div>
        <h2 class="section-title">Why <span>Us</span></h2>
        <div class="divider-gold" style="margin:0 0 2rem;"></div>
        <div class="why-item">
          <div class="why-icon"><i class="fas fa-users"></i></div>
          <div>
            <div class="why-title">Small Class Sizes</div>
            <div class="why-text">Maximum 5–6 students per class — every child gets focused, personalised attention every lesson.</div>
          </div>
        </div>
        <div class="why-item why-item-highlight">
          <div class="why-icon"><i class="fas fa-hand-holding-heart"></i></div>
          <div>
            <div class="why-title">Inclusive Learning for All</div>
            <div class="why-text">Talent Pool Academy does not discriminate — we welcome diverse abilities, backgrounds and learning needs, including those with Dyslexia, Autism, ADHD and other disabilities.</div>
          </div>
        </div>
        <div class="why-item">
          <div class="why-icon"><i class="fas fa-medal"></i></div>
          <div>
            <div class="why-title">16+ Years of Proven Results</div>
            <div class="why-text">Since 2008, we've prepared over 5,000 students across 11 Plus, GCSE, A-level and more — with 90% + grammar school enrolment rates.</div>
          </div>
        </div>
        <div class="why-item">
          <div class="why-icon"><i class="fas fa-chalkboard-teacher"></i></div>
          <div>
            <div class="why-title">Expert, Qualified Teachers</div>
            <div class="why-text">All teachers are qualified, DBS-checked, Well experienced and fully trained.</div>
          </div>
        </div>
        <div class="why-item">
          <div class="why-icon"><i class="fas fa-laptop-house"></i></div>
          <div>
            <div class="why-title">Online &amp; In-Centre Options</div>
            <div class="why-text">Attend at Chadwell Heath, Chelmsford, or join online classes — the same quality wherever you are.</div>
          </div>
        </div>
        <div class="trust-badges mt-3">
          <div class="trust-badge"><i class="fas fa-check"></i> DBS Checked</div>
          <div class="trust-badge"><i class="fas fa-check"></i> Expert Teachers</div>
          <div class="trust-badge"><i class="fas fa-check"></i> Trusted Since 2008</div>
        </div>
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <div style="border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-lg);">
          <img src="<?= SITE_URL ?>/images/gallery_class.webp" alt="TPA small group tutoring session" style="width:100%;height:480px;object-fit:cover;">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ================================================
       STAR STUDENTS — SCROLLABLE CAROUSEL
       ================================================ -->
<section class="star-students-section">
  <div class="container">
    <div class="text-center mb-4" data-aos="fade-up">
      <div class="section-tag"><i class="fas fa-star"></i> Student Success</div>
      <h2 class="section-title">Our <span>Star Students</span></h2>
      <p class="section-subtitle mx-auto">These incredible students secured places at top grammar schools — we're so proud of every one of them!</p>
      <div class="divider-gold"></div>
    </div>

    <!-- Carousel -->
    <div class="star-carousel-wrap" data-aos="fade-up">
      <button class="star-carousel-btn star-carousel-prev" id="starPrev" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
      <div class="star-carousel-track-wrap">
        <div class="star-carousel-track" id="starTrack">
          <?php foreach ($star_students as $student): ?>
            <div class="star-card">
              <div class="star-card-img-wrap">
                <span class="star-card-ribbon"><i class="fas fa-trophy me-1" aria-hidden="true"></i>Grammar Place <?= $student['year'] ?></span>
                <img src="<?= $student['img'] ?>" alt="<?= htmlspecialchars($student['name']) ?> — <?= htmlspecialchars($student['school']) ?>">
              </div>
              <div class="star-card-body">
                <div class="star-name"><?= htmlspecialchars($student['name']) ?></div>
                <div class="star-placement"><?= htmlspecialchars($student['school']) ?></div>
                <div class="star-school"><i class="fas fa-map-marker-alt text-gold"></i> <?= htmlspecialchars($student['programme']) ?></div>
                <div class="star-quote"><?= htmlspecialchars($student['quote']) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <button class="star-carousel-btn star-carousel-next" id="starNext" aria-label="Next"><i class="fas fa-chevron-right"></i></button>
    </div>
    <!-- Dots -->
    <div class="star-carousel-dots" id="starDots"></div>

    <!-- <div class="text-center mt-4" data-aos="fade-up">
      <a href="<?= SITE_URL ?>/about.php#success" class="btn-outline-tpa"><i class="fas fa-users me-2"></i>See All Success Stories</a>
    </div> -->
  </div>
</section>

<!-- ================================================
       EVENTS PREVIEW
       ================================================ -->
<section class="section-pad section-bg">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <div class="section-tag"><i class="fas fa-calendar-alt"></i> Events &amp; Activities</div>
      <h2 class="section-title">Life Beyond the <span>Classroom</span></h2>
      <p class="section-subtitle mx-auto">From summer camps and talent shows to awards days and open days — there is always something exciting happening at TPA.</p>
      <div class="divider-gold"></div>
    </div>
    <div class="row g-4">
      <div class="col-md-4" data-aos="fade-up">
        <a href="<?= SITE_URL ?>/events.php" style="text-decoration:none;color:inherit;">
          <div class="event-card h-100">
            <div class="event-card-media">
              <img src="<?= SITE_URL ?>/images/events/romford/talent-show-2025-talent-pool-academy-romford-1.jpeg" alt="Annual Talent Show 2025 — Chadwell Heath" loading="lazy">
              <div class="event-card-media-overlay"><span class="event-branch-badge event-branch-romford"><i class="fas fa-map-marker-alt"></i> Chadwell Heath</span></div>
            </div>
            <div class="event-card-body">
              <div class="event-card-date"><i class="fas fa-calendar-alt"></i> December 2025</div>
              <div class="event-card-title">Annual Talent Show 2025 — Chadwell Heath</div>
              <div class="event-card-desc">Singing, dancing, music, poetry and more — graced by Mayor Princess Bright in a spectacular evening of student talent.</div>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
        <a href="<?= SITE_URL ?>/events.php" style="text-decoration:none;color:inherit;">
          <div class="event-card h-100">
            <div class="event-card-media">
              <img src="<?= SITE_URL ?>/images/events/chelmsford/talent-show-2025-talent-pool-academy-chelmsford-1.jpeg" alt="Annual Talent Show 2025 — Chelmsford" loading="lazy">
              <div class="event-card-media-overlay"><span class="event-branch-badge event-branch-chelmsford"><i class="fas fa-map-marker-alt"></i> Chelmsford</span></div>
            </div>
            <div class="event-card-body">
              <div class="event-card-date"><i class="fas fa-calendar-alt"></i> December 2025</div>
              <div class="event-card-title">Annual Talent Show 2025 — Chelmsford</div>
              <div class="event-card-desc">A lively evening of creativity at our Chelmsford centre, attended by the Deputy Mayor, celebrating our students' incredible talents.</div>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <a href="<?= SITE_URL ?>/events.php" style="text-decoration:none;color:inherit;">
          <div class="event-card h-100">
            <div class="event-card-media" style="position:relative;">
              <img src="<?= SITE_URL ?>/images/founder.png" alt="Founder's New Year Message 2026" loading="lazy">
              <div class="event-card-media-overlay"><span class="event-branch-badge event-branch-both"><i class="fas fa-map-marker-alt"></i> Both Centres</span></div>
              <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;">
                <div style="width:52px;height:52px;background:rgba(245,166,35,0.9);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(0,0,0,0.35);"><i class="fas fa-play" style="color:#fff;font-size:1.1rem;margin-left:3px;"></i></div>
              </div>
            </div>
            <div class="event-card-body">
              <div class="event-card-date"><i class="fas fa-calendar-alt"></i> December 2025</div>
              <div class="event-card-title">Founder's New Year Message 2026</div>
              <div class="event-card-desc">Mrs Meena Kumar shares her vision for 2026 — reflecting on a brilliant 2025 and the year ahead for TPA students.</div>
            </div>
          </div>
        </a>
      </div>
    </div>
    <div class="text-center mt-5">
      <a href="<?= SITE_URL ?>/events.php" class="btn-outline-tpa"><i class="fas fa-calendar-alt me-2"></i>View All Events &amp; Photos</a>
    </div>
  </div>
</section>


<!-- ================================================
       TESTIMONIALS SLIDER
       ================================================ -->
<section class="section-pad section-bg">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <div class="section-tag"><i class="fas fa-quote-left"></i> Parent Reviews</div>
      <h2 class="section-title">What <span>Parents Say</span></h2>
      <div class="divider-gold"></div>
    </div>
    <!-- Slider -->
    <div class="testi-slider-wrap" data-aos="fade-up">
      <button class="testi-carousel-btn" id="testiPrev" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
      <div class="testi-slider-track-wrap">
        <div class="testi-slider-track" id="testiTrack">
          <?php foreach ($all_reviews as $r):
            $stars = str_repeat('★', $r['rating']) . str_repeat('☆', 5 - $r['rating']);
            $badge = $r['platform'] === 'google'
              ? '<span class="google-badge ms-1"><i class="fab fa-google me-1"></i>Google</span>'
              : '<span class="google-badge ms-1" style="background:#00b67a;color:#fff;"><i class="fas fa-star me-1"></i>Trustpilot</span>';
          ?>
          <div class="testimonial-card">
            <div class="testimonial-stars"><?= $stars ?> <?= $badge ?></div>
            <p class="testimonial-text"><?= htmlspecialchars($r['text']) ?></p>
            <div class="testimonial-author"><?= htmlspecialchars($r['name']) ?></div>
            <div class="testimonial-meta"><?= htmlspecialchars($r['meta']) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <button class="testi-carousel-btn" id="testiNext" aria-label="Next"><i class="fas fa-chevron-right"></i></button>
    </div>
    <div class="testi-carousel-dots" id="testiDots"></div>
  </div>
</section>

<!-- ================================================
       CTA
       ================================================ -->
<section class="cta-section">
  <div class="container text-center position-relative">
    <div data-aos="fade-up">
      <h2>Ready to Give Your Child the <span style="color:var(--gold);">Best Chance?</span></h2>
      <p>Book a free, no-obligation diagnostic assessment and discover the right programme for your child.</p>
      <div class="d-flex gap-3 justify-content-center flex-wrap">
        <a href="<?= SITE_URL ?>/contact.php#assessment" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Assessment</a>
        <a href="tel:07772922943" class="btn-secondary-tpa"><i class="fas fa-phone-alt"></i> Call <?= PHONE ?></a>
      </div>
    </div>
  </div>
</section>

<!-- ================================================
       LOCATIONS
       ================================================ -->
<section class="section-pad-sm" style="background:var(--off-white);">
  <div class="container">
    <div class="row g-4 text-center">
      <div class="col-md-4 text-md-start" data-aos="fade-up">
        <div style="font-size:.82rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:var(--gray);margin-bottom:.5rem;">Our Centres</div>
        <h3 style="font-family:var(--font-heading);color:var(--navy);">Two Locations + Online</h3>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
        <div style="display:flex;gap:.75rem;align-items:flex-start;justify-content:center;text-align:left;">
          <i class="fas fa-map-marker-alt text-gold mt-1" style="font-size:1.2rem;"></i>
          <div>
            <div style="font-weight:700;color:var(--navy);">Chadwell Heath Centre</div>
            <div style="color:var(--text-muted);font-size:.9rem;">60 High Road, Chadwell Heath<br>Romford RM6 6PP</div>
          </div>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <div style="display:flex;gap:.75rem;align-items:flex-start;justify-content:center;text-align:left;">
          <i class="fas fa-map-marker-alt text-gold mt-1" style="font-size:1.2rem;"></i>
          <div>
            <div style="font-weight:700;color:var(--navy);">Chelmsford Centre</div>
            <div style="color:var(--text-muted);font-size:.9rem;">4B Corporation Road<br>Chelmsford CM1 2AR</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>