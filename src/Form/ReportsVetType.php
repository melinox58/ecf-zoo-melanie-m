<?php

namespace App\Form;


use App\Entity\Habitats;
use App\Entity\ReportsVet;
use Proxies\__CG__\App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ReportsVetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];  // Utilisateur connecté
        $habitats = $options['habitats'];  // Liste des habitats à afficher

        // Vérifiez si l'utilisateur est null
        if (!$user) {
            throw new \LogicException('User must be provided to the form.');
        }

        $builder
            ->add('comment', TextareaType::class, [
                'mapped' => true,
                'label' => 'Commentaires'
            ])
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
            ->add('idHabitats', EntityType::class, [
                'class' => Habitats::class,
                'choice_label' => 'name',
                'choices' => $habitats,
                'label' => 'Choisir un habitat',
                'mapped' => true,
            ])
            // Ajoutez un champ pour l'état de l'animal
            ->add('state', TextType::class, [
                'required' => false,
                'label' => 'État de l\'animal'
            ])
            ->add('save', SubmitType::class, ['label' => "Ajouter"]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReportsVet::class,
            'habitats' => [],
            'user' => null, // Option pour l'utilisateur connecté
        ]);
    }
}
