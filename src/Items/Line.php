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
use App\Provider\ProviderFactory;
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
     * @var ArrayCollection
     */
    private ArrayCollection $periods;

    /**
     * @var int
     */
    private int $placementCount;

    /**
     * @var int
     */
    private int $doublePeriods;

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
     * getGradeStrings
     * 6/01/2021 11:19
     * @return string
     */
    public function getGradeStrings(): string
    {
        $result = [];
        foreach ($this->getGrades() as $grade) {
            $result[] = $grade->getName();
        }

        return empty($result) ? '' : "<ul>\n<li>" . implode("</li>\n<li>", $result) . "</li>\n</ul>";
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
     * hasGrade
     * 6/01/2021 10:49
     * @param Grade|null $grade
     * @return bool
     */
    public function hasGrade(?Grade $grade): bool
    {
        if ((is_null($grade))) return false;
        $grades = $this->getGrades()->filter(function (Grade $item) use ($grade) {
            if ($grade->isEqualto($item)) return $item;
        });
        return $grades->count() === 1;
    }

    /**
     * getClasses
     * 31/12/2020 14:05
     * @return ArrayCollection
     */
    public function getClasses(): ArrayCollection
    {
        if (!isset($this->classes) || $this->classes->count() === 0) {
            $line = $this;
            $this->classes = ProviderFactory::create(ClassDetail::class)->all()->filter(function(ClassDetail $class) use ($line) {
                if ($line->isEqualTo($class->getLine())) return $class;
            });
            return $this->classes;
        } else {
            return isset($this->classes) ? $this->classes : new ArrayCollection();
        }
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
     * getDaysString
     * 7/01/2021 10:50
     * @return string
     */
    public function getDaysString(): string
    {
        $result = [];
        foreach ($this->getDays() as $day)
            $result[] = $day->getName();
        return implode(', ', $result);
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
     * getPeriods
     *
     * 10/01/2021 10:08
     * @return ArrayCollection
     */
    public function getPeriods(): ArrayCollection
    {
        return $this->periods = isset($this->periods) ? $this->periods : new ArrayCollection();
    }
    
    /**
     * getPeriodsString
     * 7/01/2021 10:50
     * @return string
     */
    public function getPeriodsString(): string
    {
        $result = [];
        foreach ($this->getPeriods() as $period)
            $result[] = $period->getName();
        return implode(', ', $result);
    }

    /**
     * setPeriods
     *
     * 10/01/2021 10:08
     * @param ArrayCollection $periods
     * @return Line
     */
    public function setPeriods(ArrayCollection $periods): Line
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
     * @return int
     */
    public function getDoublePeriods(): int
    {
        return $this->doublePeriods = isset($this->doublePeriods) ? $this->doublePeriods : 0;
    }

    /**
     * @param int $doublePeriods
     * @return Line
     */
    public function setDoublePeriods(int $doublePeriods): Line
    {
        $this->doublePeriods = $doublePeriods;
        return $this;
    }

    /**
     * isEqualTo
     * 5/01/2021 14:46
     * @param Line $line
     * @return bool
     */
    public function isEqualTo(Line $line): bool
    {
        return $line->getId() === $this->getId();
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
            'days' => ItemSerialiser::serialise($this->getDays()),
            'periods' => ItemSerialiser::serialise($this->getPeriods()),
            'placementCount' => $this->getPlacementCount(),
            'doublePeriods' => $this->getDoublePeriods(),
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
                    'periods' => [],
                    'days' => [],
                    'placementCount' => 0,
                    'doublePeriods' => 0,
                ]
            )
            ->setAllowedTypes('id', 'string')
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('grades', 'array')
            ->setAllowedTypes('periods', 'array')
            ->setAllowedTypes('days', 'array')
            ->setAllowedTypes('placementCount', 'integer')
            ->setAllowedTypes('doublePeriods', 'integer')
        ;
        $data = $resolver->resolve($data);
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->placementCount = $data['placementCount'];
        $this->doublePeriods = $data['doublePeriods'];
        $this->days = ItemSerialiser::deserialise(Day::class, $data['days']);
        $this->grades = ItemSerialiser::deserialise(Grade::class, $data['grades']);
        $this->periods = ItemSerialiser::deserialise(Period::class, $data['periods']);
        return $this;
    }
}
