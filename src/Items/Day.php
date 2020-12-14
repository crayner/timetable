<?php
/**
 * Created by PhpStorm.
 *
 * timetable
 * (c) 2020 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 14/12/2020
 * Time: 10:11
 */
namespace App\Items;

class Day implements NameInterface
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var int
     */
    private int $periods = 6;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Name.
     *
     * @param string $name
     * @return Day
     */
    public function setName(string $name): Day
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getPeriods(): int
    {
        return $this->periods;
    }

    /**
     * Periods.
     *
     * @param int $periods
     * @return Day
     */
    public function setPeriods(int $periods): Day
    {
        $this->periods = $periods > 0 ? $periods : $this->periods;
        return $this;
    }

    /**
     * serialise
     * @return array
     * 14/12/2020 10:15
     */
    public function serialise(): array
    {
        return [
            'name' => $this->getName(),
            'periods' => $this->getPeriods(),
        ];
    }

    /**
     * deserialise
     * @param array $data
     * @return Day
     * 14/12/2020 10:43
     */
    public function deserialise(array $data): Day
    {
        $this->name = $data['name'];
        $this->periods = $data['periods'];
        return $this;
    }

}
