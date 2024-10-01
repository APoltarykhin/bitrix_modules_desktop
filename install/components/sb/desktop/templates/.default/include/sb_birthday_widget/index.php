<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Page\Asset;
use Firstbit\Desktop\Repositories\UsersRepository;
use Bitrix\Main\Application;


$strDir = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
Asset::getInstance()->addCss($strDir . '/styles.css');

CModule::IncludeModule('firstbit.desktop');

$connection = Application::getConnection();
$sqlHelper = $connection->getSqlHelper();


$widgetStatus = [];
$query = $connection->query("SELECT `VALUE` FROM `b_option` WHERE `MODULE_ID` = 'firstbit.desktop' AND `NAME` = 'HIDE_BIRTHDAY_WIDGET_REAL'");
while ($stat = $query->fetch()) {
    $widgetStatus [] = $stat['VALUE'];
}

if (in_array('N', $widgetStatus, true)) {

}else {

    // $ownerId = [];
    // $query = $connection->query("SELECT `VALUE` FROM `b_option` WHERE `MODULE_ID` = 'firstbit.desktop' AND `NAME` = 'SELECTED_GROUP_REAL'");
    // while ($group = $query->fetch()) {
    //     $ownerId[] = $group['VALUE'];
    // }

    global $USER;
    $usersRepo = new UsersRepository($USER->GetID());

    $today = date("Y-m-d", time());
    $birthdayResult = $usersRepo->getUserListByDateBirthday($today, $today, true, 4);

    $i = 0;
    $count = count($birthdayResult);
?>

    <div class="birthday-container">
        <div class="widget widget-birthdays">
            <div class="widget-top">
                <a class="widget-top-title" href="/aboutcompany/about/dni-rozhdeniya.php">Празднуют сегодня</a>
            </div>
            <div class="widget-body">
                <? if (empty($birthdayResult)):?>
                    <div class="widget-body__item">
                        <div class="widget-body--empty">
                            Сегодня в твоем офисе праздников нет.
                            Посмотри, кто будет отмечать в ближайшее время
                        </div>
                    </div>
                <? else: ?>
                    <? foreach ($birthdayResult as $arUser): ?>
                        <a href="javascript:void(0);" class="widget-body__item
                        <?= (++$i == $count) ? 'widget-last-item' : ''; ?> today-birth"
                           onclick="BX.SidePanel.Instance.open('/company/personal/user/<?= $USER->GetID(); ?>/blog/edit/grat/0/?gratUserId=<?= $arUser['ID']; ?>',
                               { cacheable: false, data: { entityType: 'gratPost', entityId: '<?= $arUser['ID']; ?>' }, width: 1000 });
                           return event.preventDefault();">
                            <div class="user-entry">
                                <div class="user-avatar user-default-avatar" style="display: inline-block; width: 40px; height: 40px; background-image: url('<?= $arUser["PERSONAL_PHOTO"]['src'] ?>'); background-size: cover; background-position: 50% 50%; border-radius: 50%;"></div>
                                <div class="sidebar-user-info">
                                    <span class="user-birth-name"><?= CUser::FormatName(CSite::GetNameFormat(false), $arUser, true); ?></span>
                                    <span class="user-birth-date"><?= $arUser['ANNIVERSARY'] ? 'Годовщина работы!' : 'День рождения!'; ?></span>
                                </div>
                            </div>
                        </a>
                    <? endforeach; ?>
                <? endif; ?>
            </div>
        </div>
    </div>
<?
}
