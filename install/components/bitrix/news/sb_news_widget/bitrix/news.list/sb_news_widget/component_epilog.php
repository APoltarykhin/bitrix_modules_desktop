<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?

if (!empty($arParams['GROUP_ID'])) {
    $userID = $USER->GetID();
    $arUserSubNewsDirect = GetArrUserSubNewsDirect($userID);
    $convSubUserVal = ConvSubUserVal();

    if ($_REQUEST['SUB'] == 'ADD') {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        if (count($arUserSubNewsDirect) <= NEWS_LIMIT_SUB) {
            $key_del = array_search($arParams['GROUP_ID'], $convSubUserVal);
            if(count($arUserSubNewsDirect) > 0){
                $arValue = array_merge($arUserSubNewsDirect, array($key_del));
            }else{
                $arValue = array($key_del);
            }
            $arValue = array_unique($arValue);

            $user = new CUser;
            $fields = Array(
                "UF_SUB_NEWS_DIRECT" => $arValue
            );

            $user->Update($userID, $fields);
            $strError .= $user->LAST_ERROR;

            if (empty($strError)) {
                $resul_ajax['STATUS'] = 'ADD';
                $resul_ajax['TEXT_BUT'] = GetMessage('AJAX_ADD_BUT');
                $resul_ajax['TEXT_POPUP'] = GetMessage('AJAX_ADD_POPUP');
                $resul_ajax['HREF'] = $APPLICATION->GetCurPageParam("SUB=DEL", array('SUB'));

            }
        } else {
            $resul_ajax['TEXT_BUT'] = GetMessage('AJAX_MAX_COUNT_BUT');
            $resul_ajax['TEXT_POPUP'] = GetMessage('AJAX_MAX_COUNT_TEXT');

        }
        echo \Bitrix\Main\Web\Json::encode($resul_ajax, $options = null);

        die();
    }


    if ($_REQUEST['SUB'] == 'DEL') {

        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        //получили список свойст в id у пользователя
        foreach ($arUserSubNewsDirect as $idUserSubNewsDirect) {
            $arValue[$idUserSubNewsDirect] = $convSubUserVal[$idUserSubNewsDirect];
        }
        
        if (in_array($arParams['GROUP_ID'], $arValue)) {
            $key_del = array_search($arParams['GROUP_ID'], $arValue);
            unset($arValue[$key_del]);
        }

        $user = new CUser;
        $fields = Array(
            "UF_SUB_NEWS_DIRECT" => array_flip($arValue)
        );
        $user->Update($userID, $fields);
        $strError .= $user->LAST_ERROR;
        if (empty($strError)) {
            $resul_ajax['STATUS'] = 'DEL';
            $resul_ajax['TEXT_BUT'] = GetMessage('AJAX_DEL_BUT');
            $resul_ajax['TEXT_POPUP'] = GetMessage('AJAX_DEL_POPUP');

            $resul_ajax['HREF'] = $APPLICATION->GetCurPageParam("SUB=ADD", array("SUB"));
        }

        echo \Bitrix\Main\Web\Json::encode($resul_ajax, $options = null);

        die();
    }
}
?>
<? if ($arParams["ZAGLAV"] != "") {
    foreach ($arUserSubNewsDirect as $idUserSubNewsDirect) {
        $arValueCheck[$idUserSubNewsDirect] = $convSubUserVal[$idUserSubNewsDirect];
    }

    if (in_array($arParams['GROUP_ID'], $arValueCheck)) {
        ?>
        <div style="width:95%;padding-top: 30px;padding-bottom: 30px;border-top: 1px solid silver;margin: 0 auto">
            <span style="height: 40px;" class="webform-small-button webform-small-button-blue bx24-top-toolbar-add">
            <a class="webform-small-button-text" style="line-height: 40px;"
               id="news_podpis_button_<?= $arParams['GROUP_ID']; ?>"
               href="<?= $APPLICATION->GetCurPageParam("SUB=DEL", array('SUB')) ?>"><?= GetMessage('AJAX_ADD_BUT'); ?></a>
            </span>
        </div>
    <? } else {
        ?>
        <div style="width:95%;padding-top: 30px;padding-bottom: 30px;border-top: 1px solid silver;margin: 0 auto">
            <span style="height: 40px;" class="webform-small-button webform-small-button-blue bx24-top-toolbar-add">
            <a class="webform-small-button-text" style="line-height: 40px;"
               id="news_podpis_button_<?= $arParams['GROUP_ID']; ?>"
               href="<?= $APPLICATION->GetCurPageParam("SUB=ADD", array('SUB')) ?>"><?= GetMessage('AJAX_DEL_BUT'); ?></a>
            </span>
        </div>
    <? }
}; ?>