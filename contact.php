<?php
$page_title = 'Contact Us';
$meta_description = 'Contact Talent Pool Academy — two centres in Chadwell Heath and Chelmsford. Call 07772 922943 or email enquiry@talentpoolacademy.com to book your free assessment.';
$schema_extra = '<script type="application/ld+json">' . json_encode([
  '@context'=>'https://schema.org','@type'=>'ContactPage',
  'name'=>'Contact Talent Pool Academy',
  'url'=>'https://www.talentpoolacademy.com/contact.php',
  'description'=>'Two tuition centres in Chadwell Heath (RM6 6PP) and Chelmsford (CM1 2AR). Call 07772 922943 or email enquiry@talentpoolacademy.com.',
  'mainEntity'=>[
    '@type'=>'EducationalOrganization',
    'name'=>'Talent Pool Academy',
    'telephone'=>'+447772922943',
    'email'=>'enquiry@talentpoolacademy.com',
    'location'=>[
      ['@type'=>'Place','name'=>'Chadwell Heath Centre','address'=>['@type'=>'PostalAddress','streetAddress'=>'60 High Road, Chadwell Heath','addressLocality'=>'Romford','postalCode'=>'RM6 6PP','addressCountry'=>'GB']],
      ['@type'=>'Place','name'=>'Chelmsford Centre','address'=>['@type'=>'PostalAddress','streetAddress'=>'4B Corporation Road','addressLocality'=>'Chelmsford','postalCode'=>'CM1 2AR','addressCountry'=>'GB']],
    ],
    'openingHoursSpecification'=>[
      ['@type'=>'OpeningHoursSpecification','dayOfWeek'=>['Monday','Tuesday','Wednesday','Thursday','Friday'],'opens'=>'16:00','closes'=>'19:00'],
      ['@type'=>'OpeningHoursSpecification','dayOfWeek'=>['Saturday','Sunday'],'opens'=>'09:00','closes'=>'17:00'],
    ],
  ],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . '</script>';
require_once 'includes/header.php';
?>
<style>
  .map-embed { border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-md); }
  .is-invalid { border-color: #dc3545 !important; }
  .form-select-tpa {
    border: 1.5px solid rgba(10,22,40,0.12);
    border-radius: var(--radius-sm);
    padding: 0.75rem 1rem;
    font-family: var(--font-body);
    font-size: 0.95rem;
    width: 100%;
    transition: var(--transition);
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%230A1628' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1em;
  }
  .form-select-tpa:focus { outline: none; border-color: var(--gold); box-shadow: 0 0 0 3px rgba(245,166,35,0.15); }
  .faq-item { border: 1px solid rgba(10,22,40,0.08); border-radius: var(--radius-md); margin-bottom: 1rem; overflow: hidden; }
  .faq-question { padding: 1.1rem 1.3rem; font-weight: 600; color: var(--navy); cursor: pointer; display: flex; justify-content: space-between; align-items: center; }
  .faq-answer { padding: 0 1.3rem 1.1rem; color: var(--text-muted); font-size: .93rem; }
</style>

  <!-- PAGE HERO -->
  <section class="page-hero">
    <div class="container">
      <div class="col-lg-8">
        <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php">Home</a></li><li class="breadcrumb-item active">Contact</li></ol></nav>
        <h1>Get in <span style="color:var(--gold);">Touch</span></h1>
        <p>We'd love to hear from you. Enquire about courses, book your free assessment, or find your nearest centre.</p>
      </div>
    </div>
  </section>

  <!-- INFO CARDS -->
  <section class="section-pad-sm section-bg">
    <div class="container">
      <div class="row g-4">
        <div class="col-sm-6 col-lg-3" data-aos="fade-up">
          <div class="contact-info-card">
            <div class="icon"><i class="fas fa-phone-alt"></i></div>
            <div class="contact-card-label">Call Us</div>
            <a href="tel:<?= PHONE ?>" class="contact-card-value"><?= PHONE ?></a>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
          <div class="contact-info-card">
            <div class="icon"><i class="fas fa-envelope"></i></div>
            <div class="contact-card-label">Email Us</div>
            <a href="mailto:<?= EMAIL ?>" class="contact-card-value" style="font-size:.88rem;word-break:break-all;"><?= EMAIL ?></a>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
          <div class="contact-info-card">
            <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
            <div class="contact-card-label">Chadwell Heath</div>
            <div class="contact-card-value">60 High Road<br>Chadwell Heath<br>RM6 6PP</div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
          <div class="contact-info-card">
            <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
            <div class="contact-card-label">Chelmsford</div>
            <div class="contact-card-value">4B Corporation Road<br>CM1 2AR</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FORM + MAP -->
  <section class="section-pad" id="assessment">
    <div class="container">
      <div class="row g-5">
        <!-- Form -->
        <div class="col-lg-6" data-aos="fade-right">
          <div class="contact-form-wrap">
            <div class="section-tag mb-3"><i class="fas fa-calendar-check"></i> Book a Free Assessment</div>
            <h2 class="section-title" style="font-size:1.6rem;margin-bottom:.5rem;">Send Us a <span>Message</span></h2>
            <p style="color:var(--text-muted);font-size:.9rem;margin-bottom:2rem;">Fill in the form and we'll get back to you within one working day.</p>
            <form id="contactForm" novalidate>
              <input type="hidden" name="timestamp" id="form-timestamp">
              <script>document.getElementById('form-timestamp').value=new Date().toLocaleString('en-GB');</script>
              <div class="row g-3">
                <div class="col-sm-6">
                  <label class="form-label-tpa" for="child_name">Child's Name *</label>
                  <input type="text" id="child_name" name="child_name" class="form-control-tpa" placeholder="Child's full name" required autocomplete="off">
                </div>
                <div class="col-sm-6">
                  <label class="form-label-tpa" for="child-year">Child's Year Group *</label>
                  <select id="child-year" name="child-year" class="form-select-tpa" required>
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
                  <label class="form-label-tpa" for="email">Email Address *</label>
                  <input type="email" id="email" name="email" class="form-control-tpa" placeholder="jane@example.com" required>
                </div>
                <div class="col-sm-6">
                  <label class="form-label-tpa" for="phone">Phone Number *</label>
                  <input type="tel" id="phone" name="phone" class="form-control-tpa" placeholder="07xxx xxxxxx" required autocomplete="tel">
                </div>
                <div class="col-sm-6">
                  <label class="form-label-tpa" for="course-interest">Course Interest *</label>
                  <select id="course-interest" name="course-interest" class="form-select-tpa" required>
                    <option value="">Select a course</option>
                    <option>KS1 (Year 1–2)</option>
                    <option>KS2 / SATs (Year 3–6)</option>
                    <option>11 Plus Preparation</option>
                    <option>11 Plus Mock Exams</option>
                    <option>KS3 (Year 7–9)</option>
                    <option>GCSE (Year 10–11)</option>
                    <option>A-Level (Year 12–13)</option>
                    <option>Adult Learning</option>
                    <option>Not sure – please advise</option>
                  </select>
                </div>
                <div class="col-sm-6">
                  <label class="form-label-tpa" for="centre">Preferred Centre</label>
                  <select id="centre" name="centre" class="form-select-tpa">
                    <option value="">Select centre</option>
                    <option>Chadwell Heath (RM6 6PP)</option>
                    <option>Chelmsford (CM1 2AR)</option>
                    <option>Online</option>
                    <option>No preference</option>
                  </select>
                </div>
                <div class="col-sm-6">
                  <label class="form-label-tpa" for="hear">How did you hear about us?</label>
                  <select id="hear" name="hear" class="form-select-tpa">
                    <option value="">Please select</option>
                    <option>Google Search</option>
                    <option>Word of Mouth / Referral</option>
                    <option>Social Media</option>
                    <option>Local Flyer / Leaflet</option>
                    <option>Other</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label-tpa" for="message">Message</label>
                  <textarea id="message" name="message" class="form-control-tpa" rows="4" placeholder="Tell us what you'd like help with, any specific subjects, or questions you have..." style="resize:vertical;"></textarea>
                </div>
                <div class="col-12">
                  <div style="display:flex;align-items:flex-start;gap:.65rem;padding:.25rem 0;">
                    <input type="checkbox" id="privacy-consent" name="consent" checked style="flex-shrink:0;margin-top:.2rem;width:1rem;height:1rem;accent-color:var(--gold);cursor:pointer;">
                    <label for="privacy-consent" style="font-size:.87rem;color:var(--text-muted);cursor:pointer;line-height:1.55;margin:0;">I agree to Talent Pool Academy contacting me regarding my enquiry. See our <a href="<?= SITE_URL ?>/privacy.php" style="color:var(--gold);">Privacy Policy</a>.</label>
                  </div>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn-primary-tpa w-100" id="submitBtn" style="justify-content:center;font-size:1rem;">
                    <i class="fas fa-paper-plane me-2"></i> Send Message
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Map + Hours -->
        <div class="col-lg-6" data-aos="fade-left">
          <div class="mb-4">
            <h4 style="font-weight:700;color:var(--navy);margin-bottom:1rem;"><i class="fas fa-map-marker-alt text-gold me-2"></i>Chadwell Heath Centre</h4>
            <div class="map-embed">
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2480.6890534745027!2d0.12267831567624513!3d51.56481477963909!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47d8a44c1f1c7c3b%3A0x3e0f2d5c6a8b1b1e!2s60%20High%20Rd%2C%20Chadwell%20Heath%2C%20Romford%20RM6%206PP%2C%20UK!5e0!3m2!1sen!2suk!4v1710000000000!5m2!1sen!2suk" width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy" title="Romford Centre Map"></iframe>
            </div>
            <div style="font-size:.88rem;color:var(--text-muted);margin-top:.6rem;"><i class="fas fa-map-marker-alt text-gold me-1"></i>60 High Road, Chadwell Heath, Romford RM6 6PP</div>
          </div>
          <div class="mb-4">
            <h4 style="font-weight:700;color:var(--navy);margin-bottom:1rem;"><i class="fas fa-map-marker-alt text-gold me-2"></i>Chelmsford Centre</h4>
            <div class="map-embed">
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2469.1280862037505!2d0.4674117!3d51.7340271!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47d88f7a3e9be1b5%3A0x7e1c9e9e9e9e9e9e!2s4%20Corporation%20Rd%2C%20Chelmsford%20CM1%202AR%2C%20UK!5e0!3m2!1sen!2suk!4v1710000000000!5m2!1sen!2suk" width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy" title="Chelmsford Centre Map"></iframe>
            </div>
            <div style="font-size:.88rem;color:var(--text-muted);margin-top:.6rem;"><i class="fas fa-map-marker-alt text-gold me-1"></i>4B Corporation Road, Chelmsford CM1 2AR</div>
          </div>
          <div style="background:var(--off-white);border-radius:var(--radius-md);padding:1.5rem;border:1px solid rgba(10,22,40,0.07);">
            <h5 style="font-weight:700;color:var(--navy);margin-bottom:1rem;"><i class="fas fa-clock text-gold me-2"></i>Opening Hours</h5>
            <table style="width:100%;font-size:.9rem;color:var(--text-muted);">
              <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.5rem 0;font-weight:600;color:var(--navy);">Monday – Friday</td><td style="text-align:right;">4:00pm – 7:00pm</td></tr>
              <tr style="border-bottom:1px solid var(--gray-light);"><td style="padding:.5rem 0;font-weight:600;color:var(--navy);">Saturday – Sunday</td><td style="text-align:right;">9:00am – 5:00pm</td></tr>
              <tr><td style="padding:.5rem 0;font-weight:600;color:var(--text-muted);">Bank Holidays</td><td style="text-align:right;">Closed</td></tr>
            </table>
          </div>
          <div style="margin-top:1.5rem;">
            <div style="font-weight:700;color:var(--navy);margin-bottom:.75rem;">Follow &amp; Connect</div>
            <div class="social-links">
              <a href="https://www.facebook.com/talentpoolacademy" class="social-link" target="_blank" rel="noopener" style="background:rgba(10,22,40,0.06);color:var(--navy);border-color:rgba(10,22,40,0.1);" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
              <a href="http://www.instagram.com/talentpoolacademy" class="social-link" target="_blank" rel="noopener" style="background:rgba(10,22,40,0.06);color:var(--navy);border-color:rgba(10,22,40,0.1);" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
              <a href="https://www.youtube.com/talentpoolacademy" class="social-link" target="_blank" rel="noopener" style="background:rgba(10,22,40,0.06);color:var(--navy);border-color:rgba(10,22,40,0.1);" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
              <a href="https://wa.me/<?= WHATSAPP ?>" class="social-link" target="_blank" rel="noopener" style="background:#25D366;color:white;border-color:#25D366;" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section class="section-pad section-bg">
    <div class="container">
      <div class="text-center mb-5" data-aos="fade-up">
        <div class="section-tag"><i class="fas fa-question-circle"></i> FAQ</div>
        <h2 class="section-title">Frequently Asked <span>Questions</span></h2>
        <div class="divider-gold"></div>
      </div>
      <div class="row justify-content-center">
        <div class="col-lg-8" data-aos="fade-up">
          <div class="faq-item">
            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq1">
              How do I book a free assessment? <i class="fas fa-chevron-down text-gold"></i>
            </div>
            <div id="faq1" class="collapse show faq-answer">
              Simply fill in the contact form above, call us on <?= PHONE ?>, or send us a WhatsApp message. We'll arrange a convenient time at your preferred centre or online. The assessment is completely free with no obligation to enrol.
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq2">
              What happens at the free assessment? <i class="fas fa-chevron-down text-gold"></i>
            </div>
            <div id="faq2" class="collapse faq-answer">
              We give your child a short, age-appropriate assessment covering their core subjects. This typically takes 45–60 minutes and helps us identify their current level, strengths, and areas to develop. We then discuss the results with you and recommend the most suitable programme.
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq3">
              What are your class sizes? <i class="fas fa-chevron-down text-gold"></i>
            </div>
            <div id="faq3" class="collapse faq-answer">
              All our classes are small group sessions of maximum 5–6 students. This ensures every child receives focused, meaningful attention every lesson and can ask questions freely — and 1-to-1 lessons are also available upon personal request.
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq4">
              Do you offer online tuition? <i class="fas fa-chevron-down text-gold"></i>
            </div>
            <div id="faq4" class="collapse faq-answer">
              Yes! We offer live online sessions via Zoom for all our programmes. These are interactive classes — not pre-recorded videos — and run at the same times as our in-centre sessions. Online students receive the same quality of tuition and resources.
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq5">
              How soon can my child start? <i class="fas fa-chevron-down text-gold"></i>
            </div>
            <div id="faq5" class="collapse faq-answer">
              Subject to availability, students can often start within 1–2 weeks of their assessment. We run sessions throughout the year, with new intakes at the start of each half-term. Contact us to check current availability.
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
        <h2>Ready to Take the <span style="color:var(--gold);">First Step?</span></h2>
        <p>Book your child's free assessment today and discover how Talent Pool Academy can make a difference.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
          <a href="tel:<?= PHONE ?>" class="btn-primary-tpa"><i class="fas fa-phone-alt"></i> Call <?= PHONE ?></a>
          <a href="https://wa.me/447772922943" class="btn-secondary-tpa" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp Us</a>
        </div>
      </div>
    </div>
  </section>

<?php require_once 'includes/footer.php'; ?>
