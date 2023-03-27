<?php

require_once APP_ROOT . '/models/CategoriesBC.php';
require_once APP_ROOT . '/models/FeaturesBC.php';

class ProductsBC extends BusinessComponent {

    public function getProducts() {
        $this->query("SELECT * from products ORDER BY SORT ASC");
        $products = $this->getResults();

        $CategoriesBC = new CategoriesBC();
        $products = $CategoriesBC->addCategoryToProducts($products);

        $FeaturesBC = new FeaturesBC();
        $products = $FeaturesBC->addFeaturesToProducts($products);

        return $products;
    }
    
    public function getProductsByQuery(string $query) {
        $sql = "SELECT products_categories.PRODUCT_ID, products_categories.CATEGORY_ID, products.ID, products.DESCRIPTION as DESCRIPTION, products.SUMMARY, categories.DESCRIPTION AS CATEGORY_DESCRIPTION, products.IMAGE, products.PRICE
        from products_categories
        left join products on products_categories.PRODUCT_ID = products.ID
        left join categories on products_categories.CATEGORY_ID = categories.ID
        where products.DESCRIPTION LIKE '%" . $query . "%' or products.SUMMARY LIKE '%" . $query . "%' or categories.DESCRIPTION LIKE '%" . $query . "%' or categories.PARENT_DESCRIPTION LIKE '%" . $query . "%'";

        $this->query($sql);
        $products = $this->getResults();

        $FeaturesBC = new FeaturesBC();
        $products = $FeaturesBC->addFeaturesToProducts($products);

        return $products;
    }

    public function getProductsForCategory($category) {
        try {
            $this->query("SELECT products_categories.PRODUCT_ID, products.DESCRIPTION, products.SUMMARY, products.ID, products.PRICE, products.IMAGE, products.SORT FROM products_categories LEFT JOIN products ON products_categories.PRODUCT_ID = products.ID WHERE products_categories.CATEGORY_ID = ?");
            $this->bind(1, $category->ID);
            $this->execute();
            $products = $this->getResults();

            $CategoriesBC = new CategoriesBC();
            $products = $CategoriesBC->addCategoryToProducts($products);
            
            $FeaturesBC = new FeaturesBC();
            $products = $FeaturesBC->addFeaturesToProducts($products);
            
            return $products;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getAllProductsForCategory($category) {
        try {
            $this->query("SELECT products.ID, products.DESCRIPTION, products.SUMMARY, products.PRICE, products.IMAGE, products.SORT
            FROM products_categories
            LEFT JOIN products ON products_categories.PRODUCT_ID = products.ID
            LEFT JOIN categories ON products_categories.CATEGORY_ID = categories.ID
            WHERE categories.DESCRIPTION = ? OR categories.PARENT_DESCRIPTION = ?");
            $this->bind(1, $category->DESCRIPTION);
            $this->bind(2, $category->DESCRIPTION);
            $this->execute();
            $products = $this->getResults();
            
            $FeaturesBC = new FeaturesBC();
            $products = $FeaturesBC->addFeaturesToProducts($products);
            
            return $products;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

}