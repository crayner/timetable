<?php
/**
 * Created by PhpStorm.
 *
 * timetable
 * (c) 2021 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 8/01/2021
 * Time: 09:24
 */

namespace App\Items;


use App\Helper\UUID;
use App\Manager\ItemSerialiser;
use App\Provider\ProviderFactory;
use App\Provider\ProviderItemInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Period implements DuplicateNameInterface, ProviderItemInterface
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
     * @var ArrayCollection
     */
    private ArrayCollection $doubleWith;

    /**
     * @var int
     */
    private int $sequence;

    /**
     * getId
     * 8/01/2021 09:31
     * @return string
     */
    public function getId(): string
    {
        return $this->id = isset($this->id) ? $this->id : UUID::v4();
    }

    /**
     * @param string $id
     * @return Period
     */
    public function setId(string $id): Period
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
     * @param string $name
     * @return Period
     */
    public function setName(string $name): Period
    {
        $this->name = $name;
        return $this;
    }

    /**
     * getDoubleWith
     * 8/01/2021 14:36
     * @return ArrayCollection
     */
    public function getDoubleWith(): ArrayCollection
    {
        $this->doubleWith = isset($this->doubleWith) ? $this->doubleWith : new ArrayCollection();

        $periods = new ArrayCollection();
        foreach ($this->doubleWith as $period) {
            if ($period instanceof Period) {
                $periods->add($period);
            } else {
                $periods->add(ProviderFactory::create(Period::class)->find($period));
            }
        }
        $this->doubleWith = $periods;

        return $this->doubleWith;
    }

    /**
     * @param ArrayCollection $doubleWith
     * @return Period
     */
    public function setDoubleWith(ArrayCollection $doubleWith): Period
    {
        $this->doubleWith = $doubleWith;
        return $this;
    }

    /**
     * @return int
     */
    public function getSequence(): int
    {
        return $this->sequence;
    }

    /**
     * @param int $sequence
     * @return Period
     */
    public function setSequence(int $sequence): Period
    {
        $this->sequence = $sequence;
        return $this;
    }

    /**
     * serialise
     * 8/01/2021 09:29
     * @return array
     */
    public function serialise(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'doubleWith' => ItemSerialiser::serialise($this->getDoubleWith()),
            'sequence' => $this->getSequence(),
        ];
    }

    /**
     * deserialise
     * 8/01/2021 09:28
     *
     * @param array $data
     * @return Period
     */
    public function deserialise(array $data): Period
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setRequired(
                [
                    'id',
                    'name',
                    'sequence',
                ]
            )
            ->setDefaults(
                [
                    'doubleWith' => [],
                ]
            )
            ->setAllowedTypes('id', 'string')
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('sequence', 'integer')
            ->setAllowedTypes('doubleWith', 'array')
        ;
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->sequence = $data['sequence'];
        $this->doubleWith = new ArrayCollection($data['doubleWith']);
        return $this;
    }
}
