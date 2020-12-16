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
 * Date: 16/12/2020
 * Time: 15:12
 */
namespace App\Controller;

use App\Form\LinesType;
use App\Items\Line;
use App\Manager\TimetableManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LineController extends AbstractController
{
    /**
     * manage
     * 16/12/2020 17:51
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/lines/",name="lines")
     * @return Response
     */
    public function manage(Request $request, TimetableManager $manager)
    {
        $form = $this->createForm(LinesType::class, $manager->getDataManager()->getLineManager());

        $form->handleRequest($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            $manager->setSaveOnTerminate(false);
        }

        return $this->render('Lines/lines.html.twig', ['form' => $form->createView()]);
    }

    /**
     * addLine
     * 16/12/2020 16:28
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/line/add/",name="line_add")
     * @return Response
     */
    public function addLine(Request $request, TimetableManager $manager): Response
    {
        $line = new Line();
        $manager->getDataManager()->getLineManager()->addLine($line);



        return $this->forward(LineController::class.'::manage',['request' => $request, 'TimetableManager' => $manager]);
    }

    /**
     * removeLine
     * 15/12/2020 10:15
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/line/remove/",name="line_remove")
     * @return Response
     */
    public function removeLine(Request $request, TimetableManager $manager): Response
    {
        $manager->getDataManager()->setLineCount($manager->getDataManager()->getLineCount() - 1);

        return $this->forward(LineController::class.'::manage',['request' => $request, 'TimetableManager' => $manager]);
    }

    /**
     * deleteLine
     * 15/12/2020 10:14
     * @param int $key
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/line/{key}/delete/",name="line_delete")
     * @return Response
     */
    public function deleteLine(int $key, Request $request, TimetableManager $manager): Response
    {
        $manager->getDataManager()->removeLine($key);

        return $this->forward(LineController::class . '::manage', ['request' => $request, 'TimetableManager' => $manager]);
    }
}
