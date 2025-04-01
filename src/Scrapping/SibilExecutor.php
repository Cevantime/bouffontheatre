<?php

namespace App\Scrapping;

use Symfony\Component\Panther\Client;

class SibilExecutor
{

    public static function createReliableClient()
    {
        return Client::createChromeClient(null, [
            '--user-agent=Mozilla/5.0 (X11; Linux x86_64; rv:102.0) Gecko/20100101 Firefox/102.0',
//            '--headless=new',
            '--window-size=1200,1100',
            '--disable-gpu',
            '--no-sandbox'
        ]);
    }
    public function test()
    {
        $client = SibilExecutor::createReliableClient();
        $client->get('https://sibil.culture.gouv.fr');
        $crawler = $client->waitFor('[jhitranslate="home-page.login"]');

        $crawler->selectButton('Se connecter')->click();

        $crawler = $client->waitFor("#username");

        $crawler->filter('#username')->sendKeys('bouffontheatre');
        $crawler->filter('#password')->sendKeys('SBII79bouffon');

        $crawler->selectButton('Connexion')->click();

        $client->waitFor('#anything');
    }
}
