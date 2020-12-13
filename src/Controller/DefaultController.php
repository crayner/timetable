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

use App\Form\LoadTimetableType;
use App\Form\NewTimetableType;
use App\Manager\TimetableManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
     * default
     * 10/12/2020 12:30
     * @param TimetableManager $manager
     * @Route("/",name="home")
     * @return Response
     */
    public function home(TimetableManager $manager)
    {
        return $manager->getResponse($this->get('twig'));
    }

    /**
     * begin
     * @param TimetableManager $manager
     * @param Request $request
     * @return Response
     * @Route("/begin/",name="begin")
     * 10/12/2020 12:37
     */
    public function begin(TimetableManager $manager, Request $request)
    {
        $form = $this->createForm(NewTimetableType::class);
        $loadForm = $this->createForm(LoadTimetableType::class, null, ['action' => $this->generateUrl('load')]);

        if ($request->getMethod() === 'POST') $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($name = $form->get('name')->getData())) {
                if (file_exists(__DIR__ . '/../../config/data/' . $name . '.yaml')) {
                    $data = Yaml::parse(file_get_contents(__DIR__ . '/../../config/data/' . $name . '.yaml'));
                    if ($data['created_at'] > date('c', strtotime('-1 day'))) {
                        $this->addFlash('alert', ['The name "{name}" is not available.  Use a different name.', ['{name}' => $name]]);
                        return $this->redirectToRoute('home');
                    }
                }

                $manager->setName($name);
            }

            if ($manager->isNameValid() && !$manager->isFileValid()) {
                $manager->createFile();
                return $this->redirectToRoute('home');
            }
        }

        return $this->render('welcome-unknown.html.twig', ['form' => $form->createView(),'loadForm' => $loadForm->createView()]);
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
        $form = $this->createForm(LoadTimetableType::class, null, ['action' => $this->generateUrl('home')]);

        if ($request->getMethod() === 'POST') $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dataFile = $form->get('dataFile')->getData();
            if ($dataFile) {
                $originalFilename = pathinfo($dataFile->getClientOriginalName(), PATHINFO_FILENAME);

                try {
                    $data = Yaml::parse($dataFile->getContent());
                } catch (ParseException $e) {
                    $data = [];
                }

                if (key_exists('name', $data) && $data['name'] === $originalFilename) {
                    $manager->setName($originalFilename);
                    $dataManager = $manager->getDataManager($data);
                    $dataManager->setInjectMissing();
                    if (!$manager->isFileValid()) {
                        $dataManager->unlink();
                    } else {
                        $manager->setName($dataManager->getName());
                    }
                }
            }
        } else {
            $this->addFlash('alert', "The file was not available to upload");
        }
        return $this->redirectToRoute('home');
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
}
