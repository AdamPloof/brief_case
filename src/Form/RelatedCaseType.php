<?php

namespace App\Form;

use App\Document\CaseFile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class RelatedCaseType extends AbstractType
{
    public function FormBuilderInterface(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextType::class, [
                'mapped' => false,
            ])
            ->add('id', TextType::class, [
                'attr' => ['hidden' => true]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Casefile::class,
        ]);
    }
}