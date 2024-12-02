<?php

namespace App\Form;


use App\Entity\Habitats;
use App\Entity\ReportsVet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ReportsVetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupérer l'habitat à pré-sélectionner si fourni
        $habitat = $options['habitat'] ?? null;

        // Récupérer l'utilisateur passé en option
        $user = $options['user'];

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
            ->add('idUsers', TextType::class, [
                'data' => $user->getName() . ' ' . $user->getFirstName(), // Afficher le nom de l'utilisateur connecté
                'label' => 'Utilisateur',
                'disabled' => true, // Désactiver le champ pour éviter la modification
            ])
            ->add('idHabitats', ChoiceType::class, [
                'choices' => $options['habitats'],
                'choice_label' => function (Habitats $habitat) {
                    return $habitat->getName();
                },
                'label' => 'Choisir un habitat',
                'mapped' => true,
            ])
            ->add('save', SubmitType::class, ['label' => "Ajouter"]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReportsVet::class,
            'users' => [],
            'habitats' => [],
            'user' => null, // Option pour l'utilisateur connecté
            'habitat' => null, // Option pour l'habitat (si nécessaire)
        ]);
    }
}
