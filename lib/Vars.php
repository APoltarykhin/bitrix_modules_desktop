<?php

namespace Firstbit\Desktop;

use Bitrix\Main\Localization\Loc;
Loc::loadLanguageFile(__FILE__);

class Vars
{
	const MODULE_ID = 'firstbit.eo';
	const SW_IBLOCK_TYPE = 'services';
	const SW_IBLOCK_CODE = 'sw';
	const SW_IBLOCK_API_CODE = 'serviceWindow';

	const TASK_GRID_ID = 'my-appeal-tasks';
	const TASK_FILTER_ID = 'my-appeal-tasks';
	const TASK_IS_MY_APPEAL_FIELD = 'UF_IS_MY_APPEAL';
	const OPTIONS = [
		'CHECKBOX' => [
			'search' => 'Y',
			'categories' => 'Y'
		]
	];
	const PROPERTY = [
		'link' => 'link',
		'detail' => 'detail',
		'slider' => 'slider'
	];

	const TASKS_FIELDS = [
		'UF_IS_MY_APPEAL' => 'UF_IS_MY_APPEAL'
	];

	const IBLOCK_FIELDS = [
		'UF_COLOR' => 'UF_COLOR'
	];

	public static function getNameTags()
	{
		return Loc::getMessage('SB_SW.TASKS_TAGS');
	}

	public static function notFoundModule(string $text)
	{
		return Loc::getMessage('SB_SW.MODULE_NOT_FOUND', ["#MODULE#" => $text]);
	}

	public static function getDemoText()
	{
		return Loc::getMessage('SB_SW.MODULES_DEMO');
	}

	public static function getExpiredText()
	{
		return Loc::getMessage('SB_SW.MODULES_DEMO');
	}

	public static function getServicesTitle()
	{
		return Loc::getMessage('SB_SW.MENU_SERVICES');
	}

	public static function getPage()
	{
		return Loc::getMessage('SB_SW.PAGE');
	}

	public static function getAlert()
	{
		return Loc::getMessage('SB_SW.ALERT');
	}

	public static function getTags()
	{
		return Loc::getMessage('SB_SW.NEWS_TAGS_FILTER');
	}

	public static function getSection()
	{
		return Loc::getMessage('SB_SW.NEWS_SECTION_FILTER');
	}
	public static function getMenuLinks ()
	{

		return [
			[
				Loc::getMessage('Рабочий стол'),
				"/desktop/index.php",
				[],
				[],
				"",
			],
		];
	}
}
