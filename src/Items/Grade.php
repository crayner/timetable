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
class Grade
{
    /**
     * @var string
     */
    private string $name;

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
     * serialise
     * @return string[]
     * 14/12/2020 09:35
     */
    public function serialise(): array
    {
        return [
            'name' => $this->getName(),
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
        return $this;
    }
}
