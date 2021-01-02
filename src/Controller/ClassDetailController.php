<?php
/**
 * Created by PhpStorm.
 *
 * Timetable Creator
 * (c) 2020-2021 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Licence: MIT
 * User: Craig Rayner
 * Date: 1/01/2021
 * Time: 11:03
 */
namespace App\Controller;

use App\Form\ClassDetailsType;
use App\Items\ClassDetail;
use App\Manager\TimetableManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ClassDetailController
 * @package App\Controller
 * @author Craig Rayner <craig@craigrayner.com>
 */
class ClassDetailController extends AbstractController
{
    /**
     * grades
     * 15/12/2020 08:44
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/class/details/",name="class_details")
     * @Route("/class/details/",name="class_delete")
     * @return Response
     */
    public function details(Request $request, TimetableManager $manager): Response
    {
        $form = $this->createForm(ClassDetailsType::class, $manager->getDataManager(), ['action' => $this->generateUrl('class_details')]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            $manager->setSaveOnTerminate(false);
        }

        return $this->render('Settings/classes.html.twig', ['form' => $form->createView()]);
    }

    /**
     * add
     * 1/01/2021 11:42
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/class/add/",name="class_add")
     * @return Response
     */
    public function add(Request $request, TimetableManager $manager): Response
    {
        $class = new ClassDetail();
        $class->setName('CL'. str_pad(strval($manager->getDataManager()->getClasses()->count() + 1), 4, '0', STR_PAD_LEFT));
        $classes = $manager->getDataManager()->getClasses();
        $classes->add($class);
        $manager->getDataManager()->setClasses($classes);

        return $this->forward(ClassDetailController::class.'::details',['request' => $request, 'TimetableManager' => $manager]);
    }

    /**
     * remove
     * 1/01/2021 12:13
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/class/remove/",name="class_remove")
     * @return Response
     */
    public function remove(Request $request, TimetableManager $manager): Response
    {
        $classes = $manager->getDataManager()->getClasses();
        if ($classes->count() > 0) {
            $last = $classes->last();
            $classes->removeElement($last);
            $manager->getDataManager()->setClasses($classes);
        }

        return $this->forward(ClassDetailController::class.'::details',['request' => $request, 'TimetableManager' => $manager]);
    }
}
