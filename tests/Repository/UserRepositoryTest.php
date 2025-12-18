<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests unitaires pour le repository UserRepository.
 * 
 * Vérifie les opérations spécifiques à la gestion des utilisateurs,
 * notamment la mise à jour des mots de passe.
 * 
 * @author Enzo Baum
 */
class UserRepositoryTest extends KernelTestCase {

    /**
     * Récupère une instance du repository UserRepository
     * @return UserRepository 
     */
    public function recupRepository(): UserRepository {
        self::bootKernel();
        return self::getContainer()->get(UserRepository::class);
    }

    /**
     * Vérifie que la mise à jour du mot de passe d'un utilisateur fonctionne.
     * 
     * Teste la méthode upgradePassword en créant un utilisateur avec un ancien
     * mot de passe, puis en le mettant à jour avec un nouveau hash
     * Vérifie que le mot de passe stocké correspond au nouveau hash fourni
     */
    public function testUpgradePassword()
    {
        $repository = $this->recupRepository();
        $em = static::getContainer()->get('doctrine')->getManager();

        // Création d'un utilisateur de test
        $user = (new User())
            ->setUsername('testuser')
            ->setPassword('ancien_mot_de_passe');
        $em->persist($user);
        $em->flush();

        // Création et ajout du nouveau mot de passe hashé
        $newHashedPassword = 'nouveau_mot_de_passe_hash';
        $repository->upgradePassword($user, $newHashedPassword);

        // Vérifie que le mot de passe a bien été mis à jour
        $this->assertEquals(
            $newHashedPassword,
            $user->getPassword()
        );
    }
}