<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;


class UsersRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    // Méthode pour mettre à jour le mot de passe de l'utilisateur
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Users) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    // Retourne tous les utilisateurs
    public function findAllUsers()
    {
        return $this->findAll();
    }

    // Retourne tous les utilisateurs triés par ID
    public function findAllById()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.id', 'ASC')  // Exemple de tri par ID
            ->getQuery()
            ->getResult();
    }

    // Méthode pour trouver un utilisateur par son email (par exemple)
    public function findByEmail(string $email)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // Méthode pour trouver un utilisateur par son nom (par exemple)
    public function findByName(string $name)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getResult();
    }
}
