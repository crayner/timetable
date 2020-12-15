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
 * Time: 09:18
 */
namespace App\Items;

/**
 * Class Grade
 * @package App\Items
 * @author Craig Rayner <craig@craigrayner.com>
 */
class Grade implements DuplicateNameInterface
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var int
     * When zero, this value is not used as a check.
     */
    private int $studentCount = 0;

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
     * @return Grade
     */
    public function setName(string $name): Grade
    {
        $this->name = $name;
        return $this;
    }

    /**
     * getStudentCount
     * 15/12/2020 08:28
     * @return int
     */
    public function getStudentCount(): int
    {
        return $this->studentCount;
    }

    /**
     * setStudentCount
     * 15/12/2020 08:28
     * @param int $studentCount
     * @return $this
     */
    public function setStudentCount(int $studentCount): Grade
    {
        $this->studentCount = $studentCount;
        return $this;
    }

    /**
     * serialise
     * @return string[]
     * 14/12/2020 09:35
     */
    public function serialise(): array
    {
        return [
            'name' => $this->getName(),
            'studentCount' => $this->getStudentCount(),
        ];
    }

    /**
     * deserialise
     * @param array $data
     * @return $this
     * 14/12/2020 09:36
     */
    public function deserialise(array $data): Grade
    {
        $this->name = $data['name'];
        $this->studentCount = $data['studentCount'];
        return $this;
    }
}
