<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of PlaylistsControllerTest
 *
 * @author Enzo Baum
 */
class PlaylistsControllerTest extends WebTestCase
{
    public function testAccesPage()
    {
        $client = static::createClient();
        $client->request('GET', '/playlists');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    
    public function testContenuPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists');
        // vérifie que le texte contient le mot "playlist" avec la bonne balise
        $this->assertSelectorTextContains('th.text-left.align-top', 'playlist');
        // vérifie que le tableau possède 5 balises <th>
        $this->assertCount(4, $crawler->filter('th'));
        // vérifie que la première playlist est celle attendue
        $this->assertSelectorTextContains('h5', 'Bases de la programmation (C#)');
    }
    
    public function testFiltrePlaylist()
    {
        $client = static::createClient();
        $client->request('GET', '/playlists');
        // simulation de la soumission du formulaire
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'Cours Curseurs']);
        // vérifie le nombre de lignes obtenues
        $this->assertCount(1, $crawler->filter('h5'));
        // vérifie si la playlist correspond à la recherche
        $this->assertSelectorTextContains('h5', 'Cours Curseurs');
    }
    
    public function testLinkPlaylist()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists');
        // clic sur le bouton (voir détail d'une playlist)
        $link = $crawler->selectLink('Voir détail')->first()->link();
        $client->click($link);
        // récupération du résultat du clic
        $response = $client->getResponse();
        // contrôle si le lien existe
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        // récupération de la route et contrôle qu'elle est correcte
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $this->assertEquals('/playlists/playlist/13', $uri);
        // vérifie le nom de la formation de la page
        $this->assertSelectorTextContains('h4', 'Bases de la programmation (C#)');
    }
}
