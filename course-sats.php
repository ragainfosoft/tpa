<?php
$page_title = 'SATs Preparation (Year 6)';
$meta_description = 'SATs preparation at Talent Pool Academy. Expert Year 6 SATs tuition in Maths and English. Chadwell Heath and Chelmsford centres plus online classes.';
$schema_extra = '<script type="application/ld+json">' . json_encode([
  '@context'=>'https://schema.org','@type'=>'Course',
  'name'=>'SATs Preparation Programme (Year 2 & Year 6)',
  'description'=>'Expert SATs tuition for Year 2 and Year 6 students covering Maths, Reading, Grammar, Punctuation and Spelling. Aligned to the national curriculum with structured mock practice.',
  'url'=>'https://www.talentpoolacademy.com/course-sats.php',
  'provider'=>['@type'=>'EducationalOrganization','name'=>'Talent Pool Academy','url'=>'https://www.talentpoolacademy.com'],
  'educationalLevel'=>'KS1 & KS2 (Year 2 and Year 6)',
  'courseMode'=>['onsite','online'],
  'teaches'=>['SATs Maths','Reading Comprehension','Grammar','Punctuation','Spelling'],
  'hasCourseInstance'=>[
    ['@type'=>'CourseInstance','courseMode'=>'onsite','location'=>['@type'=>'Place','name'=>'Chadwell Heath','address'=>['@type'=>'PostalAddress','addressLocality'=>'Romford','postalCode'=>'RM6 6PP','addressCountry'=>'GB']]],
    ['@type'=>'CourseInstance','courseMode'=>'onsite','location'=>['@type'=>'Place','name'=>'Chelmsford','address'=>['@type'=>'PostalAddress','addressLocality'=>'Chelmsford','postalCode'=>'CM1 2AR','addressCountry'=>'GB']]],
    ['@type'=>'CourseInstance','courseMode'=>'online'],
  ],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . '</script>';
require_once 'includes/feedback-data.php';
require_once 'includes/star-students-data.php';
require_once 'includes/header.php';
$course_tag = 'sats';
?>

  <!-- HERO -->
  <section class="course-hero">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-7 course-hero-content">
          <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb" style="background:none;padding:0;"><li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php" style="color:rgba(255,255,255,0.6);">Home</a></li><li class="breadcrumb-item"><a href="<?= SITE_URL ?>/courses.php" style="color:rgba(255,255,255,0.6);">Courses</a></li><li class="breadcrumb-item active" style="color:var(--gold);">SATs Preparation</li></ol></nav>
          <div class="section-tag mb-3" style="background:rgba(21,101,192,0.2);border-color:rgba(21,101,192,0.4);color:#90caf9;"><i class="fas fa-pencil-alt"></i> Year 2 &amp; Year 6</div>
          <h1>SATs <span style="color:var(--gold);">Preparation Programme</span></h1>
          <p class="course-hero-desc">Expert SATs tuition for Year 2 and Year 6 — aligned to the national curriculum. Covering Maths, Reading Comprehension, Grammar, Punctuation &amp; Spelling — building confidence for exam day.</p>
          <div class="course-meta-bar">
            <div class="course-meta-item"><i class="fas fa-graduation-cap"></i> Year 2 &amp; Year 6</div>
            <div class="course-meta-item"><i class="fas fa-users"></i> Max 5–6 per class</div>
            <div class="course-meta-item"><i class="fas fa-calendar-alt"></i> Runs Sept – May</div>
            <div class="course-meta-item"><i class="fas fa-laptop"></i> Online &amp; in-centre</div>
          </div>
          <div class="d-flex gap-3 flex-wrap">
            <a href="#enquire" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Assessment</a>
            <a href="#included" class="btn-secondary-tpa"><i class="fas fa-book-open"></i> What's Included</a>
          </div>
          <div class="trust-badges mt-4">
            <div class="trust-badge"><i class="fas fa-chart-line"></i> Average 2 sub-levels progress</div>
            <div class="trust-badge"><i class="fas fa-check"></i> 2025 curriculum aligned</div>
            <div class="trust-badge"><i class="fas fa-check"></i> Mock SATs included</div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="course-sidebar-card" id="enquire" style="position:relative;top:auto;">
            <div class="course-sidebar-header"><h4>Book a Free Assessment</h4><p>No obligation · Tailored report included</p></div>
            <div class="course-sidebar-body">
              <div class="urgency-strip">🔥 SATs coming up — time is running out!</div>
              <form id="satsForm" class="tpa-enquiry-form" novalidate>
                <input type="hidden" name="source"  value="Website - SATs">
                <input type="hidden" name="subject" value="SATs Preparation">
                <div class="mb-3"><label class="form-label-tpa" for="sats-child">Child's Name *</label><input type="text" id="sats-child" name="child_name" class="form-control-tpa" placeholder="Child's full name" required></div>
                <div class="mb-3"><label class="form-label-tpa" for="sats-year">Child's Year Group *</label><select id="sats-year" name="year_group" class="form-control-tpa" required style="appearance:auto;"><option value="">Select year</option><option>Year 2</option><option>Year 5</option><option>Year 6</option></select></div>
                <div class="mb-3"><label class="form-label-tpa" for="sats-phone">Phone Number *</label><input type="tel" id="sats-phone" name="phone" class="form-control-tpa" placeholder="07xxx xxxxxx" required autocomplete="tel"></div>
                <div class="mb-3"><label class="form-label-tpa" for="sats-email">Email Address *</label><input type="email" id="sats-email" name="email" class="form-control-tpa" placeholder="email@example.com" required autocomplete="email"></div>
                <div class="mb-4"><label class="form-label-tpa" for="sats-centre">Preferred Centre</label><select id="sats-centre" name="centre" class="form-control-tpa" style="appearance:auto;"><option>Chadwell Heath</option><option>Chelmsford</option><option>Online</option></select></div>
                <button type="submit" class="btn-primary-tpa w-100" style="justify-content:center;"><i class="fas fa-calendar-check me-2"></i>Reserve My Free Place</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="stats-bar">
    <div class="container"><div class="row g-4 text-center">
      <div class="col-6 col-md-3 stat-item"><span class="stat-number">95%</span><span class="stat-number">+</span><div class="stat-label">SATs Success Rate</div></div>
      <div class="col-6 col-md-3 stat-item"><span class="stat-number">2</span><div class="stat-label">Sub-levels avg progress</div></div>
      <div class="col-6 col-md-3 stat-item"><span class="stat-number">10</span><div class="stat-label">Max Class Size</div></div>
      <div class="col-6 col-md-3 stat-item"><span class="stat-number">4</span><div class="stat-label">Mock SATs included</div></div>
    </div></div>
  </section>

  <!-- WHAT'S INCLUDED -->
  <section class="section-pad" id="included">
    <div class="container">
      <div class="row g-5 align-items-start">
        <div class="col-lg-7" data-aos="fade-right">
          <div class="section-tag mb-3"><i class="fas fa-list-check"></i> What's Included</div>
          <h2 class="section-title" style="font-size:2rem;">Full SATs Coverage — <span>Maths &amp; English</span></h2>
          <div class="divider-gold" style="margin:0 0 2rem;"></div>
          <ul class="included-list">
            <li><span><strong style="color:var(--navy);">Maths — Arithmetic &amp; Reasoning</strong> — Full coverage of number, fractions, geometry and statistics using past papers.</span></li>
            <li><span><strong style="color:var(--navy);">Reading Comprehension</strong> — Fiction, non-fiction and poetry analysis. Answering inference, retrieval and vocabulary questions.</span></li>
            <li><span><strong style="color:var(--navy);">SPaG (Grammar, Punctuation &amp; Spelling)</strong> — All KS2 grammar terminology and punctuation covered with past-paper questions.</span></li>
            <li><span><strong style="color:var(--navy);">Writing Composition</strong> — Planning, drafting and editing narrative and non-fiction writing pieces.</span></li>
            <li><span><strong style="color:var(--navy);">4 Full Mock SATs Papers</strong> — Timed under exam conditions with detailed performance reports and targets.</span></li>
            <li><span><strong style="color:var(--navy);">Home Practice Pack</strong> — Weekly worksheets linked to each lesson for continued progress at home.</span></li>
            <li><span><strong style="color:var(--navy);">Progress Reports</strong> — Termly written reports plus ongoing communication via our parent portal.</span></li>
          </ul>
        </div>
        <div class="col-lg-5" data-aos="fade-left">
          <div style="background:var(--off-white);border-radius:var(--radius-lg);padding:2rem;border:1px solid rgba(10,22,40,0.07);">
            <h5 style="font-weight:700;color:var(--navy);margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:2px solid var(--gold);">Course Details</h5>
            <table style="width:100%;font-size:.92rem;">
              <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Year Groups</td><td style="font-weight:700;color:var(--navy);text-align:right;">Year 2 &amp; Year 6</td></tr>
              <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Class Size</td><td style="font-weight:700;color:var(--navy);text-align:right;">Max 5–6 students</td></tr>
              <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Sessions</td><td style="font-weight:700;color:var(--navy);text-align:right;">Weekly (Sat &amp; Sun)</td></tr>
              <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Duration</td><td style="font-weight:700;color:var(--navy);text-align:right;">2 hours per session</td></tr>
              <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Locations</td><td style="font-weight:700;color:var(--navy);text-align:right;">Chadwell Heath · Chelmsford · Online</td></tr>
              <tr><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Assessment</td><td style="font-weight:700;color:var(--gold);text-align:right;">FREE (No obligation)</td></tr>
            </table>
            <a href="#enquire" class="btn-primary-tpa w-100 mt-3" style="justify-content:center;"><i class="fas fa-calendar-check me-2"></i>Book Free Assessment</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CURRICULUM -->
  <section class="section-pad section-bg">
    <div class="container">
      <div class="text-center mb-5" data-aos="fade-up">
        <div class="section-tag"><i class="fas fa-book-open"></i> Curriculum</div>
        <h2 class="section-title">SATs <span>Curriculum Breakdown</span></h2>
        <div class="divider-gold"></div>
      </div>
      <div style="border-radius:var(--radius-md);overflow:hidden;box-shadow:var(--shadow-sm);" data-aos="fade-up">
        <table class="curriculum-table">
          <thead><tr><th>Session Type</th><th>Maths Focus</th><th>English Focus</th></tr></thead>
          <tbody>
            <tr><td>Week 1–4</td><td>Number, place value, addition &amp; subtraction</td><td>Reading inference &amp; retrieval</td></tr>
            <tr><td>Week 5–8</td><td>Multiplication, division, fractions</td><td>Grammar &amp; punctuation (SPaG)</td></tr>
            <tr><td>Week 9–12</td><td>Ratio, percentages, algebra</td><td>Vocabulary &amp; word meaning</td></tr>
            <tr><td>Week 13–16</td><td>Geometry, measurement, statistics</td><td>Spelling rules &amp; patterns</td></tr>
            <tr><td>Mock Exams</td><td>Full Arithmetic + Reasoning Papers 2 &amp; 3</td><td>Full Reading &amp; SPaG Papers</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <?php
  $star_carousel_tags = ['sats', '11plus'];
  require_once 'includes/star-carousel.php';
  ?>

  <?php render_testimonials_section($course_tag, 'What <span>SATs Parents Say</span>', 0, false); ?>

  <!-- CTA -->
  <section class="cta-section">
    <div class="container text-center position-relative"><div data-aos="fade-up">
      <h2>Help Your Child <span style="color:var(--gold);">Ace Their SATs</span></h2>
      <p>Book a free diagnostic assessment today and get a personalised SATs preparation plan for your child.</p>
      <div class="d-flex gap-3 justify-content-center flex-wrap">
        <a href="#enquire" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Assessment</a>
        <a href="tel:<?= PHONE ?>" class="btn-secondary-tpa"><i class="fas fa-phone-alt"></i> Call <?= PHONE ?></a>
      </div>
    </div></div>
  </section>

<?php require_once 'includes/footer.php'; ?>
