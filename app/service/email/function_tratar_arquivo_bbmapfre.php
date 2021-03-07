<?php


function Tratar_arquivo_bbmapfre($Dados,$array_lista_itens) {
#	include 'db.php';

#	$mysql_id = new mysqli('afinccocopel.dyndns.org', 'rubens', 'rubens','drupal','12001');
#        $mysql_id=cria_db();
#        $query="select Dados from Perfil_Consultas where ID_Consultas=".$ID.";";
#        $res = $mysql_id->query($query);
#$linha=mysqli_fetch_array($res);

#print($Dados);
#$dados=($linha['Dados_Pesquisa']);
$dados=$Dados;
#dados=str_replace('=\n'," ",$dados);
#$dados=str_replace("=0A","\n",$dados);
#print nl2br($dados);
#print('<br><br>---------------------------------------------------------------------------<br><br>');
$dados_trat=explode(PHP_EOL, $dados);
#print_r($dados_trat);
#print nl2br ($linha['Dados_Pesquisa']);
$cont_dados_trat=count($dados_trat);
#print($cont_dados_trat);
#print("\n");

######################################################### remontar uma variavel de texto sem o igual no final da linha, para ser novamente requebrado em varias linhas.
$dados_retratar='';
foreach ($dados_trat as $key => $value) 
{
#	$size = strlen($value);
#	$value=substr($value,0,$size-1);
#	$dados_retratar=$value;
	$dados_retratar=$dados_retratar.substr_replace($value, ' ', -2);
#print $dados_retratar;
}
#print $dados_retratar;

####################################################################333

$dados_trat=explode('=0A', $dados_retratar);
$cont_dados_trat=count($dados_trat);

#print_r($dados_trat);



$valor[]=array('seguradora','BBMAPFRE');
foreach ($array_lista_itens as $key => $value) 
{
#print('entrou');
		for($i=0;$cont_dados_trat>$i;$i++)
		#foreach ($dados_trat as $key1 => $value1) 
		{
		$value1=$dados_trat[$i];
#		print_r($dados_trat);
#		$posvalid=false;	
#		print($value1."\n");
#			$posicao='-1';


#			if (strpos($value1,$value[0])>=0){print('entrou');$posicao=strpos($value1,$value[0]);}else{$posicao=-1;}
			$posicao=strpos($value1,$value[0]);
#			print($posicao."\n");
#			if ($posicao!=''){$posvalid=true;}else{$posvalid=false;} 



			if (($posicao===0) or ($posicao>0))
			{
				#print('entrou');
#				print($posicao."\n");
				#print($value1."\n");
			#	print($value[2]);

				if($value[2] == '')
				{
				$valor_temp=ltrim(substr($value1,$posicao+$value[1])," ");
				#print('Resultado fora - '.$valor_temp."\n");	
				}
				else 
				{
				#print('entrou2');
				$posicao_final=strpos($value1,$value[2]);
				$posicao_final=$posicao_final;
				$posicao_inicial=$posicao+$value[1];
				$posicao_final=$posicao_final-$posicao_inicial;
				#print('Resultado parcial fora='.$value1.' - '.$value[2].' - '.$posicao_inicial.' - '.$posicao_final."\n");
# 		 	        $valor_temp=substr($value1,$posicao+$value[1],$posicao_final);	
				$valor_temp=substr($value1,$posicao_inicial,$posicao_final);
				#print('Resultado dentro - '.$valor_temp."\n");
				}			   
			   
				#print($valor_temp."\n");			

				if(strpos($valor_temp,':')>0)
				{
					$temp_array=explode(':',$valor_temp);
					#print_r($temp_array);
					$valor_temp=$temp_array[1];
				}	
			$valor[]=array($value[0],$valor_temp);
			$posicao='';
			break;
			}
			
			
		}




}

return $valor;


	
	
	}

