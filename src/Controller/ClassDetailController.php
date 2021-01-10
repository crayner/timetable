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
use App\Items\Line;
use App\Manager\LineManager;
use App\Manager\TimetableManager;
use App\Provider\ProviderFactory;
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
     * @param LineManager $manager
     * @param string $line
     * @return Response
     * @Route("/class/{line}/details/",name="class_details")
     */
    public function details(Request $request, LineManager $manager, string $line): Response
    {
        $manager->setLine($line);
        $form = $this->createForm(ClassDetailsType::class, $manager, ['action' => $this->generateUrl('class_details', ['line' => $manager->getLine()->getId()])]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            $manager->setSaveOnTerminate(false);
        }

        return $this->render('Settings/classes.html.twig', ['form' => $form->createView()]);
    }

    /**
     * add
     * 1/01/2021 11:42
     *
     * @param string      $line
     * @param LineManager $manager
     * @Route("/class/{line}/add/",name="class_add")
     * @return Response
     */
    public function add(string $line, LineManager $manager): Response
    {
        $class = new ClassDetail();
        $manager->setLine($line);
        $class->setName($manager->getLine()->getName() . str_pad(strval($manager->getLine()->getClasses()->count() + 1), 2, '0', STR_PAD_LEFT))
            ->setLine($manager->getLine());
        $classes = $manager->getDataManager()->getClasses();
        $classes->add($class);
        $manager->getDataManager()->setClasses($classes);

        return $this->redirectToRoute('line_details', ['line' => $line]);
    }

    /**
     * remove
     * 1/01/2021 12:13
     *
     * @param string      $line
     * @param LineManager $manager
     * @Route("/class/{line}/remove/",name="class_remove")
     * @return Response
     */
    public function remove(string $line, LineManager $manager): Response
    {
        $manager->setLine($line);
        $classes = $manager->getClasses();
        if ($classes->count() > 0) {
            $last = $classes->last();
            $manager->removeClass($last);
        }

        return $this->redirectToRoute('line_details', ['line' => $line]);
    }

    /**
     * delete
     * 5/01/2021 12:29
     *
     * @param string      $line
     * @param string      $class
     * @param Request     $request
     * @param LineManager $manager
     * @Route("/class/{line}/{class}/delete/",name="class_delete")
     * @return Response
     */
    public function delete(string $line, string $class, Request $request, LineManager $manager)
    {
        $manager->setLine($line);
        if (!$manager->removeClass($class)) $this->addFlash('warning', 'The class detail was not valid!');

        return $this->forward(ClassDetailController::class.'::details',['request' => $request, 'LineManager' => $manager]);
    }
}
