<?php

namespace App\Form\DataTransformer;

use App\Document\Person;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TraitsToArrayTransformer implements DataTransformerInterface
{
    /**
     * Transforms a traits array to string
     * 
     * @param array|null $traits
     */
    public function transform($traits): ?string {
        $traitStr = '';

        if (!isset($traits)) {
            return null;
        }

        foreach ($traits as $trait => $val) {
            $traitStr .= $trait . ':' . $val;

            if ($trait != array_key_last($traits)) {
                $traitStr .= ',';
            }
        }
        return $traitStr;
    }

    /**
     * Transforms a string to a traits array
     * 
     * @param string $traitsStr
     * @throws TransformationFailedException if traits string can't be parsed.
     */
    public function reverseTransform($traitsStr): ?array {
        $traitsArr = array();
        $traits = explode(',', $traitsStr);

        if (!isset($traits)) {
            return null;
        }

        foreach ($traits as $trait) {
            $hash = explode(':', $trait);
            $traitsArr[trim($hash[0])] = trim($hash[1]);
        }
        return $traitsArr;
    }
}
