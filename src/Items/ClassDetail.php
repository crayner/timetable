<?php
/**
 * Created by PhpStorm.
 *
 * Timetable Creator
 * (c) 2020-2020 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Licence: MIT
 * User: Craig Rayner
 * Date: 31/12/2020
 * Time: 14:11
 */

namespace App\Items;

use App\Helper\UUID;
use App\Provider\ProviderFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassDetail implements DuplicateNameInterface
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
    private int $capacity;

    /**
     * @var ArrayCollection|Staff[]
     */
    private ArrayCollection $teachers;

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
     * @return ClassDetail
     */
    public function setId(string $id): ClassDetail
    {
        $this->id = $id;
        return $this;
    }

    /**
     * getName
     * 31/12/2020 14:19
     * @return string
     */
    public function getName(): string
    {
        return $this->name = isset($this->name) ? $this->name : '';
    }

    /**
     * @param string $name
     * @return ClassDetail
     */
    public function setName(string $name): ClassDetail
    {
        $this->name = $name;
        return $this;
    }

    /**
     * getCapacity
     * 31/12/2020 14:19
     * @return int
     */
    public function getCapacity(): int
    {
        return $this->capacity = isset($this->capacity) ? $this->capacity : 0;
    }

    /**
     * @param int $capacity
     * @return ClassDetail
     */
    public function setCapacity(int $capacity): ClassDetail
    {
        $this->capacity = $capacity;
        return $this;
    }

    /**
     * getTeachers
     * 1/01/2021 15:02
     * @return Staff[]|ArrayCollection
     */
    public function getTeachers(bool $serialise = false): ArrayCollection
    {
        if ($serialise) {
            $result = new ArrayCollection();
            foreach($this->getTeachers() as $teacher) {
                $result->add($teacher->getId());
            }
            return $result;
        }

        return $this->teachers = isset($this->teachers) ? $this->teachers : new ArrayCollection();
    }

    /**
     * setTeachers
     * 1/01/2021 15:02
     * @param ArrayCollection $teachers
     * @return ClassDetail
     */
    public function setTeachers(ArrayCollection $teachers): ClassDetail
    {
        $this->teachers = $teachers;
        return $this;
    }

    /**
     * addTeacher
     * 1/01/2021 15:07
     * @param Staff $teacher
     * @return ClassDetail
     */
    public function addTeacher(Staff $teacher): ClassDetail
    {
        if ($this->getTeachers()->contains($teacher)) return $this;

        $this->teachers->add($teacher);

        return $this;
    }


    /**
     * serialise
     * 31/12/2020 14:16
     * @return string[]
     */
    public function serialise(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'capacity' => $this->getCapacity(),
            'teachers' => $this->getTeachers(true)->toArray(),
        ];
    }

    /**
     * deserialise
     * 1/01/2021 11:14
     * @param array $data
     * @return ClassDetail
     */
    public function deserialise(array $data): ClassDetail
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
                    'capacity' => 0,
                    'teachers' => [],
                ]
            )
            ->setAllowedTypes('id', 'string')
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('capacity', 'integer')
            ->setAllowedTypes('teachers', 'array')
        ;
        $data = $resolver->resolve($data);
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->capacity = $data['capacity'];
        foreach ($data['teachers'] as $teacher) {
            $this->addTeacher(ProviderFactory::create(Staff::class)->find($teacher));
        }
        return $this;
    }

}