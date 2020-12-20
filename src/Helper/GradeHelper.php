<?php
/**
 * Created by PhpStorm.
 *
 * Timetable Creator
 * (c) 2020-2020 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Licence: MIT
 * User: Craig Rayner
 * Date: 17/12/2020
 * Time: 15:05
 */
namespace App\Helper;

use App\Items\Grade;
use App\Manager\TimetableDataManager;

/**
 * Class GradeHelper
 * @package App\Helper
 * @author Craig Rayner <craig@craigrayner.com>
 */
class GradeHelper
{
    /**
     * @var TimetableDataManager
     */
    private static TimetableDataManager $manager;

    /**
     * find
     * 17/12/2020 15:07
     * @param string|null $id
     * @return Grade|null
     */
    public static function find(?string $id): ?Grade
    {
        if (is_null($id)) return null;

        $items = self::$manager->getGrades()->filter(function(Grade $grade) use ($id) {
            if ($id === $grade->getId()) return $grade;
        });
        if ($items->count() > 1) throw new \OutOfBoundsException('The grade provided is not unique!');

        return $items->count() === 1 ? $items->first() : null;
    }
}
