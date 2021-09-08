<?php

require_once __DIR__ . '/vendor/autoload.php';


use Moovin\Job\Backend;

$contas = new Backend\Contas();
echo "Todas as contas disponiveis \n";

print_r($contas->getData());
echo "_______________________________________________________________________________________ \n";

echo "Depositar 2000,00  para conta 001 \n ";

$contas->depositar(001 , 2000.00);

echo "\n_______________________________________________________________________________________ \n";

 echo "Depositar 1000,00  para conta 0020 (inexistente) \n ";

 $contas->depositar(020,1000.00);

echo "\n_______________________________________________________________________________________ \n";

echo "sacar 1000,00  da  conta poupança 001 (limite de saque conta poupança 1000,00)  \n ";
echo "SALDO liberado para saque  é ="."['SALDO - Taxa(0,80 para a poupança)]' \n ";
$contas->sacar( 001 , 1000.00);

echo "\n_______________________________________________________________________________________ \n";

echo "sacar 3000,00  da  conta 001  (valor acima do saldo existente) \n ";

$contas->sacar(001, 3000.00);

echo "\n_______________________________________________________________________________________ \n";


echo "transferir 300,00  da  conta poupança 001 para Conta corrente 002   \n ";
$contas->trasnferir(001 ,002, 300.00);

echo "\n_______________________________________________________________________________________ \n";

echo "transferir 3000,00  da  conta poupança 001 para Conta corrente 002 (valor acima do Saldo existente)   \n ";
$contas->trasnferir(001 ,002, 3000.00);

echo "\n_______________________________________________________________________________________ \n";