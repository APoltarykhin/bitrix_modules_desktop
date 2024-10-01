<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;
use Bitrix\Main\SiteTable;
use Bitrix\Sale\Internals\PaySystemServiceTable;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Loader;
use CUserOptions;

IncludeModuleLangFile(__FILE__);

class firstbit_desktop extends CModule
{
        const MODULE_ID = 'firstbit.desktop';
        var $MODULE_ID = 'firstbit.desktop';
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;
        var $MODULE_CSS;
        var $strError = '';

        public function __construct ()
    {
        $arModuleVersion = [];
        include(dirname(__FILE__) . '/version.php');
        $this->MODULE_ID = 'firstbit.desktop';
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('FIRSTBIT_DESKTOP_MODULE.NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('FIRSTBIT_DESKTOP.MODULE_DESC');
        $this->PARTNER_NAME = Loc::getMessage('FIRSTBIT_DESKTOP.PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('FIRSTBIT_DESKTOP.PARTNER_URI');
    }

    public function DoInstall ()
    {
        ModuleManager::registerModule($this->MODULE_ID);

        Loader::includeModule('iblock');

        CModule::IncludeModule('fileman');

        // $res = $iblockType->Add($arFields);
        $dbIblock = CIBlock::GetList([], ['CODE' => 'DESKTOP_TITLE']);

        if ($dbIblock->SelectedRowsCount() == 0) {
            $iblockFields = [
                'ACTIVE'         => 'Y',
                'NAME'           => 'Заголовок Рабочего стола',
                'CODE'           => 'DESKTOP_TITLE',
                'API_CODE'       => 'desktopTitle',
                'IBLOCK_TYPE_ID' => 'lists',
                'SITE_ID'        => 's1'
            ];
            $firstIblock = new CIBlock;
            $firstIblockId = $firstIblock->Add($iblockFields);

            if ($firstIblockId) {
                $propertiesFirst = [
                    'TITLE_TYPE' => [
                        'NAME'          => 'Тип условия',
                        'PROPERTY_TYPE' => 'L',
                        'LIST_TYPE'     => 'C',
                        'VALUES'        => [
                            ['VALUE' => 'Рандомный заголовок', 'DEF' => 'N', 'SORT' => '100'],
                            ['VALUE' => 'При первом заходе', 'DEF' => 'N', 'SORT' => '200'],
                            ['VALUE' => 'При повторном заходе', 'DEF' => 'N', 'SORT' => '200'],
                            ['VALUE' => 'По условию', 'DEF' => 'N', 'SORT' => '200']
                        ]
                    ],
                    'DATE_START' => [
                        'NAME' => 'Условие по дате от',
                        'PROPERTY_TYPE' => 'S',
                        'USER_TYPE' => 'DateTime',
                    ],
                    'DATE_END' => [
                        'NAME' => 'Условие по дате до',
                        'PROPERTY_TYPE' => 'S',
                        'USER_TYPE' => 'DateTime',
                    ],
                    'TIME_START' => [
                        'NAME'          => 'Условие по времени от (ч)',
                        'PROPERTY_TYPE' => 'N'
                    ],
                    'TIME_END'   => [
                        'NAME'          => 'Условие по времени до (ч)',
                        'PROPERTY_TYPE' => 'N'
                    ]
                ];

                foreach ($propertiesFirst as $code => $property) {
                    $ibp = new CIBlockProperty;
                    $ibp->Add(array_merge($property, ['IBLOCK_ID' => $firstIblockId, 'CODE' => $code]));
                }
            }
        }

        $dbTwoIblock = CIBlock::GetList([], ['CODE' => 'SB_POPULAR_SERVICE']);

        if ($dbTwoIblock->SelectedRowsCount() == 0)
        {
            $iblockFieldsTwo = [
                'ACTIVE'         => 'Y',
                'NAME'           => 'Популярные сервисы',
                'CODE'           => 'SB_POPULAR_SERVICE',
                'API_CODE'       => 'sbPopularService',
                'IBLOCK_TYPE_ID' => 'lists',
                'SITE_ID'        => 's1'
            ];

            $twoIblock = new CIBlock;
            $twoIblockId = $twoIblock->Add($iblockFieldsTwo);

            if ($twoIblockId) {
                $propertiesTwo = [
                    'SB_LINK'     => [
                        'NAME'          => 'Ссылка',
                        'PROPERTY_TYPE' => 'S'
                    ],
                    'SB_COLOR_BG' => [
                        'NAME'          => 'Цвет заливки',
                        'PROPERTY_TYPE' => 'S'
                    ],
                    'SB_TAG'      => [
                        'NAME'          => 'Название тега',
                        'PROPERTY_TYPE' => 'S'
                    ],
                    'POPUP'       => [
                        'NAME'          => 'Открыть в окне',
                        'PROPERTY_TYPE' => 'L',
                        'LIST_TYPE'     => 'C',
                        'VALUES'        => [
                            ['VALUE' => 'Да', 'DEF' => 'N', 'SORT' => '100'],
                            ['VALUE' => 'Нет', 'DEF' => 'N', 'SORT' => '500']
                        ]
                    ],
                ];

                foreach ($propertiesTwo as $code => $propertyTwo) {
                    $ibpTwo = new CIBlockProperty;
                    $ibpTwo->Add(array_merge($propertyTwo, ['IBLOCK_ID' => $twoIblockId, 'CODE' => $code]));
                }
            }
        }

        \Bitrix\Main\IO\Directory::createDirectory(\Bitrix\Main\Application::getDocumentRoot() . '/local/components', 0755, true, true);

        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/firstbit.desktop/install/components",
            $_SERVER["DOCUMENT_ROOT"] . "/local/components/",
            true,
            true
        );

        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/firstbit.desktop/install/templates/.default/components/bitrix/news.list",
            $_SERVER["DOCUMENT_ROOT"] . "/local/templates/.default/components/bitrix/news.list/",
            true,
            true
        );

        \Bitrix\Main\IO\Directory::createDirectory(\Bitrix\Main\Application::getDocumentRoot() . '/desktop', 0755, true, true);

        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/firstbit.desktop/install/public/desktop",
            $_SERVER["DOCUMENT_ROOT"] . "/desktop",
            true,
            true
        );

        //Добавление пункта меню

        CModule::IncludeModule('fileman');
        $menuPath = '/.top.menu.php';
        $res = CFileMan::GetMenuArray($_SERVER['DOCUMENT_ROOT'] . $menuPath);
        $newMenuItem = [
            'Рабочий стол',
            '/desktop/',
            [],
            [],
            ''
        ];
        array_splice($res['aMenuLinks'], 0, 0, [$newMenuItem]); // Изменили индекс на 0
        CFileMan::SaveMenu(['s1', $menuPath], $res['aMenuLinks'], $res['sMenuTemplate']);
    }

    public function DoUninstall()
    {
        Loader::includeModule('iblock');

        $firstIblockCode = 'DESKTOP_TITLE';

        $iblock = IblockTable::getList(array(
            'filter' => array('=CODE' => $firstIblockCode),
            'select' => array('ID'),
            'limit' => 1,
        ))->fetch();

        $firstIblockId = $iblock['ID'];


        $twoIblockCode = 'SB_POPULAR_SERVICE';
        $iblockIdTwo = IblockTable::getList(array(
            'filter' => array('=CODE' => $twoIblockCode),
            'select' => array('ID'),
            'limit' => 1,
        ))->fetch();

        $twoIblockId = $iblockIdTwo ['ID'];

        if ($firstIblockId) {
            $iblock = new CIBlock;
            $iblock->Delete($firstIblockId);
        }

        if ($twoIblockId) {
            $iblockTwo = new CIBlock;
            $iblockTwo->Delete($twoIblockId);
        }

        DeleteDirFilesEx('/local/components/sb/desktop/');
        DeleteDirFilesEx('/local/templates/.default/components/bitrix/news.list/sb_news_list/');
        DeleteDirFilesEx('/desktop');

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}
