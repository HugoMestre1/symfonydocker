<?php

namespace App\Commands;

use App\Entity\CountrySearch;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;

class CountryCommand extends Command
{
    private $container;

    public function __construct(Container $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function configure()
    {
        $this->setName('country')
             ->setDescription('Shows country')
             ->setHelp('This command demonstrates the usage of a table helper')
             ->addArgument('limit', InputArgument::OPTIONAL, 'Number of row to search');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $limit = $input->getArgument('limit');

        try {
            $lastSearchCountries = $this->container->get('doctrine')->getManager()->getRepository(CountrySearch::class)->getLastSearchs($limit);
        } catch (\Exception $ex) {
            // Alternative for get the last search save in database
            $lastSearchCountries = $this->getLastSearchV2($limit);
        }

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
            curl_setopt($ch, CURLOPT_URL, 'https://restcountries.eu/rest/v2/name/' . $infoCountry["name"]);
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

    /**
     * This function is used when don't get the doctrine service
     *
     * @param int $limit
     *
     * @return array
     */
    public function getLastSearchV2($limit)
    {
        $limit = $limit ?? 5;
        $conn  = new \PDO("mysql:host=mysql;dbname=symfony", "symfony", "symfony");
        $stmt  = $conn->prepare("SELECT country_name, search_date FROM country_search ORDER BY id DESC limit $limit");
        $stmt->execute();

        return $stmt->fetchAll();
    }
}