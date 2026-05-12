// =============================================
// Профсоюз 69 — Admin JS
// =============================================

document.addEventListener('DOMContentLoaded', function () {

  // === Мобильный сайдбар ===
  const toggle  = document.getElementById('sidebar-toggle');
  const sidebar = document.getElementById('admin-sidebar');

  if (toggle && sidebar) {
    toggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
    });
    document.addEventListener('click', function (e) {
      if (sidebar.classList.contains('open') &&
          !sidebar.contains(e.target) &&
          !toggle.contains(e.target)) {
        sidebar.classList.remove('open');
      }
    });
  }

  // === Авто-скрытие алертов ===
  document.querySelectorAll('.alert-success, .alert-info').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity .5s';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 500);
    }, 4000);
  });

  // === Предпросмотр загружаемого изображения ===
  document.querySelectorAll('input[type="file"][accept*="image"]').forEach(function (input) {
    input.addEventListener('change', function () {
      const preview = this.parentElement.querySelector('.img-preview img, #preview-img');
      if (preview && this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; };
        reader.readAsDataURL(this.files[0]);
      }
    });
  });

  // === Подтверждение удаления ===
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm(this.getAttribute('data-confirm') || 'Вы уверены?')) {
        e.preventDefault();
      }
    });
  });

  // === Актуальный раздел в навигации ===
  const currentPath = window.location.pathname;
  document.querySelectorAll('.nav-item').forEach(function (link) {
    const href = link.getAttribute('href') || '';
    if (href && currentPath.endsWith(href.split('/').pop())) {
      link.classList.add('active');
    }
  });

  // === Таблицы: выделение строки по клику ===
  document.querySelectorAll('.admin-table tbody tr').forEach(function (row) {
    const link = row.querySelector('a');
    if (link) {
      row.style.cursor = 'pointer';
      row.addEventListener('click', function (e) {
        if (e.target.closest('button, a, input, select')) return;
        link.click();
      });
    }
  });

  // === Editor: синхронизация contenteditable → hidden input ===
  const editor = document.getElementById('editor');
  const contentInput = document.getElementById('content-input');
  if (editor && contentInput) {
    document.querySelector('form')?.addEventListener('submit', function () {
      contentInput.value = editor.innerHTML;
    });
  }

});
