<?php
$page_title = 'Key Stage 3 Tuition (Year 7–9)';
$meta_description = 'Key Stage 3 tuition at Talent Pool Academy. Expert Maths, English and Science tuition for Year 7, 8, and 9 students in Chadwell Heath and Chelmsford.';
$schema_extra = '<script type="application/ld+json">' . json_encode([
  '@context'=>'https://schema.org','@type'=>'Course',
  'name'=>'Key Stage 3 Tuition (Year 7–9)',
  'description'=>'Maths, English and Science tuition for secondary school students in Year 7, 8 and 9. Bridging the gap between primary and GCSE with structured KS3 curriculum support.',
  'url'=>'https://www.talentpoolacademy.com/course-ks3.php',
  'provider'=>['@type'=>'EducationalOrganization','name'=>'Talent Pool Academy','url'=>'https://www.talentpoolacademy.com'],
  'educationalLevel'=>'Key Stage 3 (Year 7–9, ages 11–14)',
  'courseMode'=>['onsite','online'],
  'teaches'=>['KS3 Maths','KS3 English','KS3 Science','Algebra','Essay Writing'],
  'hasCourseInstance'=>[
    ['@type'=>'CourseInstance','courseMode'=>'onsite','location'=>['@type'=>'Place','name'=>'Chadwell Heath','address'=>['@type'=>'PostalAddress','addressLocality'=>'Romford','postalCode'=>'RM6 6PP','addressCountry'=>'GB']]],
    ['@type'=>'CourseInstance','courseMode'=>'onsite','location'=>['@type'=>'Place','name'=>'Chelmsford','address'=>['@type'=>'PostalAddress','addressLocality'=>'Chelmsford','postalCode'=>'CM1 2AR','addressCountry'=>'GB']]],
    ['@type'=>'CourseInstance','courseMode'=>'online'],
  ],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . '</script>';
$schema_extra .= '<script type="application/ld+json">' . json_encode([
  '@context'=>'https://schema.org','@type'=>'FAQPage',
  'mainEntity'=>[
    ['@type'=>'Question','name'=>'Why is KS3 support important?','acceptedAnswer'=>['@type'=>'Answer','text'=>'KS3 is the critical bridge between primary and GCSE. Students who fall behind in Year 7 and 8 often struggle in GCSE unless the gaps are identified and addressed early. TPA\'s KS3 programme builds secure foundations and confidence for the crucial GCSE years.']],
    ['@type'=>'Question','name'=>'Do you also offer GCSE tuition?','acceptedAnswer'=>['@type'=>'Answer','text'=>'Yes — our Year 9 GCSE transition programme blends KS3 completion with early GCSE preparation. For Year 10 and 11 students needing GCSE-level tuition, please contact us directly as availability is limited.']],
  ],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . '</script>';
require_once 'includes/feedback-data.php';
require_once 'includes/header.php';
$course_tag = 'ks3';
?>

  <section class="course-hero">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-7 course-hero-content">
          <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb" style="background:none;padding:0;"><li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php" style="color:rgba(255,255,255,0.6);">Home</a></li><li class="breadcrumb-item active" style="color:var(--gold);">KS3 Tuition</li></ol></nav>
          <div class="section-tag mb-3" style="background:rgba(106,27,154,0.2);border-color:rgba(106,27,154,0.4);color:#ce93d8;"><i class="fas fa-flask"></i> Year 7, 8 &amp; 9</div>
          <h1>Key Stage 3 <span style="color:var(--gold);">Tuition Programme</span></h1>
          <p class="course-hero-desc">Expert secondary school support for Year 7, 8, and 9 — covering Maths, English, and Science. Build the solid foundations your child needs to excel in GCSEs and beyond.</p>
          <div class="course-meta-bar">
            <div class="course-meta-item"><i class="fas fa-graduation-cap"></i> Year 7, 8 &amp; 9</div>
            <div class="course-meta-item"><i class="fas fa-users"></i> Max 5–6 per class</div>
            <div class="course-meta-item"><i class="fas fa-book"></i> Maths · English · Science</div>
            <div class="course-meta-item"><i class="fas fa-laptop"></i> Online available</div>
          </div>
          <div class="d-flex gap-3 flex-wrap">
            <a href="#enquire" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Assessment</a>
            <a href="#included" class="btn-secondary-tpa"><i class="fas fa-book-open"></i> What We Teach</a>
          </div>
          <div class="trust-badges mt-4">
            <div class="trust-badge"><i class="fas fa-check"></i> GCSE gateway preparation</div>
            <div class="trust-badge"><i class="fas fa-flask"></i> NEW Science programme</div>
            <div class="trust-badge"><i class="fas fa-check"></i> Expert subject teachers</div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="course-sidebar-card" id="enquire" style="position:relative;top:auto;">
            <div class="course-sidebar-header"><h4>Book a Free Assessment</h4><p>No obligation · Expert advice included</p></div>
            <div class="course-sidebar-body">
              <div class="urgency-strip">🔬 New KS3 Science Programme — Starting April 2025!</div>
              <form id="ks3Form" class="tpa-enquiry-form" novalidate>
                <input type="hidden" name="source"  value="Website - KS3">
                <input type="hidden" name="subject" value="KS3 Tuition">
                <div class="mb-3"><label class="form-label-tpa" for="ks3-child">Child's Name *</label><input type="text" id="ks3-child" name="child_name" class="form-control-tpa" placeholder="Child's full name" required></div>
                <div class="mb-3"><label class="form-label-tpa" for="ks3-year">Year Group *</label><select id="ks3-year" name="year_group" class="form-control-tpa" required style="appearance:auto;"><option value="">Select year</option><option>Year 7</option><option>Year 8</option><option>Year 9</option></select></div>
                <div class="mb-3"><label class="form-label-tpa" for="ks3-phone">Phone *</label><input type="tel" id="ks3-phone" name="phone" class="form-control-tpa" placeholder="07xxx xxxxxx" required autocomplete="tel"></div>
                <div class="mb-3"><label class="form-label-tpa" for="ks3-email">Email *</label><input type="email" id="ks3-email" name="email" class="form-control-tpa" placeholder="email@example.com" required autocomplete="email"></div>
                <div class="mb-3"><label class="form-label-tpa" for="ks3-subj">Subject Needed</label><select id="ks3-subj" name="subject_detail" class="form-control-tpa" style="appearance:auto;"><option>Maths &amp; English</option><option>Maths only</option><option>English only</option><option>Science</option><option>All subjects</option></select></div>
                <div class="mb-4"><label class="form-label-tpa" for="ks3-centre">Centre</label><select id="ks3-centre" name="centre" class="form-control-tpa" style="appearance:auto;"><option>Chadwell Heath</option><option>Chelmsford</option><option>Online</option></select></div>
                <button type="submit" class="btn-primary-tpa w-100" style="justify-content:center;"><i class="fas fa-calendar-check me-2"></i>Reserve Free Place</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="stats-bar"><div class="container"><div class="row g-4 text-center">
    <div class="col-6 col-md-3 stat-item"><span class="stat-number">3</span><div class="stat-label">Subjects Available</div></div>
    <div class="col-6 col-md-3 stat-item"><span class="stat-number">10</span><div class="stat-label">Max Class Size</div></div>
    <div class="col-6 col-md-3 stat-item"><span class="stat-number">16</span><span class="stat-number">+</span><div class="stat-label">Years' Experience</div></div>
    <div class="col-6 col-md-3 stat-item"><span class="stat-number">3</span><div class="stat-label">Year Groups (7, 8, 9)</div></div>
  </div></div></section>

  <section class="section-pad" id="included">
    <div class="container"><div class="row g-5 align-items-start">
      <div class="col-lg-7" data-aos="fade-right">
        <div class="section-tag mb-3"><i class="fas fa-list-check"></i> What We Teach</div>
        <h2 class="section-title" style="font-size:2rem;">Full KS3 Coverage — <span>Maths, English &amp; Science</span></h2>
        <div class="divider-gold" style="margin:0 0 2rem;"></div>
        <ul class="included-list">
          <li><span><strong style="color:var(--navy);">Maths — Algebra &amp; Number</strong> — Algebraic expressions, equations, sequences, advanced fractions and ratio.</span></li>
          <li><span><strong style="color:var(--navy);">Maths — Geometry &amp; Data</strong> — Angles, Pythagoras, transformations, statistics, and probability.</span></li>
          <li><span><strong style="color:var(--navy);">English — Literature</strong> — Analysing texts, exploring themes and characters, and essay writing technique.</span></li>
          <li><span><strong style="color:var(--navy);">English — Language</strong> — Descriptive, narrative, and analytical writing for GCSE preparation.</span></li>
          <li><span><strong style="color:var(--navy);">Science — Biology</strong> — Cells, lifestyle, ecology, and genetics aligned to KS3 national curriculum.</span></li>
          <li><span><strong style="color:var(--navy);">Science — Chemistry</strong> — Particles, reactions, acids and alkalis, and the periodic table.</span></li>
          <li><span><strong style="color:var(--navy);">Science — Physics</strong> — Forces, energy, electricity and waves appropriate to year group.</span></li>
          <li><span><strong style="color:var(--navy);">GCSE Transition Support</strong> — Year 9 students receive dedicated GCSE preparation guidance and subject choice advice.</span></li>
        </ul>
      </div>
      <div class="col-lg-5" data-aos="fade-left">
        <div style="background:var(--off-white);border-radius:var(--radius-lg);padding:2rem;border:1px solid rgba(10,22,40,0.07);">
          <h5 style="font-weight:700;color:var(--navy);margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:2px solid var(--gold);">Course Details</h5>
          <table style="width:100%;font-size:.92rem;">
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Year Groups</td><td style="font-weight:700;color:var(--navy);text-align:right;">Year 7, 8 &amp; 9</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Class Size</td><td style="font-weight:700;color:var(--navy);text-align:right;">Max 5–6 students</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Sessions</td><td style="font-weight:700;color:var(--navy);text-align:right;">Weekdays &amp; Weekend</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Duration</td><td style="font-weight:700;color:var(--navy);text-align:right;">2 hours per session</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Subjects</td><td style="font-weight:700;color:var(--navy);text-align:right;">Maths · English · Science</td></tr>
            <tr><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Assessment</td><td style="font-weight:700;color:var(--gold);text-align:right;">FREE diagnostic</td></tr>
          </table>
          <a href="#enquire" class="btn-primary-tpa w-100 mt-3" style="justify-content:center;"><i class="fas fa-calendar-check me-2"></i>Book Free Assessment</a>
        </div>
      </div>
    </div></div>
  </section>

  <section class="section-pad section-bg">
    <div class="container"><div class="row justify-content-center"><div class="col-lg-8" data-aos="fade-up">
      <div class="text-center mb-5"><div class="section-tag"><i class="fas fa-question-circle"></i> FAQ</div><h2 class="section-title">KS3 <span>Questions Answered</span></h2><div class="divider-gold"></div></div>
      <div class="accordion" id="faqAccordion">
        <div class="accordion-item" style="border-radius:var(--radius-md);border:1px solid rgba(10,22,40,0.08);margin-bottom:1rem;overflow:hidden;"><h2 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" style="font-weight:600;color:var(--navy);">Why is KS3 support important?</button></h2><div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion"><div class="accordion-body" style="color:var(--text-muted);">KS3 is the critical bridge between primary and GCSE. Students who fall behind in Year 7 and 8 often struggle in GCSE unless the gaps are identified and addressed early. TPA's KS3 programme builds secure foundations and confidence for the crucial GCSE years.</div></div></div>
        <div class="accordion-item" style="border-radius:var(--radius-md);border:1px solid rgba(10,22,40,0.08);overflow:hidden;"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" style="font-weight:600;color:var(--navy);">Do you also offer GCSE tuition?</button></h2><div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body" style="color:var(--text-muted);">Yes — our Year 9 GCSE transition programme blends KS3 completion with early GCSE preparation. For Year 10 and 11 students needing GCSE-level tuition, please contact us directly as availability is limited.</div></div></div>
      </div>
    </div></div></div>
  </section>

  <?php
  $star_carousel_tags = ['ks3'];
  require_once 'includes/star-carousel.php';
  ?>

  <?php render_testimonials_section($course_tag, 'What <span>KS3 Parents Say</span>', 0, false); ?>

  <section class="cta-section"><div class="container text-center position-relative"><div data-aos="fade-up">
    <h2>Build the <span style="color:var(--gold);">GCSE Foundations Now</span></h2>
    <p>Book a free assessment today and give your secondary school child the structured support they need to thrive.</p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="#enquire" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Assessment</a>
      <a href="tel:<?= PHONE ?>" class="btn-secondary-tpa"><i class="fas fa-phone-alt"></i> Call <?= PHONE ?></a>
    </div>
  </div></div></section>

<?php require_once 'includes/footer.php'; ?>
