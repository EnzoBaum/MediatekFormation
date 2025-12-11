<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of AccueilControllerTest
 *
 * @author Enzo Baum
 */
class AccueilControllerTest extends WebTestCase
{
    public function testAccesPage()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    
    public function testContenuPage()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertSelectorTextContains
                ('h3', 'Bienvenue sur le site de MediaTek86 consacré aux formations en ligne');
    }
}
