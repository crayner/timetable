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
 * Date: 10/12/2020
 * Time: 12:21
 */
namespace App\Controller;

use App\Form\CreateTimetableType;
use App\Form\LoadTimetableType;
use App\Form\NewTimetableType;
use App\Helper\SecurityEncoder;
use App\Manager\TimetableManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;


/**
 * Class DefaultController
 * @package App\Controller
 * @author Craig Rayner <craig@craigrayner.com>
 */
class DefaultController extends AbstractController
{
    /**
     * begin
     * @param TimetableManager $manager
     * @param Request $request
     * @return Response
     * @Route("/",name="home")
     * @Route("/begin/",name="begin")
     * 10/12/2020 12:37
     */
    public function begin(TimetableManager $manager, Request $request)
    {
        $form = $this->createForm(NewTimetableType::class);
        $loadForm = $this->createForm(LoadTimetableType::class, null, ['action' => $this->generateUrl('load')]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($name = $form->get('name')->getData())) {
                $manager->setName($name);
                if ($manager->isFileValid()) {
                    if (!empty($password = $form->get('password')->getData())) {
                        $security = new SecurityEncoder();
                        if ($security->isPasswordValid($manager->getDataManager()->getPassword(), $password)) {
                            $user = new \stdClass();
                            $user->name = $name;
                            $user->password = $manager->getDataManager()->getPassword();
                            $session = $request->getSession();
                            $session->set('_security_user', $user);
                            $session->set('timetable_name', $name);
                            return $this->redirectToRoute('basic_settings');
                        }
                        $this->addFlash('alert', ['The password provided is not valid for the data file "{name}"', ['{name}' => $name]]);
                        return $this->redirectToRoute('begin');
                    }

                    $this->addFlash('alert', ['The name "{name}" is already in use.  Use a different name or provide the required password.', ['{name}' => $name]]);
                    return $this->redirectToRoute('begin');
                }
            }

            if ($manager->isNameValid() && !$manager->getDataManager()->isFileAvailable()) {
                return $this->redirectToRoute('create_timetable', ['name' => $name]);
            }
        }

        return $this->render('welcome-unknown.html.twig', ['form' => $form->createView(), 'loadForm' => $loadForm->createView()]);
    }

    /**
     * load
     * @param Request $request
     * @param TimetableManager $manager
     * @return RedirectResponse
     * @Route("/load/",name="load")
     * 12/12/2020 08:43
     */
    public function load(Request $request, TimetableManager $manager)
    {
        $form = $this->createForm(LoadTimetableType::class, null, ['action' => $this->generateUrl('load')]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dataFile = $form->get('dataFile')->getData();
            if ($dataFile instanceof UploadedFile) {
                $originalFilename = pathinfo($dataFile->getClientOriginalName(), PATHINFO_FILENAME);

                try {
                    $data = Yaml::parse($dataFile->getContent());
                } catch (ParseException $e) {
                    $data = [];
                }

                if (key_exists('name', $data) && $data['name'] === $originalFilename) {
                    $manager->setName($data['name']);
                    $dataFile->move(dirname($manager->getDataManager()->getFileName()), $data['name']. '.yaml');
                    if ($manager->getValidator()->isFileValid(true)) {
                        $request->getSession()->set('_security_user', $manager->getUser());
                        $request->getSession()->set('timetable_name', $manager->getName());
                        return $this->redirectToRoute('basic_settings');
                    } else {
                        if (!empty($password = $form->get('password')->getData())) {
                            if ($manager->getDataManager()->getEncoder()->isPasswordValid($manager->getDataManager()->getPassword(), $password)) {
                                $manager->getDataManager()->setSecret($manager->getSecret());
                                $request->getSession()->set('_security_user', $manager->getUser());
                                $request->getSession()->set('timetable_name', $manager->getName());
                                return $this->redirectToRoute('basic_settings');
                            }
                        }
                        $this->addFlash('alert', "The file is not valid. Use the password of the file to validate the file.");
                        $manager->setSaveOnTerminate(false)->getDataManager()->unlink();
                    }
                }
            }
        } else {
            $this->addFlash('alert', "The file was not available to upload");
        }

        return $this->forward(DefaultController::class.'::begin', ['request' => $request, 'manager' => $manager]);
    }

    /**
     * download
     * @param Request $request
     * @Route("/download/",name="download")
     * @return BinaryFileResponse|RedirectResponse
     * 12/12/2020 11:44
     */
    public function download(Request $request)
    {
        $name = $request->getSession()->get('timetable_name', null);
        if (is_null($name)) {
            $this->addFlash('alert', 'The timetable data is not available to download.');
            return $this->redirectToRoute('home');
        }

        $path = realpath(__DIR__ . '/../../config/data/' . $name . '.yaml');
        try {
            $response = new BinaryFileResponse($path);
            // To generate a file download, you need the mimetype of the file
            $mimeTypeGuesser = new FileinfoMimeTypeGuesser();

            // Set the mimetype with the guesser or manually
            if($mimeTypeGuesser->isGuesserSupported()){
                // Guess the mimetype of the file according to the extension of the file
                $response->headers->set('Content-Type', $mimeTypeGuesser->guessMimeType($path));
            }else{
                // Set the mimetype of the file manually, in this case for a text file is text/plain
                $response->headers->set('Content-Type', 'text/plain');
            }

            // Set content disposition inline of the file
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                basename($path)
            );

            return $response;

        } catch (FileException $e) {
            $this->addFlash('alert', 'The file for the timetable data was not found.');
            return $this->redirectToRoute('home');
        }
    }

    /**
     * createFile
     * 19/12/2020 09:41
     * @param string $name
     * @param TimetableManager $manager
     * @param Request $request
     * @return Response
     * @Route("/timetable/{name}/create/", name="create_timetable")
     */
    public function createTimetable(string $name, TimetableManager $manager, Request $request): Response
    {
        $manager->getDataManager()->setName($name);
        $form = $this->createForm(CreateTimetableType::class, $manager->getDataManager());
        $loadForm = $this->createForm(LoadTimetableType::class, null, ['action' => $this->generateUrl('load')]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = $request->getSession();
            $manager->setName($form->get('name')->getData());
            if ($manager->getDataManager()->encodePassword($form->get('password')->getData())) {
                $user = $manager->getUser();
                $session->set('_security_user', $user);
                $session->set('timetable_name', $manager->getName());
                return $this->redirectToRoute('basic_settings');
            }
        }

        return $this->render('Settings\create_timetable.html.twig', ['form' => $form->createView(), 'loadForm' => $loadForm->createView(), 'name' => $name]);
    }

    /**
     * close
     * 23/12/2020 09:58
     * @param Request $request
     * @param TimetableManager $manager
     * @Route("/timetable/close/",name="close")
     * @return Response
     */
    public function close(Request $request, TimetableManager $manager): Response
    {
        $manager->getDataManager()->unlink();
        $manager->setSaveOnTerminate(false);
        $request->getSession()->invalidate();

        return $this->redirectToRoute('begin');
    }
}
