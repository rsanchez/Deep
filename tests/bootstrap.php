<?php

use Symfony\Component\Console\Output\NullOutput;
use Phinx\Config\Config;
use Phinx\Migration\Manager as MigrationManager;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/constraints/ArrayHasOnlyValuesConstraint.php';
require_once __DIR__.'/constraints/ArrayHasValueConstraint.php';
require_once __DIR__.'/constraints/ArrayDoesNotHaveValueConstraint.php';
require_once __DIR__.'/constraints/CollectionPropertyHasOneValueConstraint.php';
require_once __DIR__.'/constraints/CollectionPropertyCompareValueConstraint.php';
require_once __DIR__.'/constraints/CollectionPropertyCompareDateTimeConstraint.php';
require_once __DIR__.'/constraints/CollectionPropertyDoesNotHaveValueConstraint.php';
require_once __DIR__.'/constraints/CollectionNestedPropertyHasOneValueConstraint.php';
require_once __DIR__.'/constraints/CollectionNestedPropertyDoesNotHaveValueConstraint.php';
require_once __DIR__.'/constraints/CollectionNestedCollectionPropertyHasOneValueConstraint.php';
require_once __DIR__.'/constraints/CollectionNestedCollectionPropertyHasAllValuesConstraint.php';
require_once __DIR__.'/constraints/CollectionNestedCollectionPropertyDoesNotHaveAllValuesConstraint.php';

/**
 * Create and seed an in-memory sqlite database for testing
 * Using Phinx for migrations/seeding
 */
$config = new Config([
    'paths' => [
        'migrations' => __DIR__ . '/migrations'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'test',
        'test' => [
            'name' => ':memory:',
            'adapter' => 'sqlite',
            'memory' => true,
        ],
    ],
]);

$migrationManager = new MigrationManager($config, new NullOutput());

// get the PDO object used by Phinx
$pdo = $migrationManager->getEnvironment('test')->getAdapter()->getConnection();

/**
 * Make Eloquent use the PDO object provided by Phinx
 */
$capsule = new Capsule();

$capsule->addConnection([], 'default');

$capsule->getDatabaseManager()->extend('default', function () use ($pdo) {
    return new Connection($pdo);
});

$capsule->bootEloquent();

// run migrations
$migrationManager->migrate('test');
