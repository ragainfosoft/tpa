<?php
$page_title = 'About Us';
$meta_description = 'Learn about Talent Pool Academy — founded in 2008 by Mrs Meena Kumar. Over 16 years of expert tutoring in Chadwell Heath and Chelmsford, serving thousands of children.';
$schema_extra = '<script type="application/ld+json">' . json_encode([
  '@context'=>'https://schema.org','@type'=>'AboutPage',
  'name'=>'About Talent Pool Academy',
  'url'=>'https://www.talentpoolacademy.com/about.php',
  'description'=>'Talent Pool Academy was founded in 2008 by Mrs Meena Kumar. Over 16 years of expert tutoring in Chadwell Heath and Chelmsford, serving thousands of children across Essex.',
  'mainEntity'=>[
    '@type'=>'EducationalOrganization',
    'name'=>'Talent Pool Academy',
    'foundingDate'=>'2008',
    'founder'=>['@type'=>'Person','name'=>'Mrs Meena Kumar'],
    'url'=>'https://www.talentpoolacademy.com',
    'location'=>[
      ['@type'=>'Place','name'=>'Chadwell Heath Centre','address'=>['@type'=>'PostalAddress','streetAddress'=>'60 High Road, Chadwell Heath','addressLocality'=>'Romford','postalCode'=>'RM6 6PP','addressCountry'=>'GB']],
      ['@type'=>'Place','name'=>'Chelmsford Centre','address'=>['@type'=>'PostalAddress','streetAddress'=>'4B Corporation Road','addressLocality'=>'Chelmsford','postalCode'=>'CM1 2AR','addressCountry'=>'GB']],
    ],
  ],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . '</script>';
require_once 'includes/header.php';
?>

<!-- PAGE HERO -->
<section class="page-hero">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <nav aria-label="breadcrumb" class="mb-3">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php">Home</a></li>
            <li class="breadcrumb-item active">About Us</li>
          </ol>
        </nav>
        <h1>About <span style="color:var(--gold);">Talent Pool Academy</span></h1>
        <p>Building brighter futures for over 16 years. Our story, our values, and the passionate team behind your child's success.</p>
      </div>
    </div>
  </div>
</section>

<!-- OUR STORY -->
<section class="section-pad">
  <div class="container">
    <div class="row align-items-center gy-5">
      <div class="col-lg-6" data-aos="fade-right">
        <img src="images/classroom.png" alt="Talent Pool Academy classroom" class="img-fluid" style="border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);">
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <div class="section-tag"><i class="fas fa-book-open"></i> Our Story</div>
        <h2 class="section-title">16 Years of <span>Transforming</span> Lives Through Education</h2>
        <div class="divider-gold"></div>
        <p style="color:var(--text-muted);">Talent Pool Academy was established in 2008. With a vision to provide every child with access to high-quality, affordable tuition that would genuinely change their educational journey
        </p>
        <p style="color:var(--text-muted);">What began as a small tuition group in Chadwell heath has grown into a thriving academy with two branch — serving families across Essex and beyond. Today, our dedicated team helps learners from Year 1 till A-level and adult programmes achieve results they, and their families, are truly proud of.
        </p>
        <p style="color:var(--text-muted);">We believe every child has unique potential. Our role is to discover it, nurture it, and help it flourish.</p>
        <div class="d-flex gap-4 mt-4 flex-wrap">
          <div>
            <div style="font-size:2rem;font-weight:800;color:var(--gold);font-family:var(--font-heading);">2008</div>
            <div style="font-size:0.85rem;color:var(--text-muted);font-weight:600;">Year Founded</div>
          </div>
          <div>
            <div style="font-size:2rem;font-weight:800;color:var(--gold);font-family:var(--font-heading);">5,000+</div>
            <div style="font-size:0.85rem;color:var(--text-muted);font-weight:600;">Students Helped</div>
          </div>
          <div>
            <div style="font-size:2rem;font-weight:800;color:var(--gold);font-family:var(--font-heading);">2</div>
            <div style="font-size:0.85rem;color:var(--text-muted);font-weight:600;">Learning Centres</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- VALUES -->
<section class="section-pad section-bg">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <div class="section-tag"><i class="fas fa-heart"></i> Our Values</div>
      <h2 class="section-title">What We <span>Stand For</span></h2>
      <div class="divider-gold"></div>
    </div>
    <div class="row g-4 gsap-stagger">
      <div class="col-sm-6 col-lg-3">
        <div class="value-card">
          <div class="value-icon"><i class="fas fa-heart"></i></div>
          <h5 style="font-weight:700;color:var(--navy);margin-bottom:0.5rem;">Passion</h5>
          <p style="color:var(--text-muted);font-size:0.92rem;">We are genuinely passionate about education and the difference excellent teaching can make to a child's life.</p>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="value-card">
          <div class="value-icon"><i class="fas fa-user-check"></i></div>
          <h5 style="font-weight:700;color:var(--navy);margin-bottom:0.5rem;">Personalisation</h5>
          <p style="color:var(--text-muted);font-size:0.92rem;">Every child is different. We tailor our approach to each student's strengths, needs and learning style.</p>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="value-card">
          <div class="value-icon"><i class="fas fa-trophy"></i></div>
          <h5 style="font-weight:700;color:var(--navy);margin-bottom:0.5rem;">Excellence</h5>
          <p style="color:var(--text-muted);font-size:0.92rem;">We hold ourselves and our students to high standards, delivering lessons that challenge, inspire and reward.</p>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="value-card">
          <div class="value-icon"><i class="fas fa-handshake"></i></div>
          <h5 style="font-weight:700;color:var(--navy);margin-bottom:0.5rem;">Partnership</h5>
          <p style="color:var(--text-muted);font-size:0.92rem;">We work closely with parents and communicate regularly to ensure we're all working together for the best outcomes.</p>
        </div>
      </div>
    </div>
  </div>
</section>




<!-- TIMELINE -->
<section class="section-pad">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <div class="section-tag"><i class="fas fa-history"></i> Our Journey</div>
      <h2 class="section-title">Key <span>Milestones</span></h2>
      <div class="divider-gold"></div>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-8" data-aos="fade-up">
        <div class="timeline">
          <div class="timeline-item">
            <div class="timeline-year">2008</div>
            <div class="timeline-event">Academy Founded</div>
            <div class="timeline-desc">Mrs Meena Kumar launches Talent Pool Academy from Romford with a small group of dedicated students.</div>
          </div>
          <div class="timeline-item">
            <div class="timeline-year">2011</div>
            <div class="timeline-event">First Expansion</div>
            <div class="timeline-desc">Growing demand led to larger premises and an expanded teaching team, serving over 200 students per term.</div>
          </div>
          <div class="timeline-item">
            <div class="timeline-year">2015</div>
            <div class="timeline-event">11 Plus Programme Launch</div>
            <div class="timeline-desc">Dedicated 11 Plus preparation programme introduced, with mock exam facilities and specialist resources.</div>
          </div>

          <div class="timeline-item">
            <div class="timeline-year">2020</div>
            <div class="timeline-event">Online Learning Launched</div>
            <div class="timeline-desc">Successfully transitioned to online teaching, keeping all students on track throughout the pandemic.</div>
          </div>
          <div class="timeline-item">
            <div class="timeline-year">2023</div>
            <div class="timeline-event">Chelmsford Centre Opens</div>
            <div class="timeline-desc">Second centre established in Chelmsford, bringing Talent Pool's expertise to a wider Essex community.</div>
          </div>
          <div class="timeline-item">
            <div class="timeline-year">2025</div>
            <div class="timeline-event">3,000+ Students Milestone</div>
            <div class="timeline-desc">Celebrated over 3,000 students having passed through the academy's doors since founding.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container text-center position-relative">
    <div data-aos="fade-up">
      <h2>Join Our Growing <span style="color:var(--gold);">Academy Family</span></h2>
      <p>Book a free assessment and discover how we can help your child reach their potential.</p>
      <a href="<?= SITE_URL ?>/contact.php#assessment" class="btn-primary-tpa">
        <i class="fas fa-calendar-check"></i> Book Free Assessment
      </a>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>