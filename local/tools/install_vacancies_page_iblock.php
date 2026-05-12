<?php
declare(strict_types=1);

use Bitrix\Main\Loader;

define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', false);
define('BX_NO_ACCELERATOR_RESET', true);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

global $USER, $APPLICATION;

header('Content-Type: text/html; charset=utf-8');

function hv(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function outMessage(string $title, string $message, string $type = 'info'): void
{
    $colors = [
        'info' => '#0b5cad',
        'ok' => '#16803c',
        'warn' => '#9a6700',
        'error' => '#b42318',
    ];
    $color = $colors[$type] ?? $colors['info'];
    echo '<div style="margin:14px 0;padding:14px 16px;border-left:4px solid ' . $color . ';background:#fff;border-radius:8px;box-shadow:0 8px 24px rgba(15,23,42,.08)">';
    echo '<h3 style="margin:0 0 6px;color:' . $color . ';font:600 18px Arial,sans-serif">' . hv($title) . '</h3>';
    echo '<div style="color:#334155;font:14px/1.55 Arial,sans-serif">' . $message . '</div>';
    echo '</div>';
}

function getDefaultSiteId(): string
{
    if (defined('SITE_ID') && SITE_ID !== '') {
        return (string)SITE_ID;
    }

    $siteId = 's1';
    $by = 'sort';
    $order = 'asc';
    $sites = CSite::GetList($by, $order, ['ACTIVE' => 'Y']);
    if ($site = $sites->Fetch()) {
        $siteId = (string)$site['LID'];
    }

    return $siteId;
}

function ensureIblockType(string $typeId): void
{
    $exists = CIBlockType::GetByID($typeId)->Fetch();
    if ($exists) {
        return;
    }

    $iblockType = new CIBlockType();
    $result = $iblockType->Add([
        'ID' => $typeId,
        'SECTIONS' => 'N',
        'IN_RSS' => 'N',
        'SORT' => 500,
        'LANG' => [
            'ru' => [
                'NAME' => 'BEREGA контент',
                'SECTION_NAME' => 'Разделы',
                'ELEMENT_NAME' => 'Элементы',
            ],
            'en' => [
                'NAME' => 'BEREGA content',
                'SECTION_NAME' => 'Sections',
                'ELEMENT_NAME' => 'Elements',
            ],
        ],
    ]);

    if (!$result) {
        throw new RuntimeException('Не удалось создать тип инфоблока: ' . $iblockType->LAST_ERROR);
    }
}

function ensureIblock(string $typeId, string $siteId): int
{
    $existing = CIBlock::GetList([], [
        'TYPE' => $typeId,
        'CODE' => 'vacancies_page_settings',
        'CHECK_PERMISSIONS' => 'N',
    ])->Fetch();

    if ($existing) {
        return (int)$existing['ID'];
    }

    $iblock = new CIBlock();
    $iblockId = (int)$iblock->Add([
        'ACTIVE' => 'Y',
        'NAME' => 'Вакансии: настройки первого экрана',
        'CODE' => 'vacancies_page_settings',
        'IBLOCK_TYPE_ID' => $typeId,
        'SITE_ID' => [$siteId],
        'SORT' => 500,
        'GROUP_ID' => ['2' => 'R'],
        'VERSION' => 2,
        'INDEX_ELEMENT' => 'N',
        'INDEX_SECTION' => 'N',
        'WORKFLOW' => 'N',
        'BIZPROC' => 'N',
        'LIST_PAGE_URL' => '#SITE_DIR#/local/vacancies/',
        'DETAIL_PAGE_URL' => '#SITE_DIR#/local/vacancies/',
        'SECTION_PAGE_URL' => '#SITE_DIR#/local/vacancies/',
        'ELEMENT_NAME' => 'Настройка',
        'ELEMENTS_NAME' => 'Настройки',
        'PROPERTY_INDEX' => 'N',
    ]);

    if ($iblockId <= 0) {
        throw new RuntimeException('Не удалось создать инфоблок: ' . $iblock->LAST_ERROR);
    }

    return $iblockId;
}

function ensureProperty(int $iblockId, array $property): void
{
    $existing = CIBlockProperty::GetList([], [
        'IBLOCK_ID' => $iblockId,
        'CODE' => $property['CODE'],
    ])->Fetch();

    $property += [
        'IBLOCK_ID' => $iblockId,
        'ACTIVE' => 'Y',
        'PROPERTY_TYPE' => 'S',
        'MULTIPLE' => 'N',
        'IS_REQUIRED' => 'N',
        'FILTRABLE' => 'N',
        'SEARCHABLE' => 'N',
        'COL_COUNT' => 60,
        'ROW_COUNT' => 1,
    ];

    $iblockProperty = new CIBlockProperty();
    if ($existing) {
        $result = $iblockProperty->Update((int)$existing['ID'], $property);
    } else {
        $result = $iblockProperty->Add($property);
    }

    if (!$result) {
        throw new RuntimeException('Не удалось сохранить свойство ' . $property['CODE'] . ': ' . $iblockProperty->LAST_ERROR);
    }
}

function ensureSettingsElement(int $iblockId, array $values): int
{
    $existing = CIBlockElement::GetList([], [
        'IBLOCK_ID' => $iblockId,
        'CODE' => 'main_screen',
        'CHECK_PERMISSIONS' => 'N',
    ], false, false, ['ID'])->Fetch();

    $element = new CIBlockElement();
    $fields = [
        'IBLOCK_ID' => $iblockId,
        'ACTIVE' => 'Y',
        'NAME' => 'Первый экран страницы вакансий',
        'CODE' => 'main_screen',
        'SORT' => 100,
        'PROPERTY_VALUES' => $values,
    ];

    if ($existing) {
        $id = (int)$existing['ID'];
        $result = $element->Update($id, $fields);
        if (!$result) {
            throw new RuntimeException('Не удалось обновить элемент настроек: ' . $element->LAST_ERROR);
        }
        return $id;
    }

    $id = (int)$element->Add($fields);
    if ($id <= 0) {
        throw new RuntimeException('Не удалось создать элемент настроек: ' . $element->LAST_ERROR);
    }

    return $id;
}

echo '<!doctype html><html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
echo '<title>Установка инфоблока вакансий</title></head><body style="margin:0;background:#f8fafc;color:#0f172a;font-family:Arial,sans-serif">';
echo '<main style="max-width:960px;margin:40px auto;padding:0 20px">';
echo '<h1 style="margin:0 0 10px;font-size:30px">Установка полей страницы вакансий</h1>';
echo '<p style="margin:0 0 22px;color:#475569;line-height:1.55">Скрипт создаёт инфоблок <b>“Вакансии: настройки первого экрана”</b> и один элемент <b>“Первый экран страницы вакансий”</b> с текстовыми полями hero-блока, меню, social и правого слайдера.</p>';

if (!$USER || !$USER->IsAuthorized()) {
    outMessage('Нужна авторизация', 'Сначала войдите в админку Bitrix, затем вернитесь на эту страницу.', 'warn');
    echo '<a href="/bitrix/admin/" style="display:inline-flex;padding:12px 18px;background:#0b5cad;color:white;border-radius:8px;text-decoration:none">Войти в админку</a>';
    echo '</main></body></html>';
    require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
    exit;
}

if (!$USER->IsAdmin()) {
    outMessage('Недостаточно прав', 'Запускать установку может только администратор Bitrix.', 'error');
    echo '</main></body></html>';
    require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
    exit;
}

if (($_GET['install'] ?? '') !== 'Y') {
    outMessage('Подтверждение', 'Нажмите кнопку ниже, чтобы создать/обновить инфоблок и поля. Повторный запуск безопасен: скрипт обновит существующие поля и демо-значения.', 'info');
    echo '<a href="?install=Y" style="display:inline-flex;padding:12px 18px;background:#16803c;color:white;border-radius:8px;text-decoration:none">Создать поля вакансий</a>';
    echo '</main></body></html>';
    require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
    exit;
}

try {
    if (!Loader::includeModule('iblock')) {
        throw new RuntimeException('Модуль iblock не подключен.');
    }

    $typeId = 'berega_content';
    $siteId = getDefaultSiteId();

    ensureIblockType($typeId);
    $iblockId = ensureIblock($typeId, $siteId);

    $properties = [
        ['CODE' => 'PAGE_TITLE', 'NAME' => 'SEO title страницы', 'SORT' => 100, 'DEFAULT_VALUE' => 'Вакансии — BEREGA'],
        ['CODE' => 'META_DESCRIPTION', 'NAME' => 'Meta description', 'SORT' => 110, 'ROW_COUNT' => 3, 'DEFAULT_VALUE' => 'Тестовая страница вакансий BEREGA в тёмном MNTN UI: команды, роли и карьерный маршрут.'],
        ['CODE' => 'HEADER_LOGO_TEXT', 'NAME' => 'Шапка: логотип / текст', 'SORT' => 200, 'DEFAULT_VALUE' => 'BEREGA'],
        ['CODE' => 'NAV_JOBS_TEXT', 'NAME' => 'Шапка: пункт “Вакансии”', 'SORT' => 210, 'DEFAULT_VALUE' => 'Вакансии'],
        ['CODE' => 'NAV_ABOUT_TEXT', 'NAME' => 'Шапка: пункт “О компании”', 'SORT' => 220, 'DEFAULT_VALUE' => 'О компании'],
        ['CODE' => 'NAV_CASES_TEXT', 'NAME' => 'Шапка: пункт “Кейсы”', 'SORT' => 230, 'DEFAULT_VALUE' => 'Кейсы'],
        ['CODE' => 'HEADER_HR_TEXT', 'NAME' => 'Шапка: ссылка HR', 'SORT' => 240, 'DEFAULT_VALUE' => 'HR'],
        ['CODE' => 'SOCIAL_LABEL', 'NAME' => 'Левый блок: Follow us', 'SORT' => 300, 'DEFAULT_VALUE' => 'Follow us'],
        ['CODE' => 'SOCIAL_INSTAGRAM_TEXT', 'NAME' => 'Левый блок: Instagram символ/текст', 'SORT' => 310, 'DEFAULT_VALUE' => '◎'],
        ['CODE' => 'SOCIAL_TELEGRAM_TEXT', 'NAME' => 'Левый блок: Telegram символ/текст', 'SORT' => 320, 'DEFAULT_VALUE' => '✦'],
        ['CODE' => 'SLIDER_START_TEXT', 'NAME' => 'Правый слайдер: Start', 'SORT' => 400, 'DEFAULT_VALUE' => 'Start'],
        ['CODE' => 'SLIDER_01_TEXT', 'NAME' => 'Правый слайдер: 01', 'SORT' => 410, 'DEFAULT_VALUE' => '01'],
        ['CODE' => 'SLIDER_02_TEXT', 'NAME' => 'Правый слайдер: 02', 'SORT' => 420, 'DEFAULT_VALUE' => '02'],
        ['CODE' => 'SLIDER_03_TEXT', 'NAME' => 'Правый слайдер: 03', 'SORT' => 430, 'DEFAULT_VALUE' => '03'],
        ['CODE' => 'SLIDER_04_TEXT', 'NAME' => 'Правый слайдер: 04', 'SORT' => 440, 'DEFAULT_VALUE' => '04'],
        ['CODE' => 'HERO_EYEBROW', 'NAME' => 'Hero: надзаголовок', 'SORT' => 500, 'DEFAULT_VALUE' => 'Careers at BEREGA'],
        ['CODE' => 'HERO_TITLE', 'NAME' => 'Hero: основной заголовок', 'SORT' => 510, 'ROW_COUNT' => 3, 'DEFAULT_VALUE' => 'Вакансии для тех, кто умеет вести клиента выше обычного сервиса'],
        ['CODE' => 'HERO_SCROLL_TEXT', 'NAME' => 'Hero: текст ссылки вниз', 'SORT' => 520, 'DEFAULT_VALUE' => 'Смотреть роли'],
    ];

    $propertyValues = [];
    foreach ($properties as $property) {
        ensureProperty($iblockId, $property);
        $propertyValues[$property['CODE']] = $property['DEFAULT_VALUE'] ?? '';
    }

    $elementId = ensureSettingsElement($iblockId, $propertyValues);

    outMessage('Готово', 'Инфоблок и поля созданы/обновлены. ID инфоблока: <b>' . $iblockId . '</b>, ID элемента настроек: <b>' . $elementId . '</b>.', 'ok');
    echo '<p style="line-height:1.65;color:#334155">Теперь откройте в админке: <b>Контент → BEREGA контент → Вакансии: настройки первого экрана</b> и отредактируйте элемент <b>“Первый экран страницы вакансий”</b>.</p>';
    echo '<p style="line-height:1.65;color:#b42318"><b>Важно:</b> после успешной установки удалите этот файл с хостинга или попросите меня убрать его из Git, чтобы установочный скрипт не лежал публично.</p>';
} catch (Throwable $exception) {
    outMessage('Ошибка установки', hv($exception->getMessage()), 'error');
}

echo '</main></body></html>';

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
