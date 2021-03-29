<?php

namespace App\Form\DataTransformer;

use App\Document\CaseFile;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RelatedCaseTransformer implements DataTransformerInterface
{
    private $dm;

    public function __construct(DocumentManager $dm) {
        $this->dm = $dm;
    }
    
    /**
     * Retrieve the id for a given CaseFile
     * 
     * @param array|null $caseFile, 
     */
    public function transform($caseFile): ?string {
        return strval($caseFile);
    }

    /**
     * Retrieve the CaseFile for agiven id
     * 
     * @param \MongoDB\BSON\ObjectId $caseId
     * @throws TransformationFailedException if can't find case for id
     */
    public function reverseTransform($caseId): ?CaseFile {
        return $this->dm->getRepository(CaseFile::class)->find($caseId);
    }
}
