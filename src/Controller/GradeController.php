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
 * Date: 15/12/2020
 * Time: 08:20
 */
namespace App\Controller;

use App\Form\GradesType;
use App\Manager\TimetableManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GradeController
 * @package App\Controller
 * @author Craig Rayner <craig@craigrayner.com>
 */
class GradeController extends AbstractController
{
    /**
     * grades
     * 15/12/2020 08:44
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/grades/",name="grades")
     * @return Response
     */
    public function grades(Request $request, TimetableManager $manager): Response
    {
        $form = $this->createForm(GradesType::class, $manager->getDataManager(), ['action' => $this->generateUrl('grades')]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            $manager->setSaveOnTerminate(false);
        }

        return $this->render('Settings/grades.html.twig', ['form' => $form->createView()]);
    }

    /**
     * addGrade
     * 15/12/2020 10:15
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/grade/add/",name="grade_add")
     * @return Response
     */
    public function addGrade(Request $request, TimetableManager $manager): Response
    {
        $manager->getDataManager()->setGradeCount($manager->getDataManager()->getGradeCount() + 1);

        return $this->forward(GradeController::class.'::grades',['request' => $request, 'TimetableManager' => $manager]);
    }

    /**
     * removeGrade
     * 15/12/2020 10:15
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/grade/remove/",name="grade_remove")
     * @return Response
     */
    public function removeGrade(Request $request, TimetableManager $manager): Response
    {
        $grades = $manager->getDataManager()->getGrades();
        if ($grades->count() > 0) {
            $last = $grades->last();
            $grades->removeElement($last);
            $manager->getDataManager()->setGrades($grades);
        }

        return $this->forward(GradeController::class.'::grades',['request' => $request, 'TimetableManager' => $manager]);
    }

    /**
     * deleteGrade
     * 15/12/2020 10:14
     * @param string $grade
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/grade/{grade}/delete/",name="grade_delete")
     * @return Response
     */
    public function deleteGrade(string $grade, Request $request, TimetableManager $manager): Response
    {
        $grades = $manager->getDataManager()->removeGrade($grade);

        return $this->forward(GradeController::class.'::grades',['request' => $request, 'TimetableManager' => $manager]);
    }
}
