<?php

namespace App\Tests\Repository;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Description of CategorieRepositoryTest
 *
 * @author Enzo Baum
 */
class CategorieRepositoryTest extends KernelTestCase {

    // Récupération du repository
    public function recupRepository(): CategorieRepository {
        
        self::bootKernel();
        return self::getContainer()->get(CategorieRepository::class);
    }

    // Vérifie le nombre de catégories présentes dans la BDD
    public function testNbCategories() {
        
        $repository = $this->recupRepository();
        $nbCategories = $repository->count([]);
        $this->assertEquals(10, $nbCategories);
    }

    // Crée une nouvelle catégorie (pour tester add/remove)
    public function newCategorie(): Categorie {
        
        $categorie = (new Categorie())->setName("PHP");
        return $categorie;
    }

    // Test d'ajout d'une catégorie
    public function testAddCategorie() {
        
        $repository = $this->recupRepository();
        $categorie = $this->newCategorie();
        $nbCategories = $repository->count([]);
        $repository->add($categorie, true);
        $this->assertEquals($nbCategories + 1, $repository->count([]), "erreur lors de l'ajout");
    }

    // Test de suppression d'une catégorie
    public function testRemoveCategorie() {
        
        $repository = $this->recupRepository();
        $categorie = $this->newCategorie();
        $repository->add($categorie, true);
        $nbCategories = $repository->count([]);
        $repository->remove($categorie, true);
        $this->assertEquals($nbCategories - 1, $repository->count([]), "erreur lors de la suppression");
    }

    // Test de findAllForOnePlaylist en utilisant la BDD de test
    public function testFindAllForOnePlaylist() {
        
        $repository = $this->recupRepository();
        $em = static::getContainer()->get('doctrine')->getManager();
        $playlist = $em->getRepository('App\Entity\Playlist')->findOneBy([]);
        $categories = $repository->findAllForOnePlaylist($playlist->getId());
        $this->assertNotEmpty($categories, "La playlist doit contenir au moins une catégorie");
        $this->assertInstanceOf(Categorie::class, $categories[0]);
    }
}
