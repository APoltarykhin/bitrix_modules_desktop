<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Page\Asset;
use SB\Site\Ratings\RatingsRepo;

$strDir = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
Asset::getInstance()->addCss($strDir . '/styles.css');

$rating = RatingsRepo::getRatingsInfoById(RatingsRepo::getRatingIdByCode('PIX'), 5);

if (!empty($rating['ITEMS'])):
?>
<div class="sb_block_rating">
    <div class="sb_block_rating-top-wrapper">
        <div class="sb_gadget_title">Рейтинг <?= $rating['NAME']; ?></div>
        <a href="/ratings/detail.php?ID=<?= $rating['ID']; ?>" class="sb_gadget_detail"></a>
    </div>
    <div class="row sb_gadget_link_block">
        <? foreach ($rating['ITEMS'] as $item): ?>

            <div class="sb_rating_item">
                <div class="sb_rating_item__position">
                    <div class="num"><?= $item['POSITION']; ?></div>
                </div>
                <div class="sb_rating_item__info">
                    <div class="sb_rating_item__info--avatar">
                        <? if (!empty($item['MEDAL'])): ?>
                            <div class="<?= $item['MEDAL']; ?>"></div>
                        <? endif; ?>
                    </div>
                    <div class="sb_rating_item__info--name">
                        <a href="javascript:void(0);"
                           onclick="BX.SidePanel.Instance.open(`/aboutcompany/map_office/detail.php?ID=<?= $item['OFFICE_ID']; ?>`)"
                        ><?= $item['OFFICE_NAME']; ?></a>
                    </div>
                </div>
                <div class="sb_rating_item__progress_wrapper">
                    <div class="sb_rating_item__progress">
                        <div class="sb_rating_item__progress--line " style="width: <?= $item["PERCENT_PROGRESS_LINE"] ?? 0; ?>%;"></div>
                        <div class="sb_rating_item__progress--text"><?= $rating['UF_R_MAIN_POINTS']; ?>: <?= $item['MAIN_ATTRIBUTE_VALUE']; ?></div>
                    </div>
                </div>
            </div>

        <? endforeach; ?>
    </div>
</div>
<? endif; ?>
