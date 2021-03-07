<?php


function Tratar_arquivo_anexo_eml($extensao,$Dados) {

$dados=$Dados;
$dados_trat=explode(PHP_EOL, $dados);
$cont_dados_trat=count($dados_trat);
$xls_part='';

$temp=0;
$valor[]=array('seguradora','AZUL');

for($i=0;$cont_dados_trat>$i;$i++)
#foreach ($array_lista_itens as $key => $value) 
{
#print_r($dados_trat[$i]);
#$linha=$dados_trat[$i];
#print('contador da extensao='.stripos($dados_trat[$i],'xls'));
#print('contador de caracteres='.strlen($linha));
#print("\n");
#
#print $extensao."\n";
	#if ((stripos($dados_trat[$i],'Content-Description:')>0) and (stripos($dados_trat[$i],$extensao)>0))
	if ((stripos($dados_trat[$i],$extensao)>0))
	{
#		print 'entrou arquivo eml com anexo';
		$temp=1;
	}
##print(count($dados_trat[$i])."\n");
	if (($temp==2) and (strlen($dados_trat[$i]))<=1)
	{
		$temp=0;
	}

	if (($temp==1) and (strlen($dados_trat[$i])<=1))
	{
		$temp=2;
	}
	
	
	if (($temp==2) )
	{
	
#	print_r($dados_trat[$i]."\n");
	$xls_part=$xls_part.$dados_trat[$i];
#	$valor[]=explode(';',$dados_trat[$i]);
#	$valor[]='AZUL';
	}


}
#print $xls_part;
#$xls_part=str_replace('^M',"\n",$xls_part);
#print_r($xls_part);
$xls=imap_base64($xls_part);

####gravqar arquivo temp
$fp = fopen("Imp_tmp.txt", "w");
 
// Escreve "exemplo de escrita" no bloco1.txt
$escreve = fwrite($fp, $xls);
 
// Fecha o arquivo
fclose($fp); 
######fechar arquivo temp
#$xls=str_replace('^M',"\n",$xls);
#print_r($xls);
#$print(coverte_xlsx_csv($xls));
$valor='';
$valor=coverte_xlsx_csv("Imp_tmp.txt");
return $valor;


	
	
}



function coverte_xlsx_csv($arq)
{
require_once 'PHPexcel/Classes/PHPExcel/IOFactory.php';
$xls=PHPExcel_IOFactory::load($arq);
$writer = PHPExcel_IOFactory::createWriter($xls, 'CSV');
$writer->setDelimiter(";");
$writer->setEnclosure("");
$writer->save("teste.csv");

#$ret=PHPExcel_IOFactory::load("teste.csv");
#ret=readfile("teste.csv");
#ret= array_map('str_getcsv', file('teste.csv'));

#$ret=explode($ret,";");
$ret=csvToArray("teste.csv");
return $ret;
#print_r($writer);
#$writer->setDelimiter(";");
#$writer->setEnclosure("");
#$writer->save("test123.csv");


}

function csvToArray($file) {
    $rows = array();
    $headers = array();
    if (file_exists($file) && is_readable($file)) {
        $handle = fopen($file, 'r');
        while (!feof($handle)) {
            $row = fgetcsv($handle, 10240, ';', '"');
            if (empty($headers))
                $headers = $row;
            else if (is_array($row)) {
                array_splice($row, count($headers));
                $rows[] = array_combine($headers, $row);
            }
        }
        fclose($handle);
    } else {
        throw new Exception($file . ' doesn`t exist or is not readable.');
    }
    return $rows;
}

