<?php
$page_title    = 'Events & Gallery';
$meta_description = 'Events, photos and videos from Talent Pool Academy — summer camps, talent shows, awards days and more at our Chadwell Heath and Chelmsford centres.';
require_once 'includes/config.php';

// ============================================================
// EVENTS DATA
// media item: type (image|video), src, poster (video only), alt
// video src: YouTube URL | Vimeo URL | .mp4/.webm path | '' (coming soon)
// ============================================================
$events = [
    [
        'id'      => 'talent-show-2025-romford',
        'date'    => '2025-12-14',
        'name'    => 'Annual Talent Show 2025 - Chadwell Heath',
        'branch'  => 'romford',
        'type'    => 'Talent Show',
        'content' => 'Talent Pool Academy hosted a vibrant Talent Show 2025 in Chadwell Heath, graced by Mayor Princess Bright. The evening featured singing, dancing, music, poetry, and more, bringing together students, families, and friends for a joyful celebration of talent.',
        'media'   => [
            ['type'=>'image','src'=> SITE_URL. '/images/events/romford/talent-show-2025-talent-pool-academy-romford-1.jpeg','alt'=>'Talent Show 2025 — Group Activity'],
            ['type'=>'image','src'=> SITE_URL. '/images/events/romford/talent-show-2025-talent-pool-academy-romford-2.jpeg','alt'=>'Talent Show 2025 — Singing Performance'],
            ['type'=>'image','src'=> SITE_URL. '/images/events/romford/talent-show-2025-talent-pool-academy-romford-3.jpeg','alt'=>'Talent Show 2025 — Dance Performance'],
            ['type'=>'image','src'=> SITE_URL. '/images/events/romford/talent-show-2025-talent-pool-academy-romford-4.jpeg','alt'=>'Talent Show 2025 — Poetry Recitation'],
            ['type'=>'image','src'=> SITE_URL. '/images/events/romford/talent-show-2025-talent-pool-academy-romford-5.jpeg','alt'=>'Talent Show 2025 — Comedy Act'],
            ['type'=>'image','src'=> SITE_URL. '/images/events/romford/talent-show-2025-talent-pool-academy-romford-6.jpeg','alt'=>'Talent Show 2025 — Audience Moment'],
            ['type'=>'image','src'=> SITE_URL. '/images/events/romford/talent-show-2025-talent-pool-academy-romford-7.jpeg','alt'=>'Talent Show 2025 — Stage Performance'],
            ['type'=>'image','src'=> SITE_URL. '/images/events/romford/talent-show-2025-talent-pool-academy-romford-8.jpeg','alt'=>'Talent Show 2025 — Musical Performance'],
            ['type'=>'image','src'=> SITE_URL. '/images/events/romford/talent-show-2025-talent-pool-academy-romford-9.jpeg','alt'=>'Talent Show 2025 — Group Finale'],
            ['type'=>'image','src'=> SITE_URL. '/images/events/romford/talent-show-2025-talent-pool-academy-romford-10.jpeg','alt'=>'Talent Show 2025 — Mayor Princess Bright'],
            ['type'=>'image','src'=> SITE_URL. '/images/events/romford/talent-show-2025-talent-pool-academy-romford-11.jpeg','alt'=>'Talent Show 2025 — Celebration Photo'],

        ],
    ],

  [
    'id'      => 'talent-show-2025-chelmsford',
    'date'    => '2025-12-14',
    'name'    => 'Annual Talent Show 2025 - Chelmsford',
    'branch'  => 'chelmsford',
    'type'    => 'Talent Show',
    'content' => 'Talent Pool Academy hosted Talent Show 2025 in Chelmsford, with the Deputy Mayor in attendance. The event featured singing, dancing, music, poetry, and more, creating a lively evening for students, families, and friends to celebrate creativity and talent together.',
    'media'   => [
      ['type' => 'image', 'src' => SITE_URL . '/images/events/chelmsford/talent-show-2025-talent-pool-academy-chelmsford-1.jpeg', 'alt' => 'Talent Show 2025 — Group Activity'],
      ['type' => 'image', 'src' => SITE_URL . '/images/events/chelmsford/talent-show-2025-talent-pool-academy-chelmsford-2.jpeg', 'alt' => 'Talent Show 2025 — Singing Performance'],
      ['type' => 'image', 'src' => SITE_URL . '/images/events/chelmsford/talent-show-2025-talent-pool-academy-chelmsford-3.jpeg', 'alt' => 'Talent Show 2025 — Dance Performance'],
      ['type' => 'image', 'src' => SITE_URL . '/images/events/chelmsford/talent-show-2025-talent-pool-academy-chelmsford-4.jpeg', 'alt' => 'Talent Show 2025 — Poetry Recitation'],
      ['type' => 'image', 'src' => SITE_URL . '/images/events/chelmsford/talent-show-2025-talent-pool-academy-chelmsford-5.jpeg', 'alt' => 'Talent Show 2025 — Comedy Act'],
      
    ],
  ],
  [
    'id'      => 'founder-message-2026',
    'date'    => '2025-12-10',
    'name'    => "Founder's New Year Message 2026",
    'branch'  => 'both',
    'type'    => 'Video',
    'content' => 'Mrs Meena Kumar shares her hopes and vision for 2026 — reflecting on a brilliant 2025 and looking forward to another year of transforming young lives through education.',
    'media'   => [
      ['type' => 'video', 'src' => SITE_URL . '/images/events/both/founders-message.mp4', 'poster' => SITE_URL . '/images/founder.png', 'alt' => "Founder's Message 2026"],
    ],
  ],
   
    [
        'id'      => 'mock-test-both-2025',
        'date'    => '2025-05-05',
        'name'    => 'Mock Test for Year 5 Students',
        'branch'  => 'both',
        'type'    => 'Mock Test',
        'content' => 'On May 5th, 2025, we hosted a comprehensive mock test for Year 5 students at both our Chadwell Heath and Chelmsford centres. The test covered key subjects including English, Maths, and Verbal Reasoning, providing students with valuable practice and insights ahead of their 11 Plus exams.',
        'media'   => [
            ['type'=>'image','src'=> SITE_URL.'/images/classroom.webp','alt'=>'Chadwell Heath Open Day 2024'],
            ['type' => 'image', 'src' => SITE_URL . '/images/events/both/mock-test-2025-both-centres-1.jpeg', 'alt' => 'Mock Test 2025 — Chadwell Heath Centre'],
            ['type' => 'image', 'src' => SITE_URL . '/images/events/both/mock-test-2025-both-centres-2.jpeg', 'alt' => 'Mock Test 2025 — Chelmsford Centre'],
      ['type' => 'image', 'src' => SITE_URL . '/images/events/both/mock-test-2025-both-centres-3.jpeg', 'alt' => 'Mock Test 2025 — Chelmsford Centre'],

        ],
    ],
    

];

// Sort: most recent first
usort($events, fn($a, $b) => strcmp($b['date'], $a['date']));

// Derive filter options
$all_types = array_values(array_unique(array_column($events, 'type')));
sort($all_types);
$years = array_values(array_unique(array_map(fn($e) => substr($e['date'], 0, 4), $events)));
rsort($years);
$all_branches = ['romford' => 'Chadwell Heath', 'chelmsford' => 'Chelmsford', 'both' => 'Both Centres', 'online' => 'Online'];
$total_events = count($events);

$extra_css = <<<'EVTSTYLES'
<style>
/* =============================================
   EVENTS PAGE — Page-specific styles
   ============================================= */

/* --- Filter bar --- */
.events-filter-section {
  padding: 40px 0 10px;
}
.events-filter-bar-wrap {
  background: var(--white);
  border: 1px solid var(--gray-light);
  border-radius: var(--radius-lg);
  padding: 1.5rem 1.75rem;
  box-shadow: var(--shadow-sm);
}
.filter-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
}
.filter-row + .filter-row { margin-top: 0.9rem; padding-top: 0.9rem; border-top: 1px solid var(--gray-light); }
.filter-row-label {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--gray);
  min-width: 44px;
  flex-shrink: 0;
}
.filter-pills-group {
  display: flex;
  gap: 0.4rem;
  flex-wrap: wrap;
  flex: 1;
}
.filter-pill-btn {
  padding: 0.35rem 1rem;
  border-radius: var(--radius-xl);
  border: 1.5px solid rgba(10,22,40,0.14);
  background: var(--white);
  color: var(--text-muted);
  font-weight: 600;
  font-size: 0.82rem;
  cursor: pointer;
  transition: var(--transition);
  white-space: nowrap;
  min-height: 34px;
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
}
.filter-pill-btn:hover {
  border-color: var(--navy);
  color: var(--navy);
}
.filter-pill-btn.active {
  background: var(--navy);
  border-color: var(--navy);
  color: var(--white);
}
.filter-select {
  border: 1.5px solid rgba(10,22,40,0.14);
  border-radius: var(--radius-md);
  padding: 0.35rem 2rem 0.35rem 0.85rem;
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--text);
  font-family: var(--font-body);
  background: var(--white) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath fill='%238892a4' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E") no-repeat right 0.6rem center;
  -webkit-appearance: none;
  appearance: none;
  cursor: pointer;
  transition: var(--transition);
  height: 34px;
}
.filter-select:focus { outline: none; border-color: var(--gold); box-shadow: 0 0 0 3px rgba(245,166,35,0.15); }
.filter-meta {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-left: auto;
}
.events-count {
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--text-muted);
  white-space: nowrap;
}
.btn-filter-reset {
  background: none;
  border: 1.5px solid rgba(10,22,40,0.14);
  border-radius: var(--radius-md);
  padding: 0.3rem 0.8rem;
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--text-muted);
  cursor: pointer;
  transition: var(--transition);
  height: 34px;
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
}
.btn-filter-reset:hover { border-color: var(--navy); color: var(--navy); }

/* --- Event cards --- */
.event-card {
  cursor: pointer;
}
.event-card:focus-visible {
  outline: 3px solid var(--gold);
  outline-offset: 3px;
  border-radius: var(--radius-lg);
}
.event-video-indicator {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%,-50%);
  width: 56px; height: 56px;
  background: rgba(245,166,35,0.92);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  color: var(--navy);
  font-size: 1.2rem;
  z-index: 2;
  transition: var(--transition);
  border: 3px solid rgba(255,255,255,0.85);
  pointer-events: none;
}
.event-card:hover .event-video-indicator {
  background: var(--gold);
  transform: translate(-50%,-50%) scale(1.1);
}
.event-media-count {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  background: rgba(10,22,40,0.65);
  color: #fff;
  padding: 0.22rem 0.6rem;
  border-radius: var(--radius-xl);
  font-size: 0.7rem;
  font-weight: 700;
  margin-left: 0.4rem;
  backdrop-filter: blur(4px);
  pointer-events: none;
}
.event-card-cta {
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--navy);
  display: flex;
  align-items: center;
  gap: 0.3rem;
  margin-left: auto;
  transition: var(--transition);
}
.event-card:hover .event-card-cta { color: var(--gold); gap: 0.5rem; }

/* --- No results --- */
.events-empty-state {
  text-align: center;
  padding: 5rem 1rem;
}
.events-empty-icon {
  width: 80px; height: 80px;
  border-radius: 50%;
  background: var(--gray-light);
  display: flex; align-items: center; justify-content: center;
  font-size: 2rem;
  color: var(--gray);
  margin: 0 auto 1.5rem;
}

/* =============================================
   LIGHTBOX
   ============================================= */
.tpa-lightbox {
  position: fixed;
  inset: 0;
  z-index: 9000;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.25s ease;
}
.tpa-lightbox.lb-open {
  opacity: 1;
  pointer-events: all;
}
.tpa-lb-backdrop {
  position: absolute;
  inset: 0;
  background: rgba(4,10,20,0.94);
  backdrop-filter: blur(6px);
  cursor: zoom-out;
}
.tpa-lb-panel {
  position: relative;
  z-index: 1;
  width: 90vw;
  max-width: 1100px;
  max-height: 92vh;
  display: flex;
  flex-direction: column;
  border-radius: var(--radius-lg);
  overflow: hidden;
  transform: scale(0.95);
  transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1);
}
.tpa-lightbox.lb-open .tpa-lb-panel {
  transform: scale(1);
}
.tpa-lb-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.9rem 1.25rem;
  background: rgba(10,22,40,0.95);
  border-bottom: 1px solid rgba(255,255,255,0.08);
  flex-shrink: 0;
}
.tpa-lb-title {
  font-family: var(--font-heading);
  font-size: 1rem;
  font-weight: 700;
  color: var(--white);
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.tpa-lb-counter {
  font-size: 0.78rem;
  font-weight: 600;
  color: rgba(255,255,255,0.5);
  white-space: nowrap;
  flex-shrink: 0;
}
.tpa-lb-close {
  width: 36px; height: 36px;
  border-radius: 50%;
  background: rgba(255,255,255,0.1);
  border: none;
  cursor: pointer;
  color: rgba(255,255,255,0.8);
  font-size: 1rem;
  display: flex; align-items: center; justify-content: center;
  transition: var(--transition);
  flex-shrink: 0;
}
.tpa-lb-close:hover { background: rgba(255,255,255,0.2); color: #fff; }

.tpa-lb-stage {
  flex: 1;
  min-height: 0;
  background: #000;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}
.tpa-lb-stage img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  display: block;
  user-select: none;
}
.lb-video-wrap {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.lb-video-wrap iframe,
.lb-video-wrap video {
  width: 100%;
  aspect-ratio: 16/9;
  max-height: 100%;
  border: none;
  background: #000;
}
.lb-video-soon {
  text-align: center;
  color: rgba(255,255,255,0.7);
  padding: 3rem;
}
.lb-video-soon i { font-size: 3.5rem; color: var(--gold); display: block; margin-bottom: 1rem; }
.lb-video-soon h4 { color: #fff; font-family: var(--font-heading); margin-bottom: 0.5rem; }
.lb-video-soon p { font-size: 0.9rem; margin-bottom: 1.25rem; }

.tpa-lb-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 2;
  width: 44px; height: 44px;
  border-radius: 50%;
  background: rgba(255,255,255,0.12);
  border: 1.5px solid rgba(255,255,255,0.2);
  color: #fff;
  font-size: 1rem;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: var(--transition);
  backdrop-filter: blur(4px);
}
.tpa-lb-nav:hover { background: rgba(245,166,35,0.85); border-color: var(--gold); color: var(--navy); }
.tpa-lb-nav:disabled { opacity: 0.25; pointer-events: none; }
.tpa-lb-prev { left: 0.75rem; }
.tpa-lb-next { right: 0.75rem; }

.tpa-lb-thumbs {
  display: flex;
  gap: 0.4rem;
  padding: 0.75rem 1.25rem;
  background: rgba(10,22,40,0.95);
  border-top: 1px solid rgba(255,255,255,0.08);
  overflow-x: auto;
  flex-shrink: 0;
  scrollbar-width: thin;
  scrollbar-color: var(--gold) transparent;
}
.tpa-lb-thumbs::-webkit-scrollbar { height: 3px; }
.tpa-lb-thumbs::-webkit-scrollbar-thumb { background: var(--gold); border-radius: 2px; }
.tpa-lb-thumb {
  flex-shrink: 0;
  width: 64px; height: 44px;
  border-radius: 6px;
  overflow: hidden;
  cursor: pointer;
  border: 2px solid transparent;
  transition: var(--transition);
  position: relative;
  background: rgba(255,255,255,0.08);
}
.tpa-lb-thumb img {
  width: 100%; height: 100%;
  object-fit: cover;
  display: block;
}
.tpa-lb-thumb.active { border-color: var(--gold); }
.tpa-lb-thumb:hover { border-color: rgba(245,166,35,0.6); }
.tpa-lb-thumb-video-icon {
  position: absolute;
  inset: 0;
  display: flex; align-items: center; justify-content: center;
  background: rgba(0,0,0,0.45);
  color: var(--gold);
  font-size: 0.75rem;
}

/* Single-media: hide nav buttons and thumb strip */
.tpa-lightbox.lb-single .tpa-lb-nav,
.tpa-lightbox.lb-single .tpa-lb-thumbs { display: none; }

/* Stage height */
.tpa-lb-stage {
  height: calc(92vh - 56px - 72px); /* viewport - header - thumbs */
}
.tpa-lightbox.lb-single .tpa-lb-stage {
  height: calc(92vh - 56px);
}

@media (max-width: 768px) {
  .tpa-lb-panel { width: 98vw; max-height: 96vh; border-radius: var(--radius-md); }
  .tpa-lb-stage { height: 55vw; }
  .tpa-lightbox.lb-single .tpa-lb-stage { height: 60vw; }
  .tpa-lb-prev { left: 0.4rem; }
  .tpa-lb-next { right: 0.4rem; }
  .tpa-lb-nav { width: 38px; height: 38px; }
  .tpa-lb-thumb { width: 52px; height: 36px; }
  .filter-row-label { display: none; }
}
</style>
EVTSTYLES;

$schema_extra = '<script type="application/ld+json">' . json_encode([
  '@context'=>'https://schema.org','@type'=>'ImageGallery',
  'name'=>'Events & Gallery — Talent Pool Academy',
  'url'=>'https://www.talentpoolacademy.com/events.php',
  'description'=>'Photos and videos from Talent Pool Academy events — summer camps, talent shows, awards days and academic achievements at our Chadwell Heath and Chelmsford centres.',
  'author'=>['@type'=>'EducationalOrganization','name'=>'Talent Pool Academy','url'=>'https://www.talentpoolacademy.com'],
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
            <li class="breadcrumb-item active">Events &amp; Gallery</li>
          </ol>
        </nav>
        <div class="section-tag mb-3"><i class="fas fa-camera-retro"></i> Our Community</div>
        <h1>Events &amp; <span style="color:var(--gold);">Gallery</span></h1>
        <p class="mb-0">Behind the scenes at Talent Pool Academy — summer camps, talent shows, awards days, open days and more from both our Chadwell Heath and Chelmsford centres.</p>
      </div>
    </div>
  </div>
</section>

<!-- EVENTS SECTION -->
<section class="section-pad">
  <div class="container">

    <!-- FILTER BAR -->
    <div class="events-filter-bar-wrap mb-5" data-aos="fade-up">
      <!-- Row 1: Branch pills + Year select + meta -->
      <div class="filter-row">
        <span class="filter-row-label">Branch</span>
        <div class="filter-pills-group" id="branchPills" role="group" aria-label="Filter by branch">
          <button class="filter-pill-btn active" data-filter="branch" data-value="" onclick="applyPillFilter(this,'branch')">
            <i class="fas fa-globe-europe"></i> All
          </button>
          <?php foreach ($all_branches as $key => $label): ?>
          <button class="filter-pill-btn" data-filter="branch" data-value="<?= $key ?>" onclick="applyPillFilter(this,'branch')">
            <?php if ($key==='romford'): ?><i class="fas fa-map-marker-alt"></i>
            <?php elseif ($key==='chelmsford'): ?><i class="fas fa-map-marker"></i>
            <?php elseif ($key==='online'): ?><i class="fas fa-wifi"></i>
            <?php else: ?><i class="fas fa-city"></i>
            <?php endif; ?>
            <?= $label ?>
          </button>
          <?php endforeach; ?>
        </div>
        <div class="filter-meta">
          <button class="btn-filter-reset" id="resetBtn" onclick="resetFilters()" style="display:none;" aria-label="Reset all filters">
            <i class="fas fa-times"></i> Reset
          </button>
          <span class="events-count" id="resultCount"><?= $total_events ?> event<?= $total_events !== 1 ? 's' : '' ?></span>
        </div>
      </div>
      <!-- Row 2: Type pills + Year select -->
      <?php if (count($all_types) > 1): ?>
      <div class="filter-row">
        <span class="filter-row-label">Type</span>
        <div class="filter-pills-group" id="typePills" role="group" aria-label="Filter by event type">
          <button class="filter-pill-btn active" data-filter="type" data-value="" onclick="applyPillFilter(this,'type')">All Types</button>
          <?php foreach ($all_types as $t): ?>
          <button class="filter-pill-btn" data-filter="type" data-value="<?= htmlspecialchars($t) ?>" onclick="applyPillFilter(this,'type')">
            <?= htmlspecialchars($t) ?>
          </button>
          <?php endforeach; ?>
        </div>
        <?php if (count($years) > 1): ?>
        <select id="yearFilter" class="filter-select ms-auto" onchange="applySelectFilter()" aria-label="Filter by year">
          <option value="">All Years</option>
          <?php foreach ($years as $y): ?>
          <option value="<?= $y ?>"><?= $y ?></option>
          <?php endforeach; ?>
        </select>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- EVENTS GRID -->
    <div class="row g-4" id="eventsGrid">
      <?php foreach ($events as $ev):
        $dateFormatted = date('j F Y', strtotime($ev['date']));
        $branchLabel   = $all_branches[$ev['branch']] ?? ucfirst($ev['branch']);
        $branchClass   = 'event-branch-' . $ev['branch'];
        $mediaCount    = count($ev['media']);
        $firstMedia    = $ev['media'][0] ?? null;
        $hasVideo      = array_filter($ev['media'], fn($m) => $m['type'] === 'video');
        $isVideoFirst  = $firstMedia && $firstMedia['type'] === 'video';
        $thumbSrc      = $isVideoFirst ? ($firstMedia['poster'] ?? '') : ($firstMedia['src'] ?? '');
      ?>
      <div class="col-md-6 col-lg-4 event-item"
           data-branch="<?= $ev['branch'] ?>"
           data-type="<?= htmlspecialchars($ev['type']) ?>"
           data-year="<?= substr($ev['date'], 0, 4) ?>"
           data-aos="fade-up">
        <div class="event-card h-100"
             tabindex="0"
             role="button"
             aria-label="Open gallery for <?= htmlspecialchars($ev['name']) ?>"
             onclick="openLightbox('<?= $ev['id'] ?>', 0)"
             onkeydown="if(event.key==='Enter'||event.key===' ')openLightbox('<?= $ev['id'] ?>',0)">

          <!-- Media thumbnail -->
          <?php if ($firstMedia): ?>
          <div class="event-card-media">
            <img src="<?= htmlspecialchars($thumbSrc) ?>"
                 alt="<?= htmlspecialchars($firstMedia['alt']) ?>"
                 loading="lazy">

            <?php if ($isVideoFirst): ?>
            <div class="event-video-indicator" aria-hidden="true"><i class="fas fa-play"></i></div>
            <?php endif; ?>

            <div class="event-card-media-overlay">
              <span class="event-branch-badge <?= $branchClass ?>">
                <i class="fas fa-map-marker-alt" aria-hidden="true"></i> <?= $branchLabel ?>
              </span>
              <?php if ($mediaCount > 1): ?>
              <span class="event-media-count">
                <i class="fas fa-images" aria-hidden="true"></i> <?= $mediaCount ?>
              </span>
              <?php elseif ($hasVideo): ?>
              <span class="event-media-count">
                <i class="fas fa-video" aria-hidden="true"></i> Video
              </span>
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>

          <!-- Card body -->
          <div class="event-card-body">
            <div class="event-card-date">
              <i class="fas fa-calendar-alt" aria-hidden="true"></i> <?= $dateFormatted ?>
            </div>
            <h3 class="event-card-title"><?= htmlspecialchars($ev['name']) ?></h3>
            <p class="event-card-desc"><?= htmlspecialchars($ev['content']) ?></p>
            <div class="event-card-footer">
              <span class="event-type-pill">
                <i class="fas fa-tag me-1" aria-hidden="true"></i><?= htmlspecialchars($ev['type']) ?>
              </span>
              <span class="event-card-cta">
                <?= $mediaCount > 1 ? 'View gallery' : ($isVideoFirst ? 'Watch video' : 'View photo') ?>
                <i class="fas fa-arrow-right" aria-hidden="true"></i>
              </span>
            </div>
          </div>

        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- No results empty state -->
    <div id="noResults" style="display:none;" aria-live="polite">
      <div class="events-empty-state">
        <div class="events-empty-icon"><i class="fas fa-search" aria-hidden="true"></i></div>
        <h4 style="color:var(--navy);margin-bottom:.5rem;">No events found</h4>
        <p style="color:var(--text-muted);margin-bottom:1.5rem;">No events match your current filters.</p>
        <button class="btn-outline-tpa" onclick="resetFilters()" style="font-size:.9rem;min-height:44px;">
          <i class="fas fa-redo me-2" aria-hidden="true"></i> Clear filters
        </button>
      </div>
    </div>

  </div>
</section>

<!-- SHARE CTA -->
<section class="section-pad-sm section-bg">
  <div class="container">
    <div class="row align-items-center gy-4">
      <div class="col-lg-8" data-aos="fade-right">
        <div class="section-tag mb-3"><i class="fas fa-camera" aria-hidden="true"></i> Share Your Memories</div>
        <h2 class="section-title" style="font-size:1.75rem;">Have photos or videos from a TPA event?</h2>
        <p style="color:var(--text-muted);margin-bottom:0;">We love seeing our community through your eyes! Share your photos and videos from TPA events with us via WhatsApp and we may feature them here.</p>
      </div>
      <div class="col-lg-4 text-lg-end" data-aos="fade-left">
        <a href="https://wa.me/<?= WHATSAPP ?>?text=Hi%2C%20I%27d%20like%20to%20share%20photos%20from%20a%20TPA%20event"
           class="btn-primary-tpa"
           target="_blank"
           rel="noopener noreferrer">
          <i class="fab fa-whatsapp me-2" aria-hidden="true"></i> Share via WhatsApp
        </a>
      </div>
    </div>
  </div>
</section>

<!-- =============================================
     LIGHTBOX
     ============================================= -->
<div id="tpaLightbox"
     class="tpa-lightbox"
     role="dialog"
     aria-modal="true"
     aria-label="Event gallery"
     style="display:none;">

  <div class="tpa-lb-backdrop" onclick="closeLightbox()"></div>

  <div class="tpa-lb-panel">

    <!-- Header -->
    <div class="tpa-lb-header">
      <span class="tpa-lb-title" id="lbTitle"></span>
      <span class="tpa-lb-counter" id="lbCounter"></span>
      <button class="tpa-lb-close" onclick="closeLightbox()" aria-label="Close gallery">
        <i class="fas fa-times" aria-hidden="true"></i>
      </button>
    </div>

    <!-- Main stage -->
    <div class="tpa-lb-stage" id="lbStage"></div>

    <!-- Prev / Next nav (inside panel, over stage) -->
    <button class="tpa-lb-nav tpa-lb-prev" id="lbPrev" onclick="lbNavigate(-1)" aria-label="Previous media">
      <i class="fas fa-chevron-left" aria-hidden="true"></i>
    </button>
    <button class="tpa-lb-nav tpa-lb-next" id="lbNext" onclick="lbNavigate(1)" aria-label="Next media">
      <i class="fas fa-chevron-right" aria-hidden="true"></i>
    </button>

    <!-- Thumbnail strip -->
    <div class="tpa-lb-thumbs" id="lbThumbs" role="list" aria-label="Media thumbnails"></div>

  </div>
</div>

<!-- Events data for JS -->
<script>
const eventsData = <?= json_encode(
  array_map(fn($e) => [
    'id'     => $e['id'],
    'name'   => $e['name'],
    'media'  => $e['media'],
  ], $events),
  JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
) ?>;

// =============================================
//  FILTER LOGIC
// =============================================
let activeFilters = { branch: '', type: '', year: '' };

function applyPillFilter(btn, group) {
  document.querySelectorAll(`[data-filter="${group}"]`).forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  activeFilters[group] = btn.dataset.value;
  runFilter();
}
function applySelectFilter() {
  activeFilters.year = document.getElementById('yearFilter')?.value || '';
  runFilter();
}
function resetFilters() {
  activeFilters = { branch: '', type: '', year: '' };
  document.querySelectorAll('.filter-pill-btn').forEach(b => b.classList.toggle('active', b.dataset.value === ''));
  const ys = document.getElementById('yearFilter');
  if (ys) ys.value = '';
  runFilter();
}
function runFilter() {
  const { branch, type, year } = activeFilters;
  const items = document.querySelectorAll('.event-item');
  let visible = 0;

  items.forEach(item => {
    const bMatch = !branch
      || item.dataset.branch === branch
      || (item.dataset.branch === 'both' && (branch === 'romford' || branch === 'chelmsford'));
    const tMatch = !type  || item.dataset.type === type;
    const yMatch = !year  || item.dataset.year === year;
    const show   = bMatch && tMatch && yMatch;
    item.style.display = show ? '' : 'none';
    if (show) visible++;
  });

  document.getElementById('noResults').style.display = visible === 0 ? 'block' : 'none';
  const label = visible + ' event' + (visible !== 1 ? 's' : '');
  document.getElementById('resultCount').textContent = label;

  const isFiltered = branch || type || year;
  const resetBtn = document.getElementById('resetBtn');
  if (resetBtn) resetBtn.style.display = isFiltered ? 'inline-flex' : 'none';
}

// =============================================
//  LIGHTBOX
// =============================================
let lbEventId  = null;
let lbItems    = [];
let lbCurrent  = 0;
const lb       = document.getElementById('tpaLightbox');
const lbStage  = document.getElementById('lbStage');
const lbThumbs = document.getElementById('lbThumbs');
const lbTitle  = document.getElementById('lbTitle');
const lbCounter = document.getElementById('lbCounter');
let lbPrevFocus = null;

function openLightbox(eventId, startIndex) {
  const ev = eventsData.find(e => e.id === eventId);
  if (!ev || !ev.media.length) return;

  lbEventId = eventId;
  lbItems   = ev.media;
  lbCurrent = startIndex || 0;
  lbTitle.textContent = ev.name;

  // Single vs multi
  lb.classList.toggle('lb-single', lbItems.length === 1);

  buildThumbs();
  renderStage(lbCurrent);

  lbPrevFocus = document.activeElement;
  lb.style.display = 'flex';
  requestAnimationFrame(() => lb.classList.add('lb-open'));
  document.body.style.overflow = 'hidden';
  document.getElementById('lbStage').focus?.();
}

function closeLightbox() {
  lb.classList.remove('lb-open');
  stopAllMedia();
  setTimeout(() => {
    lb.style.display = 'none';
    document.body.style.overflow = '';
  }, 260);
  if (lbPrevFocus) lbPrevFocus.focus?.();
}

function lbNavigate(dir) {
  const next = (lbCurrent + dir + lbItems.length) % lbItems.length;
  renderStage(next);
}

function renderStage(index) {
  stopAllMedia();
  lbCurrent = index;
  const item = lbItems[index];

  // Update counter
  lbCounter.textContent = lbItems.length > 1 ? `${index + 1} / ${lbItems.length}` : '';

  // Update nav buttons
  const prevBtn = document.getElementById('lbPrev');
  const nextBtn = document.getElementById('lbNext');
  if (lbItems.length <= 1) {
    if (prevBtn) prevBtn.style.display = 'none';
    if (nextBtn) nextBtn.style.display = 'none';
  } else {
    if (prevBtn) prevBtn.style.display = '';
    if (nextBtn) nextBtn.style.display = '';
  }

  // Update active thumb
  document.querySelectorAll('.tpa-lb-thumb').forEach((t, i) => t.classList.toggle('active', i === index));

  // Render content
  if (item.type === 'video') {
    lbStage.innerHTML = buildVideoHtml(item.src, item.poster, item.alt);
  } else {
    lbStage.innerHTML = `<img src="${escHtml(item.src)}" alt="${escHtml(item.alt || '')}" draggable="false">`;
  }
}

function buildVideoHtml(src, poster, alt) {
  if (!src) {
    return `<div class="lb-video-soon">
      <i class="fas fa-film" aria-hidden="true"></i>
      <h4>Video Coming Soon</h4>
      <p>This video will be uploaded shortly.</p>
      <a href="https://wa.me/<?= WHATSAPP ?>?text=Hi%2C+I%27d+like+to+find+out+more+about+TPA+events"
         class="btn-primary-tpa" target="_blank" rel="noopener" style="justify-content:center;display:inline-flex;min-height:44px;">
        <i class="fab fa-whatsapp me-2"></i>Contact us on WhatsApp
      </a>
    </div>`;
  }

  // YouTube
  const yt = src.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
  if (yt) {
    return `<div class="lb-video-wrap">
      <iframe src="https://www.youtube.com/embed/${yt[1]}?autoplay=1&rel=0&modestbranding=1"
              title="${escHtml(alt || 'TPA Event Video')}"
              allow="autoplay; fullscreen; picture-in-picture"
              allowfullscreen></iframe>
    </div>`;
  }

  // Vimeo
  const vimeo = src.match(/vimeo\.com\/(\d+)/);
  if (vimeo) {
    return `<div class="lb-video-wrap">
      <iframe src="https://player.vimeo.com/video/${vimeo[1]}?autoplay=1&title=0&byline=0"
              title="${escHtml(alt || 'TPA Event Video')}"
              allow="autoplay; fullscreen"
              allowfullscreen></iframe>
    </div>`;
  }

  // Native video (.mp4 / .webm / .ogg)
  if (/\.(mp4|webm|ogg|mov)(\?|$)/i.test(src)) {
    return `<div class="lb-video-wrap">
      <video src="${escHtml(src)}"
             ${poster ? `poster="${escHtml(poster)}"` : ''}
             controls autoplay playsinline
             aria-label="${escHtml(alt || 'TPA Event Video')}">
        Your browser does not support the video tag.
      </video>
    </div>`;
  }

  // Unknown src — try native as fallback
  return `<div class="lb-video-wrap">
    <video src="${escHtml(src)}"
           ${poster ? `poster="${escHtml(poster)}"` : ''}
           controls autoplay playsinline
           aria-label="${escHtml(alt || 'TPA Event Video')}">
      Your browser does not support the video tag.
    </video>
  </div>`;
}

function buildThumbs() {
  lbThumbs.innerHTML = lbItems.map((item, i) => {
    const thumbSrc = item.type === 'video' ? (item.poster || '') : item.src;
    const isVideo  = item.type === 'video';
    return `<div class="tpa-lb-thumb ${i === lbCurrent ? 'active' : ''}"
                 role="listitem"
                 tabindex="0"
                 aria-label="${escHtml(item.alt || `Media ${i+1}`)}"
                 onclick="renderStage(${i})"
                 onkeydown="if(event.key==='Enter')renderStage(${i})">
      ${thumbSrc ? `<img src="${escHtml(thumbSrc)}" alt="" loading="lazy" aria-hidden="true">` : ''}
      ${isVideo ? `<div class="tpa-lb-thumb-video-icon" aria-hidden="true"><i class="fas fa-play"></i></div>` : ''}
    </div>`;
  }).join('');
}

function stopAllMedia() {
  // Pause native video
  lbStage.querySelectorAll('video').forEach(v => { v.pause(); v.src = ''; });
  // Remove iframe to stop embed video
  lbStage.querySelectorAll('iframe').forEach(f => { f.src = ''; });
}

function escHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;')
    .replace(/'/g,'&#39;');
}

// Keyboard navigation
document.addEventListener('keydown', e => {
  if (lb.style.display === 'none' || !lb.classList.contains('lb-open')) return;
  if (e.key === 'Escape')      { e.preventDefault(); closeLightbox(); }
  if (e.key === 'ArrowLeft')   { e.preventDefault(); lbNavigate(-1); }
  if (e.key === 'ArrowRight')  { e.preventDefault(); lbNavigate(1); }
});

// Touch swipe on lightbox
(function() {
  let sx = 0, sy = 0;
  lbStage.addEventListener('touchstart', e => { sx = e.touches[0].clientX; sy = e.touches[0].clientY; }, { passive: true });
  lbStage.addEventListener('touchend', e => {
    const dx = sx - e.changedTouches[0].clientX;
    const dy = sy - e.changedTouches[0].clientY;
    if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 50) lbNavigate(dx > 0 ? 1 : -1);
  });
})();
</script>

<?php require_once 'includes/footer.php'; ?>
