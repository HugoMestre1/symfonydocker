<?php

namespace App\Commands;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CountryCommand extends Command
{
    protected function configure()
    {
        $this->setName('country')
            ->setDescription('Shows country')
            ->setHelp('This command demonstrates the usage of a table helper')
            ->addArgument('country',InputArgument::REQUIRED, 'Name of the country to search ');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table       = new Table($output);
        $countryName = $input->getArgument('country');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://restcountries.eu/rest/v2/name/' . $countryName);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $data     = json_decode($response);

        if (!empty($data->status) && $data->status == 404) {
            $output->writeln("Pais não encontrado");
            exit();
        }

        $countries = [];
        foreach ($data as $country) {
            $countries[] = [
                $country->name,
                $country->alpha2Code,
                $country->alpha3Code,
                $country->numericCode,
                $country->region,
                $country->population
            ];
        }

        $table->setHeaderTitle('Books')->setHeaders([
            'Nome',
            'Alpha 2 Code',
            'Alpha 3 Code',
            'Numeric Code',
            'Continente',
            'Populacao'
        ])->setRows($countries);

        $table->render();
    }
}