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
class TimetableValidatorManager
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var TimetableDataManager
     */
    private TimetableDataManager $dataManager;

    /**
     * TimetableValidatorManager constructor.
     * @param TimetableDataManager $dataManager
     */
    public function __construct(TimetableDataManager $dataManager)
    {
        $this->setDataManager($dataManager);
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
     * @return TimetableValidatorManager
     */
    public function setName(string $name): TimetableValidatorManager
    {
        $this->name = $name;
        return $this;
    }

    /**
     * getFilePath
     * @return string
     * 10/12/2020 14:48
     */
    private function getFilePath(): string
    {
        return __DIR__ .'/../../config/data/'.$this->getName().'.yaml';
    }

    /**
     * doesFileExist
     * @return bool
     * 10/12/2020 14:45
     */
    public function doesFileExist(): bool
    {
        return realpath($this->getFilePath()) !== false;
    }

    /**
     * isFileValid
     * @return bool
     * 10/12/2020 14:50
     */
    public function isFileValid(): bool
    {
        if (!$this->doesFileExist()) return false;

        $data = Yaml::parse(file_get_contents($this->getFilePath()));

        $this->getDataManager()->setData($data);

        if (empty($this->getDataManager()->getName())) return false;

        if (empty($this->getDataManager()->getStaff())) return false;

        if (empty($this->getDataManager()->getLines())) return false;

        if ($this->getDataManager()->getCreatedAt() === null || $this->getDataManager()->getCreatedAt()->format('c') < date('c', strtotime('-1 Day'))) return false;

        return true;
    }

    /**
     * @return TimetableDataManager
     */
    public function getDataManager(): TimetableDataManager
    {
        return $this->dataManager;
    }

    /**
     * DataManager.
     *
     * @param TimetableDataManager $dataManager
     * @return TimetableValidatorManager
     */
    public function setDataManager(TimetableDataManager $dataManager): TimetableValidatorManager
    {
        $this->dataManager = $dataManager;
        return $this;
    }

}
