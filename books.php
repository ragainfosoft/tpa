<?php
$page_title       = 'Books & Resources | Talent Pool Academy';
$meta_description = 'Browse Talent Pool Academy\'s educational books and resources — including Vocabulary Builders, Spelling Workbooks, and Maths Practice books for UK students.';
$extra_css = '
<style>
  .book-card { background:var(--white);border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-sm);border:1px solid rgba(10,22,40,0.07);transition:var(--transition);height:100%; }
  .book-card:hover { transform:translateY(-6px);box-shadow:var(--shadow-lg);border-color:rgba(245,166,35,0.35); }
  .book-cover-wrap { background:var(--off-white);padding:2rem;display:flex;align-items:center;justify-content:center;min-height:280px;position:relative; }
  .book-cover-wrap img { width:160px;border-radius:6px;box-shadow:0 8px 30px rgba(0,0,0,0.25); }
  .book-badge { position:absolute;top:1rem;right:1rem;background:var(--gold);color:var(--navy);font-size:.72rem;font-weight:800;padding:.3rem .8rem;border-radius:20px;letter-spacing:.05em; }
  .book-body { padding:1.5rem; }
  .book-title { font-weight:700;color:var(--navy);font-size:1.05rem;margin-bottom:.4rem; }
  .book-series { color:var(--gold);font-size:.82rem;font-weight:700;margin-bottom:.8rem; }
  .book-desc { color:var(--text-muted);font-size:.9rem;margin-bottom:1rem; }
  .book-features { list-style:none;padding:0;margin-bottom:1.2rem; }
  .book-features li { font-size:.85rem;color:var(--text-muted);margin-bottom:.35rem; }
  .book-features li::before { content:"✓";color:var(--gold);font-weight:700;margin-right:.5rem; }
  .book-footer { display:flex;justify-content:space-between;align-items:center;padding:1rem 1.5rem;border-top:1px solid var(--gray-light); }
  .book-level { font-size:.8rem;font-weight:600;color:var(--text-muted); }
</style>';
require_once 'includes/config.php';
$schema_extra = '<script type="application/ld+json">' . json_encode([
  '@context'=>'https://schema.org','@type'=>'CollectionPage',
  'name'=>'Books & Resources — Talent Pool Academy',
  'url'=>'https://www.talentpoolacademy.com/books.php',
  'description'=>'Educational books and revision resources by Talent Pool Academy — Vocabulary Builders, Spelling Workbooks and Maths Practice books for KS1–GCSE students.',
  'provider'=>['@type'=>'EducationalOrganization','name'=>'Talent Pool Academy','url'=>'https://www.talentpoolacademy.com'],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . '</script>';
require_once 'includes/header.php';
?>

  <!-- PAGE HERO -->
  <section class="page-hero">
    <div class="container">
      <div class="col-lg-8">
        <nav aria-label="breadcrumb" class="mb-3">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php">Home</a></li>
            <li class="breadcrumb-item active">Books</li>
          </ol>
        </nav>
        <h1>Books &amp; <span style="color:var(--gold);">Learning Resources</span></h1>
        <p>All our resources are aligned with the National Curriculum and match children's abilities, and the practice materials are designed to complement every stage of your child's learning journey.</p>
      </div>
    </div>
  </section>

  <!-- INTRO -->
  <section class="section-pad-sm section-bg">
    <div class="container">
      <div class="row align-items-center gy-4">
        <div class="col-lg-8" data-aos="fade-right">
          <div class="section-tag"><i class="fas fa-book"></i> Our Publications</div>
          <h2 class="section-title" style="font-size:1.9rem;">Written by Our <span>Expert Teachers</span></h2>
          <p style="color:var(--text-muted);">All Talent Pool Academy resources are written by our experienced teaching team. Each book is carefully structured to align with the UK National Curriculum and is used directly in our tuition sessions — so they're proven to work.</p>
        </div>
        <div class="col-lg-4 text-center" data-aos="fade-left">
          <div style="background:var(--white);border-radius:var(--radius-lg);padding:1.5rem;box-shadow:var(--shadow-sm);">
            <div style="font-size:2.5rem;color:var(--gold);margin-bottom:.5rem;"><i class="fas fa-truck"></i></div>
            <div style="font-weight:700;color:var(--navy);">UK Delivery Available</div>
            <div style="color:var(--text-muted);font-size:.88rem;">Order by post or collect in-centre at Chadwell Heath or Chelmsford.</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- BOOKS GRID -->
  <section class="section-pad">
    <div class="container">
      <div class="text-center mb-5" data-aos="fade-up">
        <div class="section-tag"><i class="fas fa-book-open"></i> All Books</div>
        <h2 class="section-title">Our <span>Publications</span></h2>
        <div class="divider-gold"></div>
      </div>
      <div class="row g-4">

        <!-- Vocabulary Builder -->
        <div class="col-md-6 col-lg-4" data-aos="fade-up">
          <div class="book-card">
            <div class="book-cover-wrap">
              <span class="book-badge">BESTSELLER</span>
              <img src="<?= SITE_URL ?>/images/book_vocab.png" alt="Vocabulary Builder Workbook">
            </div>
            <div class="book-body">
              <div class="book-series">TPA Educational Series</div>
              <div class="book-title">Vocabulary Builder Workbook</div>
              <div class="book-desc">A comprehensive vocabulary development resource for KS1 &amp; KS2 students, packed with exercises to expand word knowledge across all subjects.</div>
              <ul class="book-features">
                <li>500+ carefully selected words</li>
                <li>Contextual sentences &amp; definitions</li>
                <li>Fill-in exercises &amp; word games</li>
                <li>Progress tracking pages</li>
              </ul>
            </div>
            <div class="book-footer">
              <span class="book-level"><i class="fas fa-child me-1"></i>Year 2–6 · Ages 6–11</span>
              <a href="<?= SITE_URL ?>/contact.php" class="btn-primary-tpa" style="font-size:.82rem;padding:.5rem 1.1rem;">Enquire</a>
            </div>
          </div>
        </div>

        <!-- Spelling Mastery -->
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
          <div class="book-card">
            <div class="book-cover-wrap">
              <img src="<?= SITE_URL ?>/images/book_spelling.png" alt="Spelling Mastery Workbook">
            </div>
            <div class="book-body">
              <div class="book-series">TPA Educational Series</div>
              <div class="book-title">Spelling Mastery Workbook</div>
              <div class="book-desc">Master the Year 1–6 statutory spelling lists, spelling rules, and common exception words with structured, engaging practice activities.</div>
              <ul class="book-features">
                <li>All KS1 &amp; KS2 spelling lists</li>
                <li>Spelling rules &amp; patterns</li>
                <li>Dictation exercises</li>
                <li>Weekly test frameworks</li>
              </ul>
            </div>
            <div class="book-footer">
              <span class="book-level"><i class="fas fa-child me-1"></i>Year 1–6 · Ages 5–11</span>
              <a href="<?= SITE_URL ?>/contact.php" class="btn-primary-tpa" style="font-size:.82rem;padding:.5rem 1.1rem;">Enquire</a>
            </div>
          </div>
        </div>

        <!-- 11 Plus VR -->
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
          <div class="book-card">
            <div class="book-cover-wrap" style="background:linear-gradient(135deg,var(--navy),var(--navy-light));min-height:280px;flex-direction:column;gap:1rem;">
              <i class="fas fa-brain" style="font-size:4rem;color:var(--gold);opacity:.8;"></i>
              <span style="color:var(--white);font-weight:700;text-align:center;font-size:.95rem;">11 Plus VR Practice</span>
            </div>
            <div class="book-body">
              <div class="book-series">TPA 11 Plus Series</div>
              <div class="book-title">11 Plus Verbal Reasoning Practice</div>
              <div class="book-desc">Hundreds of VR practice questions covering every question type found in the 11+ exam, with worked examples and detailed answers.</div>
              <ul class="book-features">
                <li>25+ question type categories</li>
                <li>Timed practice tests</li>
                <li>Answers with full explanations</li>
                <li>Progress assessment charts</li>
              </ul>
            </div>
            <div class="book-footer">
              <span class="book-level"><i class="fas fa-child me-1"></i>Year 4–6 · Ages 9–11</span>
              <a href="<?= SITE_URL ?>/contact.php" class="btn-primary-tpa" style="font-size:.82rem;padding:.5rem 1.1rem;">Enquire</a>
            </div>
          </div>
        </div>

        <!-- KS2 Maths -->
        <div class="col-md-6 col-lg-4" data-aos="fade-up">
          <div class="book-card">
            <div class="book-cover-wrap" style="background:linear-gradient(135deg,#e8f4f8,#c9e8f5);min-height:280px;flex-direction:column;gap:1rem;">
              <i class="fas fa-calculator" style="font-size:4rem;color:#17a2b8;opacity:.8;"></i>
              <span style="color:var(--navy);font-weight:700;text-align:center;font-size:.95rem;">KS2 Maths Practice</span>
            </div>
            <div class="book-body">
              <div class="book-series">TPA Maths Series</div>
              <div class="book-title">KS2 Maths Reasoning &amp; Problem Solving</div>
              <div class="book-desc">Develops mathematical reasoning skills essential for SATs — covering all Year 3–6 topics with graduated difficulty levels.</div>
              <ul class="book-features">
                <li>3 levels: Foundation, Core &amp; Extension</li>
                <li>SATs-style questions throughout</li>
                <li>Multi-step problem solving</li>
                <li>Worked example solutions</li>
              </ul>
            </div>
            <div class="book-footer">
              <span class="book-level"><i class="fas fa-child me-1"></i>Year 3–6 · Ages 7–11</span>
              <a href="<?= SITE_URL ?>/contact.php" class="btn-primary-tpa" style="font-size:.82rem;padding:.5rem 1.1rem;">Enquire</a>
            </div>
          </div>
        </div>

        <!-- NVR -->
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
          <div class="book-card">
            <div class="book-cover-wrap" style="background:linear-gradient(135deg,#f3e8ff,#e8d5fb);min-height:280px;flex-direction:column;gap:1rem;">
              <i class="fas fa-shapes" style="font-size:4rem;color:#6f42c1;opacity:.8;"></i>
              <span style="color:var(--navy);font-weight:700;text-align:center;font-size:.95rem;">11 Plus NVR Practice</span>
            </div>
            <div class="book-body">
              <div class="book-series">TPA 11 Plus Series</div>
              <div class="book-title">11 Plus Non-Verbal Reasoning Practice</div>
              <div class="book-desc">Visual spatial puzzles, pattern recognition, and shapes-based reasoning to build NVR skills from scratch — no prior knowledge needed.</div>
              <ul class="book-features">
                <li>Step-by-step skill building</li>
                <li>20+ NVR question types</li>
                <li>Full-colour visual examples</li>
                <li>Timed mock test included</li>
              </ul>
            </div>
            <div class="book-footer">
              <span class="book-level"><i class="fas fa-child me-1"></i>Year 4–6 · Ages 9–11</span>
              <a href="<?= SITE_URL ?>/contact.php" class="btn-primary-tpa" style="font-size:.82rem;padding:.5rem 1.1rem;">Enquire</a>
            </div>
          </div>
        </div>

        <!-- Coming Soon -->
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
          <div class="book-card" style="border:2px dashed rgba(10,22,40,0.2);">
            <div class="book-cover-wrap" style="background:var(--gray-light);min-height:280px;flex-direction:column;gap:1rem;">
              <i class="fas fa-clock" style="font-size:4rem;color:var(--gray);opacity:.6;"></i>
              <span style="color:var(--gray);font-weight:700;text-align:center;">Coming Soon</span>
            </div>
            <div class="book-body">
              <div class="book-series" style="color:var(--gray);">TPA KS3 Series</div>
              <div class="book-title">KS3 Science Revision Guide</div>
              <div class="book-desc">A comprehensive revision guide for Year 7–9 Science covering Biology, Chemistry, and Physics aligned to the KS3 programme of study.</div>
              <ul class="book-features">
                <li>All three science disciplines</li>
                <li>Key term glossaries</li>
                <li>Practice questions &amp; answers</li>
                <li>GCSE transition content</li>
              </ul>
            </div>
            <div class="book-footer">
              <span class="book-level"><i class="fas fa-child me-1"></i>Year 7–9 · Ages 11–14</span>
              <span style="background:var(--gray-light);color:var(--gray);padding:.45rem 1rem;border-radius:20px;font-size:.82rem;font-weight:600;">Notify Me</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- HOW TO ORDER -->
  <section class="section-pad section-bg">
    <div class="container">
      <div class="text-center mb-5" data-aos="fade-up">
        <div class="section-tag"><i class="fas fa-shopping-cart"></i> How to Order</div>
        <h2 class="section-title">Getting Your <span>Books</span></h2>
        <p class="section-subtitle mx-auto">Simple steps to get the right resources in your child's hands.</p>
        <div class="divider-gold"></div>
      </div>
      <div class="row g-4">

        <div class="col-sm-6 col-lg-3" data-aos="fade-up">
          <div class="order-step-card">
            <div class="order-step-num">1</div>
            <div class="order-step-icon"><i class="fas fa-phone-alt"></i></div>
            <h5>Get in Touch</h5>
            <p>Contact us by phone, email or WhatsApp to enquire and confirm availability.</p>
            <a href="tel:<?= PHONE ?>" class="order-step-link"><i class="fas fa-phone-alt me-1"></i><?= PHONE ?></a>
          </div>
        </div>

        <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
          <div class="order-step-card">
            <div class="order-step-num">2</div>
            <div class="order-step-icon"><i class="fas fa-clipboard-check"></i></div>
            <h5>Choose &amp; Pay</h5>
            <p>Choose <strong>hard copy</strong> or <strong>soft copy</strong> and specify the book name. Pay by bank transfer or cash on collection. Use your <strong>student name + "books"</strong> as the payment reference.</p>
          </div>
        </div>

        <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
          <div class="order-step-card">
            <div class="order-step-num">3</div>
            <div class="order-step-icon"><i class="fas fa-truck"></i></div>
            <h5>Collect or Deliver</h5>
            <p>Collect from our Chadwell Heath or Chelmsford centre, or have books posted to your UK address.</p>
            <span class="order-step-badge"><i class="fas fa-check me-1"></i>Free local collection</span>
          </div>
        </div>

        <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
          <div class="order-step-card">
            <div class="order-step-num">4</div>
            <div class="order-step-icon"><i class="fas fa-rocket"></i></div>
            <h5>Start Learning</h5>
            <p>Your child uses the resources at home and can bring them into class for teacher guidance.</p>
          </div>
        </div>

      </div>

      <!-- BANK DETAILS -->
      <div class="row justify-content-center mt-5" data-aos="fade-up">
        <div class="col-lg-8">
          <div style="background:var(--navy);border-radius:var(--radius-lg);padding:1.75rem 2rem;color:var(--white);">
            <h5 style="color:var(--gold);font-weight:700;margin-bottom:1.2rem;"><i class="fas fa-university me-2"></i>Bank Transfer Details</h5>
            <div class="row g-3">
              <div class="col-sm-4"><div style="font-size:.8rem;color:rgba(255,255,255,0.6);margin-bottom:.2rem;">Account Name</div><div style="font-weight:700;color:var(--white);">Talent Pool Academy</div></div>
              <div class="col-sm-4"><div style="font-size:.8rem;color:rgba(255,255,255,0.6);margin-bottom:.2rem;">Account Number</div><div style="font-weight:700;color:var(--white);">69995444</div></div>
              <div class="col-sm-4"><div style="font-size:.8rem;color:rgba(255,255,255,0.6);margin-bottom:.2rem;">Sort Code</div><div style="font-weight:700;color:var(--white);">08-92-99</div></div>
            </div>
            <div style="margin-top:1.1rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,0.15);font-size:.88rem;color:rgba(255,255,255,0.75);">
              <i class="fas fa-info-circle text-gold me-2"></i><strong style="color:var(--gold);">Payment Reference:</strong> Please write your <strong style="color:var(--white);">student's name + "books"</strong> as the payment reference (e.g. <em>Ahmed Ali — books</em>).
            </div>
          </div>
        </div>
      </div>

      <div class="text-center mt-5" data-aos="fade-up">
        <p style="color:var(--text-muted);margin-bottom:1.25rem;">The quickest way to order is via WhatsApp:</p>
        <a href="https://wa.me/<?= WHATSAPP ?>?text=Hi, I'd like to order a TPA book" class="btn-primary-tpa me-3"><i class="fab fa-whatsapp me-2"></i>Order via WhatsApp</a>
        <a href="<?= SITE_URL ?>/contact.php" class="btn-outline-tpa"><i class="fas fa-envelope me-2"></i>Send Enquiry</a>
      </div>
    </div>
  </section>

  <style>
  .order-step-card {
    background: #fff;
    border-radius: var(--radius-lg);
    padding: 2rem 1.5rem 1.75rem;
    border: 1px solid rgba(10,22,40,0.07);
    box-shadow: 0 2px 12px rgba(10,22,40,0.06);
    text-align: center;
    height: 100%;
    position: relative;
    transition: var(--transition);
  }
  .order-step-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 48px rgba(10,22,40,0.12);
    border-color: rgba(245,166,35,0.3);
  }
  .order-step-num {
    position: absolute;
    top: -14px;
    left: 50%;
    transform: translateX(-50%);
    width: 32px;
    height: 32px;
    background: var(--navy);
    color: var(--gold);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .85rem;
    font-weight: 800;
    box-shadow: 0 4px 12px rgba(10,22,40,0.2);
  }
  .order-step-icon {
    width: 68px;
    height: 68px;
    background: linear-gradient(135deg, #F5A623, #FFD700);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0.5rem auto 1.25rem;
    font-size: 1.5rem;
    color: var(--navy);
    box-shadow: 0 8px 24px rgba(245,166,35,0.35);
  }
  .order-step-card h5 {
    font-weight: 700;
    color: var(--navy);
    margin-bottom: .5rem;
    font-size: 1rem;
  }
  .order-step-card p {
    color: var(--text-muted);
    font-size: .9rem;
    margin: 0;
    line-height: 1.6;
  }
  .order-step-link {
    display: inline-block;
    margin-top: .85rem;
    font-size: .8rem;
    color: var(--gold);
    font-weight: 600;
    text-decoration: none;
  }
  .order-step-badge {
    display: inline-block;
    margin-top: .85rem;
    font-size: .78rem;
    background: #FFF8E7;
    color: var(--navy);
    padding: .25rem .8rem;
    border-radius: 20px;
    font-weight: 600;
  }
  </style>

  <!-- CTA -->
  <section class="cta-section">
    <div class="container text-center position-relative">
      <div data-aos="fade-up">
        <h2>Want to Order or Find Out <span style="color:var(--gold);">More?</span></h2>
        <p>Get in touch with our team and we'll help you find the right books for your child's stage and programme.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
          <a href="<?= SITE_URL ?>/contact.php" class="btn-primary-tpa"><i class="fas fa-envelope"></i> Get in Touch</a>
          <a href="tel:<?= PHONE ?>" class="btn-secondary-tpa"><i class="fas fa-phone-alt"></i> Call <?= PHONE ?></a>
        </div>
      </div>
    </div>
  </section>

<?php require_once 'includes/footer.php'; ?>
