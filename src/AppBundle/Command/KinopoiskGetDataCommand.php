<?php

namespace AppBundle\Command;

use AppBundle\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class KinopoiskGetDataCommand
 *
 * @package AppBundle\Command
 */
class KinopoiskGetDataCommand extends ContainerAwareCommand
{
    private const TOP_COUNT  = 10;
    private const TOP_URL    = 'https://www.kinopoisk.ru/top/';
    private const USER_AGENT = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.102 Safari/537.36 Vivaldi/2.0.1309.42';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * KinopoiskGetDataCommand constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this->setName('kinopoisk:get-data')
            ->setDescription('Get TOP-10 from Kinopoisk rating')
            ->addOption('date', 'd', InputOption::VALUE_REQUIRED, 'Date for selection in yyyy-mm-dd format', new \DateTime())
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = $input->getOption('date');
        if (!($date instanceof \DateTime)) {
            $date = new \DateTime($date);
        }

        $movies = $this->entityManager->getRepository(Movie::class)->getAllByDate($date);
        if (\count($movies) > 0) {
            return;
        }

        $this->entityManager->getRepository(Movie::class)->resetQueryBuilder();

        $ch = curl_init(sprintf(self::TOP_URL . 'day/%s/', $date->format('Y-m-d')));

        curl_setopt_array($ch, [
            CURLOPT_USERAGENT      => self::USER_AGENT,
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $data = curl_exec($ch);

        $document = new \DOMDocument();
        $document->loadHTML($data, LIBXML_NOERROR);

        $xPath = new \DOMXPath($document);

        for ($counter = 1; $counter <= self::TOP_COUNT; $counter++) {
            $nodeList  = $xPath->query(sprintf('//*[@id="top250_place_%d"]', $counter));
            $rawString = trim(preg_replace('#\n{2,}#', "\n", preg_replace('# {2,}#', '', $nodeList->item(0)->nodeValue)));
            $rawString = trim(str_replace("\xC2\xA0", ' ', $rawString));

            $data = explode("\n", $rawString);

            $position = (int)$data[0];
            $name     = trim($data[1]);
            $rate     = trim($data[2]);

            $rating = floatval($rate);
            preg_match('#\(([\d\s]+)\)#', $rate, $votersMatch);
            $votersCount = (int)str_replace(' ', '', $votersMatch[1]);

            preg_match('#^(.+?)\(#', $name, $rMatches);
            preg_match('#\d{4}#', $name, $yMatches);
            preg_match('#(?!(.*\)))(.*)$#', $name, $oMatches);

            $year         = (int)$yMatches[0];
            $russianName  = trim($rMatches[1]);
            $originalName = $oMatches[0] !== '' ? trim($oMatches[0]) : NULL;

            $movie = (new Movie())->setYear($year)
                ->setPosition($position)
                ->setRating($rating)
                ->setVotersCount($votersCount)
                ->setRussainName($russianName)
                ->setOriginalName($originalName)
                ->setInTopDate($date);

            $this->entityManager->persist($movie);
        }

        $this->entityManager->flush();
    }

}
