<?php
require __DIR__ . '/vendor/autoload.php';
try {
    new Lucinda\UnitTest\ConsoleController("unit-tests.xml", "local", ($argv[1] ?? null));
} catch (\Throwable $e) {
    echo "ERROR: ".$e->getMessage().PHP_EOL;
    echo "TRACE: ".$e->getTraceAsString();
}
