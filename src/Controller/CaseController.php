<?php

namespace App\Controller;

use App\Document\CaseFile;
use App\Document\Person;
use App\Form\CaseType;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Doctrine\ODM\MongoDB\DocumentManager;

class CaseController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {
        $context = array();
        $message = $request->query->get('message');
        if ($message) {
            $context['message'] = $message;
        }
        return $this->render("cases/index.html.twig", $context);
    }

    /**
     * @Route("/newcase", name="newcase")
     */
    public function newCaseFile(Request $request, DocumentManager $dm)
    {
        $caseFile = new CaseFile();

        $form = $this->createForm(CaseType::class, $caseFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $caseFile = $form->getData();
            $dm->persist($caseFile);
            $dm->flush();

            return $this->redirectToRoute('home', ['message' => 'Created New Case!']);
        }

        return $this->render("cases/newcase.html.twig", [
            'form' => $form->createView(),
        ]);
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
}