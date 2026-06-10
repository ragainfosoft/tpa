<?php
$page_title = 'Summer Holiday Camp 2026';
$meta_description = 'Summer Holiday Camp 2026 — Talent Pool Academy. 4 weeks, 27 Jul–23 Aug. Ages 5–14. Maths, English, Science & 11 Plus. Online, Chadwell Heath & Chelmsford. £200 only.';
$schema_extra = '<script type="application/ld+json">' . json_encode([
  '@context'=>'https://schema.org','@type'=>'Event',
  'name'=>'Summer Holiday Camp 2026 — Talent Pool Academy',
  'description'=>'4-week summer holiday camp for ages 5–14. Maths, English, Science & 11 Plus preparation. Available online, at Chadwell Heath and Chelmsford. Only £200.',
  'url'=>'https://www.talentpoolacademy.com/summer-camp.php',
  'image'=>'https://www.talentpoolacademy.com/images/tpa-og-image.jpg',
  'startDate'=>'2026-07-27',
  'endDate'=>'2026-08-23',
  'eventStatus'=>'https://schema.org/EventScheduled',
  'eventAttendanceMode'=>'https://schema.org/MixedEventAttendanceMode',
  'location'=>[
    ['@type'=>'Place','name'=>'Chadwell Heath Centre','address'=>['@type'=>'PostalAddress','streetAddress'=>'60 High Road, Chadwell Heath','addressLocality'=>'Romford','postalCode'=>'RM6 6PP','addressCountry'=>'GB']],
    ['@type'=>'Place','name'=>'Chelmsford Centre','address'=>['@type'=>'PostalAddress','streetAddress'=>'4B Corporation Road','addressLocality'=>'Chelmsford','postalCode'=>'CM1 2AR','addressCountry'=>'GB']],
    ['@type'=>'VirtualLocation','url'=>'https://www.talentpoolacademy.com/summer-camp.php'],
  ],
  'organizer'=>['@type'=>'EducationalOrganization','name'=>'Talent Pool Academy','url'=>'https://www.talentpoolacademy.com'],
  'offers'=>['@type'=>'Offer','price'=>'200','priceCurrency'=>'GBP','availability'=>'https://schema.org/InStock','url'=>'https://www.talentpoolacademy.com/summer-camp.php'],
  'audience'=>['@type'=>'EducationalAudience','educationalRole'=>'student','audienceType'=>'Ages 5–14'],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . '</script>';
require_once 'includes/header.php';
?>
<style>
  .summer-hero { background: linear-gradient(135deg, #0d2240 0%, #1a3a6b 55%, #0d3b1f 100%); padding: 140px 0 80px; position: relative; overflow: hidden; }
  .summer-hero::before { content: ''; position: absolute; inset: 0; background: radial-gradient(ellipse at 75% 40%, rgba(255,111,0,0.1) 0%, transparent 65%), radial-gradient(ellipse at 20% 80%, rgba(245,166,35,0.07) 0%, transparent 60%); pointer-events: none; }
  .camp-badge { display: inline-flex; align-items: center; gap: .5rem; background: rgba(255,111,0,0.15); border: 1px solid rgba(255,111,0,0.4); color: #ffab40; padding: .45rem 1.1rem; border-radius: 50px; font-size: .82rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 1.2rem; }
  .summer-hero h1 { color: #fff; font-size: clamp(2rem, 4.5vw, 3.2rem); font-weight: 900; line-height: 1.1; }
  .summer-hero h1 span { color: #ffab40; }
  .summer-hero p.lead { color: rgba(255,255,255,0.75); font-size: 1.1rem; max-width: 600px; }
  .hero-stats { display: flex; gap: 2rem; flex-wrap: wrap; margin-top: 2.5rem; }
  .hero-stat { text-align: center; }
  .hero-stat .stat-val { font-size: 2rem; font-weight: 900; color: #ffab40; line-height: 1; }
  .hero-stat .stat-lbl { font-size: .78rem; color: rgba(255,255,255,0.55); text-transform: uppercase; letter-spacing: .05em; margin-top: .25rem; }

  /* Location tabs */
  .loc-tabs { display: flex; gap: 1rem; margin: 2rem 0; flex-wrap: wrap; }
  .loc-tab { flex: 1; min-width: 180px; padding: 1.3rem 1.2rem; border-radius: var(--radius-lg); cursor: pointer; border: 2px solid rgba(255,255,255,0.12); background: rgba(255,255,255,0.05); transition: border-color .25s, background .25s; text-align: center; }
  .loc-tab:hover, .loc-tab.active { border-color: #ffab40; background: rgba(255,111,0,0.1); }
  .loc-tab .loc-icon { font-size: 1.8rem; margin-bottom: .5rem; color: rgba(255,255,255,0.5); }
  .loc-tab.active .loc-icon { color: #ffab40; }
  .loc-tab .loc-title { color: #fff; font-weight: 700; font-size: 1rem; }
  .loc-tab .loc-sub { color: rgba(255,255,255,0.5); font-size: .8rem; margin-top: .2rem; }
  .loc-tab.active .loc-title { color: #ffab40; }
  .tab-content-panel { display: none; }
  .tab-content-panel.active { display: block; }

  /* schedule */
  .schedule-section { padding: 70px 0; }
  .schedule-card { background: #fff; border-radius: var(--radius-lg); box-shadow: var(--shadow-md); overflow: hidden; margin-bottom: 1.5rem; }
  .schedule-header { background: var(--navy); padding: 1.1rem 1.4rem; display: flex; align-items: center; gap: .9rem; }
  .schedule-header .course-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
  .schedule-header h4 { color: #fff; font-weight: 700; margin: 0; font-size: 1rem; }
  .schedule-header .year-badge { background: #ffab40; color: var(--navy); font-weight: 800; font-size: .73rem; padding: .2rem .6rem; border-radius: 20px; white-space: nowrap; margin-left: auto; }
  .schedule-body { padding: 1.3rem 1.4rem; }
  .dates-row { display: flex; gap: .7rem; flex-wrap: wrap; margin-bottom: .8rem; }
  .date-chip { display: flex; align-items: center; gap: .35rem; background: var(--off-white); border-radius: 7px; padding: .45rem .85rem; font-size: .85rem; font-weight: 600; color: var(--navy); }
  .time-chip { display: flex; align-items: center; gap: .35rem; background: rgba(255,111,0,0.08); border: 1px solid rgba(255,111,0,0.25); border-radius: 7px; padding: .45rem .85rem; font-size: .85rem; font-weight: 700; color: #b23c00; }
  .hours-chip { display: flex; align-items: center; gap: .35rem; background: rgba(33,150,243,0.08); border: 1px solid rgba(33,150,243,0.2); border-radius: 7px; padding: .45rem .85rem; font-size: .85rem; font-weight: 700; color: #0d47a1; }
  .fee-tag { background: var(--navy); color: #ffab40; font-weight: 900; font-size: 1.25rem; padding: .35rem .9rem; border-radius: 8px; display: inline-block; }

  /* subjects */
  .subjects-section { background: var(--off-white); padding: 60px 0; }
  .subject-card { background: #fff; border-radius: var(--radius-lg); padding: 1.8rem; box-shadow: var(--shadow-sm); height: 100%; border-top: 4px solid #ffab40; }
  .subject-card h4 { color: var(--navy); font-weight: 800; font-size: 1.05rem; margin-bottom: 1.1rem; }
  .subject-card ul { padding-left: 1.2rem; margin: 0; }
  .subject-card ul li { font-size: .88rem; color: var(--text-muted); line-height: 1.7; }

  /* why-us */
  .why-section { padding: 60px 0; background: var(--navy); }
  .why-card { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-lg); padding: 1.8rem 1.6rem; text-align: center; height: 100%; display: flex; flex-direction: column; align-items: center; }
  .why-card .why-icon { width: 64px; height: 64px; border-radius: 16px; background: rgba(255,171,64,0.12); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.2rem; font-size: 1.6rem; color: #ffab40; flex-shrink: 0; }
  .why-card h5 { color: #fff; font-weight: 700; font-size: .95rem; margin-bottom: .5rem; }
  .why-card p { color: rgba(255,255,255,0.55); font-size: .85rem; margin: 0; }

  /* booking */
  .booking-section { padding: 70px 0; }
  .booking-card { background: var(--navy); border-radius: var(--radius-lg); padding: 2.5rem; }
  .booking-card h3 { color: #fff; font-weight: 800; margin-bottom: .5rem; }
  .booking-card .booking-sub { color: rgba(255,255,255,0.6); margin-bottom: 2rem; }
  .booking-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
  @media(max-width:600px) { .booking-form-grid { grid-template-columns: 1fr; } }
  .form-label-tpa { font-size: .82rem; font-weight: 700; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: .04em; margin-bottom: .35rem; display: block; }
  .form-control-tpa { width: 100%; padding: .7rem 1rem; border-radius: 8px; border: 1.5px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.07); color: #fff; font-size: .92rem; outline: none; transition: border .2s; font-family: inherit; }
  .form-control-tpa::placeholder { color: rgba(255,255,255,0.35); }
  .form-control-tpa:focus { border-color: #ffab40; background: rgba(255,255,255,0.1); }
  .form-control-tpa option { color: var(--navy); background: #fff; }
  .bank-details { background: rgba(255,111,0,0.08); border: 1px solid rgba(255,111,0,0.25); border-radius: var(--radius-md); padding: 1.5rem; margin-top: 2rem; }
  .bank-details h5 { color: #ffab40; font-weight: 700; margin-bottom: 1rem; font-size: .95rem; }
  .bank-row { display: flex; justify-content: space-between; padding: .4rem 0; border-bottom: 1px solid rgba(255,255,255,0.07); font-size: .88rem; }
  .bank-row:last-child { border: none; }
  .bank-row .label { color: rgba(255,255,255,0.55); }
  .bank-row .value { color: #fff; font-weight: 700; }
  .refund-notice { background: rgba(255,80,80,0.1); border: 1px solid rgba(255,80,80,0.25); border-radius: 8px; padding: 1rem 1.2rem; margin-top: 1.5rem; font-size: .85rem; color: rgba(255,200,200,0.9); display: flex; gap: .6rem; align-items: flex-start; }
  .urgency-banner { background: linear-gradient(90deg, #e65100, #ff6d00); border-radius: 8px; padding: 1rem 1.2rem; margin-bottom: 1.5rem; font-size: .9rem; color: #fff; font-weight: 600; display: flex; align-items: center; gap: .6rem; }
  .text-orange { color: #ffab40; }
</style>

  <!-- HERO -->
  <section class="summer-hero">
    <div class="container position-relative">
      <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="background:none;padding:0;">
          <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php" style="color:rgba(255,255,255,0.5);">Home</a></li>
          <li class="breadcrumb-item active" style="color:#ffab40;">Summer Camp 2026</li>
        </ol>
      </nav>
      <div class="camp-badge"><i class="fas fa-fire-alt me-1"></i> Limited Seats — Book Now!</div>
      <h1>Summer Holiday Camp<br><span>27 July – 23 August 2026</span></h1>
      <p class="lead mt-3">4-week intensive summer programme for <strong style="color:#ffab40;">ages 5–14</strong>. Maths, English, Science &amp; 11 Plus preparation. Available <strong style="color:#ffab40;">Online</strong>, <strong style="color:#ffab40;">Chadwell Heath</strong> &amp; <strong style="color:#ffab40;">Chelmsford</strong>.</p>
      <div class="hero-stats">
        <div class="hero-stat"><div class="stat-val">4</div><div class="stat-lbl">Weeks</div></div>
        <div class="hero-stat"><div class="stat-val">5–14</div><div class="stat-lbl">Age Group</div></div>
        <div class="hero-stat"><div class="stat-val">£200</div><div class="stat-lbl">Per Child</div></div>
        <div class="hero-stat"><div class="stat-val">60+</div><div class="stat-lbl">Hours (Chadwell Heath)</div></div>
      </div>
      <!-- Location tabs in hero -->
      <div class="loc-tabs mt-4">
        <div class="loc-tab active" data-tab="chadwell" onclick="switchTab('chadwell',this)">
          <div class="loc-icon"><i class="fas fa-map-marker-alt"></i></div>
          <div class="loc-title">Chadwell Heath</div>
          <div class="loc-sub">Tue–Fri &amp; Saturdays · 60 hrs total</div>
        </div>
        <div class="loc-tab" data-tab="chelmsford" onclick="switchTab('chelmsford',this)">
          <div class="loc-icon"><i class="fas fa-map-marker-alt"></i></div>
          <div class="loc-title">Chelmsford</div>
          <div class="loc-sub">Mon, Wed, Fri &amp; Sundays</div>
        </div>
        <div class="loc-tab" data-tab="online" onclick="switchTab('online',this)">
          <div class="loc-icon"><i class="fas fa-laptop"></i></div>
          <div class="loc-title">Online</div>
          <div class="loc-sub">Mon–Fri · 9:00 – 11:30 AM</div>
        </div>
      </div>
    </div>
  </section>

  <!-- SCHEDULE SECTION -->
  <section class="schedule-section">
    <div class="container">

      <!-- CHADWELL HEATH TAB -->
      <div id="tab-chadwell" class="tab-content-panel active">
        <div class="text-center mb-4" data-aos="fade-up">
          <div class="section-tag"><i class="fas fa-map-marker-alt"></i> Chadwell Heath</div>
          <h2 class="section-title">Chadwell Heath <span>Schedule</span></h2>
          <p style="color:var(--text-muted);">60 High Road, Chadwell Heath, RM6 6PP &nbsp;·&nbsp; <strong>60 hours total</strong> over 4 weeks</p>
          <div class="divider-gold"></div>
        </div>
        <div class="row g-4">
          <div class="col-lg-6" data-aos="fade-up">
            <div class="schedule-card">
              <div class="schedule-header">
                <div class="course-icon" style="background:rgba(255,111,0,0.2);color:#ffab40;"><i class="fas fa-calendar-week"></i></div>
                <div class="flex-grow-1"><h4>Weekday Sessions</h4><small style="color:rgba(255,255,255,0.55);">Tuesday to Friday</small></div>
                <div class="year-badge">4 days/week</div>
              </div>
              <div class="schedule-body">
                <div class="dates-row">
                  <div class="date-chip"><i class="fas fa-calendar" style="color:#e65100;"></i> Tuesday – Friday</div>
                  <div class="time-chip"><i class="fas fa-clock"></i> 4:00 PM – 7:00 PM</div>
                </div>
                <p style="font-size:.88rem;color:var(--text-muted);margin:0;">Runs every week 27 Jul – 23 Aug 2026 (excluding bank holidays)</p>
              </div>
            </div>
          </div>
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="80">
            <div class="schedule-card">
              <div class="schedule-header">
                <div class="course-icon" style="background:rgba(245,166,35,0.2);color:var(--gold);"><i class="fas fa-calendar-day"></i></div>
                <div class="flex-grow-1"><h4>Saturday Sessions</h4><small style="color:rgba(255,255,255,0.55);">3-hour sessions — choose one batch</small></div>
                <div class="year-badge">3 hrs</div>
              </div>
              <div class="schedule-body">
                <div class="dates-row">
                  <div class="date-chip"><i class="fas fa-calendar" style="color:#e65100;"></i> Every Saturday</div>
                </div>
                <div class="dates-row">
                  <div class="time-chip"><i class="fas fa-clock"></i> Batch 1: 10:00 AM – 1:00 PM</div>
                </div>
                <div class="dates-row">
                  <div class="time-chip"><i class="fas fa-clock"></i> Batch 2: 1:00 PM – 4:00 PM</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12" data-aos="fade-up" data-aos-delay="100">
            <div class="schedule-card" style="border:2px solid #ffab40;">
              <div class="schedule-header" style="background:linear-gradient(90deg,var(--navy),#1a3a6b);">
                <div class="course-icon" style="background:rgba(255,111,0,0.25);color:#ffab40;"><i class="fas fa-star"></i></div>
                <div class="flex-grow-1"><h4>Full Programme Summary — Chadwell Heath</h4><small style="color:rgba(255,255,255,0.55);">Ages 5–14 · Maths, English, Science &amp; 11 Plus</small></div>
                <div class="year-badge" style="background:#ffab40;">£200</div>
              </div>
              <div class="schedule-body">
                <div class="dates-row">
                  <div class="date-chip"><i class="fas fa-calendar-alt" style="color:#e65100;"></i> 27 Jul – 23 Aug 2026</div>
                  <div class="hours-chip"><i class="fas fa-hourglass-half"></i> 60 Hours Total</div>
                  <div class="fee-tag">£200 only</div>
                </div>
                <p style="font-size:.88rem;color:var(--text-muted);margin:.5rem 0 0;">Includes Tue–Fri afternoon sessions + Saturday morning/afternoon batch. Small groups, expert teachers.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- CHELMSFORD TAB -->
      <div id="tab-chelmsford" class="tab-content-panel">
        <div class="text-center mb-4" data-aos="fade-up">
          <div class="section-tag"><i class="fas fa-map-marker-alt"></i> Chelmsford</div>
          <h2 class="section-title">Chelmsford <span>Schedule</span></h2>
          <p style="color:var(--text-muted);">4B Corporation Road, Chelmsford, CM1 2AR</p>
          <div class="divider-gold"></div>
        </div>
        <div class="row g-4">
          <div class="col-lg-6" data-aos="fade-up">
            <div class="schedule-card">
              <div class="schedule-header">
                <div class="course-icon" style="background:rgba(76,175,80,0.2);color:#a5d6a7;"><i class="fas fa-calendar-week"></i></div>
                <div class="flex-grow-1"><h4>Weekday &amp; Friday Sessions</h4><small style="color:rgba(255,255,255,0.55);">Monday, Wednesday &amp; Friday</small></div>
                <div class="year-badge">3 days/week</div>
              </div>
              <div class="schedule-body">
                <div class="dates-row">
                  <div class="date-chip"><i class="fas fa-calendar" style="color:#e65100;"></i> Mon, Wed &amp; Fri</div>
                  <div class="time-chip"><i class="fas fa-clock"></i> 5:00 PM – 7:30 PM</div>
                </div>
                <p style="font-size:.88rem;color:var(--text-muted);margin:0;">2.5-hour evening sessions every Mon, Wed &amp; Fri throughout the 4-week camp.</p>
              </div>
            </div>
          </div>
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="80">
            <div class="schedule-card">
              <div class="schedule-header">
                <div class="course-icon" style="background:rgba(245,166,35,0.2);color:var(--gold);"><i class="fas fa-calendar-day"></i></div>
                <div class="flex-grow-1"><h4>Sunday Sessions</h4><small style="color:rgba(255,255,255,0.55);">Evening + Bonus Full-Day</small></div>
                <div class="year-badge">Every Sun</div>
              </div>
              <div class="schedule-body">
                <div class="dates-row">
                  <div class="date-chip"><i class="fas fa-calendar" style="color:#e65100;"></i> Sunday evenings</div>
                  <div class="time-chip"><i class="fas fa-clock"></i> 5:00 PM – 7:30 PM</div>
                </div>
                <div class="dates-row">
                  <div class="date-chip" style="background:#fff3e0;color:#e65100;font-weight:700;"><i class="fas fa-star" style="color:#ffab40;"></i> Bonus Full Day</div>
                  <div class="time-chip"><i class="fas fa-clock"></i> 10:00 AM – 5:00 PM</div>
                </div>
                <p style="font-size:.83rem;color:var(--text-muted);margin:.4rem 0 0;">Full-day Sunday sessions are an added bonus — packed with revision, practice papers &amp; fun activities.</p>
              </div>
            </div>
          </div>
          <div class="col-12" data-aos="fade-up" data-aos-delay="100">
            <div class="schedule-card" style="border:2px solid #ffab40;">
              <div class="schedule-header" style="background:linear-gradient(90deg,var(--navy),#1a3a6b);">
                <div class="course-icon" style="background:rgba(255,111,0,0.25);color:#ffab40;"><i class="fas fa-star"></i></div>
                <div class="flex-grow-1"><h4>Full Programme Summary — Chelmsford</h4><small style="color:rgba(255,255,255,0.55);">Ages 5–14 · Maths, English, Science &amp; 11 Plus</small></div>
                <div class="year-badge" style="background:#ffab40;">£200</div>
              </div>
              <div class="schedule-body">
                <div class="dates-row">
                  <div class="date-chip"><i class="fas fa-calendar-alt" style="color:#e65100;"></i> 27 Jul – 23 Aug 2026</div>
                  <div class="time-chip"><i class="fas fa-clock"></i> Evenings 5–7:30 PM + Sunday full day</div>
                  <div class="fee-tag">£200 only</div>
                </div>
                <p style="font-size:.88rem;color:var(--text-muted);margin:.5rem 0 0;">Mon, Wed, Fri &amp; Sun evening sessions plus bonus full-day Sundays. Expert-led small group teaching.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ONLINE TAB -->
      <div id="tab-online" class="tab-content-panel">
        <div class="text-center mb-4" data-aos="fade-up">
          <div class="section-tag"><i class="fas fa-laptop"></i> Online via Zoom</div>
          <h2 class="section-title">Online <span>Schedule</span></h2>
          <p style="color:var(--text-muted);">Zoom link sent on booking confirmation. Join from anywhere in the UK or abroad.</p>
          <div class="divider-gold"></div>
        </div>
        <div class="row g-4 justify-content-center">
          <div class="col-lg-8" data-aos="fade-up">
            <div class="schedule-card" style="border:2px solid #ffab40;">
              <div class="schedule-header" style="background:linear-gradient(90deg,var(--navy),#1a3a6b);">
                <div class="course-icon" style="background:rgba(33,150,243,0.2);color:#90caf9;"><i class="fas fa-video"></i></div>
                <div class="flex-grow-1"><h4>Online Morning Sessions</h4><small style="color:rgba(255,255,255,0.55);">Monday to Friday · Live on Zoom</small></div>
                <div class="year-badge" style="background:#ffab40;">£200</div>
              </div>
              <div class="schedule-body">
                <div class="dates-row">
                  <div class="date-chip"><i class="fas fa-calendar" style="color:#e65100;"></i> Monday – Friday</div>
                  <div class="time-chip"><i class="fas fa-clock"></i> 9:00 AM – 11:30 AM</div>
                  <div class="date-chip"><i class="fas fa-calendar-alt" style="color:#e65100;"></i> 27 Jul – 23 Aug</div>
                </div>
                <p style="font-size:.88rem;color:var(--text-muted);margin:.5rem 0 0;">2.5-hour live interactive sessions Mon–Fri. Full curriculum coverage: Maths, English, Science &amp; 11 Plus prep. Recordings available on request.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- SUBJECTS COVERED -->
  <section class="subjects-section">
    <div class="container">
      <div class="text-center mb-5" data-aos="fade-up">
        <div class="section-tag"><i class="fas fa-book-open"></i> What We Cover</div>
        <h2 class="section-title">Topics <span>Covered</span></h2>
        <p style="color:var(--text-muted);max-width:520px;margin:0 auto;">Comprehensive curriculum across all key subjects, tailored to every age group.</p>
        <div class="divider-gold"></div>
      </div>
      <div class="row g-4">
        <div class="col-md-6 col-lg-3" data-aos="fade-up">
          <div class="subject-card">
            <h4><i class="fas fa-calculator text-orange me-2"></i>Maths</h4>
            <ul>
              <li>Number &amp; arithmetic</li>
              <li>Fractions, decimals &amp; percentages</li>
              <li>Algebra (KS3+)</li>
              <li>Geometry &amp; measurement</li>
              <li>Problem-solving &amp; reasoning</li>
              <li>Past paper practice</li>
            </ul>
          </div>
        </div>
        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="60">
          <div class="subject-card">
            <h4><i class="fas fa-pen text-orange me-2"></i>English</h4>
            <ul>
              <li>Reading comprehension</li>
              <li>Creative &amp; persuasive writing</li>
              <li>Grammar, punctuation &amp; spelling</li>
              <li>Vocabulary building</li>
              <li>Classic text analysis (KS3+)</li>
              <li>Exam-style practice</li>
            </ul>
          </div>
        </div>
        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="120">
          <div class="subject-card">
            <h4><i class="fas fa-flask text-orange me-2"></i>Science</h4>
            <ul>
              <li>Biology — cells &amp; living things</li>
              <li>Chemistry — elements &amp; reactions</li>
              <li>Physics — forces &amp; energy</li>
              <li>Scientific method &amp; experiments</li>
              <li>Curriculum-aligned topics</li>
              <li>Fun practicals &amp; activities</li>
            </ul>
          </div>
        </div>
        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="180">
          <div class="subject-card" style="border-top-color:var(--gold);">
            <h4><i class="fas fa-star" style="color:var(--gold);margin-right:.5rem;"></i>11 Plus Prep</h4>
            <ul>
              <li>Verbal Reasoning (VR)</li>
              <li>Non-Verbal Reasoning (NVR)</li>
              <li>Advanced Maths techniques</li>
              <li>Comprehension strategies</li>
              <li>Timed mock practice</li>
              <li>Exam-technique coaching</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- WHY CHOOSE US -->
  <section class="why-section">
    <div class="container">
      <div class="text-center mb-5" data-aos="fade-up">
        <div class="section-tag" style="color:rgba(255,255,255,0.7);border-color:rgba(255,255,255,0.2);"><i class="fas fa-trophy me-1" style="color:#ffab40;"></i> Why Choose Us</div>
        <h2 class="section-title" style="color:#fff;">Why Our Summer Camp <span style="color:#ffab40;">Stands Out</span></h2>
        <div class="divider-gold"></div>
      </div>
      <div class="row g-4">
        <div class="col-sm-6 col-lg-3" data-aos="fade-up">
          <div class="why-card">
            <div class="why-icon"><i class="fas fa-fire-alt"></i></div>
            <h5>Always Oversubscribed</h5>
            <p>Every year our camp fills up fast. Book early to avoid missing out — demand is always higher than places.</p>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="60">
          <div class="why-card">
            <div class="why-icon"><i class="fas fa-users"></i></div>
            <h5>Small Group Teaching</h5>
            <p>Maximum 6–8 students per group ensures every child gets personal attention and support.</p>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="120">
          <div class="why-card">
            <div class="why-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <h5>Expert Teachers</h5>
            <p>Our qualified, experienced teachers know exactly what each year group needs to excel.</p>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="180">
          <div class="why-card">
            <div class="why-icon"><i class="fas fa-trophy"></i></div>
            <h5>Proven Track Record</h5>
            <p>16+ years of excellence. Over 90% of our 11 Plus students gain grammar school places.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- BOOKING SECTION -->
  <section class="booking-section" id="book">
    <div class="container">
      <div class="row g-5 align-items-start">
        <div class="col-lg-7" data-aos="fade-right">
          <div class="booking-card">
            <h3><i class="fas fa-clipboard-list me-2" style="color:#ffab40;"></i>Book Your Place</h3>
            <p class="booking-sub">Complete the form or WhatsApp/Text us on <strong style="color:#ffab40;">07772 922943</strong> to reserve your child's place.</p>
            <div class="urgency-banner"><i class="fas fa-fire-alt"></i> Limited seats available — this camp sells out every year. Book now!</div>
            <form id="summerForm">
              <div class="booking-form-grid">
                <div><label class="form-label-tpa">Student's Name *</label><input type="text" name="child_name" class="form-control-tpa" placeholder="Full name" required></div>
                <div><label class="form-label-tpa">Child's Age / Year Group *</label><select name="year_group" class="form-control-tpa" required><option value="">Select age / year</option><option>Age 5 (Reception)</option><option>Year 1 (Age 6)</option><option>Year 2 (Age 7)</option><option>Year 3 (Age 8)</option><option>Year 4 (Age 9)</option><option>Year 5 (Age 10)</option><option>Year 6 (Age 11)</option><option>Year 7 (Age 12)</option><option>Year 8 (Age 13)</option><option>Year 9 (Age 14)</option><option>Year 10 (Age 15)</option><option>Year 11 (Age 16)</option><option>Year 12 – A-Level</option><option>Adult</option></select></div>
                <div><label class="form-label-tpa">Location *</label><select name="centre" class="form-control-tpa" required><option value="">Select location</option><option>Chadwell Heath (Tue–Fri + Saturdays)</option><option>Chelmsford (Mon, Wed, Fri &amp; Sundays)</option><option>Online (Mon–Fri, 9–11:30 AM)</option></select></div>
                <div><label class="form-label-tpa">Saturday Batch (Chadwell Heath only)</label><select name="saturday_batch" class="form-control-tpa"><option value="">N/A or select batch</option><option>Batch 1 — 10:00 AM to 1:00 PM</option><option>Batch 2 — 1:00 PM to 4:00 PM</option></select></div>
                <div><label class="form-label-tpa">Parent/Guardian Name *</label><input type="text" name="name" class="form-control-tpa" placeholder="Your full name" required></div>
                <div><label class="form-label-tpa">Phone / WhatsApp *</label><input type="tel" name="phone" class="form-control-tpa" placeholder="07xxx xxxxxx" required></div>
                <div style="grid-column:1/-1;"><label class="form-label-tpa">Email Address *</label><input type="email" name="email" class="form-control-tpa" placeholder="email@example.com" required></div>
              </div>
              <div class="mt-3"><label class="form-label-tpa">Any additional notes or questions</label><textarea name="notes" class="form-control-tpa" rows="3" placeholder="e.g. specific topics, accessibility needs…" style="resize:vertical;"></textarea></div>
              <button type="submit" class="btn-primary-tpa w-100 mt-4" style="justify-content:center;font-size:1rem;background:linear-gradient(135deg,#e65100,#ff6d00);border-color:transparent;"><i class="fas fa-paper-plane me-2"></i>Submit Booking Request</button>
            </form>
            <div class="bank-details">
              <h5><i class="fas fa-university me-2"></i>Bank Transfer Payment Details</h5>
              <div class="bank-row"><span class="label">Account Name</span><span class="value">Talent Pool Academy</span></div>
              <div class="bank-row"><span class="label">Account Number</span><span class="value">69995444</span></div>
              <div class="bank-row"><span class="label">Sort Code</span><span class="value">08-92-99</span></div>
              <div class="bank-row"><span class="label">Amount</span><span class="value" style="color:#ffab40;">£200 per child</span></div>
              <div class="bank-row"><span class="label">Payment Reference</span><span class="value" style="color:#ffab40;">Child Name + SummerCamp</span></div>
            </div>
            <div class="refund-notice"><i class="fas fa-exclamation-circle mt-1 flex-shrink-0"></i><span>Once a place is booked, <strong>no refund, cancellation, or adjustment</strong> is possible. Please ensure you have selected the correct location and dates before submitting payment.</span></div>
          </div>
        </div>

        <div class="col-lg-5" data-aos="fade-left">
          <div style="position:sticky;top:100px;">
            <h4 style="font-weight:800;color:var(--navy);margin-bottom:1.5rem;">Quick Summary</h4>
            <div style="border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-md);">
              <table style="width:100%;border-collapse:collapse;font-size:.9rem;">
                <thead><tr style="background:var(--navy);color:#fff;"><th style="padding:.8rem 1rem;">Detail</th><th style="padding:.8rem;text-align:right;">Info</th></tr></thead>
                <tbody>
                  <tr style="background:#fff;border-bottom:1px solid #f0f0f0;"><td style="padding:.8rem 1rem;">Duration</td><td style="padding:.8rem;text-align:right;font-weight:700;">27 Jul – 23 Aug 2026</td></tr>
                  <tr style="background:var(--off-white);border-bottom:1px solid #f0f0f0;"><td style="padding:.8rem 1rem;">Age Group</td><td style="padding:.8rem;text-align:right;font-weight:700;">5 – 14 years</td></tr>
                  <tr style="background:#fff;border-bottom:1px solid #f0f0f0;"><td style="padding:.8rem 1rem;">Subjects</td><td style="padding:.8rem;text-align:right;font-weight:700;">Maths, English, Science, 11+</td></tr>
                  <tr style="background:var(--off-white);border-bottom:1px solid #f0f0f0;"><td style="padding:.8rem 1rem;">Chadwell Heath</td><td style="padding:.8rem;text-align:right;font-weight:700;">60 hours total</td></tr>
                  <tr style="background:#fff;border-bottom:1px solid #f0f0f0;"><td style="padding:.8rem 1rem;">Chelmsford</td><td style="padding:.8rem;text-align:right;font-weight:700;">Eves + Full-day Sundays</td></tr>
                  <tr style="background:var(--off-white);border-bottom:1px solid #f0f0f0;"><td style="padding:.8rem 1rem;">Online</td><td style="padding:.8rem;text-align:right;font-weight:700;">Mon–Fri 9–11:30 AM</td></tr>
                  <tr style="background:var(--navy);"><td style="padding:.8rem 1rem;color:#fff;font-weight:700;">Fee per Child</td><td style="padding:.8rem;text-align:right;font-weight:900;color:#ffab40;font-size:1.2rem;">£200</td></tr>
                </tbody>
              </table>
            </div>
            <div style="background:var(--navy);border-radius:var(--radius-lg);padding:1.5rem;margin-top:1.5rem;">
              <h6 style="color:#ffab40;font-weight:700;margin-bottom:1rem;"><i class="fas fa-calendar-alt me-2"></i>Camp Dates</h6>
              <div style="color:rgba(255,255,255,0.8);font-size:.9rem;line-height:2.2;">
                <div><i class="fas fa-check" style="color:#ffab40;margin-right:.6rem;"></i><strong>Week 1:</strong> 27–31 July 2026</div>
                <div><i class="fas fa-check" style="color:#ffab40;margin-right:.6rem;"></i><strong>Week 2:</strong> 3–7 August 2026</div>
                <div><i class="fas fa-check" style="color:#ffab40;margin-right:.6rem;"></i><strong>Week 3:</strong> 10–14 August 2026</div>
                <div><i class="fas fa-check" style="color:#ffab40;margin-right:.6rem;"></i><strong>Week 4:</strong> 17–23 August 2026</div>
              </div>
            </div>
            <div style="background:var(--off-white);border-radius:var(--radius-lg);padding:1.5rem;margin-top:1rem;text-align:center;">
              <p style="color:var(--text-muted);font-size:.88rem;margin-bottom:.8rem;">Prefer to book directly?</p>
              <a href="https://wa.me/447772922943?text=I'd%20like%20to%20book%20the%20Summer%20Holiday%20Camp%202026" class="btn-primary-tpa" style="justify-content:center;margin-bottom:.6rem;background:linear-gradient(135deg,#e65100,#ff6d00);border-color:transparent;"><i class="fab fa-whatsapp me-2"></i>WhatsApp Us to Book</a>
              <a href="tel:<?= PHONE ?>" class="btn-secondary-tpa" style="justify-content:center;"><i class="fas fa-phone-alt me-2"></i><?= PHONE ?></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

<script>
  function switchTab(tab, el) {
    document.querySelectorAll('.loc-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-content-panel').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('tab-' + tab).classList.add('active');
  }

  const summerForm = document.getElementById('summerForm');
  if (summerForm) {
    TpaForm.bindBlur(summerForm);
    summerForm.addEventListener('submit', function(e) {
      e.preventDefault();
      if (!TpaForm.validate(summerForm)) return;
      const btn = summerForm.querySelector('button[type="submit"]');
      TpaForm.btnLoading(btn);
      const fd = new FormData(summerForm);
      TpaForm.postLead({
        name:       fd.get('name')           || '',
        email:      fd.get('email')          || '',
        phone:      fd.get('phone')          || '',
        child_name: fd.get('child_name')     || '',
        year_group: fd.get('year_group')     || '',
        subject:    'Summer Holiday Camp 2026',
        centre:     fd.get('centre')         || '',
        notes:      (fd.get('saturday_batch') ? 'Saturday batch: ' + fd.get('saturday_batch') + '. ' : '') + (fd.get('notes') || ''),
        source:     'Website - Summer Camp 2026'
      })
      .then(() => {
        summerForm.innerHTML = '<div style="text-align:center;padding:2rem 1rem;"><div style="font-size:3rem;margin-bottom:1rem;color:#ffab40;"><i class="fas fa-check-circle" aria-hidden="true"></i></div><h4 style="color:#fff;font-weight:800;">Booking Request Sent!</h4><p style="color:rgba(255,255,255,0.7);">We will confirm your place via WhatsApp or phone within 24 hours. Limited seats — we\'ll be in touch soon!</p><a href="https://wa.me/447772922943" class="btn-primary-tpa mt-3" style="justify-content:center;background:linear-gradient(135deg,#e65100,#ff6d00);border-color:transparent;"><i class="fab fa-whatsapp me-2"></i>Message Us on WhatsApp</a></div>';
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
