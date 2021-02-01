<?php

namespace App\Form;

use App\Document\CaseFile;
use App\Form\PersonType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class CaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextType::class)
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('summary', TextareaType::class)
            ->add('video', FileType::class, [
                'label' => 'Upload Video',
                'required' => false,
                'mapped' => false,
            ])
            ->add('primary_person', PersonType::class)
            ->add('associated_persons', CollectionType::class, [
                'entry_type' => PersonType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
                'by_reference' => false,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Submit']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CaseFile::class,
        ]);
    }
}