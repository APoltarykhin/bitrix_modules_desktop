<?php

namespace Firstbit\Desktop\Helper;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use CIBlock;

class IblockHelper
{
    public static function getIblockIdByCode($iblockCode, $iblockType = 'lists')
    {
        $iblockId = false;

        $iblocks = CIBlock::GetList([], ['CODE' => $iblockCode, 'TYPE' => $iblockType]);
        if ($iblock = $iblocks->Fetch())
        {
            $iblockId = $iblock['ID'];
        }

        return $iblockId;
    }
}
