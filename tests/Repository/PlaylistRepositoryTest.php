<?php

namespace App\Tests\Repository;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Description of PlaylistRepositoryTest
 *
 * @author Enzo Baum
 */
class PlaylistRepositoryTest extends KernelTestCase {
    
 // Récupération du repository
    public function recupRepository(): PlaylistRepository
    {
        self::bootKernel();
        return self::getContainer()->get(PlaylistRepository::class);
    }
    
    // Test d'ajout d'une playlist
    public function testAddPlaylist()
    {
        $repository = $this->recupRepository();
        $playlist = (new Playlist())->setName("Le PHP");
        $nbPlaylists = $repository->count([]);
        $repository->add($playlist, true);
        $this->assertEquals($nbPlaylists + 1, $repository->count([]), "erreur lors de l'ajout");
    }
    
    // Test de suppression d'une playlist
    public function testRemovePlaylist()
    {
        $repository = $this->recupRepository();
        $playlist = (new Playlist())->setName("Le PHP");
        $repository->add($playlist, true);
        $nbPlaylists = $repository->count([]);
        $repository->remove($playlist, true);
        $this->assertEquals($nbPlaylists - 1, $repository->count([]), "erreur lors de la suppression");
    }
    
    // Test du tri des playlists par nom
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
    
    // Test du tri des playlists par nombre de formations
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
    
    // Test retournant les enregistrements dont un champ contient une valeur
    // ou tous les enregistrements si la valeur est vide
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