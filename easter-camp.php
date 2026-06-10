<?php
$page_title = 'Easter Holiday Course April 2026';
$meta_description = 'Easter Holiday Course April 2026 — Talent Pool Academy. Online via Zoom and In-Centre at Chelmsford & Chadwell Heath. Year 2 SATs, Year 6 SATs, 11 Plus, GCSE revision.';
require_once 'includes/header.php';
?>
<style>
  .easter-hero { background: linear-gradient(135deg, var(--navy) 0%, #1a3a6b 60%, #0d2240 100%); padding: 140px 0 80px; position: relative; overflow: hidden; }
  .easter-hero::before { content: ''; position: absolute; inset: 0; background: radial-gradient(ellipse at 80% 50%, rgba(245,166,35,0.08) 0%, transparent 70%); }
  .camp-badge { display: inline-flex; align-items: center; gap: .5rem; background: rgba(245,166,35,0.12); border: 1px solid rgba(245,166,35,0.35); color: var(--gold); padding: .45rem 1.1rem; border-radius: 50px; font-size: .82rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 1.2rem; }
  .easter-hero h1 { color: #fff; font-size: clamp(2rem, 4vw, 3rem); font-weight: 900; line-height: 1.15; }
  .easter-hero h1 span { color: var(--gold); }
  .easter-hero p.lead { color: rgba(255,255,255,0.75); font-size: 1.1rem; max-width: 560px; }
  .mode-tabs { display: flex; gap: 1rem; margin: 2rem 0; flex-wrap: wrap; }
  .mode-tab { flex: 1; min-width: 200px; padding: 1.5rem; border-radius: var(--radius-lg); cursor: pointer; border: 2px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.05); transition: all .3s; text-align: center; }
  .mode-tab:hover, .mode-tab.active { border-color: var(--gold); background: rgba(245,166,35,0.12); }
  .mode-tab .mode-icon { font-size: 2rem; margin-bottom: .5rem; }
  .mode-tab .mode-title { color: #fff; font-weight: 700; font-size: 1.05rem; }
  .mode-tab .mode-sub { color: rgba(255,255,255,0.55); font-size: .82rem; margin-top: .2rem; }
  .mode-tab.active .mode-title { color: var(--gold); }
  .tab-content-panel { display: none; }
  .tab-content-panel.active { display: block; }
  .schedule-section { padding: 60px 0; }
  .schedule-card { background: #fff; border-radius: var(--radius-lg); box-shadow: var(--shadow-md); overflow: hidden; margin-bottom: 2rem; }
  .schedule-header { background: var(--navy); padding: 1.2rem 1.5rem; display: flex; align-items: center; gap: 1rem; }
  .schedule-header .course-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
  .schedule-header h4 { color: #fff; font-weight: 700; margin: 0; font-size: 1.05rem; }
  .schedule-header .year-badge { background: var(--gold); color: var(--navy); font-weight: 800; font-size: .75rem; padding: .2rem .6rem; border-radius: 20px; white-space: nowrap; }
  .schedule-body { padding: 1.5rem; }
  .dates-row { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem; }
  .date-chip { display: flex; align-items: center; gap: .4rem; background: var(--off-white); border-radius: 8px; padding: .5rem .9rem; font-size: .88rem; font-weight: 600; color: var(--navy); }
  .time-chip { display: flex; align-items: center; gap: .4rem; background: rgba(245,166,35,0.1); border: 1px solid rgba(245,166,35,0.3); border-radius: 8px; padding: .5rem .9rem; font-size: .88rem; font-weight: 700; color: var(--navy); }
  .fee-tag { background: var(--navy); color: var(--gold); font-weight: 900; font-size: 1.3rem; padding: .4rem 1rem; border-radius: 8px; display: inline-block; }
  .curriculum-section { background: var(--off-white); padding: 60px 0; }
  .year-curriculum-card { background: #fff; border-radius: var(--radius-lg); padding: 1.8rem; box-shadow: var(--shadow-sm); height: 100%; border-top: 4px solid var(--gold); }
  .year-curriculum-card h4 { color: var(--navy); font-weight: 800; font-size: 1.1rem; margin-bottom: 1.2rem; }
  .subject-block { margin-bottom: 1rem; }
  .subject-block h6 { font-weight: 700; color: var(--navy); font-size: .88rem; text-transform: uppercase; letter-spacing: .05em; margin-bottom: .5rem; display: flex; align-items: center; gap: .4rem; }
  .subject-block ul { padding-left: 1.2rem; margin: 0; }
  .subject-block ul li { font-size: .88rem; color: var(--text-muted); line-height: 1.6; }
  .booking-section { padding: 60px 0; }
  .booking-card { background: var(--navy); border-radius: var(--radius-lg); padding: 2.5rem; }
  .booking-card h3 { color: #fff; font-weight: 800; margin-bottom: .5rem; }
  .booking-card .booking-sub { color: rgba(255,255,255,0.6); margin-bottom: 2rem; }
  .booking-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
  @media(max-width:600px) { .booking-form-grid { grid-template-columns: 1fr; } }
  .booking-card .form-label-tpa { font-size: .82rem; font-weight: 700; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: .04em; margin-bottom: .35rem; display: block; }
  .booking-card .form-control-tpa { width: 100%; padding: .7rem 1rem; border-radius: 8px; border: 1.5px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.07); color: #fff; font-size: .92rem; outline: none; transition: border .2s; }
  .booking-card .form-control-tpa::placeholder { color: rgba(255,255,255,0.35); }
  .booking-card .form-control-tpa:focus { border-color: var(--gold); background: rgba(255,255,255,0.1); }
  .booking-card .form-control-tpa option { color: var(--navy); background: #fff; }
  .bank-details { background: rgba(245,166,35,0.08); border: 1px solid rgba(245,166,35,0.25); border-radius: var(--radius-md); padding: 1.5rem; margin-top: 2rem; }
  .bank-details h5 { color: var(--gold); font-weight: 700; margin-bottom: 1rem; font-size: .95rem; }
  .bank-row { display: flex; justify-content: space-between; padding: .4rem 0; border-bottom: 1px solid rgba(255,255,255,0.07); font-size: .88rem; }
  .bank-row:last-child { border: none; }
  .bank-row .label { color: rgba(255,255,255,0.55); }
  .bank-row .value { color: #fff; font-weight: 700; }
  .refund-notice { background: rgba(255,80,80,0.1); border: 1px solid rgba(255,80,80,0.25); border-radius: 8px; padding: 1rem 1.2rem; margin-top: 1.5rem; font-size: .85rem; color: rgba(255,200,200,0.9); display: flex; gap: .6rem; align-items: flex-start; }
  .deadline-banner { background: linear-gradient(90deg, #c0392b, #e74c3c); border-radius: 8px; padding: 1rem 1.2rem; margin-bottom: 1.5rem; font-size: .9rem; color: #fff; font-weight: 600; display: flex; align-items: center; gap: .6rem; }
</style>

  <!-- HERO -->
  <section class="easter-hero">
    <div class="container position-relative">
      <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb" style="background:none;padding:0;"><li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php" style="color:rgba(255,255,255,0.5);">Home</a></li><li class="breadcrumb-item active" style="color:var(--gold);">Easter Camp 2026</li></ol></nav>
      <div class="camp-badge"><i class="fas fa-sun"></i> Limited Places — Book Early</div>
      <h1>Easter Holiday Course<br><span>April 2026</span></h1>
      <p class="lead mt-3">Intensive Easter revision covering Year 2 SATs, Year 6 SATs, 11 Plus (Year 3–5), and full GCSE revision. Available <strong style="color:var(--gold);">Online via Zoom</strong> and <strong style="color:var(--gold);">In-Centre</strong> at Chelmsford &amp; Chadwell Heath.</p>
      <div class="mode-tabs mt-4">
        <div class="mode-tab active" data-tab="online" onclick="switchTab('online', this)">
          <div class="mode-icon">💻</div>
          <div class="mode-title">Online via Zoom</div>
          <div class="mode-sub">Tue 7 Apr · Sat 11 Apr · 10am–1pm</div>
        </div>
        <div class="mode-tab" data-tab="centre" onclick="switchTab('centre', this)">
          <div class="mode-icon">🏫</div>
          <div class="mode-title">In-Centre</div>
          <div class="mode-sub">Chelmsford &amp; Chadwell Heath</div>
        </div>
      </div>
    </div>
  </section>

  <!-- SCHEDULE SECTION -->
  <section class="schedule-section">
    <div class="container">

      <!-- ONLINE TAB -->
      <div id="tab-online" class="tab-content-panel active">
        <div class="text-center mb-4" data-aos="fade-up">
          <div class="section-tag"><i class="fas fa-video"></i> Online via Zoom</div>
          <h2 class="section-title">Online <span>Schedule</span></h2>
          <p style="color:var(--text-muted);">All sessions run <strong>10:00 AM – 1:00 PM</strong> via Zoom. Zoom link sent on booking confirmation.</p>
          <div class="divider-gold"></div>
        </div>
        <div class="row g-4">
          <div class="col-md-6" data-aos="fade-up"><div class="schedule-card"><div class="schedule-header"><div class="course-icon" style="background:rgba(33,150,243,0.2);color:#90caf9;"><i class="fas fa-pencil-alt"></i></div><div class="flex-grow-1"><h4>Year 2 SATs Revision</h4><small style="color:rgba(255,255,255,0.55);">Maths &amp; English (SPaG)</small></div><div class="year-badge">Year 2</div></div><div class="schedule-body"><div class="dates-row"><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Tuesday 7th April</div><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Saturday 11th April</div></div><div class="dates-row"><div class="time-chip"><i class="fas fa-clock"></i> 10:00 AM – 1:00 PM</div><div class="fee-tag">£150</div></div></div></div></div>
          <div class="col-md-6" data-aos="fade-up" data-aos-delay="50"><div class="schedule-card"><div class="schedule-header"><div class="course-icon" style="background:rgba(76,175,80,0.2);color:#a5d6a7;"><i class="fas fa-book-open"></i></div><div class="flex-grow-1"><h4>Year 6 SATs Revision</h4><small style="color:rgba(255,255,255,0.55);">In-depth paper practice</small></div><div class="year-badge">Year 6</div></div><div class="schedule-body"><div class="dates-row"><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Tuesday 7th April</div><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Saturday 11th April</div></div><div class="dates-row"><div class="time-chip"><i class="fas fa-clock"></i> 10:00 AM – 1:00 PM</div><div class="fee-tag">£150</div></div></div></div></div>
          <div class="col-md-6" data-aos="fade-up" data-aos-delay="100"><div class="schedule-card"><div class="schedule-header"><div class="course-icon" style="background:rgba(245,166,35,0.2);color:var(--gold);"><i class="fas fa-star"></i></div><div class="flex-grow-1"><h4>11 Plus Preparation</h4><small style="color:rgba(255,255,255,0.55);">VR, NVR, Maths &amp; English</small></div><div class="year-badge">Year 3 &amp; 4</div></div><div class="schedule-body"><div class="dates-row"><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Tuesday 7th April</div><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Saturday 11th April</div></div><div class="dates-row"><div class="time-chip"><i class="fas fa-clock"></i> 10:00 AM – 1:00 PM</div><div class="fee-tag">£150</div></div></div></div></div>
          <div class="col-md-6" data-aos="fade-up" data-aos-delay="150"><div class="schedule-card"><div class="schedule-header"><div class="course-icon" style="background:rgba(245,166,35,0.2);color:var(--gold);"><i class="fas fa-star"></i></div><div class="flex-grow-1"><h4>11 Plus Preparation</h4><small style="color:rgba(255,255,255,0.55);">Advanced: Fractions, Algebra &amp; Comprehension</small></div><div class="year-badge">Year 5</div></div><div class="schedule-body"><div class="dates-row"><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Tuesday 7th April</div><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Saturday 11th April</div></div><div class="dates-row"><div class="time-chip"><i class="fas fa-clock"></i> 10:00 AM – 1:00 PM</div><div class="fee-tag">£150</div></div></div></div></div>
          <div class="col-12" data-aos="fade-up" data-aos-delay="200"><div class="schedule-card" style="border: 2px solid var(--gold);"><div class="schedule-header" style="background: linear-gradient(90deg, var(--navy), #1a3a6b);"><div class="course-icon" style="background:rgba(245,166,35,0.25);color:var(--gold);"><i class="fas fa-graduation-cap"></i></div><div class="flex-grow-1"><h4>GCSE Revision &amp; Mock Tests</h4><small style="color:rgba(255,255,255,0.55);">Maths · English · Science — AQA, Edexcel &amp; other boards. Full past-paper revision.</small></div><div class="year-badge" style="background:var(--gold);">Year 10 &amp; 11</div></div><div class="schedule-body"><div class="dates-row"><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Tuesday 7th April</div><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Saturday 11th April</div></div><div class="dates-row"><div class="time-chip"><i class="fas fa-clock"></i> 10:00 AM – 1:00 PM</div><div class="fee-tag" style="font-size:1.5rem;">£200</div></div></div></div></div>
        </div>
      </div>

      <!-- IN-CENTRE TAB -->
      <div id="tab-centre" class="tab-content-panel">
        <div class="text-center mb-4" data-aos="fade-up">
          <div class="section-tag"><i class="fas fa-school"></i> In-Centre</div>
          <h2 class="section-title">In-Centre <span>Schedule</span></h2>
          <div class="d-flex justify-content-center gap-3 flex-wrap mb-3">
            <div style="background:var(--off-white);border-radius:8px;padding:.5rem 1.2rem;font-size:.9rem;font-weight:600;color:var(--navy);"><i class="fas fa-map-marker-alt text-gold me-1"></i> Chelmsford</div>
            <div style="background:var(--off-white);border-radius:8px;padding:.5rem 1.2rem;font-size:.9rem;font-weight:600;color:var(--navy);"><i class="fas fa-map-marker-alt text-gold me-1"></i> Chadwell Heath</div>
          </div>
          <p style="color:var(--text-muted);">Weekday sessions <strong>4:00 PM – 7:00 PM</strong> · Saturday sessions <strong>10:00 AM – 1:00 PM</strong></p>
          <div class="divider-gold"></div>
        </div>
        <div class="row g-4">
          <div class="col-md-6" data-aos="fade-up"><div class="schedule-card"><div class="schedule-header"><div class="course-icon" style="background:rgba(33,150,243,0.2);color:#90caf9;"><i class="fas fa-pencil-alt"></i></div><div class="flex-grow-1"><h4>Year 2 SATs Revision</h4><small style="color:rgba(255,255,255,0.55);">Maths &amp; English (SPaG)</small></div><div class="year-badge">Year 2</div></div><div class="schedule-body"><div class="dates-row"><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Tue 7th April</div><div class="time-chip"><i class="fas fa-clock"></i> 4:00 – 7:00 PM</div></div><div class="dates-row"><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Sat 11th April</div><div class="time-chip"><i class="fas fa-clock"></i> 10:00 AM – 1:00 PM</div></div><div class="mt-2"><div class="fee-tag">£150</div></div></div></div></div>
          <div class="col-md-6" data-aos="fade-up" data-aos-delay="50"><div class="schedule-card"><div class="schedule-header"><div class="course-icon" style="background:rgba(76,175,80,0.2);color:#a5d6a7;"><i class="fas fa-book-open"></i></div><div class="flex-grow-1"><h4>Year 6 SATs Revision</h4><small style="color:rgba(255,255,255,0.55);">In-depth paper practice</small></div><div class="year-badge">Year 6</div></div><div class="schedule-body"><div class="dates-row"><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Tue 7th April</div><div class="time-chip"><i class="fas fa-clock"></i> 4:00 – 7:00 PM</div></div><div class="dates-row"><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Sat 11th April</div><div class="time-chip"><i class="fas fa-clock"></i> 10:00 AM – 1:00 PM</div></div><div class="mt-2"><div class="fee-tag">£150</div></div></div></div></div>
          <div class="col-md-6" data-aos="fade-up" data-aos-delay="100"><div class="schedule-card"><div class="schedule-header"><div class="course-icon" style="background:rgba(245,166,35,0.2);color:var(--gold);"><i class="fas fa-star"></i></div><div class="flex-grow-1"><h4>11 Plus Preparation</h4><small style="color:rgba(255,255,255,0.55);">VR, NVR, Maths &amp; English</small></div><div class="year-badge">Year 3 &amp; 4</div></div><div class="schedule-body"><div class="dates-row"><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Tue 7th April</div><div class="time-chip"><i class="fas fa-clock"></i> 4:00 – 7:00 PM</div></div><div class="dates-row"><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Sat 11th April</div><div class="time-chip"><i class="fas fa-clock"></i> 10:00 AM – 1:00 PM</div></div><div class="mt-2"><div class="fee-tag">£150</div></div></div></div></div>
          <div class="col-md-6" data-aos="fade-up" data-aos-delay="150"><div class="schedule-card"><div class="schedule-header"><div class="course-icon" style="background:rgba(245,166,35,0.2);color:var(--gold);"><i class="fas fa-star"></i></div><div class="flex-grow-1"><h4>11 Plus Preparation</h4><small style="color:rgba(255,255,255,0.55);">Fractions, Algebra &amp; Comprehension</small></div><div class="year-badge">Year 5</div></div><div class="schedule-body"><div class="dates-row"><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Tue 7th April</div><div class="time-chip"><i class="fas fa-clock"></i> 4:00 – 7:00 PM</div></div><div class="dates-row"><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Sat 11th April</div><div class="time-chip"><i class="fas fa-clock"></i> 10:00 AM – 1:00 PM</div></div><div class="mt-2"><div class="fee-tag">£150</div></div></div></div></div>
          <div class="col-12" data-aos="fade-up" data-aos-delay="200"><div class="schedule-card" style="border: 2px solid var(--gold);"><div class="schedule-header"><div class="course-icon" style="background:rgba(245,166,35,0.25);color:var(--gold);"><i class="fas fa-graduation-cap"></i></div><div class="flex-grow-1"><h4>GCSE Revision &amp; Mock Tests</h4><small style="color:rgba(255,255,255,0.55);">Maths · English · Science — AQA, Edexcel &amp; other boards</small></div><div class="year-badge">Year 10 &amp; 11</div></div><div class="schedule-body"><div class="dates-row"><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Tue 7th April</div><div class="time-chip"><i class="fas fa-clock"></i> 4:00 – 7:00 PM</div></div><div class="dates-row"><div class="date-chip"><i class="fas fa-calendar text-gold"></i> Sat 11th April</div><div class="time-chip"><i class="fas fa-clock"></i> 10:00 AM – 1:00 PM</div></div><div class="mt-2"><div class="fee-tag" style="font-size:1.5rem;">£200</div></div></div></div></div>
        </div>
      </div>
    </div>
  </section>

  <!-- CURRICULUM -->
  <section class="curriculum-section">
    <div class="container">
      <div class="text-center mb-5" data-aos="fade-up">
        <div class="section-tag"><i class="fas fa-book-open"></i> What We Cover</div>
        <h2 class="section-title">Curriculum <span>Breakdown</span></h2>
        <p style="color:var(--text-muted); max-width:540px;margin:0 auto;">Detailed topics taught each day across all year groups and subjects.</p>
        <div class="divider-gold"></div>
      </div>
      <div class="row g-4">
        <div class="col-md-6 col-lg-4" data-aos="fade-up"><div class="year-curriculum-card"><h4><i class="fas fa-pencil-alt text-gold me-2"></i>Year 2 — SATs Revision</h4><div class="subject-block"><h6><i class="fas fa-calculator text-gold"></i> Maths</h6><ul><li>Arithmetic &amp; number bonds</li><li>Addition, subtraction, multiplication, division</li><li>Shapes and measurement</li></ul></div><div class="subject-block"><h6><i class="fas fa-pen text-gold"></i> English (SPaG)</h6><ul><li>Spelling and phonics patterns</li><li>Grammar and punctuation</li><li>Reading comprehension</li></ul></div></div></div>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="50"><div class="year-curriculum-card"><h4><i class="fas fa-star text-gold me-2"></i>Year 3 — 11 Plus Prep</h4><div class="subject-block"><h6><i class="fas fa-calculator text-gold"></i> Maths</h6><ul><li>Fractions</li><li>Measurement</li><li>Geometry — properties of shapes</li></ul></div><div class="subject-block"><h6><i class="fas fa-pen text-gold"></i> English</h6><ul><li>SPaG</li><li>Creative writing</li><li>Writing transcription</li><li>Reading comprehension (poetry, fiction, non-fiction)</li><li>Writing inference skills</li></ul></div></div></div>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100"><div class="year-curriculum-card"><h4><i class="fas fa-star text-gold me-2"></i>Year 4 — 11 Plus Prep</h4><div class="subject-block"><h6><i class="fas fa-calculator text-gold"></i> Maths</h6><ul><li>Calculation &amp; conversion of measurements</li><li>Perimeter and area</li><li>Properties of 2D and 3D shapes</li><li>Symmetry</li><li>Decimal fractions &amp; rounding</li><li>Introduction to negative numbers</li></ul></div><div class="subject-block"><h6><i class="fas fa-pen text-gold"></i> English</h6><ul><li>SPaG</li><li>Creative writing</li><li>Introduction to figurative language</li><li>Comprehension (poetry, fiction, non-fiction)</li></ul></div></div></div>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="150"><div class="year-curriculum-card"><h4><i class="fas fa-star text-gold me-2"></i>Year 5 — 11 Plus Prep</h4><div class="subject-block"><h6><i class="fas fa-calculator text-gold"></i> Maths</h6><ul><li>Fractions, decimals &amp; percentages</li><li>Worded questions for metric units</li><li>Currency conversion</li><li>Algebra</li></ul></div><div class="subject-block"><h6><i class="fas fa-pen text-gold"></i> English</h6><ul><li>50 spellings every day to learn</li><li>Grammar &amp; Punctuation</li><li>Comprehension (Non-Fiction &amp; Classic Texts)</li><li>Exam-style creative writing</li></ul></div></div></div>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200"><div class="year-curriculum-card" style="border-top-color: #4caf50;"><h4><i class="fas fa-book-open text-gold me-2"></i>Year 6 — SATs Revision</h4><div class="subject-block"><h6><i class="fas fa-file-alt text-gold"></i> Format</h6><ul><li>Explanation &amp; in-depth paper practice</li><li>Timed past paper conditions</li><li>Detailed feedback per question</li></ul></div><div class="subject-block"><h6><i class="fas fa-calculator text-gold"></i> Maths</h6><ul><li>Arithmetic paper (Paper 1)</li><li>Reasoning papers (Paper 2 &amp; 3)</li></ul></div><div class="subject-block"><h6><i class="fas fa-pen text-gold"></i> English</h6><ul><li>Reading comprehension paper</li><li>SPaG paper — all grammar topics</li></ul></div></div></div>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="250"><div class="year-curriculum-card" style="border-top-color: var(--gold);"><h4><i class="fas fa-graduation-cap text-gold me-2"></i>GCSE — Year 10 &amp; 11</h4><div class="subject-block"><h6><i class="fas fa-calculator text-gold"></i> Maths</h6><ul><li>Full past-paper revision</li><li>AQA, Edexcel &amp; other boards</li><li>Algebra, geometry &amp; statistics</li></ul></div><div class="subject-block"><h6><i class="fas fa-pen text-gold"></i> English</h6><ul><li>Language (Paper 1 &amp; 2)</li><li>Literature — key texts</li></ul></div><div class="subject-block"><h6><i class="fas fa-flask text-gold"></i> Science</h6><ul><li>Biology, Chemistry &amp; Physics</li><li>Mock test under exam conditions</li></ul></div></div></div>
      </div>
    </div>
  </section>

  <!-- BOOKING SECTION -->
  <section class="booking-section" id="book">
    <div class="container">
      <div class="row g-5 align-items-start">
        <div class="col-lg-7" data-aos="fade-right">
          <div class="booking-card">
            <h3>📝 Book Your Place</h3>
            <p class="booking-sub">Complete the form below or WhatsApp/Text us on <strong style="color:var(--gold);">+44 07772 922943</strong> to reserve your place.</p>
            <div class="deadline-banner"><i class="fas fa-exclamation-triangle"></i> Last Date for Payment: <strong>12th February 2026</strong></div>
            <form id="easterForm">
              <div class="booking-form-grid">
                <div><label class="form-label-tpa">Student's Name *</label><input type="text" name="child_name" class="form-control-tpa" placeholder="Full name" required></div>
                <div><label class="form-label-tpa">Year Group *</label><select name="year_group" class="form-control-tpa" required><option value="">Select year group</option><option>Year 2</option><option>Year 3</option><option>Year 4</option><option>Year 5</option><option>Year 6</option><option>Year 10</option><option>Year 11</option></select></div>
                <div><label class="form-label-tpa">Course Name *</label><select name="subject" class="form-control-tpa" required><option value="">Select course</option><option>Year 2 SATs Revision — £150</option><option>Year 6 SATs Revision — £150</option><option>Year 3 &amp; 4 — 11 Plus Prep — £150</option><option>Year 5 — 11 Plus Prep — £150</option><option>GCSE Revision (Year 10 &amp; 11) — £200</option></select></div>
                <div><label class="form-label-tpa">Mode *</label><select name="centre" class="form-control-tpa" required><option value="">Select mode</option><option>Online (Zoom)</option><option>In-Centre — Chelmsford</option><option>In-Centre — Chadwell Heath</option></select></div>
                <div><label class="form-label-tpa">Parent/Guardian Name *</label><input type="text" name="name" class="form-control-tpa" placeholder="Your full name" required></div>
                <div><label class="form-label-tpa">Phone / WhatsApp *</label><input type="tel" name="phone" class="form-control-tpa" placeholder="07xxx xxxxxx" required></div>
                <div style="grid-column:1/-1;"><label class="form-label-tpa">Email Address *</label><input type="email" name="email" class="form-control-tpa" placeholder="email@example.com" required></div>
              </div>
              <div class="mt-3"><label class="form-label-tpa">Parent/Guardian Signature (type name to confirm)</label><input type="text" name="signature" class="form-control-tpa" placeholder="Type your full name as signature"></div>
              <button type="submit" class="btn-primary-tpa w-100 mt-4" style="justify-content:center;font-size:1rem;"><i class="fas fa-paper-plane me-2"></i>Submit Booking Request</button>
            </form>
            <div class="bank-details">
              <h5><i class="fas fa-university me-2"></i>Bank Transfer Payment Details</h5>
              <div class="bank-row"><span class="label">Account Name</span><span class="value">Talent Pool Academy</span></div>
              <div class="bank-row"><span class="label">Account Number</span><span class="value">69995444</span></div>
              <div class="bank-row"><span class="label">Sort Code</span><span class="value">08-92-99</span></div>
              <div class="bank-row"><span class="label">Payment Reference</span><span class="value" style="color:var(--gold);">Child Name + HolidayCamp</span></div>
            </div>
            <div class="refund-notice"><i class="fas fa-exclamation-circle mt-1 flex-shrink-0"></i><span>Once a course is booked, <strong>no refund, cancellation, or adjustment</strong> is possible. Please ensure you have selected the correct course and dates before submitting payment.</span></div>
          </div>
        </div>

        <div class="col-lg-5" data-aos="fade-left">
          <div style="position:sticky;top:100px;">
            <h4 style="font-weight:800;color:var(--navy);margin-bottom:1.5rem;">Quick Summary</h4>
            <div style="border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-md);">
              <table style="width:100%;border-collapse:collapse;font-size:.9rem;">
                <thead><tr style="background:var(--navy);color:white;"><th style="padding:.8rem 1rem;">Course</th><th style="padding:.8rem;text-align:center;">Years</th><th style="padding:.8rem;text-align:right;">Fee</th></tr></thead>
                <tbody>
                  <tr style="background:#fff;border-bottom:1px solid #f0f0f0;"><td style="padding:.8rem 1rem;">Year 2 SATs Revision</td><td style="padding:.8rem;text-align:center;"><span style="background:#e3f2fd;color:#1565c0;padding:.15rem .5rem;border-radius:20px;font-size:.78rem;font-weight:700;">Y2</span></td><td style="padding:.8rem;text-align:right;font-weight:800;color:var(--navy);">£150</td></tr>
                  <tr style="background:var(--off-white);border-bottom:1px solid #f0f0f0;"><td style="padding:.8rem 1rem;">Year 6 SATs Revision</td><td style="padding:.8rem;text-align:center;"><span style="background:#e8f5e9;color:#2e7d32;padding:.15rem .5rem;border-radius:20px;font-size:.78rem;font-weight:700;">Y6</span></td><td style="padding:.8rem;text-align:right;font-weight:800;color:var(--navy);">£150</td></tr>
                  <tr style="background:#fff;border-bottom:1px solid #f0f0f0;"><td style="padding:.8rem 1rem;">11 Plus Prep (Yr 3 &amp; 4)</td><td style="padding:.8rem;text-align:center;"><span style="background:rgba(245,166,35,0.15);color:#b8750a;padding:.15rem .5rem;border-radius:20px;font-size:.78rem;font-weight:700;">Y3–4</span></td><td style="padding:.8rem;text-align:right;font-weight:800;color:var(--navy);">£150</td></tr>
                  <tr style="background:var(--off-white);border-bottom:1px solid #f0f0f0;"><td style="padding:.8rem 1rem;">11 Plus Prep (Yr 5)</td><td style="padding:.8rem;text-align:center;"><span style="background:rgba(245,166,35,0.15);color:#b8750a;padding:.15rem .5rem;border-radius:20px;font-size:.78rem;font-weight:700;">Y5</span></td><td style="padding:.8rem;text-align:right;font-weight:800;color:var(--navy);">£150</td></tr>
                  <tr style="background:var(--navy);"><td style="padding:.8rem 1rem;color:white;font-weight:700;">GCSE Revision</td><td style="padding:.8rem;text-align:center;"><span style="background:var(--gold);color:var(--navy);padding:.15rem .5rem;border-radius:20px;font-size:.78rem;font-weight:700;">Y10–11</span></td><td style="padding:.8rem;text-align:right;font-weight:900;color:var(--gold);font-size:1.1rem;">£200</td></tr>
                </tbody>
              </table>
            </div>
            <div style="background:var(--navy);border-radius:var(--radius-lg);padding:1.5rem;margin-top:1.5rem;">
              <h6 style="color:var(--gold);font-weight:700;margin-bottom:1rem;"><i class="fas fa-calendar-alt me-2"></i>Key Dates</h6>
              <div style="color:rgba(255,255,255,0.8);font-size:.9rem;line-height:2;">
                <div><i class="fas fa-check text-gold me-2"></i><strong>Tue 7th April 2026</strong> — Weekday session</div>
                <div><i class="fas fa-check text-gold me-2"></i><strong>Sat 11th April 2026</strong> — Weekend session</div>
                <div style="margin-top:.8rem;padding-top:.8rem;border-top:1px solid rgba(255,255,255,0.1);"><i class="fas fa-exclamation-triangle text-gold me-2"></i><strong style="color:#ff8a80;">Payment deadline: 12 Feb 2026</strong></div>
              </div>
            </div>
            <div style="background:var(--off-white);border-radius:var(--radius-lg);padding:1.5rem;margin-top:1rem;text-align:center;">
              <p style="color:var(--text-muted);font-size:.88rem;margin-bottom:.8rem;">Prefer to book by WhatsApp or call?</p>
              <a href="https://wa.me/447772922943?text=I'd%20like%20to%20book%20the%20Easter%20Holiday%20Course%202026" class="btn-primary-tpa" style="justify-content:center;margin-bottom:.6rem;"><i class="fab fa-whatsapp me-2"></i>WhatsApp Us to Book</a>
              <a href="tel:<?= PHONE ?>" class="btn-secondary-tpa" style="justify-content:center;"><i class="fas fa-phone-alt me-2"></i><?= PHONE ?></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

<script>
  function switchTab(tab, el) {
    document.querySelectorAll('.mode-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-content-panel').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('tab-' + tab).classList.add('active');
  }
  const easterForm = document.getElementById('easterForm');
  if (easterForm) {
    TpaForm.bindBlur(easterForm);
    easterForm.addEventListener('submit', function(e) {
      e.preventDefault();
      if (!TpaForm.validate(easterForm)) return;
      const btn = easterForm.querySelector('button[type="submit"]');
      TpaForm.btnLoading(btn);
      const fd = new FormData(easterForm);
      TpaForm.postLead({
        name:       fd.get('name')       || '',
        email:      fd.get('email')      || '',
        phone:      fd.get('phone')      || '',
        child_name: fd.get('child_name') || '',
        year_group: fd.get('year_group') || '',
        subject:    fd.get('subject')    || 'Easter Holiday Course',
        centre:     fd.get('centre')     || '',
        source:     'Website - Easter Camp'
      })
      .then(() => {
        easterForm.innerHTML = '<div style="text-align:center;padding:2rem 1rem;"><div style="font-size:3rem;margin-bottom:1rem;color:#4caf50;"><i class="fas fa-check-circle" aria-hidden="true"></i></div><h4 style="color:#fff;font-weight:800;">Booking Request Sent!</h4><p style="color:rgba(255,255,255,0.7);">We will confirm your place via WhatsApp or phone within 24 hours. Please ensure payment is made before the deadline.</p><a href="https://wa.me/447772922943" class="btn-primary-tpa mt-3" style="justify-content:center;"><i class="fab fa-whatsapp me-2"></i>Message Us on WhatsApp</a></div>';
      })
      .catch(() => {
        btn.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Try Again';
        btn.style.background = '#dc3545';
        setTimeout(() => TpaForm.btnReset(btn), 3500);
      });
    });
  }
</script>

<?php require_once 'includes/footer.php'; ?>
