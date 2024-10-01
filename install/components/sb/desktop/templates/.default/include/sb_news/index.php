<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Page\Asset;

$strDir = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
Asset::getInstance()->addCss($strDir . '/styles.css');
Asset::getInstance()->addCss("/bitrix/css/main/bootstrap.min.css");
?>

<?php
if (CModule::IncludeModule('firstbit.news')) {
    ?>

    <div class="sb_block_news">
        <div class="sb_block_news-top-wrapper">
            <div class="sb_gadget_title">Новости</div>
            <a href="/news/" class="sb_gadget_detail"></a>
        </div>
        <div class="row sb_gadget_link_block">
            <?php
            global $arNewsCompany, $USER;
            $arNewsCompany = [];
            $arNewsCompany['ACTIVE'] = 'Y';
            $arNewsCompany['IBLOCK_ID'] = "CBIT_NEWS";

            $untreatedDepartment = \Bitrix\Iblock\SectionTable::query()
                ->where('IBLOCK.CODE', 'departments')
                ->where('CODE', 'UNTREATED')
                ->addSelect('ID')
                ->exec()
                ->fetchObject();
            $untreatedDepartmentId = false;
            if ($untreatedDepartment) {
                $untreatedDepartmentId = $untreatedDepartment->getId();
            }

            $userDepartament = \Bitrix\Main\UserTable::query()
                ->where('ID', $USER->GetID())
                ->whereNot('UF_DEPARTMENT', false)
                ->addSelect('UF_DEPARTMENT')
                ->exec()
                ->fetchObject();

            $departamentId = false;
            if ($userDepartament) {
                $departamentId = $userDepartament->get('UF_DEPARTMENT')[0];
            }

            $departamentTree = [];
            $nav = CIBlockSection::GetNavChain(5, $departamentId, ['ID']);
            while ($navItem = $nav->Fetch()) {
                $departamentTree[] = $navItem['ID'];
            }

            $isUntreatedDepartment = in_array($untreatedDepartmentId, $departamentTree);
            if ($isUntreatedDepartment) {
                $arNewsCompany['SECTION_ID'] = \SB\Site\News::getFederalNewsSectionId();
            } else {
                $arNewsCompany[] = [
                    'LOGIC' => 'OR',
                    ['PROPERTY_STRUCTURE' => false],
                    ['PROPERTY_STRUCTURE' => $departamentTree],
                ];
            }

            $arNewsCompany[] = [
                'LOGIC' => 'OR',
                ['PROPERTY_USER_GROUP' => false],
                ['PROPERTY_USER_GROUP' => $USER->GetUserGroupArray()],
            ];
            ?>

            <?$APPLICATION->IncludeComponent(
                "bitrix:news.list",
                "sb_news_list",
                Array(
                    "ACTIVE_DATE_FORMAT" => "d.m.Y",
                    "ADD_SECTIONS_CHAIN" => "Y",
                    "AJAX_MODE" => "N",
                    "AJAX_OPTION_ADDITIONAL" => "",
                    "AJAX_OPTION_HISTORY" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "Y",
                    "CACHE_FILTER" => "N",
                    "CACHE_GROUPS" => "Y",
                    "CACHE_TIME" => "36000000",
                    "CACHE_TYPE" => "A",
                    "CHECK_DATES" => "Y",
                    "COMPONENT_TEMPLATE" => "sb_news_list",
                    "DETAIL_URL" => "",
                    "DISPLAY_BOTTOM_PAGER" => "Y",
                    "DISPLAY_DATE" => "Y",
                    "DISPLAY_NAME" => "Y",
                    "DISPLAY_PICTURE" => "Y",
                    "DISPLAY_PREVIEW_TEXT" => "Y",
                    "DISPLAY_TOP_PAGER" => "N",
                    "FIELD_CODE" => array(0=>"",1=>"",),
                    "FILTER_NAME" => "",
                    "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                    "IBLOCK_ID" => "120",
                    "IBLOCK_TYPE" => "news",
                    "INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
                    "INCLUDE_SUBSECTIONS" => "Y",
                    "MESSAGE_404" => "",
                    "NEWS_COUNT" => "20",
                    "PAGER_BASE_LINK_ENABLE" => "N",
                    "PAGER_DESC_NUMBERING" => "N",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                    "PAGER_SHOW_ALL" => "N",
                    "PAGER_SHOW_ALWAYS" => "N",
                    "PAGER_TEMPLATE" => ".default",
                    "PAGER_TITLE" => "Новости",
                    "PARENT_SECTION" => "",
                    "PARENT_SECTION_CODE" => "",
                    "PREVIEW_TRUNCATE_LEN" => "",
                    "PROPERTY_CODE" => ['LIVE'],
                    "SET_BROWSER_TITLE" => "Y",
                    "SET_LAST_MODIFIED" => "N",
                    "SET_META_DESCRIPTION" => "Y",
                    "SET_META_KEYWORDS" => "Y",
                    "SET_STATUS_404" => "N",
                    "SET_TITLE" => "N",
                    "SHOW_404" => "N",
                    "SORT_BY1" => "ACTIVE_FROM",
                    "SORT_BY2" => "SORT",
                    "SORT_ORDER1" => "DESC",
                    "SORT_ORDER2" => "ASC",
                    "STRICT_SECTION_CHECK" => "N"
                )
            );?>

        </div>
    </div>
<?php
} else { ?>
    <p>Установите модуль 'firstbit.news'</p>
    <?php
}
?>
