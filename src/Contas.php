<?php

namespace Moovin\Job\Backend;

/**
 * Classe de Contas
 *
 * @author Marcos Sartori <infomarcossartori@gmail.com>
 */

 class Contas{

     private $data;
	 private $taxa_C_corrennte="2.50";
	 private $taxa_C_poupanca="0.80";
	 private $limite_Corrente="600.00";
	 private $limite_Poupanca="1000.00";
	 


     public function __construct()
     {
		 
         $jsonString = file_get_contents('caixa.json');
         $this->data = json_decode($jsonString, true);

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
	
    function  buscarConta($numero)
     {
       foreach ($this->data as $key=>$value)
          {
              if ($value['numero'] == $numero) 
				  
			  {
                 return  $this->data[$key];
				 //echo  "Numero da conta:".$this->data[$key]['numero']."<br>";
				// echo  "Tipo de conta:".$this->data[$key]['tipo']."<br>";
				 //echo  "Saldo:".$this->data[$key]['saldo'];
              }


          }

     }
	 
	function depositar ($numero, $valor)
	 {
	
      foreach ($this->data as $key => $value)
	   {
          if ($value['numero'] == $numero)
		  {
		      $this->data[$key]['saldo'] = ($this->data[$key]['saldo']+ $valor);
          }
       }
	 $newJsonString = json_encode($this->data);
     file_put_contents($this->database, $newJsonString);
     return  $this->buscarConta($numero);
        		
	 }
	 
	 
    function sacar ($numero, $valor)
	 {
	
      foreach ($this->data as $key => $value)
	   {
          if ($value['numero'] == $numero)
		  {
		    $saldo=$this->definirTaxas($this->data[$key]); 
            $limite=$this->definirLimite($this->data[$key]);
			
			if($valor <= $saldo and $valor <= $limite)
            {				
             $this->data[$key]['saldo']= $saldo - $valor;
			 $newJsonString = json_encode($this->data);
             file_put_contents($this->database, $newJsonString);
            }
			else{ return "Saldo insuficiente ou limite de saque exedido";}
		  }
       }
	 
     return  $this->buscarConta($numero);
        		
	 }
	 
	 function trasnferir ($sacado, $sacador, $valor)
	 {
	   $data_sacado= $this->buscarConta($sacado);
	   $data_sacador=$this->buscarConta($sacador);

         if($valor <= $data_sacado['saldo'] )
        {
			 $data_sacador['saldo']= $data_sacador['saldo'] + $valor;
			 $data_sacado['saldo']= $data_sacado['saldo']-$valor;
			 
			 foreach ($this->data as $key => $value)
			 {
				 if ($value['numero'] == $sacado)
				 {
					 $this->data[$key]['saldo']= $data_sacado['saldo'];
				 }
				 if ($value['numero'] == $sacador)
				 {
					 $this->data[$key]['saldo']= $data_sacador['saldo'];
				 }
						 
		     }
        			 
		}			 
       	 else
		{
			return "Saldo insuficiente!";	 
		} 
		
		$newJsonString = json_encode($this->data);
        file_put_contents($this->database, $newJsonString);
	 }
 }


 