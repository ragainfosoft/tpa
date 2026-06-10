<?php
$page_title = 'All Courses & Programmes';
$meta_description = 'Explore all courses at Talent Pool Academy — KS1, KS2, 11 Plus, SATs, KS3, GCSE, A-level and Adult Learning in Chadwell Heath and Chelmsford.';
$schema_extra = '<script type="application/ld+json">' . json_encode([
  '@context'=>'https://schema.org','@type'=>'ItemList',
  'name'=>'All Courses at Talent Pool Academy',
  'url'=>'https://www.talentpoolacademy.com/courses.php',
  'description'=>'Full range of tuition programmes from KS1 to A-Level and Adult Learning, delivered at Chadwell Heath and Chelmsford centres and online.',
  'itemListElement'=>[
    ['@type'=>'ListItem','position'=>1,'name'=>'11 Plus Preparation','url'=>'https://www.talentpoolacademy.com/course-11plus.php'],
    ['@type'=>'ListItem','position'=>2,'name'=>'SATs Preparation','url'=>'https://www.talentpoolacademy.com/course-sats.php'],
    ['@type'=>'ListItem','position'=>3,'name'=>'Key Stage 1 Tuition','url'=>'https://www.talentpoolacademy.com/course-ks1.php'],
    ['@type'=>'ListItem','position'=>4,'name'=>'Key Stage 2 Tuition','url'=>'https://www.talentpoolacademy.com/course-ks2.php'],
    ['@type'=>'ListItem','position'=>5,'name'=>'Key Stage 3 Tuition','url'=>'https://www.talentpoolacademy.com/course-ks3.php'],
    ['@type'=>'ListItem','position'=>6,'name'=>'GCSE Tuition','url'=>'https://www.talentpoolacademy.com/course-gcse.php'],
    ['@type'=>'ListItem','position'=>7,'name'=>'A-Level Tuition','url'=>'https://www.talentpoolacademy.com/course-alevel.php'],
    ['@type'=>'ListItem','position'=>8,'name'=>'Adult Learning','url'=>'https://www.talentpoolacademy.com/course-adult.php'],
  ],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . '</script>';
require_once 'includes/feedback-data.php';
require_once 'includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <div class="col-lg-8">
      <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php">Home</a></li>
          <li class="breadcrumb-item active">Courses</li>
        </ol>
      </nav>
      <h1>Our <span style="color:var(--gold);">Programmes</span></h1>
      <p>Tailored tuition from Year 1 through to A-level and Adult Learning — covering English, Maths, Science and specialist 11 Plus preparation for Grammar &amp; Independent schools.</p>
    </div>
  </div>
</section>

<!-- CURRICULUM TRUST BAR -->
<div style="background:var(--navy);border-bottom:3px solid var(--gold);padding:1.4rem 0;">
  <div class="container">
    <div class="d-flex flex-wrap align-items-center justify-content-center gap-3 text-center" style="font-size:1.05rem;font-weight:700;color:rgba(255,255,255,0.95);letter-spacing:.01em;">
      <span><i class="fas fa-check-circle text-gold me-2"></i>Follows the National Curriculum</span>
      <span style="color:rgba(255,255,255,0.25);">|</span>
      <span><i class="fas fa-check-circle text-gold me-2"></i>All Major Exam Boards — AQA · Edexcel · OCR · CCEA</span>
      <span style="color:rgba(255,255,255,0.25);">|</span>
      <span><i class="fas fa-check-circle text-gold me-2"></i>DBS Checked Teachers</span>
      <span style="color:rgba(255,255,255,0.25);">|</span>
      <span><i class="fas fa-check-circle text-gold me-2"></i>Trusted Since 2008</span>
    </div>
  </div>
</div>

<section class="section-pad">
  <div class="container">
    <!-- Filter -->
    <div class="filter-tabs" id="courseFilter" data-aos="fade-up">
      <button class="filter-tab active" data-filter="all">All Courses</button>
      <button class="filter-tab" data-filter="ks1">KS1 · Year 1–2</button>
      <button class="filter-tab" data-filter="ks2">KS2 · Year 3–6</button>
      <button class="filter-tab" data-filter="sats">SATs</button>
      <button class="filter-tab" data-filter="11plus">11 Plus</button>
      <button class="filter-tab" data-filter="ks3">KS3 · Year 7–9</button>
      <button class="filter-tab" data-filter="gcse">GCSE</button>
      <button class="filter-tab" data-filter="alevel">A-Level</button>
      <button class="filter-tab" data-filter="adult">Adult Learning</button>
    </div>

    <div class="row g-4" id="courseGrid">

      <!-- KS1: Phonics -->
      <div class="col-md-6 col-lg-4 course-item" data-category="ks1" data-aos="fade-up">
        <div class="course-card h-100">
          <div style="background:linear-gradient(135deg,#fff8e7,#fffde0);padding:2rem 2rem 0;border-bottom:3px solid #F5A623;">
            <div class="course-card-icon" style="background:rgba(245,166,35,0.15);"><i class="fas fa-spell-check" style="color:#F5A623;"></i></div>
            <span class="course-badge" style="background:rgba(245,166,35,0.15);color:#c78c1a;">KS1 · Year 1–2</span>
            <h3 style="font-size:1.2rem;font-weight:700;color:var(--navy);margin-bottom:.5rem;">Phonics &amp; Early Reading</h3>
          </div>
          <div class="course-card-body">
            <p style="color:var(--text-muted);font-size:.92rem;">Structured phonics sessions &amp; literacy aligned with National Curriculum — building strong decoding skills and a love of reading.</p>
            <ul style="color:var(--text-muted);font-size:.88rem;padding-left:1.2rem;">
              <li>Phase 3–6 phonics</li>
              <li>High frequency words mastery</li>
              <li>Reading fluency exercises</li>
              <li>Comprehension activities</li>
            </ul>
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top:1px solid var(--gray-light);">
              <span style="font-size:.82rem;color:var(--gray);"><i class="fas fa-laptop-house me-1"></i>Online &amp; Face to Face</span>
              <a href="<?= SITE_URL ?>/course-ks1.php" class="btn-primary-tpa" style="font-size:.82rem;padding:.45rem 1rem;">View Course →</a>
            </div>
          </div>
        </div>
      </div>

      <!-- KS1: Maths -->
      <div class="col-md-6 col-lg-4 course-item" data-category="ks1" data-aos="fade-up" data-aos-delay="100">
        <div class="course-card h-100">
          <div style="background:linear-gradient(135deg,#fff8e7,#fffde0);padding:2rem 2rem 0;border-bottom:3px solid #F5A623;">
            <div class="course-card-icon" style="background:rgba(245,166,35,0.15);"><i class="fas fa-plus-circle" style="color:#F5A623;"></i></div>
            <span class="course-badge" style="background:rgba(245,166,35,0.15);color:#c78c1a;">KS1 · Year 1–2</span>
            <h3 style="font-size:1.2rem;font-weight:700;color:var(--navy);margin-bottom:.5rem;">KS1 Maths Foundations</h3>
          </div>
          <div class="course-card-body">
            <p style="color:var(--text-muted);font-size:.92rem;">Number recognition, addition, subtraction and shape work aligned to the National Curriculum.</p>
            <ul style="color:var(--text-muted);font-size:.88rem;padding-left:1.2rem;">
              <li>Number bonds to 20</li>
              <li>Times tables introduction</li>
              <li>Shape, space &amp; measures</li>
              <li>Problem-solving activities</li>
            </ul>
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top:1px solid var(--gray-light);">
              <span style="font-size:.82rem;color:var(--gray);"><i class="fas fa-laptop-house me-1"></i>Online &amp; Face to Face</span>
              <a href="<?= SITE_URL ?>/course-ks1.php" class="btn-primary-tpa" style="font-size:.82rem;padding:.45rem 1rem;">View Course →</a>
            </div>
          </div>
        </div>
      </div>

      <!-- KS2: English -->
      <div class="col-md-6 col-lg-4 course-item" data-category="ks2" data-aos="fade-up">
        <div class="course-card h-100">
          <div style="background:linear-gradient(135deg,#e8f0fe,#d2e3fc);padding:2rem 2rem 0;border-bottom:3px solid var(--navy);">
            <div class="course-card-icon" style="background:rgba(10,22,40,0.1);"><i class="fas fa-pen-nib" style="color:var(--navy);"></i></div>
            <span class="course-badge" style="background:rgba(10,22,40,0.08);color:var(--navy);">KS2 · Year 3–6</span>
            <h3 style="font-size:1.2rem;font-weight:700;color:var(--navy);margin-bottom:.5rem;">KS2 English</h3>
          </div>
          <div class="course-card-body">
            <p style="color:var(--text-muted);font-size:.92rem;">Grammar, comprehension, creative and analytical writing — building confident SATs performance.</p>
            <ul style="color:var(--text-muted);font-size:.88rem;padding-left:1.2rem;">
              <li>Grammar &amp; punctuation (SPAG)</li>
              <li>Reading comprehension techniques</li>
              <li>Creative &amp; narrative writing</li>
              <li>Vocabulary enhancement</li>
            </ul>
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top:1px solid var(--gray-light);">
              <span style="font-size:.82rem;color:var(--gray);"><i class="fas fa-laptop-house me-1"></i>Online &amp; Face to Face</span>
              <a href="<?= SITE_URL ?>/course-ks2.php" class="btn-primary-tpa" style="font-size:.82rem;padding:.45rem 1rem;">View Course →</a>
            </div>
          </div>
        </div>
      </div>

      <!-- KS2: Maths -->
      <div class="col-md-6 col-lg-4 course-item" data-category="ks2" data-aos="fade-up" data-aos-delay="100">
        <div class="course-card h-100">
          <div style="background:linear-gradient(135deg,#e8f0fe,#d2e3fc);padding:2rem 2rem 0;border-bottom:3px solid var(--navy);">
            <div class="course-card-icon" style="background:rgba(10,22,40,0.1);"><i class="fas fa-square-root-alt" style="color:var(--navy);"></i></div>
            <span class="course-badge" style="background:rgba(10,22,40,0.08);color:var(--navy);">KS2 · Year 3–6</span>
            <h3 style="font-size:1.2rem;font-weight:700;color:var(--navy);margin-bottom:.5rem;">KS2 Maths</h3>
          </div>
          <div class="course-card-body">
            <p style="color:var(--text-muted);font-size:.92rem;">From multiplication and fractions to ratio and algebra — building fluency, reasoning and problem-solving aligned with National Curriculum.</p>
            <ul style="color:var(--text-muted);font-size:.88rem;padding-left:1.2rem;">
              <li>Times tables &amp; number fluency</li>
              <li>Fractions, decimals &amp; percentages</li>
              <li>Geometry &amp; measurement</li>
              <li>Reasoning &amp; problem-solving</li>
            </ul>
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top:1px solid var(--gray-light);">
              <span style="font-size:.82rem;color:var(--gray);"><i class="fas fa-laptop-house me-1"></i>Online &amp; Face to Face</span>
              <a href="<?= SITE_URL ?>/course-ks2.php" class="btn-primary-tpa" style="font-size:.82rem;padding:.45rem 1rem;">View Course →</a>
            </div>
          </div>
        </div>
      </div>

      <!-- SATs -->
      <div class="col-md-6 col-lg-4 course-item" data-category="sats ks2" data-aos="fade-up" data-aos-delay="200">
        <div class="course-card h-100" style="border:2px solid rgba(245,166,35,0.5);position:relative;">
          <div style="position:absolute;top:-12px;left:50%;transform:translateX(-50%);background:var(--gold);color:var(--navy);font-size:.72rem;font-weight:800;padding:4px 14px;border-radius:20px;white-space:nowrap;"><i class="fas fa-star me-1" aria-hidden="true"></i>MOST POPULAR</div>
          <div style="background:linear-gradient(135deg,#fffde0,#fff8e7);padding:2rem 2rem 0;border-bottom:3px solid #F5A623;">
            <div class="course-card-icon" style="background:rgba(245,166,35,0.2);"><i class="fas fa-pencil-alt" style="color:#F5A623;"></i></div>
            <span class="course-badge" style="background:rgba(245,166,35,0.2);color:#c78c1a;">Year 2 &amp; Year 6</span>
            <h3 style="font-size:1.2rem;font-weight:700;color:var(--navy);margin-bottom:.5rem;">SATs Preparation</h3>
          </div>
          <div class="course-card-body">
            <p style="color:var(--text-muted);font-size:.92rem;">Intensive programme for Year 6 SATs with past paper practice, timed tests, and expert exam technique coaching aligned with National Curriculum.</p>
            <ul style="color:var(--text-muted);font-size:.88rem;padding-left:1.2rem;">
              <li>Full SATs past paper practice</li>
              <li>Timed exam conditions</li>
              <li>English &amp; Maths</li>
              <li>Detailed feedback &amp; analysis</li>
            </ul>
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top:1px solid var(--gray-light);">
              <span style="font-size:.82rem;color:var(--gray);"><i class="fas fa-laptop-house me-1"></i>Online &amp; Face to Face</span>
              <a href="<?= SITE_URL ?>/course-sats.php" class="btn-primary-tpa" style="font-size:.82rem;padding:.45rem 1rem;">View Course →</a>
            </div>
          </div>
        </div>
      </div>

      <!-- 11 Plus Full Programme -->
      <div class="col-md-6 col-lg-4 course-item" data-category="11plus" data-aos="fade-up">
        <div class="course-card h-100">
          <div style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);padding:2rem 2rem 0;border-bottom:3px solid #28a745;">
            <div class="course-card-icon" style="background:rgba(40,167,69,0.12);"><i class="fas fa-star" style="color:#28a745;"></i></div>
            <span class="course-badge" style="background:rgba(40,167,69,0.12);color:#1e7e34;">Year 3 – Year 6</span>
            <h3 style="font-size:1.2rem;font-weight:700;color:var(--navy);margin-bottom:.5rem;">11 Plus — Grammar &amp; Independent Schools</h3>
          </div>
          <div class="course-card-body">
            <p style="color:var(--text-muted);font-size:.92rem;">Comprehensive 11+ preparation for all grammar schools in UK &amp; Independent schools.</p>
            <ul style="color:var(--text-muted);font-size:.88rem;padding-left:1.2rem;">
              <li>Verbal Reasoning (VR)</li>
              <li>Non-Verbal Reasoning (NVR)</li>
              <li>Mathematics &amp; English</li>
              <li>Full mock exam programme</li>
            </ul>
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top:1px solid var(--gray-light);">
              <span style="font-size:.82rem;color:var(--gray);"><i class="fas fa-laptop-house me-1"></i>Online &amp; Face to Face</span>
              <a href="<?= SITE_URL ?>/course-11plus.php" class="btn-primary-tpa" style="font-size:.82rem;padding:.45rem 1rem;">View Course →</a>
            </div>
          </div>
        </div>
      </div>

      <!-- 11 Plus Mock Exams -->
      <div class="col-md-6 col-lg-4 course-item" data-category="11plus" data-aos="fade-up" data-aos-delay="100">
        <div class="course-card h-100">
          <div style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);padding:2rem 2rem 0;border-bottom:3px solid #28a745;">
            <div class="course-card-icon" style="background:rgba(40,167,69,0.12);"><i class="fas fa-clipboard-list" style="color:#28a745;"></i></div>
            <span class="course-badge" style="background:rgba(40,167,69,0.12);color:#1e7e34;">Year 4 – Year 6</span>
            <h3 style="font-size:1.2rem;font-weight:700;color:var(--navy);margin-bottom:.5rem;">11 Plus Mock Exams</h3>
          </div>
          <div class="course-card-body">
            <p style="color:var(--text-muted);font-size:.92rem;">Sit realistic mock exams in genuine exam conditions. Receive a detailed performance report with targeted feedback.</p>
            <ul style="color:var(--text-muted);font-size:.88rem;padding-left:1.2rem;">
              <li>Real exam simulation</li>
              <li>Timed test with particular exam board style &amp; condition</li>
              <li>Personalised results report</li>
              <li>Parent debrief session</li>
            </ul>
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top:1px solid var(--gray-light);">
              <span style="font-size:.82rem;color:var(--gray);"><i class="fas fa-laptop-house me-1"></i>Online &amp; Face to Face</span>
              <a href="<?= SITE_URL ?>/course-11plus.php" class="btn-primary-tpa" style="font-size:.82rem;padding:.45rem 1rem;">View Course →</a>
            </div>
          </div>
        </div>
      </div>

      <!-- KS3 -->
      <div class="col-md-6 col-lg-4 course-item" data-category="ks3" data-aos="fade-up">
        <div class="course-card h-100">
          <div style="background:linear-gradient(135deg,#f3e8ff,#e8d5fb);padding:2rem 2rem 0;border-bottom:3px solid #6f42c1;">
            <div class="course-card-icon" style="background:rgba(111,66,193,0.1);"><i class="fas fa-flask" style="color:#6f42c1;"></i></div>
            <span class="course-badge" style="background:rgba(111,66,193,0.1);color:#6f42c1;">KS3 · Year 7–9</span>
            <h3 style="font-size:1.2rem;font-weight:700;color:var(--navy);margin-bottom:.5rem;">KS3 Maths, English &amp; Science</h3>
          </div>
          <div class="course-card-body">
            <p style="color:var(--text-muted);font-size:.92rem;">Secondary school support in all core subjects — keeping students ahead of the curriculum and GCSE-ready.</p>
            <ul style="color:var(--text-muted);font-size:.88rem;padding-left:1.2rem;">
              <li>Maths</li>
              <li>English language &amp; literature</li>
              <li>Science — Biology, Chemistry &amp; Physics</li>
              <li>Other subjects</li>
              <li>End-of-year exam preparation &amp; intensive classes</li>
            </ul>
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top:1px solid var(--gray-light);">
              <span style="font-size:.82rem;color:var(--gray);"><i class="fas fa-laptop-house me-1"></i>Online &amp; Face to Face</span>
              <a href="<?= SITE_URL ?>/course-ks3.php" class="btn-primary-tpa" style="font-size:.82rem;padding:.45rem 1rem;">View Course →</a>
            </div>
          </div>
        </div>
      </div>

      <!-- GCSE -->
      <div class="col-md-6 col-lg-4 course-item" data-category="gcse" data-aos="fade-up">
        <div class="course-card h-100">
          <div style="background:linear-gradient(135deg,#e0f7fa,#b2ebf2);padding:2rem 2rem 0;border-bottom:3px solid #17a2b8;">
            <div class="course-card-icon" style="background:rgba(23,162,184,0.12);"><i class="fas fa-graduation-cap" style="color:#17a2b8;"></i></div>
            <span class="course-badge" style="background:rgba(23,162,184,0.12);color:#17a2b8;">GCSE · Year 10–11</span>
            <h3 style="font-size:1.2rem;font-weight:700;color:var(--navy);margin-bottom:.5rem;">GCSE Preparation</h3>
          </div>
          <div class="course-card-body">
            <p style="color:var(--text-muted);font-size:.92rem;">Targeted GCSE tuition with exam technique coaching, past paper practice, and subject-specialist support.</p>
            <ul style="color:var(--text-muted);font-size:.88rem;padding-left:1.2rem;">
              <li>Maths, English &amp; Science</li>
              <li>Exam technique &amp; strategy</li>
              <li>Past paper practice &amp; marking</li>
              <li>Guaranteed results</li>
            </ul>
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top:1px solid var(--gray-light);">
              <span style="font-size:.82rem;color:var(--gray);"><i class="fas fa-laptop-house me-1"></i>Online &amp; Face to Face</span>
              <a href="<?= SITE_URL ?>/course-gcse.php" class="btn-primary-tpa" style="font-size:.82rem;padding:.45rem 1rem;">View Course →</a>
            </div>
          </div>
        </div>
      </div>

      <!-- A-Level -->
      <div class="col-md-6 col-lg-4 course-item" data-category="alevel" data-aos="fade-up">
        <div class="course-card h-100">
          <div style="background:linear-gradient(135deg,#e0f2f1,#b2dfdb);padding:2rem 2rem 0;border-bottom:3px solid #009688;">
            <div class="course-card-icon" style="background:rgba(0,150,136,0.12);"><i class="fas fa-university" style="color:#009688;"></i></div>
            <span class="course-badge" style="background:rgba(0,150,136,0.12);color:#00695c;">Year 12 &amp; 13</span>
            <h3 style="font-size:1.2rem;font-weight:700;color:var(--navy);margin-bottom:.5rem;">A-Level Tuition</h3>
          </div>
          <div class="course-card-body">
            <p style="color:var(--text-muted);font-size:.92rem;">Specialist A-level tuition by subject experts — preparing for university with rigorous content coverage and exam technique.</p>
            <ul style="color:var(--text-muted);font-size:.88rem;padding-left:1.2rem;">
              <li>Maths &amp; Further Maths</li>
              <li>English Literature</li>
              <li>Sciences (Biology, Chemistry, Physics)</li>
              <li>UCAS &amp; Personal Statement support</li>
            </ul>
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top:1px solid var(--gray-light);">
              <span style="font-size:.82rem;color:var(--gray);"><i class="fas fa-laptop-house me-1"></i>Online &amp; Face to Face</span>
              <a href="<?= SITE_URL ?>/course-alevel.php" class="btn-primary-tpa" style="font-size:.82rem;padding:.45rem 1rem;">View Course →</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Adult Learning -->
      <div class="col-md-6 col-lg-4 course-item" data-category="adult" data-aos="fade-up" data-aos-delay="100">
        <div class="course-card h-100">
          <div style="background:linear-gradient(135deg,#fce4ec,#f8bbd0);padding:2rem 2rem 0;border-bottom:3px solid #e91e63;">
            <div class="course-card-icon" style="background:rgba(233,30,99,0.1);"><i class="fas fa-user-graduate" style="color:#e91e63;"></i></div>
            <span class="course-badge" style="background:rgba(233,30,99,0.1);color:#880e4f;">Adults 18+</span>
            <h3 style="font-size:1.2rem;font-weight:700;color:var(--navy);margin-bottom:.5rem;">Adult Learning</h3>
          </div>
          <div class="course-card-body">
            <p style="color:var(--text-muted);font-size:.92rem;">Flexible Functional Skills, GCSE resit and literacy/numeracy programmes for adults — evenings and weekends available.</p>
            <ul style="color:var(--text-muted);font-size:.88rem;padding-left:1.2rem;">
              <li>Functional Skills Maths &amp; English</li>
              <li>GCSE Maths &amp; English (adult resit)</li>
              <li>General literacy &amp; numeracy</li>
              <li>Inclusive — all abilities welcome</li>
            </ul>
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top:1px solid var(--gray-light);">
              <span style="font-size:.82rem;color:var(--gray);"><i class="fas fa-laptop-house me-1"></i>Online &amp; Face to Face</span>
              <a href="<?= SITE_URL ?>/course-adult.php" class="btn-primary-tpa" style="font-size:.82rem;padding:.45rem 1rem;">View Course →</a>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="section-pad section-bg">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <div class="section-tag"><i class="fas fa-quote-left"></i> Parent Reviews</div>
      <h2 class="section-title">What <span>Our Parents Say</span></h2>
      <div class="divider-gold"></div>
    </div>
    <?php render_reviews(get_reviews('general', 4)); ?>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container text-center position-relative">
    <div data-aos="fade-up">
      <h2>Not Sure Which <span style="color:var(--gold);">Course to Choose?</span></h2>
      <p>Book a free assessment — we'll identify the right programme for your child and answer all your questions.</p>
      <div class="d-flex gap-3 justify-content-center flex-wrap">
        <a href="<?= SITE_URL ?>/contact.php#assessment" class="btn-primary-tpa"><i class="fas fa-calendar-check"></i> Book Free Assessment</a>
        <a href="<?= SITE_URL ?>/contact.php" class="btn-secondary-tpa"><i class="fas fa-envelope"></i> Get in Touch</a>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>