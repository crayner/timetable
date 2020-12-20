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
 * Date: 10/12/2020
 * Time: 14:42
 */
namespace App\Manager;

use DateTimeImmutable;
use Symfony\Component\Yaml\Yaml;

/**
 * Class TimetableValidatorManager
 * @package App\Manager
 * @author Craig Rayner <craig@craigrayner.com>
 */
class ValidatorManager
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var DataManager
     */
    private DataManager $dataManager;

    /**
     * getName
     * @return string|null
     * 14/12/2020 12:06
     */
    public function getName(): ?string
    {
        return isset($this->name) ? $this->name : null;
    }

    /**
     * Name.
     *
     * @param string $name
     * @return ValidatorManager
     */
    public function setName(string $name): ValidatorManager
    {
        $this->name = $name;
        return $this;
    }

    /**
     * doesFileExist
     * @return bool
     * 10/12/2020 14:45
     */
    public function doesFileExist(): bool
    {
        return realpath($this->getDataManager()->getFilePath()) !== false;
    }

    /**
     * isFileValid
     * @return bool
     * 10/12/2020 14:50
     */
    public function isFileValid(): bool
    {
        dump($this);
        if (!$this->getDataManager()->readFile()) return false;
        dump($this);

        if (empty($this->getDataManager()->getName())) return false;
        dump($this);

        if ($this->getDataManager()->getCreatedOn()->format('c') < date('c', strtotime('-3 Hours'))) return false;
        dump($this);

        return true;
    }

    /**
     * @return DataManager
     */
    public function getDataManager(): DataManager
    {
        return $this->dataManager;
    }

    /**
     * @param DataManager $dataManager
     * @return ValidatorManager
     */
    public function setDataManager(DataManager $dataManager): ValidatorManager
    {
        $this->dataManager = $dataManager;
        return $this;
    }
}
