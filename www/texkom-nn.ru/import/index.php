<?php
include_once dirname(__FILE__) . '/vendor/autoload.php';


$offset = 1;
$start = microtime(true);
$memory = memory_get_usage();
$reader = Asan\PHPExcel\Excel::load('data/import.xls', function (Asan\PHPExcel\Reader\Xls $reader) {
    $reader->setRowLimit(2005);
    $reader->setColumnLimit(16);
    $reader->ignoreEmptyRow(true);
});

$fields = array(
    'parent' => 'Группа_товаров',
    'parent_children' => 'Подгруппа_товаров',
    'article' => 'Код_товара',
    'brand' => 'Бренд',
    'vendor' => 'Производитель',
    'pagetitle' => 'Название',
    'baseunit' => 'Упаковка',
    'cat_number' => 'Кат_номер',
    'sov_number' => 'Совм_номер',
    'price' => 'Цена_1',
    'price_2' => 'Цена_2',
    'price_4' => 'Цена_4',
    'count' => 'Наличие',
    'storage_corp' => 'Корпсклад',
    'storage_transit' => 'Транзит',
);
$headers = array();
$rows = array();
foreach ($reader as $i => $row) {
    if ($i == 0) {
        foreach ($row as $col => $name_col) {
            if (in_array($name_col, $fields)) {
                $key = array_search($name_col, $fields);
                $headers[$col] = trim($key);
            }
        }
        continue;
    }

    $tmp = array();
    foreach ($headers as $k => $header) {
        if (isset($row[$k])) {
            $tmp[$header] = trim($row[$k]);
        }
    }
    $rows[] = $tmp;
}


$categoriesParent = array();
$categoriesChildren = array();
$vendorsPrepare = array();
$products = $rows;
foreach ($rows as $row) {
    $categoriesParent[] = trim($row['parent']);
    $categoriesChildren[] = trim($row['parent']) . ';' . trim($row['parent_children']);
}


$categoriesParent = array_values(array_filter(array_unique($categoriesParent)));
$categoriesChildren = array_values(array_filter(array_unique($categoriesChildren)));


foreach ($rows as $row) {
    $vendorsPrepare[] = trim($row['vendor']);
}
$vendorsPrepare = array_values(array_filter(array_unique($vendorsPrepare)));


define('MODX_API_MODE', true);
require_once dirname(dirname(__FILE__)) . '/index.php';


$category_template = $modx->getOption('ms2_template_category_default');
$product_template = $modx->getOption('ms2_template_product_default');
$root_category = 19;
$context_key = 'opt';

$parents = array();
$vendors = array();
$parentsChildren = array();

// Create parent
foreach ($categoriesParent as $name) {
    $criteria = array(
        'pagetitle' => $name,
        'context_key' => $context_key,
        'parent' => $root_category,
    );
    if (!$object = $modx->getObject('msCategory', $criteria)) {

        /* @var modResource $object */
        $object = $modx->newObject('modResource');
        $object->fromArray(array_merge($criteria, array(
            'longtitle' => $name,
            'class_key' => 'msCategory',
            'published' => 1,
            'isfolder' => 1,
            'template' => $category_template,
        )));
        $object->save();
    }
    $parents[$name] = $object->get('id');
}


// Create parent
foreach ($categoriesChildren as $name) {
    list($parent, $children) = explode(';', $name);
    $criteria = array(
        'pagetitle' => $children,
        'context_key' => $context_key,
        'parent' => $parents[$parent],
    );


    if (!$object = $modx->getObject('msCategory', $criteria)) {

        /* @var modResource $object */
        $object = $modx->newObject('modResource');
        $object->fromArray(array_merge($criteria, array(
            'longtitle' => $name,
            'class_key' => 'msCategory',
            'published' => 1,
            'isfolder' => 1,
            'template' => $category_template,
        )));
        $object->save();
    }
    $parentsChildren[$name] = $object->get('id');

}


// Create vendor
foreach ($vendorsPrepare as $name) {
    $criteria = array(
        'name' => $name,
    );

    /* @var msVendor $object */
    if (!$object = $modx->getObject('msVendor', $criteria)) {
        $object = $modx->newObject('msVendor');
        $object->fromArray($criteria);
        $object->save();
    }
    $vendors[$name] = $object->get('id');
}



$key_field = 'article';
foreach ($products as $menuindex => $product) {
    $key_parent = $product['parent'] . ';' . $product['parent_children'];
    unset($product['parent']);
    unset($product['parent_children']);



    $q = $modx->newQuery('msProduct');
    $q->where(array('Data.article' => $product['article']));
    $q->innerJoin('msProductData', 'Data', 'msProduct.id = Data.id');

    /* @var msProduct $object */
    if (!$object = $modx->getObject('msProduct', $q)) {
        $object = $modx->newObject('msProduct');
        $object->set('published', 1);
    }


    $product['vendor'] = isset($vendors[$product['vendor']]) ? $vendors[$product['vendor']] : 0;
    $object->fromArray(array_merge($product, array(
        'longtitle' => $product['pagetitle'],
        'context_key' => $context_key,
        'class_key' => 'msProduct',
        'parent' => $parentsChildren[$key_parent],
        'template' => $product_template,
        'hide_menu' => 0,
        'show_in_tree' => 0,
        'menuindex' => $menuindex,
    )));

    $object->save();
}

$count = count($rows);
exit("Импорт завершен. Импортировано товаров {$count}");