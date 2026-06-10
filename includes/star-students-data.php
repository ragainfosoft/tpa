<?php
if (!defined('SITE_URL')) require_once __DIR__ . '/config.php';
// Central star students data — edit here to update all pages.
// Tags indicate which course pages this student is relevant to.
$star_students = [
  [
    'name'      => 'Ann',
    'school'    => 'Chelmsford County High School for Girls',
    'programme' => 'Year 4–6 · 11 Plus Programme',
    'quote'     => '"Mrs Kumar and her team believed in me from day one. The mock exams were exactly like the real thing!"',
    'img'       => SITE_URL . '/images/star1.jpeg',
    'year'      => '2024',
    'tags'      => ['11plus'],
  ],
  [
    'name'      => 'Miftaha',
    'school'    => 'Chelmsford County High School for Girls',
    'programme' => 'Year 5–6 · 11 Plus Programme',
    'quote'     => '"I went from hating VR questions to scoring in the top 5% — TPA made it enjoyable and achievable."',
    'img'       => SITE_URL . '/images/star2.jpeg',
    'year'      => '2024',
    'tags'      => ['11plus'],
  ],
  [
    'name'      => 'Faith',
    'school'    => 'Chelmsford County High School for Girls',
    'programme' => 'Year 4–6 · 11 Plus Programme',
    'quote'     => '"The small classes mean the teacher actually knows where you\'re struggling — that made all the difference."',
    'img'       => SITE_URL . '/images/star3.jpeg',
    'year'      => '2024',
    'tags'      => ['11plus'],
  ],
  [
    'name'      => 'Veer',
    'school'    => 'King Edward VI Grammar School, Chelmsford',
    'programme' => 'Year 5–6 · 11 Plus Programme',
    'quote'     => '"I loved every session at TPA. The practice papers helped me feel confident walking into my exam."',
    'img'       => SITE_URL . '/images/star4.jpeg',
    'year'      => '2024',
    'tags'      => ['11plus'],
  ],
  [
    'name'      => 'Joanna',
    'school'    => 'Westcliff High School for Girls',
    'programme' => 'Year 4–6 · 11 Plus Programme',
    'quote'     => '"TPA is the reason I got my first choice school. Brilliant teaching, brilliant results."',
    'img'       => SITE_URL . '/images/star5.jpeg',
    'year'      => '2024',
    'tags'      => ['11plus'],
  ],
  [
    'name'      => 'Navya',
    'school'    => 'Chelmsford County High School for Girls',
    'programme' => 'Year 6 · SATs & 11 Plus',
    'quote'     => '"Year 6 was tough but TPA helped me stay calm and focused. I achieved Level 5+ in all my SATs!"',
    'img'       => SITE_URL . '/images/star6.jpeg',
    'year'      => '2024',
    'tags'      => ['11plus', 'sats'],
  ],
  [
    'name'      => 'Abir',
    'school'    => 'Colchester Royal Grammar School',
    'programme' => 'Year 5–6 · 11 Plus Programme',
    'quote'     => '"My parents were so happy when I got in. TPA was worth every minute — I highly recommend it!"',
    'img'       => SITE_URL . '/images/star7.jpeg',
    'year'      => '2023',
    'tags'      => ['11plus'],
  ],
];

/**
 * Returns star students filtered by course tag.
 * Pass an empty array (or omit) to get all students.
 */
function get_star_students(array $tags = [], int $max = 0): array {
  global $star_students;
  if (empty($tags)) {
    $result = $star_students;
  } else {
    $result = array_filter($star_students, fn($s) => !empty(array_intersect($tags, $s['tags'])));
    $result = array_values($result);
  }
  return ($max > 0) ? array_slice($result, 0, $max) : $result;
}
