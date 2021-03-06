<?php

namespace App\Controller;

use App\Document\CaseFile;
use App\Document\Person;
use App\Form\CaseType;

use App\Service\UploaderHelper;
use App\Service\CaseGenerator;
use App\Service\PersonFilesBuilder;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

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
     * @Route("/fetchcases", name="fetchcases", options={"expose": true})
     */
    public function fetchCaseFiles(Request $request, DocumentManager $dm): Response {
        $cases = $dm->getRepository(CaseFile::class)->findAllOrderByDate();

        $encoder = new JsonEncoder();
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader())); // In order to reckognize @Group annotations
        $normalizer = new ObjectNormalizer($classMetadataFactory, null, null, new ReflectionExtractor());
        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer], [$encoder]);

        // dd($cases);
        $json = $serializer->serialize($cases, 'json', ['groups' => 'list_cases']);
        $response = new Response($json, Response::HTTP_OK);
        $response->headers->set('Content-type', 'application/json');
        return $response;
    }

    /**
     * @Route("/case/{id}", name="viewcase", requirements={"id"="[\d\w]+"}, options={"expose"=true})
     */
    public function viewCaseFile(Request $request, DocumentManager $dm, $id)
    {
        $repo = $dm->getRepository(CaseFile::class);
        $mongoId = new \MongoDB\BSON\ObjectId($id);
        $caseFile = $repo->find($mongoId);
        $relatedCases = $repo->getRelatedCaseObjects($mongoId);

        if (!$caseFile) {
            throw $this->createNotFoundException('Could not find Case with id: ' . $id);
        }

        return $this->render("cases/viewcase.html.twig", [
            "case" => $caseFile,
            'related_cases' => $relatedCases,
        ]);
    }

    /**
     * @Route("/newcase", name="newcase")
     */
    public function newCaseFile(Request $request, DocumentManager $dm, UploaderHelper $uploaderHelper)
    {
        $caseFile = new CaseFile();
        $repo = $dm->getRepository(CaseFile::class);

        $form = $this->createForm(CaseType::class, $caseFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $video */
            $video = $form->get('video_file')->getData();

            /** @var UploadedFile $image */
            $primary_image = $form->get('primary_person')->get('image_file')->getData();

            if ($video) {
                $newVideoFileName = $uploaderHelper->uploadVideoFile($video);
                $caseFile->setVideo($newVideoFileName);
            }

            if ($primary_image) {
                $newImageFileName = $uploaderHelper->uploadImageFile($primary_image);
                $caseFile->getPrimaryPerson()->setImage($newImageFileName);
            }

            foreach ($form->get('associated_persons') as $assocPerson) {

                /** @var UploadedFile $assocImage  */
                $assocImage = $assocPerson->get('image_file')->getData();

                if ($assocImage) {
                    $newAssocImageFileName = $uploaderHelper->uploadImageFile($assocImage);
                    $caseFile->getAssociatedPersonByName($assocPerson->getData()->getName())->setImage($newAssocImageFileName);
                }
            }

            $caseFile = $form->getData();

            // Added related cases separately since they are unmapped in the form
            $relatedCases = array();
            foreach ($form->get('related_cases') as $relatedCase) {
                $relatedCaseId = $relatedCase->get('case_id')->getData();
                $relatedCases[] = new \MongoDB\BSON\ObjectId($relatedCaseId);
            }

            $caseFile->setRelatedCases($relatedCases);
            
            $dm->persist($caseFile);
            $dm->flush();

            // Have to connect related cases to this one separately because ID isn't generated until post flush
            $this->cascadeRelatedCases($caseFile, $dm);

            return $this->redirectToRoute('home', ['message' => 'Created New Case!']);
        }

        return $this->render("cases/newcase.html.twig", [
            'form' => $form->createView(),
            'form_title' => 'New Case',
        ]);
    }

    private function cascadeRelatedCases($caseFile, &$dm) {
        $repo = $dm->getRepository(CaseFile::class);

        foreach ($caseFile->getRelatedCases() as $relatedCaseId) {
            $mongoId = new \MongoDB\BSON\ObjectId($caseFile->getId());
            $repo->find($relatedCaseId)->addRelatedCase($mongoId);
        }

        $dm->flush();
    }

    /**
     * @Route("/editcase/{id}", name="editcase", requirements={"id"="[\d\w]+"})
     */
    public function editCaseFile(Request $request, DocumentManager $dm, UploaderHelper $uploaderHelper, $id) {
        $mongoId = new \MongoDB\BSON\ObjectId($id);
        $repo = $dm->getRepository(CaseFile::class);
        $caseFile = $repo->find($mongoId);

        if (!$caseFile) {
            throw $this->createNotFoundException('Could not find Case with id: ' . $id);
        }

        $form = $this->createForm(CaseType::class, $caseFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $video */
            $video = $form->get('video_file')->getData();

            /** @var UploadedFile $image */
            $primary_image = $form->get('primary_person')->get('image_file')->getData();

            if ($video) {
                $newVideoFileName = $uploaderHelper->uploadVideoFile($video);
                $caseFile->setVideo($newVideoFileName);
            }

            if ($primary_image) {
                $newImageFileName = $uploaderHelper->uploadImageFile($primary_image);
                $caseFile->getPrimaryPerson()->setImage($newImageFileName);
            }

            foreach ($form->get('associated_persons') as $assocPerson) {

                /** @var UploadedFile $assocImage  */
                $assocImage = $assocPerson->get('image_file')->getData();

                if ($assocImage) {
                    $newAssocImageFileName = $uploaderHelper->uploadImageFile($assocImage);
                    $caseFile->getAssociatedPersonByName($assocPerson->getData()->getName())->setImage($newAssocImageFileName);
                }
            }

            $caseFile = $form->getData();

            // Added related cases separately since they are unmapped in the form
            $relatedCases = array();
            foreach ($form->get('related_cases') as $relatedCase) {
                $relatedCaseId = $relatedCase->get('case_id')->getData();
                $relatedCases[] = new \MongoDB\BSON\ObjectId($relatedCaseId);
                $repo->find($relatedCaseId)->addRelatedCase($mongoId);
            }

            $caseFile->setRelatedCases($relatedCases);

            $dm->persist($caseFile);
            $dm->flush();

            return $this->redirectToRoute('home', ['message' => $caseFile->getDescription() . ' has been updated!']);
        }

        $context = [
            'form' => $form->createView(),
            'form_title' => 'Edit Case',
            'video_filename' => $this->getOriginalUploadFilename($caseFile->getVideo()),
            'image_list' => $this->makePersonsImageList($caseFile),
        ];

        return $this->render("cases/editcase.html.twig", $context);
    }

    /**
     * @Route("/fetchrelated/{id}", name="fetchrelated", requirements={"id"="[\d\w]+"}, options={"expose": true})
     */
    public function fetchRelatedCases(DocumentManager $dm, $id) {
        $mongoId = new \MongoDB\BSON\ObjectId($id);
        $repo = $dm->getRepository(CaseFile::class);
        $caseFile = $repo->find($mongoId);

        $relatedCases = array();
        foreach ($caseFile->getRelatedCases() as $relCaseId) {
            $relatedCases[] = array(
                'id' => strval($relCaseId),
                'description' => $repo->find($relCaseId)->getDescription() 
            );
        }

        return $this->json($relatedCases);
    }

    // Collect a list of image filenames for each person in a case
    // To be used when rendering the edit form for a case
    private function makePersonsImageList($caseFile) {
        $images = array();
        $primary_person = $caseFile->getPrimaryPerson();
        $primary_image = $this->getOriginalUploadFilename($caseFile->getPrimaryPerson()->getImage());

        if ($primary_image) {
            $images[$primary_person->getName()] = $primary_image;
        } else {
            $images[$primary_person->getName()] = null;
        }

        $additional_persons = $caseFile->getAssociatedPersons();
        
        foreach ($additional_persons as $person) {
            $imageFilename = $this->getOriginalUploadFilename($person->getImage());

            if ($imageFilename) {
                $images[$person->getName()] = $imageFilename;
            } else {
                $images[$person->getName()] = null;
            }
        }

        return $images;
    }

    private function getOriginalUploadFilename($videoFilename) {
        if (!isset($videoFilename)) {
            return null;
        }

        $parts = explode('-', $videoFilename);
        $id = array_shift($parts);
        return implode('', $parts);
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
     * @Route("/primary-persons/{name}", options={"expose"=true}, name="primary_persons")
     */
    public function getPrimaryPersons(DocumentManager $dm, $name): Response {
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

    /**
     * @Route("/generate", name="generate")
     */
    public function generateCases(Request $request, DocumentManager $dm)
    {
        /**
         * Generates dummy case files using a set of random names, ipsum content, choice of images and video.
         * Create 100 dummy cases
         * 50% shoplifting
         * 10% workplace injury
         * 10% vehicle accident
         * 10% aggressive behavior
         * 5% everything else except
         */

        $cases = array();
        $generator = new CaseGenerator();
        $batchSize = 20;

        for ($i = 0; $i < 100; $i++) {
            switch ($i) {
                case 0:
                    $category = 'Shoplifting';
                    break;
                case 50:
                    $category = 'Workplace Injury';
                    break;
                case 60:
                    $category = 'Vehicle Accident';
                    break;
                case 70:
                    $category = 'Aggressive Behavior';
                    break;
                case 80:
                    $category = 'Harrasment';
                    break;
                case 85:
                    $category = 'Foodborne Illness';
                    break;
                case 90:
                    $category = 'Medical Emergency';
                    break;
                case 95:
                    $category = 'Vandalism';
                    break;
            }

            $dateAdd = strval(rand(0, 90));
            $caseDate = new \Datetime('2021-01-12');
            $caseDate->add(new \DateInterval('P' . $dateAdd . 'D'));

            $newCase = $generator->generateCaseFile($category, $caseDate);
            $dm->persist($newCase);
            $cases[] = $newCase;

            if ($i % $batchSize == 0) {
                $dm->flush();
            }
        }

        $dm->flush();
        
        $context = ['cases' => $cases];
        return $this->render("cases/generate.html.twig", $context);
    }
}
