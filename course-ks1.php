<?php
$page_title = 'Key Stage 1 Tuition (Year 1–2)';
$meta_description = 'Expert phonics, reading, writing and early maths tuition for Year 1 and Year 2 children at Talent Pool Academy. Chadwell Heath & Chelmsford.';
$schema_extra = '<script type="application/ld+json">' . json_encode([
  '@context'=>'https://schema.org','@type'=>'Course',
  'name'=>'Key Stage 1 Tuition (Year 1–2)',
  'description'=>'Phonics, reading, early writing and core maths for Year 1 and Year 2. Building essential literacy and numeracy foundations in a nurturing small-class environment.',
  'url'=>'https://www.talentpoolacademy.com/course-ks1.php',
  'provider'=>['@type'=>'EducationalOrganization','name'=>'Talent Pool Academy','url'=>'https://www.talentpoolacademy.com'],
  'educationalLevel'=>'Key Stage 1 (Year 1–2, ages 5–7)',
  'courseMode'=>['onsite','online'],
  'teaches'=>['Phonics','Early Reading','Early Writing','Early Maths'],
  'hasCourseInstance'=>[
    ['@type'=>'CourseInstance','courseMode'=>'onsite','location'=>['@type'=>'Place','name'=>'Chadwell Heath','address'=>['@type'=>'PostalAddress','addressLocality'=>'Romford','postalCode'=>'RM6 6PP','addressCountry'=>'GB']]],
    ['@type'=>'CourseInstance','courseMode'=>'onsite','location'=>['@type'=>'Place','name'=>'Chelmsford','address'=>['@type'=>'PostalAddress','addressLocality'=>'Chelmsford','postalCode'=>'CM1 2AR','addressCountry'=>'GB']]],
    ['@type'=>'CourseInstance','courseMode'=>'online'],
  ],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . '</script>';
require_once 'includes/feedback-data.php';
require_once 'includes/header.php';
$course_tag = 'ks1';
?>

  <section class="course-hero">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-7 course-hero-content">
          <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb" style="background:none;padding:0;"><li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php" style="color:rgba(255,255,255,0.6);">Home</a></li><li class="breadcrumb-item active" style="color:var(--gold);">KS1 Tuition</li></ol></nav>
          <div class="section-tag mb-3" style="background:rgba(46,125,50,0.2);border-color:rgba(46,125,50,0.4);color:#a5d6a7;"><i class="fas fa-book"></i> Year 1 &amp; Year 2</div>
          <h1>Key Stage 1 <span style="color:var(--gold);">Tuition Programme</span></h1>
          <p class="course-hero-desc">Building the essential literacy and numeracy foundations in Year 1 and Year 2 — phonics, reading, early writing, and core maths concepts, in a warm, supportive environment.</p>
          <div class="course-meta-bar">
            <div class="course-meta-item"><i class="fas fa-graduation-cap"></i> Year 1 &amp; Year 2</div>
            <div class="course-meta-item"><i class="fas fa-users"></i> Max 5–6 per class</div>
            <div class="course-meta-item"><i class="fas fa-smile"></i> Fun, nurturing approach</div>
            <div class="course-meta-item"><i class="fas fa-laptop"></i> Online available</div>
          </div>
          <div class="d-flex gap-3 flex-wrap">
            <a href="#enquire" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Assessment</a>
            <a href="#included" class="btn-secondary-tpa"><i class="fas fa-book-open"></i> What We Teach</a>
          </div>
          <div class="trust-badges mt-4">
            <div class="trust-badge"><i class="fas fa-check"></i> Phonics-first approach</div>
            <div class="trust-badge"><i class="fas fa-check"></i> KS1 curriculum aligned</div>
            <div class="trust-badge"><i class="fas fa-heart"></i> Nurturing teachers</div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="course-sidebar-card" id="enquire" style="position:relative;top:auto;">
            <div class="course-sidebar-header"><h4>Book a Free Assessment</h4><p>No obligation · Child-friendly session</p></div>
            <div class="course-sidebar-body">
              <form id="ks1Form" class="tpa-enquiry-form" novalidate>
                <input type="hidden" name="source"  value="Website - KS1">
                <input type="hidden" name="subject" value="KS1 Tuition">
                <div class="mb-3"><label class="form-label-tpa" for="ks1-child">Child's Name *</label><input type="text" id="ks1-child" name="child_name" class="form-control-tpa" placeholder="Child's full name" required></div>
                <div class="mb-3"><label class="form-label-tpa" for="ks1-year">Year Group *</label><select id="ks1-year" name="year_group" class="form-control-tpa" required style="appearance:auto;"><option value="">Select year</option><option>Year 1</option><option>Year 2</option></select></div>
                <div class="mb-3"><label class="form-label-tpa" for="ks1-phone">Phone *</label><input type="tel" id="ks1-phone" name="phone" class="form-control-tpa" placeholder="07xxx xxxxxx" required autocomplete="tel"></div>
                <div class="mb-3"><label class="form-label-tpa" for="ks1-email">Email *</label><input type="email" id="ks1-email" name="email" class="form-control-tpa" placeholder="email@example.com" required autocomplete="email"></div>
                <div class="mb-4"><label class="form-label-tpa" for="ks1-centre">Centre</label><select id="ks1-centre" name="centre" class="form-control-tpa" style="appearance:auto;"><option>Chadwell Heath</option><option>Chelmsford</option><option>Online</option></select></div>
                <button type="submit" class="btn-primary-tpa w-100" style="justify-content:center;"><i class="fas fa-calendar-check me-2"></i>Reserve Free Place</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section-pad" id="included">
    <div class="container">
      <div class="row g-5 align-items-start">
        <div class="col-lg-7" data-aos="fade-right">
          <div class="section-tag mb-3"><i class="fas fa-list-check"></i> What We Teach</div>
          <h2 class="section-title" style="font-size:2rem;">Building <span>Strong Foundations</span> in Year 1 &amp; 2</h2>
          <div class="divider-gold" style="margin:0 0 2rem;"></div>
          <ul class="included-list">
            <li><span><strong style="color:var(--navy);">Phonics &amp; Phonemic Awareness</strong> — Phase 3–6 phonics, blending, segmenting, and common exception words.</span></li>
            <li><span><strong style="color:var(--navy);">Early Reading</strong> — Decoding strategies, comprehension skills, and developing a love of reading.</span></li>
            <li><span><strong style="color:var(--navy);">Handwriting &amp; Writing</strong> — Letter formation, sentence structure, punctuation, and creative writing starters.</span></li>
            <li><span><strong style="color:var(--navy);">Number &amp; Counting</strong> — Place value, addition, subtraction, number bonds, and multiplication introduction.</span></li>
            <li><span><strong style="color:var(--navy);">Shapes &amp; Measurement</strong> — 2D/3D shapes, measuring length, weight, and telling the time.</span></li>
            <li><span><strong style="color:var(--navy);">Fun Problem Solving</strong> — Age-appropriate puzzles and activities to build mathematical thinking.</span></li>
            <li><span><strong style="color:var(--navy);">Parent Progress Reports</strong> — Regular updates with recommended home activities.</span></li>
          </ul>
        </div>
        <div class="col-lg-5" data-aos="fade-left">
          <div style="background:var(--off-white);border-radius:var(--radius-lg);padding:2rem;border:1px solid rgba(10,22,40,0.07);">
            <h5 style="font-weight:700;color:var(--navy);margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:2px solid var(--gold);">Course Details</h5>
            <table style="width:100%;font-size:.92rem;">
              <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Year Groups</td><td style="font-weight:700;color:var(--navy);text-align:right;">Year 1 &amp; 2</td></tr>
              <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Class Size</td><td style="font-weight:700;color:var(--navy);text-align:right;">Max 5–6 students</td></tr>
              <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Sessions</td><td style="font-weight:700;color:var(--navy);text-align:right;">Sat &amp; Sun mornings</td></tr>
              <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Duration</td><td style="font-weight:700;color:var(--navy);text-align:right;">90 minutes per session</td></tr>
              <tr><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Assessment</td><td style="font-weight:700;color:var(--gold);text-align:right;">FREE (friendly &amp; relaxed)</td></tr>
            </table>
            <a href="#enquire" class="btn-primary-tpa w-100 mt-3" style="justify-content:center;"><i class="fas fa-calendar-check me-2"></i>Book Free Assessment</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php
  $star_carousel_tags = ['ks1'];
  require_once 'includes/star-carousel.php';
  ?>

  <?php render_testimonials_section($course_tag, 'What <span>KS1 Parents Say</span>'); ?>

  <section class="cta-section">
    <div class="container text-center position-relative"><div data-aos="fade-up">
      <h2>Give Your Child the <span style="color:var(--gold);">Best Start in School</span></h2>
      <p>Book a free, friendly assessment and discover how TPA can build your child's confidence and love of learning.</p>
      <div class="d-flex gap-3 justify-content-center flex-wrap">
        <a href="#enquire" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Assessment</a>
        <a href="tel:<?= PHONE ?>" class="btn-secondary-tpa"><i class="fas fa-phone-alt"></i> Call <?= PHONE ?></a>
      </div>
    </div></div>
  </section>

<?php require_once 'includes/footer.php'; ?>
