<?php

namespace App\Tests\Repository;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests unitaires pour le repository PlaylistRepository
 * 
 * Vérifie les opérations CRUD et les requêtes personnalisées sur les playlists :
 * - Ajout et suppression
 * - Tri par nom
 * - Tri par nombre de formations
 * - Recherche par valeur contenue (dans l'entité et les relations)
 * 
 * @author Enzo Baum
 */
class PlaylistRepositoryTest extends KernelTestCase {
    
    /**
     * Récupère une instance du repository PlaylistRepository
     * @return PlaylistRepository
     */
    public function recupRepository(): PlaylistRepository
    {
        self::bootKernel();
        return self::getContainer()->get(PlaylistRepository::class);
    }
    
    /**
     * Vérifie que l'ajout d'une playlist fonctionne correctement.
     * 
     * Teste que le compteur de playlists augmente de 1 après l'ajout.
     */
    public function testAddPlaylist()
    {
        $repository = $this->recupRepository();
        $playlist = (new Playlist())->setName("Le PHP");
        $nbPlaylists = $repository->count([]);
        $repository->add($playlist, true);
        $this->assertEquals($nbPlaylists + 1, $repository->count([]), "erreur lors de l'ajout");
    }
    
    /**
     * Vérifie que la suppression d'une playlist fonctionne correctement.
     * 
     * Teste que le compteur de playlists diminue de 1 après la suppression.
     */
    public function testRemovePlaylist()
    {
        $repository = $this->recupRepository();
        $playlist = (new Playlist())->setName("Le PHP");
        $repository->add($playlist, true);
        $nbPlaylists = $repository->count([]);
        $repository->remove($playlist, true);
        $this->assertEquals($nbPlaylists - 1, $repository->count([]), "erreur lors de la suppression");
    }
    
    /**
     * Vérifie que le tri des playlists par nom fonctionne correctement.
     * 
     * Teste findAllOrderByName avec un tri ascendant et vérifie que
     * l'ordre alphabétique est respecté.
     */
    public function testFindAllOrderByName()
    {
        $repository = $this->recupRepository();
        $em = static::getContainer()->get('doctrine')->getManager();

        // Création de deux playlists de test
        $playlistPHP = (new Playlist())->setName("Le PHP");
        $playlistHTML = (new Playlist())->setName("L'HTML");
        $em->persist($playlistPHP);
        $em->persist($playlistHTML);
        
        $em->flush();

        // Appel de la méthode à tester
        $result = $repository->findAllOrderByName("ASC");

        // Extraction des noms pour vérifier l'ordre
        $names = array_map(fn($p) => $p->getName(), $result);

        // Vérifie que "L'HTML" vient avant "Le PHP"
        $this->assertTrue(array_search("L'HTML", $names) < array_search("Le PHP", $names));
    }

    /**
     * Vérifie que le tri des playlists par nombre de formations fonctionne.
     *
     * Teste findAllOrderByNbFormations avec un nombre différent de formations et
     * vérifie que le tri décroissant place en premier celle qui a le plus de formations
     */
    public function testFindAllOrderByNbFormations()
    {
        $repository = $this->recupRepository();
        $em = static::getContainer()->get('doctrine')->getManager();

        // Création de deux playlists de test
        $playlistPHP = (new Playlist())->setName("Le PHP");
        $playlistHTML = (new Playlist())->setName("L'HTML");
        $em->persist($playlistPHP);
        $em->persist($playlistHTML);

        // Création de trois formations de test
        $formationPHP = (new Formation())->setTitle("Les bases du PHP")->setPlaylist($playlistPHP);
        $formationHTML1 = (new Formation())->setTitle("Les bases de l'HTML 1")->setPlaylist($playlistHTML);
        $formationHTML2 = (new Formation())->setTitle("Les bases de l'HTML 2")->setPlaylist($playlistHTML);
        $em->persist($formationPHP);
        $em->persist($formationHTML1);
        $em->persist($formationHTML2);

        $em->flush();
        
        // Appel de la méthode à tester
        $results = $repository->findAllOrderByNbFormations('DESC');

        // Vérifie si la playlist avec le plus de formations est avant celle avec le moins
        $this->assertGreaterThan(
            count($results[1]->getFormations()),
            count($results[0]->getFormations())
        );
    }
    
    /**
     * Vérifie que la recherche fonctionne correctement.
     *
     * Teste findByContainValue avec :
     * - Valeur vide : retourne toutes les playlists
     * - Recherche dans un champ de Playlist (name)
     * - Recherche dans un champ d'une relation (Categorie via formations)
     *
     * Ce dernier cas vérifie qu'une playlist peut être retrouvée via les catégories
     * de ses formations.
     */
    public function testFindByContainValue()
    {
        $repository = $this->recupRepository();
        $em = static::getContainer()->get('doctrine')->getManager();

        // Création des deux playlists de test
        $playlistPHP = (new Playlist())->setName("Le PHP");
        $playlistHTML = (new Playlist())->setName("L'HTML");
        $em->persist($playlistPHP);
        $em->persist($playlistHTML);

        // Création d'une catégorie de test
        $categoriePHP = (new Categorie())->setName("PHP");
        $em->persist($categoriePHP);

        // Création de deux formations de test
        $formationPHP = (new Formation())
            ->setTitle("Les bases du langage PHP")
            ->setPlaylist($playlistPHP)
            ->addCategory($categoriePHP);
        $em->persist($formationPHP);

        $formationHTML = (new Formation())
            ->setTitle("Les bases d l'HTML")
            ->setPlaylist($playlistHTML)
            ->addCategory($categoriePHP);
        $em->persist($formationHTML);

        $em->flush();

        // Renvoie tous les enregistrements si valeur vide
        $resultsAll = $repository->findByContainValue("name", "");
        $this->assertNotEmpty($resultsAll);

        // Recherche dans l'entité Playlist
        $resultsPHP = $repository->findByContainValue("name", "PHP");
        $this->assertEquals("Le PHP", $resultsPHP[0]->getName());

        // Recherche dans l'entité Categorie
        $resultsCategorie = $repository->findByContainValue("name", "PHP", "categories");
        $this->assertGreaterThanOrEqual(2, count($resultsCategorie));
    }
}