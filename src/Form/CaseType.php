<?php

namespace App\Form;

use App\Document\CaseFile;
use App\Form\PersonType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class CaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // TODO: Handle empty traits inputs
        $builder
            ->add('description', TextType::class)
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Shoplifting' => 'Shoplifting',
                    'Aggressive Behavior' => 'Aggressive Behavior',
                    'Harrasment' => 'Harrasment',
                    'Workplace Injury' => 'Workplace Injury',
                    'Foodborne Illness' => 'Foodborne Illness',
                    'Vehicle Accident' => 'Vehicle Accident',
                    'Vandalism' => 'Vandalism',
                    'Medical Emergency' => 'Medical Emergency',
                ]
            ])
            ->add('summary', TextareaType::class)
            ->add('video_file', FileType::class, [
                'label' => 'Upload Video',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => 'video/*',
                        'mimeTypesMessage' => 'File is not a valid video file.',
                        'maxSize' => '4M',
                    ]),
                ]
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
            ->add('related_cases', CollectionType::class, [
                'entry_type' => RelatedCaseType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
                'by_reference' => false,
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Submit']);
    }

    // Explicitly stating which class (Document) this form belongs to
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CaseFile::class,
        ]);
    }
}