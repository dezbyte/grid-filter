<?php

use Dez\Url\Url;
use DezByte\Grid\Grid;

include_once './../vendor/autoload.php';

$url = new Url();
$grid = new Grid(new Url());

$grid->processRequest('/catalog/products/filter/manufacturer/asus-samsung-dell-ne-acer/attr-disp/ge-1920-eq-128-192/processor/eq-arm-qualcomm/brand/eq-lenovo');

var_dump($grid->getFilters(), $grid->filter('brand', 'hp'), $grid->filter('brand', 'lenovo'), $grid->filter('processor', 'intel'));
