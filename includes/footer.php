  <!-- FOOTER -->
  <footer class="tpa-footer">
    <div class="container tpa-footer-top">
      <div class="row g-5">
        <div class="col-lg-4">
          <div class="tpa-footer-logo mb-3"><span>Talent <em>Pool</em> Academy</span></div>
          <p>Celebrating over 16 years of excellence in education. Expert tuition for KS1, KS2, 11 Plus, SATs, KS3, GCSE &amp; A-level.</p>
          <div class="social-links mt-4">
            <a href="https://www.facebook.com/talentpoolacademy" class="social-link" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="http://www.instagram.com/talentpoolacademy" class="social-link" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="https://www.youtube.com/talentpoolacademy" class="social-link" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            <a href="https://wa.me/<?= WHATSAPP ?>" class="social-link" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
          </div>
        </div>
        <div class="col-sm-6 col-lg-2">
          <h5>Quick Links</h5>
          <ul>
            <li><a href="<?= SITE_URL ?>/index.php">Home</a></li>
            <li><a href="<?= SITE_URL ?>/about.php">About Us</a></li>
            <li><a href="<?= SITE_URL ?>/courses.php">All Courses</a></li>
            <li><a href="<?= SITE_URL ?>/books.php">Books</a></li>
            <li><a href="<?= SITE_URL ?>/events.php">Events &amp; Gallery</a></li>
            <li><a href="<?= SITE_URL ?>/contact.php">Contact</a></li>
            <li><a href="<?= SITE_URL ?>/student/index.php">Student Login</a></li>
            <li><a href="<?= SITE_URL ?>/parent/index.php">Parent Login</a></li>
            <li><a href="<?= SITE_URL ?>/parkwood-academy.php"><i class="fas fa-handshake me-1" aria-hidden="true"></i>Parkwood Academy</a></li>
          </ul>
        </div>
        <div class="col-sm-6 col-lg-2">
          <h5>Programmes</h5>
          <ul>
            <li><a href="<?= SITE_URL ?>/course-ks1.php">KS1 (Year 1–2)</a></li>
            <li><a href="<?= SITE_URL ?>/course-ks2.php">KS2 (Year 3–6)</a></li>
            <li><a href="<?= SITE_URL ?>/course-sats.php">SATs (Year 2 &amp; 6)</a></li>
            <li><a href="<?= SITE_URL ?>/course-11plus.php">11 Plus</a></li>
            <li><a href="<?= SITE_URL ?>/course-ks3.php">KS3 (Year 7–9)</a></li>
            <li><a href="<?= SITE_URL ?>/course-gcse.php">GCSE (Year 10–11)</a></li>
            <li><a href="<?= SITE_URL ?>/course-alevel.php">A-Level (Year 12–13)</a></li>
            <li><a href="<?= SITE_URL ?>/course-adult.php">Adult Learning</a></li>
            <li><a href="<?= SITE_URL ?>/easter-camp.php"><i class="fas fa-egg me-1" aria-hidden="true"></i>Easter Camp 2026</a></li>
            <li><a href="<?= SITE_URL ?>/summer-camp.php"><i class="fas fa-sun me-1" aria-hidden="true"></i>Summer Camp 2026</a></li>
            <li><a href="<?= SITE_URL ?>/announcements.php"><i class="fas fa-bullhorn me-1" aria-hidden="true"></i>Announcements</a></li>
          </ul>
        </div>
        <div class="col-lg-4">
          <h5>Contact Us</h5>
          <div class="footer-contact-item"><i class="fas fa-map-marker-alt mt-1"></i>
            <div><strong style="color:rgba(255,255,255,0.8);">Chadwell Heath:</strong><br>60 High Road, Chadwell Heath, RM6 6PP</div>
          </div>
          <div class="footer-contact-item"><i class="fas fa-map-marker-alt mt-1"></i>
            <div><strong style="color:rgba(255,255,255,0.8);">Chelmsford:</strong><br>4B Corporation Road, CM1 2AR</div>
          </div>
          <div class="footer-contact-item"><i class="fas fa-phone-alt"></i><a href="tel:<?= PHONE ?>" style="color:rgba(255,255,255,0.65);"><?= PHONE ?></a></div>
          <div class="footer-contact-item"><i class="fas fa-envelope"></i><a href="mailto:<?= EMAIL ?>" style="color:rgba(255,255,255,0.65);"><?= EMAIL ?></a></div>
        </div>
      </div>
    </div>
    <div class="tpa-footer-bottom">
      <div class="container">
        <p class="mb-0">© <?= date('Y') ?> Talent Pool Academy. All rights reserved. · <a href="<?= SITE_URL ?>/privacy.php">Privacy Policy</a> · <a href="<?= SITE_URL ?>/terms.php">Terms &amp; Conditions</a></p>
      </div>
    </div>
  </footer>

  <a href="https://wa.me/<?= WHATSAPP ?>" class="whatsapp-float" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.3/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.3/ScrollTrigger.min.js"></script>
  <script src="<?= SITE_URL ?>/js/main.js"></script>
  <?= isset($extra_js) ? $extra_js : '' ?>
  </body>

  </html>