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

use App\Helper\UUID;

class Day implements DuplicateNameInterface
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var int
     */
    private int $periods = 6;

    /**
     * getId
     * 16/12/2020 17:45
     * @return string
     */
    public function getId(): string
    {
        return $this->id = isset($this->id) ? $this->id : UUID::v4();
    }

    /**
     * @param string $id
     * @return Day
     */
    public function setId(string $id): Day
    {
        $this->id = $id;
        return $this;
    }

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
            'id' => $this->getId(),
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
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->periods = $data['periods'];
        return $this;
    }
}
