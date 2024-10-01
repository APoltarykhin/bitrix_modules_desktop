<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Entity\Base;
use Bitrix\Socialnetwork\WorkgroupTable;
use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\Application;


Loc::loadMessages(__FILE__);

$moduleId = 'firstbit.desktop';
$request = Bitrix\Main\Context::getCurrent()->getRequest();

if ($request->isPost() && check_bitrix_sessid()) {
    $hideBirthdayWidget = $request->getPost('HIDE_BIRTHDAY_WIDGET') === 'Y' ? 'Y' : 'N';
    Option::set($moduleId, 'HIDE_BIRTHDAY_WIDGET', $hideBirthdayWidget);

    $selectedGroupId = (int)$request->getPost('SELECTED_GROUP');
    Option::set($moduleId, 'SELECTED_GROUP', $selectedGroupId);

    // Новые переменные для второго чекбокса и выпадающего списка
    $newCheckboxValue = $request->getPost('HIDE_BIRTHDAY_WIDGET_TWO') === 'Y' ? 'Y' : 'N';
    Option::set($moduleId, 'HIDE_BIRTHDAY_WIDGET_TWO', $newCheckboxValue);

    $selectedSecondGroupId = (int)$request->getPost('SELECTED_GROUP_TWO');
    Option::set($moduleId, 'SELECTED_GROUP_TWO', $selectedSecondGroupId);

    // Новые переменные для третьего чекбокса и выпадающего списка
    $newCheckboxValue = $request->getPost('HIDE_BIRTHDAY_WIDGET_REAL') === 'Y' ? 'Y' : 'N';
    Option::set($moduleId, 'HIDE_BIRTHDAY_WIDGET_REAL', $newCheckboxValue);

    // $selectedSecondGroupId = (int)$request->getPost('SELECTED_GROUP_REAL');
    // Option::set($moduleId, 'SELECTED_GROUP_REAL', $selectedSecondGroupId);

    $widgetTitle = $request->getPost('WIDGET_TITLE_1');
    Option::set($moduleId, 'WIDGET_TITLE_1', $widgetTitle);

    $widgetTitleTwo = $request->getPost('WIDGET_TITLE_2');
    Option::set($moduleId, 'WIDGET_TITLE_2', $widgetTitleTwo);


    if (!empty($_FILES['NEWS_IMAGE']['tmp_name'])) {
        $uploadDir = '/upload/';
        $uploadFile = $_SERVER['DOCUMENT_ROOT'] . $uploadDir . basename($_FILES['NEWS_IMAGE']['name']);



        if (move_uploaded_file($_FILES['NEWS_IMAGE']['tmp_name'], $uploadFile)) {
            $imagePath = $uploadDir . $_FILES['NEWS_IMAGE']['name'];
            Option::set($moduleId, 'NEWS_IMAGE', $imagePath);
        }
    }

}


$connection = Application::getConnection();
$sqlHelper = $connection->getSqlHelper();

$groups = [];
$query = $connection->query("SELECT ID, NAME FROM b_sonet_group WHERE ACTIVE = 'Y'");
while ($group = $query->fetch()) {
    $groups[] = $group;
}

$selectedGroup = Option::get($moduleId, 'SELECTED_GROUP', 0);
$hideBirthdayWidget = Option::get($moduleId, 'HIDE_BIRTHDAY_WIDGET', 'N');

$newCheckboxValue = Option::get($moduleId, 'HIDE_BIRTHDAY_WIDGET_TWO', 'N');
$selectedSecondGroup = Option::get($moduleId, 'SELECTED_GROUP_TWO', 0);

// $selectedGroupReal = Option::get($moduleId, 'SELECTED_GROUP_REAL', 0);
$hideBirthdayWidgetReal = Option::get($moduleId, 'HIDE_BIRTHDAY_WIDGET_REAL', 'N');

$newsImage = Option::get($moduleId, 'NEWS_IMAGE', '');

$tabControl = new CAdminTabControl('settings_tab', [
    ['DIV' => 'edit', 'TAB' => 'Основные', 'TITLE' => 'Настройки модуля Рабочий стол'],
]);

$tabControl->begin();
?>
<form method="post" action="<?= $request->getRequestUri() ?>" enctype="multipart/form-data">
    <?= bitrix_sessid_post(); ?>
    <?php $tabControl->beginNextTab(); ?>
    <tr>
        <td width="40%">Показывать виджет "Дни рождения"</td>
        <td width="60%">
            <input type="checkbox" name="HIDE_BIRTHDAY_WIDGET_REAL" value="Y" <?= $hideBirthdayWidgetReal === 'Y' ? 'checked' : '' ?>>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr></td>
    </tr>
    <tr>
        <td width="40%">Показывать виджет "Мероприятия":</td>
        <td width="60%">
            <input type="checkbox" name="HIDE_BIRTHDAY_WIDGET" value="Y" <?= $hideBirthdayWidget === 'Y' ? 'checked' : '' ?>>
        </td>
    </tr>
    <tr>
        <td>Группа для отображения в виджете:</td>
        <td>
            <select name="SELECTED_GROUP">
                <option value="0">Не выбрано</option>
                <? foreach($groups as $group): ?>
                    <option value="<?= $group['ID'] ?>" <?= $group['ID'] == $selectedGroup ? 'selected' : '' ?>>
                        <?= $group['NAME'] ?>
                    </option>
                <? endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>Название виджета:</td>
        <td>
            <input type="text" name="WIDGET_TITLE_1" value="<?= Option::get($moduleId, 'WIDGET_TITLE_1', 'Мероприятия') ?>">
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr></td>
    </tr>
    <tr>
        <td width="40%">Показывать виджет "Мероприятия":</td>
        <td width="60%">
            <input type="checkbox" name="HIDE_BIRTHDAY_WIDGET_TWO" value="Y" <?= $newCheckboxValue === 'Y' ? 'checked' : '' ?>>
        </td>
    </tr>
    <tr>
        <td>Группа для отображения в виджете:</td>
        <td>
            <select name="SELECTED_GROUP_TWO">
                <option value="0">Не выбрано</option>
                <? foreach($groups as $group): ?>
                    <option value="<?= $group['ID'] ?>" <?= $group['ID'] == $selectedSecondGroup ? 'selected' : '' ?>>
                        <?= $group['NAME'] ?>
                    </option>
                <? endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>Название виджета:</td>
        <td>
            <input type="text" name="WIDGET_TITLE_2" value="<?= Option::get($moduleId, 'WIDGET_TITLE_2', 'Мероприятия') ?>">
        </td>
    </tr>
    <td colspan="2"><hr></td>
    <tr>
        <td>Логотип новостей по умолчанию (.jpg, .png):</td>
        <td>
            <input type="file" name="NEWS_IMAGE" accept="upload/*">
        </td>
    </tr>
    <tr>
        <td>Просмотр логотипа новостей:</td>
        <td>
            <img src="<?= $newsImage ?>" style="max-width: 200px;">
        </td>
    </tr>

    <?php $tabControl->buttons(); ?>
    <input type="submit" name="Update" value="<?= Loc::getMessage('MAIN_SAVE') ?>" title="<?= Loc::getMessage("MAIN_OPT_SAVE_TITLE") ?>">
</form>

<?php $tabControl->end();
