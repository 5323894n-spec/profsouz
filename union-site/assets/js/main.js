// =============================================
// Профсоюз 69 — Main JS
// =============================================

document.addEventListener('DOMContentLoaded', function () {

  // === Шапка: тень при скролле ===
  const header = document.getElementById('site-header');
  if (header) {
    const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 20);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // === Мобильное меню (бургер) ===
  const burger     = document.getElementById('burger');
  const mobileMenu = document.getElementById('mobile-menu');
  if (burger && mobileMenu) {
    burger.addEventListener('click', function () {
      const isOpen = mobileMenu.classList.toggle('open');
      burger.setAttribute('aria-expanded', isOpen);
      // Анимация бургера → крестик
      const spans = burger.querySelectorAll('span');
      if (isOpen) {
        spans[0].style.transform = 'translateY(7px) rotate(45deg)';
        spans[1].style.opacity   = '0';
        spans[2].style.transform = 'translateY(-7px) rotate(-45deg)';
      } else {
        spans.forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
      }
    });

    // Закрыть при клике вне
    document.addEventListener('click', function (e) {
      if (!burger.contains(e.target) && !mobileMenu.contains(e.target)) {
        mobileMenu.classList.remove('open');
        const spans = burger.querySelectorAll('span');
        spans.forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
      }
    });
  }

  // === FAQ: аккордеон ===
  document.querySelectorAll('.faq-question').forEach(function (q) {
    q.addEventListener('click', function () {
      const item = this.closest('.faq-item');
      const isOpen = item.classList.contains('open');
      // Закрыть все
      document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
      // Открыть текущий (если был закрыт)
      if (!isOpen) item.classList.add('open');
    });
  });

  // === Лайтбокс для галереи ===
  const lightboxItems = document.querySelectorAll('[data-lightbox]');
  if (lightboxItems.length) {
    const lb       = document.getElementById('lightbox');
    const lbImg    = document.getElementById('lb-img');
    const lbCaption= document.getElementById('lb-caption');
    const lbClose  = document.getElementById('lb-close');
    const lbPrev   = document.getElementById('lb-prev');
    const lbNext   = document.getElementById('lb-next');

    if (!lb || !lbImg) return;

    let items = [];
    let current = 0;

    // Собираем все элементы с одинаковым data-lightbox
    lightboxItems.forEach(function (el) {
      el.addEventListener('click', function () {
        const group = this.getAttribute('data-lightbox');
        items = Array.from(document.querySelectorAll('[data-lightbox="' + group + '"]'));
        current = items.indexOf(this);
        openLb();
      });
    });

    function openLb() {
      const el = items[current];
      lbImg.src = el.getAttribute('data-src') || el.querySelector('img')?.src || '';
      if (lbCaption) lbCaption.textContent = el.getAttribute('data-title') || '';
      lb.classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    function closeLb() {
      lb.classList.remove('active');
      document.body.style.overflow = '';
      lbImg.src = '';
    }

    function prevItem() {
      current = (current - 1 + items.length) % items.length;
      openLb();
    }

    function nextItem() {
      current = (current + 1) % items.length;
      openLb();
    }

    if (lbClose) lbClose.addEventListener('click', closeLb);
    if (lbPrev)  lbPrev.addEventListener('click', prevItem);
    if (lbNext)  lbNext.addEventListener('click', nextItem);

    lb.addEventListener('click', function (e) {
      if (e.target === lb) closeLb();
    });

    document.addEventListener('keydown', function (e) {
      if (!lb.classList.contains('active')) return;
      if (e.key === 'Escape') closeLb();
      if (e.key === 'ArrowLeft') prevItem();
      if (e.key === 'ArrowRight') nextItem();
    });
  }

  // === Плавная прокрутка якорных ссылок ===
  document.querySelectorAll('a[href^="#"]').forEach(function (link) {
    link.addEventListener('click', function (e) {
      const id = this.getAttribute('href').slice(1);
      const el = document.getElementById(id);
      if (el) {
        e.preventDefault();
        const top = el.getBoundingClientRect().top + window.scrollY - 80;
        window.scrollTo({ top, behavior: 'smooth' });
      }
    });
  });

  // === Анимация появления элементов ===
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.news-card, .advantage-card, .leader-card, .gallery-item').forEach(function (el) {
      el.classList.add('fade-in');
      observer.observe(el);
    });
  }

});

// === CSS для анимаций ===
(function () {
  const style = document.createElement('style');
  style.textContent = `
    .fade-in { opacity: 0; transform: translateY(16px); transition: opacity .4s ease, transform .4s ease; }
    .fade-in.visible { opacity: 1; transform: none; }
  `;
  document.head.appendChild(style);
})();
