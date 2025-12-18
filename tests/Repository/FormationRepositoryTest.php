<?php

namespace App\Tests\Repository;

use App\Entity\Formation;
use App\Entity\Playlist;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests unitaires pour le repository FormationRepository
 * 
 * Vérifie les opérations CRUD et les requêtes personnalisées sur les formations :
 * - Ajout et suppression
 * - Tri sur différents champs (entité et relations)
 * - Recherche par valeur contenue
 * - Récupération des formations récentes
 * - Récupération des formations d'une playlist
 * 
 * @author Enzo Baum
 */
class FormationRepositoryTest extends KernelTestCase {

    /**
     * Récupère une instance du repository FormationRepository
     * @return FormationRepository
     */
    public function recupRepository(): FormationRepository
    {
        self::bootKernel();
        return self::getContainer()->get(FormationRepository::class);
    }
    
   /**
     * Vérifie que l'ajout d'une formation fonctionne correctement
     *
     * Teste que le compteur de formations augmente de 1 après l'ajout
     */
    public function testAddFormation()
    {
        $repository = $this->recupRepository();
        $formation = (new Formation())->setTitle("Les bases du langage PHP");
        $nbFormations = $repository->count([]);
        $repository->add($formation, true);
        $this->assertEquals($nbFormations + 1, $repository->count([]), "erreur lors de l'ajout");
    }
    
    /**
     * Vérifie que la suppression d'une formation fonctionne correctement
     * 
     * Teste que le compteur de formations diminue de 1 après la suppression
     */
    public function testRemoveFormation()
    {
        $repository = $this->recupRepository();
        $formation = (new Formation())->setTitle("Les bases du langage PHP");
        $repository->add($formation, true);
        $nbFormations = $repository->count([]);
        $repository->remove($formation, true);
        $this->assertEquals($nbFormations - 1, $repository->count([]), "erreur lors de la suppression");
    }
    
    /**
     * Vérifie que le tri des formations fonctionne sur différents champs
     * 
     * Teste findAllOrderBy avec :
     * - Tri sur un champ de l'entité Formation (title)
     * - Tri sur un champ d'une relation (Playlist)
     * 
     * Vérifie que l'ordre ASC est respecté dans les deux cas
     */
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
    
    /**
     * Vérifie que la recherche fonctionne correctement
     * 
     * Teste findByContainValue avec :
     * - Valeur vide : retourne tous les enregistrements
     * - Recherche dans un champ de Formation (title)
     * - Recherche dans un champ d'une relation (Playlist)
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
        
    /**
     * Vérifie que la récupération des formations les plus récentes fonctionne.
     *
     * Teste findAllLasted en vérifiant que les n formations les plus récentes sont retournées
     * sont dans l'ordre décroissant en fonction de la date de publication.
     */
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
    
    /**
     * Vérifie que findAllForOnePlaylist retourne les formations d'une playlist
     *
     * Teste la requête personnalisée qui récupère toutes les formations
     * associées à une playlist donnée.
     *
     * Vérifie que le résultat n'est pas vide.
     */
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