<?php
/**
 * Created by PhpStorm.
 * timetable
 * (c) 2021 Craig Rayner <craig@craigrayner.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * User: craig
 * Date: 11/01/2021
 * Time: 09:22
 */

namespace App\Items;

use App\Helper\UUID;
use App\Manager\ItemSerialiser;
use App\Provider\ProviderItemInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Slot
 *
 * @package App\Items
 * @author  Craig Rayner <craig@craigrayner.com>
 * 11/01/2021 09:23
 */
class Slot implements ProviderItemInterface
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @var ArrayCollection
     */
    private ArrayCollection $lines;

    /**
     * @var Day
     */
    private Day $day;

    /**
     * @var Period
     */
    private Period $period;

    /**
     * Slot constructor.
     *
     * @param Day|null    $day
     * @param Period|null $period
     */
    public function __construct(Day $day = null, Period $period = null)
    {
        if (!is_null($day)) $this->day = $day;
        if (!is_null($period)) $this->period = $period;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id = isset($this->id) ? $this->id : UUID::v4();
    }

    /**
     * @param string $id
     * @return Slot
     */
    public function setId(string $id): Slot
    {
        $this->id = $id;
        return $this;
    }

    /**
     * getLines
     *
     * 11/01/2021 09:39
     * @return ArrayCollection
     */
    public function getLines(): ArrayCollection
    {
        return $this->lines = isset($this->lines) ? $this->lines : new ArrayCollection();
    }

    /**
     * @param ArrayCollection $lines
     * @return Slot
     */
    public function setLines(ArrayCollection $lines): Slot
    {
        $this->lines = $lines;
        return $this;
    }

    /**
     * @return Day
     */
    public function getDay(): Day
    {
        return $this->day;
    }

    /**
     * @param Day $day
     * @return Slot
     */
    public function setDay(Day $day): Slot
    {
        $this->day = $day;
        return $this;
    }

    /**
     * @return Period
     */
    public function getPeriod(): Period
    {
        return $this->period;
    }

    /**
     * setPeriod
     *
     * 11/01/2021 09:25
     * @param Period $period
     * @return Slot
     */
    public function setPeriod(Period $period): Slot
    {
        $this->period = $period;
        return $this;
    }

    /**
     * getName
     *
     * 11/01/2021 09:24
     * @return string
     */
    public function getName(): string
    {
        return $this->getDay()->getName() . ': ' . $this->getPeriod()->getName();
    }

    /**
     * serialise
     *
     * 11/01/2021 09:40
     * @return array
     */
    public function serialise(): array
    {
        return [
            'id' => $this->getId(),
            'lines' => ItemSerialiser::serialise($this->getLines()),
            'period' => ItemSerialiser::serialise($this->getPeriod()),
            'day' => ItemSerialiser::serialise($this->getDay()),
        ];
    }

    /**
     * deserialise
     *
     * 11/01/2021 09:40
     * @param array $data
     * @return Slot
     */
    public function deserialise(array $data): Slot
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setRequired(
                [
                    'id',
                    'day',
                    'period',
                ]
            )
            ->setDefaults(
                [   
                    'lines' => [],
                ]
            )
            ->setAllowedTypes('id','string')
            ->setAllowedTypes('day','string')
            ->setAllowedTypes('period','string')
            ->setAllowedTypes('lines','array')
        ;
        $data = $resolver->resolve($data);
        $this->id = $data['id'];
        $this->day = ItemSerialiser::deserialise(Day::class, $data['day']);
        $this->period = ItemSerialiser::deserialise(Period::class, $data['period']);
        $this->lines = ItemSerialiser::deserialise(Line::class, $data['lines']);
        return $this;
    }
}