<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\SystemException;

class Desktop extends \CBitrixComponent
{
    /**
     * include lang files
     */
    public function onIncludeComponentLang ()
    {
        $this->includeComponentLang(basename(__FILE__));
        Loc::loadMessages(__FILE__);
    }

    /**
     * prepare input params
     * @param array $params
     * @return array
     */
    public function onPrepareComponentParams ($params)
    {
        return $params;
    }

    /**
     * check required to be input params
     * @throws SystemException
     */
    protected function checkParams ()
    {
    }

    /**
     * get component results
     */
    protected function getResult ()
    {
        global $USER;

        if (!$USER->IsAuthorized()) {
            return;
        }

        $arResult = [];

        $this->arResult = $arResult;
    }

    /**
     * component logic
     */
    public function executeComponent ()
    {
        try {
            $this->checkParams();

            $this->initTitleHandler();

            $this->getResult();
            $this->includeComponentTemplate();
        } catch (Exception $e) {
            ShowError($e->getMessage());
        }
    }

    private function initTitleHandler ()
    {
        global $APPLICATION;
        global $USER;

        $strNewTitle = '';
        $UserFirstName = $USER->GetFirstName();
        $CurUser = CUser::GetByID($USER->GetID())->fetch();
        $currentDate = new DateTime();
        $bufDate = new DateTime();
        $currentDateFormated = $currentDate->format('d.m.Y');
        $CurUserTime = $bufDate->setTimestamp($CurUser['TIME_ZONE_OFFSET'] + (new DateTime)->getTimestamp());
        $CurUserHours = $CurUserTime->format('H');
        $num_session = 0;

        // $qwHits = CHit::GetList(
        //     $by = "s_id",
        //     $sort = "desc",
        //     [
        //         'USER'             => $USER->GetID(),
        //         'USER_EXACT_MATCH' => 'Y',
        //         'URL'              => 'desktop',
        //         'DATE_1'           => $currentDateFormated,
        //     ],
        //     $is_Filtred
        // );
        // while ($arHit = $qwHits->fetch()) {
        //     if ($arHit['USER_AUTH'] == 'Y') {
        //         $check = stripos($arHit['URL'], 'backurl=%2Fdesktop%2');
        //         if ($check === false) {
        //             $num_session++;
        //         }
        //     }
        // }
        // if ($num_session == 0) {
        //     $num_session = 1;
        // }

        $ListTitle = [];
        $qwListTitle = CIBlockElement::GetList(
            ['SORT' => "ASC"],
            [
                "IBLOCK_CODE" => "DESKTOP_TITLE",
                "ACTIVE"      => "Y",
            ],
            false,
            false,
            ['ID', 'NAME', "PROPERTY_TITLE_TYPE", "PROPERTY_DATE_START", "PROPERTY_DATE_END", "PROPERTY_TIME_START", "PROPERTY_TIME_END"]
        );
        while ($arLitsTitle = $qwListTitle->fetch()) {
            $ListTitle[] = $arLitsTitle;
        }

        /** Проверка по условию */
        foreach ($ListTitle as $index => $TitleItem) {
            if ($TitleItem['PROPERTY_TITLE_TYPE_VALUE'] == 'По условию') {
                if ($TitleItem['PROPERTY_DATE_START_VALUE']) {
                    $dateStart = DateTime::createFromFormat('d.m.Y', $TitleItem['PROPERTY_DATE_START_VALUE']);
                    if ($dateStart < $currentDate) {
                        unset($ListTitle[$index]);
                        continue;
                    }
                }
                if ($TitleItem['PROPERTY_DATE_END_VALUE']) {
                    $dateEnd = DateTime::createFromFormat('d.m.Y', $TitleItem['PROPERTY_DATE_END_VALUE']);
                    if ($dateEnd > $currentDate) {
                        unset($ListTitle[$index]);
                        continue;
                    }
                }
                $strNewTitle = $TitleItem['NAME'];
            }
            if ($TitleItem['PROPERTY_TIME_START_VALUE']) {
                if ((int)$TitleItem['PROPERTY_TIME_START_VALUE'] > (int)$CurUserHours) {
                    unset($ListTitle[$index]);
                    continue;
                }
            }
            if ($TitleItem['PROPERTY_TIME_END_VALUE']) {
                if ((int)$TitleItem['PROPERTY_TIME_END_VALUE'] <= (int)$CurUserHours) {
                    unset($ListTitle[$index]);
                }
            }
        }

        if ($strNewTitle == '') {
            if ((int)$num_session == 1) {
                foreach ($ListTitle as $index => $TitleItem) {
                    if ($TitleItem['PROPERTY_TITLE_TYPE_VALUE'] == 'При первом заходе') {
                        $strNewTitle = $TitleItem['NAME'];
                    }
                }
            }
        }

        if ($strNewTitle == '') {
            if ((int)$num_session == 2) {
                foreach ($ListTitle as $index => $TitleItem) {
                    if ($TitleItem['PROPERTY_TITLE_TYPE_VALUE'] == 'При повторном заходе') {
                        $strNewTitle = $TitleItem['NAME'];
                    }
                }
            }
        }

        if ($strNewTitle == '') {
            $newList = [];
            foreach ($ListTitle as $index => $TitleItem) {
                if ($TitleItem['PROPERTY_TITLE_TYPE_VALUE'] == 'Рандомный заголовок') {
                    $newList[] = $ListTitle[$index];
                }
            }
            if (count($newList) > 0) {
                $rand_key = array_rand($newList);
                $strNewTitle = $newList[$rand_key]['NAME'];
            }
        }

        if ($strNewTitle != '') {
            $strNewTitle = str_replace('#NAME#', $UserFirstName, $strNewTitle);
            $APPLICATION->SetTitle($strNewTitle);
        }
    }
}
