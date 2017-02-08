<?php

namespace Influencer\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminPageControllerTest extends WebTestCase
{
    public function testSettingssignup()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/settings/signup');
    }

}
