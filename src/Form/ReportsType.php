<?php

namespace App\Form;

use App\Entity\Reports;
use App\Repository\AnimalsRepository;
use App\Repository\FoodsRepository;
use App\Entity\Foods;
use App\Entity\Animals;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;



class ReportsType extends AbstractType
{
    private $animalsRepository;
    private $foodsRepository;

    public function __construct(AnimalsRepository $animalsRepository, FoodsRepository $foodsRepository)
    {
        $this->animalsRepository = $animalsRepository;
        $this->foodsRepository = $foodsRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupérer l'animal à pré-sélectionner si fourni
        $animal = $options['animal'] ?? null;

        // Récupérer l'utilisateur passé en option
        $user = $options['user'];

        // Vérifiez si l'utilisateur est null
        if (!$user) {
            throw new \LogicException('User must be provided to the form.');
        }

        // Récupérer les aliments
        $foods = $this->foodsRepository->findAll();

        // Récupérer les unités uniques des aliments
        $uniqueUnits = array_unique(array_map(function ($food) {
            return $food->getUnit();
        }, $foods));

        // Créer un tableau associatif pour le choix des unités
        $unitChoices = array_combine($uniqueUnits, $uniqueUnits);

        // Créer le formulaire
        $builder
            ->add('date', DateTimeType::class, [
                'data' => new \DateTime(),
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['readonly' => true],
            ])
            ->add('comment', TextareaType::class, [
                'mapped' => true,
            ])
            ->add('weight', TextType::class, [
                'label' => 'Poids (saisir ou choisir)',
                'attr' => [
                    'placeholder' => 'Entrez le poids ici...',
                ],
                'mapped' => true,
                'constraints' => [
                    new NotBlank(), // Assurez-vous que le champ n'est pas vide
                    new Type(['type' => 'numeric']) // Vérifiez que c'est un nombre
                ],
            ])
            ->add('unit', ChoiceType::class, [
                'choices' => $unitChoices,
                'placeholder' => 'Unité',
                'mapped' => true,
            ])
            ->add('idAnimals', EntityType::class, [
                'class' => Animals::class,
                'label' => 'Animal',
                'data' => $animal, // Pré-sélectionner l'animal trouvé
                'disabled' => true, // Désactiver le champ
                'mapped' => true,
            ])
            ->add('idFoods', EntityType::class, [
                'class' => Foods::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez un aliment',
                'mapped' => true,
            ])
            ->add('idUsers', TextType::class, [
                'data' => $user->getName() . ' ' . $user->getFirstName(), // Indiquer l'utilisateur connecté
                'label' => 'Utilisateur',
                'disabled' => true, // Désactiver le champ pour éviter la modification
            ])
            ->add('save', SubmitType::class, ['label' => "Ajouter un rapport"]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reports::class,
            'user' => null, // Définir l'option par défaut pour l'utilisateur
            'animal' => null, // Définir l'option par défaut pour l'animal
        ]);
    }
}
