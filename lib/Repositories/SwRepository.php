<?php

namespace Firstbit\Desktop\Repositories;


use Bitrix\Iblock\IblockTable;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\UserTable;
use Bitrix\Tasks\Internals\Counter\CounterTable;
use Bitrix\Tasks\Internals\Task\TagTable;
use CFile;
use CTasks;
use SB\Site\Variables;

class SwRepository
{
    public $iblock;

    public function __construct()
    {
        $this->iblock = IblockTable::compileEntity('serviceWindow');
        if (!$this->iblock) {
            throw new \Error('iblock serviceWindow not found');
        }
        $this->iblock = $this->iblock->getDataClass();
    }

    public function getElement ($id)
    {
        $element = $this->iblock::query()
            ->where('ID', $id)
            ->addSelect('RESPONSIBLE')
            ->addSelect('DEADLINE')
            ->addSelect('DETAIL_TEXT')
            ->exec()
            ->fetchObject();

        if (!$element) {
            return [];
        }

        if ($element->get('DEADLINE')) {
            $deadline = $element->get('DEADLINE')->getValue();
        }

        if ($element->get('RESPONSIBLE')) {
            $responsibleId = $element->get('RESPONSIBLE')->getValue();
            $responsible = UserTable::query()
                ->where('ID', $responsibleId)
                ->addSelect('NAME')
                ->addSelect('LAST_NAME')
                ->addSelect('PERSONAL_PHOTO')
                ->exec()
                ->fetch();

            $responsiblePhoto = CFile::ResizeImageGet(
                $responsible['PERSONAL_PHOTO'],
                [
                    'width' => 30,
                    'height' => 30,
                ],
                BX_RESIZE_IMAGE_PROPORTIONAL,
                true
            )['src'];
            $responsibleName = $responsible['NAME'] . ' ' . $responsible['LAST_NAME'];
        }

        return [
            'RESPONSIBLE_ID' => $responsibleId,
            'RESPONSIBLE_PHOTO' => $responsiblePhoto,
            'RESPONSIBLE_FULL_NAME' => $responsibleName,
            'DEADLINE' => $deadline,
            'DESCRIPTION' => $element->get('DETAIL_TEXT')
        ];
    }

    public static function getCountTask ($userId)
    {
        $tagCollection = TagTable::query()
            ->where('NAME', 'EO')
            ->where('TASK.CREATED_BY', $userId)
            ->whereNot('TASK.ZOMBIE', 'Y')
            ->whereNot('TASK.STATUS', CTasks::STATE_COMPLETED)
            ->whereNot('TASK.STATUS', CTasks::STATE_DECLINED)
            ->addSelect('*')
            ->exec()
            ->fetchCollection();

        $swTaskCollection = $tagCollection->fillTask();

        print_r($swTaskCollection);
        die();
        //список id задач, которые ждут реакции
        $appelTaskIdList = [];
        $appelOpenTaskIdList = [];

        if (!empty($swTaskCollection->getIdList())) {
            $tasksScorerCollection = CounterTable::query()
                ->where('USER_ID', $userId)
                ->where(Query::filter()
                    ->logic('or')
                    ->where('TYPE', 'my_new_comments')
                    ->where('TYPE', 'originator_new_comments'))
                ->whereIn('TASK_ID', $swTaskCollection->getIdList())
                ->addSelect('*')
                ->exec()
                ->fetchCollection();

            //список id задач, в которых есть новые комментарии
            $notViewedCommentTaskIdList = $tasksScorerCollection->getTaskIdList();
            $appelTaskIdList = array_merge($appelTaskIdList, $notViewedCommentTaskIdList ?? []);

            //список id открытых задач
            $appelOpenTaskIdList = $swTaskCollection->getIdList();
        }

        foreach ($swTaskCollection as $taskObject) {
            //добавляем просроченные задачи
            if (!is_null($taskObject->getDeadline())) {
                $dateNow = new \Bitrix\Tasks\Util\Type\DateTime();
                if (
                    !is_null($taskObject->getClosedDate())
                    && $taskObject->getDeadline() < $taskObject->getClosedDate()
                    && $taskObject->getStatus() != CTasks::STATE_COMPLETED
                ) {
                    array_push($appelTaskIdList, $taskObject->getId());
                } else if (
                    $taskObject->getDeadline() < $dateNow
                    && $taskObject->getStatus() != CTasks::STATE_COMPLETED
                ) {
                    array_push($appelTaskIdList, $taskObject->getId());
                }
            }
        }
        $appelTaskIdList = array_unique($appelTaskIdList);

        return [
            'OPEN_TASKS' => count($appelOpenTaskIdList),
            'COMMENT_TASKS' => count($appelTaskIdList)
        ];
    }
}
