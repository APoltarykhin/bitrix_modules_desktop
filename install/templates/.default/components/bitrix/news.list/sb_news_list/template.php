<? if ( ! defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) {
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
$this->setFrameMode( true );
$this->addExternalCss('/local/css/news_animation.css');

use Bitrix\Main\Config\Option;
use Firstbit\Desktop\Bitrix;

CModule::IncludeModule('firstbit.desktop');
$moduleId = 'firstbit.desktop';

function getSectionPicture( $iblockId, $id, $default = "" )
{
    $linkUrlPic = $default;

    $arResSection = Firstbit\Desktop\Bitrix\SBElement::getSection( [
        'IBLOCK_ID' => $iblockId,
        'ID' => $id
    ], [ 'NAME', 'ID', 'UF_SOCNETTYPE' ] );

    if ( $arResSection['UF_SOCNETTYPE'] ) {
        $UF_SOCNETTYPE = $arResSection['UF_SOCNETTYPE'];
        $arWorkGroup = \Bitrix\Socialnetwork\WorkgroupTable::getById( $UF_SOCNETTYPE )->fetchAll();
        if ( $arWorkGroup ) {
            $linkUrlPic = CFile::GetPath( $arWorkGroup[0]['IMAGE_ID'] );
        }
    }

    return $linkUrlPic;
}

foreach ( $arResult['ITEMS'] as $ITEM ):?>
    <div class="col-xs-12 col-sm-12 col-md-12 sb_gadget_link_row">
        <?php
        $linkUrlPic = '/local/components/sb/desktop/templates/.default/include/sb_news/favicon-0.png';

        if ( $ITEM['PROPERTIES']['OLD_SECTION_PORTAL']['VALUE_ENUM_ID'] === '104' ) {
            $linkUrlPic = getSectionPicture( 46, 108742, $linkUrlPic );
        } else {
            $linkUrlPic = Option::get($moduleId, 'NEWS_IMAGE', '');
        }
        $responsibleUser = \Bitrix\Main\UserTable::getList([
            'filter' => ['ID' => $ITEM['PROPERTIES']['MODERATOR_ID']['VALUE']],
            'select' => ['ID', 'PERSONAL_PHOTO']
        ])->fetch();
        ?>
        <div class="sb_pic"
             style="background-image: url('<?= $linkUrlPic ?>');     background-size: <?= $linkUrlPic == '/local/gadgets/1cbit/sb_news/favicon-0.png' ? '80%' : '100%' ?>;"></div>
        <div class="sb_name_news size">
            <a class="" href="<?= $ITEM['DETAIL_PAGE_URL'] ?> "><?= $ITEM['NAME'] ?></a>
        </div>
        <div class="sb_right_block">
            <div style="display: flex">
                <? if ($ITEM['PROPERTIES']['MAIN_EVENT']['VALUE'] == 'Да'): ?>
                    <div class="sb_fire">важно!</div>
                <? endif; ?>
                <? if ($ITEM['PROPERTIES']['LIVE']['VALUE'] == 'Да'): ?>
                    <div class="sb_live">
                        <div class="glitch" data-text="LIVE">LIVE</div>
                    </div>
                <? endif; ?>
            </div>
            <a class="sb_responsible_user" href="/company/personal/user/<?= $responsibleUser['ID']; ?>/"
               bx-tooltip-user-id="<?= $responsibleUser['ID']; ?>"
               style="background-image: url('<?= $responsibleUser['PERSONAL_PHOTO']
                   ? CFile::ResizeImageGet($responsibleUser['PERSONAL_PHOTO'], array('width'=>25, 'height'=>25), BX_RESIZE_IMAGE_PROPORTIONAL, true)['src']
                   : 'data:image/svg+xml;charset=US-ASCII,%3Csvg%20viewBox%3D%220%200%2089%2089%22%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%3E%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%3Ccircle%20fill%3D%22%23535C69%22%20cx%3D%2244.5%22%20cy%3D%2244.5%22%20r%3D%2244.5%22/%3E%3Cpath%20d%3D%22M68.18%2071.062c0-3.217-3.61-16.826-3.61-16.826%200-1.99-2.6-4.26-7.72-5.585a17.363%2017.363%200%200%201-4.887-2.223c-.33-.188-.28-1.925-.28-1.925l-1.648-.25c0-.142-.14-2.225-.14-2.225%201.972-.663%201.77-4.574%201.77-4.574%201.252.695%202.068-2.4%202.068-2.4%201.482-4.3-.738-4.04-.738-4.04a27.076%2027.076%200%200%200%200-7.918c-.987-8.708-15.847-6.344-14.085-3.5-4.343-.8-3.352%209.082-3.352%209.082l.942%202.56c-1.85%201.2-.564%202.65-.5%204.32.09%202.466%201.6%201.955%201.6%201.955.093%204.07%202.1%204.6%202.1%204.6.377%202.556.142%202.12.142%202.12l-1.786.217a7.147%207.147%200%200%201-.14%201.732c-2.1.936-2.553%201.485-4.64%202.4-4.032%201.767-8.414%204.065-9.193%207.16-.78%203.093-3.095%2015.32-3.095%2015.32H68.18z%22%20fill%3D%22%23FFF%22/%3E%3C/g%3E%3C/svg%3E'
               ?>')"></a>
            <div class="sb_date_news feed-time"><?= ConvertDateTime($ITEM['ACTIVE_FROM'], "DD.MM", "ru") ?></div>
        </div>
    </div>
<? endforeach;

?>
