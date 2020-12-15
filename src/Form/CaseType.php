<?php

namespace App\Form;

use App\Document\CaseFile;

use App\Form\PersonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class CaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextType::class)
            ->add('summary', TextareaType::class)
            ->add('date', DateType::class)
            ->add('video', TextType::class, [
                'required' => false,
            ])
            ->add('primary_person', PersonType::class)
            ->add('submit', SubmitType::class, ['label' => 'Create Case']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CaseFile::class,
        ]);
    }
}