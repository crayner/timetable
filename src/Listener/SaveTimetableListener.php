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
        ];
    }

    /**
     * onTerminate
     * @param TerminateEvent $event
     * 13/12/2020 07:46
     */
    public function onTerminate(TerminateEvent $event)
    {
        $this->manager->getDataManager()->writeFile();
    }
}
