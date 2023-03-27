<?php

require_once APP_ROOT . '/models/CategoriesBC.php';
require_once APP_ROOT . '/models/ProductsBC.php';

class Menu extends Controller {
    
    private $data;

    public function index($slug = null) {
        $slug = $slug != null ? strtolower($slug) : null;
        $CategoriesBC = new CategoriesBC();

        switch ($slug) {
            case "assortiment":
                $ProductsBC = new ProductsBC();
                $products = $ProductsBC->getProducts();

                $this->data['products'] = $products;
                $this->data['title'] = 'Assortiment';
                $this->data['description'] = '';
                $this->data['keywords'] = 'Assortiment';
                $this->data['breadcrumb'] = URL_ROOT;
                $this->view('Products', $this->data);

                break;
            case null:
                $categories = $CategoriesBC->getParentCategories();

                $this->data['categories'] = $categories;
                $this->data['title'] = 'Menu';
                $this->data['description'] = '';
                $this->data['keywords'] = '';
                $this->view('Categories', $this->data);

                break;
            default:
                $category = $CategoriesBC->getCategoryBySlug($slug);
                
                if (isset($_GET['view']) && $_GET['view'] == "all") {
                    $ProductsBC = new ProductsBC();
                    $products = $ProductsBC->getAllProductsForCategory($category);

                    $this->data['products'] = $products;
                    $this->data['title'] = $category->DESCRIPTION;
                    $this->data['description'] = '';
                    $this->data['keywords'] = $category->DESCRIPTION;
                    $this->data['breadcrumb'] = URL_ROOT . "/Menu/" . $slug;

                    $this->view('Products', $this->data);
                } else if ($CategoriesBC->hasSubCategories($category)) {
                    $categories = $CategoriesBC->getSubCategories($category);

                    $this->data['category'] = $category;
                    $this->data['categories'] = $categories;
                    $this->data['title'] = $category->DESCRIPTION;
                    $this->data['description'] = '';
                    $this->data['keywords'] = $category->DESCRIPTION;
                    $this->data['breadcrumb'] = URL_ROOT;
                    
                    $this->view('Categories', $this->data);
                } else {
                    $ProductsBC = new ProductsBC();
                    $products = $ProductsBC->getProductsForCategory($category);

                    $this->data['products'] = $products;
                    $this->data['title'] = $category->DESCRIPTION;
                    $this->data['description'] = '';
                    $this->data['keywords'] = $category->DESCRIPTION;
                    
                    $parent_category = $CategoriesBC->getParentCategoryBySlug($slug);
                    if ($parent_category !== false && $parent_category->SLUG != "") {
                        $this->data['breadcrumb'] = URL_ROOT . "/Menu/" . $parent_category->SLUG;
                    } else {
                        $this->data['breadcrumb'] = URL_ROOT . "/Menu/";
                    }

                    $this->view('Products', $this->data);
                }

                break;
        }
    }

    public function search() {
        $query = isset($_GET['q']) ? $_GET['q'] : "";

        $ProductsBC = new ProductsBC();
        $products = $ProductsBC->getProductsByQuery($query);

        $data = [
            'products' => $products
        ];

        $this->view('Search', $data);
    }

}