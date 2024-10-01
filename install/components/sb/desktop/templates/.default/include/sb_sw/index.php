<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Page\Asset;

$strDir = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
Asset::getInstance()->addCss($strDir . '/styles.css');

if (CModule::IncludeModule('firstbit.eo')) {
$iblockId = 0;

$iblockCode = 'sw';

$filter = array('CODE' => $iblockCode);
$res = CIBlock::GetList(array(), $filter);
if ($ar_res = $res->Fetch()) {
    $iblockId = $ar_res['ID'];
}

$db = CIBlockSection::GetList(
    [
        'SORT' => 'ASC'
    ],
    [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $iblockId,
        'SECTION_ID' => false,
        'CHECK_PERMISSIONS' => 'Y',
        'CNT_ACTIVE' => 'Y',
    ],
    true,
    ['ID', 'NAME', 'DESCRIPTION', 'PICTURE', 'UF_COLOR'],
    false

);
//
$sections = [];
while ($ob = $db->GetNext()) {
    if ($ob['ELEMENT_CNT'] > 0) {
        $sections[] = $ob;
    }
}

// TODO:: add background-color
$defaultColor = '#F7A1B2';

?>
<div class="sb-category-title">Категории сервисов</div>
<div class="sb-category-wrapper">

    <?php foreach ($sections as $section): ?>
        <a class="sb-category-item" href="/SW/?sectionId=<?= $section['ID']; ?>"
           style="background-color: <?= $section['UF_COLOR'] ?: $defaultColor; ?>">
            <div class="sb-category-item__title"><?= $section['NAME']; ?></div>
            <div class="sb-category-item__preview-text"><?= $section['DESCRIPTION']; ?></div>
            <div class="sb-category-item__img">
                <img src="<?= CFile::ResizeImageGet($section['PICTURE'], ['width' => 120, 'height' => 120])['src']; ?>" alt="<?= $section['NAME']; ?>">
            </div>
        </a>
    <?php endforeach; ?>

</div>
<?  } else { ?>
    <p>Установите модуль 'firstbit.eo'</p>
    <?
}
?>
