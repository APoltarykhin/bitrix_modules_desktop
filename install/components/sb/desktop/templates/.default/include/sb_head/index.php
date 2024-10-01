<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Firstbit\Eo\Repositories\SwRepository;
use Bitrix\Main\Page\Asset;
use Firstbit\Eo\Repositories\FavoriteLinks;
use SB\Site\Variables;

$this->SetViewTarget("below_pagetitle");

$strDir = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
Asset::getInstance()->addCss($strDir . '/styles.css');

CModule::IncludeModule('firstbit.desktop');


global $USER, $APPLICATION;

if (CModule::IncludeModule('firstbit.eo')) {
$userId = $USER->GetID();


$links = Firstbit\Eo\Repositories\FavoriteLinks::getListByUserId($userId, 5);

$appel = Firstbit\Eo\Repositories\SwRepository::getCountTask($userId);
?>

    <div class="sb-head-wrapper">
        <div class="sb-head-item">
            <div class="sb-head-item__star"></div>
            <div class="sb-head-item__link-wrapper">
                 <?foreach ($links as $link): ?>
                     <a class="sb-head-item__link" href="javascript:void(0);" onclick="BX.SidePanel.Instance.open('<?= $link->get('UF_LINK'); ?>', { width: '100%'})"><?= $link->get('UF_NAME'); ?></a>
                <? endforeach; ?>
            </div>
            <a class="sb_gadget_detail sb-head-item__link" href="/SW/"></a>
        </div>
        <div class="sb-head-item">
            <div class="sb-head-item__task">
                <span class="sb-head-item__title">Мои:</span>
                <a href="/SW/tasks/" class="sb-head-item__counter">
                    <span class="sb-head-item__count"><?= $appel['OPEN_TASKS']; ?></span>
                    <span class="sb-head-item__label">Заявок в работе</span>
                </a>
                <a href="/SW/tasks/" class="sb-head-item__counter">
                    <span class="sb-head-item__count--green" id="sb-new_comments"><?= $appel['COMMENT_TASKS']; ?></span>
                    <span class="sb-head-item__label">Комментарии</span>
                </a>

                <div class="sb-head-item__count--hr"></div>

                <a href="javascript:void(0);" class="sb-head-item__counter" onclick="sb_readAllComments('<?= $userId; ?>', '<?= 1; ?>')">
                    <span class="sb-head-item__count--read"></span>
                </a>
            </div>
        </div>
    </div>
   <?php }else{ ?>
    <p>Установите модуль bitrix.eo</p>
<?php
}
$this->EndViewTarget();
