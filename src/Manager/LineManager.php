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
 * Date: 16/12/2020
 * Time: 10:11
 */
namespace App\Manager;

use App\Items\ClassDetail;
use App\Items\Line;

/**
 * Class LineManager
 * @package App\Manager
 * @author Craig Rayner <craig@craigrayner.com>
 */
class LineManager extends TimetableManager
{
    /**
     * validateClassDetails
     * 4/01/2021 13:14
     * @param Line $line
     * @param int $count
     * @return bool
     */
    public function validateClassDetails(Line $line, int $count): bool
    {
        if ($line->getClassCount() === $count) return false;

        if ($count > $line->getClassCount()) {
            for ($i=$line->getClassCount()+1; $i<=$count; $i++) {
                $class = new ClassDetail();
                $class->setName($line->getName() . str_pad($i, 2, '0', STR_PAD_LEFT));
                $this->getDataManager()->addClass($class);
                $line->addClass($class);
            }
        }

        while ($count < $line->getClassCount()) {
            $item = $line->getClasses()->last();
            $line->getClasses()->removeElement($item);
        }
        return true;
    }

    /**
     * getCapacityWarning
     * 4/01/2021 13:19
     * @param Line $line
     * @return array
     */
    public function getCapacityWarning(Line $line): array
    {
        $total = 0;
        foreach ($line->getGrades() as $grade)
            $total += $grade->getStudentCount();

        if ($total === 0) return ['message' => 'no_test'];

        foreach ($line->getClasses() as $class) {
            $total -= $class->getCapacity();
        }

        if ($total > 0) return ['message' => 'under', 'count' => $total];

        if ($total < 0) return ['message' => 'over', 'count' => abs($total)];

        return ['message' => 'correct'];
    }
}
