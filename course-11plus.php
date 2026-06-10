<?php
$page_title = '11 Plus Preparation Programme';
$meta_description = '11 Plus preparation at Talent Pool Academy — small group classes, VR, NVR, Maths & English, mock exams, and a 90%+ grammar school pass rate. Chadwell Heath and Chelmsford.';
$_locations = [
  ['@type'=>'CourseInstance','courseMode'=>'onsite','location'=>['@type'=>'Place','name'=>'Chadwell Heath Centre','address'=>['@type'=>'PostalAddress','streetAddress'=>'60 High Road, Chadwell Heath','addressLocality'=>'Romford','postalCode'=>'RM6 6PP','addressCountry'=>'GB']]],
  ['@type'=>'CourseInstance','courseMode'=>'onsite','location'=>['@type'=>'Place','name'=>'Chelmsford Centre','address'=>['@type'=>'PostalAddress','streetAddress'=>'4B Corporation Road','addressLocality'=>'Chelmsford','postalCode'=>'CM1 2AR','addressCountry'=>'GB']]],
  ['@type'=>'CourseInstance','courseMode'=>'online'],
];
$schema_extra = '<script type="application/ld+json">' . json_encode([
  '@context'=>'https://schema.org','@type'=>'Course',
  'name'=>'11 Plus Preparation Programme',
  'description'=>'Comprehensive 11 Plus preparation for Grammar and Independent School entry. Covering Verbal Reasoning, Non-Verbal Reasoning, Maths and English with mock exams. Small group classes.',
  'url'=>'https://www.talentpoolacademy.com/course-11plus.php',
  'provider'=>['@type'=>'EducationalOrganization','name'=>'Talent Pool Academy','url'=>'https://www.talentpoolacademy.com'],
  'educationalLevel'=>'Primary (Year 3–6, ages 7–11)',
  'courseMode'=>['onsite','online'],
  'teaches'=>['Verbal Reasoning','Non-Verbal Reasoning','Mathematics','English','Mock Exam Technique'],
  'hasCourseInstance'=>$_locations,
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . '</script>';
$schema_extra .= '<script type="application/ld+json">' . json_encode([
  '@context'=>'https://schema.org','@type'=>'FAQPage',
  'mainEntity'=>[
    ['@type'=>'Question','name'=>'When should my child start 11 Plus preparation?','acceptedAnswer'=>['@type'=>'Answer','text'=>'Ideally, students should start in Year 4 (age 8–9) to give them 2 full years of preparation. However, we have helped students who started in Year 5 and even Year 6 achieve excellent results through our intensive programmes.']],
    ['@type'=>'Question','name'=>'Which grammar schools do you help with?','acceptedAnswer'=>['@type'=>'Answer','text'=>'We prepare students for all Essex and East London grammar schools including Ilford County High, Westcliff High, Chelmsford County High, King Edward VI, Colchester Royal Grammar, and others. We tailor preparation to the specific exam format of your target school.']],
    ['@type'=>'Question','name'=>'What does the free assessment involve?','acceptedAnswer'=>['@type'=>'Answer','text'=>"It's a 60-minute diagnostic session covering VR, NVR, Maths and English. We then provide a full written report identifying strengths, gaps, and recommended learning plan. There's no cost and no obligation to enrol."]],
    ['@type'=>'Question','name'=>'How many hours of tutoring does a child typically need?','acceptedAnswer'=>['@type'=>'Answer','text'=>'We recommend 1–2 sessions per week with 30–60 minutes of independent practice at home between sessions. Students starting in Year 4 typically have 18–24 months of preparation, while Year 6 students follow an accelerated plan.']],
  ],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . '</script>';
require_once 'includes/feedback-data.php';
require_once 'includes/star-students-data.php';
require_once 'includes/header.php';
$course_tag = '11plus';
?>

  <!-- ================================================
       COURSE HERO
       ================================================ -->
  <section class="course-hero">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-7 course-hero-content">
          <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb" style="background:none;padding:0;"><li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php" style="color:rgba(255,255,255,0.6);">Home</a></li><li class="breadcrumb-item"><a href="<?= SITE_URL ?>/courses.php" style="color:rgba(255,255,255,0.6);">Courses</a></li><li class="breadcrumb-item active" style="color:var(--gold);">11 Plus</li></ol></nav>
          <div class="section-tag mb-3" style="background:rgba(245,166,35,0.15);border-color:rgba(245,166,35,0.3);color:var(--gold);"><i class="fas fa-star"></i> Most Popular Programme</div>
          <h1>11 Plus <span style="color:var(--gold);">Preparation Programme</span></h1>
          <p class="course-hero-desc">Comprehensive preparation for <strong style="color:var(--gold);">Grammar School &amp; Independent School</strong> entry — small group classes, expert teaching, VR, NVR, Maths and English, plus regular mock exams. Trusted by families across Essex since 2008.</p>
          <div class="course-meta-bar">
            <div class="course-meta-item"><i class="fas fa-graduation-cap"></i> Year 3 – Year 6</div>
            <div class="course-meta-item"><i class="fas fa-users"></i> Max 5–6 per class</div>
            <div class="course-meta-item"><i class="fas fa-calendar-alt"></i> Enrol anytime</div>
            <div class="course-meta-item"><i class="fas fa-laptop"></i> Online &amp; in-centre</div>
          </div>
          <div class="d-flex gap-3 flex-wrap">
            <a href="#enquire" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Assessment</a>
            <a href="#curriculum" class="btn-secondary-tpa"><i class="fas fa-book-open"></i> View Curriculum</a>
          </div>
          <div class="trust-badges mt-4">
            <div class="trust-badge"><i class="fas fa-trophy"></i> 90%+ Pass Rate 2024</div>
            <div class="trust-badge"><i class="fas fa-check"></i> DBS Checked Teachers</div>
            <div class="trust-badge"><i class="fas fa-check"></i> Mock Exams Included</div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="course-sidebar-card" id="enquire" style="position:relative;top:auto;">
            <div class="course-sidebar-header">
              <h4>Book a Free Assessment</h4>
              <p>No obligation · Results within 48 hours</p>
            </div>
            <div class="course-sidebar-body">
              <div class="urgency-strip">🔥 Only 3 places remaining this term</div>
              <form id="landingForm" class="tpa-enquiry-form" novalidate>
                <input type="hidden" name="source"  value="Website - 11 Plus">
                <input type="hidden" name="subject" value="11 Plus Preparation">
                <div class="mb-3"><label class="form-label-tpa" for="lf-child">Child's Name *</label><input type="text" id="lf-child" name="child_name" class="form-control-tpa" placeholder="Child's full name" required></div>
                <div class="mb-3"><label class="form-label-tpa" for="lf-year">Child's Year Group *</label><select id="lf-year" name="year_group" class="form-control-tpa" required style="appearance:auto;"><option value="">Select year group</option><option>Year 4</option><option>Year 5</option><option>Year 6</option></select></div>
                <div class="mb-3"><label class="form-label-tpa" for="lf-phone">Phone Number *</label><input type="tel" id="lf-phone" name="phone" class="form-control-tpa" placeholder="07xxx xxxxxx" required autocomplete="tel"></div>
                <div class="mb-3"><label class="form-label-tpa" for="lf-email">Email Address *</label><input type="email" id="lf-email" name="email" class="form-control-tpa" placeholder="email@example.com" required autocomplete="email"></div>
                <div class="mb-4"><label class="form-label-tpa" for="lf-centre">Preferred Centre</label><select id="lf-centre" name="centre" class="form-control-tpa" style="appearance:auto;"><option>Chadwell Heath (RM6 6PP)</option><option>Chelmsford (CM1 2AR)</option><option>Online</option><option>No preference</option></select></div>
                <button type="submit" class="btn-primary-tpa w-100" style="justify-content:center;font-size:1rem;"><i class="fas fa-calendar-check me-2"></i>Reserve My Free Place</button>
              </form>
              <div class="text-center mt-3" style="font-size:.8rem;color:var(--text-muted);"><i class="fas fa-lock me-1"></i>Your details are safe with us · No spam</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- STATS -->
  <section class="stats-bar">
    <div class="container">
      <div class="row g-4 text-center">
        <div class="col-6 col-md-3 stat-item"><span class="stat-number">90%</span><span class="stat-number">+</span><div class="stat-label">Grammar School Pass Rate</div></div>
        <div class="col-6 col-md-3 stat-item"><span class="stat-number">16</span><span class="stat-number">+</span><div class="stat-label">Years Teaching 11 Plus</div></div>
        <div class="col-6 col-md-3 stat-item"><span class="stat-number">8</span><div class="stat-label">Max Students per Class</div></div>
        <div class="col-6 col-md-3 stat-item"><span class="stat-number">500</span><span class="stat-number">+</span><div class="stat-label">11+ Students Placed</div></div>
      </div>
    </div>
  </section>

  <!-- WHAT'S INCLUDED -->
  <section class="section-pad" id="included">
    <div class="container">
      <div class="row g-5 align-items-start">
        <div class="col-lg-7" data-aos="fade-right">
          <div class="section-tag mb-3"><i class="fas fa-list-check"></i> What's Included</div>
          <h2 class="section-title" style="font-size:2rem;">Everything Your Child Needs to <span>Pass the 11 Plus</span></h2>
          <div class="divider-gold" style="margin:0 0 2rem;"></div>
          <ul class="included-list">
            <li><span><strong style="color:var(--navy);">Verbal Reasoning (VR)</strong> — All 21 question types, including codes, word patterns, and logic puzzles.</span></li>
            <li><span><strong style="color:var(--navy);">Non-Verbal Reasoning (NVR)</strong> — Shapes, patterns, matrices, and spatial reasoning from the ground up.</span></li>
            <li><span><strong style="color:var(--navy);">Maths</strong> — Full Year 5 &amp; 6 curriculum plus advanced topics that appear in 11+ papers.</span></li>
            <li><span><strong style="color:var(--navy);">English &amp; Comprehension</strong> — Reading comprehension, vocabulary, grammar, and creative writing.</span></li>
            <li><span><strong style="color:var(--navy);">Monthly Mock Exams</strong> — Full-length, timed mock exams under exam conditions with detailed analysis.</span></li>
            <li><span><strong style="color:var(--navy);">Bespoke Learning Plans</strong> — Personal targets set for each child based on diagnostic results and progress.</span></li>
            <li><span><strong style="color:var(--navy);">Parent Progress Reports</strong> — Regular updates so you always know where your child stands.</span></li>
            <li><span><strong style="color:var(--navy);">TPA Workbook Resources</strong> — Our own in-house written practice books included in tuition.</span></li>
            <li><span><strong style="color:var(--navy);">Interview Preparation</strong> — Guidance for selective schools that include interviews or written assessments.</span></li>
            <li><span><strong style="color:var(--navy);">School Selection Advice</strong> — Expert guidance on which grammar schools to target based on ability and location.</span></li>
          </ul>
        </div>
        <div class="col-lg-5" data-aos="fade-left">
          <div style="background:var(--off-white);border-radius:var(--radius-lg);padding:2rem;border:1px solid rgba(10,22,40,0.07);">
            <h5 style="font-weight:700;color:var(--navy);margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:2px solid var(--gold);">Course Details</h5>
            <table style="width:100%;font-size:.92rem;">
              <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Year Groups</td><td style="font-weight:700;color:var(--navy);text-align:right;">Year 3, 4, 5 &amp; 6</td></tr>
              <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Class Size</td><td style="font-weight:700;color:var(--navy);text-align:right;">Max 5–6 students</td></tr>
              <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Sessions</td><td style="font-weight:700;color:var(--navy);text-align:right;">Weekly (Sat &amp; Sun)</td></tr>
              <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Duration</td><td style="font-weight:700;color:var(--navy);text-align:right;">2 hours per session</td></tr>
              <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Locations</td><td style="font-weight:700;color:var(--navy);text-align:right;">Chadwell Heath · Chelmsford · Online</td></tr>
              <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Start Date</td><td style="font-weight:700;color:var(--navy);text-align:right;">Rolling enrolment</td></tr>
              <tr><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Assessment</td><td style="font-weight:700;color:var(--gold);text-align:right;">FREE (No obligation)</td></tr>
            </table>
            <a href="#enquire" class="btn-primary-tpa w-100 mt-3" style="justify-content:center;"><i class="fas fa-calendar-check me-2"></i>Book Free Assessment</a>
          </div>
          <div style="background:var(--navy);border-radius:var(--radius-lg);padding:1.5rem;margin-top:1rem;text-align:center;">
            <div style="color:var(--gold);font-size:1.5rem;margin-bottom:.5rem;"><i class="fas fa-phone-alt"></i></div>
            <div style="color:white;font-weight:700;margin-bottom:.25rem;">Prefer to call?</div>
            <a href="tel:<?= PHONE ?>" style="color:var(--gold);font-size:1.15rem;font-weight:800;"><?= PHONE ?></a>
            <div style="color:rgba(255,255,255,0.6);font-size:.82rem;margin-top:.3rem;">Mon–Sat 9am–6pm</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CURRICULUM -->
  <section class="section-pad section-bg" id="curriculum">
    <div class="container">
      <div class="text-center mb-5" data-aos="fade-up">
        <div class="section-tag"><i class="fas fa-book-open"></i> Curriculum</div>
        <h2 class="section-title">What We <span>Cover Each Term</span></h2>
        <div class="divider-gold"></div>
      </div>
      <div class="row g-5">
        <div class="col-lg-6" data-aos="fade-right">
          <h5 style="font-weight:700;color:var(--navy);margin-bottom:1rem;"><i class="fas fa-brain text-gold me-2"></i>Verbal &amp; Non-Verbal Reasoning</h5>
          <div style="border-radius:var(--radius-md);overflow:hidden;box-shadow:var(--shadow-sm);">
            <table class="curriculum-table">
              <thead><tr><th>Term</th><th>VR Topics</th><th>NVR Topics</th></tr></thead>
              <tbody>
                <tr><td>Term 1</td><td>Letter codes, Word relationships</td><td>Sequences, Odd one out</td></tr>
                <tr><td>Term 2</td><td>Anagrams, Missing words</td><td>Nets, Reflections</td></tr>
                <tr><td>Term 3</td><td>Analogies, Compound words</td><td>Matrices, Rotations</td></tr>
                <tr><td>Term 4</td><td>Mixed revision, timed practice</td><td>Full mixed practice tests</td></tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-lg-6" data-aos="fade-left">
          <h5 style="font-weight:700;color:var(--navy);margin-bottom:1rem;"><i class="fas fa-calculator text-gold me-2"></i>Maths &amp; English</h5>
          <div style="border-radius:var(--radius-md);overflow:hidden;box-shadow:var(--shadow-sm);">
            <table class="curriculum-table">
              <thead><tr><th>Term</th><th>Maths Focus</th><th>English Focus</th></tr></thead>
              <tbody>
                <tr><td>Term 1</td><td>Number operations, Fractions</td><td>Comprehension strategies</td></tr>
                <tr><td>Term 2</td><td>Ratio, Algebra intro</td><td>Vocabulary &amp; word types</td></tr>
                <tr><td>Term 3</td><td>Geometry, Measurement</td><td>Creative writing techniques</td></tr>
                <tr><td>Term 4</td><td>Data handling, Problem solving</td><td>Grammar, Punctuation, SPaG</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php
  $star_carousel_tags     = ['11plus'];
  $star_carousel_title    = 'Our <span>11 Plus Star Students</span>';
  $star_carousel_subtitle = 'These incredible students secured grammar school places through our 11 Plus programme — we couldn\'t be prouder!';
  require_once 'includes/star-carousel.php';
  ?>

  <?php render_testimonials_section($course_tag, 'What <span>11 Plus Parents Say</span>'); ?>

  <!-- FAQ -->
  <section class="section-pad" id="faq">
    <div class="container">
      <div class="text-center mb-5" data-aos="fade-up">
        <div class="section-tag"><i class="fas fa-question-circle"></i> FAQ</div>
        <h2 class="section-title">Frequently Asked <span>Questions</span></h2>
        <div class="divider-gold"></div>
      </div>
      <div class="row justify-content-center">
        <div class="col-lg-8" data-aos="fade-up">
          <div class="accordion" id="faqAccordion">
            <div class="accordion-item" style="border-radius:var(--radius-md);border:1px solid rgba(10,22,40,0.08);margin-bottom:1rem;overflow:hidden;"><h2 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" style="font-weight:600;color:var(--navy);">When should my child start 11 Plus preparation?</button></h2><div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion"><div class="accordion-body" style="color:var(--text-muted);">Ideally, students should start in Year 4 (age 8–9) to give them 2 full years of preparation. However, we have helped students who started in Year 5 and even Year 6 achieve excellent results through our intensive programmes.</div></div></div>
            <div class="accordion-item" style="border-radius:var(--radius-md);border:1px solid rgba(10,22,40,0.08);margin-bottom:1rem;overflow:hidden;"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" style="font-weight:600;color:var(--navy);">Which grammar schools do you help with?</button></h2><div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body" style="color:var(--text-muted);">We prepare students for all Essex and East London grammar schools including Ilford County High, Westcliff High, Chelmsford County High, King Edward VI, Colchester Royal Grammar, and others. We tailor preparation to the specific exam format of your target school.</div></div></div>
            <div class="accordion-item" style="border-radius:var(--radius-md);border:1px solid rgba(10,22,40,0.08);margin-bottom:1rem;overflow:hidden;"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" style="font-weight:600;color:var(--navy);">What does the free assessment involve?</button></h2><div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body" style="color:var(--text-muted);">It's a 60-minute diagnostic session covering VR, NVR, Maths and English. We then provide a full written report identifying strengths, gaps, and recommended learning plan. There's no cost and no obligation to enrol.</div></div></div>
            <div class="accordion-item" style="border-radius:var(--radius-md);border:1px solid rgba(10,22,40,0.08);overflow:hidden;"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" style="font-weight:600;color:var(--navy);">How many hours of tutoring does a child typically need?</button></h2><div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body" style="color:var(--text-muted);">We recommend 1–2 sessions per week with 30–60 minutes of independent practice at home between sessions. Students starting in Year 4 typically have 18–24 months of preparation, while Year 6 students follow an accelerated plan.</div></div></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta-section">
    <div class="container text-center position-relative">
      <div data-aos="fade-up">
        <h2>Secure Your Child's <span style="color:var(--gold);">Grammar School Place</span></h2>
        <p>Book a free diagnostic assessment today. Our expert team will map out a personalised plan to get your child into their dream school.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
          <a href="#enquire" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Assessment</a>
          <a href="tel:<?= PHONE ?>" class="btn-secondary-tpa"><i class="fas fa-phone-alt"></i> Call <?= PHONE ?></a>
        </div>
      </div>
    </div>
  </section>

<?php require_once 'includes/footer.php'; ?>
