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
 * Time: 10:46
 */
namespace App\Controller;

use App\Form\StaffMemberType;
use App\Form\UploadStaffType;
use App\Items\Staff;
use App\Manager\TimetableManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StaffController
 * @package App\Controller
 * @author Craig Rayner <craig@craigrayner.com>
 */
class StaffController extends AbstractController
{
    /**
     * staff
     * 14/12/2020 14:37
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/staff/",name="staff")
     * @return Response
     */
    public function staff(Request $request, TimetableManager $manager): Response
    {
        $form = $this->createForm(StaffMemberType::class, $manager->getDataManager(), ['action' => $this->generateUrl('staff')]);
        $formUpload = $this->createForm(UploadStaffType::class, null, ['action' => $this->generateUrl('staff_upload')]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            $manager->setSaveOnTerminate(false);
        }

        return $this->render('Settings/staff.html.twig', ['form' => $form->createView(), 'formUpload' => $formUpload->createView()]);
    }

    /**
     * addStaff
     * 14/12/2020 15:47
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/staff/add/",name="staff_add")
     * @return Response
     */
    public function addStaff(Request $request, TimetableManager $manager): Response
    {
        $manager->getDataManager()->setStaffCount($manager->getDataManager()->getStaffCount() + 1);

        return $this->forward(StaffController::class.'::staff',['request' => $request, 'TimetableManager' => $manager]);
    }

    /**
     * removeStaff
     * 14/12/2020 15:47
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/staff/remove/",name="staff_remove")
     * @return Response
     */
    public function removeStaff(Request $request, TimetableManager $manager): Response
    {
        $staff = $manager->getDataManager()->getStaff();
        if ($staff->count() > 0) {
            $last = $staff->last();
            $staff->removeElement($last);
            $manager->getDataManager()->setStaff($staff);
        }

        return $this->forward(StaffController::class.'::staff',['request' => $request, 'TimetableManager' => $manager]);
    }

    /**
     * removeStaff
     * 14/12/2020 15:47
     * @param string $staff
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/staff/{staff}/delete/",name="staff_delete")
     * @return Response
     */
    public function deleteStaff(string $staff, Request $request, TimetableManager $manager): Response
    {
        $manager->getDataManager()->removeStaff($staff);

        return $this->forward(StaffController::class.'::staff',['request' => $request, 'TimetableManager' => $manager]);
    }

    /**
     * upload
     * 22/12/2020 09:46
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/staff/upload/",name="staff_upload")
     * @return Response
     */
    public function upload(Request $request, TimetableManager $manager): Response
    {
        $form = $this->createForm(UploadStaffType::class, null, ['action' => $this->generateUrl('staff_upload')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dataFile = $form->get('dataFile')->getData();
            if ($dataFile) {
                $data = explode("\n", $dataFile->getContent());
                $staff = $manager->getDataManager()->getStaff();
                foreach ($data as $q=>$name) {
                    if (!empty($name)) {
                        if ($staff->containsKey($q)) {
                            $member = $staff->get($q);
                        } else {
                            $member = new Staff();
                        }
                        $member->setName(trim($name));
                        $staff->set($q,$member);
                    } else {
                        unset($data[$q]);
                    }
                }
                $manager->getDataManager()->setStaff($staff);
                $this->addFlash('success', ['staff.upload', ['count' => count($data)]]);

            } else {
                $this->addFlash('alert', 'Invalid Data.');
            }
        } else {
            $this->addFlash('alert', 'Invalid Data.');
        }

        return $this->forward(StaffController::class.'::staff',['request' => $request, 'TimetableManager' => $manager]);
    }

    /**
     * sort
     * 15/12/2020 13:23
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/staff/sort/",name="staff_sort")
     * @return Response
     */
    public function sort(Request $request, TimetableManager $manager): Response
    {
        $manager->getDataManager()->sortStaff();

        return $this->forward(StaffController::class.'::staff',['request' => $request, 'TimetableManager' => $manager]);
    }
}
