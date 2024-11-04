<?php

namespace App\Form;

use App\Entity\Animals;
use App\Repository\AnimalsRepository;
use App\Entity\Foods;
use App\Repository\FoodsRepository;
use App\Entity\Users;
use App\Repository\UsersRepository;
use App\Entity\Reports;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ReportsType extends AbstractType
{
    private $animalsRepository;
    private $usersRepository;
    private $foodsRepository;

    public function __construct(AnimalsRepository $animalsRepository, UsersRepository $usersRepository, FoodsRepository $foodsRepository)
    {
        $this->animalsRepository = $animalsRepository;
        $this->usersRepository = $usersRepository;
        $this->foodsRepository = $foodsRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $animals = $this->animalsRepository->findAll();
        $foods = $this->foodsRepository->findAll();
        $users = $this->usersRepository->findAll();

            // Récupérer les unités uniques des aliments
        $uniqueUnits = array_unique(array_map(function ($food) {
            return $food->getUnit();
        }, $foods));

        // Créer un tableau associatif pour le choix des unités
        $unitChoices = array_combine($uniqueUnits, $uniqueUnits);

        // Récupérer les poids uniques des aliments
        $uniqueWeights = array_unique(array_map(function ($food) {
            return $food->getWeight();
        }, $foods));

    // Créer un tableau associatif pour le choix des poids
    $weightChoices = array_combine($uniqueWeights, $uniqueWeights);

        $builder
            ->add('date', DateTimeType::class, [
                'data' => new \DateTime(),
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['readonly' => true],
            ])
            ->add('comment', TextType::class, [
                'mapped' => true,
            ])
            ->add('weight', TextType::class, [
                'label' => 'Poids (saisir ou choisir)',
                'mapped' => true,
                'attr' => [
                    'placeholder' => 'Entrez le poids ici...',
                ],
            ])
            ->add('unit', ChoiceType::class, [
                'choices' => $unitChoices,
                'placeholder' => 'Unité',
                'mapped' => true,
            ])
            ->add('idAnimals', ChoiceType::class, [
                'choices' => array_reduce($animals, function($result, $animal) {
                    $result[$animal->getNameAnimal()] = $animal->getId();
                    return $result;
                }, []),
                'placeholder' => 'Choisissez un animal',
                'label' => 'Animal',
            ])
            ->add('idFoods', EntityType::class, [
                'class' => Foods::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez un aliment',
                'mapped' => true,
            ])
            ->add('idUsers', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(fn($user) => $user->getId(), $users),
                    array_map(fn($user) => $user->getName(), $users) 
                ),
                'placeholder' => 'Choisissez un utilisateur',
                'label' => ''
            ])
            ->add('save', SubmitType::class, ['label' => "Ajouter un rapport"])
            ->getForm();
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reports::class,
        ]);
    }
}

