<?php
/**
 * Created by PhpStorm.
 *
 * timetable
 * (c) 2021 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 8/01/2021
 * Time: 10:42
 */
namespace App\Controller;

use App\Form\PeriodsType;
use App\Items\Period;
use App\Manager\TimetableManager;
use App\Provider\ProviderFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PeriodController
 *
 * @package App\Controller
 * @author Craig Rayner <craig@craigrayner.com>
 * 8/01/2021 11:12
 */
class PeriodController extends AbstractController
{
    /**
     * periods
     * 14/12/2020 14:37
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/periods/",name="periods")
     * @return Response
     */
    public function periods(Request $request, TimetableManager $manager): Response
    {
        $form = $this->createForm(PeriodsType::class, $manager->getDataManager(), ['action' => $this->generateUrl('periods')]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            $manager->setSaveOnTerminate(false);
        }

        return $this->render('Settings/periods.html.twig', ['form' => $form->createView()]);
    }

    /**
     * addPeriod
     * 14/12/2020 15:47
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/period/add/",name="period_add")
     * @return Response
     */
    public function addPeriod(Request $request, TimetableManager $manager): Response
    {
        $period = new Period();
        $periods = $manager->getDataManager()->getPeriods();
        $last = $periods->last();
        $period->setName('New Period')->setSequence($last->getSequence() + 1);
        $periods->add($period);
        $manager->getDataManager()->setPeriods($periods);

        return $this->forward(PeriodController::class.'::periods',['request' => $request, 'TimetableManager' => $manager]);
    }

    /**
     * removePeriod
     * 22/12/2020 09:11
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/period/remove/",name="period_remove")
     * @return Response
     */
    public function remove(Request $request, TimetableManager $manager): Response
    {
        $periods = $manager->getDataManager()->getPeriods();
        if ($periods->count() > 0) {
            $last = $periods->last();
            $periods->removeElement($last);
            $manager->getDataManager()->setPeriods($periods);
        }

        return $this->forward(PeriodController::class.'::periods',['request' => $request, 'TimetableManager' => $manager]);
    }

    /**
     * removePeriod
     * 14/12/2020 15:47
     * @param string $period
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/period/{period}/delete/",name="period_delete")
     * @return Response
     */
    public function delete(string $period, Request $request, TimetableManager $manager): Response
    {
        $period = ProviderFactory::create(Period::class)->find($period);
        if ($period instanceof Period) {
            $periods = $manager->getDataManager()->getPeriods();
            $periods->removeElement($period);
            $manager->getDataManager()->setPeriods($periods);
            foreach ($manager->getDataManager()->getDays() as $day) {
                $periods = $day->getPeriods();
                $periods->removeElement($period);
                $day->setPeriods($periods);
            }
        }

        return $this->forward(PeriodController::class.'::periods', ['request' => $request, 'TimetableManager' => $manager]);
    }
}