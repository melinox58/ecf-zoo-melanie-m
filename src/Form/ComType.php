<?php

// src/Form/ComType.php
namespace App\Form;

use App\Entity\Reports;
use App\Entity\Habitats;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ComType extends AbstractType
{
    // Construire le formulaire
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Récupérer l'utilisateur et l'habitat passés en options
        $user = $options['user'];
        $habitat = $options['habitat'];

        $builder
            // Champ caché pour l'ID de l'utilisateur connecté (pas besoin de le modifier)
            ->add('idUsers', HiddenType::class, [
                'data' => $user->getId(),
            ])
            // Champ caché pour l'ID de l'habitat concerné
            ->add('idHabitats', HiddenType::class, [
                'data' => $habitat->getId(),
            ])
            // Champ pour le commentaire
            ->add('comment', TextareaType::class, [
                'label' => 'Commentaire',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ajoutez votre commentaire ici...',
                    'rows' => 5,
                ],
            ])
            // Bouton pour soumettre le formulaire
            ->add('submit', SubmitType::class, [
                'label' => 'Soumettre le rapport',
            ]);
    }

    // Configurer les options du formulaire
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reports::class, // Associe le formulaire à l'entité Reports
            'user' => null, // Permet de passer l'utilisateur connecté
            'habitat' => null, // Permet de passer l'habitat sélectionné
        ]);
    }
}

