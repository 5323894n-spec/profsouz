</div><!-- /.page-wrap -->

<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="<?= url() ?>" class="brand brand--light">
          <div class="brand-mark">П69</div>
          <div class="brand-text">
            <div class="brand-name"><?= h(setting('site_name')) ?></div>
            <div class="brand-sub"><?= h(setting('site_tagline')) ?></div>
          </div>
        </a>
        <p class="footer-since">С <?= h(setting('site_since', '1957')) ?> года на стороне работников</p>
        <div class="footer-social">
          <?php if (setting('vk_url')): ?>
          <a href="<?= h(setting('vk_url')) ?>" target="_blank" class="social-link" aria-label="ВКонтакте">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M15.684 0H8.316C1.592 0 0 1.592 0 8.316v7.368C0 22.408 1.592 24 8.316 24h7.368C22.408 24 24 22.408 24 15.684V8.316C24 1.592 22.408 0 15.684 0zm3.692 17.123h-1.744c-.66 0-.864-.525-2.05-1.727-1.033-1.01-1.49-.9-1.744-.9-.356 0-.457.101-.457.593v1.575c0 .424-.135.677-1.253.677-1.846 0-3.896-1.118-5.335-3.202C4.624 10.857 4.03 8.57 4.03 8.097c0-.254.101-.491.593-.491h1.744c.44 0 .61.203.78.677.863 2.49 2.303 4.675 2.896 4.675.22 0 .322-.101.322-.66V9.812c-.068-1.186-.695-1.287-.695-1.71 0-.204.17-.407.44-.407h2.744c.373 0 .508.203.508.643v3.473c0 .372.169.508.271.508.22 0 .407-.136.813-.542 1.253-1.406 2.151-3.574 2.151-3.574.118-.254.322-.491.762-.491h1.744c.525 0 .644.27.525.643-.22 1.017-2.354 4.031-2.354 4.031-.186.305-.254.44 0 .78.186.254.796.779 1.203 1.253.745.847 1.32 1.558 1.473 2.05.17.49-.085.745-.576.745z"/></svg>
          </a>
          <?php endif; ?>
          <?php if (setting('telegram_url')): ?>
          <a href="<?= h(setting('telegram_url')) ?>" target="_blank" class="social-link" aria-label="Telegram">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.013 9.487c-.148.666-.54.828-1.093.515l-3.025-2.229-1.46 1.405c-.162.162-.297.297-.608.297l.216-3.082 5.596-5.055c.243-.216-.054-.337-.377-.121L6.45 14.11 3.47 13.188c-.656-.205-.67-.656.138-.972l10.934-4.214c.545-.198 1.02.13.84.97l.18.276z"/></svg>
          </a>
          <?php endif; ?>
        </div>
      </div>

      <div class="footer-col">
        <h4>Навигация</h4>
        <ul>
          <li><a href="<?= url('news.php') ?>">Новости</a></li>
          <li><a href="<?= url('about.php') ?>">О профсоюзе</a></li>
          <li><a href="<?= url('docs.php') ?>">Документы</a></li>
          <li><a href="<?= url('help.php') ?>">Помощь членам</a></li>
          <li><a href="<?= url('gallery.php') ?>">Галерея</a></li>
          <li><a href="<?= url('contacts.php') ?>">Контакты</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>Помощь членам</h4>
        <ul>
          <li><a href="<?= url('help.php') ?>">Юридическая помощь</a></li>
          <li><a href="<?= url('help.php') ?>">Материальная помощь</a></li>
          <li><a href="<?= url('help.php') ?>">Санаторно-курортное лечение</a></li>
          <li><a href="<?= url('help.php#join') ?>">Вступить в профсоюз</a></li>
        </ul>
      </div>

      <div class="footer-col footer-contacts">
        <h4>Контакты</h4>
        <p>
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 1.5C5.51 1.5 3.5 3.51 3.5 6c0 4.5 4.5 8.5 4.5 8.5s4.5-4 4.5-8.5c0-2.49-2.01-4.5-4.5-4.5z"/><circle cx="8" cy="6" r="1.5"/></svg>
          <?= h(setting('site_address')) ?>
        </p>
        <p>
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2.5 3.5h11v9h-11z"/><path d="M2.5 3.5l5.5 5 5.5-5"/></svg>
          <a href="mailto:<?= h(setting('site_email')) ?>"><?= h(setting('site_email')) ?></a>
        </p>
        <p>
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5.5 3c-.5 0-1 .3-1.2.7l-.8 1.6C3 5.8 3 6.4 3.3 6.9c.7 1.1 1.8 2.7 3.2 3.9 1.4 1.2 3.1 2.1 4.3 2.6.6.3 1.2.2 1.6-.1l1.4-1c.4-.3.6-.8.5-1.3l-.4-1.5c-.1-.4-.5-.7-.9-.8L11.5 9c-.4-.1-.9.1-1.1.4l-.4.6c-.7-.4-1.5-1-2-1.5-.5-.5-1.1-1.3-1.5-2l.6-.4c.3-.2.5-.7.4-1.1L7 3.8c-.1-.4-.4-.7-.8-.8H5.5z"/></svg>
          <a href="tel:<?= h(str_replace([' ', '(', ')'], '', setting('site_phone'))) ?>"><?= h(setting('site_phone')) ?></a>
        </p>
        <p>
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="8" r="5.5"/><path d="M8 5v3.5l2 2"/></svg>
          <?= h(setting('site_schedule')) ?>
        </p>
      </div>
    </div>

    <div class="footer-bottom">
      <p>© <?= date('Y') ?> <?= h(setting('site_name')) ?>. Все права защищены.</p>
      <a href="<?= url('admin/') ?>" class="footer-admin-link">Администрирование</a>
    </div>
  </div>
</footer>

<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
