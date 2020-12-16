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
 * Time: 08:43
 */
namespace App\Items;

use App\Helper\UUID;

class Room implements DuplicateNameInterface
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
    private int $size = 30;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id = isset($this->id) ? $this->id : UUID::v4();
    }

    /**
     * @return string|null
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Name.
     *
     * @param string $name
     * @return Room
     */
    public function setName(string $name): Room
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Size.
     *
     * @param int $size
     * @return Room
     */
    public function setSize(int $size): Room
    {
        $this->size = $size;
        return $this;
    }

    /**
     * serialise
     * @return null[]|string[]
     * 14/12/2020 08:46
     */
    public function serialise(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'size' => $this->getSize(),
        ];
    }

    /**
     * deserialise
     * @param array $data
     * @return $this
     * 14/12/2020 08:46
     */
    public function deserialise(array $data): Room
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->size = $data['size'];
        return $this;
    }
}
