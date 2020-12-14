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
 * Date: 14/12/2020
 * Time: 12:14
 */
namespace App\Controller;

use App\Form\DaysType;
use App\Manager\TimetableManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DayController
 * @package App\Controller
 * @author Craig Rayner <craig@craigrayner.com>
 */
class DayController extends AbstractController
{
    /**
     * days
     * 14/12/2020 14:37
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/days/",name="days")
     * @return Response
     */
    public function days(Request $request, TimetableManager $manager): Response
    {
        $form = $this->createForm(DaysType::class, $manager->getDataManager(), ['action' => $this->generateUrl('days')]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            $manager->setSaveOnTerminate(false);
        }

        return $this->render('Settings/days.html.twig', ['form' => $form->createView()]);
    }

    /**
     * addDay
     * 14/12/2020 15:47
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Manager\TimetableManager $manager
     * @Route("/day/add/",name="day_add")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addDay(Request $request, TimetableManager $manager): Response
    {
        $manager->getDataManager()->setDayCount($manager->getDataManager()->getDayCount() + 1);

        return $this->forward(DayController::class.'::days',['request' => $request, 'TimetableManager' => $manager]);
    }

    /**
     * removeDay
     * 14/12/2020 15:47
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Manager\TimetableManager $manager
     * @Route("/day/remove/",name="day_remove")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeDay(Request $request, TimetableManager $manager): Response
    {
        $manager->getDataManager()->setDayCount($manager->getDataManager()->getDayCount() - 1);

        return $this->forward(DayController::class.'::days',['request' => $request, 'TimetableManager' => $manager]);
    }

    /**
     * removeDay
     * 14/12/2020 15:47
     * @param int $key
     * @param Request $request
     * @param TimetableManager $manager
     * @return Response
     * @Route("/day/{key}/delete/",name="day_delete")
     */
    public function deleteDay(int $key, Request $request, TimetableManager $manager): Response
    {
        $manager->getDataManager()->removeDay($key);

        return $this->forward(DayController::class.'::days',['request' => $request, 'TimetableManager' => $manager]);
    }
}