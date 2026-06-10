<?php
$page_title = 'GCSE Tuition (Year 10–11)';
$meta_description = 'GCSE tuition at Talent Pool Academy. Expert Maths, English and Science tuition for Year 10 and 11 students in Chadwell Heath and Chelmsford. Small classes, proven results.';
$schema_extra = '<script type="application/ld+json">' . json_encode([
  '@context'=>'https://schema.org','@type'=>'Course',
  'name'=>'GCSE Tuition (Year 10–11)',
  'description'=>'Expert GCSE tuition in Maths, English Language, English Literature and Science for Year 10 and 11 students. Small classes, exam technique, past paper practice and grade improvement focus.',
  'url'=>'https://www.talentpoolacademy.com/course-gcse.php',
  'provider'=>['@type'=>'EducationalOrganization','name'=>'Talent Pool Academy','url'=>'https://www.talentpoolacademy.com'],
  'educationalLevel'=>'GCSE (Year 10–11, ages 14–16)',
  'courseMode'=>['onsite','online'],
  'teaches'=>['GCSE Maths','GCSE English Language','GCSE English Literature','GCSE Biology','GCSE Chemistry','GCSE Physics','Exam Technique'],
  'hasCourseInstance'=>[
    ['@type'=>'CourseInstance','courseMode'=>'onsite','location'=>['@type'=>'Place','name'=>'Chadwell Heath','address'=>['@type'=>'PostalAddress','addressLocality'=>'Romford','postalCode'=>'RM6 6PP','addressCountry'=>'GB']]],
    ['@type'=>'CourseInstance','courseMode'=>'onsite','location'=>['@type'=>'Place','name'=>'Chelmsford','address'=>['@type'=>'PostalAddress','addressLocality'=>'Chelmsford','postalCode'=>'CM1 2AR','addressCountry'=>'GB']]],
    ['@type'=>'CourseInstance','courseMode'=>'online'],
  ],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . '</script>';
require_once 'includes/feedback-data.php';
require_once 'includes/header.php';
$course_tag = 'gcse';
?>

  <section class="course-hero">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-7 course-hero-content">
          <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb" style="background:none;padding:0;"><li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php" style="color:rgba(255,255,255,0.6);">Home</a></li><li class="breadcrumb-item"><a href="<?= SITE_URL ?>/courses.php" style="color:rgba(255,255,255,0.6);">Courses</a></li><li class="breadcrumb-item active" style="color:var(--gold);">GCSE Tuition</li></ol></nav>
          <div class="section-tag mb-3" style="background:rgba(26,35,126,0.3);border-color:rgba(92,107,192,0.5);color:#9fa8da;"><i class="fas fa-book-open"></i> Year 10 – 11</div>
          <h1>GCSE <span style="color:var(--gold);">Tuition Programme</span></h1>
          <p class="course-hero-desc">Expert GCSE tuition for Year 10 and 11 — maximising grades in Maths, English and Science through targeted exam preparation, past paper practice and personalised feedback in small groups.</p>
          <div class="course-meta-bar">
            <div class="course-meta-item"><i class="fas fa-user-graduate"></i> Year 10 &amp; Year 11</div>
            <div class="course-meta-item"><i class="fas fa-users"></i> Max 5–6 per class</div>
            <div class="course-meta-item"><i class="fas fa-book"></i> Maths · English · Science</div>
            <div class="course-meta-item"><i class="fas fa-laptop"></i> Online available</div>
          </div>
          <div class="d-flex gap-3 flex-wrap">
            <a href="#enquire" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Assessment</a>
            <a href="#included" class="btn-secondary-tpa"><i class="fas fa-book-open"></i> What We Teach</a>
          </div>
          <div class="trust-badges mt-4">
            <div class="trust-badge"><i class="fas fa-check"></i> Exam board aligned</div>
            <div class="trust-badge"><i class="fas fa-check"></i> Past paper practice</div>
            <div class="trust-badge"><i class="fas fa-check"></i> DBS-checked teachers</div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="course-sidebar-card" id="enquire" style="position:relative;top:auto;">
            <div class="course-sidebar-header"><h4>Book a Free Assessment</h4><p>No obligation · Expert advice included</p></div>
            <div class="course-sidebar-body">
              <div class="urgency-strip">📝 GCSE Tuition — Enrolment Open Now!</div>
              <form class="tpa-enquiry-form" novalidate>
                <input type="hidden" name="source"  value="Website - GCSE">
                <input type="hidden" name="subject" value="GCSE Tuition">
                <div class="mb-3"><label class="form-label-tpa" for="gcse-child">Child's Name *</label><input type="text" id="gcse-child" name="child_name" class="form-control-tpa" placeholder="Child's full name" required></div>
                <div class="mb-3"><label class="form-label-tpa" for="gcse-year">Year Group *</label><select id="gcse-year" name="year_group" class="form-control-tpa" required style="appearance:auto;"><option value="">Select year</option><option>Year 10</option><option>Year 11</option></select></div>
                <div class="mb-3"><label class="form-label-tpa" for="gcse-phone">Phone *</label><input type="tel" id="gcse-phone" name="phone" class="form-control-tpa" placeholder="07xxx xxxxxx" required autocomplete="tel"></div>
                <div class="mb-3"><label class="form-label-tpa" for="gcse-email">Email *</label><input type="email" id="gcse-email" name="email" class="form-control-tpa" placeholder="email@example.com" required autocomplete="email"></div>
                <div class="mb-3"><label class="form-label-tpa" for="gcse-subj">Subject(s) Needed</label><select id="gcse-subj" name="subject_detail" class="form-control-tpa" style="appearance:auto;"><option>Maths &amp; English</option><option>Maths only</option><option>English only</option><option>Science (single/double/triple)</option><option>All subjects</option></select></div>
                <div class="mb-4"><label class="form-label-tpa" for="gcse-centre">Preferred Centre</label><select id="gcse-centre" name="centre" class="form-control-tpa" style="appearance:auto;"><option>Chadwell Heath</option><option>Chelmsford</option><option>Online</option></select></div>
                <button type="submit" class="btn-primary-tpa w-100" style="justify-content:center;"><i class="fas fa-calendar-check me-2"></i>Reserve Free Place</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="stats-bar"><div class="container"><div class="row g-4 text-center">
    <div class="col-6 col-md-3 stat-item"><div class="stat-value"><span class="stat-number">3</span></div><div class="stat-label">Subjects Available</div></div>
    <div class="col-6 col-md-3 stat-item"><div class="stat-value"><span class="stat-number">5</span><span class="stat-suffix">–6</span></div><div class="stat-label">Max Class Size</div></div>
    <div class="col-6 col-md-3 stat-item"><div class="stat-value"><span class="stat-number">16</span><span class="stat-suffix">+</span></div><div class="stat-label">Years' Experience</div></div>
    <div class="col-6 col-md-3 stat-item"><div class="stat-value"><span class="stat-number">2</span></div><div class="stat-label">Centre Locations</div></div>
  </div></div></section>

  <section class="section-pad" id="included">
    <div class="container"><div class="row g-5 align-items-start">
      <div class="col-lg-7" data-aos="fade-right">
        <div class="section-tag mb-3"><i class="fas fa-list-check"></i> What We Teach</div>
        <h2 class="section-title" style="font-size:2rem;">Full GCSE Coverage — <span>Maths, English &amp; Science</span></h2>
        <div class="divider-gold" style="margin:0 0 2rem;"></div>
        <ul class="included-list">
          <li><span><strong style="color:var(--navy);">Maths</strong> — Number, algebra, ratio, geometry, statistics and probability. Full coverage of Higher and Foundation tiers aligned to AQA / Edexcel / OCR.</span></li>
          <li><span><strong style="color:var(--navy);">English Language</strong> — Reading comprehension, descriptive and narrative writing, transactional writing, and spoken language preparation.</span></li>
          <li><span><strong style="color:var(--navy);">English Literature</strong> — Analysing set texts, poetry comparison, essay writing technique and 19th-century prose.</span></li>
          <li><span><strong style="color:var(--navy);">Biology</strong> — Cell biology, organisation, disease, bioenergetics, homeostasis, inheritance and ecology.</span></li>
          <li><span><strong style="color:var(--navy);">Chemistry</strong> — Atomic structure, bonding, quantitative chemistry, chemical changes, energy changes and organic chemistry.</span></li>
          <li><span><strong style="color:var(--navy);">Physics</strong> — Forces, energy, waves, electricity, magnetism, particle model and space physics.</span></li>
          <li><span><strong style="color:var(--navy);">Past Paper Practice</strong> — Regular timed exam practice with personalised marking and detailed feedback.</span></li>
          <li><span><strong style="color:var(--navy);">Exam Technique</strong> — Command word analysis, mark allocation strategy, and common error correction.</span></li>
        </ul>
      </div>
      <div class="col-lg-5" data-aos="fade-left">
        <div style="background:var(--off-white);border-radius:var(--radius-lg);padding:2rem;border:1px solid rgba(10,22,40,0.07);">
          <h5 style="font-weight:700;color:var(--navy);margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:2px solid var(--gold);">Course Details</h5>
          <table style="width:100%;font-size:.92rem;">
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Year Groups</td><td style="font-weight:700;color:var(--navy);text-align:right;">Year 10 &amp; 11</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Class Size</td><td style="font-weight:700;color:var(--navy);text-align:right;">Max 5–6 students</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Subjects</td><td style="font-weight:700;color:var(--navy);text-align:right;">Maths, English, Science</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Exam Boards</td><td style="font-weight:700;color:var(--navy);text-align:right;">AQA · Edexcel · OCR</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Locations</td><td style="font-weight:700;color:var(--navy);text-align:right;">Chadwell Heath, Chelmsford, Online</td></tr>
            <tr><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Sessions</td><td style="font-weight:700;color:var(--navy);text-align:right;">Weekly + intensive revision</td></tr>
          </table>
        </div>

        <div style="background:var(--gold-pale);border:1.5px solid rgba(245,166,35,0.4);border-radius:var(--radius-md);padding:1.5rem;margin-top:1.5rem;">
          <h6 style="font-weight:700;color:var(--navy);margin-bottom:.75rem;"><i class="fas fa-star text-gold me-2"></i>Inclusion &amp; Accessibility</h6>
          <p style="font-size:.88rem;color:var(--text-muted);margin:0;">We welcome learners with Dyslexia, Autism, ADHD and other learning needs. All our GCSE classes are inclusive and supportive environments.</p>
        </div>
      </div>
    </div></div>
  </section>

  <!-- WHY CHOOSE GCSE AT TPA -->
  <section class="section-pad section-bg">
    <div class="container">
      <div class="text-center mb-5" data-aos="fade-up">
        <div class="section-tag"><i class="fas fa-trophy"></i> Why TPA for GCSE</div>
        <h2 class="section-title">Maximise Your <span>GCSE Grades</span></h2>
        <div class="divider-gold"></div>
      </div>
      <div class="row g-4 gsap-stagger">
        <div class="col-sm-6 col-lg-3"><div class="value-card"><div class="value-icon"><i class="fas fa-users"></i></div><h5 style="font-weight:700;color:var(--navy);">Small Groups</h5><p style="color:var(--text-muted);font-size:.9rem;">Maximum 5–6 students means your child gets the attention they deserve — not lost in a crowd.</p></div></div>
        <div class="col-sm-6 col-lg-3"><div class="value-card"><div class="value-icon"><i class="fas fa-bullseye"></i></div><h5 style="font-weight:700;color:var(--navy);">Exam-Focused</h5><p style="color:var(--text-muted);font-size:.9rem;">We focus on exactly what examiners look for — mark scheme familiarity and command word mastery.</p></div></div>
        <div class="col-sm-6 col-lg-3"><div class="value-card"><div class="value-icon"><i class="fas fa-chalkboard-teacher"></i></div><h5 style="font-weight:700;color:var(--navy);">Subject Specialists</h5><p style="color:var(--text-muted);font-size:.9rem;">QTS-qualified, DBS-checked teachers who specialise in their GCSE subject — not generalists.</p></div></div>
        <div class="col-sm-6 col-lg-3"><div class="value-card"><div class="value-icon"><i class="fas fa-laptop-house"></i></div><h5 style="font-weight:700;color:var(--navy);">Flexible Learning</h5><p style="color:var(--text-muted);font-size:.9rem;">In-centre at Chadwell Heath or Chelmsford, or online via live interactive classes — your choice.</p></div></div>
      </div>
    </div>
  </section>

  <?php
  $star_carousel_tags = ['gcse'];
  require_once 'includes/star-carousel.php';
  ?>

  <?php render_testimonials_section($course_tag, 'What <span>GCSE Parents Say</span>', 0, false); ?>

  <!-- CTA -->
  <section class="cta-section">
    <div class="container text-center position-relative">
      <div data-aos="fade-up">
        <h2>Ready to <span style="color:var(--gold);">Boost GCSE Grades?</span></h2>
        <p>Book a free diagnostic assessment — we'll identify gaps and create a personalised study plan.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
          <a href="<?= SITE_URL ?>/contact.php#assessment" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Assessment</a>
          <a href="tel:<?= PHONE ?>" class="btn-secondary-tpa"><i class="fas fa-phone-alt"></i> Call <?= PHONE ?></a>
        </div>
      </div>
    </div>
  </section>

<?php require_once 'includes/footer.php'; ?>
