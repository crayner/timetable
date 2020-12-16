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
 * Time: 13:03
 */
namespace App\Items;

use App\Helper\UUID;

/**
 * Class Staff
 * @package App\Items
 * @author Craig Rayner <craig@craigrayner.com>
 */
class Staff implements DuplicateNameInterface
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
     * @return string
     */
    public function getId(): string
    {
        return $this->id = isset($this->id) ? $this->id : UUID::v4();
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
     * @return Staff
     */
    public function setName(string $name): Staff
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
            'id' => $this->getId(),
            'name' => $this->getName(),
        ];
    }

    /**
     * deserialise
     * @param array $data
     * @return $this
     * 11/12/2020 13:45
     */
    public function deserialise(array $data): Staff
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        return $this;
    }
}
