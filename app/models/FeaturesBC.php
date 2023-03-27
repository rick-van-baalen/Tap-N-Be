<?php

class FeaturesBC extends BusinessComponent {

    public function addFeaturesToProducts($products) {
        foreach ($products as $product) {
            try {
                $this->query("SELECT features.ID, features.DESCRIPTION, features.AFTER, products_features.VALUE
                FROM features
                LEFT JOIN products_features ON features.ID = products_features.FEATURE_ID
                WHERE products_features.PRODUCT_ID = ?");
                $this->bind(1, $product->ID);
                $this->execute();

                $used_features = $this->getResults();
                $product->USED_FEATURES = $used_features;
            } catch (PDOException $e) {
                continue;
            }
        }

        return $products;
    }

}