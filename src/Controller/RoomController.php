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
 * Time: 13:26
 */
namespace App\Controller;

use App\Form\RoomsType;
use App\Form\UploadRoomType;
use App\Form\UploadStaffType;
use App\Items\Room;
use App\Items\Staff;
use App\Manager\TimetableManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RoomController
 * @package App\Controller
 * @author Craig Rayner <craig@craigrayner.com>
 */
class RoomController extends AbstractController
{
    /**
     * rooms
     * 15/12/2020 08:44
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/rooms/",name="rooms")
     * @return Response
     */
    public function rooms(Request $request, TimetableManager $manager): Response
    {
        $form = $this->createForm(RoomsType::class, $manager->getDataManager(), ['action' => $this->generateUrl('rooms')]);
        $formUpload = $this->createForm(UploadRoomType::class, null, ['action' => $this->generateUrl('room_upload')]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            $manager->setSaveOnTerminate(false);
        }

        return $this->render('Settings/rooms.html.twig', ['form' => $form->createView(), 'formUpload' => $formUpload->createView()]);
    }

    /**
     * addRoom
     * 15/12/2020 10:15
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/room/add/",name="room_add")
     * @return Response
     */
    public function addRoom(Request $request, TimetableManager $manager): Response
    {
        $manager->getDataManager()->setRoomCount($manager->getDataManager()->getRoomCount() + 1);

        return $this->forward(RoomController::class.'::rooms',['request' => $request, 'TimetableManager' => $manager]);
    }

    /**
     * removeRoom
     * 15/12/2020 10:15
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/room/remove/",name="room_remove")
     * @return Response
     */
    public function removeRoom(Request $request, TimetableManager $manager): Response
    {
        $manager->getDataManager()->setRoomCount($manager->getDataManager()->getRoomCount() - 1);

        return $this->forward(RoomController::class.'::rooms',['request' => $request, 'TimetableManager' => $manager]);
    }

    /**
     * deleteRoom
     * 15/12/2020 10:14
     * @param string $name
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/room/{name}/delete/",name="room_delete")
     * @return Response
     */
    public function deleteRoom(string $name, Request $request, TimetableManager $manager): Response
    {
        $manager->getDataManager()->removeRoom($name);

        return $this->forward(RoomController::class.'::rooms',['request' => $request, 'TimetableManager' => $manager]);
    }

    /**
     * upload
     * 15/12/2020 14:28
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/room/upload/",name="room_upload")
     * @return Response
     */
    public function upload(Request $request, TimetableManager $manager): Response
    {
        $form = $this->createForm(UploadRoomType::class, null, ['action' => $this->generateUrl('room_upload')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dataFile = $form->get('dataFile')->getData();
            if ($dataFile) {
                $data = explode("\n", $dataFile->getContent());
                $rooms = $manager->getDataManager()->getRooms();
                foreach ($data as $q=>$name) {
                    if (!empty($name)) {
                        if ($rooms->containsKey($q)) {
                            $member = $rooms->get($q);
                        } else {
                            $member = new Room();
                        }
                        $name = explode(',',$name);
                        $member->setName($name[0]);
                        key_exists(1, $name) ? $member->setSize(intval($name[1])) : $member->setSize(30);
                        $rooms->set($q, $member);
                    } else {
                        unset($data[$q]);
                    }
                }
                $manager->getDataManager()->setRooms($rooms);
                $this->addFlash('success', ['room.upload', ['count' => count($data)]]);

            } else {
                $this->addFlash('alert', 'Invalid Data.');
            }
        } else {
            $this->addFlash('alert', 'Invalid Data.');
        }

        return $this->forward(RoomController::class.'::rooms',['request' => $request, 'TimetableManager' => $manager]);
    }

    /**
     * sort
     * 15/12/2020 13:23
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/room/sort/",name="room_sort")
     * @return Response
     */
    public function sort(Request $request, TimetableManager $manager): Response
    {
        $manager->getDataManager()->sortRooms();

        return $this->forward(RoomController::class.'::rooms',['request' => $request, 'TimetableManager' => $manager]);
    }
}
