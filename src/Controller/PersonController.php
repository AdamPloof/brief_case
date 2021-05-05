<?php

namespace App\Controller;

use App\Document\CaseFile;
use App\Document\Person;

use App\Service\PersonFilesBuilder;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ODM\MongoDB\DocumentManager;

class PersonController extends AbstractController
{
    /**
     * @Route("/persons/", options={"expose"=true}, name="persons")
     */
    public function viewPersons(): Response {
        return $this->render('persons/list.html.twig', [
            'title' => 'Persons',
        ]);
    }

    /**
     * @Route("/fetchpersons/", options={"expose"=true}, name="fetchpersons")
     */
    public function fetchPersons(PersonFilesBuilder $pfb): Response {
        $persons = $pfb->getAllPersonFiles();
        return $this->json($persons);
    }

    /**
     * @Route("/person/{name}", options={"expose"=true}, name="viewperson")
     */
    public function viewPerson(PersonFilesBuilder $pfb, $name): Response {
        $files = $pfb->getPersonFiles($name);
        // dd($files);
        return $this->render('persons/view.html.twig', [
            'title' => 'Person Detail',
            'files' => $files,
        ]);
    }  
}
