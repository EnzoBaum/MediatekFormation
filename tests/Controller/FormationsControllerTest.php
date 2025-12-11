<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of FormationsControllerTest
 *
 * @author Enzo Baum
 */
class FormationsControllerTest extends WebTestCase
{
    public function testAccesPage()
    {
        $client = static::createClient();
        $client->request('GET', '/formations');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    
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