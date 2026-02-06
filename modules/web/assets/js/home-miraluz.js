/**
 * Ópticas Miraluz - Home JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {

  // ========================================
  // LOADER
  // ========================================
  window.addEventListener('load', function() {
    setTimeout(function() {
      const loader = document.getElementById('mlLoader');
      if (loader) {
        loader.classList.add('hidden');
      }
    }, 800);
  });

  // ========================================
  // CURSOR GLOW (Desktop only)
  // ========================================
  const cursorGlow = document.getElementById('mlCursorGlow');
  if (cursorGlow && window.innerWidth > 768) {
    document.addEventListener('mousemove', function(e) {
      cursorGlow.style.left = e.clientX + 'px';
      cursorGlow.style.top = e.clientY + 'px';
    });
  }

  // ========================================
  // NAV SCROLL EFFECT
  // ========================================
  const nav = document.getElementById('mlNavbar');
  if (nav) {
    window.addEventListener('scroll', function() {
      nav.classList.toggle('scrolled', window.scrollY > 50);
    });
  }

  // ========================================
  // HAMBURGER MENU (Mobile)
  // ========================================
  const hamburger = document.getElementById('mlHamburger');
  const navLinks = document.getElementById('mlNavLinks');
  if (hamburger && navLinks) {
    hamburger.addEventListener('click', function() {
      navLinks.classList.toggle('open');
    });
  }

  // ========================================
  // SMOOTH SCROLL FOR ANCHOR LINKS
  // ========================================
  document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
    anchor.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href.length > 1) {
        const target = document.querySelector(href);
        if (target) {
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          // Close mobile menu if open
          if (navLinks) {
            navLinks.classList.remove('open');
          }
        }
      }
    });
  });

  // ========================================
  // REVEAL ON SCROLL
  // ========================================
  const revealElements = document.querySelectorAll('.ml-reveal');
  if (revealElements.length > 0) {
    const revealObserver = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    revealElements.forEach(function(el) {
      revealObserver.observe(el);
    });
  }

  // ========================================
  // COUNTER ANIMATION
  // ========================================
  const counterElements = document.querySelectorAll('[data-count]');
  if (counterElements.length > 0) {
    const counterObserver = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          const el = entry.target;
          const target = parseInt(el.dataset.count);
          const duration = 2000;
          const start = performance.now();

          function tick(now) {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            if (target >= 1000) {
              el.textContent = Math.floor(eased * target).toLocaleString() + '+';
            } else {
              el.textContent = Math.floor(eased * target);
            }
            if (progress < 1) {
              requestAnimationFrame(tick);
            }
          }
          requestAnimationFrame(tick);
          counterObserver.unobserve(el);
        }
      });
    }, { threshold: 0.5 });

    counterElements.forEach(function(el) {
      counterObserver.observe(el);
    });
  }

  // ========================================
  // HERO SLIDER
  // ========================================
  const slides = document.querySelectorAll('.ml-slider-slide');
  const dotsContainer = document.getElementById('mlSliderDots');
  const progressBar = document.getElementById('mlSliderProgress');
  const counterCurrent = document.getElementById('mlSliderCurrent');
  const counterTotal = document.getElementById('mlSliderTotal');
  const prevBtn = document.getElementById('mlSliderPrev');
  const nextBtn = document.getElementById('mlSliderNext');

  if (slides.length > 0 && dotsContainer) {
    let currentSlide = 0;
    let progressInterval;
    let progressValue = 0;
    const SLIDE_DURATION = 6000;
    const PROGRESS_STEP = 30;

    // Create dots
    slides.forEach(function(_, i) {
      const dot = document.createElement('button');
      dot.className = 'ml-slider-dot' + (i === 0 ? ' active' : '');
      dot.addEventListener('click', function() { goToSlide(i); });
      dotsContainer.appendChild(dot);
    });

    if (counterTotal) {
      counterTotal.textContent = String(slides.length).padStart(2, '0');
    }

    function goToSlide(index) {
      // Deactivate current
      slides[currentSlide].classList.remove('active');
      dotsContainer.children[currentSlide].classList.remove('active');

      // Reset image transform
      const oldImg = slides[currentSlide].querySelector('.ml-slide-bg img');
      if (oldImg) oldImg.style.transform = 'scale(1)';

      currentSlide = index;

      // Activate new
      slides[currentSlide].classList.add('active');
      dotsContainer.children[currentSlide].classList.add('active');

      if (counterCurrent) {
        counterCurrent.textContent = String(currentSlide + 1).padStart(2, '0');
      }

      resetProgress();
    }

    function nextSlide() {
      goToSlide((currentSlide + 1) % slides.length);
    }

    function prevSlide() {
      goToSlide((currentSlide - 1 + slides.length) % slides.length);
    }

    function resetProgress() {
      progressValue = 0;
      if (progressBar) progressBar.style.width = '0%';
      clearInterval(progressInterval);

      progressInterval = setInterval(function() {
        progressValue += (PROGRESS_STEP / SLIDE_DURATION) * 100;
        if (progressBar) progressBar.style.width = progressValue + '%';
        if (progressValue >= 100) {
          clearInterval(progressInterval);
          nextSlide();
        }
      }, PROGRESS_STEP);
    }

    // Start slider
    resetProgress();

    // Arrow buttons
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
      if (e.key === 'ArrowLeft') prevSlide();
      if (e.key === 'ArrowRight') nextSlide();
    });

    // Pause on hover
    const sliderEl = document.querySelector('.ml-hero-slider');
    if (sliderEl) {
      sliderEl.addEventListener('mouseenter', function() {
        clearInterval(progressInterval);
      });
      sliderEl.addEventListener('mouseleave', function() {
        progressInterval = setInterval(function() {
          progressValue += (PROGRESS_STEP / SLIDE_DURATION) * 100;
          if (progressBar) progressBar.style.width = progressValue + '%';
          if (progressValue >= 100) {
            clearInterval(progressInterval);
            nextSlide();
          }
        }, PROGRESS_STEP);
      });

      // Touch/swipe support
      let touchStartX = 0;
      sliderEl.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
      }, { passive: true });
      sliderEl.addEventListener('touchend', function(e) {
        const touchEndX = e.changedTouches[0].screenX;
        const diff = touchStartX - touchEndX;
        if (Math.abs(diff) > 50) {
          if (diff > 0) nextSlide();
          else prevSlide();
        }
      });
    }
  }

  // ========================================
  // PARTICLES
  // ========================================
  const particlesContainer = document.getElementById('mlParticles');
  if (particlesContainer) {
    for (let i = 0; i < 15; i++) {
      const p = document.createElement('div');
      p.className = 'ml-particle';
      p.style.left = Math.random() * 100 + '%';
      p.style.top = Math.random() * 100 + '%';
      p.style.animationDelay = Math.random() * 6 + 's';
      p.style.animationDuration = (4 + Math.random() * 4) + 's';
      particlesContainer.appendChild(p);
    }
  }

  // ========================================
  // TAB BUTTONS
  // ========================================
  document.querySelectorAll('.ml-tab-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.ml-tab-btn').forEach(function(b) {
        b.classList.remove('active');
      });
      btn.classList.add('active');
    });
  });

  // ========================================
  // BRANCH CARDS
  // ========================================
  document.querySelectorAll('.ml-branch-card').forEach(function(card) {
    card.addEventListener('click', function() {
      document.querySelectorAll('.ml-branch-card').forEach(function(c) {
        c.classList.remove('active');
      });
      card.classList.add('active');
    });
  });

  // ========================================
  // PROMO SLIDER NAV
  // ========================================
  const promosTrack = document.getElementById('mlPromosTrack');
  if (promosTrack) {
    const promoNavBtns = document.querySelectorAll('.ml-promo-nav button');
    if (promoNavBtns.length >= 2) {
      promoNavBtns[0].addEventListener('click', function() {
        promosTrack.scrollBy({ left: -424, behavior: 'smooth' });
      });
      promoNavBtns[1].addEventListener('click', function() {
        promosTrack.scrollBy({ left: 424, behavior: 'smooth' });
      });
    }
  }

});
