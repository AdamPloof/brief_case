<?php

namespace App\Form\DataTransformer;

use App\Document\CaseFile;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RelatedCaseTransformer implements DataTransformerInterface
{ 
    /**
     * Convert BSON ObjectId to string
     * 
     * @param \MongoDB\BSON\ObjectId|null $caseFile, 
     */
    public function transform($caseFile): ?string {
        return strval($caseFile);
    }

    /**
     * Transform id string to BSON ObjectId
     * 
     * @param string $caseId
     * @throws TransformationFailedException if can't make ObjectId
     */
    public function reverseTransform($caseId): ?\MongoDB\BSON\ObjectId {
        return new \MongoDB\BSON\ObjectId($caseId);
    }
}
