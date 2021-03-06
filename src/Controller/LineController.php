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

use App\Form\FullLineType;
use App\Form\LinePaginationType;
use App\Items\Line;
use App\Manager\LineManager;
use App\Manager\TimetableManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LineController
 * @package App\Controller
 * @author  Craig Rayner <craig@craigrayner.com>
 * 12/01/2021 13:01
 */
class LineController extends AbstractController
{
    /**
     * manage
     * 23/12/2020 14:41
     *
     * @param Request     $request
     * @param LineManager $manager
     * @return Response
     * @Route("/lines/",name="lines")
     */
    public function manage(Request $request, LineManager $manager): Response
    {
        $grade = $request->getSession()->has('searchGrade') ? $request->getSession()->get('searchGrade') : null;
        $form = $this->createForm(LinePaginationType::class, $manager, ['action' => $this->generateUrl('lines', ), 'grade' => $grade]);

        $manager->setGrade($grade);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $grade = $form->get('searchGrade')->getData() ?: null;
            $request->getSession()->set('searchGrade', $grade);
            $manager->setGrade($grade);
            $form = $this->createForm(LinePaginationType::class, $manager, ['action' => $this->generateUrl('lines', ), 'grade' => $grade]);
        }

        return $this->render('Lines/lines.html.twig', ['form' => $form->createView()]);
    }

    /**
     * addLine
     * 23/12/2020 14:45
     * @param Request $request
     * @param LineManager $manager
     * @Route("/line/add/",name="line_add")
     * @return Response
     */
    public function addLine(Request $request, LineManager $manager): Response
    {
        $line = new Line();
        $lines = $manager->getDataManager()->getLines();
        $line->setName('Line '.strval($lines->count() + 1));
        $lines->add($line);
        $manager->getDataManager()->setLines($lines);

        return $this->forward(LineController::class.'::manage',['request' => $request, 'manager' => $manager]);
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
        $lines = $manager->getDataManager()->getLines();
        if ($lines->count() > 0) {
            $last = $lines->last();
            $lines->removeElement($last);
            $manager->getDataManager()->setLines($lines);
        }

        return $this->forward(LineController::class.'::manage',['request' => $request, 'manager' => $manager]);
    }

    /**
     * deleteLine
     * 23/12/2020 14:46
     * @param string $line
     * @param Request $request
     * @param LineManager $manager
     * @Route("/line/{line}/delete/",name="line_delete")
     * @return Response
     */
    public function deleteLine(string $line, Request $request, LineManager $manager): Response
    {
        $items = $manager->getDataManager()->getLines();
        $lines = $items->filter(function(Line $item) use ($line) {
            if ($line === $item->getId()) return $item;
        });

        if ($lines->count() > 1) throw new \InvalidArgumentException('The database is not consistent.  Duplicate Lines.');
        if ($lines->count() === 1) $items->removeElement($lines->first());
        $manager->getDataManager()->setLines($items);

        return $this->forward(LineController::class . '::manage', ['request' => $request, 'manager' => $manager]);
    }

    /**
     * details
     * 3/01/2021 09:46
     * @param string $line
     * @param Request $request
     * @param LineManager $manager
     * @Route("/line/{line}/details/",name="line_details")
     * @return Response
     */
    public function details(string $line, Request $request, LineManager $manager)
    {
        $manager->setLine($line);
        if (empty($line)) {
            $this->addFlash('warning', 'The line was not found.');
            return $this->forward(LineController::class.'::manager', ['request' => $request, 'manager' => $manager]);
        }

        $form = $this->createForm(FullLineType::class, $manager->getline());

        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            $manager->setSaveOnTerminate(false);
        }

        return $this->render('Lines/full_line.html.twig', ['form' => $form->createView()]);
    }
}
