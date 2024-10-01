<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */

/** @var CBitrixComponent $component */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc as Loc;
use Bitrix\Main\UI\Extension;

Loc::loadMessages(__FILE__);
Extension::load([
    'ui',
    'jquery',
    'sidepanel',
]);

include(__DIR__ . '/include/sb_head/index.php');

?>

<div class="desktop-container">
    <div class="desktop-main">
        <div class="desktop-main__item">
            <?include(__DIR__ . '/include/sb_popular_service/index.php');?>
        </div>
        <div class="desktop-main__item">
            <?include(__DIR__ . '/include/sb_news/index.php'); ?>
        </div>
        <div class="desktop-main__item">
            <?include(__DIR__ . '/include/sb_sw/index.php'); ?>
        </div>
        <div class="desktop-main__item">
            <?// include(__DIR__ . '/include/sb_ratings/index.php'); ?>
        </div>
    </div>
</div>


<?php
//SIDEBAR

$this->SetViewTarget("sidebar");

include(__DIR__ . '/include/sb_calendar_employee_widget/index.php');
include(__DIR__ . '/include/sb_calendar_client_widget/index.php');
include(__DIR__ . '/include/sb_birthday_widget/index.php');

$this->EndViewTarget();
