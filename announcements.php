<?php
$page_title = 'Announcements & News';
$meta_description = 'Latest announcements, upcoming events, holiday dates and news from Talent Pool Academy — tuition centre in Chadwell Heath and Chelmsford.';
require_once 'includes/config.php';
require_once 'includes/announcements-data.php';
require_once 'includes/header.php';
?>

  <section class="page-hero">
    <div class="container">
      <div class="col-lg-8">
        <nav aria-label="breadcrumb" class="mb-3">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php">Home</a></li>
            <li class="breadcrumb-item active">Announcements</li>
          </ol>
        </nav>
        <h1>Announcements &amp; <span style="color:var(--gold);">News</span></h1>
        <p>Stay up to date with the latest news, upcoming events, holiday notices and special programmes at Talent Pool Academy.</p>
      </div>
    </div>
  </section>

  <section class="section-pad">
    <div class="container">
      <div class="row g-4">
        <?php foreach ($announcements as $a): ?>
        <div class="col-lg-6" data-aos="fade-up">
          <div class="announcement-card h-100" style="display:flex;flex-direction:column;">
            <div style="display:flex;gap:1rem;align-items:flex-start;">
              <div class="announcement-icon" style="background:<?= $a['icon_bg'] ?>;color:<?= $a['icon_color'] ?>;flex-shrink:0;">
                <i class="fas <?= $a['icon'] ?>"></i>
              </div>
              <div style="flex:1;">
                <div class="announcement-tag" style="background:<?= $a['tag_color'] ?>;color:<?= $a['tag_text'] ?>;"><?= htmlspecialchars($a['tag']) ?></div>
                <div class="announcement-title" style="font-size:1.05rem;"><?= htmlspecialchars($a['title']) ?></div>
                <div class="announcement-desc"><?= htmlspecialchars($a['short']) ?></div>
                <div class="announcement-date"><i class="fas <?= $a['date_icon'] ?>"></i> <?= htmlspecialchars($a['date']) ?></div>
              </div>
            </div>
            <?php if ($a['has_more'] && $a['full_content']): ?>
            <div class="mt-3 pt-3" style="border-top:1px solid var(--gray-light);margin-top:auto;">
              <div class="announcement-full" id="full-<?= $a['id'] ?>" style="display:none;padding-top:.8rem;">
                <?= $a['full_content'] ?>
              </div>
              <?php if (!empty($a['more_url'])): ?>
                <a href="<?= $a['more_url'] ?>" class="btn-outline-tpa" style="font-size:.85rem;padding:.45rem 1rem;">
                  <i class="fas fa-arrow-right me-1"></i>Go to Page
                </a>
              <?php endif; ?>
              <button onclick="toggleAnnouncement('<?= $a['id'] ?>')" class="btn-outline-tpa" id="btn-<?= $a['id'] ?>" style="font-size:.85rem;padding:.45rem 1rem;margin-left:<?= !empty($a['more_url']) ? '.5rem' : '0' ?>;">
                <i class="fas fa-plus me-1" id="ico-<?= $a['id'] ?>"></i>Know More
              </button>
            </div>
            <?php elseif ($a['has_more'] && !empty($a['more_url'])): ?>
            <div class="mt-3 pt-3" style="border-top:1px solid var(--gray-light);margin-top:auto;">
              <a href="<?= $a['more_url'] ?>" class="btn-outline-tpa" style="font-size:.85rem;padding:.45rem 1rem;">
                <i class="fas fa-arrow-right me-1"></i>View Full Details
              </a>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- WhatsApp CTA -->
      <div class="text-center mt-5 p-5" style="background:var(--navy);border-radius:var(--radius-lg);">
        <h4 style="color:#fff;font-weight:800;margin-bottom:.5rem;">Have a question about any of our programmes?</h4>
        <p style="color:rgba(255,255,255,0.65);margin-bottom:1.5rem;">We're happy to help via WhatsApp, phone, or email.</p>
        <a href="https://wa.me/<?= WHATSAPP ?>" class="btn-primary-tpa me-2"><i class="fab fa-whatsapp me-2"></i>WhatsApp Us</a>
        <a href="<?= SITE_URL ?>/contact.php" class="btn-secondary-tpa"><i class="fas fa-envelope me-2"></i>Send Enquiry</a>
      </div>
    </div>
  </section>

  <style>
    .announcement-full { animation: fadeSlide .3s ease; }
    .announcement-full h5 { font-weight:700;color:var(--navy);margin:1rem 0 .5rem; }
    .announcement-full ul { padding-left:1.2rem; }
    .announcement-full ul li { margin-bottom:.3rem; }
    @keyframes fadeSlide { from { opacity:0;transform:translateY(-8px); } to { opacity:1;transform:none; } }
  </style>
  <script>
    function toggleAnnouncement(id) {
      const full = document.getElementById('full-' + id);
      const btn  = document.getElementById('btn-' + id);
      const ico  = document.getElementById('ico-' + id);
      const isOpen = full.style.display === 'block';
      full.style.display = isOpen ? 'none' : 'block';
      btn.innerHTML = isOpen ? '<i class="fas fa-plus me-1"></i>Know More' : '<i class="fas fa-minus me-1"></i>Show Less';
    }
  </script>

<?php require_once 'includes/footer.php'; ?>
