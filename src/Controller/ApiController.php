<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\ProductTransformer;
use App\Service\CategoryTransformer;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ProductService;
use App\Service\CategoryService;
use App\Service\ChangeCurrencyService;


/**
 * Class ApiController
 *
 * @Route("/api")
 */
class ApiController extends AbstractFOSRestController
{
    private $productService;
    private $serializer;
    private $categoryService;
    private $changeCurrencyService;
    private $productTransformer;
    private $categoryTransformer;

    public function __construct(
        ProductService $productService,
        SerializerInterface $serializer,
        CategoryService $categoryService,
        ChangeCurrencyService $changeCurrencyService,
        ProductTransformer $productTransformer,
        CategoryTransformer $categoryTransformer
    ) {
        $this->productService = $productService;
        $this->serializer = $serializer;
        $this->categoryService = $categoryService;
        $this->changeCurrencyService = $changeCurrencyService;
        $this->productTransformer = $productTransformer;
        $this->categoryTransformer = $categoryTransformer;
    }

    /**
     * @Rest\Get("/v1/products", name="products_list_all")
     */
    public function getAllProduct()
    {
        $products = [];

        try {
            $code = 200;
            $error = false;
            $products = $this->productService->getAllProducts();
            $products = $this->productTransformer->productTransformer($products);
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Products - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $products : $message,
        ];

        return new Response($this->serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/v1/products/featured", name="products_list_featured")
     */
    public function getFeaturedProduct(Request $request)
    {

        try {

            $products = $this->productService->getFeaturedProducts();
            $code = 200;
            $error = false;

            if ($request->query->has("currency")) {
                $currency = $request->query->get("currency");
                if ($currency == Product::EUR || $currency == Product::USD) {
                    $USDPriceFromEUR = $this->changeCurrencyService->getUSDValueFromEUR();
                    $EURPriceFromUSD = $this->changeCurrencyService->getEURValueFromUSD();

                    $currencyAndValues = [
                        'currency' => $currency,
                        'USDPriceFromEUR' => $USDPriceFromEUR,
                        'EURPriceFromUSD' => $EURPriceFromUSD
                    ];

                    $products = $this->changeCurrencyService->changeCurrency($products, $currencyAndValues);
                } else {
                    $code = 500;
                    $error = true;
                    $message = "The selected currency is not available, select one between USD and EUR";
                }
                $data = $this->productTransformer->ProductTransformer($products, $currency);

            } else {
                $data = $this->productTransformer->productTransformer($products);
            }


        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Products - Error: { $ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $data : $message,
        ];

        return new Response($this->serializer->serialize($response, "json"));
    }


    /**
     * @Rest\Post("/v1/product", name="product_add")
     */

    public function addProduct()
    {

        $product = [];

        try {
            $code = 201;
            $error = false;
            $product = $this->productService->addProduct();
            if (is_null($product)) {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to add new product - Verify your parameters (currency only acepted EUR or USD and you must provide a name)";
            }


        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to add new product - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 201 ? $product : $message,
        ];

        return new Response($this->serializer->serialize($response, "json"));
    }


    /**
     * @Rest\Post("/v1/category", name="category_add")
     */

    public function addCategory()
    {

        $category = [];
        $message = "";

        try {
            $code = 201;
            $error = false;
            $category = $this->categoryService->addCategory();
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to add new category - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 201 ? $category : $message,
        ];

        return new Response($this->serializer->serialize($response, "json"));
    }


    /**
     * @Rest\Get("/v1/categories", name="categories_list_all")
     */
    public function getAllCategories()
    {

        $categories = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $categories = $this->categoryService->getAllCategories();
            $categories = $this->categoryTransformer->categoryTransformer($categories);


        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Categories - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $categories : $message,
        ];

        return new Response($this->serializer->serialize($response, "json"));
    }


    /**
     * @Rest\Put("/v1/category/{id}", name="category_edit")
     */

    public function editCategory($id)
    {

        $category = [];

        try {

            $category = $this->categoryService->editCategory($id);
            if (!is_null($category)) {
                $code = 200;
                $error = false;
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to updating category - Error: The category id does not exist";
            }
        } catch
        (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the current category - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $category : $message,
        ];

        return new Response($this->serializer->serialize($response, "json"));
    }


    /**
     * @Rest\Delete("/v1/category/{id}", name="category_remove")
     */


    public function deleteCategory($id)
    {

        try {
            $code = 200;
            $error = false;
            $message = $this->categoryService->deleteCategory($id);


        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the current category - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $message,
        ];

        return new Response($this->serializer->serialize($response, "json"));
    }


}