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
use App\Provider\ProviderItemInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Room implements DuplicateNameInterface, ProviderItemInterface
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
    private int $capacity = 30;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id = isset($this->id) ? $this->id : UUID::v4();
    }

    /**
     * @param string $id
     * @return Room
     */
    public function setId(string $id): Room
    {
        $this->id = $id;
        return $this;
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
    public function getCapacity(): int
    {
        return $this->capacity;
    }

    /**
     * Capacity.
     *
     * @param int $capacity
     * @return Room
     */
    public function setCapacity(int $capacity): Room
    {
        $this->capacity = $capacity;
        return $this;
    }

    /**
     * getNameCapacity
     * 5/01/2021 08:15
     * @return string
     */
    public function getNameCapacity(): string
    {
        return $this->getName() . ' ('.$this->getCapacity().')';
    }

    /**
     * serialise
     * @return array
     * 14/12/2020 08:46
     */
    public function serialise(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'capacity' => $this->getCapacity(),
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
        $resolver = new OptionsResolver();
        $resolver
            ->setRequired(
                [
                    'id',
                    'name',
                ]
            )
            ->setDefaults(
                [
                    'capacity' => 30,
                ]
            )
            ->setAllowedTypes('id', 'string')
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('capacity', 'integer')
        ;
        $data = $resolver->resolve($data);
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->capacity = $data['capacity'];
        return $this;
    }
}
