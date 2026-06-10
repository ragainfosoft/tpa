<?php
// Central reviews & testimonials data — edit here to update all pages.
// tags: array of course slugs this review is relevant to.
//   Use 'general' to show on all pages.
//   Valid slugs: general | 11plus | sats | ks1 | ks2 | ks3 | gcse | alevel | adult
$all_reviews = [

  // ── 11 Plus ────────────────────────────────────────────────────────────────
  [
    'name'     => 'Ananya M.',
    'text'     => '"My daughter got into Ilford County High after just 18 months with TPA. The teachers are incredible and the approach to VR and NVR practice is unlike anything else we tried. 100% recommend."',
    'meta'     => 'Parent · Ilford County High placement',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['11plus'],
  ],
  [
    'name'     => 'James O.',
    'text'     => '"Both my sons have been through TPA. The first got into Westcliff, and now my younger one\'s 11 Plus prep is going brilliantly. Mrs Kumar and her team genuinely care about every child."',
    'meta'     => 'Parent · Two TPA grammar placements',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['11plus'],
  ],
  [
    'name'     => 'Priya K.',
    'text'     => '"TPA helped my daughter improve her confidence massively. She passed her 11+ and is now thriving at grammar school. Couldn\'t recommend more highly!"',
    'meta'     => 'Parent · 11 Plus student',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['11plus'],
  ],
  [
    'name'     => 'TPA Parent',
    'text'     => '"Excellent result for CCHS — my daughter scored 115.56. I am a single mum and it was very difficult, but today I am so proud of her. Thank you Talent Pool Academy team."',
    'meta'     => 'Parent · Chelmsford County High placement',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['11plus'],
  ],
  [
    'name'     => 'TPA Parent',
    'text'     => '"Talent Pool Academy and the team did a fantastic role in my child\'s 11 Plus journey. Although it was just a year, their contribution helped him to achieve higher. He got into KEGS in Chelmsford. We are very grateful."',
    'meta'     => 'Parent · KEGS placement',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['11plus'],
  ],
  [
    'name'     => 'TPA Parent',
    'text'     => '"Despite joining the 11+ late, Meena and the team supported my son so much that he smashed his CSSE examination. Incredible result — highly recommend."',
    'meta'     => 'Parent · 11 Plus student',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['11plus'],
  ],
  [
    'name'     => 'TPA Parent',
    'text'     => '"I will recommend Talent Pool to every parent preparing their child for 11+ exams. Excellent tuition — my daughter loves going every week and the progress has been outstanding."',
    'meta'     => 'Parent · 11 Plus student',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['11plus'],
  ],

  // ── SATs ───────────────────────────────────────────────────────────────────
  [
    'name'     => 'Fatima A.',
    'text'     => '"The SATs results were outstanding — my son went from just below expected to well above expected in both Maths and Reading. The progress tracking kept us informed every step of the way."',
    'meta'     => 'Parent · Year 6 SATs student',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['sats', 'ks2'],
  ],
  [
    'name'     => 'David M.',
    'text'     => '"My daughter was really struggling with fractions and reading comprehension. After 6 months at TPA she was flying — the SATs results were incredible. Highly recommend!"',
    'meta'     => 'Parent · Year 6 student',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['sats'],
  ],
  [
    'name'     => 'Navya',
    'text'     => '"Year 6 was tough but TPA helped me stay calm and focused. I achieved Level 5+ in all my SATs and got into my first choice secondary. Brilliant teachers, brilliant results."',
    'meta'     => 'Student · Year 6 · SATs & 11 Plus',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['sats', '11plus'],
  ],

  // ── KS1 ────────────────────────────────────────────────────────────────────
  [
    'name'     => 'Aisha N.',
    'text'     => '"The teachers are so patient and warm. My daughter used to cry about maths — now she rushes to TPA every Saturday! The progress has been remarkable."',
    'meta'     => 'Parent · Year 1 student',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['ks1'],
  ],
  [
    'name'     => 'TPA Parent',
    'text'     => '"My son attended Talent Pool Academy at the beginning of Year 2. He has become very confident in both English and maths. Extremely pleased with the help and support received. Would highly recommend."',
    'meta'     => 'Parent · Year 2 student',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['ks1'],
  ],

  // ── KS2 ────────────────────────────────────────────────────────────────────
  [
    'name'     => 'Rachel T.',
    'text'     => '"My son was really behind in maths when he joined TPA. Within 6 months his school reports completely turned around. The small class sizes make all the difference."',
    'meta'     => 'Parent · Year 4 student',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['ks2'],
  ],
  [
    'name'     => 'Omar F.',
    'text'     => '"TPA identified exactly where my daughter was struggling and built a plan around it. Her confidence in English has soared — she now loves writing!"',
    'meta'     => 'Parent · Year 5 student',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['ks2'],
  ],
  [
    'name'     => 'TPA Parent',
    'text'     => '"Very high standard of teaching — I have seen my child grow and enhance his academic skills. Well-structured teaching materials tailored to every child\'s individual needs."',
    'meta'     => 'Parent · KS2 student',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['ks2', 'general'],
  ],

  // ── KS3 ────────────────────────────────────────────────────────────────────
  [
    'name'     => 'Sarah L.',
    'text'     => '"We tried other tutoring centres before TPA and there\'s simply no comparison. The quality of teaching and the structured programme is exceptional. My son\'s Year 7 confidence has transformed."',
    'meta'     => 'Parent · Year 7 student',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['ks3'],
  ],
  [
    'name'     => 'Raj P.',
    'text'     => '"Small class sizes make a huge difference. My son gets real attention from his teacher every lesson and his scores have improved by 30% in 6 months. Brilliant value."',
    'meta'     => 'Parent · Year 8 student',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['ks3', 'general'],
  ],

  // ── GCSE ───────────────────────────────────────────────────────────────────
  [
    'name'     => 'TPA Parent',
    'text'     => '"My eldest daughter attended Talent Pool for many years. Well-planned classes to a very high standard. She achieved A* and A in Maths and Further Maths at GCSE and A-Level — and is now one of the tutors at TPA!"',
    'meta'     => 'Parent · GCSE & A-Level student',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['gcse', 'alevel'],
  ],
  [
    'name'     => 'TPA Parent',
    'text'     => '"My son\'s GCSE Maths grade jumped two full grades after just one term at TPA. The exam technique coaching and past paper practice made a huge difference. Wish we\'d started sooner."',
    'meta'     => 'Parent · Year 11 student',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['gcse'],
  ],

  // ── A-Level ────────────────────────────────────────────────────────────────
  [
    'name'     => 'TPA Student',
    'text'     => '"TPA\'s A-Level Maths sessions were genuinely the reason I got into my university of choice. The subject specialist understood exactly what examiners look for and tailored every lesson to my weak points."',
    'meta'     => 'A-Level student · University placement',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['alevel'],
  ],

  // ── Adult Learning ─────────────────────────────────────────────────────────
  [
    'name'     => 'TPA Learner',
    'text'     => '"I joined TPA\'s adult Functional Skills programme after years away from education. The tutors were patient, encouraging and completely non-judgemental. I passed both my Maths and English qualifications first time."',
    'meta'     => 'Adult learner · Functional Skills',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['adult'],
  ],

  // ── General (shown on any page as fill-in) ─────────────────────────────────
  [
    'name'     => 'TPA Parent',
    'text'     => '"Talent Pool Academy has helped my kids with their academic excellence. Teachers are very helpful and supportive. My kids are doing exceptionally well at school."',
    'meta'     => 'Parent · Multiple TPA students',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['general'],
  ],
  [
    'name'     => 'TPA Parent',
    'text'     => '"I feel Talent Pool has been a brilliant tuition centre. My son\'s maths was quite bad, but within a few months at Talent Pool he has improved so much. The teachers are all absolutely brilliant."',
    'meta'     => 'Parent · TPA student',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['general'],
  ],
  [
    'name'     => 'TPA Parent',
    'text'     => '"I extend my best wishes to Talent Pool and recommend their services to all parents seeking quality educational support. Our experience has been nothing short of exceptional."',
    'meta'     => 'Parent · TPA student',
    'platform' => 'google',
    'rating'   => 5,
    'tags'     => ['general'],
  ],
];

/**
 * Returns reviews relevant to the given course tag(s).
 * Course-specific reviews are prioritised; general reviews fill remaining slots.
 *
 * @param string|array $tags  Course slug(s) e.g. '11plus' or ['11plus','sats']
 * @param int          $max   Maximum reviews to return (default 4)
 */
function get_reviews($tags, int $max = 4): array {
  global $all_reviews;
  if (is_string($tags)) $tags = [$tags];

  $matched = [];
  $general = [];
  foreach ($all_reviews as $r) {
    if (!empty(array_intersect($tags, $r['tags']))) {
      $matched[] = $r;
    } elseif (in_array('general', $r['tags'])) {
      $general[] = $r;
    }
  }
  $result = array_merge($matched, $general);
  return array_slice($result, 0, $max);
}

/**
 * Renders a 2-column grid of testimonial cards (legacy / static layout).
 */
function render_reviews(array $reviews): void {
  echo '<div class="row g-4">';
  foreach ($reviews as $i => $r) {
    $delay = ($i % 2 === 1) ? ' data-aos-delay="100"' : '';
    $stars = str_repeat('★', $r['rating']) . str_repeat('☆', 5 - $r['rating']);
    $badge = $r['platform'] === 'google'
      ? '<span class="google-badge ms-1"><i class="fab fa-google me-1"></i>Google</span>'
      : '<span class="google-badge ms-1" style="background:#00b67a;color:#fff;"><i class="fas fa-star me-1"></i>Trustpilot</span>';
    echo '<div class="col-md-6" data-aos="fade-up"' . $delay . '>';
    echo '<div class="testimonial-card">';
    echo '<div class="testimonial-stars">' . $stars . ' ' . $badge . '</div>';
    echo '<p class="testimonial-text">' . htmlspecialchars($r['text']) . '</p>';
    echo '<div class="testimonial-author">' . htmlspecialchars($r['name']) . '</div>';
    echo '<div class="testimonial-meta">' . htmlspecialchars($r['meta']) . '</div>';
    echo '</div></div>';
  }
  echo '</div>';
}

/**
 * Renders a full testimonials section with carousel slider (same layout as homepage).
 * Pass course $tag to get relevant reviews; pass 0 for $max to show all.
 *
 * @param string $tag          Course slug ('11plus', 'sats', 'gcse', etc.) or 'general'
 * @param string $heading_html HTML for the h2 heading (may contain <span>)
 * @param int    $max          Max reviews (0 = all matched + general fill)
 * @param bool   $dark_bg      If true wraps in section-bg class
 */
function render_testimonials_section(string $tag, string $heading_html = 'What <span>Parents Say</span>', int $max = 0, bool $dark_bg = true): void {
  $reviews = get_reviews($tag, $max > 0 ? $max : 99);
  if (empty($reviews)) return;
  $bg = $dark_bg ? 'section-pad section-bg' : 'section-pad';
  echo '<section class="' . $bg . '">';
  echo '<div class="container">';
  echo '<div class="text-center mb-5" data-aos="fade-up">';
  echo '<div class="section-tag"><i class="fas fa-quote-left"></i> Parent Reviews</div>';
  echo '<h2 class="section-title">' . $heading_html . '</h2>';
  echo '<div class="divider-gold"></div>';
  echo '</div>';
  echo '<div class="testi-slider-wrap" data-aos="fade-up">';
  echo '<button class="testi-carousel-btn" id="testiPrev" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>';
  echo '<div class="testi-slider-track-wrap"><div class="testi-slider-track" id="testiTrack">';
  foreach ($reviews as $r) {
    $stars = str_repeat('★', $r['rating']) . str_repeat('☆', 5 - $r['rating']);
    $badge = $r['platform'] === 'google'
      ? '<span class="google-badge ms-1"><i class="fab fa-google me-1"></i>Google</span>'
      : '<span class="google-badge ms-1" style="background:#00b67a;color:#fff;"><i class="fas fa-star me-1"></i>Trustpilot</span>';
    echo '<div class="testimonial-card">';
    echo '<div class="testimonial-stars">' . $stars . ' ' . $badge . '</div>';
    echo '<p class="testimonial-text">' . htmlspecialchars($r['text']) . '</p>';
    echo '<div class="testimonial-author">' . htmlspecialchars($r['name']) . '</div>';
    echo '<div class="testimonial-meta">' . htmlspecialchars($r['meta']) . '</div>';
    echo '</div>';
  }
  echo '</div></div>';
  echo '<button class="testi-carousel-btn" id="testiNext" aria-label="Next"><i class="fas fa-chevron-right"></i></button>';
  echo '</div>';
  echo '<div class="testi-carousel-dots" id="testiDots"></div>';
  echo '</div></section>';
}
