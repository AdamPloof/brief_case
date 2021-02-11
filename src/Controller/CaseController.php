<?php

namespace App\Controller;

use App\Document\CaseFile;
use App\Document\Person;
use App\Form\CaseType;

use App\Service\UploaderHelper;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Doctrine\ODM\MongoDB\DocumentManager;

class CaseController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, DocumentManager $dm)
    {
        $context = array();
        $message = $request->query->get('message');
        if ($message) {
            $context['message'] = $message;
        }

        $cases = $dm->getRepository(CaseFile::class)->findAll();
        $context['cases'] = $cases;

        return $this->render("cases/index.html.twig", $context);
    }

    /**
     * @Route("/case/{id}", name="viewcase", requirements={"id"="[\d\w]+"})
     */
    public function viewCaseFile(Request $request, DocumentManager $dm, $id)
    {
        $mongoId = new \MongoDB\BSON\ObjectId($id);
        $caseFile = $dm->getRepository(CaseFile::class)->find($mongoId);

        if (!$caseFile) {
            throw $this->createNotFoundException('Could not find Case with id: ' . $id);
        }

        return $this->render("cases/viewcase.html.twig", [
            "case" => $caseFile,
        ]);
    }

    /**
     * @Route("/newcase", name="newcase")
     */
    public function newCaseFile(Request $request, DocumentManager $dm, UploaderHelper $uploaderHelper)
    {
        $caseFile = new CaseFile();

        $form = $this->createForm(CaseType::class, $caseFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $video */
            $video = $form->get('video_file')->getData();

            if ($video) {
                $newFileName = $uploaderHelper->uploadVideoFile($video);
                $caseFile->setVideo($newFileName);
            }
            
            $caseFile = $form->getData();
            $dm->persist($caseFile);
            $dm->flush();

            return $this->redirectToRoute('home', ['message' => 'Created New Case!']);
        }

        return $this->render("cases/newcase.html.twig", [
            'form' => $form->createView(),
            'form_title' => 'New Case',
        ]);
    }

    /**
     * @Route("/editcase/{id}", name="editcase", requirements={"id"="[\d\w]+"})
     */
    public function editCaseFile(Request $request, DocumentManager $dm, UploaderHelper $uploaderHelper, $id) {
        $mongoId = new \MongoDB\BSON\ObjectId($id);
        $caseFile = $dm->getRepository(CaseFile::class)->find($mongoId);

        if (!$caseFile) {
            throw $this->createNotFoundException('Could not find Case with id: ' . $id);
        }

        $form = $this->createForm(CaseType::class, $caseFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $video */
            $video = $form->get('video_file')->getData();

            if ($video) {
                $newFileName = $uploaderHelper->uploadVideoFile($video);
                $caseFile->setVideo($newFileName);
            }

            $caseFile = $form->getData();
            $dm->persist($caseFile);
            $dm->flush();

            return $this->redirectToRoute('home', ['message' => $caseFile->getDescription() . ' has been updated!']);
        }

        $context = [
            'form' => $form->createView(),
            'form_title' => 'Edit Case',
            'video_filename' => $this->getOriginalVideoFilename($caseFile->getVideo())
        ];

        return $this->render("cases/editcase.html.twig", $context);
    }

    private function getOriginalVideoFilename($videoFilename) {
        if (!isset($videoFilename)) {
            return null;
        }

        $parts = explode('-', $videoFilename);
        $id = array_shift($parts);
        return implode('', $parts);
    }

    /**
     * @Route("/reports", name="reports")
     */
    public function caseReports(Request $request) {
        return $this->render('cases/reports.html.twig');
    }

    /**
     * @Route("/addcase", name="addcase")
     */
    public function addCaseFile(DocumentManager $dm)
    {
        $caseFile = new CaseFile();
        $person = new Person;

        $caseFile->setDescription("sample_case");
        $caseFile->setSummary("A test cases");
        $caseFile->setDate(new \DateTime());

        $person->setName("Test McTest");
        $person->setRole("Perp");

        $traits = array(
            "height" => "6'1\"",
            "weight" => "165lb",
            "disposition" => "mercurial"
        );

        $person->addTraits($traits);

        $caseFile->setPrimaryPerson($person);
        $caseFile->setVideo("C:\\Videos\\TestVideo");

        $dm->persist($caseFile);
        $dm->flush();

        $context = array("message" => "Test Case Created! Case: " . $caseFile->getId());

        return ($this->render("cases/index.html.twig", $context));
    }

    /**
     * @Route("/primary-persons/{name}", options={"expose"=true}, name="primary_persons", requirements={"name"="[\d\w]+"})
     */
    public function getPrimaryPersons(DocumentManager $dm, $name): Response {
        // TODO: Switch up the requirements to be able to handle white space.
        $name = new \MongoDB\BSON\Regex($name, 'i');
        $persons = array();

        $cases = $dm->createQueryBuilder(CaseFile::class)
            ->field('primary_person.name')->equals($name)
            ->sort('primary_person.name')
            ->getQuery()
            ->execute();

        foreach ($cases as $caseFile) {
            $persons[] = $caseFile->getPrimaryPerson();
        }

        return $this->json($persons);
    }
}
