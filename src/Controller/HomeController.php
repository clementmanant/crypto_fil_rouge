<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{


    #[Route('/', name: 'app_home')]
    public function index(): Response
    {


        $response = "hello";
        print_r($this->CoinGeckoAPI());

        return $this->render('home/index.html.twig', [
            $response => 'HomeController',
        ]);
    }

    //TODO: Vérifier le delta en cas de bug des API
    public function moyenne(int $limit, array $coinMarketArray, array $binanceArray) : array
    {
        $finalArray = [];
        for ($i = 0; $i < $limit; $i++) {
            $array = [];

            for ($j = 0; $j < $limit; $j++) {
                if ($coinMarketArray[$i][1] === $binanceArray[$j][1]) {
                    $averagePrice = ($coinMarketArray[$i][3] + $binanceArray[$i][3]) / 2;
                    $averageMarketCap = ($coinMarketArray[$i][4] + $binanceArray[$i][4]) / 2;
                    $array[0] = $i;
                    $array[1] = $coinMarketArray[$i]->{'name'};
                    $array[2] = $coinMarketArray[$i]->{'symbol'};
                    $array[3] = $averagePrice;
                    $array[4] = $averageMarketCap;
                }
            }
        }
        return $finalArray;
    }

    private function CoinGeckoAPI() : array{
        $url = 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=35&page=1&sparkline=false';

        $parameters = [];

        $headers = [
            'Accepts: application/json'
        ];




        $qs = http_build_query($parameters); // query string encode the parameters
        $request = "{$url}?{$qs}"; // create the request URL

        $curl = curl_init(); // Get cURL resource
        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => $request,            // set the request URL
            CURLOPT_HTTPHEADER => $headers,     // set the headers
            CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
        ));

        $response = curl_exec($curl); // Send the request, save the response


        $myObject = json_decode($response);

        $limit = 30;

        $finalArray = [];
        for ($i = 0; $i < $limit; $i++) {
            $data = $myObject[$i];
            $result = $data->{'current_price'};
            $market_cap = $data->{'market_cap'};
            $image = $data->{'image'};
            $array = [];
            $id = $i;
            $_name = $data->{'name'};
            $symbol = $data->{'symbol'};
            $_price = round($result, 2);
            $ArroundMarket = round($market_cap, 2);

            $array[0] = $id;
            $array[1] = $_name;
            $array[2] = $symbol;
            $array[3] = $image;
            $array[4] = $_price;
            $array[5] = $ArroundMarket;

            array_push($finalArray, $array);

        }
        curl_close($curl); // Close request
        return $finalArray;
    }

    private function coinMarketCapAPI() : array
    {
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';
        $id = 'market_cap';
        $limit = 30;
        $parameters = ['sort' => $id, 'limit' => $limit];

        $headers = [
            'Accepts: application/json',
            'X-CMC_PRO_API_KEY: 1379c989-f465-4f09-884c-167cfccd8710'
        ];

        $qs = http_build_query($parameters); // query string encode the parameters
        $request = "{$url}?{$qs}"; // create the request URL

        // print($request);

        $curl = curl_init(); // Get cURL resource
        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => $request,            // set the request URL
            CURLOPT_HTTPHEADER => $headers,     // set the headers
            CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
        ));

        $response = curl_exec($curl); // Send the request, save the response


        $myObject = json_decode($response);


        $finalArray = [];
        for ($i = 0; $i < $limit; $i++) {
            $data = $myObject->{'data'}[$i];
            $array = [];
            $id = $i;
            $name = $data->{'name'};
            $symbol = $data->{'symbol'};
            $price = round($data->{'quote'}->{'USD'}->{'price'}, 2);
            $market_cap = round($data->{'quote'}->{'USD'}->{'market_cap'}, 2);
            $array[0] = $id;
            $array[1] = $name;
            $array[2] = $symbol;
            $array[3] = $price;
            $array[4] = $market_cap;

            array_push($finalArray, $array);
        }
        print_r($finalArray);
        curl_close($curl); // Close request
        return $finalArray;
    }
}
