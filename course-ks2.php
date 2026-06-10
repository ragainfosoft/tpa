<?php
$page_title = 'Key Stage 2 Tuition (Year 3–5)';
$meta_description = 'Key Stage 2 tuition at Talent Pool Academy. Expert Maths and English tuition for Year 3, 4, and 5 children in Chadwell Heath and Chelmsford.';
$schema_extra = '<script type="application/ld+json">' . json_encode([
  '@context'=>'https://schema.org','@type'=>'Course',
  'name'=>'Key Stage 2 Tuition (Year 3–5)',
  'description'=>'Expert Maths and English tuition for Year 3, 4 and 5 pupils at KS2. Building on primary foundations with structured learning, times tables, long division, reading comprehension and creative writing.',
  'url'=>'https://www.talentpoolacademy.com/course-ks2.php',
  'provider'=>['@type'=>'EducationalOrganization','name'=>'Talent Pool Academy','url'=>'https://www.talentpoolacademy.com'],
  'educationalLevel'=>'Key Stage 2 (Year 3–5, ages 7–10)',
  'courseMode'=>['onsite','online'],
  'teaches'=>['KS2 Maths','KS2 English','Reading Comprehension','Creative Writing','Times Tables'],
  'hasCourseInstance'=>[
    ['@type'=>'CourseInstance','courseMode'=>'onsite','location'=>['@type'=>'Place','name'=>'Chadwell Heath','address'=>['@type'=>'PostalAddress','addressLocality'=>'Romford','postalCode'=>'RM6 6PP','addressCountry'=>'GB']]],
    ['@type'=>'CourseInstance','courseMode'=>'onsite','location'=>['@type'=>'Place','name'=>'Chelmsford','address'=>['@type'=>'PostalAddress','addressLocality'=>'Chelmsford','postalCode'=>'CM1 2AR','addressCountry'=>'GB']]],
    ['@type'=>'CourseInstance','courseMode'=>'online'],
  ],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . '</script>';
require_once 'includes/feedback-data.php';
require_once 'includes/header.php';
$course_tag = 'ks2';
?>

  <section class="course-hero">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-7 course-hero-content">
          <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb" style="background:none;padding:0;"><li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php" style="color:rgba(255,255,255,0.6);">Home</a></li><li class="breadcrumb-item active" style="color:var(--gold);">KS2 Tuition</li></ol></nav>
          <div class="section-tag mb-3" style="background:rgba(21,101,192,0.2);border-color:rgba(21,101,192,0.4);color:#90caf9;"><i class="fas fa-graduation-cap"></i> Year 3, 4 &amp; 5</div>
          <h1>Key Stage 2 <span style="color:var(--gold);">Tuition Programme</span></h1>
          <p class="course-hero-desc">Comprehensive Maths and English tuition for Year 3, 4, and 5 — strengthening curriculum knowledge, closing gaps, and preparing for SATs and 11 Plus ahead of time.</p>
          <div class="course-meta-bar">
            <div class="course-meta-item"><i class="fas fa-graduation-cap"></i> Year 3, 4 &amp; 5</div>
            <div class="course-meta-item"><i class="fas fa-users"></i> Max 5–6 per class</div>
            <div class="course-meta-item"><i class="fas fa-book"></i> Maths &amp; English</div>
            <div class="course-meta-item"><i class="fas fa-laptop"></i> Online available</div>
          </div>
          <div class="d-flex gap-3 flex-wrap">
            <a href="#enquire" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Assessment</a>
            <a href="#included" class="btn-secondary-tpa"><i class="fas fa-book-open"></i> What We Cover</a>
          </div>
          <div class="trust-badges mt-4">
            <div class="trust-badge"><i class="fas fa-check"></i> Curriculum-aligned</div>
            <div class="trust-badge"><i class="fas fa-check"></i> Gap analysis included</div>
            <div class="trust-badge"><i class="fas fa-check"></i> SATs &amp; 11+ gateway</div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="course-sidebar-card" id="enquire" style="position:relative;top:auto;">
            <div class="course-sidebar-header"><h4>Book a Free Assessment</h4><p>Full diagnostic report included</p></div>
            <div class="course-sidebar-body">
              <div class="urgency-strip">🌟 KS2 Summer Intensive Starting July 2025!</div>
              <form id="ks2Form" class="tpa-enquiry-form" novalidate>
                <input type="hidden" name="source"  value="Website - KS2">
                <input type="hidden" name="subject" value="KS2 Tuition">
                <div class="mb-3"><label class="form-label-tpa" for="ks2-child">Child's Name *</label><input type="text" id="ks2-child" name="child_name" class="form-control-tpa" placeholder="Child's full name" required></div>
                <div class="mb-3"><label class="form-label-tpa" for="ks2-year">Year Group *</label><select id="ks2-year" name="year_group" class="form-control-tpa" required style="appearance:auto;"><option value="">Select year</option><option>Year 3</option><option>Year 4</option><option>Year 5</option></select></div>
                <div class="mb-3"><label class="form-label-tpa" for="ks2-phone">Phone *</label><input type="tel" id="ks2-phone" name="phone" class="form-control-tpa" placeholder="07xxx xxxxxx" required autocomplete="tel"></div>
                <div class="mb-3"><label class="form-label-tpa" for="ks2-email">Email *</label><input type="email" id="ks2-email" name="email" class="form-control-tpa" placeholder="email@example.com" required autocomplete="email"></div>
                <div class="mb-4"><label class="form-label-tpa" for="ks2-centre">Centre</label><select id="ks2-centre" name="centre" class="form-control-tpa" style="appearance:auto;"><option>Chadwell Heath</option><option>Chelmsford</option><option>Online</option></select></div>
                <button type="submit" class="btn-primary-tpa w-100" style="justify-content:center;"><i class="fas fa-calendar-check me-2"></i>Reserve Free Place</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="stats-bar"><div class="container"><div class="row g-4 text-center">
    <div class="col-6 col-md-3 stat-item"><span class="stat-number">8</span><div class="stat-label">Max Class Size</div></div>
    <div class="col-6 col-md-3 stat-item"><span class="stat-number">2</span><div class="stat-label">Subjects Covered</div></div>
    <div class="col-6 col-md-3 stat-item"><span class="stat-number">3</span><div class="stat-label">Year Groups (3, 4, 5)</div></div>
    <div class="col-6 col-md-3 stat-item"><span class="stat-number">100</span><span class="stat-number">%</span><div class="stat-label">Curriculum Aligned</div></div>
  </div></div></section>

  <section class="section-pad" id="included">
    <div class="container"><div class="row g-5 align-items-start">
      <div class="col-lg-7" data-aos="fade-right">
        <div class="section-tag mb-3"><i class="fas fa-list-check"></i> What We Teach</div>
        <h2 class="section-title" style="font-size:2rem;">Full KS2 <span>Maths &amp; English Coverage</span></h2>
        <div class="divider-gold" style="margin:0 0 2rem;"></div>
        <ul class="included-list">
          <li><span><strong style="color:var(--navy);">Maths — Number</strong> — Place value to millions, all four operations, fractions, decimals and percentages.</span></li>
          <li><span><strong style="color:var(--navy);">Maths — Geometry &amp; Measurement</strong> — Properties of shapes, perimeter, area, angles and coordinates.</span></li>
          <li><span><strong style="color:var(--navy);">Maths — Data &amp; Statistics</strong> — Tables, bar charts, pie charts, and averages appropriate to year group.</span></li>
          <li><span><strong style="color:var(--navy);">English — Reading Comprehension</strong> — Inference, retrieval, summarising, and vocabulary in context.</span></li>
          <li><span><strong style="color:var(--navy);">English — Writing</strong> — Planning, narrative writing, persuasive writing and report writing.</span></li>
          <li><span><strong style="color:var(--navy);">Grammar &amp; Punctuation</strong> — Year-appropriate SPaG topics aligned to the national curriculum.</span></li>
          <li><span><strong style="color:var(--navy);">Progress Reports</strong> — Termly written reports with targets and home learning suggestions.</span></li>
        </ul>
      </div>
      <div class="col-lg-5" data-aos="fade-left">
        <div style="background:var(--off-white);border-radius:var(--radius-lg);padding:2rem;border:1px solid rgba(10,22,40,0.07);">
          <h5 style="font-weight:700;color:var(--navy);margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:2px solid var(--gold);">Course Details</h5>
          <table style="width:100%;font-size:.92rem;">
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Year Groups</td><td style="font-weight:700;color:var(--navy);text-align:right;">Year 3, 4 &amp; 5</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Class Size</td><td style="font-weight:700;color:var(--navy);text-align:right;">Max 5–6 students</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Sessions</td><td style="font-weight:700;color:var(--navy);text-align:right;">Weekly (Sat &amp; Sun)</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Duration</td><td style="font-weight:700;color:var(--navy);text-align:right;">2 hours per session</td></tr>
            <tr><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Assessment</td><td style="font-weight:700;color:var(--gold);text-align:right;">FREE diagnostic</td></tr>
          </table>
          <a href="#enquire" class="btn-primary-tpa w-100 mt-3" style="justify-content:center;"><i class="fas fa-calendar-check me-2"></i>Book Free Assessment</a>
        </div>
      </div>
    </div></div>
  </section>

  <?php
  $star_carousel_tags = ['ks2'];
  require_once 'includes/star-carousel.php';
  ?>

  <?php render_testimonials_section($course_tag, 'What <span>KS2 Parents Say</span>'); ?>

  <section class="cta-section"><div class="container text-center position-relative"><div data-aos="fade-up">
    <h2>Set Your Child Up for <span style="color:var(--gold);">SATs &amp; 11 Plus Success</span></h2>
    <p>Book a free diagnostic assessment and get a personalised learning plan tailored to your child's specific needs.</p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="#enquire" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Assessment</a>
      <a href="tel:<?= PHONE ?>" class="btn-secondary-tpa"><i class="fas fa-phone-alt"></i> Call <?= PHONE ?></a>
    </div>
  </div></div></section>

<?php require_once 'includes/footer.php'; ?>
