<?php

use DezByte\Grid\Grid;

include_once './../vendor/autoload.php';

$grid = new Grid();

$grid->processRequest('/catalog/products/filter/vendor/eq-asus-samsung-dell-ne-acer/display/ge-1920-eq-128-192/processor/eq-arm-qualcomm');


var_dump($grid->filter('vendor', 'microsoft', 'eq', true));
