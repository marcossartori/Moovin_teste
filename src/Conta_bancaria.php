<?php

namespace Moovin\Job\Backend;

/**
 * Classe de Conta_bancaria
 *
 * @author Marcos Sartori <infomarcossartori@gmail.com>
 */

 class Conta_bancaria{

     private $data;
	 private $taxa_C_corrennte=2.50;
	 private $taxa_C_poupanca= 0.80;
	 private $limite_Corrente= 600.00;
	 private $limite_Poupanca= 1000.00;
	 private $databasename="caixa.json"; //arquivo com registro dos  dados  bancarios
	

	 


     public function __construct()
     {

		 $jsonString = file_get_contents($this->databasename);
         $this->data = json_decode($jsonString, true);

     }
	 
	 function getData(){
		 
		 return $this->data;
	 }
	 
	 
	 function retornarMensagem($mensagem=null, $data=null){
		 
		 $dados['mensagem']=$mensagem;
		 $dados['data']= $data;
		 echo "\n \n".$dados['mensagem']."\n";
		 
		  if($data != null)
		  {
			  echo "\n Conta Nº: ".$dados['data']['numero']."\n";
			  echo "\n Tipo: ".$dados['data']['tipo']."\n";
			  echo "\n Saldo: ".$dados['data']['saldo']."\n";
		  }
		 ///return $dados;
		 //print_r($dados);
	 }
	 
	 
	function definirTaxas($data)
	{
		switch ($data['tipo']) 
	 {
     case "corrente":
        return $data['saldo'] - $this->taxa_C_corrennte;
        break;
     case "poupanca":
         return $data['saldo'] - $this->taxa_C_poupanca;
        break;
      }
	}
    
  function definirLimite($data)
	{
		switch ($data['tipo']) 
	 {
     case "corrente":
        return $this->limite_Corrente;
        break;
     case "poupanca":
         return $this->limite_Poupanca;
        break;
      }
	}
	
    function  buscarConta($numero_conta)
     {
       foreach ($this->data as $key=>$value)
          {
              if ($value['numero'] == $numero_conta)
				  
			  {
                 return  $this->data[$key];
				
              }


          }

     }
	 
	function depositar ($numero_conta, $valor)
	 {
	
       foreach ($this->data as $key => $value)
	   {
          if ($value['numero'] == $numero_conta) //procurar Nª da conta
		  {
		      $this->data[$key]['saldo'] = ($this->data[$key]['saldo']+ $valor); // acrecentar valor desajado ao saldo existente//
			  $newJsonString = json_encode($this->data); // atualizar as  informaçoes com os novos valores na Variavel Data//
              file_put_contents($this->databasename, $newJsonString); //salvar no  arquivo json os  dados  da transação//
			  	   $mensagem=  $this->retornarMensagem("Deposito feito com sucesso", $value);
			  break;
          }
		  else
		  {
			 $mensagem= $this->retornarMensagem("Nao foi possivel completar a transação! Favor verificar numero da  Conta ");
			  break;
		  }
		  
        }
	      
     // return $mensagem;     		
	 }
	 
	 
    function sacar ($numero_conta, $valor)
	 {
	
      foreach ($this->data as $key => $value)
	   {
          if ($value['numero'] == $numero_conta)
		  {
		    $saldo=$this->definirTaxas($this->data[$key]);  // verifica o tipo de  conta e aplica as taxa
            $limite=$this->definirLimite($this->data[$key]); // verifica o tipo de  conta e retorna o limite
			
			if($valor <= $saldo and $valor <=  $limite)
            {				
             $this->data[$key]['saldo']= $saldo - $valor;
			 $newJsonString = json_encode($this->data);
             file_put_contents($this->databasename, $newJsonString);
			 $conta = $this->buscarConta($numero_conta);
			 $mensagem= $this->retornarMensagem("Saque feito com sucesso!", $conta);
			 break;
			 }
			else{ $mensagem= $this->retornarMensagem("Saldo insuficiente ou limite de saque exedido "); break;}
		  }
       }
	 
      //return  $mensagem;
        		
	 }
	 
	 function trasnferir ($Conta_sacado, $Conta_sacador, $valor)
	 {
	   $data_sacado= $this->buscarConta($Conta_sacado);
	   $data_sacador=$this->buscarConta($Conta_sacador);

         if($valor <= $data_sacado['saldo'] )
         {
			 $data_sacador['saldo']= $data_sacador['saldo'] + $valor;
			 $data_sacado['saldo']= $data_sacado['saldo']-$valor;
			 
			 foreach ($this->data as $key => $value)
			 {
				 if ($value['numero'] == $Conta_sacado)
				 {
					 $this->data[$key]['saldo']= $data_sacado['saldo'];
				 }
				 if ($value['numero'] == $Conta_sacador)
				 {
					 $this->data[$key]['saldo']= $data_sacador['saldo'];
				 }
						 
		     }
        	   $newJsonString = json_encode($this->data);
               file_put_contents($this->databasename, $newJsonString);
            
               $mensagem= $this->retornarMensagem("transferencia feita com sucesso!", $data_sacado);
               $mensagem= $this->retornarMensagem("transferido ".$valor." para:", $data_sacador);			   
		 
		 }			 
       	 else
		 {
			 $mensagem= $this->retornarMensagem("Saldo insuficiente!");
		 } 
		
		 //return $mensagem;
	}
 }


 