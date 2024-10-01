<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Application;

$strDir = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
Asset::getInstance()->addCss($strDir . '/styles.css');

$moduleId = 'firstbit.desktop';

$connection = Application::getConnection();
$sqlHelper = $connection->getSqlHelper();

$widgetStatus = [];
$query = $connection->query("SELECT `VALUE` FROM `b_option` WHERE `MODULE_ID` = 'firstbit.desktop' AND `NAME` = 'HIDE_BIRTHDAY_WIDGET_TWO'");
while ($stat = $query->fetch()) {
    $widgetStatus [] = $stat['VALUE'];
}

if (in_array('N', $widgetStatus, true)) {

}else {

    $groupId = [];
    $query = $connection->query("SELECT `VALUE` FROM `b_option` WHERE `MODULE_ID` = 'firstbit.desktop' AND `NAME` = 'SELECTED_GROUP_TWO'");
    while ($group = $query->fetch()) {
        $groupId[] = $group['VALUE'];
    }



    global $USER;
    $now = new DateTime();
    $fromLimit = $now->add('-1 month')->setTime(0, 0, 0)->format('d.m.Y');
    $toLimit = $now->add('+2 month')->add('-1 day')->format('d.m.Y');

    $arFilter = [
        'CAL_TYPE' => 'group',  // Тип календаря (group - для рабочих групп)
        'OWNER_ID' => $groupId[0],  // ID рабочей группы
        'FROM_LIMIT' => $fromLimit,
        'TO_LIMIT' => $toLimit
    ];

    $res = \CCalendarEvent::GetList([
        'arFilter' => $arFilter,
        'parseRecursion' => true,
        'fetchAttendees' => true,
        'userId' => $USER->GetID(),
        'fetchMeetings' => false,
        'setDefaultLimit' => false,
    ]);


    // $res = array_filter($res, function ($e) use ($now) {
    //     return \Bitrix\Main\Type\DateTime::createFromTimestamp(intval($e['DATE_FROM'])) >= $now;
    // });

    usort($res, function ($a, $b) {
        $dateA = \Bitrix\Main\Type\DateTime::createFromTimestamp(intval($a['DATE_FROM']));
        $dateB = \Bitrix\Main\Type\DateTime::createFromTimestamp(intval($b['DATE_FROM']));
        return $dateA->getTimestamp() - $dateB->getTimestamp();
    });

    $templateDetailLink = '/workgroups/group/#GROUP_ID#/calendar/?EVENT_ID=#EVENT_ID#&EVENT_DATE=#EVENT_DATE#';
    $events = [];
    foreach ($res as $event) {
        $dateFrom = new DateTime($event['DATE_FROM']);  // Создаем объект DateTime из строки с датой
        $events[] = [
            'ID' => $event['ID'],
            'NAME' => $event['NAME'],
            'DATE' => $dateFrom->format('d.m.Y'),  // Форматируем дату
            // ... остальные данные событий
        ];
    }
    $count = count($events);
    ?>

    <div class="calendar-container">
        <div class="widget widget-birthdays">
            <div class="widget-top">
                <a href="/workgroups/group/" target="_blank" class="widget-top-title"><?= Option::get($moduleId, 'WIDGET_TITLE_2', 'Мероприятия') ?></a>
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
                        <a href="/workgroups/group/" target="_blank" class="widget-body__item" style="text-align: center">Посмотреть другие события</a>
                    <? } ?>
                <? endif; ?>
            </div>
        </div>
    </div>
    <?
}
