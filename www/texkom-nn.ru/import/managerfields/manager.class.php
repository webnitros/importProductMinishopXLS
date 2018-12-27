<?php


class extensionManager
{
    /* @var modX|null $modx */
    protected $modx = null;
    public $metaTree = array(
        'msProductData' => array(
            'fieldMeta' => array(
                'count' => array(
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 0,
                ),
                'barcode' => array(
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => false,
                ),
                'baseunit' => array(
                    'dbtype' => 'int',
                    'precision' => '3',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 0,
                ),
                'item_type' => array(
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => false,
                ),
                'type_nomenclature' => array(
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
            )
        ),
    );

    /**
     * msCmlGenerateMapFields constructor.
     * @param modX $modx
     */
    function __construct(modX &$modx)
    {
        $this->modx = $modx;
    }


    /**
     * Генерация дополнительных таблиц msProductData
     */
    private function miniShop2($action)
    {

        /* @var miniShop2 $miniShop2 */
        $miniShop2 = $this->modx->getService('miniShop2');
        if ($miniShop2 instanceof miniShop2) {
            if ($action == 'create') {

                $cache = $this->modx->getCacheManager();

                $source = $miniShop2->config['pluginsPath'] . 'templates/default/images/modx-icon-color.svg';
                $target = MODX_BASE_PATH . 'logo/modx-logo.svg';
                $cache->copyFile($source, $target);


                $dir = 'plugins/extensionmanager/';



                /**
                    core
                 */
                $corePath = $miniShop2->config['corePath'] . $dir;
                

                if (file_exists($corePath)) {
                    // Удаляем все если папка существует
                    $cache->deleteTree($corePath, ['deleteTop' => true, 'extensions' => []]);
                }

                // Создаем файлы
                $source = dirname(dirname(__FILE__)) . '/manager/core/minishop2/plugins';
                $cache->copyTree($source, dirname($corePath));


                /**
                    assets
                 */
                $assetsPath = $miniShop2->config['assetsPath'] . $dir;
                if (file_exists($assetsPath)) {
                    // Удаляем все если папка существует
                    $cache->deleteTree($assetsPath, ['deleteTop' => true, 'extensions' => []]);
                }

                // Создаем файлы
                $source = dirname(dirname(__FILE__)) . '/manager/assets/minishop2/plugins/';
                $cache->copyTree($source, dirname($assetsPath));



                $miniShop2->addPlugin('extensionManager', '{core_path}components/minishop2/plugins/extensionmanager/index.php');

                return $this->createFields('msProductData');
            } else {
                $miniShop2->removePlugin('extensionManager');
            }
        }
        return true;
    }



    /**
     * @param $class
     * @return bool|array
     */
    private function getMeta($class)
    {
        if (isset($this->metaTree[$class])) {
            return $this->metaTree[$class];
        }
        return false;
    }

    /**
     * @param $class
     * @return bool|array
     */
    public function classExtension($class)
    {
        if ($meta = $this->getMeta($class)) {
            $this->modx->loadClass($class);
            if (isset($meta['fieldMeta']) and count($meta['fieldMeta']) > 0) {
                foreach ($meta['fieldMeta'] as $field => $options) {
                    if (!isset($this->modx->map[$class]['fields'][$field])) {
                        $this->modx->map[$class]['fields'][$field] = '';
                        $this->modx->map[$class]['fieldMeta'][$field] = $options;
                    }
                }
            }
            if (isset($meta['indexes']) and count($meta['indexes']) > 0) {
                foreach ($meta['indexes'] as $field => $options) {
                    if (!isset($this->modx->map[$class]['indexes'][$field])) {
                        $this->modx->map[$class]['indexes'][$field] = $options;
                    }
                }
            }
        }
        return false;
    }


    /**
     * @param string $class
     * @return bool
     */
    private function createFields($class)
    {
        if ($meta = $this->getMeta($class)) {
            $meta = $this->metaTree[$class];

            $this->classExtension($class);

            $manager = $this->modx->getManager();

            // 1. Add field database
            $fieldMetaData = $meta['fieldMeta'];
            if (is_array($fieldMetaData) and count($fieldMetaData) > 0) {

                $tableFields = [];
                $c = $this->modx->prepare("SHOW COLUMNS IN {$this->modx->getTableName($class)}");
                $c->execute();
                while ($cl = $c->fetch(PDO::FETCH_ASSOC)) {
                    $tableFields[$cl['Field']] = $cl['Field'];
                }


                foreach ($fieldMetaData as $field => $options) {
                    if (in_array($field, $tableFields)) {
                        /*unset($tableFields[$field]);
                        if (!$manager->alterField($class, $field, $options)) {
                            return false;
                        }*/
                    } else {
                        if (!$response = $manager->addField($class, $field, $options)) {
                            return false;
                        }
                    }
                }
            }

            // 2. Operate with indexes
            $indexesData = $meta['indexes'];
            if (is_array($indexesData) and count($indexesData) > 0) {

                // 2. Operate with indexes
                $indexes = [];
                $c = $this->modx->prepare("SHOW INDEX FROM {$this->modx->getTableName($class)}");
                $c->execute();
                while ($row = $c->fetch(PDO::FETCH_ASSOC)) {
                    $name = $row['Key_name'];
                    if (!isset($indexes[$name])) {
                        $indexes[$name] = [$row['Column_name']];
                    } else {
                        $indexes[$name][] = $row['Column_name'];
                    }
                }
                foreach ($indexes as $name => $values) {
                    sort($values);
                    $indexes[$name] = implode(':', $values);
                }


                // Add or alter existing
                foreach ($indexesData as $key => $index) {
                    ksort($index['columns']);
                    $index = implode(':', array_keys($index['columns']));
                    if (!isset($indexes[$key])) {
                        if (!$manager->addIndex($class, $key)) {
                            return false;
                        }
                    } else {
                        /*if ($index != $indexes[$key]) {
                            if (!$manager->removeIndex($class, $key) && $manager->addIndex($class, $key)) {
                                return false;
                            }
                        }*/
                    }
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Генерация дополнительных таблиц
     */
    public function generationMap($service, $action)
    {
        if (method_exists($this, $service)) {
            return $this->{$service}($action);
        }
        return true;
    }



}