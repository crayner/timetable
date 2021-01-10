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
use App\Items\Grade;
use App\Items\Line;
use App\Provider\ProviderFactory;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class LineManager
 * @package App\Manager
 * @author Craig Rayner <craig@craigrayner.com>
 */
class LineManager extends TimetableManager
{
    /**
     * @var Line|null
     */
    private ?Line $line;

    /**
     * @var Grade|null
     */
    private ?Grade $grade;

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

    /**
     * getLine
     * 6/01/2021 09:41
     * @return Line|null
     */
    public function getLine(): ?Line
    {
        return $this->line = isset($this->line) ? $this->line : null;
    }

    /**
     * setLine
     * 6/01/2021 09:41
     * @param $line
     * @return LineManager
     */
    public function setLine($line): LineManager
    {
        if (is_string($line)) $line = ProviderFactory::create(Line::class)->find($line);
        $this->line = $line instanceof Line ? $line : null;
        return $this;
    }

    /**
     * getClasses
     * 5/01/2021 13:04
     * @return ArrayCollection
     */
    public function getClasses(): ArrayCollection
    {
        if (is_null($this->getLine())) return $this->getDataManager()->getClasses();

        return $this->getLine()->getClasses();
    }

    /**
     * removeClass
     *
     * 10/01/2021 09:45
     * @param ClassDetail $detail
     * @return LineManager
     */
    public function removeClass(ClassDetail $detail): LineManager
    {
        $this->line->getClasses()->removeElement($detail);
        $this->getDataManager()->getClasses()->removeElement($detail);
        return $this;
    }

    /**
     * getGrade
     * 6/01/2021 10:40
     * @return Grade|null
     */
    public function getGrade(): ?Grade
    {
        return $this->grade = isset($this->grade) ? $this->grade : null;
    }

    /**
     * setGrade
     * 7/01/2021 10:31
     *
     * @param $grade
     * @return LineManager
     */
    public function setGrade($grade): LineManager
    {
        if (is_string($grade)) $grade = ProviderFactory::create(Grade::class)->find($grade);
        $this->grade = $grade instanceof Grade ? $grade : null;

        return $this;
    }

    /**
     * getLines
     * 6/01/2021 10:44
     * @return ArrayCollection
     */
    public function getLines(): ArrayCollection
    {
        if (($grade = $this->getGrade()) === null) return $this->getDataManager()->getLines();

        $lines = $this->getDataManager()->getLines()->filter(function (Line $line) use ($grade) {
            if ($line->hasGrade($grade)) return $line;
        });

        return $lines;
    }
}
