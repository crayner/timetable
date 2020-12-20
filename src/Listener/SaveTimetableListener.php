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

use App\Manager\TimetableManager;
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
    /**
     * @var TimetableManager
     */
    private TimetableManager $manager;

    /**
     * SaveTimetableListener constructor.
     * @param TimetableManager $manager
     */
    public function __construct(TimetableManager $manager)
    {
        $this->manager = $manager;
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
        if ($this->manager->isSaveOnTerminate()) $this->manager->getDataManager()->writeFile();
    }

    /**
     * onRequest
     * 20/12/2020 08:50
     * @param RequestEvent $event
     */
    public function onRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if (in_array($request->attributes->get('_controller'), ['App\Controller\DefaultController::begin','"web_profiler.controller.profiler::toolbarAction','error_controller', 'App\Controller\DefaultController::createTimetable'])) return;

        if (!$request->hasSession() || !$request->getSession()->has('_security_user')) {
            $response = new RedirectResponse("/begin/");
            $event->setResponse($response);
            return;
        }
        $user = $request->getSession()->get('_security_user');
        $this->manager->setName($user->name);

        if (!$this->manager->isFileValid() || $user->name !== $this->manager->getDataManager()->getName() || $user->password !== $this->manager->getDataManager()->getPassword()) {
            $response = new RedirectResponse("/begin/");
            $event->setResponse($response);
        }
    }
}
