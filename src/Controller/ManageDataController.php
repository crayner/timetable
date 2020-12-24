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
 * Time: 08:55
 */
namespace App\Controller;

use App\Form\BasicSettingsType;
use App\Manager\TimetableManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ManageDataController
 * @package App\Controller
 * @author Craig Rayner <craig@craigrayner.com>
 */
class ManageDataController extends AbstractController
{
    /**
     * manage
     * @param TimetableManager $manager
     * @param Request $request
     * @return Response
     * @Route("/settings/basic/",name="basic_settings")
     * 13/12/2020 08:57
     */
    public function basicSettings(TimetableManager $manager, Request $request)
    {
        $form = $this->createForm(BasicSettingsType::class, $manager->getDataManager());

        $form->handleRequest($request);

        return $this->render('Settings/basic.html.twig',
            ['form' => $form->createView(), 'manager' => $manager]);
    }
}
