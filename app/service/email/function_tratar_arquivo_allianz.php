<?php


function Tratar_arquivo_allianz($Dados) {

$dados=$Dados;
#print_r($dados);
$dados_trat=explode(PHP_EOL, $dados);
$cont_dados_trat=count($dados_trat);
$valor='';
#print_r($dados_trat);

$temp=0;
#$valor[]=array('seguradora','AZUL');

for($i=0;$cont_dados_trat>$i;$i++)
#foreach ($array_lista_itens as $key => $value) 
{

#print_r($dados_trat);

	if (stripos($dados_trat[$i],'TABLE style=3D')>0)
	{
#		print 'entrou '."$i\n";
#	print_r($dados_trat[$i]);
#	$valor[]=$dados_trat[$i];
		$temp=1;
	}

	#if (($temp==1) and (stripos($dados_trat[$i],'</TR></TBODY></TABLE>')>=1))
	#{
#		$temp=0;
#	print ('saindo '."$i\n");
#	}
	
	
	if (($temp==1) )
	{
#	print ('linha '."$i\n");
	$valor[]=$dados_trat[$i];
#$valor[]='AZUL';
#		print 'entrou';
#print_r($dados_trat[$i]);
	}

	if (($temp==1) and (stripos($dados_trat[$i],'</TR></TBODY></TABLE>')>=1))
	{
		$temp=0;
#	print ('saindo '."$i\n");
	}



}

#print_r($valor);

$tabela_html=implode("\n",$valor);

#print_r($tabela_html);
$tabela_html=implode("\n",$valor);
$array_table=convert_table_array($tabela_html);

#print_r ($array_table);
return $array_table;
#return $valor;


	
	
	}



function convert_table_array ($tables_i)
{
#print_r($tables_i);

$dom = new DOMDocument();
 
#$html = $dom->loadHTML('<html>'.$tables_i);
$dom->loadHTML('<HTML><HEAD><title>a</title>
</HEAD>
<BODY >'.$tables_i.'</BODY></HTML>');
 
#$dom->preserveWhiteSpace = false;
 
$tables = $dom->getElementsByTagName('table');
$rows = $tables->item(0)->getElementsByTagName('tr');
// get each column by tag name
$cols = $rows->item(0)->getElementsByTagName('th');
$row_headers = NULL;
foreach ($cols as $node) {
    //print $node->nodeValue."\n";
    $row_headers[] = $node->nodeValue;
}
 
$table = array();
//get all rows from the table
$rows = $tables->item(0)->getElementsByTagName('tr');
foreach ($rows as $row)
{
    // get each column by tag name
    $cols = $row->getElementsByTagName('td');
    $row = array();
    $i=0;
    foreach ($cols as $node) {
        # code...
        //print $node->nodeValue."\n";
        if($row_headers==NULL)
            $row[] = $node->nodeValue;
        else
            $row[$row_headers[$i]] = $node->nodeValue;
        $i++;
    }
    $table[] = $row;
}

return $table;


} 






