<?php
$page_title = 'Parkwood Academy Partnership | Talent Pool Academy';
$meta_description = 'Exclusive academic support for Parkwood Academy families. Talent Pool Academy offers dedicated tutoring, 11 Plus preparation, KS3, GCSE and A-Level tuition for Parkwood Academy students.';
$_canonical = 'https://www.talentpoolacademy.com/parkwood-academy.php';
$schema_extra = '<script type="application/ld+json">' . json_encode([
  '@context'   => 'https://schema.org',
  '@type'      => 'EducationalOrganization',
  'name'       => 'Talent Pool Academy — Parkwood Academy Partnership',
  'url'        => 'https://www.talentpoolacademy.com/parkwood-academy.php',
  'description'=> 'Specialist tuition partner for Parkwood Academy students. Covering 11 Plus, KS2, KS3, GCSE and A-Level across Chadwell Heath and online.',
  'parentOrganization' => ['@type'=>'EducationalOrganization','name'=>'Talent Pool Academy','url'=>'https://www.talentpoolacademy.com'],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . '</script>';

require_once 'includes/header.php';
?>

<style>
.pw-hero {
  background: linear-gradient(135deg, #0A1628 0%, #1a2f4e 60%, #0d2240 100%);
  padding: 72px 0 80px;
  position: relative;
  overflow: hidden;
}
.pw-hero::before {
  content: '';
  position: absolute; inset: 0;
  background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23F5A623' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.pw-partner-badge {
  display: inline-flex; align-items: center; gap: 10px;
  background: rgba(245,166,35,.12); border: 1px solid rgba(245,166,35,.35);
  border-radius: 50px; padding: 6px 18px 6px 10px;
  font-size: .82rem; font-weight: 700; color: var(--gold);
  margin-bottom: 1.5rem;
}
.pw-partner-badge img { width: 24px; height: 24px; border-radius: 50%; object-fit: cover; }
.pw-logo-bar {
  display: flex; align-items: center; gap: 16px; margin-bottom: 1.8rem;
}
.pw-logo-bar .divider-plus {
  font-size: 1.4rem; color: rgba(255,255,255,.3); font-weight: 300;
}
.pw-logo-tpa {
  background: var(--gold); color: var(--navy); font-weight: 900;
  font-size: .85rem; padding: 8px 16px; border-radius: 8px; letter-spacing: .04em;
}
.pw-logo-school {
  background: rgba(255,255,255,.12); color: #fff; font-weight: 800;
  font-size: .85rem; padding: 8px 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,.2);
}
.pw-hero h1 { color: #fff; font-size: clamp(1.8rem,4vw,2.6rem); font-weight: 900; line-height: 1.2; margin-bottom: 1rem; }
.pw-hero p { color: rgba(255,255,255,.78); font-size: 1.05rem; line-height: 1.7; max-width: 560px; }
.pw-stats-bar {
  display: flex; flex-wrap: wrap; gap: 20px; margin-top: 2rem;
}
.pw-stat { display: flex; align-items: center; gap: 8px; color: rgba(255,255,255,.85); font-size: .88rem; }
.pw-stat i { color: var(--gold); }
.pw-school-card {
  background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);
  border-radius: 16px; padding: 28px; color: #fff;
}
.pw-school-card h3 { color: var(--gold); font-size: 1rem; font-weight: 800; margin-bottom: .5rem; }
.benefit-card {
  background: #fff; border: 1px solid #eef1f6; border-radius: 14px; padding: 24px;
  height: 100%; transition: box-shadow .15s, transform .15s;
}
.benefit-card:hover { box-shadow: 0 8px 28px rgba(10,22,40,.1); transform: translateY(-2px); }
.benefit-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 14px; }
.pw-offer-banner {
  background: linear-gradient(135deg, #F5A623 0%, #f0920e 100%);
  border-radius: 16px; padding: 32px 36px;
  display: flex; align-items: center; gap: 24px; flex-wrap: wrap;
}
.pw-offer-banner h3 { color: var(--navy); font-weight: 900; font-size: 1.3rem; margin: 0 0 4px; }
.pw-offer-banner p { color: rgba(10,22,40,.75); margin: 0; font-size: .95rem; }
.pw-courses-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(200px,1fr)); gap: 16px; }
.pw-course-pill {
  background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px;
  padding: 16px 18px; text-align: center; font-weight: 700; font-size: .9rem;
  color: var(--navy); transition: border-color .15s, background .15s;
}
.pw-course-pill:hover { border-color: var(--gold); background: #fffbf0; }
.pw-course-pill small { display: block; font-weight: 500; color: #64748b; font-size: .75rem; margin-top: 3px; }
.pw-form-wrap {
  background: #fff; border-radius: 20px; padding: 40px;
  box-shadow: 0 8px 40px rgba(10,22,40,.12);
}
.pw-form-wrap .form-label-tpa { font-weight: 700; font-size: .82rem; color: var(--navy); margin-bottom: 5px; display: block; text-transform: uppercase; letter-spacing: .04em; }
.pw-form-wrap .form-control-tpa, .pw-form-wrap .form-select-tpa {
  border: 1.5px solid rgba(10,22,40,.12); border-radius: 10px;
  padding: .7rem 1rem; width: 100%; font-size: .95rem;
  transition: border-color .15s, box-shadow .15s;
  appearance: none;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%230A1628' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
  background-repeat: no-repeat; background-position: right 1rem center; background-size: 1em;
}
.pw-form-wrap .form-control-tpa { background-image: none; }
.pw-form-wrap .form-control-tpa:focus, .pw-form-wrap .form-select-tpa:focus {
  outline: none; border-color: var(--gold); box-shadow: 0 0 0 3px rgba(245,166,35,.15);
}
.pw-form-wrap textarea.form-control-tpa { background-image: none; resize: vertical; }
.pw-school-badge-form {
  display: flex; align-items: center; gap: 10px;
  background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 12px 16px;
  margin-bottom: 24px;
}
.pw-school-badge-form i { color: #0284c7; font-size: 1.1rem; }
.pw-school-badge-form span { font-size: .85rem; font-weight: 600; color: #0369a1; }
.testimonial-pw {
  background: #f8fafc; border-left: 4px solid var(--gold); border-radius: 0 12px 12px 0;
  padding: 20px 24px; font-style: italic; color: var(--navy); position: relative;
}
.testimonial-pw cite { font-style: normal; font-size: .82rem; color: #64748b; display: block; margin-top: 8px; }
.pw-promo-badge {
  display: flex; align-items: center; gap: 10px;
  background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
  border: 2px solid var(--gold); border-radius: 10px;
  padding: 12px 16px; margin-bottom: 14px;
  animation: pw-pulse-border 2.2s ease-in-out infinite;
}
.pw-promo-icon { font-size: 1.3rem; flex-shrink: 0; animation: pw-bounce 1.8s ease-in-out infinite; }
.pw-promo-text { font-size: .88rem; color: #92400e; line-height: 1.4; }
.pw-promo-text strong { color: #78350f; }
@keyframes pw-pulse-border {
  0%, 100% { box-shadow: 0 0 0 0 rgba(245,166,35,.4); border-color: var(--gold); }
  50%       { box-shadow: 0 0 0 6px rgba(245,166,35,.0); border-color: #f0921a; }
}
@keyframes pw-bounce {
  0%, 100% { transform: translateY(0); }
  50%       { transform: translateY(-4px); }
}
@media (max-width: 768px) {
  .pw-hero { padding: 50px 0 56px; }
  .pw-offer-banner { padding: 22px; }
  .pw-form-wrap { padding: 24px 20px; }
  .pw-courses-grid { grid-template-columns: 1fr 1fr; }
}
</style>

<!-- ── HERO ────────────────────────────────────────────────────────────── -->
<section class="pw-hero">
  <div class="container position-relative">
    <div class="row align-items-center g-5">
      <div class="col-lg-7">
        <div class="pw-partner-badge">
          <i class="fas fa-handshake"></i>
          Official Academic Partner — Parkwood Academy
        </div>
        <div class="pw-logo-bar">
          <div class="pw-logo-tpa">TPA</div>
          <div class="divider-plus">×</div>
          <div class="pw-logo-school">Parkwood Academy</div>
        </div>
        <h1>Expert Tuition for<br><span style="color:var(--gold);">Parkwood Academy</span> Students</h1>
        <p>Talent Pool Academy is proud to be the chosen academic support partner for Parkwood Academy. We provide personalised small-group tuition specifically tailored to the curriculum and assessment needs of Parkwood Academy students.</p>
        <div class="pw-stats-bar">
          <div class="pw-stat"><i class="fas fa-users"></i> Max 5–6 per class</div>
          <div class="pw-stat"><i class="fas fa-graduation-cap"></i> All year groups</div>
          <div class="pw-stat"><i class="fas fa-laptop"></i> In-centre &amp; Online</div>
          <div class="pw-stat"><i class="fas fa-trophy"></i> 16+ years experience</div>
        </div>
        <div class="d-flex gap-3 mt-4 flex-wrap">
          <a href="#pw-enquire" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Assessment</a>
          <a href="#pw-courses" class="btn-secondary-tpa"><i class="fas fa-book-open"></i> View Courses</a>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="pw-school-card">
          <h3><i class="fas fa-school me-2"></i>Parkwood Academy Partnership</h3>
          <p style="color:rgba(255,255,255,.75);font-size:.92rem;margin-bottom:1.2rem;">Through this partnership, Parkwood Academy families receive:</p>
          <ul style="list-style:none;padding:0;margin:0;color:rgba(255,255,255,.85);font-size:.9rem;">
            <li class="mb-2"><i class="fas fa-check-circle me-2" style="color:var(--gold);"></i>Priority enrolment &amp; fast-track assessments</li>
            <li class="mb-2"><i class="fas fa-check-circle me-2" style="color:var(--gold);"></i>Curriculum-aligned tuition matched to school's schemes of work</li>
            <li class="mb-2"><i class="fas fa-check-circle me-2" style="color:var(--gold);"></i>Regular progress reports shared with parents</li>
            <li class="mb-2"><i class="fas fa-check-circle me-2" style="color:var(--gold);"></i>Dedicated point of contact for school families</li>
            <li class="mb-0"><i class="fas fa-check-circle me-2" style="color:var(--gold);"></i>Free diagnostic assessment for every new student</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── EXCLUSIVE OFFER BANNER ──────────────────────────────────────────── -->
<section style="background:#f8fafc;padding:40px 0;">
  <div class="container">
    <div class="pw-offer-banner" data-aos="fade-up">
      <div style="font-size:2.5rem;flex-shrink:0;">🎓</div>
      <div class="flex-grow-1">
        <h3>Free Assessment for All Parkwood Academy Students</h3>
        <p>Every student from Parkwood Academy receives a complimentary 60-minute diagnostic assessment — no obligation to enrol. We identify exactly where your child needs support and build a personalised learning plan.</p>
      </div>
      <a href="#pw-enquire" class="btn-primary-tpa flex-shrink-0" style="white-space:nowrap;"><i class="fas fa-calendar-check me-2"></i>Book Now — It's Free</a>
    </div>
  </div>
</section>

<!-- ── WHY TPA ──────────────────────────────────────────────────────────── -->
<section class="section-pad">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <div class="section-tag mb-3"><i class="fas fa-star"></i> Why Families Choose Us</div>
      <h2 class="section-title">What Parkwood Academy<br><span>Students Gain</span></h2>
    </div>
    <div class="row g-4">
      <div class="col-md-6 col-lg-4" data-aos="fade-up">
        <div class="benefit-card">
          <div class="benefit-icon" style="background:#fef3c7;color:#d97706;"><i class="fas fa-users-cog"></i></div>
          <h5 style="font-weight:800;color:var(--navy);">Small Group Classes</h5>
          <p class="text-muted small" style="line-height:1.65;">Maximum 5–6 students per group ensures every child gets individual attention. No child gets left behind.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="80">
        <div class="benefit-card">
          <div class="benefit-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-chalkboard-teacher"></i></div>
          <h5 style="font-weight:800;color:var(--navy);">School-Aligned Curriculum</h5>
          <p class="text-muted small" style="line-height:1.65;">Our tutors work in sync with the Parkwood Academy curriculum so tuition directly supports what's being taught in school.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="160">
        <div class="benefit-card">
          <div class="benefit-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-chart-line"></i></div>
          <h5 style="font-weight:800;color:var(--navy);">Measurable Progress</h5>
          <p class="text-muted small" style="line-height:1.65;">Regular assessments and written progress reports keep parents informed. Tracked grade improvements, every term.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-4" data-aos="fade-up">
        <div class="benefit-card">
          <div class="benefit-icon" style="background:#fce7f3;color:#be185d;"><i class="fas fa-laptop-house"></i></div>
          <h5 style="font-weight:800;color:var(--navy);">Flexible Learning Options</h5>
          <p class="text-muted small" style="line-height:1.65;">Attend in-centre at Chadwell Heath or Chelmsford, or join live online classes — whichever fits your family's schedule.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="80">
        <div class="benefit-card">
          <div class="benefit-icon" style="background:#f3e8ff;color:#7e22ce;"><i class="fas fa-user-check"></i></div>
          <h5 style="font-weight:800;color:var(--navy);">DBS-Checked Teachers</h5>
          <p class="text-muted small" style="line-height:1.65;">All TPA tutors are qualified teachers, DBS-checked, and experienced in supporting students at every level.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="160">
        <div class="benefit-card">
          <div class="benefit-icon" style="background:#ffedd5;color:#c2410c;"><i class="fas fa-shield-alt"></i></div>
          <h5 style="font-weight:800;color:var(--navy);">16+ Years of Trust</h5>
          <p class="text-muted small" style="line-height:1.65;">Since 2008, Talent Pool Academy has helped thousands of students across Essex achieve their academic potential.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── COURSES ──────────────────────────────────────────────────────────── -->
<section class="section-pad section-bg" id="pw-courses">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <div class="section-tag mb-3"><i class="fas fa-book-open"></i> Programmes Available</div>
      <h2 class="section-title">Courses for Parkwood<br><span>Academy Students</span></h2>
      <p style="color:var(--text-muted);max-width:520px;margin:0 auto;">From Reception all the way through to A-Level — we support every stage of your child's education.</p>
    </div>
    <div class="pw-courses-grid mb-5" data-aos="fade-up">
      <a href="<?= SITE_URL ?>/course-ks1.php" class="pw-course-pill text-decoration-none">
        Key Stage 1
        <small>Year 1–2 · Ages 5–7</small>
      </a>
      <a href="<?= SITE_URL ?>/course-ks2.php" class="pw-course-pill text-decoration-none">
        Key Stage 2
        <small>Year 3–6 · Ages 7–11</small>
      </a>
      <a href="<?= SITE_URL ?>/course-sats.php" class="pw-course-pill text-decoration-none">
        SATs Preparation
        <small>Year 2 &amp; Year 6</small>
      </a>
      <a href="<?= SITE_URL ?>/course-11plus.php" class="pw-course-pill text-decoration-none">
        11 Plus
        <small>Year 3–6 · Grammar Entry</small>
      </a>
      <a href="<?= SITE_URL ?>/course-ks3.php" class="pw-course-pill text-decoration-none">
        Key Stage 3
        <small>Year 7–9 · Ages 11–14</small>
      </a>
      <a href="<?= SITE_URL ?>/course-gcse.php" class="pw-course-pill text-decoration-none">
        GCSE
        <small>Year 10–11 · Ages 14–16</small>
      </a>
      <a href="<?= SITE_URL ?>/course-alevel.php" class="pw-course-pill text-decoration-none">
        A-Level
        <small>Year 12–13 · Ages 16–18</small>
      </a>
      <a href="<?= SITE_URL ?>/course-adult.php" class="pw-course-pill text-decoration-none">
        Adult Learning
        <small>18+ · Flexible study</small>
      </a>
    </div>

    <!-- Testimonial -->
    <div class="row justify-content-center">
      <div class="col-lg-8" data-aos="fade-up">
        <div class="testimonial-pw">
          "My son started at TPA in Year 5 and the improvement has been remarkable. The small class sizes and the way the teachers explain things differently from school has made such a difference. He now actually enjoys Maths!"
          <cite>— Parent of a Parkwood Academy student, Year 6</cite>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── HOW IT WORKS ─────────────────────────────────────────────────────── -->
<section class="section-pad">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <div class="section-tag mb-3"><i class="fas fa-route"></i> The Process</div>
      <h2 class="section-title">How to Get <span>Started</span></h2>
    </div>
    <div class="row g-4 justify-content-center">
      <?php foreach ([
        ['1','fas fa-paper-plane','Submit Your Enquiry','Fill in the form below — it takes 2 minutes. Let us know your child\'s year group and subjects of interest.','#2563eb'],
        ['2','fas fa-clipboard-check','Free Assessment','We\'ll arrange a convenient time for a free 60-minute diagnostic session, in-centre or online.','#16a34a'],
        ['3','fas fa-file-alt','Learning Plan','You receive a written report with our recommended programme — tailored to your child\'s specific needs.','#d97706'],
        ['4','fas fa-rocket','Start Learning','Once enrolled, your child joins their group and begins making measurable progress within weeks.','#dc2626'],
      ] as [$num,$icon,$title,$desc,$col]): ?>
      <div class="col-sm-6 col-lg-3" data-aos="fade-up">
        <div style="text-align:center;padding:24px 16px;">
          <div style="width:64px;height:64px;border-radius:50%;background:<?= $col ?>;color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin:0 auto 16px;box-shadow:0 8px 24px <?= $col ?>44;">
            <i class="<?= $icon ?>"></i>
          </div>
          <div style="width:28px;height:28px;border-radius:50%;background:#f1f5f9;color:var(--navy);font-weight:900;font-size:.82rem;display:flex;align-items:center;justify-content:center;margin:0 auto -10px;position:relative;top:-10px;z-index:1;"><?= $num ?></div>
          <h5 style="font-weight:800;color:var(--navy);font-size:.98rem;margin-bottom:.5rem;"><?= $title ?></h5>
          <p style="font-size:.85rem;color:var(--text-muted);line-height:1.6;"><?= $desc ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── ENQUIRY FORM ─────────────────────────────────────────────────────── -->
<section class="section-pad section-bg" id="pw-enquire">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-7">
        <div class="text-center mb-4" data-aos="fade-up">
          <div class="section-tag mb-3"><i class="fas fa-calendar-check"></i> Get Started</div>
          <h2 class="section-title">Book Your <span>Free Assessment</span></h2>
          <p style="color:var(--text-muted);">For Parkwood Academy families only. Completely free, no obligation.</p>
        </div>
        <div class="pw-form-wrap" data-aos="fade-up">
          <div class="pw-promo-badge">
            <span class="pw-promo-icon">🎉</span>
            <span class="pw-promo-text">Special <strong>discounted rates</strong> exclusively for Parkwood Academy students!</span>
          </div>
          <div class="pw-school-badge-form">
            <i class="fas fa-school"></i>
            <span>This form is for Parkwood Academy families. Your enquiry will be prioritised.</span>
          </div>
          <form id="pwForm" novalidate>
            <input type="hidden" name="source" value="Parkwood Academy">
            <input type="hidden" name="centre" value="Parkwood Academy">
            <input type="hidden" name="timestamp" id="pw-timestamp">
            <script>document.getElementById('pw-timestamp').value=new Date().toLocaleString('en-GB');</script>
            <div class="row g-3">
              <div class="col-sm-6">
                <label class="form-label-tpa" for="pw-parent-name">Your Name (Parent/Guardian) *</label>
                <input type="text" id="pw-parent-name" name="name" class="form-control-tpa" placeholder="Your full name" required>
              </div>
              <div class="col-sm-6">
                <label class="form-label-tpa" for="pw-child-name">Child's Name *</label>
                <input type="text" id="pw-child-name" name="child_name" class="form-control-tpa" placeholder="Child's full name" required>
              </div>
              <div class="col-sm-6">
                <label class="form-label-tpa" for="pw-year">Child's Year Group *</label>
                <select id="pw-year" name="year_group" class="form-select-tpa" required>
                  <option value="">Select year group</option>
                  <option>Reception</option>
                  <option>Year 1</option><option>Year 2</option><option>Year 3</option>
                  <option>Year 4</option><option>Year 5</option><option>Year 6</option>
                  <option>Year 7</option><option>Year 8</option><option>Year 9</option>
                  <option>Year 10</option><option>Year 11</option>
                  <option>Year 12 (A-Level)</option><option>Year 13 (A-Level)</option>
                  <option>Adult Learning</option>
                </select>
              </div>
              <div class="col-sm-6">
                <label class="form-label-tpa" for="pw-course">Course Interest *</label>
                <select id="pw-course" name="course_interest" class="form-select-tpa" required>
                  <option value="">Select a course</option>
                  <option>KS1 (Year 1–2)</option>
                  <option>KS2 / SATs (Year 3–6)</option>
                  <option>11 Plus Preparation</option>
                  <option>KS3 (Year 7–9)</option>
                  <option>GCSE (Year 10–11)</option>
                  <option>A-Level (Year 12–13)</option>
                  <option>Adult Learning</option>
                  <option>Not sure – please advise</option>
                </select>
              </div>
              <div class="col-sm-6">
                <label class="form-label-tpa" for="pw-email">Email Address *</label>
                <input type="email" id="pw-email" name="email" class="form-control-tpa" placeholder="jane@example.com" required>
              </div>
              <div class="col-sm-6">
                <label class="form-label-tpa" for="pw-phone">Phone Number *</label>
                <input type="tel" id="pw-phone" name="phone" class="form-control-tpa" placeholder="07xxx xxxxxx" required>
              </div>
              <div class="col-sm-6">
                <label class="form-label-tpa" for="pw-location">Preferred Location</label>
                <select id="pw-location" name="preferred_location" class="form-select-tpa">
                  <option value="">No preference</option>
                  <option>In School (Parkwood Academy)</option>
                  <option>Chadwell Heath (RM6 6PP)</option>
                  <option>Chelmsford (CM1 2AR)</option>
                  <option>Online</option>
                </select>
              </div>
              <div class="col-sm-6">
                <label class="form-label-tpa" for="pw-hear">How did you hear about this?</label>
                <select id="pw-hear" name="hear" class="form-select-tpa">
                  <option value="">Please select</option>
                  <option>Via Parkwood Academy</option>
                  <option>School Newsletter</option>
                  <option>Word of Mouth</option>
                  <option>Google Search</option>
                  <option>Social Media</option>
                  <option>Other</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label-tpa" for="pw-message">Message (optional)</label>
                <textarea id="pw-message" name="message" class="form-control-tpa" rows="3" placeholder="Tell us what subjects you need help with, any specific goals, or questions you have…"></textarea>
              </div>
              <div class="col-12">
                <div style="display:flex;align-items:flex-start;gap:.65rem;padding:.25rem 0;">
                  <input type="checkbox" id="pw-consent" name="consent" checked style="flex-shrink:0;margin-top:.2rem;width:1rem;height:1rem;accent-color:var(--gold);cursor:pointer;">
                  <label for="pw-consent" style="font-size:.87rem;color:var(--text-muted);cursor:pointer;line-height:1.55;margin:0;">I agree to Talent Pool Academy contacting me regarding my enquiry. See our <a href="<?= SITE_URL ?>/privacy.php" style="color:var(--gold);">Privacy Policy</a>.</label>
                </div>
              </div>
              <div class="col-12">
                <button type="submit" id="pwSubmitBtn" class="btn-primary-tpa w-100" style="justify-content:center;font-size:1rem;">
                  <i class="fas fa-paper-plane me-2"></i>Submit — Book My Free Assessment
                </button>
              </div>
            </div>
            <div id="pwSuccess" style="display:none;margin-top:1.5rem;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:20px 24px;text-align:center;">
              <div style="font-size:2rem;margin-bottom:8px;">✅</div>
              <div style="font-weight:800;color:#15803d;font-size:1.05rem;margin-bottom:4px;">Enquiry Received!</div>
              <div style="color:#166534;font-size:.9rem;">Thank you! We'll be in touch within one working day to arrange your free assessment. Check your inbox (and spam folder) for a confirmation.</div>
            </div>
            <div id="pwError" style="display:none;margin-top:1rem;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:14px 18px;color:#dc2626;font-size:.9rem;"></div>
          </form>
        </div>
      </div>

      <!-- Contact sidebar -->
      <div class="col-lg-4 d-none d-lg-block" data-aos="fade-left">
        <div style="position:sticky;top:100px;">
          <!-- Contact card -->
          <div style="background:var(--navy);border-radius:16px;padding:28px 28px 24px;margin-bottom:16px;">
            <div style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--gold);margin-bottom:18px;">Need to talk to us directly?</div>
            <div style="border-bottom:1px solid rgba(255,255,255,.1);padding-bottom:16px;margin-bottom:16px;">
              <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:rgba(255,255,255,.45);margin-bottom:5px;">Call / WhatsApp</div>
              <a href="tel:<?= PHONE ?>" style="color:#fff;font-weight:800;text-decoration:none;font-size:1.15rem;letter-spacing:.01em;"><?= PHONE ?></a>
            </div>
            <div style="border-bottom:1px solid rgba(255,255,255,.1);padding-bottom:16px;margin-bottom:16px;">
              <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:rgba(255,255,255,.45);margin-bottom:5px;">Email</div>
              <a href="mailto:<?= EMAIL ?>" style="color:rgba(255,255,255,.85);text-decoration:none;font-size:.88rem;word-break:break-all;line-height:1.4;"><?= EMAIL ?></a>
            </div>
            <div>
              <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:rgba(255,255,255,.45);margin-bottom:5px;">Opening Hours</div>
              <div style="color:rgba(255,255,255,.8);font-size:.88rem;line-height:1.8;">Mon – Fri: 4pm – 7pm<br>Sat – Sun: 9am – 5pm</div>
            </div>
          </div>
          <!-- Centres card -->
          <div style="background:#fff;border:1.5px solid #e8edf4;border-radius:16px;padding:24px 28px;">
            <div style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:16px;">Our Centres</div>
            <div style="padding-bottom:14px;margin-bottom:14px;border-bottom:1px solid #f1f5f9;">
              <div style="font-weight:700;color:var(--navy);font-size:.9rem;margin-bottom:3px;"><i class="fas fa-map-marker-alt me-1" style="color:var(--gold);"></i>Chadwell Heath</div>
              <div style="font-size:.82rem;color:#64748b;line-height:1.6;">60 High Road, Chadwell Heath<br>Romford RM6 6PP</div>
            </div>
            <div>
              <div style="font-weight:700;color:var(--navy);font-size:.9rem;margin-bottom:3px;"><i class="fas fa-map-marker-alt me-1" style="color:var(--gold);"></i>Chelmsford</div>
              <div style="font-size:.82rem;color:#64748b;line-height:1.6;">4B Corporation Road<br>Chelmsford CM1 2AR</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
document.getElementById('pwForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const btn    = document.getElementById('pwSubmitBtn');
  const errEl  = document.getElementById('pwError');
  const okEl   = document.getElementById('pwSuccess');
  errEl.style.display = 'none';

  const fd   = new FormData(this);
  const data = {
    name:            fd.get('name')            || '',
    child_name:      fd.get('child_name')      || '',
    year_group:      fd.get('year_group')      || '',
    course_interest: fd.get('course_interest') || '',
    email:           fd.get('email')           || '',
    phone:           fd.get('phone')           || '',
    centre:          fd.get('centre')          || 'Parkwood Academy',
    source:          'Parkwood Academy',
    hear:            fd.get('hear')            || '',
    notes:           (fd.get('message') || '') + (fd.get('preferred_location') ? ' | Preferred location: ' + fd.get('preferred_location') : ''),
    timestamp:       fd.get('timestamp')       || '',
  };

  // Basic validation
  if (!data.child_name || !data.email || !data.phone || !data.year_group || !data.course_interest) {
    errEl.textContent = 'Please fill in all required fields marked with *.';
    errEl.style.display = 'block';
    return;
  }

  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending…';

  fetch('<?= SITE_URL ?>/../api/contact-form.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(data)
  })
  .then(r => r.json())
  .then(res => {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit — Book My Free Assessment';
    if (res.success || res.ok) {
      document.getElementById('pwForm').querySelectorAll('input:not([type=hidden]):not([type=checkbox]),select,textarea').forEach(el => el.value = el.tagName==='SELECT'?'':el.defaultValue||'');
      okEl.style.display = 'block';
      okEl.scrollIntoView({behavior:'smooth', block:'center'});
    } else {
      errEl.textContent = res.error || 'Something went wrong. Please try again or call us directly.';
      errEl.style.display = 'block';
    }
  })
  .catch(() => {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit — Book My Free Assessment';
    errEl.textContent = 'Connection error. Please try again or call us on <?= PHONE ?>.';
    errEl.style.display = 'block';
  });
});
</script>

<?php require_once 'includes/footer.php'; ?>
