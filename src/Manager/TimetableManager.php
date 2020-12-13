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
 * Time: 12:31
 */
namespace App\Manager;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

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
    private string $status;

    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var RequestStack
     */
    private RequestStack $stack;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $generator;

    /**
     * @var TimetableDataManager
     */
    private TimetableDataManager $dataManager;

    /**
     * @var TimetableValidatorManager
     */
    private TimetableValidatorManager $validatorManager;

    private array $messages = [];

    /**
     * TimetableManager constructor.
     * @param RequestStack $stack
     * @param UrlGeneratorInterface $generator
     * @param TimetableDataManager $dataManager
     * @param TimetableValidatorManager $validatorManager
     */
    public function __construct(RequestStack $stack, UrlGeneratorInterface $generator, TimetableDataManager $dataManager, TimetableValidatorManager $validatorManager)
    {
        $this->setGenerator($generator)
            ->setRequestStack($stack)
            ->setDataManager($dataManager)
            ->setValidatorManager($validatorManager)
            ->getSession();
    }

    /**
     * getStatus
     * @return string
     * 10/12/2020 12:35
     */
    public function getStatus(): ?string
    {
        return isset($this->status) ? $this->status : null;
    }

    /**
     * Status.
     *
     * @param string $status
     * @return TimetableManager
     */
    public function setStatus(string $status): TimetableManager
    {
        $this->status = $status;
        return $this;
    }

    /**
     * getRequest
     * @return Request|null
     * 13/12/2020 09:39
     */
    public function getRequest(): ?Request
    {
        if (!isset($this->request) || is_null($this->request)) {
            $request = $this->getRequestStack()->getCurrentRequest();
            if ($request instanceof Request) {
                $this->request = $request;
            }
        }
        return isset($this->request) ? $this->request : null;
    }

    /**
     * getSession
     * @return SessionInterface|null
     * 13/12/2020 09:39
     */
    public function getSession(): ?SessionInterface
    {

        return $this->getRequest() ? $this->getRequest()->getSession() : null;
    }

    /**
     * getName
     * @return string|null
     * 10/12/2020 12:50
     */
    public function getName(): ?string
    {
        if (!isset($this->name) && $this->getSession()->has('timetable_name')) {
            $this->setName($this->getSession()->get('timetable_name'));
        } else if (!isset($this->name)) {
            return null;
        }
        return $this->name;
    }

    /**
     * Name.
     *
     * @param string $name
     * @return TimetableManager
     */
    public function setName(string $name): TimetableManager
    {
        if ($name === '_unknown_') return $this;

        if (!$this->isNameValid($name))
        {
            if ($this->getSession()->has('timetable_name')) $this->getSession()->remove('timetable_name');
            return $this;
        }

        $this->getSession()->set('timetable_name', $name);
        $this->name = $name;
        $this->getValidateManager()->setName($name);
        $this->getDataManager()->setName($name);

        return $this;
    }

    /**
     * isNameValid
     * @param string|null $name
     * @return bool
     * 10/12/2020 14:19
     */
    public function isNameValid(?string $name = null): bool
    {
        if ($name === null) $name = $this->getName();

        if ($name === null) return false;
        return true;
    }

    /**
     * getResponse
     * @param Environment $twig
     * @return Response
     * 10/12/2020 12:54
     */
    public function getResponse(Environment $twig): Response
    {
        if ($this->getName() === null || !$this->isFileValid()) {
            return new RedirectResponse($this->getUrl('begin'));
        }
        return new RedirectResponse($this->getUrl('basic_settings'));


        return new Response($twig->render('base.html.twig', ['manager' => $this]));
    }

    /**
     * getGenerator
     * @return UrlGeneratorInterface
     * 10/12/2020 13:11
     */
    public function getGenerator(): UrlGeneratorInterface
    {
        return $this->generator;
    }

    /**
     * setGenerator
     * @param UrlGeneratorInterface $generator
     * @return $this
     * 10/12/2020 13:11
     */
    public function setGenerator(UrlGeneratorInterface $generator): TimetableManager
    {
        $this->generator = $generator;
        return $this;
    }

    /**
     * getUrl
     * @param string|null $name
     * @param array $parameters
     * @param bool $schemeRelative
     * @return string|null
     * 10/12/2020 12:57
     */
    public function getUrl(?string $name, array $parameters = [], bool $schemeRelative = false): ?string
    {
        if ($name === null)
            return null;
        try {
            return $this->getGenerator()->generate($name, $parameters, $schemeRelative ? UrlGeneratorInterface::ABSOLUTE_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
        } catch (InvalidParameterException | MissingMandatoryParametersException | RouteNotFoundException $e) {
            return null;
        }
    }

    /**
     * isFileValid
     * @return bool
     * 10/12/2020 14:51
     */
    public function isFileValid(): bool
    {
        $validator = $this->getValidateManager();
        return $validator ? $validator->isFileValid() : false;
    }

    /**
     * createFile
     * @return mixed
     * 11/12/2020 09:06
     */
    public function createFile()
    {
        return $this->getDataManager()->createFile($this->getName());
    }

    /**
     * getDataManager
     * @param array $data
     * @return TimetableDataManager|null
     * 11/12/2020 09:06
     */
    public function getDataManager(array $data = []): ?TimetableDataManager
    {
        if ($data !== []) $this->dataManager->setData($data);
        return isset($this->dataManager) ? $this->dataManager : null;
    }

    /**
     * DataManager.
     *
     * @param TimetableDataManager $dataManager
     * @return TimetableManager
     */
    public function setDataManager(TimetableDataManager $dataManager): TimetableManager
    {
        $this->dataManager = $dataManager;
        return $this;
    }

    /**
     * getDataManager
     * @return TimetableDataManager|null
     * 11/12/2020 09:06
     */
    public function getValidateManager(): ?TimetableValidatorManager
    {
        return isset($this->validatorManager) ? $this->validatorManager : null;
    }

    /**
     * ValidatorManager.
     *
     * @param TimetableValidatorManager $validatorManager
     * @return TimetableManager
     */
    public function setValidatorManager(TimetableValidatorManager $validatorManager): TimetableManager
    {
        $this->validatorManager = $validatorManager;
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
     * getRequestStack
     * @return RequestStack
     * 13/12/2020 09:35
     */
    public function getRequestStack(): RequestStack
    {
        return $this->stack;
    }

    /**
     * setRequestStack
     * @param RequestStack $stack
     * @return $this
     * 13/12/2020 09:50
     */
    public function setRequestStack(RequestStack $stack): TimetableManager
    {
        $this->stack = $stack;
        return $this;
    }

    /**
     * load
     * @return $this
     * 13/12/2020 09:50
     */
    public function load(): TimetableManager
    {
        $this->getDataManager()->setData(Yaml::parse(file_get_contents($this->getFilePath())));
        return $this;
    }

    /**
     * handleBasicRequest
     * @param FormInterface $form
     * @param Request $request
     * 13/12/2020 15:52
     */
    public function handleBasicRequest(FormInterface $form, Request $request): TimetableManager
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
dump($data);
        }

        return $this->addFlashMessages($this->getSession()->getBag('flashes'));
    }

    /**
     * getMessages
     * @return array
     * 13/12/2020 15:47
     */
    public function getMessages(): array
    {
        return array_merge($this->getDataManager()->getMessages(), $this->messages);
    }

    /**
     * addMessage
     * @param string $type
     * @param $message
     * @return $this
     * 13/12/2020 15:50
     */
    public function addMessage(string $type, $message): TimetableManager
    {
        $message = ['message' => $message];
        $message['type'] = $type;
        $this->messages[] = $message;
        return $this;
    }

    /**
     * addFlashMessages
     * @param FlashBagInterface $flashBag
     * @return $this
     * 13/12/2020 16:01
     */
    public function addFlashMessages(FlashBagInterface $flashBag): TimetableManager
    {
        foreach ($this->getMessages() as $message) {
            $flashBag->add($message['type'], $message['message']);
            dump($message);
        }
        return $this;
    }
}
