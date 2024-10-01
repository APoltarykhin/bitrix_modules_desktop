<?php

namespace Firstbit\Desktop\Bitrix;

use Bitrix\Main\Loader;
use CIBlockElement;
use CIBlockProperty;
use CIBlockSection;
use CPHPCache;
use CUtil;


class SBElement
{

    public static function getElement($arFilter, $arSelect = ['*'], $arNavStartParam = false, $arOrder = [], $cacheLifetime = 86400)
    {
        Loader::includeModule('iblock');
        $obCache = new CPHPCache();

        $cacheID = md5(serialize([$arFilter, __FUNCTION__]));//уникальный ключ кеша
        $cachePath = '/getElement';//Подраздел для хранения кеша
        //$cacheLifetime = 1;
        if ($cacheLifetime == 1) {
            $GLOBALS['CACHE_MANAGER']->ClearByTag("getElement");
        }
        if ($obCache->startDataCache($cacheLifetime, $cacheID, $cachePath)) {
            $res = CIBlockElement::GetList($arOrder, $arFilter, false, $arNavStartParam, $arSelect);
            $arResult = false;
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                if ($arFields['PREVIEW_PICTURE']) {
                    $arFields['PREVIEW_PICTURE'] = \CFile::GetPath($arFields['PREVIEW_PICTURE']);
                }
                $arFields['PROP'] = $ob->GetProperties();
                $arResult[] = $arFields;
            }
            if ($arResult === false) {
                $GLOBALS['CACHE_MANAGER']->ClearByTag("getElement");
            }
            //Добавим теги нашему кешу
            $GLOBALS['CACHE_MANAGER']->StartTagCache($cachePath);
            $GLOBALS['CACHE_MANAGER']->RegisterTag('getElement');//Наш собственный тег
            $GLOBALS['CACHE_MANAGER']->EndTagCache();
            $obCache->EndDataCache($arResult);// Сохраняем переменные в кэш.

        } else {
            //Получаем данные из кеша
            $arResult = $obCache->GetVars();

            if (empty($arResult)) {
                return self::getElement($arFilter, $arSelect, $arNavStartParam, 1);
            }
        }
        return $arResult;
    }

    public static function isProperty($arFilter)
    {
        $result = false;
        $properties = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), $arFilter);
        while ($prop_fields = $properties->GetNext()) {
            $result[] = $prop_fields;
        }

        if ($result != false && count($result) == 1) {
            $result = $result[0];
        }
        return $result;
    }

    public static function getSection($arFilter, $arSelect = ['*'])
    {
        Loader::includeModule('include');
        $db_list = CIBlockSection::GetList([], $arFilter, true, $arSelect);
        $result = false;
        while ($arElement = $db_list->Fetch()) {
            $result[] = $arElement;
        }
        if (count($result) == 1) {
            return $result[0];
        }
        return $result;
    }

    public static function addElement($arFields, $prefixNum = 0)
    {
        \CModule::IncludeModule('iblock');

        if (empty($arFields)) {
            return false;
        }
        $arFields['ACTIVE'] = 'Y';

        if ($prefixNum > 0) {
            $arFields['CODE'] = self::translitCode($arFields['NAME'] . '_' . $prefixNum);
        }

        $newElement = new CIBlockElement;

        if (!$ELEMENT_ID = $newElement->Add($arFields)) {
            \_::d('Error Element Add');
            \_::d($arFields);
            \_::d($newElement->LAST_ERROR);
        }


        return $ELEMENT_ID;
    }

    /**
     * генерация кода для Раздела или элемента
     * @param $name
     * @return string
     */
    public static function translitCode($name)
    {
        $params = array(
            "max_len" => "150",
            "change_case" => "L",
            "replace_space" => "_",
            "replace_other" => "_",
            "delete_repeat_replace" => "true",
            "use_google" => "false",
        );

        if (strlen($name) > 0) {
            return CUtil::translit($name, "ru", $params);
        }

    }
}
ams);
        }

    }
}
