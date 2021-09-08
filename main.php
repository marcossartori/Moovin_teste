<?php

require_once __DIR__ . '/vendor/autoload.php';


use Moovin\Job\Backend;

$contas = new Backend\Contas();

print_r($conta= $contas->buscarConta('001'));
 