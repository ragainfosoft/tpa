<?php
$page_title = 'Adult Learning';
$meta_description = 'Adult learning programmes at Talent Pool Academy. Functional Skills Maths & English, literacy, numeracy and professional development in Chadwell Heath and Chelmsford.';
$schema_extra = '<script type="application/ld+json">' . json_encode([
  '@context'=>'https://schema.org','@type'=>'Course',
  'name'=>'Adult Learning Programme',
  'description'=>'Functional Skills Maths and English, literacy, numeracy and professional development courses for adults. Flexible scheduling, supportive environment, accredited qualifications.',
  'url'=>'https://www.talentpoolacademy.com/course-adult.php',
  'provider'=>['@type'=>'EducationalOrganization','name'=>'Talent Pool Academy','url'=>'https://www.talentpoolacademy.com'],
  'educationalLevel'=>'Adult / Further Education',
  'courseMode'=>['onsite','online'],
  'teaches'=>['Functional Skills Maths','Functional Skills English','Literacy','Numeracy','Professional Development'],
  'audience'=>['@type'=>'EducationalAudience','educationalRole'=>'adult learner'],
  'hasCourseInstance'=>[
    ['@type'=>'CourseInstance','courseMode'=>'onsite','location'=>['@type'=>'Place','name'=>'Chadwell Heath','address'=>['@type'=>'PostalAddress','addressLocality'=>'Romford','postalCode'=>'RM6 6PP','addressCountry'=>'GB']]],
    ['@type'=>'CourseInstance','courseMode'=>'onsite','location'=>['@type'=>'Place','name'=>'Chelmsford','address'=>['@type'=>'PostalAddress','addressLocality'=>'Chelmsford','postalCode'=>'CM1 2AR','addressCountry'=>'GB']]],
    ['@type'=>'CourseInstance','courseMode'=>'online'],
  ],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . '</script>';
require_once 'includes/feedback-data.php';
require_once 'includes/header.php';
$course_tag = 'adult';
?>

  <section class="course-hero">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-7 course-hero-content">
          <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb" style="background:none;padding:0;"><li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php" style="color:rgba(255,255,255,0.6);">Home</a></li><li class="breadcrumb-item"><a href="<?= SITE_URL ?>/courses.php" style="color:rgba(255,255,255,0.6);">Courses</a></li><li class="breadcrumb-item active" style="color:var(--gold);">Adult Learning</li></ol></nav>
          <div class="section-tag mb-3" style="background:rgba(183,28,28,0.25);border-color:rgba(229,115,115,0.5);color:#ef9a9a;"><i class="fas fa-user-graduate"></i> Adults &amp; Professionals</div>
          <h1>Adult <span style="color:var(--gold);">Learning Programme</span></h1>
          <p class="course-hero-desc">It is never too late to learn. Our adult learning programmes offer flexible, supportive and accessible tuition in Maths, English, Functional Skills and more — helping adults build confidence, gain qualifications, and achieve their personal and professional goals.</p>
          <div class="course-meta-bar">
            <div class="course-meta-item"><i class="fas fa-user"></i> Adults &amp; Professionals</div>
            <div class="course-meta-item"><i class="fas fa-users"></i> Small &amp; 1-to-1 sessions</div>
            <div class="course-meta-item"><i class="fas fa-book"></i> Maths · English · Functional Skills</div>
            <div class="course-meta-item"><i class="fas fa-laptop"></i> Online available</div>
          </div>
          <div class="d-flex gap-3 flex-wrap">
            <a href="#enquire" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Consultation</a>
            <a href="#included" class="btn-secondary-tpa"><i class="fas fa-book-open"></i> What We Offer</a>
          </div>
          <div class="trust-badges mt-4">
            <div class="trust-badge"><i class="fas fa-check"></i> Inclusive &amp; welcoming</div>
            <div class="trust-badge"><i class="fas fa-check"></i> Flexible scheduling</div>
            <div class="trust-badge"><i class="fas fa-check"></i> DBS-checked tutors</div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="course-sidebar-card" id="enquire" style="position:relative;top:auto;">
            <div class="course-sidebar-header"><h4>Book a Free Consultation</h4><p>Tell us your goals — we'll find the right path</p></div>
            <div class="course-sidebar-body">
              <div class="urgency-strip">🌱 Adult Learning — Now Available!</div>
              <form class="tpa-enquiry-form" novalidate>
                <input type="hidden" name="source"  value="Website - Adult Learning">
                <input type="hidden" name="subject" value="Adult Learning">
                <div class="mb-3"><label class="form-label-tpa" for="adl-name">Your Name *</label><input type="text" id="adl-name" name="name" class="form-control-tpa" placeholder="Your full name" required autocomplete="name"></div>
                <div class="mb-3"><label class="form-label-tpa" for="adl-phone">Phone *</label><input type="tel" id="adl-phone" name="phone" class="form-control-tpa" placeholder="07xxx xxxxxx" required autocomplete="tel"></div>
                <div class="mb-3"><label class="form-label-tpa" for="adl-email">Email *</label><input type="email" id="adl-email" name="email" class="form-control-tpa" placeholder="email@example.com" required autocomplete="email"></div>
                <div class="mb-3"><label class="form-label-tpa" for="adl-subj">Area of Interest</label><select id="adl-subj" name="subject_detail" class="form-control-tpa" style="appearance:auto;">
                  <option>Functional Skills Maths (Level 1 or 2)</option>
                  <option>Functional Skills English (Level 1 or 2)</option>
                  <option>GCSE Maths (adult resit)</option>
                  <option>GCSE English (adult resit)</option>
                  <option>General Literacy / Numeracy</option>
                  <option>Other — please advise</option>
                </select></div>
                <div class="mb-4"><label class="form-label-tpa" for="adl-centre">Preferred Location</label><select id="adl-centre" name="centre" class="form-control-tpa" style="appearance:auto;"><option>Chadwell Heath</option><option>Chelmsford</option><option>Online</option></select></div>
                <button type="submit" class="btn-primary-tpa w-100" style="justify-content:center;"><i class="fas fa-calendar-check me-2"></i>Request Consultation</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="stats-bar"><div class="container"><div class="row g-4 text-center">
    <div class="col-6 col-md-3 stat-item"><div class="stat-value"><span class="stat-number">5</span><span class="stat-suffix">+</span></div><div class="stat-label">Programmes Available</div></div>
    <div class="col-6 col-md-3 stat-item"><div class="stat-value"><span class="stat-number">16</span><span class="stat-suffix">+</span></div><div class="stat-label">Years' Experience</div></div>
    <div class="col-6 col-md-3 stat-item"><div class="stat-value"><span class="stat-number">2</span></div><div class="stat-label">Centre Locations</div></div>
    <div class="col-6 col-md-3 stat-item"><div class="stat-value"><span class="stat-number">100</span><span class="stat-suffix">%</span></div><div class="stat-label">Inclusive Approach</div></div>
  </div></div></section>

  <section class="section-pad" id="included">
    <div class="container"><div class="row g-5 align-items-start">
      <div class="col-lg-7" data-aos="fade-right">
        <div class="section-tag mb-3"><i class="fas fa-list-check"></i> What We Offer</div>
        <h2 class="section-title" style="font-size:2rem;">Adult Learning <span>Programmes</span></h2>
        <div class="divider-gold" style="margin:0 0 2rem;"></div>
        <ul class="included-list">
          <li><span><strong style="color:var(--navy);">Functional Skills Maths (Level 1 &amp; 2)</strong> — Nationally recognised qualification covering everyday maths: fractions, percentages, ratios, data handling and problem-solving.</span></li>
          <li><span><strong style="color:var(--navy);">Functional Skills English (Level 1 &amp; 2)</strong> — Reading comprehension, writing for different purposes, and speaking &amp; listening skills for real-life situations.</span></li>
          <li><span><strong style="color:var(--navy);">GCSE Maths (Adult Resit)</strong> — Full GCSE Maths preparation for adults resat through exam centres — structured, supportive and achievable.</span></li>
          <li><span><strong style="color:var(--navy);">GCSE English (Adult Resit)</strong> — GCSE English Language and Literature for adults — with flexible scheduling around work commitments.</span></li>
          <li><span><strong style="color:var(--navy);">General Numeracy &amp; Literacy</strong> — Non-qualification sessions to build everyday confidence with numbers and written communication.</span></li>
          <li><span><strong style="color:var(--navy);">1-to-1 Tuition</strong> — Completely personalised sessions tailored to your specific goals, pace and availability.</span></li>
        </ul>

        <div style="background:var(--gold-pale);border:1.5px solid rgba(245,166,35,0.35);border-radius:var(--radius-md);padding:1.3rem 1.5rem;margin-top:1.5rem;">
          <h6 style="font-weight:700;color:var(--navy);margin-bottom:.6rem;"><i class="fas fa-hand-holding-heart text-gold me-2"></i>Our Inclusive Promise</h6>
          <p style="font-size:.9rem;color:var(--text-muted);margin:0;">Talent Pool Academy welcomes all adult learners regardless of background, ability or learning need. We are fully inclusive and support learners with Dyslexia, Autism, ADHD, and other learning differences. No one is turned away — every learner matters.</p>
        </div>
      </div>
      <div class="col-lg-5" data-aos="fade-left">
        <div style="background:var(--off-white);border-radius:var(--radius-lg);padding:2rem;border:1px solid rgba(10,22,40,0.07);">
          <h5 style="font-weight:700;color:var(--navy);margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:2px solid var(--gold);">Programme Details</h5>
          <table style="width:100%;font-size:.92rem;">
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Who Is It For?</td><td style="font-weight:700;color:var(--navy);text-align:right;">Adults 18+</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Session Format</td><td style="font-weight:700;color:var(--navy);text-align:right;">Group &amp; 1-to-1</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Scheduling</td><td style="font-weight:700;color:var(--navy);text-align:right;">Flexible — evenings &amp; weekends</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Qualifications</td><td style="font-weight:700;color:var(--navy);text-align:right;">Functional Skills · GCSE</td></tr>
            <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Locations</td><td style="font-weight:700;color:var(--navy);text-align:right;">Chadwell Heath, Chelmsford, Online</td></tr>
            <tr><td style="padding:.65rem 0;color:var(--text-muted);font-weight:600;">Inclusion</td><td style="font-weight:700;color:var(--navy);text-align:right;">All abilities welcome</td></tr>
          </table>
        </div>

        <div class="mt-4" style="background:var(--navy);border-radius:var(--radius-lg);padding:1.75rem;color:var(--white);">
          <h6 style="color:var(--gold);font-weight:700;margin-bottom:.75rem;"><i class="fas fa-quote-left me-2"></i>Why It Matters</h6>
          <p style="font-size:.9rem;color:rgba(255,255,255,0.8);margin:0;">"Learning doesn't stop at 18. Whether you need a qualification for your career, want to support your child's learning, or simply want to grow — we are here to help every step of the way."</p>
          <div style="margin-top:1rem;font-size:.82rem;color:var(--gold);font-weight:600;">— Mrs Meena Kumar, Founder</div>
        </div>
      </div>
    </div></div>
  </section>

  <!-- WHY TPA FOR ADULT LEARNING -->
  <section class="section-pad section-bg">
    <div class="container">
      <div class="text-center mb-5" data-aos="fade-up">
        <div class="section-tag"><i class="fas fa-heart"></i> Why TPA for Adults</div>
        <h2 class="section-title">Learning in a <span>Safe, Supportive Environment</span></h2>
        <div class="divider-gold"></div>
      </div>
      <div class="row g-4 gsap-stagger">
        <div class="col-sm-6 col-lg-3"><div class="value-card"><div class="value-icon"><i class="fas fa-heart"></i></div><h5 style="font-weight:700;color:var(--navy);">No Judgement</h5><p style="color:var(--text-muted);font-size:.9rem;">We know returning to study can feel daunting. Our tutors are warm, patient and experienced with adult learners.</p></div></div>
        <div class="col-sm-6 col-lg-3"><div class="value-card"><div class="value-icon"><i class="fas fa-clock"></i></div><h5 style="font-weight:700;color:var(--navy);">Flexible Times</h5><p style="color:var(--text-muted);font-size:.9rem;">Evenings and weekends available — because we know life doesn't stop when you want to learn.</p></div></div>
        <div class="col-sm-6 col-lg-3"><div class="value-card"><div class="value-icon"><i class="fas fa-hand-holding-heart"></i></div><h5 style="font-weight:700;color:var(--navy);">Fully Inclusive</h5><p style="color:var(--text-muted);font-size:.9rem;">Dyslexia, Autism, ADHD and other learning differences are fully supported — we adapt to every individual.</p></div></div>
        <div class="col-sm-6 col-lg-3"><div class="value-card"><div class="value-icon"><i class="fas fa-laptop-house"></i></div><h5 style="font-weight:700;color:var(--navy);">Online or In-Centre</h5><p style="color:var(--text-muted);font-size:.9rem;">Study from home or visit us in Chadwell Heath or Chelmsford — whichever suits your lifestyle.</p></div></div>
      </div>
    </div>
  </section>

  <?php
  $star_carousel_tags = ['adult'];
  require_once 'includes/star-carousel.php';
  ?>

  <?php render_testimonials_section($course_tag, 'What <span>Adult Learners Say</span>', 0, false); ?>

  <!-- CTA -->
  <section class="cta-section">
    <div class="container text-center position-relative">
      <div data-aos="fade-up">
        <h2>Ready to <span style="color:var(--gold);">Start Your Learning Journey?</span></h2>
        <p>Book a free, no-obligation consultation and let's find the right programme for you.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
          <a href="<?= SITE_URL ?>/contact.php#assessment" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Consultation</a>
          <a href="tel:<?= PHONE ?>" class="btn-secondary-tpa"><i class="fas fa-phone-alt"></i> Call <?= PHONE ?></a>
        </div>
      </div>
    </div>
  </section>

<?php require_once 'includes/footer.php'; ?>
