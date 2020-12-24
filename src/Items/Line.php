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
use App\Provider\ProviderFactory;

/**
 * Class Line
 * @package App\Items
 * @author Craig Rayner <craig@craigrayner.com>
 */
class Line implements DuplicateNameInterface
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
     * @var Grade|null
     */
    private ?Grade $grade;

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
        return isset($this->name) ? $this->name : '';
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
            'id' => $this->getId(),
            'name' => $this->getName(),
            'grade' => $this->getGrade() ? $this->getGrade()->serialise() : null,
        ];
    }

    /**
     * deserialise
     * @param array $data
     * 11/12/2020 13:28
     */
    public function deserialise(array $data): Line
    {
        if (empty($data)) return $this;
        $this->id = $data['id'];
        $this->name = $data['name'];
        if (is_array($data['grade']) && key_exists('id', $data['grade'])) {
            $this->grade = ProviderFactory::create(Grade::class)->find($data['grade']['id']);
        } else {
            $this->grade = ProviderFactory::create(Grade::class)->find($data['grade']);
        }
        return $this;
    }

    /**
     * getGrade
     * 16/12/2020 17:19
     * @return Grade
     */
    public function getGrade(): Grade
    {
        return $this->grade = isset($this->grade) ? $this->grade : new Grade();
    }

    /**
     * setGrade
     * 16/12/2020 17:19
     * @param Grade $grade
     * @return $this
     */
    public function setGrade(Grade $grade): Line
    {
        $this->grade = $grade;
        return $this;
    }

}