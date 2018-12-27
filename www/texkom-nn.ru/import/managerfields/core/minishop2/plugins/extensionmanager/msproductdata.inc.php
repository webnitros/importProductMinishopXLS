<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 20.12.2018
 * Time: 1:39
 */

//
return array(
    'fields' => array(
        'count' => null,
        'baseunit' => null,
        'storage_corp' => 0,
        'storage_transit' => 0,
        'price_2' => 0,
        'price_4' => 0,
        'cat_number' => null,
        'sov_number' => null,
        'brand' => null,
    ),
    'fieldMeta' => array(
        'count' => array(
            'dbtype' => 'int',
            'precision' => '10',
            'attributes' => 'unsigned',
            'phptype' => 'integer',
            'null' => true,
            'default' => 0,
        ),
        'storage_corp' => array(
            'dbtype' => 'int',
            'precision' => '10',
            'attributes' => 'unsigned',
            'phptype' => 'integer',
            'null' => true,
            'default' => 0,
        ),
        'storage_transit' => array(
            'dbtype' => 'int',
            'precision' => '10',
            'attributes' => 'unsigned',
            'phptype' => 'integer',
            'null' => true,
            'default' => 0,
        ),
        'baseunit' => array(
            'dbtype' => 'varchar',
            'precision' => '255',
            'phptype' => 'string',
            'null' => false,
        ),
        'price_2' => array(
            'dbtype' => 'decimal',
            'precision' => '12,2',
            'phptype' => 'float',
            'null' => true,
            'default' => 0.0,
        ),
        'price_4' => array(
            'dbtype' => 'decimal',
            'precision' => '12,2',
            'phptype' => 'float',
            'null' => true,
            'default' => 0.0,
        ),
        'cat_number' => array(
            'dbtype' => 'varchar',
            'precision' => '255',
            'phptype' => 'string',
            'null' => false,
        ),
        'sov_number' => array(
            'dbtype' => 'varchar',
            'precision' => '255',
            'phptype' => 'string',
            'null' => false,
        ),
        'brand' => array(
            'dbtype' => 'varchar',
            'precision' => '255',
            'phptype' => 'string',
            'null' => false,
        ),
    ),
    'indexes' => array(
        'count' => array(
            'alias' => 'count',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => array(
                'count' => array(
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ),
            ),
        ),
    ),
);