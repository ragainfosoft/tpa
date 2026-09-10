<?php
$page_title       = 'Books & Workbooks | Talent Pool Academy';
$meta_description = 'Browse every Talent Pool Academy workbook — Comprehension, Creative Writing, Grammar, Mathematical Reasoning, Spelling, Synonyms & Antonyms, Vocabulary Builder and Sure Pass. Written by our teachers and used in every centre.';

require_once 'includes/config.php';

// ─────────────────────────────────────────────────────────────────────
// BOOK CATALOGUE
// Covers live in images/books/<slug>-book-<n>.jpg
// `books` lists the book numbers that exist in each series — Creative
// Writing deliberately has no Book 3.
// ─────────────────────────────────────────────────────────────────────
$series = [
  'comprehension' => [
    'name'   => 'Comprehension',
    'accent' => '#16213E',
    'icon'   => 'fa-book-open-reader',
    'books'  => [1, 2, 3, 4, 5, 6],
    'blurb'  => 'Graded reading passages with question sets that build inference, retrieval and vocabulary skills for SATs and 11 Plus.',
  ],
  'creative-writing' => [
    'name'   => 'Creative Writing',
    'accent' => '#4B2E9E',
    'icon'   => 'fa-feather-pointed',
    'books'  => [1, 2, 4, 5, 6],
    'blurb'  => 'Structured story planning, descriptive technique and narrative practice that lifts writing from competent to compelling.',
  ],
  'grammar' => [
    'name'   => 'Grammar',
    'accent' => '#6B2FA0',
    'icon'   => 'fa-spell-check',
    'books'  => [1, 2, 3, 4, 5, 6],
    'blurb'  => 'Word classes, punctuation, tense and sentence construction — the full primary grammar curriculum, one step at a time.',
  ],
  'maths-reasoning' => [
    'name'   => 'Mathematical Reasoning',
    'accent' => '#5B32C4',
    'icon'   => 'fa-calculator',
    'books'  => [1, 2, 3, 4, 5],
    'blurb'  => 'Multi-step word problems and reasoning questions in the style children meet in SATs and grammar school entrance papers.',
  ],
  'spelling' => [
    'name'   => 'Spelling',
    'accent' => '#E01F26',
    'icon'   => 'fa-pen-to-square',
    'books'  => [1, 2, 3, 4, 5, 6],
    'blurb'  => 'The statutory word lists, spelling rules and common exception words, with dictation and weekly test frameworks.',
  ],
  'synonyms-antonyms' => [
    'name'   => 'Synonyms & Antonyms',
    'accent' => '#C4C935',
    'icon'   => 'fa-arrow-right-arrow-left',
    'books'  => [1, 2, 3],
    'blurb'  => 'Word-pair practice that widens vocabulary fast — the single highest-value area for 11 Plus verbal reasoning marks.',
  ],
  'vocabulary-builder' => [
    'name'   => 'Vocabulary Builder',
    'accent' => '#B8BE2E',
    'icon'   => 'fa-book-bookmark',
    'level'  => '11 Plus · Ages 9–11',
    'books'  => [3, 4],
    'blurb'  => 'The 11 Plus word bank — high-frequency exam vocabulary with definitions, context sentences and recall practice.',
  ],
  'sure-pass' => [
    'name'   => 'Sure Pass',
    'accent' => '#8A9022',
    'icon'   => 'fa-award',
    'level'  => '11 Plus · Ages 9–11',
    'books'  => [['file' => 'vocabulary', 'label' => 'Vocabulary']],
    'blurb'  => 'Our condensed final-stretch revision title — the vocabulary that matters most, in the weeks before the exam.',
  ],
];

// Sample pages: images/books/samples/<stem>/p1.jpg … written by the
// extraction script, with a manifest of how many pages each book has.
$sampleManifest = [];
$manifestFile = __DIR__ . '/images/books/samples/manifest.json';
if (is_readable($manifestFile)) {
  $sampleManifest = json_decode(file_get_contents($manifestFile), true) ?: [];
}

// Book number → suggested school year. Edit here to change every card.
// A series can override this wholesale with its own 'level' key.
function tpa_book_year(int $n): string  { return 'Year ' . $n; }
function tpa_book_ages(int $n): string  { return 'Ages ' . ($n + 4) . '–' . ($n + 5); }

/**
 * Normalise one entry of a series' `books` list.
 * An int is a numbered book; an array is a titled one (Sure Pass has no number).
 * Returns: label, cover filename stem, meta line and full title.
 */
function tpa_book(string $slug, array $series, $book): array {
  global $sampleManifest;
  if (is_array($book)) {
    $label = $book['label'];
    $file  = $slug . '-' . $book['file'];
    $meta  = $series['level'] ?? '';
  } else {
    $label = 'Book ' . $book;
    $file  = $slug . '-book-' . $book;
    $meta  = $series['level'] ?? (tpa_book_year($book) . ' · ' . tpa_book_ages($book));
  }
  return [
    'label'   => $label,
    'title'   => $series['name'] . ' — ' . $label,
    'stem'    => $file,
    'img'     => SITE_URL . '/images/books/' . $file . '.jpg',
    'meta'    => $meta,
    'samples' => (int)($sampleManifest[$file] ?? 0),
  ];
}

$totalBooks = array_sum(array_map(fn($s) => count($s['books']), $series));

$extra_css = '
<style>
  /* ── Filter bar ─────────────────────────────────────────── */
  .book-filter-wrap { position:sticky; top:var(--nav-h,76px); z-index:20; background:rgba(255,255,255,.94);
    backdrop-filter:blur(12px); border-bottom:1px solid var(--gray-light); padding:.9rem 0; }
  .book-filter { display:flex; gap:.55rem; overflow-x:auto; scrollbar-width:none; -ms-overflow-style:none;
    padding-bottom:.15rem; scroll-snap-type:x proximity; }
  .book-filter::-webkit-scrollbar { display:none; }
  .filter-chip { flex:0 0 auto; scroll-snap-align:start; display:inline-flex; align-items:center; gap:.5rem;
    border:1.5px solid var(--gray-light); background:var(--white); color:var(--text-muted);
    font-size:.86rem; font-weight:600; padding:.55rem 1.05rem; border-radius:40px; cursor:pointer;
    transition:background .2s,color .2s,border-color .2s,transform .2s; white-space:nowrap; }
  .filter-chip:hover { border-color:var(--gold); color:var(--navy); transform:translateY(-1px); }
  .filter-chip.active { background:var(--navy); border-color:var(--navy); color:var(--white); }
  .filter-chip.active .chip-count { background:var(--gold); color:var(--navy); }
  .chip-count { background:var(--gray-light); color:var(--navy); font-size:.72rem; font-weight:800;
    padding:.1rem .48rem; border-radius:20px; line-height:1.5; }

  /* ── Series heading ─────────────────────────────────────── */
  .series-head { display:flex; align-items:flex-start; gap:1rem; margin:3.5rem 0 1.5rem;
    padding-top:2rem; border-top:1px solid var(--gray-light); }
  /* first group only — :first-of-type would match inside every .series-group */
  .series-group:first-child .series-head { margin-top:0; padding-top:0; border-top:none; }
  .series-mark { width:48px; height:48px; flex:0 0 48px; border-radius:14px; display:flex; align-items:center;
    justify-content:center; color:#fff; font-size:1.15rem; box-shadow:0 6px 18px rgba(10,22,40,.18); }
  .series-name { font-weight:800; color:var(--navy); font-size:1.3rem; margin:0 0 .2rem; line-height:1.25; }
  .series-blurb { color:var(--text-muted); font-size:.92rem; margin:0; max-width:62ch; line-height:1.6; }

  /* ── Book card ──────────────────────────────────────────── */
  .book-card { position:relative; background:var(--white); border-radius:16px; overflow:hidden; height:100%;
    border:1px solid rgba(10,22,40,.08); box-shadow:0 2px 10px rgba(10,22,40,.05);
    display:flex; flex-direction:column;
    transition:transform .28s cubic-bezier(.4,0,.2,1), box-shadow .28s, border-color .28s; }
  .book-card:hover { transform:translateY(-6px); box-shadow:0 18px 44px rgba(10,22,40,.16); border-color:rgba(245,166,35,.4); }
  .book-cover { position:relative; aspect-ratio:3/4; background:var(--off-white); overflow:hidden;
    border-bottom:1px solid rgba(10,22,40,.06); }
  .book-cover img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .5s cubic-bezier(.4,0,.2,1); }
  .book-card:hover .book-cover img { transform:scale(1.04); }
  .book-num { position:absolute; top:.6rem; right:.6rem; background:rgba(255,255,255,.94); color:var(--navy);
    font-size:.7rem; font-weight:800; letter-spacing:.04em; padding:.28rem .62rem; border-radius:20px;
    box-shadow:0 2px 8px rgba(10,22,40,.14); }
  .book-body { padding:.9rem .95rem 1rem; display:flex; flex-direction:column; flex:1; }
  .book-series-tag { font-size:.68rem; font-weight:800; letter-spacing:.07em; text-transform:uppercase; margin-bottom:.3rem; }
  .book-title { font-weight:700; color:var(--navy); font-size:.98rem; line-height:1.35; margin-bottom:.45rem; }
  .book-meta { color:var(--text-muted); font-size:.78rem; margin-bottom:.85rem; display:flex; align-items:center; gap:.4rem; }
  .btn-enquire { margin-top:auto; width:100%; border:none; border-radius:10px; background:var(--navy); color:var(--white);
    font-size:.82rem; font-weight:700; padding:.6rem .75rem; cursor:pointer;
    transition:background .25s, color .25s, transform .15s; }
  .btn-enquire:hover { background:var(--gold); color:var(--navy); }
  .btn-enquire:active { transform:scale(.98); }

  /* ── Look inside ────────────────────────────────────────── */
  .cover-peek { position:absolute; left:50%; bottom:.6rem; transform:translate(-50%, calc(100% + .8rem));
    border:none; border-radius:30px; background:rgba(10,22,40,.92); color:#fff; font-size:.72rem;
    font-weight:700; padding:.4rem .85rem; cursor:pointer; white-space:nowrap;
    opacity:0; transition:transform .28s cubic-bezier(.4,0,.2,1), opacity .22s; }
  .book-card:hover .cover-peek, .cover-peek:focus-visible { opacity:1; transform:translate(-50%,0); }
  .cover-peek:hover { background:var(--gold); color:var(--navy); }
  .book-actions { margin-top:auto; display:flex; gap:.4rem; }
  .book-actions .btn-enquire { margin-top:0; flex:1 1 auto; }
  .btn-sample { flex:0 0 auto; border:1.5px solid var(--gray-light); border-radius:10px; background:var(--white);
    color:var(--navy); font-size:.78rem; font-weight:700; padding:.6rem .7rem; cursor:pointer;
    transition:border-color .2s, background .2s; }
  .btn-sample:hover { border-color:var(--gold); background:var(--gold-pale); }

  /* ── Sample viewer ──────────────────────────────────────── */
  /* Height-budgeted so the footer CTA is never pushed below the fold:
     the stage flexes and the page image scales to whatever is left over. */
  .sample-modal .modal-content { border:none; border-radius:18px; overflow:hidden; background:var(--navy);
    /* Definite height, so the flexed stage has something to fill and the
       absolutely positioned page image has a box to scale into. */
    height:min(88vh, 960px); display:flex; flex-direction:column; }
  .sample-modal .sample-bar, .sample-modal .sample-thumbs, .sample-modal .sample-foot { flex:0 0 auto; }
  .sample-bar { display:flex; align-items:center; gap:1rem; padding:.9rem 1.2rem; color:#fff;
    border-bottom:1px solid rgba(255,255,255,.12); }
  .sample-bar h5 { margin:0; font-size:.98rem; font-weight:700; }
  .sample-bar .page-count { font-size:.78rem; color:rgba(255,255,255,.65); }
  .sample-stage { position:relative; background:#0b1220; display:flex; align-items:center;
    justify-content:center; flex:1 1 auto; min-height:0; padding:1rem; }
  /* Absolutely filled rather than max-height:100% — a percentage height does
     not resolve against a flex-sized parent, so the page used to overflow. */
  .sample-stage img { position:absolute; inset:1rem; width:calc(100% - 2rem); height:calc(100% - 2rem);
    object-fit:contain; filter:drop-shadow(0 18px 40px rgba(0,0,0,.55)); }
  .sample-nav { position:absolute; top:50%; transform:translateY(-50%); width:44px; height:44px; border-radius:50%;
    border:none; background:rgba(255,255,255,.9); color:var(--navy); font-size:1rem; cursor:pointer;
    display:flex; align-items:center; justify-content:center; transition:background .2s, opacity .2s; }
  .sample-nav:hover { background:var(--gold); }
  .sample-nav:disabled { opacity:.25; cursor:default; }
  .sample-nav.prev { left:.8rem; } .sample-nav.next { right:.8rem; }
  .sample-thumbs { display:flex; gap:.45rem; overflow-x:auto; padding:.75rem 1.2rem; scrollbar-width:thin; }
  .sample-thumbs img { height:58px; width:auto; border-radius:4px; cursor:pointer; opacity:.5;
    border:2px solid transparent; transition:opacity .2s, border-color .2s; }
  .sample-thumbs img.active, .sample-thumbs img:hover { opacity:1; border-color:var(--gold); }
  .sample-foot { display:flex; flex-wrap:wrap; gap:.6rem; align-items:center; justify-content:space-between;
    padding:.85rem 1.2rem; background:var(--white); }
  .sample-foot p { margin:0; font-size:.8rem; color:var(--text-muted); }

  /* ── Empty state ────────────────────────────────────────── */
  .books-empty { display:none; text-align:center; padding:4rem 1rem; color:var(--text-muted); }

  /* ── Modal ──────────────────────────────────────────────── */
  .modal-content.book-modal { border:none; border-radius:20px; overflow:hidden; }
  .book-modal-head { background:var(--navy); color:var(--white); padding:1.35rem 1.5rem; display:flex; gap:1rem; align-items:center; }
  .book-modal-head img { width:58px; border-radius:6px; box-shadow:0 6px 18px rgba(0,0,0,.4); flex:0 0 58px; }
  .book-modal-head h5 { margin:0 0 .15rem; font-size:1.05rem; font-weight:700; }
  .book-modal-head p { margin:0; font-size:.82rem; color:rgba(255,255,255,.72); }
  .book-modal .btn-close { filter:invert(1) grayscale(100%) brightness(200%); opacity:.7; }
  .form-label-tpa { font-weight:600; font-size:.83rem; color:var(--navy); margin-bottom:.3rem; display:block; }
  .form-control-tpa, .form-select-tpa { width:100%; border:1.5px solid var(--gray-light); border-radius:10px;
    padding:.6rem .85rem; font-size:.9rem; color:var(--navy); background:var(--white); transition:border-color .2s, box-shadow .2s; }
  .form-control-tpa:focus, .form-select-tpa:focus { outline:none; border-color:var(--gold); box-shadow:0 0 0 3px rgba(245,166,35,.15); }
  .form-control-tpa.is-invalid, .form-select-tpa.is-invalid { border-color:#dc3545; }
  .field-err { display:none; color:#dc3545; font-size:.76rem; margin-top:.25rem; }
  .form-control-tpa.is-invalid ~ .field-err, .form-select-tpa.is-invalid ~ .field-err { display:block; }
  .modal-ok { display:none; text-align:center; padding:2.5rem 1.5rem; }
  .modal-ok i { font-size:3rem; color:#28a745; margin-bottom:1rem; }

  @media (max-width:575.98px) {
    .cover-peek { display:none; }
    .book-actions { flex-direction:column; }
    .btn-sample { width:100%; }
    .sample-modal .modal-content { height:92vh; }
    .sample-stage { padding:.5rem; }
    .sample-stage img { inset:.5rem; width:calc(100% - 1rem); height:calc(100% - 1rem); }
    .sample-thumbs { padding:.5rem .8rem; }
    .sample-thumbs img { height:44px; }
    .sample-nav { width:38px; height:38px; }
    .series-head { gap:.75rem; margin:2.5rem 0 1.15rem; }
    .series-mark { width:40px; height:40px; flex:0 0 40px; font-size:1rem; }
    .series-name { font-size:1.1rem; }
    .series-blurb { font-size:.85rem; }
    .book-body { padding:.75rem .8rem .85rem; }
    .book-title { font-size:.9rem; }
    .book-modal-head { padding:1.1rem 1.15rem; }
  }
</style>';

$schema_extra = '<script type="application/ld+json">' . json_encode([
  '@context' => 'https://schema.org', '@type' => 'CollectionPage',
  'name'        => 'Books & Workbooks — Talent Pool Academy',
  'url'         => 'https://www.talentpoolacademy.com/books.php',
  'description' => $meta_description,
  'provider'    => ['@type' => 'EducationalOrganization', 'name' => 'Talent Pool Academy', 'url' => 'https://www.talentpoolacademy.com'],
  'mainEntity'  => [
    '@type'           => 'ItemList',
    'numberOfItems'   => $totalBooks,
    'itemListElement' => (function () use ($series) {
      $out = []; $i = 1;
      foreach ($series as $slug => $s) {
        foreach ($s['books'] as $b) {
          $bk = tpa_book($slug, $s, $b);
          $out[] = ['@type' => 'ListItem', 'position' => $i++, 'name' => $bk['title']];
        }
      }
      return $out;
    })(),
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';

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
        <h1>Our <span style="color:var(--gold);">Workbooks</span></h1>
        <p><?= $totalBooks ?> workbooks across <?= count($series) ?> subjects, written by our own teaching team and used in every Talent Pool Academy session. Aligned to the National Curriculum and graded book by book.</p>
      </div>
    </div>
  </section>

  <!-- FILTER BAR -->
  <div class="book-filter-wrap">
    <div class="container">
      <div class="book-filter" role="tablist" aria-label="Filter books by subject">
        <button class="filter-chip active" data-filter="all" role="tab" aria-selected="true">
          All Books <span class="chip-count"><?= $totalBooks ?></span>
        </button>
        <?php foreach ($series as $slug => $s): ?>
        <button class="filter-chip" data-filter="<?= $slug ?>" role="tab" aria-selected="false">
          <?= htmlspecialchars($s['name'], ENT_QUOTES) ?> <span class="chip-count"><?= count($s['books']) ?></span>
        </button>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- BOOKS -->
  <section class="section-pad">
    <div class="container">

      <?php foreach ($series as $slug => $s): ?>
      <div class="series-group" data-series="<?= $slug ?>">
        <div class="series-head" id="<?= $slug ?>">
          <div class="series-mark" style="background:<?= $s['accent'] ?>;">
            <i class="fa-solid <?= $s['icon'] ?>"></i>
          </div>
          <div>
            <h2 class="series-name"><?= htmlspecialchars($s['name'], ENT_QUOTES) ?>
              <span style="font-weight:600;color:var(--text-muted);font-size:.85rem;">· <?= count($s['books']) ?> books</span>
            </h2>
            <p class="series-blurb"><?= htmlspecialchars($s['blurb'], ENT_QUOTES) ?></p>
          </div>
        </div>

        <div class="row g-3 g-md-4">
          <?php foreach ($s['books'] as $b): $bk = tpa_book($slug, $s, $b); ?>
          <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <article class="book-card">
              <div class="book-cover">
                <span class="book-num"><?= htmlspecialchars($bk['label'], ENT_QUOTES) ?></span>
                <img src="<?= $bk['img'] ?>" alt="<?= htmlspecialchars($bk['title'], ENT_QUOTES) ?> — Talent Pool Academy workbook cover" loading="lazy" width="640" height="853">
                <?php if ($bk['samples']): ?>
                <button type="button" class="cover-peek js-sample"
                        data-book="<?= htmlspecialchars($bk['title'], ENT_QUOTES) ?>"
                        data-stem="<?= $bk['stem'] ?>"
                        data-pages="<?= $bk['samples'] ?>"
                        aria-label="Look inside <?= htmlspecialchars($bk['title'], ENT_QUOTES) ?>">
                  <i class="fas fa-magnifying-glass-plus"></i> Look inside
                </button>
                <?php endif; ?>
              </div>
              <div class="book-body">
                <div class="book-series-tag" style="color:<?= $s['accent'] ?>;"><?= htmlspecialchars($s['name'], ENT_QUOTES) ?></div>
                <div class="book-title"><?= htmlspecialchars($bk['label'], ENT_QUOTES) ?></div>
                <?php if ($bk['meta']): ?><div class="book-meta"><i class="fas fa-child"></i><?= $bk['meta'] ?></div><?php endif; ?>
                <div class="book-actions">
                  <?php if ($bk['samples']): ?>
                  <button type="button" class="btn-sample js-sample"
                          data-book="<?= htmlspecialchars($bk['title'], ENT_QUOTES) ?>"
                          data-stem="<?= $bk['stem'] ?>"
                          data-pages="<?= $bk['samples'] ?>">
                    <i class="fas fa-book-open me-1"></i> Sample <span class="d-none d-sm-inline">pages</span>
                  </button>
                  <?php endif; ?>
                  <button type="button" class="btn-enquire js-enquire"
                          data-book="<?= htmlspecialchars($bk['title'], ENT_QUOTES) ?>"
                          data-img="<?= $bk['img'] ?>"
                          data-meta="<?= htmlspecialchars($bk['meta'], ENT_QUOTES) ?>">
                    <i class="fas fa-envelope me-1"></i> Enquire to Buy
                  </button>
                </div>
              </div>
            </article>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>

      <div class="books-empty" id="booksEmpty">
        <i class="fas fa-book-open fa-2x mb-3 d-block" style="color:var(--gold);"></i>
        No books in this subject yet.
      </div>
    </div>
  </section>

  <!-- HOW TO ORDER -->
  <section class="section-pad-sm section-bg">
    <div class="container">
      <div class="text-center mb-4" data-aos="fade-up">
        <div class="section-tag"><i class="fas fa-truck"></i> Ordering</div>
        <h2 class="section-title">How to <span>Get Your Books</span></h2>
        <div class="divider-gold"></div>
      </div>
      <div class="row g-4 justify-content-center">
        <?php foreach ([
          ['fa-hand-pointer', 'Send an enquiry', 'Pick a book above and tell us which stage your child is at. It takes under a minute.'],
          ['fa-comments',     'We confirm price &amp; stock', 'Our team replies within one working day with pricing and availability.'],
          ['fa-box-open',     'Collect or have it posted', 'Collect in-centre at Chadwell Heath or Chelmsford, or we post it anywhere in the UK.'],
        ] as $i => [$icon, $head, $copy]): ?>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
          <div class="text-center" style="background:var(--white);border-radius:var(--radius-lg);padding:2rem 1.5rem;box-shadow:var(--shadow-sm);height:100%;">
            <div style="width:64px;height:64px;margin:0 auto 1.15rem;border-radius:50%;background:linear-gradient(135deg,#F5A623,#FFD700);display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:var(--navy);box-shadow:0 8px 24px rgba(245,166,35,.35);">
              <i class="fas <?= $icon ?>"></i>
            </div>
            <h5 style="font-weight:700;color:var(--navy);font-size:1rem;margin-bottom:.5rem;"><?= $head ?></h5>
            <p style="color:var(--text-muted);font-size:.9rem;margin:0;line-height:1.6;"><?= $copy ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta-section">
    <div class="container text-center position-relative">
      <div data-aos="fade-up">
        <h2>Not sure which book <span style="color:var(--gold);">fits your child?</span></h2>
        <p>Tell us their year group and we'll recommend the right stage — the books are graded, so starting in the right place matters.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
          <a href="<?= SITE_URL ?>/contact.php" class="btn-primary-tpa"><i class="fas fa-envelope"></i> Ask Our Team</a>
          <a href="tel:<?= PHONE ?>" class="btn-secondary-tpa"><i class="fas fa-phone-alt"></i> Call <?= PHONE ?></a>
        </div>
      </div>
    </div>
  </section>

  <!-- SAMPLE PAGES VIEWER -->
  <div class="modal fade sample-modal" id="sampleModal" tabindex="-1" aria-labelledby="sampleLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">

        <div class="sample-bar">
          <div class="flex-grow-1">
            <h5 id="sampleLabel">Sample pages</h5>
            <span class="page-count" id="smCount"></span>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="sample-stage">
          <button type="button" class="sample-nav prev" id="smPrev" aria-label="Previous page"><i class="fas fa-chevron-left"></i></button>
          <img id="smImg" src="" alt="">
          <button type="button" class="sample-nav next" id="smNext" aria-label="Next page"><i class="fas fa-chevron-right"></i></button>
        </div>

        <div class="sample-thumbs" id="smThumbs"></div>

        <div class="sample-foot">
          <p><i class="fas fa-circle-info me-1" style="color:var(--gold);"></i>A short extract — the full book has considerably more.</p>
          <button type="button" class="btn-primary-tpa js-sample-enquire" style="font-size:.82rem;padding:.5rem 1.1rem;">
            <i class="fas fa-envelope"></i> Enquire to Buy
          </button>
        </div>

      </div>
    </div>
  </div>

  <!-- ENQUIRY MODAL -->
  <div class="modal fade" id="bookEnquiryModal" tabindex="-1" aria-labelledby="bookEnquiryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
      <div class="modal-content book-modal">

        <div class="book-modal-head">
          <img id="bkImg" src="" alt="">
          <div class="flex-grow-1">
            <h5 id="bookEnquiryLabel">Book enquiry</h5>
            <p id="bkMeta">Tell us where to send it and we'll confirm price and stock.</p>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-4">
          <form id="bookEnquiryForm" novalidate>
            <input type="hidden" name="book_title" id="bkTitle">
            <div class="row g-3">
              <div class="col-sm-6">
                <label class="form-label-tpa" for="bk-name">Your Name *</label>
                <input type="text" id="bk-name" name="name" class="form-control-tpa" placeholder="Parent or guardian name" required>
                <span class="field-err">Please enter your name</span>
              </div>
              <div class="col-sm-6">
                <label class="form-label-tpa" for="bk-phone">Phone Number *</label>
                <input type="tel" id="bk-phone" name="phone" class="form-control-tpa" placeholder="07xxx xxxxxx" required autocomplete="tel">
                <span class="field-err">Please enter a contact number</span>
              </div>
              <div class="col-sm-6">
                <label class="form-label-tpa" for="bk-email">Email Address *</label>
                <input type="email" id="bk-email" name="email" class="form-control-tpa" placeholder="you@example.com" required>
                <span class="field-err">Please enter a valid email address</span>
              </div>
              <div class="col-sm-3">
                <label class="form-label-tpa" for="bk-qty">Quantity</label>
                <input type="number" id="bk-qty" name="quantity" class="form-control-tpa" value="1" min="1" max="99">
              </div>
              <div class="col-sm-3">
                <label class="form-label-tpa" for="bk-year">Child's Year</label>
                <select id="bk-year" name="year_group" class="form-select-tpa">
                  <option value="">—</option>
                  <option>Reception</option>
                  <?php for ($y = 1; $y <= 11; $y++): ?><option>Year <?= $y ?></option><?php endfor; ?>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label-tpa" for="bk-collect">Collection or Delivery</label>
                <select id="bk-collect" name="fulfilment" class="form-select-tpa">
                  <option>Collect — Chadwell Heath (RM6 6PP)</option>
                  <option>Collect — Chelmsford (CM1 2AR)</option>
                  <option>Post to me (UK delivery)</option>
                  <option>Not sure yet</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label-tpa" for="bk-message">Anything else?</label>
                <textarea id="bk-message" name="message" class="form-control-tpa" rows="3" placeholder="Other books you're interested in, or questions about the right stage…" style="resize:vertical;"></textarea>
              </div>
              <div class="col-12">
                <div style="display:flex;align-items:flex-start;gap:.65rem;">
                  <input type="checkbox" id="bk-consent" name="consent" checked style="flex-shrink:0;margin-top:.2rem;width:1rem;height:1rem;accent-color:var(--gold);cursor:pointer;">
                  <label for="bk-consent" style="font-size:.83rem;color:var(--text-muted);cursor:pointer;line-height:1.55;margin:0;">
                    I agree to Talent Pool Academy contacting me about this enquiry. See our
                    <a href="<?= SITE_URL ?>/privacy.php" style="color:var(--gold);">Privacy Policy</a>.
                  </label>
                </div>
              </div>
              <div class="col-12">
                <button type="submit" class="btn-primary-tpa w-100" style="justify-content:center;">
                  <i class="fas fa-paper-plane"></i> Send Enquiry
                </button>
                <div id="bkError" class="field-err mt-2" style="text-align:center;"></div>
              </div>
            </div>
          </form>

          <div class="modal-ok" id="bkSuccess">
            <i class="fas fa-circle-check d-block"></i>
            <h5 style="font-weight:700;color:var(--navy);">Enquiry sent</h5>
            <p style="color:var(--text-muted);margin-bottom:1.25rem;">Thanks — our team will be in touch within one working day to confirm price and availability.</p>
            <button type="button" class="btn-secondary-tpa" data-bs-dismiss="modal">Close</button>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script>
  // Runs on DOMContentLoaded so bootstrap.bundle.js (loaded in the footer,
  // after this block) is available when the modal is constructed.
  document.addEventListener('DOMContentLoaded', function () {
    // ── Subject filter ────────────────────────────────────────
    var chips  = document.querySelectorAll('.filter-chip');
    var groups = document.querySelectorAll('.series-group');
    var empty  = document.getElementById('booksEmpty');

    function applyFilter(key) {
      var shown = 0;
      groups.forEach(function (g) {
        var match = (key === 'all' || g.dataset.series === key);
        g.hidden = !match;
        if (match) shown++;
      });
      empty.style.display = shown ? 'none' : 'block';
      chips.forEach(function (c) {
        var on = c.dataset.filter === key;
        c.classList.toggle('active', on);
        c.setAttribute('aria-selected', on ? 'true' : 'false');
      });
    }

    chips.forEach(function (c) {
      c.addEventListener('click', function () {
        applyFilter(c.dataset.filter);
        window.scrollTo({ top: document.querySelector('.book-filter-wrap').offsetTop - 10, behavior: 'smooth' });
      });
    });

    // Deep link: books.php#grammar opens that subject
    var hash = (location.hash || '').replace('#', '');
    if (hash && document.querySelector('.filter-chip[data-filter="' + hash + '"]')) applyFilter(hash);

    // ── Sample page viewer ────────────────────────────────────
    var smEl = document.getElementById('sampleModal');
    var smModal = smEl ? new bootstrap.Modal(smEl) : null;
    var smImg = document.getElementById('smImg');
    var smThumbs = document.getElementById('smThumbs');
    var smCount = document.getElementById('smCount');
    var smPrev = document.getElementById('smPrev');
    var smNext = document.getElementById('smNext');
    var sm = { stem: '', pages: 0, i: 1, book: '' };

    function smSrc(n) { return '<?= SITE_URL ?>/images/books/samples/' + sm.stem + '/p' + n + '.jpg'; }

    function smShow(n) {
      sm.i = Math.min(Math.max(1, n), sm.pages);
      smImg.src = smSrc(sm.i);
      smImg.alt = sm.book + ' — sample page ' + sm.i;
      smCount.textContent = 'Page ' + sm.i + ' of ' + sm.pages;
      smPrev.disabled = sm.i === 1;
      smNext.disabled = sm.i === sm.pages;
      Array.prototype.forEach.call(smThumbs.children, function (t, idx) {
        t.classList.toggle('active', idx + 1 === sm.i);
      });
      var active = smThumbs.children[sm.i - 1];
      if (active) active.scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' });
    }

    document.querySelectorAll('.js-sample').forEach(function (btn) {
      btn.addEventListener('click', function () {
        sm.stem  = btn.dataset.stem;
        sm.pages = parseInt(btn.dataset.pages, 10) || 1;
        sm.book  = btn.dataset.book;
        document.getElementById('sampleLabel').textContent = sm.book;

        smThumbs.innerHTML = '';
        for (var n = 1; n <= sm.pages; n++) {
          var t = document.createElement('img');
          t.src = smSrc(n);
          t.alt = 'Page ' + n;
          t.loading = 'lazy';
          t.dataset.page = n;
          t.addEventListener('click', function () { smShow(parseInt(this.dataset.page, 10)); });
          smThumbs.appendChild(t);
        }
        smShow(1);
        smModal.show();
      });
    });

    if (smPrev) smPrev.addEventListener('click', function () { smShow(sm.i - 1); });
    if (smNext) smNext.addEventListener('click', function () { smShow(sm.i + 1); });
    document.addEventListener('keydown', function (e) {
      if (!smEl || !smEl.classList.contains('show')) return;
      if (e.key === 'ArrowLeft')  smShow(sm.i - 1);
      if (e.key === 'ArrowRight') smShow(sm.i + 1);
    });

    // ── Modal ─────────────────────────────────────────────────
    var modalEl = document.getElementById('bookEnquiryModal');
    if (!modalEl) return;
    var modal   = new bootstrap.Modal(modalEl);
    var form    = document.getElementById('bookEnquiryForm');
    var okPanel = document.getElementById('bkSuccess');
    var errBox  = document.getElementById('bkError');

    function openEnquiry(book, img, meta) {
      document.getElementById('bkTitle').value = book;
      document.getElementById('bkImg').src     = img;
      document.getElementById('bkImg').alt     = book;
      document.getElementById('bookEnquiryLabel').textContent = book;
      document.getElementById('bkMeta').textContent = meta || '';
      form.style.display    = '';
      okPanel.style.display = 'none';
      errBox.style.display  = 'none';
      modal.show();
    }

    document.querySelectorAll('.js-enquire').forEach(function (btn) {
      btn.addEventListener('click', function () {
        openEnquiry(btn.dataset.book, btn.dataset.img, btn.dataset.meta);
      });
    });

    // "Enquire to Buy" inside the sample viewer — swap one modal for the other
    document.querySelectorAll('.js-sample-enquire').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var book = sm.book, img = '<?= SITE_URL ?>/images/books/' + sm.stem + '.jpg';
        smEl.addEventListener('hidden.bs.modal', function once() {
          smEl.removeEventListener('hidden.bs.modal', once);
          openEnquiry(book, img, '');
        });
        smModal.hide();
      });
    });

    function invalid(el, bad) { el.classList.toggle('is-invalid', bad); return !bad; }

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var name  = document.getElementById('bk-name');
      var phone = document.getElementById('bk-phone');
      var email = document.getElementById('bk-email');
      var ok = true;
      ok = invalid(name,  !name.value.trim()) && ok;
      ok = invalid(phone, phone.value.replace(/\D/g, '').length < 10) && ok;
      ok = invalid(email, !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) && ok;
      if (!ok) return;

      var btn = form.querySelector('button[type="submit"]');
      var orig = btn.innerHTML;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending…';
      btn.disabled = true;
      errBox.style.display = 'none';

      var book = document.getElementById('bkTitle').value;
      var qty  = document.getElementById('bk-qty').value || '1';
      var ful  = document.getElementById('bk-collect').value;
      var msg  = document.getElementById('bk-message').value.trim();

      var centre = '';
      if (ful.indexOf('Chadwell') > -1)   centre = 'Chadwell Heath';
      if (ful.indexOf('Chelmsford') > -1) centre = 'Chelmsford';

      var notes = 'BOOK ENQUIRY\n'
                + 'Book: ' + book + '\n'
                + 'Quantity: ' + qty + '\n'
                + 'Fulfilment: ' + ful
                + (msg ? '\n\nMessage:\n' + msg : '');

      fetch(window.tpaApiUrl || '<?= SITE_URL ?>/api/contact-form.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          name:       name.value.trim(),
          email:      email.value.trim(),
          phone:      phone.value.trim(),
          year_group: document.getElementById('bk-year').value,
          subject:    book,
          centre:     centre,
          notes:      notes,
          source:     'Website - Book Enquiry'
        })
      })
      .then(function (r) { return r.json().catch(function () { return { success: r.ok }; }); })
      .then(function (d) {
        if (!d || d.success === false) throw new Error(d && d.error ? d.error : 'Please try again');
        form.style.display    = 'none';
        okPanel.style.display = 'block';
        form.reset();
      })
      .catch(function (err) {
        errBox.textContent   = err.message || 'Something went wrong — please call us instead.';
        errBox.style.display = 'block';
      })
      .finally(function () { btn.innerHTML = orig; btn.disabled = false; });
    });
  });
  </script>

<?php require_once 'includes/footer.php'; ?>
