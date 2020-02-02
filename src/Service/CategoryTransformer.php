<?php

namespace App\Service;


class CategoryTransformer
{
    public function __construct()
    {

    }

    public function categoryTransformer($categories)
    {
        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
            ];
        }

        return $data;

    }

}