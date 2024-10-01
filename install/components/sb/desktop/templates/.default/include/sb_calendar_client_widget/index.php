<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Calendar\Internals\EventTable;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Application;

$strDir = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
Asset::getInstance()->addCss($strDir . '/styles.css');

global $USER;

$moduleId = 'firstbit.desktop';

$today = new DateTime();
$offset = $_COOKIE['BITRIX_SM_TIME_ZONE'];

$connection = Application::getConnection();
$sqlHelper = $connection->getSqlHelper();

$widgetStatus = [];
$query = $connection->query("SELECT `VALUE` FROM `b_option` WHERE `MODULE_ID` = 'firstbit.desktop' AND `NAME` = 'HIDE_BIRTHDAY_WIDGET'");
while ($stat = $query->fetch()) {
    $widgetStatus [] = $stat['VALUE'];
}

if (in_array('N', $widgetStatus, true)) {

}else {

    $ownerId = [];
    $query = $connection->query("SELECT `VALUE` FROM `b_option` WHERE `MODULE_ID` = 'firstbit.desktop' AND `NAME` = 'SELECTED_GROUP'");
    while ($group = $query->fetch()) {
        $ownerId[] = $group['VALUE'];
    }

    $events = EventTable::getList([
        'filter' => [
            'ACTIVE' => 'Y',
            'DELETED' => 'N',
            'CAL_TYPE' => 'group',
            '>DATE_FROM_TS_UTC' => $today->getTimestamp() + $offset * 60,
            'OWNER_ID' => $ownerId
        ],
        'select' => ['ID', 'NAME', 'OWNER_ID', 'DATE_FROM'],
        'limit' => 6,
        'order' => ['DATE_FROM_TS_UTC' => 'ASC']
    ])->fetchAll();

    $templateDetailLink = '/workgroups/group/#GROUP_ID#/calendar/?EVENT_ID=#EVENT_ID#&EVENT_DATE=#EVENT_DATE#';


    foreach ($events as &$event) {
        $event['DATE'] = $event['DATE_FROM']->format('d.m.Y');
        $event['DATE_TIME'] = $event['DATE_FROM']->format('d.m.Y H:i:s');
        $event['DETAIL_URL'] = str_replace(
            ['#GROUP_ID#', '#EVENT_ID#', '#EVENT_DATE#'],
            [$event['OWNER_ID'], $event['ID'], $event['DATE_TIME']],
            $templateDetailLink
        );
        $event['TODAY'] = $today->format('Y-m-d') == $event['DATE_FROM']->format('Y-m-d');
    }
    unset($event);
    $i = 0;
    $count = count($events);
    ?>
    <!---->
    <div class="calendar-container">
        <div class="widget widget-birthdays">
            <div class="widget-top">
                <a href="/workgroups/group/40/calendar/" target="_blank" class="widget-top-title"><?= Option::get($moduleId, 'WIDGET_TITLE_1', 'Мероприятия для клиентов') ?></a>
            </div>
            <div class="widget-body">
                <? if (empty($events)):?>
                    <div class="widget-body__item">
                        <div class="widget-body--empty">
                            На ближайшее время событий нет
                        </div>
                    </div>
                <? else: ?>
                    <? foreach ($events as $index => $event): ?>
                        <?if($index < 5) {?>
                            <a href="<?= $event['DETAIL_URL'] ?>" target="_blank" class="widget-body__item <?= (++$i == $count) ? 'widget-last-item' : '';?>">
                                <div class="sidebar-event-info">
                                    <div class="event-name"><?= $event['NAME']; ?></div>
                                    <div class="event-date <?= $event['TODAY'] ? 'event-date--green' : ''; ?>"><?= $event['TODAY'] ? 'Сегодня' : $event['DATE']; ?></div>
                                </div>
                            </a>
                        <? } ?>
                    <? endforeach; ?>
                    <? if(count($events) > 5) { ?>
                        <a href="/workgroups/group/40/calendar/" target="_blank" class="widget-body__item" style="text-align: center">Посмотреть другие события</a>
                    <? } ?>
                <? endif; ?>
            </div>
        </div>
    </div>
    <?
}
