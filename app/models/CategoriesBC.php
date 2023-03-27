<?php

class CategoriesBC extends BusinessComponent {

    public function getCategories() {
        $this->query("SELECT * FROM categories ORDER BY PARENT_ID, SORT ASC");
        $categories = $this->getResults();

        foreach ($categories as $category) {
            $this->query("SELECT DESCRIPTION FROM categories WHERE ID = ?");
            $this->bind(1, $category->PARENT_ID);
            $this->execute();
            $result = $this->getResult();

            $category->PARENT_DESCRIPTION = $result != "" ? $result->DESCRIPTION : "";
        }

        return $categories;
    }

    public function getCategoryBySlug($slug) {
        try {
            $this->query("SELECT * FROM categories WHERE slug = ?");
            $this->bind(1, $slug);
            $this->execute();
            return $this->getResult();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function hasSubCategories($category) {
        try {
            $this->query("SELECT * FROM categories WHERE PARENT_ID = ?");
            $this->bind(1, $category->ID);
            $this->execute();

            $result = $this->rowCount();
            return $result > 0 ? true : false;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getParentCategories() {
        $this->query("SELECT * FROM categories WHERE PARENT_ID IS NULL OR PARENT_ID = 0 ORDER BY SORT ASC");
        return $this->getResults();
    }

    public function getParentCategoryBySlug($slug) {
        $this->query("SELECT PARENT_ID FROM categories WHERE SLUG = ?");
        $this->bind(1, $slug);
        $this->execute();
        $category = $this->getResult();

        if ($category->PARENT_ID != "") {
            $this->query("SELECT SLUG FROM categories WHERE ID = ?");
            $this->bind(1, $category->PARENT_ID);
            $this->execute();
            return $this->getResult();
        } else {
            return false;
        }
    }

    public function getSubCategories($category) {
        try {
            $this->query("SELECT * FROM categories WHERE PARENT_ID = ? ORDER BY PARENT_ID, SORT ASC");
            $this->bind(1, $category->ID);
            $this->execute();
            return $this->getResults();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function addCategoryToProducts($products) {
        foreach ($products as $product) {
            try {
                $this->query("SELECT categories.ID, categories.DESCRIPTION FROM products_categories LEFT JOIN categories ON products_categories.CATEGORY_ID = categories.ID WHERE products_categories.PRODUCT_ID = ?");
                $this->bind(1, $product->ID);
                $this->execute();

                $categories = $this->getResults();
                $product->CATEGORIES = $categories;
            } catch (PDOException $e) {
                continue;
            }
        }

        return $products;
    }

}