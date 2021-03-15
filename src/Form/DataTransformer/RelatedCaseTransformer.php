<?php

namespace App\Form\DataTransformer;

use App\Document\CaseFile;
use App\Repository\CaseFileRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RelatedCaseTransformer implements DataTransformerInterface
{
    /**
     * Retrieve the id for a given CaseFile
     * 
     * @param CaseFile|null $caseFile, 
     */
    public function transform($caseFile): string {
        return $caseFile->getId();
    }

    /**
     * Retrieve the CaseFile for agiven id
     * 
     * @param \MongoDB\BSON\ObjectId $caseId
     * @throws TransformationFailedException if can't find case for id
     */
    public function reverseTransform($caseId): ?CaseFile {
        $repo = new CaseFileRepository();
        return $repo->find($caseId);
    }
}
