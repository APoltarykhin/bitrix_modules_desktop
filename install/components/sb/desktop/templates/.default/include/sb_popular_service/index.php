<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Page\Asset;

$strDir = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
Asset::getInstance()->addCss($strDir . '/styles.css');

$iblock = \Bitrix\Iblock\IblockTable::compileEntity('sbPopularService');

if (!$iblock) {
    return;
}

$iblock = $iblock->getDataClass();
$elements = $iblock::query()
    ->where('ACTIVE', true)
    ->setLimit(4)
    ->addOrder('SORT')
    ->addSelect('NAME')
    ->addSelect('PREVIEW_TEXT')
    ->addSelect('SB_LINK')
    ->addSelect('SB_COLOR_BG')
    ->addSelect('SB_TAG')
    ->exec()
    ->fetchCollection();

$defaultColor = '#F7A1B2';

?>

<div class="sb-popular-wrapper" style="display: flex; flex-wrap: wrap;">
    <div class="sb-popular-container" style="display: flex; flex-wrap: wrap; width: 100%;">
        <?php $count = 0; ?>
        <?php foreach ($elements as $element): ?>
        <a class="sb-popular-item" href="<?= $element->get('SB_LINK') ? $element->get('SB_LINK')->getValue() : '#'; ?>"
           style="background-color: <?= $element->get('SB_COLOR_BG') ? '#' . $element->get('SB_COLOR_BG')->getValue() : $defaultColor; ?>;
               flex: 1 0 25%; /* Устанавливаем ширину элемента в 25%, чтобы было по 4 элемента в строке */">
            <div class="sb-popular-item__top">
                <div class="sb-popular-item__title"><?= $element->get('NAME'); ?></div>
                <div class="sb-popular-item__tag">
                    <?= $element->get('SB_TAG') ? $element->get('SB_TAG')->getValue() : 'Ссылка'; ?>
                </div>
            </div>
            <div class="sb-popular-item__preview-text"><?= $element->get('PREVIEW_TEXT'); ?></div>
        </a>

        <?php endforeach; ?>

</div>
