<?php

namespace App\Commands;

use App\Entity\CountrySearch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CountryCommand extends Command
{
    private $repository;
    protected static $defaultName = 'app:country';

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(CountrySearch::class);
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Shows country')
             ->setHelp('This command demonstrates the usage of a table helper')
             ->addArgument('limit', InputArgument::OPTIONAL, 'Number of row to search');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $limit = $input->getArgument('limit');

        $lastSearchCountries = $this->repository->getLastSearchs($limit);

        $countriesSearch = [];

        foreach ($lastSearchCountries as $lastSearchCountry) {

            $country    = $lastSearchCountry["country_name"] ?? null;
            if (is_object($lastSearchCountry["search_date"])){
                $searchDate = $lastSearchCountry["search_date"]->format("Y-m-d H:i:s");
            }else{
                $searchDate = $lastSearchCountry["search_date"] ?? null;
            }


            $infoSearch = [
                "name"       => $country,
                "dateSearch" => $searchDate
            ];
            array_push($countriesSearch, $infoSearch);
        }

        $countries = [];
        foreach ($countriesSearch as $infoCountry) {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://restcountries.eu/rest/v2/name/' . urlencode($infoCountry["name"]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            $data     = json_decode($response);

            if (!empty($data->status) && $data->status == 404) {
                continue;
            }

            foreach ($data as $country) {
                $countries[] = [
                    $country->name,
                    $country->alpha2Code,
                    $country->alpha3Code,
                    $country->numericCode,
                    $country->region,
                    $country->population,
                    $infoCountry["dateSearch"]
                ];
            }
        }

        $table->setHeaderTitle("Countries")->setHeaders([
            'Nome',
            'Alpha 2 Code',
            'Alpha 3 Code',
            'Numeric Code',
            'Continente',
            'Populacao',
            'Data da pesquisa'
        ])->setRows($countries);

        $table->render();
    }
}