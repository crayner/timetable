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
 * Date: 13/12/2020
 * Time: 07:42
 */
namespace App\Listener;

use App\Manager\DataManager;
use App\Manager\ValidatorManager;
use App\Provider\ProviderFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class SaveTimetableListener
 * @package App\Listener
 * @author Craig Rayner <craig@craigrayner.com>
 */
class SaveTimetableListener implements EventSubscriberInterface
{
    const IGNORE_CONTROLLER_METHODS = [
        'App\Controller\DefaultController::begin',
        'web_profiler.controller.profiler::toolbarAction',
        'web_profiler.controller.profiler::panelAction',
        'web_profiler.controller.profiler::openAction',
        'error_controller',
        'App\Controller\DefaultController::createTimetable',
        'App\Controller\DefaultController::load'];

    const IGNORE_ROUTE = [
        '_profiler'
    ];

    /**
     * @var DataManager
     */
    private DataManager $manager;

    /**
     * @var ValidatorManager
     */
    private ValidatorManager $validator;

    /**
     * SaveTimetableListener constructor.
     * @param DataManager $manager
     * @param ProviderFactory $factory
     * @param ValidatorManager $validator
     */
    public function __construct(DataManager $manager, ProviderFactory $factory, ValidatorManager $validator)
    {
        $this->manager = $manager;
        $this->validator = $validator;
        $this->validator->setDataManager($this->manager);
    }

    /**
     * getSubscribedEvents
     * @return array[]
     * 13/12/2020 07:44
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => ['onTerminate', 0],
            KernelEvents::REQUEST => ['onRequest', 0],
        ];
    }

    /**
     * onTerminate
     * @param TerminateEvent $event
     * 13/12/2020 07:46
     */
    public function onTerminate(TerminateEvent $event)
    {
        if (!$event->getRequest()->hasSession()) return;
        $request = $event->getRequest();
        if (in_array($request->attributes->get('_controller'), self::IGNORE_CONTROLLER_METHODS)) return;
        if ($request->attributes->has('_route') && $this->isRouteIgnored($request->attributes->get('_route'))) return;
        if ($this->manager->isSaveOnTerminate()) $this->manager->writeFile();
    }

    /**
     * onRequest
     * 1/01/2021 13:09
     * @param RequestEvent $event
     */
    public function onRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if (in_array($request->attributes->get('_controller'), self::IGNORE_CONTROLLER_METHODS)) return;
        if ($request->attributes->has('_route') && $this->isRouteIgnored($request->attributes->get('_route'))) return;

        if (!$request->hasSession() || !$request->getSession()->has('_security_user')) {
            $response = new RedirectResponse("/begin/");
            $event->setResponse($response);
            return;
        }
        $user = $request->getSession()->get('_security_user');

        $this->manager->setName($user->name);

        if (!$this->validator->isFileValid() || $user->name !== $this->manager->getName() || $user->password !== $this->manager->getPassword()) {
            $response = new RedirectResponse("/begin/");
            $event->setResponse($response);
        }
    }

    /**
     * isRouteIgnored
     * 24/12/2020 07:58
     * @param string $route
     * @return bool
     */
    private function isRouteIgnored(string $route): bool
    {
        foreach (self::IGNORE_ROUTE as $item) {
            if (0 === strpos($route, $item)) return true;
        }
        return false;
    }
}
