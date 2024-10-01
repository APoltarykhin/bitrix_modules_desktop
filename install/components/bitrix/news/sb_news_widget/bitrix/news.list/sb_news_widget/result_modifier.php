<?php
//global ${$arParams['FILTER_NAME']};
// $arIblock_id = [];
// $arIblock_id = 90;
// $arElementFire = [];
//
// foreach ($arIblock_id as $IBLOCK_ID) {
//
//     $arTMP = \SB\Site\Bitrix\SBElement::getElement([
//         'IBLOCK_ID' => $IBLOCK_ID,
//         'ACTIVE' => 'Y',
//         [
//             'LOGIC' => 'OR',
//             'PROPERTY_MAIN_EVENT_VALUE' => 'Да',
//            'PROPERTY_HOT_NEWS_VALUE' => 'Да'
        // ],
        // ">=DATE_CREATE" => date('d.m.Y', strtotime("-2 months")),
        // "ACTIVE_DATE" => "Y"
    // ],
    //     [
    //         'ID',
    //         'NAME',
    //         'ACTIVE',
    //         'IBLOCK_ID',
    //         'IBLOCK_TYPE_ID',
    //         'PREVIEW_PICTURE',
    //         'PROPERTY_MAIN_EVENT_VALUE',
    //         'PROPERTY_HOT_NEWS_VALUE'
    //     ],
    //     ["nPageSize" => 2],
    //     ["created_date" => "ASC"],1
    // );
    //
    // if ($arTMP) {
    //     $arElementFire[] = $arTMP;
    // }
// }
// $tmpFerstITEMS = [];
// if (count($arElementFire) === 1) {
//
//     $arElementFire = $arElementFire[0];
//     foreach ($arElementFire as $item) {
//         $tmpFerstITEMS[] = [
//             'ID' => $item['ID'],
//             'NAME' => $item['NAME'],
//             'ACTIVE' => $item['ACTIVE'],
//             'DETAIL_PAGE_URL' => '/' . $item['IBLOCK_TYPE_ID'] . '/' . $item['ID'] . '/',
//             'PREVIEW_PICTURE' => ['SRC' => $item['PREVIEW_PICTURE']],
//             'PROPERTIES' => [
//                 'MAIN_EVENT' => [
//                     'VALUE' => $item['PROPERTY_MAIN_EVENT_VALUE_VALUE']
//                 ],
//             ],
//         ];
//     }
// } else {
//     foreach ($arElementFire as $arElementFire) {
//         foreach ($arElementFire as $item) {
//             $tmpFerstITEMS[] = [
//                 'ID' => $item['ID'],
//                 'NAME' => $item['NAME'],
//                 'ACTIVE' => $item['ACTIVE'],
//                 'DETAIL_PAGE_URL' => '/' . $item['IBLOCK_TYPE_ID'] . '/' . $item['ID'] . '/',
//                 'PREVIEW_PICTURE' => ['SRC' => $item['PREVIEW_PICTURE']],
//                 'PROPERTIES' => [
//                     'MAIN_EVENT' => [
//                         'VALUE' => $item['PROPERTY_MAIN_EVENT_VALUE_VALUE']
//                     ],
//                 ],
//             ];
//         }
//     }
// }
//
//
// $arResult['ITEMS'] = array_reverse(array_merge($arResult['ITEMS'], $tmpFerstITEMS));
