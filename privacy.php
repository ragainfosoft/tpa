<?php
$page_title = 'Privacy Policy';
$meta_description = 'Privacy Policy for Talent Pool Academy. How we collect, use and protect your personal data in accordance with UK GDPR.';
require_once 'includes/header.php';
?>

  <section class="page-hero">
    <div class="container">
      <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb" style="background:none;padding:0;"><li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php" style="color:var(--gold);">Home</a></li><li class="breadcrumb-item active" style="color:rgba(255,255,255,0.6);">Privacy Policy</li></ol></nav>
      <h1>Privacy <span style="color:var(--gold);">Policy</span></h1>
      <p>Last updated: April 2025</p>
    </div>
  </section>

  <section class="section-pad">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-9">
          <div style="background:var(--white);border-radius:var(--radius-lg);border:1px solid rgba(10,22,40,0.08);padding:2.5rem 3rem;">

            <h2 style="font-size:1.4rem;color:var(--navy);margin-bottom:.75rem;">1. Who We Are</h2>
            <p>Talent Pool Academy ("we", "us", "our") operates tuition centres at Chadwell Heath and Chelmsford, and provides online tuition services. Our contact details:</p>
            <ul style="color:var(--text-muted);margin-bottom:1.5rem;">
              <li><strong>Email:</strong> <?= EMAIL ?></li>
              <li><strong>Phone:</strong> <?= PHONE ?></li>
              <li><strong>Address:</strong> 60 High Road, Chadwell Heath, Romford RM6 6PP</li>
            </ul>

            <h2 style="font-size:1.4rem;color:var(--navy);margin-bottom:.75rem;">2. What Data We Collect</h2>
            <p>We collect the following personal data when you enquire, register, or communicate with us:</p>
            <ul style="color:var(--text-muted);margin-bottom:1.5rem;">
              <li>Name of parent/guardian and student</li>
              <li>Contact details: phone number, email address, postal address</li>
              <li>Student year group and subjects of interest</li>
              <li>Any special educational needs or learning differences you share with us</li>
              <li>Payment information (processed securely; we do not store card details)</li>
              <li>Website usage data collected via cookies (see Section 8)</li>
            </ul>

            <h2 style="font-size:1.4rem;color:var(--navy);margin-bottom:.75rem;">3. How We Use Your Data</h2>
            <p>We use your data for the following purposes:</p>
            <ul style="color:var(--text-muted);margin-bottom:1.5rem;">
              <li>To respond to your enquiry and provide tuition services</li>
              <li>To manage enrolments, class scheduling and progress reports</li>
              <li>To process payments and issue receipts</li>
              <li>To send you relevant updates about your child's progress or upcoming events</li>
              <li>To send occasional newsletters or promotions (only with your consent; you may opt out at any time)</li>
              <li>To comply with legal obligations (e.g. safeguarding records)</li>
            </ul>

            <h2 style="font-size:1.4rem;color:var(--navy);margin-bottom:.75rem;">4. Legal Basis for Processing</h2>
            <p style="color:var(--text-muted);margin-bottom:1.5rem;">We process your data under the following UK GDPR lawful bases: (a) <strong>Contract</strong> — to deliver tuition services you have enrolled in; (b) <strong>Legitimate interests</strong> — to respond to enquiries and manage our business; (c) <strong>Consent</strong> — for marketing communications; (d) <strong>Legal obligation</strong> — for safeguarding and statutory requirements.</p>

            <h2 style="font-size:1.4rem;color:var(--navy);margin-bottom:.75rem;">5. Who We Share Data With</h2>
            <p style="color:var(--text-muted);margin-bottom:1.5rem;">We do not sell your personal data. We may share data with trusted third-party service providers (e.g. payment processors, email systems) under strict data processing agreements. We may also disclose data where required by law or to safeguarding authorities when there is a child welfare concern.</p>

            <h2 style="font-size:1.4rem;color:var(--navy);margin-bottom:.75rem;">6. How Long We Keep Your Data</h2>
            <p style="color:var(--text-muted);margin-bottom:1.5rem;">Enquiry records are kept for up to 2 years. Active student records are retained for the duration of enrolment plus 3 years. Financial records are kept for 7 years in line with HMRC requirements. Safeguarding records are retained as required by law.</p>

            <h2 style="font-size:1.4rem;color:var(--navy);margin-bottom:.75rem;">7. Your Rights</h2>
            <p>Under UK GDPR, you have the right to:</p>
            <ul style="color:var(--text-muted);margin-bottom:1.5rem;">
              <li><strong>Access</strong> a copy of the personal data we hold about you</li>
              <li><strong>Rectification</strong> — ask us to correct inaccurate data</li>
              <li><strong>Erasure</strong> — request deletion of your data where we have no legal grounds to retain it</li>
              <li><strong>Restriction</strong> — ask us to restrict processing in certain circumstances</li>
              <li><strong>Portability</strong> — receive your data in a portable format</li>
              <li><strong>Object</strong> — to processing based on legitimate interests or for direct marketing</li>
              <li><strong>Withdraw consent</strong> at any time for marketing communications</li>
            </ul>
            <p style="color:var(--text-muted);margin-bottom:1.5rem;">To exercise any of these rights, email us at <a href="mailto:<?= EMAIL ?>" style="color:var(--gold);"><?= EMAIL ?></a>. You also have the right to lodge a complaint with the ICO at <a href="https://ico.org.uk" style="color:var(--gold);" target="_blank" rel="noopener">ico.org.uk</a>.</p>

            <h2 style="font-size:1.4rem;color:var(--navy);margin-bottom:.75rem;">8. Cookies</h2>
            <p style="color:var(--text-muted);margin-bottom:1.5rem;">Our website uses essential cookies required for the site to function. We may also use analytics cookies to understand how visitors use our site. You can control cookies through your browser settings. Continued use of the site implies acceptance of essential cookies.</p>

            <h2 style="font-size:1.4rem;color:var(--navy);margin-bottom:.75rem;">9. Data Security</h2>
            <p style="color:var(--text-muted);margin-bottom:1.5rem;">We implement appropriate technical and organisational measures to protect your personal data against unauthorised access, loss, or disclosure. All staff with access to personal data are trained in data protection obligations.</p>

            <h2 style="font-size:1.4rem;color:var(--navy);margin-bottom:.75rem;">10. Changes to This Policy</h2>
            <p style="color:var(--text-muted);margin-bottom:0;">We may update this policy from time to time. The most current version will always be available on this page. We will notify you of material changes by email where we hold your contact details.</p>

          </div>
        </div>
      </div>
    </div>
  </section>

<?php require_once 'includes/footer.php'; ?>
