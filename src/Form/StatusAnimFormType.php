<?php

namespace App\Form;

use App\Entity\ReportsVet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatusAnimFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];  // Utilisateur connecté

        // Vérifiez si l'utilisateur est null
        if (!$user) {
            throw new \LogicException('User must be provided to the form.');
        }

        $builder
            ->add('date', DateTimeType::class, [
                'data' => new \DateTime(),
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['readonly' => true],
                'label' => 'Date du rapport'
            ])
            ->add('idUsers', HiddenType::class, [
                'data' => $user->getId(), // Passez l'ID de l'utilisateur pour lier l'entrée
                'mapped' => false, // Désactivez le mappage direct, car l'utilisateur est défini dans le contrôleur
            ])            
            // Ajoutez un champ pour l'état de l'animal
            ->add('state', TextareaType::class, [
                'required' => false,
                'label' => 'État de l\'animal'
            ])
            ->add('save', SubmitType::class, [
                'label' => "Ajouter"
            ]);
            
        }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReportsVet::class,
            'user' => null, // Option pour l'utilisateur connecté
        ]);
    }
}
