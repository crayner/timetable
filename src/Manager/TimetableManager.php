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
 * Date: 18/12/2020
 * Time: 07:41
 */
namespace App\Manager;

use stdClass;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class TimetableManager
 * @package App\Manager
 * @author Craig Rayner <craig@craigrayner.com>
 */
class TimetableManager
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var SessionInterface
     */
    private SessionInterface $session;

    /**
     * @var DataManager
     */
    private DataManager $dataManager;

    /**
     * @var bool
     */
    private bool $saveOnTerminate = true;

    /**
     * @var ValidatorManager
     */
    private ValidatorManager $validatorManager;

    /**
     * @var string
     */
    private string $secret;

    /**
     * @var stdClass
     */
    private stdClass $user;

    /**
     * TimetableManager constructor.
     * @param SessionInterface $session
     * @param ValidatorManager $validatorManager
     * @param string $secret
     */
    public function __construct(SessionInterface $session, ValidatorManager $validatorManager, string $secret, DataManager $dataManager)
    {
        $this->session = $session;
        $this->setDataManager($dataManager)
            ->setValidator($validatorManager)
            ->setSecret($secret);
    }

    /**
     * getName
     * 18/12/2020 07:43
     */
    public function getName(): string
    {
        $this->name = isset($this->name) ? $this->name : '';

        if ($this->name === '' && $this->hasSession()) {
            if ($this->getSession()->has('timetable_name')) $this->name = $this->getSession()->get('timetable_name');
        }

        return $this->name;
    }

    /**
     * setName
     * 18/12/2020 07:48
     * @param string $name
     * @return $this
     */
    public function setName(string $name): TimetableManager
    {
        $this->name = $name;
        $this->isNameValid() ? $this->getSession()->set('timetable_name', $this->getName()) : $this->getSession()->remove('timetable_name');
        $this->getDataManager()
            ->setName($this->getName())
            ->setSecret($this->getSecret());
        return $this;
    }

    /**
     * isNameValid
     * 18/12/2020 07:47
     * @return bool
     */
    public function isNameValid(): bool
    {
        return $this->getName() !== '';
    }

    /**
     * getSession
     * 18/12/2020 07:46
     * @return SessionInterface
     */
    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    /**
     * getSession
     * 18/12/2020 07:46
     * @return bool
     */
    public function hasSession(): bool
    {
        return $this->session instanceof SessionInterface && $this->session->isStarted();
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
     * @return TimetableManager
     */
    public function setDataManager(DataManager $dataManager): TimetableManager
    {
        $this->dataManager = $dataManager;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSaveOnTerminate(): bool
    {
        return $this->saveOnTerminate;
    }

    /**
     * @param bool $saveOnTerminate
     * @return TimetableManager
     */
    public function setSaveOnTerminate(bool $saveOnTerminate): TimetableManager
    {
        $this->saveOnTerminate = $saveOnTerminate;
        return $this;
    }

    /**
     * getValidator
     * 21/12/2020 08:29
     * @return ValidatorManager
     */
    public function getValidator(): ValidatorManager
    {
        return $this->validatorManager->setDataManager($this->getDataManager());
    }

    /**
     * setValidator
     * 21/12/2020 08:29
     * @param ValidatorManager $validatorManager
     * @return TimetableManager
     */
    public function setValidator(ValidatorManager $validatorManager): TimetableManager
    {
        $this->validatorManager = $validatorManager->setDataManager($this->getDataManager());
        return $this;
    }

    /**
     * isFileValid
     * 21/12/2020 08:32
     * @return bool
     */
    public function isFileValid(): bool
    {
        if (!$this->getValidator()->isFileValid()) {
            $this->getDataManager()->unlink();
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     * @return TimetableManager
     */
    public function setSecret(string $secret): TimetableManager
    {
        $this->secret = $secret;
        return $this;
    }

    /**
     * getUser
     * 21/12/2020 10:47
     * @return stdClass
     */
    public function getUser(): stdClass
    {
        if (!isset($this->user)) {
            $this->user = new stdClass();
            $this->user->name = $this->getName();
            $this->user->password = $this->getDataManager()->getPassword();
            $this->user->secret = $this->getDataManager()->getSecret();
        }
        return $this->user;
    }

    /**
     * isReadyToMap
     * 23/12/2020 11:19
     * @return bool
     */
    public function isReadyToMap(): bool
    {
        return $this->isNameValid()
            && $this->isFileValid()
            && $this->getDataManager()->getStaffCount() > 0
            && $this->getDataManager()->getGradeCount() > 0
            && $this->getDataManager()->getDayCount() > 0
        ;
    }
}
