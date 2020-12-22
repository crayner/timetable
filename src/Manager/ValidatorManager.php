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

use App\Helper\SecurityEncoder;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
     * @var SecurityEncoder
     */
    private SecurityEncoder $encoder;

    /**
     * @var string
     */
    private string $secret;

    /**
     * @var SessionInterface
     */
    private SessionInterface $session;

    /**
     * ValidatorManager constructor.
     * @param string $secret
     * @param SessionInterface $session
     */
    public function __construct(string $secret, SessionInterface $session)
    {
        $this->secret = $secret;
        $this->session = $session;
    }

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
     * @param bool $ignoreDate
     * @return bool
     * 10/12/2020 14:50
     */
    public function isFileValid(bool $ignoreDate = false): bool
    {
        $valid = true;
        if (!$this->getDataManager()->readFile()) $valid = false;

        if ($valid && empty($this->getDataManager()->getName())) $valid = false;

        if ($valid && !$ignoreDate && $this->getDataManager()->getCreatedOn()->format('c') < date('c', strtotime('-3 Hours'))) $valid = false;

        if ($valid && !$this->getEncoder()->isPasswordValid($this->getDataManager()->getSecret(), $this->getDataManager()->getName().$this->getSecret())) $valid = false;

        if (!$valid) {
            $this->getDataManager()->unlink();
            $this->getSession()->invalidate();
        }

        return $valid;
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

    /**
     * getEncoder
     * 21/12/2020 15:20
     * @return SecurityEncoder
     */
    public function getEncoder(): SecurityEncoder
    {
        return $this->encoder = isset($this->encoder) ? $this->encoder : new SecurityEncoder();
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @return SessionInterface
     */
    public function getSession(): SessionInterface
    {
        return $this->session;
    }
}
