<?php

/*namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PageControllerTest extends WebTestCase{

    public function testAuthPageIsRestricted(){

        $client = static::createClient();
        $client->request('GET', '/checkout');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

    }

    public function testRedirectToLogin(){

        $client = static::createClient();
        $client->request('GET', '/checkout');
        $this->assertResponseRedirects('/login');

    }

}*/