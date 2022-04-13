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

        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';
        $id = 'market_cap';
        $limit = 2;
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
        // $array = get_object_vars((object)$response);
        // var_dump($array["scalar"]);
        /*$newArray = explode(",", $array["scalar"]);
        var_dump($newArray);*/
        // print_r(array_values($array["scalar"]));
        /*foreach (json_decode($response) as $value)
            $array[] = $value->status;
        print_r($array);*/
        $response = json_decode($response, true); // print json decoded response
        var_dump($response[0]);
        curl_close($curl); // Close request

        return $this->render('home/index.html.twig', [
            $response => 'HomeController',
        ]);
    }
}
