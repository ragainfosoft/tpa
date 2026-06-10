// =============================================
// TALENT POOL ACADEMY — Main JavaScript
// =============================================

document.addEventListener('DOMContentLoaded', function () {

  // ---- Scroll Progress Bar ----
  const scrollProgress = document.getElementById('scroll-progress');
  if (scrollProgress) {
    window.addEventListener('scroll', () => {
      const scrollTop = window.scrollY;
      const docHeight = document.documentElement.scrollHeight - window.innerHeight;
      const pct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
      scrollProgress.style.width = pct + '%';
    }, { passive: true });
  }

  // ---- Navbar Scroll ----
  const navbar = document.querySelector('.tpa-navbar');
  if (navbar) {
    window.addEventListener('scroll', () => navbar.classList.toggle('scrolled', window.scrollY > 60), { passive: true });
  }

  // ---- Announcement Bar Dismiss ----
  const announcementBar   = document.getElementById('announcementBar');
  const announcementClose = document.getElementById('announcementClose');
  if (announcementBar && announcementClose) {
    announcementClose.addEventListener('click', () => {
      announcementBar.style.transition = 'max-height 0.3s ease, padding 0.3s ease, opacity 0.3s ease';
      announcementBar.style.overflow   = 'hidden';
      announcementBar.style.opacity    = '0';
      announcementBar.style.maxHeight  = '0';
      announcementBar.style.padding    = '0';
      announcementBar.setAttribute('aria-hidden', 'true');
      setTimeout(() => announcementBar.remove(), 320);
    });
  }

  // ---- AOS ----
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: prefersReducedMotion ? 0 : 700,
      once: true,
      offset: 60,
      easing: 'ease-out-cubic',
      disable: prefersReducedMotion
    });
  }

  // ---- GSAP ----
  if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined' && !prefersReducedMotion) {
    gsap.registerPlugin(ScrollTrigger);

    const heroTitle    = document.querySelector('.hero-title');
    const heroBadge    = document.querySelector('.hero-badge');
    const heroSubtitle = document.querySelector('.hero-subtitle');
    const heroCtas     = document.querySelector('.hero-ctas');
    const heroTrust    = document.querySelector('.hero-trust');
    const heroImg      = document.querySelector('.hero-img-wrap');

    if (heroTitle) {
      const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
      tl.from(heroBadge,    { opacity: 0, y: 30, duration: 0.6 })
        .from(heroTitle,    { opacity: 0, y: 40, duration: 0.8 }, '-=0.3')
        .from(heroSubtitle, { opacity: 0, y: 30, duration: 0.7 }, '-=0.5')
        .from(heroCtas,     { opacity: 0, y: 25, duration: 0.6 }, '-=0.4')
        .from(heroTrust,    { opacity: 0, y: 20, duration: 0.5 }, '-=0.3');
      if (heroImg) tl.from(heroImg, { opacity: 0, x: 60, scale: 0.95, duration: 1 }, 0.3);
      gsap.from('.hero-float-card', { opacity: 0, y: 20, duration: 0.6, stagger: 0.2, delay: 1.2, ease: 'back.out(1.7)' });
    }

    document.querySelectorAll('.counter').forEach(el => {
      const target = parseInt(el.dataset.target, 10);
      ScrollTrigger.create({
        trigger: el, start: 'top 85%', once: true,
        onEnter: () => {
          gsap.fromTo(el, { innerText: 0 }, {
            innerText: target, duration: 2, ease: 'power2.out',
            snap: { innerText: 1 },
            onUpdate: function () { el.innerText = Math.round(this.targets()[0].innerText).toLocaleString(); }
          });
        }
      });
    });

    gsap.utils.toArray('.gsap-stagger').forEach(c => {
      gsap.from(c.children, {
        scrollTrigger: { trigger: c, start: 'top 85%', once: true },
        opacity: 0, y: 30, duration: 0.6, stagger: 0.12, ease: 'power2.out'
      });
    });
  }

  // Show counter final values immediately when animations are disabled
  if (prefersReducedMotion) {
    document.querySelectorAll('.counter').forEach(el => {
      el.innerText = parseInt(el.dataset.target, 10).toLocaleString();
    });
  }

  // ---- Course Filter Tabs ----
  const filterTabs  = document.querySelectorAll('.filter-tab');
  const courseItems = document.querySelectorAll('.course-item');
  if (filterTabs.length && courseItems.length) {
    filterTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        filterTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const filter = tab.dataset.filter;
        courseItems.forEach(item => {
          const show = filter === 'all' || item.dataset.category === filter;
          item.style.display = show ? '' : 'none';
          if (show && typeof gsap !== 'undefined') gsap.fromTo(item, { opacity: 0, y: 15 }, { opacity: 1, y: 0, duration: 0.4 });
        });
      });
    });
  }

  // =============================================
  // TPA FORM ENGINE — shared validation + submit
  // =============================================
  const TpaForm = {
    EMAIL_RE: /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/,
    PHONE_RE: /^[\+\d][\d\s\-\(\)\.]{8,19}$/,  // lenient: 9-20 chars of digits/spaces/dashes

    // Ensure a .field-error div exists immediately after `field` within its wrapper
    _errEl(field) {
      const wrap = field.closest('.mb-3, .col-12, .col-sm-6, .col-sm-12') || field.parentNode;
      let el = wrap.querySelector('.field-error');
      if (!el) {
        el = document.createElement('div');
        el.className = 'field-error';
        el.setAttribute('role', 'alert');
        field.insertAdjacentElement('afterend', el);
      }
      return el;
    },

    setErr(field, msg) {
      field.classList.add('is-invalid');
      field.classList.remove('is-valid');
      const el = this._errEl(field);
      el.textContent = msg;
      el.classList.add('visible');
    },

    clearErr(field) {
      field.classList.remove('is-invalid');
      const wrap = field.closest('.mb-3, .col-12, .col-sm-6, .col-sm-12') || field.parentNode;
      const el = wrap.querySelector('.field-error');
      if (el) { el.classList.remove('visible'); el.textContent = ''; }
    },

    setValid(field) {
      field.classList.remove('is-invalid');
      field.classList.add('is-valid');
      this.clearErr(field);
    },

    labelText(form, field) {
      const lbl = form.querySelector('label[for="' + field.id + '"]') ||
                  field.closest('.mb-3, .col-12, .col-sm-6')?.querySelector('label');
      return lbl ? lbl.textContent.replace(/[*✱]/g, '').trim() : 'This field';
    },

    validateField(form, field) {
      const val = field.value.trim();
      if (field.hasAttribute('required') && !val) {
        this.setErr(field, this.labelText(form, field) + ' is required');
        return false;
      }
      if (field.type === 'email' && val && !this.EMAIL_RE.test(val)) {
        this.setErr(field, 'Please enter a valid email address');
        return false;
      }
      if (field.type === 'tel' && val && !this.PHONE_RE.test(val.replace(/\s/g, ''))) {
        this.setErr(field, 'Please enter a valid UK phone number (e.g. 07772 922943)');
        return false;
      }
      if (val) this.setValid(field);
      else this.clearErr(field);
      return true;
    },

    validate(form) {
      let ok = true;
      let firstErr = null;
      form.querySelectorAll('input, select, textarea').forEach(f => {
        if (!this.validateField(form, f)) {
          ok = false;
          if (!firstErr) firstErr = f;
        }
      });
      if (firstErr) firstErr.focus();
      return ok;
    },

    bindBlur(form) {
      form.querySelectorAll('input, select, textarea').forEach(f => {
        f.addEventListener('input',  () => this.clearErr(f));
        f.addEventListener('change', () => this.validateField(form, f));
        f.addEventListener('blur',   () => {
          if (f.value.trim() || f.hasAttribute('required')) this.validateField(form, f);
        });
      });
    },

    btnLoading(btn) {
      btn._orig = btn.innerHTML;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending…';
      btn.disabled = true;
    },

    btnReset(btn) {
      btn.innerHTML = btn._orig || btn.innerHTML;
      btn.style.background = '';
      btn.disabled = false;
    },

    postLead(payload) {
      return fetch(window.tpaApiUrl || '/tpaAG/api/contact-form.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify(payload)
      }).then(r => { if (!r.ok && r.status !== 200) throw new Error(r.status); return r.json(); });
    },

    // Map "How did you hear" dropdown values → DB enum
    mapSource(rawHear, fallback) {
      const h = (rawHear || '').toLowerCase();
      if (h.includes('google'))   return 'Google Search';
      if (h.includes('word') || h.includes('referral') || h.includes('mouth')) return 'Word of Mouth';
      if (h.includes('social'))   return 'Social Media';
      if (h.includes('flyer') || h.includes('leaflet')) return 'Flyer';
      if (h.includes('other'))    return 'Other';
      return fallback || 'Website';
    }
  };

  // ---- Contact Form (#contactForm) ----
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    TpaForm.bindBlur(contactForm);
    contactForm.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!TpaForm.validate(contactForm)) return;
      const btn  = contactForm.querySelector('button[type="submit"]');
      TpaForm.btnLoading(btn);
      const fd = new FormData(contactForm);
      TpaForm.postLead({
        name:       fd.get('child_name')      || '',
        email:      fd.get('email')           || '',
        phone:      fd.get('phone')           || '',
        child_name: fd.get('child_name')      || '',
        year_group: fd.get('child-year')      || '',
        subject:    fd.get('course-interest') || '',
        centre:     fd.get('centre')          || '',
        notes:      fd.get('message')         || '',
        source:     TpaForm.mapSource(fd.get('hear'), 'Website - Contact Form')
      })
      .then(() => {
        btn.innerHTML = '<i class="fas fa-check me-2"></i>Message Sent!';
        btn.style.background = '#28a745';
        setTimeout(() => { TpaForm.btnReset(btn); contactForm.reset(); contactForm.querySelectorAll('.is-valid').forEach(f => f.classList.remove('is-valid')); }, 4000);
      })
      .catch(() => {
        btn.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Try Again';
        btn.style.background = '#dc3545';
        setTimeout(() => TpaForm.btnReset(btn), 3500);
      });
    });
  }

  // ---- Generic Enquiry Forms (.tpa-enquiry-form on course pages) ----
  document.querySelectorAll('.tpa-enquiry-form').forEach(function (form) {
    TpaForm.bindBlur(form);
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!TpaForm.validate(form)) return;
      const btn  = form.querySelector('button[type="submit"]');
      TpaForm.btnLoading(btn);
      const fd = new FormData(form);
      TpaForm.postLead({
        name:       fd.get('child_name') || fd.get('name') || '',
        email:      fd.get('email')      || '',
        phone:      fd.get('phone')      || '',
        child_name: fd.get('child_name') || '',
        year_group: fd.get('year_group') || '',
        subject:    fd.get('subject')    || '',
        centre:     fd.get('centre')     || '',
        source:     fd.get('source')     || 'Website'
      })
      .then(() => {
        form.innerHTML = '<div class="tpa-form-success"><i class="fas fa-check-circle"></i><div><strong>Thank you!</strong> We\'ll be in touch within one working day to confirm your place.</div></div>';
      })
      .catch(() => {
        btn.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Try Again';
        btn.style.background = '#dc3545';
        setTimeout(() => TpaForm.btnReset(btn), 3500);
      });
    });
  });

  // ---- Smooth Scroll ----
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const id = this.getAttribute('href');
      if (id === '#') return;
      const target = document.querySelector(id);
      if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
  });

  // =============================================
  //  CAROUSEL FACTORY
  //  Uses actual measured card width — reliable
  // =============================================
  function makeCarousel({ trackId, prevId, nextId, dotsId, cardClass, dotClass, autoMs }) {
    const track  = document.getElementById(trackId);
    const prev   = document.getElementById(prevId);
    const next   = document.getElementById(nextId);
    const dotsEl = dotsId ? document.getElementById(dotsId) : null;
    if (!track || !prev || !next) return;

    const dc    = dotClass || 'star-carousel-dot';
    const cards = Array.from(track.querySelectorAll('.' + cardClass));
    if (!cards.length) return;

    let current     = 0;
    let autoTimer   = null;

    // How many cards fit in view based on viewport
    function cpv() {
      if (window.innerWidth < 600) return 1;
      if (window.innerWidth < 992) return 2;
      return 3;
    }

    function totalSlides() {
      return Math.max(1, Math.ceil(cards.length / cpv()));
    }

    // Measure the actual rendered width of the first card
    function cardWidth() {
      return cards[0].getBoundingClientRect().width;
    }

    // Measure the gap from the track's computed style
    function cardGap() {
      return parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap) || 24;
    }

    function buildDots() {
      if (!dotsEl) return;
      dotsEl.innerHTML = '';
      for (let i = 0; i < totalSlides(); i++) {
        const dot = document.createElement('button');
        dot.className = dc + (i === 0 ? ' active' : '');
        dot.setAttribute('aria-label', 'Slide ' + (i + 1));
        dot.addEventListener('click', () => { stopAuto(); goTo(i); });
        dotsEl.appendChild(dot);
      }
    }

    function updateDots() {
      if (!dotsEl) return;
      dotsEl.querySelectorAll('.' + dc).forEach((d, i) => d.classList.toggle('active', i === current));
    }

    function goTo(idx) {
      const total = totalSlides();
      current = ((idx % total) + total) % total;           // wrap around
      const cw  = cardWidth();
      const gap = cardGap();
      const offset = current * cpv() * (cw + gap);
      track.style.transform = 'translateX(-' + offset + 'px)';
      prev.disabled = false;                               // always enabled (wraps)
      next.disabled = false;
      updateDots();
    }

    function startAuto() {
      if (!autoMs) return;
      stopAuto();
      autoTimer = setInterval(() => goTo(current + 1), autoMs);
    }

    function stopAuto() {
      if (autoTimer) { clearInterval(autoTimer); autoTimer = null; }
    }

    prev.addEventListener('click', () => { stopAuto(); goTo(current - 1); startAuto(); });
    next.addEventListener('click', () => { stopAuto(); goTo(current + 1); startAuto(); });

    // Pause on hover
    track.parentElement.addEventListener('mouseenter', stopAuto);
    track.parentElement.addEventListener('mouseleave', startAuto);

    // Touch swipe
    let sx = 0;
    track.addEventListener('touchstart', e => { sx = e.touches[0].clientX; stopAuto(); }, { passive: true });
    track.addEventListener('touchend',   e => {
      const d = sx - e.changedTouches[0].clientX;
      if (Math.abs(d) > 50) goTo(current + (d > 0 ? 1 : -1));
      startAuto();
    });

    // Rebuild on resize
    window.addEventListener('resize', () => { buildDots(); goTo(0); });

    buildDots();
    goTo(0);
    startAuto();
  }

  // ---- Star Students Carousel (auto every 4s) ----
  makeCarousel({
    trackId:  'starTrack',
    prevId:   'starPrev',
    nextId:   'starNext',
    dotsId:   'starDots',
    cardClass: 'star-card',
    dotClass:  'star-carousel-dot',
    autoMs:    prefersReducedMotion ? 0 : 4000
  });

  // ---- Testimonials Carousel (auto every 5s) ----
  makeCarousel({
    trackId:  'testiTrack',
    prevId:   'testiPrev',
    nextId:   'testiNext',
    dotsId:   'testiDots',
    cardClass: 'testimonial-card',
    dotClass:  'testi-carousel-dot',
    autoMs:    prefersReducedMotion ? 0 : 5000
  });

});
