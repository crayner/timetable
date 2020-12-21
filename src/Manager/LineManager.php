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

use App\Items\Line;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class LineManager
 * @package App\Manager
 * @author Craig Rayner <craig@craigrayner.com>
 */
class LineManager
{
    /**
     * @var
     */
    private int $linesPerGrade = 25;

    /**
     * @var ArrayCollection
     */
    private ArrayCollection $lines;

    /**
     * LineManager constructor.
     * @param int $linesPerGrade
     */
    public function __construct(int $linesPerGrade = 25)
    {
        $this->linesPerGrade = $linesPerGrade;
    }

    /**
     * serialise
     * @return string[]
     * 11/12/2020 13:28
     */
    public function serialise(): array
    {
        $lines = [];
        foreach ($this->getLines() as $line) $lines[] = $line->serialise();
        return $lines;
    }

    /**
     * deserialise
     * 16/12/2020 11:15
     * @param array $data
     * @return $this
     */
    public function deserialise(array $data): LineManager
    {
        $lines = new ArrayCollection();
        foreach ($data as $item) {
            $line = new Line($item);
        }
        return $this;
    }

    /**
     * getLinesPerGrade
     * 16/12/2020 10:25
     * @return int
     */
    public function getLinesPerGrade(): int
    {
        return $this->linesPerGrade;
    }

    /**
     * setLinesPerGrade
     * 16/12/2020 10:25
     * @param int $linesPerGrade
     * @return LineManager
     */
    public function setLinesPerGrade(int $linesPerGrade): LineManager
    {
        $this->linesPerGrade = $linesPerGrade;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getLines(): ArrayCollection
    {
        return $this->lines = isset($this->lines) ? $this->lines : new ArrayCollection();
    }

    /**
     * @param ArrayCollection $lines
     * @return LineManager
     */
    public function setLines(ArrayCollection $lines): LineManager
    {
        $this->lines = $lines;
        return $this;
    }

    /**
     * addLine
     * 16/12/2020 16:25
     * @param Line $line
     * @return LineManager
     */
    public function addLine(Line $line): LineManager
    {
        if ($this->getLines()->contains($line)) return $this;

        if ($line->getName() === '') $line->setName('Line ' . $this->lines->count());

        $this->lines->add($line);

        return $this;
    }
}
