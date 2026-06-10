<?php
/**
 * Reusable Star Students Carousel Section
 *
 * Set these variables before including:
 *   $star_carousel_tags    (array)  — filter by course tags; empty = show all
 *   $star_carousel_title   (string) — HTML for h2 (optional)
 *   $star_carousel_subtitle(string) — plain text subtitle (optional)
 */
if (!defined('SITE_URL'))           require_once __DIR__ . '/config.php';
if (!function_exists('get_star_students')) require_once __DIR__ . '/star-students-data.php';

$_sc_tags     = $star_carousel_tags     ?? [];
$_sc_title    = $star_carousel_title    ?? 'Our <span>Star Students</span>';
$_sc_subtitle = $star_carousel_subtitle ?? 'These incredible students secured places at top grammar &amp; independent schools — we\'re so proud of every one of them!';

// Prefer tag-matched students; fall back to all students
$_sc_students = !empty($_sc_tags) ? get_star_students($_sc_tags) : [];
if (empty($_sc_students)) $_sc_students = get_star_students();
?>
<?php if (!empty($_sc_students)): ?>
<section class="star-students-section">
  <div class="container">
    <div class="text-center mb-4" data-aos="fade-up">
      <div class="section-tag"><i class="fas fa-star"></i> Student Success</div>
      <h2 class="section-title"><?= $_sc_title ?></h2>
      <p class="section-subtitle mx-auto"><?= $_sc_subtitle ?></p>
      <div class="divider-gold"></div>
    </div>
    <div class="star-carousel-wrap" data-aos="fade-up">
      <button class="star-carousel-btn star-carousel-prev" id="starPrev" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
      <div class="star-carousel-track-wrap">
        <div class="star-carousel-track" id="starTrack">
          <?php foreach ($_sc_students as $s): ?>
            <div class="star-card">
              <div class="star-card-img-wrap">
                <span class="star-card-ribbon"><i class="fas fa-trophy me-1" aria-hidden="true"></i>Grammar Place <?= htmlspecialchars($s['year']) ?></span>
                <img src="<?= htmlspecialchars($s['img']) ?>" alt="<?= htmlspecialchars($s['name']) ?> — <?= htmlspecialchars($s['school']) ?>" loading="lazy">
              </div>
              <div class="star-card-body">
                <div class="star-name"><?= htmlspecialchars($s['name']) ?></div>
                <div class="star-placement"><?= htmlspecialchars($s['school']) ?></div>
                <div class="star-school"><i class="fas fa-map-marker-alt text-gold"></i> <?= htmlspecialchars($s['programme']) ?></div>
                <div class="star-quote"><?= htmlspecialchars($s['quote']) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <button class="star-carousel-btn star-carousel-next" id="starNext" aria-label="Next"><i class="fas fa-chevron-right"></i></button>
    </div>
    <div class="star-carousel-dots" id="starDots"></div>
  </div>
</section>
<?php endif; ?>
