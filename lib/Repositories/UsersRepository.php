<?php

namespace Firstbit\Desktop\Repositories;

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Bitrix\Socialnetwork\UserToGroupTable;
use Carbon\Carbon;
use CFile;
use CIBlock;
use CIBlockElement;
use CIntranetUtils;

class UsersRepository
{
    protected $detail_url;
    protected $honourIblockID;
    protected $userID;

    public function __construct (int $userID)
    {
        $this->detail_url = '/company/personal/user/#USER_ID#/';

        $rsIBlock = CIBlock::GetList([], ['CODE' => 'honour', 'TYPE' => 'structure']);
        $this->honourIblockID = $rsIBlock->GetNext()['ID'];
        $this->userOffice = UserTable::GetList([
            'filter' => ['ID' => $userID],
            'select' => ['UF_DEPARTMENT'],
        ])->fetch()['UF_DEPARTMENT'][0];
        $this->userID = $userID;
    }



        public function getBasicBirthdayListUsers(): array
    {
            $today = new DateTime();
            $todayFormatted = $today->format('m-d');

            $filter = ['=PERSONAL_BIRTHDAY', $todayFormatted, '=ACTIVE', 'Y'];

            $rsUsers = \CUser::GetList(($by = 'id'), ($order = 'asc'), $filter);

            $userList = [];
            while ($arUser = $rsUsers->fetch()) {
                    $arUser['DETAIL_URL'] = str_replace('#USER_ID#', $arUser['ID'], $this->detail_url);
                $userList[] = $arUser;
            }

            return $userList;
    }

    public function getUserFreandly ($userID)
    {
        $rsUsers = UserTable::GetList([
            'filter' => [
                'ID' => $userID,
            ],
            'select' => ['ID', 'UF_USER_FRIEND_ID'],
        ]);
        $currentUser = $rsUsers->fetch();
        $freendIDs = $currentUser['UF_USER_FRIEND_ID'];

        return $freendIDs;
    }

    /**
     * @param $date_start
     * @param $date_end
     * @param bool $wAnnisersary add filter DATE_REGISTER
     * @param int $limit
     * @return array $userList
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getUserListByDateBirthday ($date_start, $date_end, bool $wAnnisersary = false, int $limit = 0): array
    {
        $datetime1 = date_create($date_start);
        $datetime2 = date_create($date_end);
        $dateDiff = date_diff($datetime1, $datetime2);



        $dateTimeObjects = [];
        $dateTimeRegistrations = [];
        for ($i = 0; $i <= $dateDiff->d; $i++) {
            $tmpDate = date('m-d', strtotime($date_start . '+' . ($i) . ' days')); //теперь в tmpDate текущая дата +$i дней
            for ($y = 1940; $y <= date('Y'); $y++) {
                $date = new DateTime($y . '-' . $tmpDate, 'Y-m-d');
                $dateTimeObjects[] = $date;
                $dateTimeRegistrations[] = [
                    '>=DATE_REGISTER' => new DateTime($y . '-' . $tmpDate . ' 00:00:00', 'Y-m-d H:i:s'),
                    '<=DATE_REGISTER' => new DateTime($y . '-' . $tmpDate . ' 23:59:59', 'Y-m-d H:i:s'),
                ];
            }
        }



        $idsUsers = array_unique(
            array_merge(
                $this->getUserHonours($this->userID),
                $this->getSubordinateUsers($this->userID),
            )
        );

        $filter = [
            'LOGIC' => 'OR',
            [
                'PERSONAL_BIRTHDAY' => $dateTimeObjects,
            ],
        ];

        $filter = [
            'ACTIVE'            => 'Y',
            // '!EXTERNAL_AUTH_ID' => ['replica', 'email', 'bot', 'imconnector'],
            [
                'LOGIC' => 'OR',
                ['ID' => $idsUsers],
                ['UF_DEPARTMENT' => $this->userOffice],
            ],
            [$filter]
        ];

        $rsUsers = UserTable::getList([
            'filter' => $filter,
            'select' => ['ID', 'NAME', 'LAST_NAME', 'PERSONAL_BIRTHDAY', 'PERSONAL_PHOTO', 'DATE_REGISTER'],
            'limit' => $limit,
            'order' => ['PERSONAL_BIRTHDAY' => 'ASC']
        ]);



        $userList = [];
        while ($arUser = $rsUsers->fetch()) {
            $arUser['DETAIL_URL'] = str_replace([
                '#ID#',
                '#USER_ID#',
            ], $arUser['ID'], $this->detail_url);
            $userList[] = $arUser;
        }



        if ($wAnnisersary) {
            $filter = array_merge(['LOGIC' => 'OR'], $dateTimeRegistrations);

            $filter = [
                'ACTIVE'            => 'Y',
                '!EXTERNAL_AUTH_ID' => ['replica', 'email', 'bot', 'imconnector'],
                [
                    'LOGIC' => 'OR',
                    'ID' => $idsUsers,
                    'UF_DEPARTMENT' => $this->userOffice,
                ],
                $filter
            ];

            $rsUsers = UserTable::getList([
                'filter' => $filter,
                'select' => ['ID', 'NAME', 'LAST_NAME', 'PERSONAL_BIRTHDAY', 'PERSONAL_PHOTO', 'DATE_REGISTER'],
                'limit' => $limit,
                'order' => ['DATE_REGISTER' => 'ASC']
            ]);
            while ($arUser = $rsUsers->fetch()) {
                $arUser['DETAIL_URL'] = str_replace([
                    '#ID#',
                    '#USER_ID#',
                ], $arUser['ID'], $this->detail_url);
                if($arUser['DATE_REGISTER']->format('d.m.Y') != date('d.m.Y')) {
                    $arUser['ANNIVERSARY'] = true;
                    $userList[] = $arUser;
                }
            }
        }

        return (is_null($userList)) ? [] : $this->getPersonalPhoto($userList);
    }

    public function getAllUserListByDateBirthday ($date_start, $date_end, $cnt = false)
    {
        $datetime1 = date_create($date_start);
        $datetime2 = date_create($date_end);
        $dateDiff = date_diff($datetime1, $datetime2);

        $dates = [];
        for ($i = 0; $i <= $dateDiff->d; $i++) {
            $tmpDate = date('m-d', strtotime($date_start . '+' . ($i) . ' days')); //теперь в tmpDate текущая дата +$i дней
            for ($y = 1950; $y <= date('Y'); $y++) {
                $date = new DateTime($y . '-' . $tmpDate, 'Y-m-d');
                $dates[] = $date;
            }
        }

        $rsUsers = UserTable::getList([
            'filter'      => [
                'ACTIVE'            => 'Y',
                '!EXTERNAL_AUTH_ID' => ['replica', 'email', 'bot', 'imconnector'],
                'PERSONAL_BIRTHDAY' => $dates,
            ],
            'select'      => ['ID', 'NAME', 'LAST_NAME', 'PERSONAL_BIRTHDAY', 'PERSONAL_PHOTO'],
            'count_total' => 'Y',
        ]);

        if ($cnt) {
            return $rsUsers->getCount();
        }

        while ($arUser = $rsUsers->fetch()) {
            $userList[$arUser['ID']] = $arUser;
            $userList[$arUser['ID']]['DETAIL_URL'] = str_replace([
                '#ID#',
                '#USER_ID#',
            ], $arUser['ID'], $this->detail_url);
        }

        return (is_null($userList)) ?: $this->getPersonalPhoto($userList);
    }

    public function getGroupBirthdayList ($dateStart, $dateEnd, $groupId)
    {
        $apiCode = 'birthdayGroup' . $groupId;

        $datetime1 = date_create($dateStart);
        $datetime2 = date_create($dateEnd);
        $dateDiff = date_diff($datetime1, $datetime2);

        $dateTimeObjects = [];
        $dateTimeRegistrations = [];

        for ($i = 0; $i <= $dateDiff->d; $i++) {
            $dates = [];

            $tmpDate = date('m-d', strtotime($dateStart . '+' . ($i) . ' days')); //теперь в tmpDate текущая дата +$i дней
            for ($y = 1950; $y <= date('Y'); $y++) {
                $dates[] = $y . '-' . $tmpDate;
                $dateTimeObjects[] = new DateTime($y . '-' . $tmpDate, 'Y-m-d');

                $dateTimeRegistrations[] = [
                    '>=DATE_REGISTER' => new DateTime($y . '-' . $tmpDate . ' 00:00:00', 'Y-m-d H:i:s'),
                    '<=DATE_REGISTER' => new DateTime($y . '-' . $tmpDate . ' 23:59:59', 'Y-m-d H:i:s'),
                ];
            }
        }

        $anniversary = [];
        $iblock = IblockTable::compileEntity($apiCode);
        if ($iblock) {
            $iblock = $iblock->getDataClass();

            $anniversaryCollection = $iblock::query()
                ->whereIn('DATA_EMPLOYMENT.VALUE', $dates)
                ->addSelect('USER.VALUE')
                ->fetchCollection();

            foreach ($anniversaryCollection as $item) {
                $anniversary[] = $item->getUser()->getValue();
            }
        }

        global $USER;

        $userIds = UserToGroupTable::query()
            ->where('GROUP_ID', '=', $groupId)
            ->whereNot('USER_ID', '=', $USER->GetID())
            ->addSelect('USER_ID')
            ->exec()
            ->fetchCollection()
            ->getUserIdList();

        $filter = [
            'LOGIC' => 'OR',
            [
                'PERSONAL_BIRTHDAY' => $dateTimeObjects,
            ],
            [
                'ID' => $anniversary,
            ],
        ];

        $filter = array_merge($filter, $dateTimeRegistrations);

        $rsUsers = UserTable::getList([
            'filter' => [
                'ACTIVE'            => 'Y',
                '!EXTERNAL_AUTH_ID' => [
                    'replica',
                    'email',
                    'bot',
                    'imconnector',
                ],
                'ID' => $userIds,
                [$filter],

            ],
            'select' => [
                'ID',
                'NAME',
                'LAST_NAME',
                'PERSONAL_BIRTHDAY',
                'PERSONAL_PHOTO',
                'DATE_REGISTER',
            ],
        ]);

        $now = new DateTime();

        while ($arUser = $rsUsers->fetch()) {
            $arUser['DETAIL_URL'] = str_replace([
                '#ID#',
                '#USER_ID#',
            ], $arUser['ID'], $this->detail_url);

            $isAnniversary = false;
            if (
                in_array($arUser['ID'], $anniversary)
                || $now->format('d.m') == $arUser['DATE_REGISTER']->format('d.m')
                || (date('m-d', strtotime($dateEnd)) >= $arUser['DATE_REGISTER']->format('m-d')
                    && date('m-d', strtotime($dateStart)) <= $arUser['DATE_REGISTER']->format('m-d'))
            ) {
                $isAnniversary = true;
            }

            $arUser['ANNIVERSARY'] = $isAnniversary;

            if ($arUser['PERSONAL_PHOTO']) {
                $arUser['PERSONAL_PHOTO'] = CFile::ResizeImageGet(
                    $arUser['PERSONAL_PHOTO'],
                    ['width' => 50, 'height' => 50],
                    BX_RESIZE_IMAGE_EXACT
                );
            }

            $userList[] = $arUser;
        }

        usort($userList, function ($a, $b)
        {
            $aBirthday = new \Bitrix\Main\Type\DateTime($a['PERSONAL_BIRTHDAY']);
            $bBirthday = new \Bitrix\Main\Type\DateTime($b['PERSONAL_BIRTHDAY']);
            $aRegister = new \Bitrix\Main\Type\DateTime($a['DATE_REGISTER']);
            $bRegister = new \Bitrix\Main\Type\DateTime($b['DATE_REGISTER']);

            if ($a['ANNIVERSARY'] && $b['ANNIVERSARY']) {
                return $aRegister->format('m-d') > $bRegister->format('m-d');
            }
            if ($a['ANNIVERSARY']) {
                return $aRegister->format('m-d') > $bBirthday->format('m-d');
            }
            if ($b['ANNIVERSARY']) {
                return $aBirthday->format('m-d') > $bRegister->format('m-d');
            }
            return $aBirthday->format('m-d') > $bBirthday->format('m-d');
        });

        return $userList;
    }

    private function getPersonalPhoto (array $userList)
    {
        foreach ($userList as &$arUser) {
            if ($arUser['PERSONAL_PHOTO']) {
                $imageFile = CFile::GetFileArray($arUser['PERSONAL_PHOTO']);
                if ($imageFile !== false) {
                    $arUser['PERSONAL_PHOTO'] = CFile::ResizeImageGet(
                        $imageFile,
                        ['width' => 100, 'height' => 100],
                        BX_RESIZE_IMAGE_EXACT,
                        true
                    );
                } else {
                    $arUser['PERSONAL_PHOTO'] = false;
                }
            }
        }
        unset($arUser);

        return $userList;
    }

    //получаем список ИД пользователей которым отправлял благодарности и которые благодарили меня
    private function getUserHonours (int $userID)
    {
        $arHonours = [];
        $rsHonours = CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => $this->honourIblockID,
                'ACTIVE'    => 'Y',
                [
                    'LOGIC'          => 'OR',
                    'CREATED_BY'     => $userID,
                    'PROPERTY_USERS' => $userID,
                ],
            ],
            false, false,
            [
                'CREATED_BY',
                'PROPERTY_USERS',
            ]
        );
        while ($arHonour = $rsHonours->GetNext()) {
            if ($arHonour['CREATED_BY'] != $userID) {
                $arHonours[] = $arHonour['CREATED_BY'];
            } else {
                $arHonours[] = $arHonour['PROPERTY_USERS_VALUE'];
            }
        }

        return $arHonours;
    }

    //получаем список ИД пользователей у которых я руководитель
    private function getSubordinateUsers (int $userID)
    {
        $departments = [];
        $dbRes = CIntranetUtils::GetSubordinateDepartmentsList($userID);
        while ($arRes = $dbRes->GetNext()) {
            $departments[] = $arRes['ID'];
        }

        $subordinate_users = [];
        $dbUsers = UserTable::GetList([
            'filter' => ["!ID" => $userID, 'UF_DEPARTMENT' => $departments, 'ACTIVE' => 'Y'],
            'select' => ["ID", "NAME", "LAST_NAME", "SECOND_NAME", "LOGIN", "WORK_POSITION"],
        ]);
        while ($arRes = $dbUsers->fetch()) {
            $subordinate_users[] = $arRes["ID"];
        }

        return $subordinate_users;
    }
}
