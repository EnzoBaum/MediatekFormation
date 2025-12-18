<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests fonctionnels pour le contrôleur de la page d'accueil
 * 
 * Vérifie l'accessibilité et le contenu de la page d'accueil du site
 * 
 * @author Enzo Baum
 */
class AccueilControllerTest extends WebTestCase
{
    /**
     * Vérifie que la page d'accueil est accessible 
     * et retourne un code HTTP 200
     */
    public function testAccesPage()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Vérifie que la page d'accueil affiche le message de bienvenue attendu.
     */
    public function testContenuPage()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertSelectorTextContains
                ('h3', 'Bienvenue sur le site de MediaTek86 consacré aux formations en ligne');
    }
}