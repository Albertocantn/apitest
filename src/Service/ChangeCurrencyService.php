<?php

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\HttpClient\CurlHttpClient;


class ChangeCurrencyService
{


    public function __construct()
    {

    }

    public function getEURValueFromUSD()
    {
        $client = new CurlHttpClient();
        $response = $client->request('GET', 'https://api.exchangeratesapi.io/latest?base=USD&symbols=EUR');
        $contentType = $response->getHeaders()['content-type'][0];
        $contents = json_decode($response->getContent(), true);
        $EURPriceFromUSD = $contents['rates'][Product::EUR];
        return $EURPriceFromUSD;
    }


    public function getUSDValueFromEUR()
    {
        $client = new CurlHttpClient();
        $response = $client->request('GET', 'https://api.exchangeratesapi.io/latest?base=EUR&symbols=USD');
        $contentType = $response->getHeaders()['content-type'][0];
        $contents = json_decode($response->getContent(), true);
        $USDPriceFromEUR = $contents['rates'][Product::USD];
        return $USDPriceFromEUR;
    }

    public function changeCurrency($products, $currencyAndValues)
    {
        foreach ($products as $product) {
            $productcurrency = $product->getCurrency();
            $productprice = $product->getPrice();

            if ($currencyAndValues['currency'] != $productcurrency) {

                if ($currencyAndValues['currency'] == Product::USD) {
                    $product->setPrice($productprice *= $currencyAndValues['USDPriceFromEUR']);
                } elseif ($currencyAndValues['currency'] == Product::EUR) {
                    $product->setPrice($productprice *= $currencyAndValues['EURPriceFromUSD']);
                }

            }


        }
        return $products;
    }
}

?>