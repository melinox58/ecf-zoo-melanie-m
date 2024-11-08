<?php

namespace App\Form;

use App\Entity\Images;
use App\Entity\Animals;
use App\Entity\Services;
use App\Entity\Habitats;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;

class AddImageFormType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Images::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idServices', EntityType::class, [
                'class' => Services::class,
                'choice_label' => 'name',
            ])
            ->add('idAnimals', EntityType::class, [
                'class' => Animals::class,
                'choice_label' => 'nameAnimal',
            ])
            ->add('idHabitats', EntityType::class, [
                'class' => Habitats::class,
                'choice_label' => 'name',
            ])
            ->add('name')
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'attr' => [
                    'accept' => 'image/png, image/jpeg, image/webp',
                ],
                'constraints' => [
                    new Image([
                        'minWidth' => 200,
                        'maxWidth' => 4000,
                        'minHeight' => 200,
                        'maxHeight' => 4000,
                        'allowPortrait' => false,
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                    ]),
                ],
            ]);
    }
}
