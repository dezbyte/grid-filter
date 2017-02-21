<?php

use Dez\Url\Url;
use DezByte\Grid\Grid;

include_once './../vendor/autoload.php';

$url = new Url();
$grid = new Grid($url);

$url->setBasePath('/administrator');

$grid->processRequest('/catalog/filter/manufacturer/asus-samsung-dell-ne-acer/ekran/ge-1920-eq-128-192/processor/eq-arm-qualcomm/brand/eq-lenovo');

$grid->setPrefixPath('catalog/products/list');

$filter = $grid->getFilter();
$filter->reset(Grid::RESET_COLUMN, 'manufacturer')->reset(Grid::RESET_COLUMN, 'ekran');

echo $filter, PHP_EOL, $filter->filter('brand', 'coca_cola'), PHP_EOL, $filter->reset(Grid::RESET_COLUMN, 'brand'), PHP_EOL, $filter->filter('brand', 'pepsi');

$array = [
  'size' => ['100x120', '120x150', '200x140', '10x3000', ],
  'weight' => ['10kg', '31kg', '57.850g', '1ton', ],
];

foreach ($array as $name => $items) {
  foreach ($items as $item) {
    $filter->filter($name, $item, $grid->getAllowFilterNames()[rand(0, 7)]);
  }
}


$grid->setFilterMarker('product-filter');

echo PHP_EOL, $filter->render();

$grid2 = new Grid($url);
$grid2->setPrefixPath('catalog/products/list');
$grid2->setFilterMarker('product-filter');
$grid2->processRequest($filter->render());


echo PHP_EOL, $grid2;