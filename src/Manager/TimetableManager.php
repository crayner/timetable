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

use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
     * @var DataManager|null
     */
    private ?DataManager $dataManager;

    /**
     * @var bool
     */
    private bool $saveOnTerminate = true;

    /**
     * @var ValidatorManager
     */
    private ValidatorManager $validatorManager;

    /**
     * TimetableManager constructor.
     * @param SessionInterface $session
     * @param ValidatorManager $validatorManager
     */
    public function __construct(SessionInterface $session, ValidatorManager $validatorManager)
    {
        $this->session = $session;
        $this->setValidator($validatorManager);
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
        $this->isNameValid() ? $this->getSession()->set('timetable_name', $name) : $this->getSession()->remove('timetable_name');
        $this->getDataManager()->setName($name);
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
        return $this->dataManager = isset($this->dataManager) && $this->dataManager !== null ? $this->dataManager : new DataManager($this->getName());
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
        return $this->validatorManager;
    }

    /**
     * setValidator
     * 21/12/2020 08:29
     * @param ValidatorManager $validatorManager
     * @return TimetableManager
     */
    public function setValidator(ValidatorManager $validatorManager): TimetableManager
    {
        $this->validatorManager = $validatorManager;
        $this->validatorManager->setDataManager($this->getDataManager());
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
            $this->dataManager = null;
            return false;
        }
        return true;
    }
}
