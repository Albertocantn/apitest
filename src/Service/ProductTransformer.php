<?php

namespace App\Service;

use App\Entity\Category;


class ProductTransformer
{
    public function __construct()
    {

    }

    public function productTransformer($products, $currency = null)
    {
        $data = [];
        if (is_array($products)) {
            foreach ($products as $product) {
                $category = $product->getCategory();
                $categoryName = null;
                if (!is_null($category)) {

                    $categoryName = $category->getName();

                }
                if (is_null($currency)) {
                    $productCurrency = $product->getCurrency();
                } else {
                    $productCurrency = $currency;
                }

                $data[] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'category.name' => $categoryName,
                    'price' => round($product->getPrice(),2),
                    'currency' => $productCurrency
                ];


            }
        } else {
            $category = $products->getCategory();
            $categoryName = null;
            if (!is_null($category)) {

                $categoryName = $category->getName();

            }
            if (is_null($currency)) {
                $productCurrency = $products->getCurrency();
            } else {
                $productCurrency = $currency;
            }

            $data[] = [
                'id' => $products->getId(),
                'name' => $products->getName(),
                'category.name' => $categoryName,
                'price' => $products->getPrice(),
                'currency' => $productCurrency
            ];

        }

        return $data;

    }

}