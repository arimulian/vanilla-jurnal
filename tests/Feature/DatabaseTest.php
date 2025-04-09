<?php

use Illuminate\Support\Facades\DB;

test('database connection test', function () {
    $db = DB::connection();
    $pdo = $db->getPdo();
    $dbName = $db->getDatabaseName();
    expect($dbName)->toBe('vanilla_db_test');
    expect($pdo)->toBeInstanceOf(PDO::class);
});
