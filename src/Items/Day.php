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
use App\Manager\ItemSerialiser;
use App\Provider\ProviderFactory;
use App\Provider\ProviderItemInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Day
 * @package App\Items
 * @author Craig Rayner <craig@craigrayner.com>
 */
class Day implements DuplicateNameInterface, ProviderItemInterface
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
    private ArrayCollection $periods;

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
     * getPeriods
     *
     * 9/01/2021 10:45
     * @param bool $serialise
     * @return ArrayCollection
     */
    public function getPeriods(bool $serialise = false): ArrayCollection
    {
        $this->periods = isset($this->periods) ? $this->periods : new ArrayCollection();
        if ($serialise && $this->periods->count() === ProviderFactory::create(Period::class)->all()->count()) $this->periods = new ArrayCollection();

        if (!$serialise and $this->periods->count() === 0) $this->periods = ProviderFactory::create(Period::class)->all();

        return $this->periods;
    }

    /**
     * setPeriods
     *
     * 9/01/2021 11:30
     * @param ArrayCollection $periods
     * @return Day
     */
    public function setPeriods(ArrayCollection $periods): Day
    {
        $this->periods = $periods;
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
            'periods' => ItemSerialiser::serialise($this->getPeriods(true)),
        ];
    }

    /**
     * deserialise
     * 8/01/2021 10:16
     *
     * @param array $data
     * @return Day
     */
    public function deserialise(array $data): Day
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
                    'periods' => [],
                ]
            )
            ->setAllowedTypes('id', 'string')
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('periods', 'array')
        ;
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->periods = ItemSerialiser::deserialise(Period::class, $data['periods']);
        return $this;
    }
}
