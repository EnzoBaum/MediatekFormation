<?php

namespace App\Tests\Repository;

use App\Entity\Formation;
use App\Entity\Playlist;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Description of FormationRepositoryTest
 *
 * @author Enzo Baum
 */
class FormationRepositoryTest extends KernelTestCase {

    // Récupération du repository
    public function recupRepository(): FormationRepository
    {
        self::bootKernel();
        return self::getContainer()->get(FormationRepository::class);
    }
    
    // Test d'ajout d'une formation
    public function testAddFormation()
    {
        $repository = $this->recupRepository();
        $formation = (new Formation())->setTitle("Les bases du langage PHP");
        $nbFormations = $repository->count([]);
        $repository->add($formation, true);
        $this->assertEquals($nbFormations + 1, $repository->count([]), "erreur lors de l'ajout");
    }
    
    // Test de suppression d'une formation
    public function testRemoveFormation()
    {
        $repository = $this->recupRepository();
        $formation = (new Formation())->setTitle("Les bases du langage PHP");
        $repository->add($formation, true);
        $nbFormations = $repository->count([]);
        $repository->remove($formation, true);
        $this->assertEquals($nbFormations - 1, $repository->count([]), "erreur lors de la suppression");
    }
    
    // Test retournant toutes les formations triées sur un champ ou une table
    public function testFindAllOrderBy()
    {
        $repository = $this->recupRepository();
        $em = static::getContainer()->get('doctrine')->getManager();

        // Créations des deux playlists de test
        $playlistPHP = (new Playlist())->setName("Le PHP");
        $playlistHTML = (new Playlist())->setName("L'HTML");
        $em->persist($playlistPHP);
        $em->persist($playlistHTML);

        // Créations des deux formations de test
        $formationPHP = (new Formation())
            ->setTitle("Les bases du langage PHP")
            ->setPlaylist($playlistPHP);
        $formationHTML = (new Formation())
            ->setTitle("Les bases de l'HTML")
            ->setPlaylist($playlistHTML);
        $em->persist($formationPHP);
        $em->persist($formationHTML);
        
        $em->flush();

        // Tri par title 
        $resultTitle = $repository->findAllOrderBy("title", "ASC");
        $titles = array_map(fn($f) => $f->getTitle(), $resultTitle);
        $this->assertLessThan(
            array_search("Les bases du langage PHP", $titles),
            array_search("Les bases de l'HTML", $titles)
        );

        // Tri par playlist 
        $resultPlaylist = $repository->findAllOrderBy("name", "ASC", "playlist");
        $playlists = array_map(fn($f) => $f->getPlaylist()->getName(), $resultPlaylist);
        $this->assertLessThan(
            array_search("Le PHP", $playlists),
            array_search("L'HTML", $playlists)
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

        // Créations des deux formations de test
        $formationPHP = (new Formation())
            ->setTitle("Les bases du langage PHP")
            ->setPlaylist($playlistPHP)
            ->setPublishedAt(new \DateTime("2025-12-01"));
        $formationHTML = (new Formation())
            ->setTitle("Les bases de l'HTML")
            ->setPlaylist($playlistHTML)
            ->setPublishedAt(new \DateTime("2020-12-02"));
        $em->persist($formationPHP);
        $em->persist($formationHTML);
        
        $em->flush();

        // Renvoie tous les enregistrements si valeur vide
        $resultsAll = $repository->findByContainValue("title", "");
        $this->assertNotEmpty($resultsAll);

        // Recherche dans l'entité Formation
        $resultsPHP = $repository->findByContainValue("title", "PHP");
        $this->assertEquals("Les bases du langage PHP", $resultsPHP[0]->getTitle());

        // Recherche dans la table Playlist
        $resultsHTML = $repository->findByContainValue("name", "HTML", "playlist");
        $this->assertEquals("Les bases de l'HTML", $resultsHTML[0]->getTitle());
    }
        
    // Test retournant les n formations les plus récentes
    public function testFindAllLasted()
    {
        $repository = $this->recupRepository();
        $em = static::getContainer()->get('doctrine')->getManager();

        // Création des trois formations de test
        $formationPHP = (new Formation())
                ->setTitle("Les bases du langage PHP")
                ->setPublishedAt(new \DateTime("2025-12-01"));
        $formationHTML = (new Formation())
                ->setTitle("Les bases de l'HTML")
                ->setPublishedAt(new \DateTime("2025-12-02"));
        $formationCPP = (new Formation())
                ->setTitle("Les bases du langage C++")
                ->setPublishedAt(new \DateTime("2025-12-03"));

        $em->persist($formationPHP);
        $em->persist($formationHTML);
        $em->persist($formationCPP);
        $em->flush();

        // Demande les 2 formations les plus récentes
        $results = $repository->findAllLasted(2);

        // Vérifie que la formation la plus récente est bien celle portant sur le C++
        $this->assertEquals("Les bases du langage C++", $results[0]->getTitle());
    }
    
    // Test retournant la liste des formations d'une playlist
    public function testFindAllForOnePlaylist()
    {
        $repository = $this->recupRepository();
        $em = static::getContainer()->get('doctrine')->getManager();
        // Récupère la première playlist existante
        $playlist = $em->getRepository('App\Entity\Playlist')->findOneBy([]);
        // Appelle la méthode à tester
        $formations = $repository->findAllForOnePlaylist($playlist->getId());
        // Vérifie que la playlist contient au moins une formation
        $this->assertNotEmpty($formations, "La playlist doit contenir au moins une formation");
    }
}