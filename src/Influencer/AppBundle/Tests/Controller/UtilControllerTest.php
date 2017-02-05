<?php

namespace Influencer\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UtilControllerTest extends WebTestCase
{
    public function testGetintlvars()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/get-intl-vars');
    }

    public function testGetposttypes()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/get-post-types');
    }

    public function testGetsocialnetworks()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/get-social-networks');
    }

    public function testCheckusername()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/check-for-username');
    }

    public function testCheckemail()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/check-for-email');
    }

}
