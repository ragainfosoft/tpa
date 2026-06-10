<?php
$page_title       = 'Blog & News | Talent Pool Academy';
$meta_description = 'The Talent Pool Academy Blog — expert advice for parents and students on 11 Plus, SATs, KS1, KS2 and KS3 revision. Tips, news and events.';
$extra_css = '
<style>
  .sidebar-widget { background:var(--white);border-radius:var(--radius-lg);padding:1.5rem;box-shadow:var(--shadow-sm);border:1px solid rgba(10,22,40,0.07);margin-bottom:1.5rem; }
  .sidebar-widget h5 { font-weight:700;color:var(--navy);margin-bottom:1rem;padding-bottom:.6rem;border-bottom:2px solid var(--gold); }
  .category-link { display:flex;justify-content:space-between;align-items:center;padding:.5rem 0;color:var(--text-muted);font-size:.9rem;transition:var(--transition);border-bottom:1px solid var(--gray-light); }
  .category-link:last-child { border-bottom:none; }
  .category-link:hover { color:var(--gold);padding-left:4px; }
  .category-link .count { background:var(--off-white);color:var(--gray);font-size:.75rem;font-weight:700;padding:.15rem .5rem;border-radius:20px; }
  .recent-post { display:flex;gap:.75rem;align-items:flex-start;margin-bottom:1rem;padding-bottom:1rem;border-bottom:1px solid var(--gray-light); }
  .recent-post:last-child { border-bottom:none;margin-bottom:0;padding-bottom:0; }
  .recent-post-icon { width:48px;height:48px;min-width:48px;border-radius:var(--radius-sm);background:var(--off-white);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--gold); }
  .recent-post-title { font-weight:600;color:var(--navy);font-size:.88rem;line-height:1.4;transition:var(--transition); }
  .recent-post-title:hover { color:var(--gold); }
  .recent-post-date { font-size:.78rem;color:var(--gray); }
  .newsletter-input { border:1.5px solid rgba(10,22,40,0.15);border-radius:var(--radius-sm);padding:.65rem 1rem;width:100%;font-family:var(--font-body);font-size:.9rem;margin-bottom:.75rem; }
  .newsletter-input:focus { outline:none;border-color:var(--gold); }
  .featured-post-wrap { background:linear-gradient(135deg,var(--navy) 0%,var(--navy-light) 100%);border-radius:var(--radius-lg);padding:3rem;color:white;margin-bottom:2.5rem;position:relative;overflow:hidden; }
  .featured-post-wrap::before { content:position:absolute;top:-60px;right:-60px;width:300px;height:300px;background:radial-gradient(circle,rgba(245,166,35,.15) 0%,transparent 70%);border-radius:50%; }
</style>';
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

  <!-- PAGE HERO -->
  <section class="page-hero">
    <div class="container">
      <div class="col-lg-8">
        <nav aria-label="breadcrumb" class="mb-3">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php">Home</a></li>
            <li class="breadcrumb-item active">Blog</li>
          </ol>
        </nav>
        <h1>News, Tips &amp; <span style="color:var(--gold);">Insights</span></h1>
        <p>Expert advice for parents and students on exams, revision strategies, wellbeing, and making the most of every learning opportunity.</p>
      </div>
    </div>
  </section>

  <!-- FEATURED POST -->
  <section class="section-pad-sm" style="background:var(--off-white);">
    <div class="container">
      <div class="featured-post-wrap" data-aos="fade-up">
        <div class="row align-items-center position-relative">
          <div class="col-lg-8">
            <span class="course-badge" style="background:rgba(245,166,35,.2);color:var(--gold);">📌 Featured Article</span>
            <h2 style="font-family:var(--font-heading);font-size:clamp(1.5rem,3vw,2rem);color:white;margin:1rem 0 .75rem;">The Ultimate Guide to 11 Plus Preparation: What Every Parent Needs to Know</h2>
            <p style="color:rgba(255,255,255,.75);margin-bottom:1.5rem;">Everything from when to start, which subjects to focus on, how many hours of practice are needed, and how to support your child emotionally through the process.</p>
            <div class="d-flex align-items-center gap-3 flex-wrap">
              <span style="color:rgba(255,255,255,.6);font-size:.85rem;"><i class="fas fa-user-circle me-1"></i>Mrs Meena Kumar</span>
              <span style="color:rgba(255,255,255,.6);font-size:.85rem;"><i class="fas fa-calendar me-1"></i>1 March 2025</span>
              <span style="color:rgba(255,255,255,.6);font-size:.85rem;"><i class="fas fa-clock me-1"></i>8 min read</span>
            </div>
          </div>
          <div class="col-lg-4 text-end d-none d-lg-block">
            <i class="fas fa-book-reader" style="font-size:6rem;color:rgba(245,166,35,.3);"></i>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- BLOG GRID + SIDEBAR -->
  <section class="section-pad">
    <div class="container">
      <div class="row g-5">

        <!-- Blog Posts -->
        <div class="col-lg-8">
          <div class="row g-4">

            <div class="col-md-6" data-aos="fade-up">
              <div class="blog-card">
                <div style="background:linear-gradient(135deg,var(--navy) 0%,var(--navy-light) 100%);height:220px;display:flex;align-items:center;justify-content:center;">
                  <i class="fas fa-pencil-ruler" style="font-size:3.5rem;color:var(--gold);opacity:.8;"></i>
                </div>
                <div class="blog-card-body">
                  <span class="blog-category">11 Plus</span>
                  <div class="blog-date"><i class="fas fa-calendar-alt me-1"></i>10 March 2025 · 6 min read</div>
                  <div class="blog-title">How to Start Preparing for the 11 Plus in Year 4</div>
                  <div class="blog-excerpt">Early preparation is the secret to 11+ success. We break down the optimal timetable, resources, and approach...</div>
                  <a class="blog-link" href="#">Read Article <i class="fas fa-long-arrow-alt-right"></i></a>
                </div>
              </div>
            </div>

            <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
              <div class="blog-card">
                <div style="background:linear-gradient(135deg,#1e3a5f 0%,#264d7d 100%);height:220px;display:flex;align-items:center;justify-content:center;">
                  <i class="fas fa-heart" style="font-size:3.5rem;color:var(--gold);opacity:.8;"></i>
                </div>
                <div class="blog-card-body">
                  <span class="blog-category">SATs</span>
                  <div class="blog-date"><i class="fas fa-calendar-alt me-1"></i>24 February 2025 · 5 min read</div>
                  <div class="blog-title">Top 5 Tips to Reduce SATs Anxiety in Your Child</div>
                  <div class="blog-excerpt">Exam pressure is real, but manageable. Here are five proven strategies to help your child stay calm...</div>
                  <a class="blog-link" href="#">Read Article <i class="fas fa-long-arrow-alt-right"></i></a>
                </div>
              </div>
            </div>

            <div class="col-md-6" data-aos="fade-up">
              <div class="blog-card">
                <div style="background:linear-gradient(135deg,#2d1b4e 0%,#4a2d7a 100%);height:220px;display:flex;align-items:center;justify-content:center;">
                  <i class="fas fa-house-user" style="font-size:3.5rem;color:var(--gold);opacity:.8;"></i>
                </div>
                <div class="blog-card-body">
                  <span class="blog-category">Parenting Tips</span>
                  <div class="blog-date"><i class="fas fa-calendar-alt me-1"></i>15 February 2025 · 4 min read</div>
                  <div class="blog-title">Creating the Perfect Home Study Environment</div>
                  <div class="blog-excerpt">The right environment makes all the difference. Here's how to design a productive study space at home...</div>
                  <a class="blog-link" href="#">Read Article <i class="fas fa-long-arrow-alt-right"></i></a>
                </div>
              </div>
            </div>

            <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
              <div class="blog-card">
                <div style="background:linear-gradient(135deg,#1b3d2d 0%,#2a6044 100%);height:220px;display:flex;align-items:center;justify-content:center;">
                  <i class="fas fa-chart-bar" style="font-size:3.5rem;color:var(--gold);opacity:.8;"></i>
                </div>
                <div class="blog-card-body">
                  <span class="blog-category">KS2</span>
                  <div class="blog-date"><i class="fas fa-calendar-alt me-1"></i>5 February 2025 · 7 min read</div>
                  <div class="blog-title">Understanding the New KS2 Maths Curriculum Changes</div>
                  <div class="blog-excerpt">The national curriculum continues to evolve. We explain what's changed for Year 3–6 maths and how to prepare...</div>
                  <a class="blog-link" href="#">Read Article <i class="fas fa-long-arrow-alt-right"></i></a>
                </div>
              </div>
            </div>

            <div class="col-md-6" data-aos="fade-up">
              <div class="blog-card">
                <div style="background:linear-gradient(135deg,#1a3d5c 0%,#2c5f8a 100%);height:220px;display:flex;align-items:center;justify-content:center;">
                  <i class="fas fa-brain" style="font-size:3.5rem;color:var(--gold);opacity:.8;"></i>
                </div>
                <div class="blog-card-body">
                  <span class="blog-category">Study Skills</span>
                  <div class="blog-date"><i class="fas fa-calendar-alt me-1"></i>28 January 2025 · 5 min read</div>
                  <div class="blog-title">The Science of Effective Revision: Techniques That Actually Work</div>
                  <div class="blog-excerpt">Not all revision is equal. We explore the research-backed methods that lead to genuine, long-lasting memory retention...</div>
                  <a class="blog-link" href="#">Read Article <i class="fas fa-long-arrow-alt-right"></i></a>
                </div>
              </div>
            </div>

            <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
              <div class="blog-card">
                <div style="background:linear-gradient(135deg,#3d1a1a 0%,#6b2d2d 100%);height:220px;display:flex;align-items:center;justify-content:center;">
                  <i class="fas fa-book" style="font-size:3.5rem;color:var(--gold);opacity:.8;"></i>
                </div>
                <div class="blog-card-body">
                  <span class="blog-category">Reading</span>
                  <div class="blog-date"><i class="fas fa-calendar-alt me-1"></i>20 January 2025 · 4 min read</div>
                  <div class="blog-title">Best Books to Read with Your KS1 Child This Year</div>
                  <div class="blog-excerpt">Reading together builds vocabulary, comprehension, and a lifelong love of learning. Our teachers' top picks for Year 1 &amp; 2...</div>
                  <a class="blog-link" href="#">Read Article <i class="fas fa-long-arrow-alt-right"></i></a>
                </div>
              </div>
            </div>

          </div>

          <!-- Pagination -->
          <div class="d-flex justify-content-center mt-5">
            <nav aria-label="Blog pagination">
              <ul class="pagination" style="gap:.3rem;">
                <li class="page-item active"><a class="page-link" href="#" style="background:var(--navy);border-color:var(--navy);">1</a></li>
                <li class="page-item"><a class="page-link" href="#" style="color:var(--navy);">2</a></li>
                <li class="page-item"><a class="page-link" href="#" style="color:var(--navy);">3</a></li>
                <li class="page-item"><a class="page-link" href="#" style="color:var(--navy);">Next <i class="fas fa-chevron-right ms-1"></i></a></li>
              </ul>
            </nav>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">

          <div class="sidebar-widget" data-aos="fade-left">
            <h5>Search</h5>
            <div style="position:relative;">
              <input type="text" class="newsletter-input" placeholder="Search articles...">
              <button style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--gold);cursor:pointer;"><i class="fas fa-search"></i></button>
            </div>
          </div>

          <div class="sidebar-widget" data-aos="fade-left" data-aos-delay="100">
            <h5>Categories</h5>
            <a href="#" class="category-link">11 Plus <span class="count">8</span></a>
            <a href="#" class="category-link">SATs <span class="count">6</span></a>
            <a href="#" class="category-link">KS1 &amp; KS2 <span class="count">10</span></a>
            <a href="#" class="category-link">KS3 &amp; GCSE <span class="count">4</span></a>
            <a href="#" class="category-link">Parenting Tips <span class="count">9</span></a>
            <a href="#" class="category-link">Study Skills <span class="count">7</span></a>
            <a href="#" class="category-link">News &amp; Events <span class="count">5</span></a>
          </div>

          <div class="sidebar-widget" data-aos="fade-left" data-aos-delay="200">
            <h5>Recent Posts</h5>
            <div class="recent-post">
              <div class="recent-post-icon"><i class="fas fa-star"></i></div>
              <div><a href="#" class="recent-post-title">Ultimate 11 Plus Prep Guide for Parents</a><div class="recent-post-date">1 March 2025</div></div>
            </div>
            <div class="recent-post">
              <div class="recent-post-icon"><i class="fas fa-pencil-ruler"></i></div>
              <div><a href="#" class="recent-post-title">How to Start Preparing for the 11 Plus in Year 4</a><div class="recent-post-date">10 March 2025</div></div>
            </div>
            <div class="recent-post">
              <div class="recent-post-icon"><i class="fas fa-heart"></i></div>
              <div><a href="#" class="recent-post-title">Top 5 Tips to Reduce SATs Anxiety</a><div class="recent-post-date">24 February 2025</div></div>
            </div>
          </div>

          <div class="sidebar-widget" style="background:linear-gradient(135deg,var(--navy),var(--navy-light));" data-aos="fade-left" data-aos-delay="300">
            <h5 style="color:white;">Newsletter</h5>
            <p style="color:rgba(255,255,255,.7);font-size:.88rem;margin-bottom:1rem;">Get our latest tips and exam updates delivered to your inbox.</p>
            <input type="email" class="newsletter-input" placeholder="Your email address" style="background:rgba(255,255,255,.1);border-color:rgba(255,255,255,.2);color:white;">
            <button class="btn-primary-tpa w-100" style="justify-content:center;"><i class="fas fa-paper-plane me-2"></i> Subscribe</button>
          </div>

          <div class="sidebar-widget" style="background:var(--gold-pale);border:1px solid rgba(245,166,35,.3);" data-aos="fade-left" data-aos-delay="400">
            <div style="font-size:2rem;margin-bottom:.75rem;color:var(--gold);"><i class="fas fa-graduation-cap" aria-hidden="true"></i></div>
            <h5 style="color:var(--navy);">Ready to Start?</h5>
            <p style="color:var(--text-muted);font-size:.88rem;">Book your child's free assessment and take the first step towards their success.</p>
            <a href="<?= SITE_URL ?>/contact.php#assessment" class="btn-primary-tpa w-100" style="justify-content:center;"><i class="fas fa-calendar-check me-2"></i>Free Assessment</a>
          </div>

        </div>
      </div>
    </div>
  </section>

<?php require_once 'includes/footer.php'; ?>
