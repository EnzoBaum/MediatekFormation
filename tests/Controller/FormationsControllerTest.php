<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests fonctionnels pour le contrôleur des formations
 * 
 * Vérifie l'accessibilité, le contenu, le filtrage et la navigation
 * dans les pages liées aux formations
 * 
 * @author Enzo Baum
 */
class FormationsControllerTest extends WebTestCase
{
    /**
     * Vérifie que la page de liste des formations est accessible
     * et retourne un code HTTP 200.
     */
    public function testAccesPage()
    {
        $client = static::createClient();
        $client->request('GET', '/formations');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    
    /**
     * Vérifie que la page contient le tableau des formations
     * avec la structure attendue (5 colonnes et données correctes)
     */
    public function testContenuPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations');
        // vérifie que le texte contient le mot "formation" avec la bonne balise
        $this->assertSelectorTextContains('th.text-left.align-top', 'formation');
        // vérifie que le tableau possède 5 balises <th>
        $this->assertCount(5, $crawler->filter('th'));
        // vérifie que la première formation est celle attendue
        $this->assertSelectorTextContains('h5', 'Eclipse n°8 : Déploiement');
    }
    
    /**
     * Vérifie que le filtrage par nom de formation fonctionne correctement
     * 
     * Teste la soumission du formulaire de recherche et vérifie que seule
     * la formation correspondante est affichée dans les résultats
     */
    public function testFiltreFormation()
    {
        $client = static::createClient();
        $client->request('GET', '/formations');
        // simulation de la soumission du formulaire
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'Cours Informatique embarquée']);
        // vérifie le nombre de lignes obtenues
        $this->assertCount(1, $crawler->filter('h5'));
        // vérifie si la formation correspond à la recherche
        $this->assertSelectorTextContains('h5', 'Cours Informatique embarquée');
    }
    
    /**
     * Vérifie que le lien vers le détail d'une formation fonctionne
     * 
     * Teste le clic sur la miniature d'une formation et vérifie que
     * la page de détail s'affiche avec les bonnes informations (route et titre)
     */
    public function testLinkFormation()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations');
        // clic sur l'image (la maniature d'une formation)
        $link = $crawler->filter('td.text-center a')->first()->link();
        $client->click($link);
        // récupération du résultat du clic
        $response = $client->getResponse();
        // contrôle si le lien existe
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        // récupération de la route et contrôle qu'elle est correcte
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $this->assertEquals('/formations/formation/1', $uri);
        // vérifie le nom de la formation de la page
        $this->assertSelectorTextContains('h4', 'Eclipse n°8 : Déploiement');
    }
}