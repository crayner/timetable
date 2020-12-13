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
 * Date: 11/12/2020
 * Time: 13:14
 */
namespace App\Items;

/**
 * Class Line
 * @package App\Items
 * @author Craig Rayner <craig@craigrayner.com>
 */
class Line
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
     * setName
     * @param string $name
     * @return $this
     * 11/12/2020 13:14
     */
    public function setName(string $name): Line
    {
        $this->name = $name;
        return $this;
    }

    /**
     * serialise
     * @return string[]
     * 11/12/2020 13:28
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
     * 11/12/2020 13:28
     */
    public function deserialise(array $data): Line
    {
        $this->name = $data['name'];
        return $this;
    }
}