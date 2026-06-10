<?php
$page_title = 'A-Level Tuition (Year 12–13)';
$meta_description = 'A-Level tuition at Talent Pool Academy. Expert specialist tutors for Year 12 and 13 students in Chadwell Heath and Chelmsford. Small group and 1-to-1 options available.';
$schema_extra = '<script type="application/ld+json">' . json_encode([
  '@context'=>'https://schema.org','@type'=>'Course',
  'name'=>'A-Level Tuition (Year 12–13)',
  'description'=>'Specialist A-Level tuition for Year 12 and 13 students. Subject-expert teachers, small group and 1-to-1 options, past paper practice, university application support.',
  'url'=>'https://www.talentpoolacademy.com/course-alevel.php',
  'provider'=>['@type'=>'EducationalOrganization','name'=>'Talent Pool Academy','url'=>'https://www.talentpoolacademy.com'],
  'educationalLevel'=>'A-Level (Year 12–13, ages 16–18)',
  'courseMode'=>['onsite','online'],
  'teaches'=>['A-Level Maths','A-Level Further Maths','A-Level English','A-Level Sciences','A-Level Economics','Exam Technique','University Application Support'],
  'hasCourseInstance'=>[
    ['@type'=>'CourseInstance','courseMode'=>'onsite','location'=>['@type'=>'Place','name'=>'Chadwell Heath','address'=>['@type'=>'PostalAddress','addressLocality'=>'Romford','postalCode'=>'RM6 6PP','addressCountry'=>'GB']]],
    ['@type'=>'CourseInstance','courseMode'=>'onsite','location'=>['@type'=>'Place','name'=>'Chelmsford','address'=>['@type'=>'PostalAddress','addressLocality'=>'Chelmsford','postalCode'=>'CM1 2AR','addressCountry'=>'GB']]],
    ['@type'=>'CourseInstance','courseMode'=>'online'],
  ],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . '</script>';
require_once 'includes/feedback-data.php';
require_once 'includes/header.php';
$course_tag = 'alevel';
?>

  <section class="course-hero">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-7 course-hero-content">
          <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb" style="background:none;padding:0;"><li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php" style="color:rgba(255,255,255,0.6);">Home</a></li><li class="breadcrumb-item"><a href="<?= SITE_URL ?>/courses.php" style="color:rgba(255,255,255,0.6);">Courses</a></li><li class="breadcrumb-item active" style="color:var(--gold);">A-Level Tuition</li></ol></nav>
          <div class="section-tag mb-3" style="background:rgba(0,77,64,0.3);border-color:rgba(0,137,123,0.5);color:#80cbc4;"><i class="fas fa-university"></i> Year 12 – 13</div>
          <h1>A-Level <span style="color:var(--gold);">Tuition Programme</span></h1>
          <p class="course-hero-desc">Specialist A-Level tuition for Year 12 and 13 — taught by subject experts who understand the demands of university entrance. Small groups, rigorous content coverage and focused exam technique for top grades.</p>
          <div class="course-meta-bar">
            <div class="course-meta-item"><i class="fas fa-user-graduate"></i> Year 12 &amp; Year 13</div>
            <div class="course-meta-item"><i class="fas fa-users"></i> Max 5–6 per class</div>
            <div class="course-meta-item"><i class="fas fa-book"></i> Maths · Sciences · English</div>
            <div class="course-meta-item"><i class="fas fa-laptop"></i> Online available</div>
          </div>
          <div class="d-flex gap-3 flex-wrap">
            <a href="#enquire" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Consultation</a>
            <a href="#included" class="btn-secondary-tpa"><i class="fas fa-book-open"></i> Subjects Covered</a>
          </div>
          <div class="trust-badges mt-4">
            <div class="trust-badge"><i class="fas fa-check"></i> University prep focus</div>
            <div class="trust-badge"><i class="fas fa-check"></i> Subject specialist tutors</div>
            <div class="trust-badge"><i class="fas fa-check"></i> UCAS support available</div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="course-sidebar-card" id="enquire" style="position:relative;top:auto;">
            <div class="course-sidebar-header"><h4>Book a Free Consultation</h4><p>Discuss your subject &amp; university goals</p></div>
            <div class="course-sidebar-body">
              <div class="urgency-strip"><i class="fas fa-graduation-cap me-1" aria-hidden="true"></i>A-Level Tuition — Enquire Today!</div>
              <form class="tpa-enquiry-form" novalidate>
                <input type="hidden" name="source"  value="Website - A-Level">
                <input type="hidden" name="subject" value="A-Level Tuition">
                <div class="mb-3"><label class="form-label-tpa" for="alv-phone">Phone *</label><input type="tel" id="alv-phone" name="phone" class="form-control-tpa" placeholder="07xxx xxxxxx" required autocomplete="tel"></div>
                <div class="mb-3"><label class="form-label-tpa" for="alv-email">Email *</label><input type="email" id="alv-email" name="email" class="form-control-tpa" placeholder="email@example.com" required autocomplete="email"></div>
                <div class="mb-3"><label class="form-label-tpa" for="alv-year">Year Group *</label><select id="alv-year" name="year_group" class="form-control-tpa" required style="appearance:auto;"><option value="">Select year</option><option>Year 12</option><option>Year 13</option></select></div>
                <div class="mb-3"><label class="form-label-tpa" for="alv-subj">Subject(s) Needed</label><select id="alv-subj" name="subject_detail" class="form-control-tpa" style="appearance:auto;"><option>Maths</option><option>Further Maths</option><option>English Literature</option><option>Biology</option><option>Chemistry</option><option>Physics</option><option>Other — please advise</option></select></div>
                <div class="mb-4"><label class="form-label-tpa" for="alv-centre">Preferred Centre</label><select id="alv-centre" name="centre" class="form-control-tpa" style="appearance:auto;"><option>Chadwell Heath</option><option>Chelmsford</option><option>Online</option></select></div>
                <button type="submit" class="btn-primary-tpa w-100" style="justify-content:center;"><i class="fas fa-calendar-check me-2"></i>Book Consultation</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="stats-bar"><div class="container"><div class="row g-4 text-center">
    <div class="col-6 col-md-3 stat-item"><div class="stat-value"><span class="stat-number">6</span><span class="stat-suffix">+</span></div><div class="stat-label">Subjects Available</div></div>
    <div class="col-6 col-md-3 stat-item"><div class="stat-value"><span class="stat-number">5</span><span class="stat-suffix">–6</span></div><div class="stat-label">Max Class Size</div></div>
    <div class="col-6 col-md-3 stat-item"><div class="stat-value"><span class="stat-number">16</span><span class="stat-suffix">+</span></div><div class="stat-label">Years' Experience</div></div>
    <div class="col-6 col-md-3 stat-item"><div class="stat-value"><span class="stat-number">2</span></div><div class="stat-label">Centre Locations</div></div>
  </div></div></section>

  <section class="section-pad" id="included">
    <div class="container"><div class="row g-5 align-items-start">
      <div class="col-lg-7" data-aos="fade-right">
        <div class="section-tag mb-3"><i class="fas fa-list-check"></i> Subjects Covered</div>
        <h2 class="section-title" style="font-size:2rem;">A-Level Subjects at <span>Talent Pool Academy</span></h2>
        <div class="divider-gold" style="margin:0 0 2rem;"></div>
        <ul class="included-list">
          <li><span><strong style="color:var(--navy);">Maths &amp; Further Maths</strong> — Pure maths, mechanics, statistics. Full A-level coverage for AQA, Edexcel and OCR specifications.</span></li>
          <li><span><strong style="color:var(--navy);">English Literature</strong> — Prose, poetry and drama analysis; comparative essays; coursework support; exam technique for AQA and Edexcel.</span></li>
          <li><span><strong style="color:var(--navy);">Biology</strong> — Biological molecules, cells, organisms, genetics, evolution, and ecology at A-level depth.</span></li>
          <li><span><strong style="color:var(--navy);">Chemistry</strong> — Physical, organic, and inorganic chemistry — including mechanisms, equilibria, and spectroscopy.</span></li>
          <li><span><strong style="color:var(--navy);">Physics</strong> — Mechanics, electricity, waves, nuclear physics, and astrophysics at A-level.</span></li>
          <li><span><strong style="color:var(--navy);">Exam Technique &amp; Essay Skills</strong> — Command word analysis, structured argument building, time management under exam conditions.</span></li>
          <li><span><strong style="color:var(--navy);">Coursework &amp; Personal Statement Support</strong> — Guidance on extended essays, NEAs, and UCAS personal statements for university applications.</span></li>
        </ul>
        <div style="background:var(--gold-pale);border:1.5px solid rgba(245,166,35,0.3);border-radius:var(--radius-md);padding:1.2rem 1.5rem;margin-top:1.5rem;">
          <p style="margin:0;font-size:.9rem;color:var(--text-muted);"><strong style="color:var(--navy);">📌 Subject not listed?</strong> Contact us — we may be able to source a specialist tutor for your subject needs.</p>
        </div>
      </div>
      <div class="col-lg-5" data-aos="fade-left">
        <div style="background:var(--off-white);border-radius:var(--radius-lg);padding:2rem;border:1px solid rgba(10,22,40,0.07);">
          <h5 style="font-weight:700;color:var(--navy);margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:2px solid var(--gold);">Course Details</h5>
          <table style="width:100%;font-size:.92rem;">
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Year Groups</td><td style="font-weight:700;color:var(--navy);text-align:right;">Year 12 &amp; 13</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Class Size</td><td style="font-weight:700;color:var(--navy);text-align:right;">Max 5–6 students</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Format</td><td style="font-weight:700;color:var(--navy);text-align:right;">Small group &amp; 1-to-1</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Exam Boards</td><td style="font-weight:700;color:var(--navy);text-align:right;">AQA · Edexcel · OCR</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Locations</td><td style="font-weight:700;color:var(--navy);text-align:right;">Chadwell Heath, Chelmsford, Online</td></tr>
            <tr><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">UCAS Support</td><td style="font-weight:700;color:var(--navy);text-align:right;">Available on request</td></tr>
          </table>
        </div>

        <div style="background:var(--gold-pale);border:1.5px solid rgba(245,166,35,0.4);border-radius:var(--radius-md);padding:1.5rem;margin-top:1.5rem;">
          <h6 style="font-weight:700;color:var(--navy);margin-bottom:.75rem;"><i class="fas fa-hand-holding-heart text-gold me-2"></i>Inclusive &amp; Accessible</h6>
          <p style="font-size:.88rem;color:var(--text-muted);margin:0;">We welcome all learners regardless of background, ability or learning need — including those with Dyslexia, Autism or ADHD. Our tutors adapt their approach to suit every student.</p>
        </div>
      </div>
    </div></div>
  </section>

  <!-- WHY TPA FOR A-LEVEL -->
  <section class="section-pad section-bg">
    <div class="container">
      <div class="text-center mb-5" data-aos="fade-up">
        <div class="section-tag"><i class="fas fa-trophy"></i> Why TPA for A-Level</div>
        <h2 class="section-title">Your Route to <span>Top Grades &amp; Top Universities</span></h2>
        <div class="divider-gold"></div>
      </div>
      <div class="row g-4 gsap-stagger">
        <div class="col-sm-6 col-lg-3"><div class="value-card"><div class="value-icon"><i class="fas fa-users"></i></div><h5 style="font-weight:700;color:var(--navy);">Tiny Classes</h5><p style="color:var(--text-muted);font-size:.9rem;">Maximum 5–6 students — detailed discussion, immediate feedback, and deep subject understanding.</p></div></div>
        <div class="col-sm-6 col-lg-3"><div class="value-card"><div class="value-icon"><i class="fas fa-graduation-cap"></i></div><h5 style="font-weight:700;color:var(--navy);">University-Ready</h5><p style="color:var(--text-muted);font-size:.9rem;">We prepare students not just to pass, but to thrive — developing the independent thinking universities demand.</p></div></div>
        <div class="col-sm-6 col-lg-3"><div class="value-card"><div class="value-icon"><i class="fas fa-chalkboard-teacher"></i></div><h5 style="font-weight:700;color:var(--navy);">Expert Specialists</h5><p style="color:var(--text-muted);font-size:.9rem;">All A-Level tutors are subject specialists — many with first-class degrees and school teaching experience.</p></div></div>
        <div class="col-sm-6 col-lg-3"><div class="value-card"><div class="value-icon"><i class="fas fa-laptop-house"></i></div><h5 style="font-weight:700;color:var(--navy);">Flexible Options</h5><p style="color:var(--text-muted);font-size:.9rem;">In-centre at Chadwell Heath or Chelmsford, or live online — fitting around sixth-form timetables.</p></div></div>
      </div>
    </div>
  </section>

  <?php
  $star_carousel_tags = ['alevel'];
  require_once 'includes/star-carousel.php';
  ?>

  <?php render_testimonials_section($course_tag, 'What <span>A-Level Students Say</span>', 0, false); ?>

  <!-- CTA -->
  <section class="cta-section">
    <div class="container text-center position-relative">
      <div data-aos="fade-up">
        <h2>Ready to <span style="color:var(--gold);">Achieve Your Best A-Levels?</span></h2>
        <p>Book a free consultation and let our specialists create a targeted study plan for your subjects.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
          <a href="<?= SITE_URL ?>/contact.php#assessment" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Consultation</a>
          <a href="tel:<?= PHONE ?>" class="btn-secondary-tpa"><i class="fas fa-phone-alt"></i> Call <?= PHONE ?></a>
        </div>
      </div>
    </div>
  </section>

<?php require_once 'includes/footer.php'; ?>
