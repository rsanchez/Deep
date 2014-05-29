<?php

use Symfony\Component\Console\Output\StreamOutput;
use Phinx\Config\Config;
use Phinx\Migration\Manager as MigrationManager;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/constraints/ArrayHasOnlyValuesConstraint.php';

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
            //'name' => __DIR__.'/test',
            'name' => ':memory:',
            'adapter' => 'sqlite',
            'memory' => true,
        ],
    ],
]);

// Print the migrations to the command line
$output = new StreamOutput(fopen('php://stdout', 'w'));

$migrationManager = new MigrationManager($config, $output);

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
