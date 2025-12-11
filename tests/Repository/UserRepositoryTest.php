<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Description of UserRepositoryTest
 *
 * @author Enzo Baum
 */
class UserRepositoryTest extends KernelTestCase {

    // Récupération du repository
    public function recupRepository(): UserRepository {
        self::bootKernel();
        return self::getContainer()->get(UserRepository::class);
    }

    // Test de la méthode upgradePassword
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
