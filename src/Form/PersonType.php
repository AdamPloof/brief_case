<?php

namespace App\Form;

use App\Document\Person;
use App\Form\DataTransformer\TraitsToArrayTransformer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;

class PersonType extends AbstractType
{
    private $transformer;

    public function __construct(TraitsToArrayTransformer $transformer) {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('role', TextType::class)
            ->add('traits', TextType::class, [
                'required' => false
                // TODO: May eventually want to add an invalid message for incorrectly formed trait strings
            ])
            ->add('image_file', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '2M',
                    ]),
                ]
            ]);

        $builder->get('traits')
            ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}