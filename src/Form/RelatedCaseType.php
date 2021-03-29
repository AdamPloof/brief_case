<?php

namespace App\Form;

use App\Document\CaseFile;
use App\Form\DataTransformer\RelatedCaseTransformer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class RelatedCaseType extends AbstractType
{
    private $transformer;

    public function __construct(RelatedCaseTransformer $transformer) {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextType::class, [
                'mapped' => false,
            ])
            ->add('case_id', TextType::class, [
                'attr' => ['hidden' => true],
                'label' => false,
                'mapped' => false,
            ]);

        // $builder->get('case_id')
        //     ->addModelTransformer($this->transformer);
    }
}