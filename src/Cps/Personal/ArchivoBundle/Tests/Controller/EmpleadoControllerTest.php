<?php

namespace Cps\Personal\ArchivoBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmpleadoControllerTest extends WebTestCase
{
    public function testBuscarempleadoajax()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/buscarEmpleadoAjax');
    }

}
