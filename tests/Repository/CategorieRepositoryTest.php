<?php

namespace App\Tests\Repository;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests unitaires pour le repository CategorieRepository
 * 
 * Vérifie les opérations CRUD (ajout, suppression, comptage) et les requêtes
 * personnalisées sur les catégories de formations
 * 
 * @author Enzo Baum
 */
class CategorieRepositoryTest extends KernelTestCase {

    /**
     * Récupère une instance du repository CategorieRepository
     * @return CategorieRepository
     */
    public function recupRepository(): CategorieRepository {
        
        self::bootKernel();
        return self::getContainer()->get(CategorieRepository::class);
    }

    /**
     * Vérifie que la base de données contient exactement 10 catégories
     * Test de base pour s'assurer que les fixtures sont correctement chargées
     */
    public function testNbCategories() {
        
        $repository = $this->recupRepository();
        $nbCategories = $repository->count([]);
        $this->assertEquals(10, $nbCategories);
    }

    /**
     * Crée une nouvelle catégorie pour les tests d'ajout et de suppression
     * @return Categorie
     */
    public function newCategorie(): Categorie {
        
        $categorie = (new Categorie())->setName("PHP");
        return $categorie;
    }

    /**
     * Vérifie que l'ajout d'une catégorie fonctionne correctement
     * Teste que le compteur de catégories augmente de 1 après l'ajout
     */
    public function testAddCategorie() {
        
        $repository = $this->recupRepository();
        $categorie = $this->newCategorie();
        $nbCategories = $repository->count([]);
        $repository->add($categorie, true);
        $this->assertEquals($nbCategories + 1, $repository->count([]), "erreur lors de l'ajout");
    }

    /**
     * Vérifie que la suppression d'une catégorie fonctionne correctement
     * 
     * Teste que le compteur de catégories diminue de 1 après la suppression
     */
    public function testRemoveCategorie() {
        
        $repository = $this->recupRepository();
        $categorie = $this->newCategorie();
        $repository->add($categorie, true);
        $nbCategories = $repository->count([]);
        $repository->remove($categorie, true);
        $this->assertEquals($nbCategories - 1, $repository->count([]), "erreur lors de la suppression");
    }

    /**
     * Vérifie que findAllForOnePlaylist retourne les catégories d'une playlist
     * 
     * Teste la requête personnalisée qui récupère toutes les catégories
     * associées aux formations d'une playlist donnée. Vérifie que :
     * - Le résultat n'est pas vide
     * - Les éléments retournés sont bien des instances de Categorie
     */
    public function testFindAllForOnePlaylist() {
        
        $repository = $this->recupRepository();
        $em = static::getContainer()->get('doctrine')->getManager();
        $playlist = $em->getRepository('App\Entity\Playlist')->findOneBy([]);
        $categories = $repository->findAllForOnePlaylist($playlist->getId());
        $this->assertNotEmpty($categories, "La playlist doit contenir au moins une catégorie");
        $this->assertInstanceOf(Categorie::class, $categories[0]);
    }
}