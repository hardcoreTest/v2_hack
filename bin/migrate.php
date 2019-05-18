<?php
use Doctrine\DBAL\Schema\Table;

if (!defined("PUBLIC_PATH")) {
    define ("PUBLIC_PATH", realpath(__DIR__));
}

if (!defined("APP_PATH")) {
    define ("APP_PATH", realpath(__DIR__ . '/../src/backend'));
}

if (!defined("TEMPLATES_PATH")) {
    define ("TEMPLATES_PATH", realpath(__DIR__ . '/../src/templates'));
}

if (!defined("CONFIG_PATH")) {
    define ("CONFIG_PATH", realpath(__DIR__ . '/../configs'));
}

if (!defined("RUNTIME_PATH")) {
    define ("RUNTIME_PATH", realpath(__DIR__ . '/../runtime'));
}


require_once __DIR__.'/../vendor/autoload.php';

$config = include (CONFIG_PATH . '/config.php');
$app = new Silex\Application(array('debug' => $config['debug']));


$app['settings'] = function() use ($config) {
    return $config['settings'];
};

include_once APP_PATH . '/services.php';

/** @var \Doctrine\DBAL\Schema\AbstractSchemaManager $schema */
$schema = $app['db']->getSchemaManager();
if (!$schema->tablesExist('migrations')) {
    $migration = new Table('migrations');
    $migration->addColumn('migration_version', 'string', array('length' => 32));
    $migration->setPrimaryKey(array('migration_version'));

    $schema->createTable($migration);
}

$path = realpath(__DIR__ . '/../migrations');
if (file_exists($path)) {
    $migrationFileList = scandir($path);
    $migrationList = array();
    foreach ($migrationFileList as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        $className = ucfirst(str_replace(".php", "", $file));
        $migrationNumber = str_replace("Migration","",$className);
        $migrationList[$migrationNumber] = array(
            'class' => $className,
            'filepath' => $path . '/' . $file,
        );
    }
    ksort($migrationList);
    $rows = $app['db']->fetchAll("SELECT migration_version FROM migrations order by migration_version");
    $knownMigrations = array();
    foreach ($rows as $row) {
        $knownMigrations[] = $row['migration_version'];
    }

    echo "Migration List:" . PHP_EOL;
    echo "\tMigration\tStatus" . PHP_EOL;
    $newMigrations = array();
    foreach ($migrationList as $version => $data) {
        echo "\t{$version}:\t" . ((array_search($version, $knownMigrations) === false)?"NO":"YES") . PHP_EOL;
        if (array_search($version, $knownMigrations) !== false) {
            array_splice($knownMigrations, array_search($version, $knownMigrations), 1);
        } else {
            $newMigrations[$version] = $data;
        }
    }

    if (!empty($knownMigrations)) {
        echo "Missing Migration List:" . PHP_EOL;
        foreach ($knownMigrations as $version) {
            echo "\t{$version}" . PHP_EOL;
        }
    }

    if (!empty($newMigrations)) {
        echo "New Migration List:" . PHP_EOL;
        foreach ($newMigrations as $version => $data) {
            echo "\t{$version}" . PHP_EOL;
        }
        if ($_SERVER['argc'] > 1 && $_SERVER['argv'][1] == 'migrate') {
            foreach ($newMigrations as $version => $data) {
                echo "Apply {$version}" . PHP_EOL;
                include $data['filepath'];
                if (class_exists($data['class'])) {
                    $migration = new $data['class']($app['db']);
                    $migration->up($schema, $app['db']);
                    $app['db']->insert("migrations", array("migration_version" => $version));

                } else {
                    echo "Missgin class \"{$data['class']}\" in file \"{$data['filepath']}\"" . PHP_EOL;
                }

            }
        }
    }
} else {
    echo "Не найдена директория с миграциями: {$path}" . PHP_EOL;
}