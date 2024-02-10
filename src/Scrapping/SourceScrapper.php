<?php


namespace App\Scrapping;


use App\Entity\Insight;
use App\Entity\Source;
use App\Repository\InsightRepository;
use App\Repository\ShowRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Remote\Service\DriverCommandExecutor;
use Facebook\WebDriver\Remote\Service\DriverService;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
use Symfony\Component\Panther\DomCrawler\Link;

class SourceScrapper
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $params,
        private InsightRepository $insightRepository,
        private ShowRepository $showRepository
    ) {
    }

    public static function createReliableClient()
    {
        return Client::createChromeClient(null, [
            '--user-agent=Mozilla/5.0 (X11; Linux x86_64; rv:102.0) Gecko/20100101 Firefox/102.0',
            '--headless=new',
            '--window-size=1200,1100',
            '--disable-gpu',
            '--no-sandbox'
        ]);
    }

    public function scrapBookings()
    {

        $client = self::createReliableClient();

        $crawler = $client->request('GET', "https://pro.billetreduc.com");

        /** @var Crawler $crawler */
        $form = $crawler->selectButton('SE CONNECTER')->form();

        $form['login'] = $this->params->get('billetreduc_login');
        $form['pass'] = $this->params->get('billetreduc_password');

        $crawler = $client->submit($form);

        $crawler = $client->waitFor('.evtc .g');
        $crawler = $client->refreshCrawler();

        $links = $crawler->filter('.box-item.br-verysmallbtn.br-blue')->links();
        $uris = array_unique(array_map(function ($link) {
            return $link->getUri();
        }, $links));

        foreach ($uris as $uri) {
            /** @var Link $link */
            $crawler = $client->request('GET', $uri);

            $client->waitFor('.bigeventtitle b');
            $crawler = $client->refreshCrawler();
            $showTitle = $crawler->filter('.bigeventtitle b')->getText();
            $show = $this->showRepository->findOneByTitleOrBilletreducTitle($showTitle);

            if (!$show) {
                continue;
            }

            $table = $crawler->filter('table.xt');
            $html = $table->getElement(0)->getDomProperty('outerHTML');

            $insight = new Insight();
            $insight->setRelatedShow($show);
            $insight->setCreatedAt(new DateTimeImmutable());
            $insight->setType(Insight::TYPE_BOOKING_COUNT);
            $insight->setHtml($html);

            $this->entityManager->persist($insight);

            // $date = null;
            // $dateTime = null;

            // $table->filter('tr')->each(function ($tr) use (&$date, &$dateTime) {
            //     $class = $tr->getAttribute('class');
            //     if ($class === 'tbNewDay') {
            //         $date = trim($tr->getText());
            //         $date = DateTimeImmutable::createFromFormat('l d F Y', trim($tr->getText()));
            //     } else {
            //         if ($class == "heure") {
            //             $td = $tr->filter('td.heure');
            //             var_dump($td->getText());
            //             preg_match('#([0-9]{2}h[0-9]{2}).+#', trim($td->getText()), $matches);
            //             var_dump($matches[1]);
            //         }
            //     }
            // });

        }

        $is = $this->insightRepository->findAll();
        foreach ($is as $i) {
            $this->entityManager->remove($i);
        }

        $this->entityManager->flush();
        // if ($source->getScript()) {
        //     $client->executeScript($source->getScript());
        // }

        // if (($selector = $source->getSelector())) {
        //     $client->waitFor($selector);
        //     $client->refreshCrawler();
        //     $crawler = $client->getCrawler();
        //     $crawler = $crawler->filter($selector);
        // }

        // if ($crawler->count() == 0) {
        //     throw new \Exception("Could not fetch the selected source {$source->getId()}");
        // }

        // $text = $crawler->text();

        // if ($source->getRegex()) {
        //     if (preg_match($source->getRegex(), $text, $matches)) {
        //         $text = $matches[$source->getRegexCaptureGroup()];
        //     }
        // }

        // $result = trim($text);
        // if (!$text) {
        //     throw new \Exception("Source {$source->getId()} returned an empty result");
        // }
        // $insight = new Insight();
        // $insight->setDate(new \DateTime());
        // $insight->setJournal($source->getJournal());
        // $insight->setType($source->getInsightType());
        // $insight->setValue($result);

        // $source->getJournal()->addInsight($insight);

        // $this->entityManager->persist($insight);
        // $this->entityManager->flush();

        // return $result;
    }
}
