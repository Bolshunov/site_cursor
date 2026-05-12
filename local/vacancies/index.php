<?php
declare(strict_types=1);

define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('BX_NO_ACCELERATOR_RESET', true);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$vacanciesSettings = [
    'PAGE_TITLE' => 'Вакансии — BEREGA',
    'META_DESCRIPTION' => 'Тестовая страница вакансий BEREGA в тёмном MNTN UI: команды, роли и карьерный маршрут.',
    'HEADER_LOGO_TEXT' => 'BEREGA',
    'NAV_JOBS_TEXT' => 'Вакансии',
    'NAV_ABOUT_TEXT' => 'О компании',
    'NAV_CASES_TEXT' => 'Кейсы',
    'HEADER_HR_TEXT' => 'HR',
    'SOCIAL_LABEL' => 'Follow us',
    'SOCIAL_INSTAGRAM_TEXT' => '◎',
    'SOCIAL_TELEGRAM_TEXT' => '✦',
    'SLIDER_START_TEXT' => 'Start',
    'SLIDER_01_TEXT' => '01',
    'SLIDER_02_TEXT' => '02',
    'SLIDER_03_TEXT' => '03',
    'SLIDER_04_TEXT' => '04',
    'HERO_EYEBROW' => 'Careers at BEREGA',
    'HERO_TITLE' => 'Вакансии для тех, кто умеет вести клиента выше обычного сервиса',
    'HERO_SCROLL_TEXT' => 'Смотреть роли',
];

if (\Bitrix\Main\Loader::includeModule('iblock')) {
    $properties = CIBlockElement::GetProperty(5, 33, ['sort' => 'asc'], []);
    while ($property = $properties->Fetch()) {
        $code = (string)($property['CODE'] ?? '');
        $value = $property['VALUE'] ?? '';
        if (is_array($value) && isset($value['TEXT'])) {
            $value = $value['TEXT'];
        }
        if ($code !== '' && array_key_exists($code, $vacanciesSettings) && trim((string)$value) !== '') {
            $vacanciesSettings[$code] = (string)$value;
        }
    }
}

function vtxt(string $code): string
{
    global $vacanciesSettings;
    return htmlspecialchars((string)($vacanciesSettings[$code] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?><!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="format-detection" content="telephone=no">
  <title><?= vtxt('PAGE_TITLE') ?></title>
  <meta name="description" content="<?= vtxt('META_DESCRIPTION') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #0b1d26;
      --bg-2: #102b36;
      --text: #fff;
      --muted: rgba(255, 255, 255, .68);
      --accent: #fbd784;
      --line: rgba(251, 215, 132, .95);
      --container: 1462px;
    }

    * {
      box-sizing: border-box;
    }

    html {
      scroll-behavior: smooth;
      background: var(--bg);
    }

    body {
      margin: 0;
      min-height: 100vh;
      font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      color: var(--text);
      background: var(--bg);
    }

    a {
      color: inherit;
      text-decoration: none;
    }

    a,
    .story,
    .story-visual,
    .vacancy-card {
      transition: color .2s ease, transform .25s ease, opacity .2s ease, border-color .25s ease, background .25s ease, box-shadow .25s ease;
    }

    .mntn-page {
      position: relative;
      min-height: 100vh;
      overflow: hidden;
      background:
        linear-gradient(180deg, rgba(11, 29, 38, 0) 0%, var(--bg) 38%, var(--bg) 100%),
        var(--bg);
      box-shadow: 0 0 100px rgba(0, 0, 0, .2);
    }

    .mountain-bg {
      position: absolute;
      inset: 0;
      pointer-events: none;
      overflow: hidden;
      height: 1900px;
    }

    .mountain-bg::before {
      content: "";
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      height: 1200px;
      background:
        linear-gradient(330.24deg, rgba(11, 29, 38, 0) 31.06%, #0b1d26 108.93%),
        linear-gradient(180deg, rgba(11,29,38,0) 0%, rgba(11,29,38,.05) 54%, #0b1d26 100%);
      z-index: 4;
    }

    .mountain-layer {
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      width: 100vw;
      min-width: 1920px;
      background-repeat: no-repeat;
      background-position: center;
      background-size: cover;
    }

    .mountain-layer--high {
      top: -380px;
      height: 1513px;
      background:
        linear-gradient(180deg, rgba(11,29,38,.12) 0%, rgba(11,29,38,.45) 100%),
        url("https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=2400&q=85");
      background-position: center top;
      opacity: .62;
      z-index: 1;
    }

    .mountain-layer--mid {
      top: 460px;
      height: 1422px;
      background:
        linear-gradient(180deg, rgba(11,29,38,0) 0%, rgba(11,29,38,.22) 62%, rgba(11,29,38,.85) 100%),
        url("https://images.unsplash.com/photo-1483728642387-6c3bdd6c93e5?auto=format&fit=crop&w=2400&q=85");
      background-position: center top;
      opacity: .85;
      z-index: 2;
    }

    .mountain-layer--front {
      top: 760px;
      height: 940px;
      background:
        linear-gradient(180deg, rgba(11,29,38,0) 0%, var(--bg) 70%),
        url("https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=2400&q=85");
      background-position: center bottom;
      z-index: 3;
    }

    .header {
      position: relative;
      z-index: 10;
      width: min(1760px, calc(100% - 80px));
      margin: 0 auto;
      padding-top: 64px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 32px;
    }

    .logo {
      font-family: "Playfair Display", Georgia, serif;
      font-size: 32px;
      line-height: 1;
      font-weight: 700;
      letter-spacing: .01em;
    }

    .logo:hover,
    .nav a:hover,
    .account:hover,
    .social a:hover,
    .slider a:hover {
      color: var(--accent);
    }

    .nav {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 40px;
      font-size: 16px;
      font-weight: 700;
    }

    .account {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 16px;
      font-weight: 700;
    }

    .account::before {
      content: "";
      width: 24px;
      height: 24px;
      border-radius: 50%;
      border: 2px solid #fff;
      box-shadow: inset 0 -8px 0 rgba(255,255,255,.28);
    }

    .hero {
      position: relative;
      z-index: 5;
      min-height: 1210px;
      padding-top: 188px;
    }

    .hero-content {
      width: min(950px, calc(100% - 48px));
      margin: 0 auto;
    }

    .eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 32px;
      margin-bottom: 32px;
      color: var(--accent);
      font-size: 16px;
      font-weight: 800;
      line-height: 22px;
      letter-spacing: 6px;
      text-transform: uppercase;
    }

    .eyebrow::before {
      content: "";
      width: 72px;
      height: 2px;
      background: var(--line);
    }

    .hero-title {
      margin: 0;
      font-family: "Playfair Display", Georgia, serif;
      font-size: clamp(46px, 6vw, 72px);
      font-weight: 600;
      line-height: 1.14;
      text-transform: capitalize;
    }

    .scroll-link {
      display: inline-flex;
      align-items: center;
      gap: 14px;
      margin-top: 32px;
      font-size: 16px;
      font-weight: 700;
    }

    .scroll-link:hover,
    .more:hover {
      color: #fff;
      transform: translateX(6px);
    }

    .scroll-link::after {
      content: "↓";
      font-size: 24px;
      line-height: 1;
    }

    .social {
      position: fixed;
      z-index: 20;
      left: 80px;
      top: 360px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 24px;
    }

    .social span {
      writing-mode: vertical-rl;
      font-size: 18px;
      font-weight: 700;
    }

    .social a {
      width: 24px;
      height: 24px;
      display: grid;
      place-items: center;
      font-weight: 800;
    }

    .slider {
      position: fixed;
      z-index: 20;
      right: 80px;
      top: 326px;
      display: grid;
      grid-template-columns: auto 3px;
      gap: 32px;
      align-items: start;
      filter: drop-shadow(0 0 100px rgba(0,0,0,.2));
    }

    .slider__text {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 40px;
      font-size: 16px;
      line-height: 22px;
    }

    .slider__line {
      position: relative;
      width: 3px;
      height: 300px;
      background: rgba(255,255,255,.5);
    }

    .slider__line::before {
      content: "";
      position: absolute;
      left: 0;
      top: 0;
      width: 3px;
      height: 75px;
      background: #fff;
    }

    .content {
      position: relative;
      z-index: 6;
      width: min(var(--container), calc(100% - 80px));
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      gap: 170px;
      padding-bottom: 120px;
    }

    .story {
      position: relative;
      min-height: 720px;
      display: grid;
      grid-template-columns: minmax(0, 1fr) 566px;
      gap: 92px;
      align-items: center;
    }

    .story:hover .story-visual {
      transform: translateY(-8px);
      box-shadow: 0 44px 96px rgba(0,0,0,.38);
    }

    .story:hover .vacancy-card {
      border-color: rgba(251,215,132,.55);
      background: rgba(11,29,38,.88);
    }

    .story:nth-child(even) {
      grid-template-columns: 566px minmax(0, 1fr);
    }

    .story:nth-child(even) .story-copy {
      order: 2;
    }

    .story:nth-child(even) .story-visual {
      order: 1;
    }

    .story-number {
      position: absolute;
      left: 0;
      top: 38px;
      color: rgba(255,255,255,.1);
      font-size: clamp(140px, 12vw, 240px);
      line-height: 1;
      font-weight: 800;
      pointer-events: none;
    }

    .story:nth-child(even) .story-number {
      left: auto;
      right: 42%;
    }

    .story-copy {
      position: relative;
      z-index: 1;
      padding-left: 150px;
    }

    .story:nth-child(even) .story-copy {
      padding-left: 0;
    }

    .story h2 {
      max-width: 555px;
      margin: 0 0 27px;
      font-family: "Playfair Display", Georgia, serif;
      font-size: clamp(34px, 4vw, 52px);
      font-weight: 600;
      line-height: 1.2;
    }

    .story p {
      max-width: 632px;
      margin: 0 0 27px;
      color: #fff;
      font-size: 16px;
      font-weight: 600;
      line-height: 29px;
    }

    .more {
      display: inline-flex;
      align-items: center;
      gap: 16px;
      color: var(--accent);
      font-size: 16px;
      font-weight: 800;
    }

    .more::after {
      content: "→";
      font-size: 22px;
    }

    .story-visual {
      position: relative;
      height: 720px;
      border-radius: 0;
      overflow: hidden;
      background:
        linear-gradient(180deg, rgba(11,29,38,0) 0%, rgba(11,29,38,.7) 100%),
        var(--story-image, url("https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80"));
      background-size: cover;
      background-position: center;
      box-shadow: 0 35px 80px rgba(0,0,0,.3);
    }

    .story-visual::before,
    .story-visual::after {
      content: "";
      position: absolute;
      left: 0;
      right: 0;
      bottom: 0;
      height: 56%;
      background:
        linear-gradient(180deg, rgba(11,29,38,0) 0%, rgba(11,29,38,.62) 100%);
    }

    .story-visual::after {
      height: 100%;
      background:
        radial-gradient(circle at 30% 20%, rgba(251,215,132,.18), transparent 32%),
        linear-gradient(180deg, rgba(11,29,38,.12) 0%, rgba(11,29,38,.55) 100%);
      opacity: 1;
    }

    .story-visual--sales {
      --story-image: url("https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1200&q=80");
    }

    .story-visual--analytics {
      --story-image: url("https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=1200&q=80");
    }

    .story-visual--service {
      --story-image: url("https://images.unsplash.com/photo-1493246507139-91e8fad9978e?auto=format&fit=crop&w=1200&q=80");
    }

    .story-visual--broker {
      --story-image: url("https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=1200&q=80");
    }

    .vacancy-card {
      position: absolute;
      left: 34px;
      right: 34px;
      bottom: 34px;
      z-index: 2;
      padding: 24px;
      border: 1px solid rgba(255,255,255,.12);
      background: rgba(11,29,38,.78);
      backdrop-filter: blur(14px);
    }

    .vacancy-card strong {
      display: block;
      margin-bottom: 8px;
      color: var(--accent);
      font-size: 13px;
      letter-spacing: 4px;
      text-transform: uppercase;
    }

    .vacancy-card span {
      display: block;
      color: #fff;
      font-size: 21px;
      font-family: "Playfair Display", Georgia, serif;
      line-height: 1.2;
    }

    .footer {
      display: grid;
      grid-template-columns: 1fr auto auto;
      gap: 160px;
      padding-top: 80px;
      border-top: 1px solid rgba(255,255,255,.1);
    }

    .footer__logo {
      margin-bottom: 24px;
      font-family: "Playfair Display", Georgia, serif;
      font-size: 32px;
      font-weight: 700;
    }

    .footer p {
      max-width: 360px;
      margin: 0;
      color: #fff;
      font-size: 16px;
      font-weight: 600;
      line-height: 29px;
    }

    .footer small {
      display: block;
      margin-top: 92px;
      color: rgba(255,255,255,.5);
      font-size: 16px;
      line-height: 32px;
    }

    .footer h3 {
      margin: 0 0 24px;
      color: var(--accent);
      font-size: 22px;
      line-height: 32px;
    }

    .footer ul {
      display: grid;
      gap: 16px;
      margin: 0;
      padding: 0;
      list-style: none;
      font-size: 16px;
      line-height: 32px;
    }

    @media (max-width: 1399px) {
      .social,
      .slider {
        display: none;
      }

      .story,
      .story:nth-child(even) {
        grid-template-columns: 1fr;
        gap: 48px;
      }

      .story:nth-child(even) .story-copy,
      .story:nth-child(even) .story-visual {
        order: initial;
      }

      .story-copy,
      .story:nth-child(even) .story-copy {
        padding-left: 96px;
      }

      .story-visual {
        height: 520px;
      }
    }

    @media (max-width: 900px) {
      .header {
        width: min(100% - 32px, 680px);
        padding-top: 28px;
      }

      .nav,
      .account {
        display: none;
      }

      .hero {
        min-height: 760px;
        padding-top: 120px;
      }

      .content {
        width: min(100% - 32px, 680px);
        gap: 96px;
      }

      .eyebrow {
        gap: 18px;
        font-size: 13px;
        letter-spacing: 3px;
      }

      .eyebrow::before {
        width: 44px;
      }

      .story-copy,
      .story:nth-child(even) .story-copy {
        padding-left: 0;
      }

      .story-number {
        top: -18px;
        font-size: 120px;
      }

      .story p {
        font-size: 16px;
        line-height: 28px;
      }

      .story-visual {
        height: 420px;
      }

      .footer {
        grid-template-columns: 1fr;
        gap: 42px;
      }

      .footer small {
        margin-top: 32px;
      }
    }
  </style>
</head>
<body>
<div class="mntn-page">
  <div class="mountain-bg" aria-hidden="true">
    <div class="mountain-layer mountain-layer--high"></div>
    <div class="mountain-layer mountain-layer--mid"></div>
    <div class="mountain-layer mountain-layer--front"></div>
  </div>

  <header class="header">
    <a class="logo" href="/local/"><?= vtxt('HEADER_LOGO_TEXT') ?></a>
    <nav class="nav" aria-label="Главная навигация">
      <a href="#jobs"><?= vtxt('NAV_JOBS_TEXT') ?></a>
      <a href="/local/about/"><?= vtxt('NAV_ABOUT_TEXT') ?></a>
      <a href="/local/cases/"><?= vtxt('NAV_CASES_TEXT') ?></a>
    </nav>
    <a class="account" href="mailto:hr@berega.test"><?= vtxt('HEADER_HR_TEXT') ?></a>
  </header>

  <aside class="social" aria-label="Социальные сети">
    <span><?= vtxt('SOCIAL_LABEL') ?></span>
    <a href="#" aria-label="Instagram"><?= vtxt('SOCIAL_INSTAGRAM_TEXT') ?></a>
    <a href="#" aria-label="Telegram"><?= vtxt('SOCIAL_TELEGRAM_TEXT') ?></a>
  </aside>

  <aside class="slider" aria-label="Навигация по странице">
    <div class="slider__text">
      <a href="#"><?= vtxt('SLIDER_START_TEXT') ?></a>
      <a href="#jobs"><?= vtxt('SLIDER_01_TEXT') ?></a>
      <a href="#culture"><?= vtxt('SLIDER_02_TEXT') ?></a>
      <a href="#apply"><?= vtxt('SLIDER_03_TEXT') ?></a>
      <a href="#broker"><?= vtxt('SLIDER_04_TEXT') ?></a>
    </div>
    <div class="slider__line"></div>
  </aside>

  <main>
    <section class="hero">
      <div class="hero-content">
        <div class="eyebrow"><?= vtxt('HERO_EYEBROW') ?></div>
        <h1 class="hero-title"><?= vtxt('HERO_TITLE') ?></h1>
        <a class="scroll-link" href="#jobs"><?= vtxt('HERO_SCROLL_TEXT') ?></a>
      </div>
    </section>

    <section class="content" id="jobs">
      <article class="story">
        <div class="story-number">01</div>
        <div class="story-copy">
          <div class="eyebrow">Get Started</div>
          <h2>Эксперт по курортной недвижимости</h2>
          <p>Нужен человек, который умеет не просто показывать объекты, а объяснять клиенту стратегию: локацию, ликвидность, риски, доходность и следующий шаг сделки.</p>
          <a class="more" href="mailto:hr@berega.test?subject=Эксперт по курортной недвижимости">Откликнуться</a>
        </div>
        <div class="story-visual story-visual--sales">
          <div class="vacancy-card">
            <strong>Full-time · Симферополь / удалённо</strong>
            <span>Продажи, подбор объектов, сопровождение клиента</span>
          </div>
        </div>
      </article>

      <article class="story" id="culture">
        <div class="story-number">02</div>
        <div class="story-copy">
          <div class="eyebrow">Hiking Essentials</div>
          <h2>Аналитик инвестиционных сценариев</h2>
          <p>Считать вход, платежи, аренду, налоги и точку выхода. Мы ищем того, кто спокойно разбирает цифры и помогает команде не продавать красивые, но слабые объекты.</p>
          <a class="more" href="mailto:hr@berega.test?subject=Аналитик инвестиционных сценариев">Откликнуться</a>
        </div>
        <div class="story-visual story-visual--analytics">
          <div class="vacancy-card">
            <strong>Part-time / project · удалённо</strong>
            <span>Финмодели, сравнительные таблицы, проверка гипотез</span>
          </div>
        </div>
      </article>

      <article class="story" id="apply">
        <div class="story-number">03</div>
        <div class="story-copy">
          <div class="eyebrow">Where you go is the key</div>
          <h2>Координатор клиентского пути</h2>
          <p>Связующее звено между клиентом, экспертом, юристом и застройщиком. Важно держать сроки, документы и коммуникацию так, чтобы клиент понимал, что происходит на каждом этапе.</p>
          <a class="more" href="mailto:hr@berega.test?subject=Координатор клиентского пути">Откликнуться</a>
        </div>
        <div class="story-visual story-visual--service">
          <div class="vacancy-card">
            <strong>Full-time · офис / гибрид</strong>
            <span>Сервис, документы, коммуникация, контроль этапов</span>
          </div>
        </div>
      </article>

      <article class="story" id="broker">
        <div class="story-number">04</div>
        <div class="story-copy">
          <div class="eyebrow">Sales Route</div>
          <h2>Брокер по недвижимости</h2>
          <p>Ищем брокера, который умеет вести диалог с инвестором, быстро разбираться в запросе и доводить клиента до просмотра, выбора объекта и сделки вместе с экспертом команды.</p>
          <a class="more" href="mailto:hr@berega.test?subject=Брокер по недвижимости">Откликнуться</a>
        </div>
        <div class="story-visual story-visual--broker">
          <div class="vacancy-card">
            <strong>Full-time · 60 000 ₽ + процент от продаж</strong>
            <span>Первичный контакт, квалификация клиента, сопровождение сделки</span>
          </div>
        </div>
      </article>

      <footer class="footer">
        <div>
          <div class="footer__logo">BEREGA</div>
          <p>Тестовая страница вакансий в MNTN UI. После согласования заменим контакты, роли и форму отклика на боевые.</p>
          <small>© 2026 BEREGA. Test page.</small>
        </div>
        <div>
          <h3>Разделы</h3>
          <ul>
            <li><a href="/local/">Главная</a></li>
            <li><a href="/local/about/"><?= vtxt('NAV_ABOUT_TEXT') ?></a></li>
            <li><a href="/local/cases/"><?= vtxt('NAV_CASES_TEXT') ?></a></li>
          </ul>
        </div>
        <div>
          <h3>Карьера</h3>
          <ul>
            <li><a href="#jobs">Открытые роли</a></li>
            <li><a href="#culture">Как работаем</a></li>
            <li><a href="#broker">Брокер</a></li>
            <li><a href="mailto:hr@berega.test">Написать HR</a></li>
          </ul>
        </div>
      </footer>
    </section>
  </main>
</div>
</body>
</html>

