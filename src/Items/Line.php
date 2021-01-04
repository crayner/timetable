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

use App\Helper\UUID;
use App\Manager\ItemSerialiser;
use App\Provider\ProviderItemInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Line
 * @package App\Items
 * @author Craig Rayner <craig@craigrayner.com>
 */
class Line implements DuplicateNameInterface, ProviderItemInterface
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
    private ArrayCollection $grades;

    /**
     * @var ArrayCollection
     */
    private ArrayCollection $classes;

    /**
     * @var ArrayCollection
     */
    private ArrayCollection $days;

    /**
     * @var array
     */
    private array $periods;

    /**
     * @var int
     */
    private int $placementCount;

    /**
     * Line constructor.
     * @param array $line
     */
    public function __construct(array $line = [])
    {
        $this->deserialise($line);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id = isset($this->id) ? $this->id : UUID::v4();
    }

    /**
     * setId
     * 23/12/2020 15:07
     * @param string|null $id
     * @return Line
     */
    public function setId(?string $id): Line
    {
        if (!empty($id)) $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name = isset($this->name) ? substr($this->name, 0, 6) : '';
    }

    /**
     * setName
     * @param string $name
     * @return $this
     * 11/12/2020 13:14
     */
    public function setName(string $name): Line
    {
        $this->name = substr($name,0,6);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getGrades(): ArrayCollection
    {
        return $this->grades = isset($this->grades) ? $this->grades : new ArrayCollection();
    }

    /**
     * @param ArrayCollection $grades
     * @return Line
     */
    public function setGrades(ArrayCollection $grades): Line
    {
        $this->grades = $grades;
        return $this;
    }

    /**
     * getClasses
     * 31/12/2020 14:05
     * @return ArrayCollection
     */
    public function getClasses(): ArrayCollection
    {
        return $this->classes = isset($this->classes) ? $this->classes : new ArrayCollection();
    }

    /**
     * @param ArrayCollection $classes
     * @return Line
     */
    public function setClasses(ArrayCollection $classes): Line
    {
        $this->classes = $classes;
        return $this;
    }

    /**
     * addClass
     * 31/12/2020 14:13
     * @param ClassDetail $class
     * @return Line
     */
    public function addClass(ClassDetail $class): Line
    {
        if ($this->getClasses()->contains($class)) return $this;

        $this->classes->add($class);

        return $this;
    }

    /**
     * getClassCount
     * 4/01/2021 08:53
     * @return int
     */
    public function getClassCount(): int
    {
        return $this->getClasses()->count();
    }

    /**
     * setClassCount
     * 4/01/2021 09:00
     * @param int $count
     * @return Line
     */
    public function setClassCount(int $count): Line
    {
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getDays(): ArrayCollection
    {
        return $this->days = isset($this->days) ? $this->days : new ArrayCollection();
    }

    /**
     * @param ArrayCollection $days
     * @return Line
     */
    public function setDays(ArrayCollection $days): Line
    {
        $this->days = $days;
        return $this;
    }

    /**
     * @return array
     */
    public function getPeriods(): array
    {
        return $this->periods = isset($this->periods) ? $this->periods : [];
    }

    /**
     * @param array $periods
     * @return Line
     */
    public function setPeriods(array $periods): Line
    {
        $this->periods = $periods;
        return $this;
    }

    /**
     * @return int
     */
    public function getPlacementCount(): int
    {
        return $this->placementCount = isset($this->placementCount) ? $this->placementCount : 0;
    }

    /**
     * @param int $placementCount
     * @return Line
     */
    public function setPlacementCount(int $placementCount): Line
    {
        $this->placementCount = $placementCount;
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
            'grades' => ItemSerialiser::serialise($this->getGrades()),
            'classes' => ItemSerialiser::serialise($this->getClasses()),
            'days' => ItemSerialiser::serialise($this->getDays()),
            'periods' => $this->getPeriods(),
            'placementCount' => $this->getPlacementCount(),
        ];
    }

    /**
     * deserialise
     * @param array $data
     * 11/12/2020 13:28
     */
    public function deserialise(array $data): Line
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
                    'grades' => [],
                    'classes' => [],
                    'periods' => [],
                    'days' => [],
                    'placementCount' => 0,
                ]
            )
            ->setAllowedTypes('id', 'string')
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('grades', 'array')
            ->setAllowedTypes('classes', 'array')
            ->setAllowedTypes('periods', 'array')
            ->setAllowedTypes('days', 'array')
            ->setAllowedTypes('placementCount', 'integer')
        ;
        $data = $resolver->resolve($data);
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->placementCount = $data['placementCount'];
        $this->grades = ItemSerialiser::deserialise(Grade::class, $data['grades']);
        $this->classes = ItemSerialiser::deserialise(ClassDetail::class, $data['classes']);
        $this->days = ItemSerialiser::deserialise(Day::class, $data['classes']);
        $this->periods = $data['periods'];
        return $this;
    }
}
