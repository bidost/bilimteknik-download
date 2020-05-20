<?
/**
 * Website: https://github.com/bidost/bilimteknik-download
 * Additional projects: https://github.com/bidost/repositories
 * Author: bidost https://github.com/bidost
 *
 * Licensed under GNU General Public License v3.0
 * See the LICENSE file in the project root for more information.
 *
 *
 * Version Rev. 1.0.0
 */

set_time_limit(90000);
include('simple_html_dom.php');

function klasorac($path){
	return is_dir($path) || mkdir($path,0755);
}

$dergiler=array(4=>"bilim-ve-teknik",8=>"bilim-cocuk",12=>"merakli-minik");
$p="./dergiler/";
$cont=array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false));
$bas="https://services.tubitak.gov.tr/edergi/user/";
$eksik=array();
$tamam=array();

klasorac($p);
		
foreach($dergiler as $dergino=>$dergi){
	klasorac($p.$dergi);
	
	for($yil=2020; $yil>=1967; $yil--){
		if(($dergino==8&&$yil==1997)||($dergino==12&&$yil==2006)) break;
		
		$p2=$p.$dergi."/".$yil;
		klasorac($p2);
		$sayilar = file_get_html($bas."yilList1.jsp?dergiKodu=".$dergino."&yil=".$yil."&submitButton=",false,stream_context_create($cont));
		
		foreach($sayilar->find('a') as $sayi) {
			parse_str(parse_url($bas.$sayi->href, PHP_URL_QUERY), $parsed);
			$pf=$p2=$p.$dergi."/".$yil."/".$parsed["ay"].".pdf";
			
			if(!is_file($pf)){
				$sayiac=file_get_html($bas.$sayi->href,false,stream_context_create($cont));
				
				if(file_put_contents($pf, file_get_contents($bas.$sayiac->find('a',0)->href,false,stream_context_create($cont))))
					$tamam[]=array("dergi"=>$dergi,"yil"=>$yil,"ay"=>$parsed["ay"],"url"=>$bas.$sayi->href);				
			
				else
					$eksik[]=array("dergi"=>$dergi,"yil"=>$yil,"ay"=>$parsed["ay"],"url"=>$bas.$sayi->href);	
				
			}
		}
	}
}
?>
<h3>tamamlananlar<h3>
<textarea><?=var_export($tamam);?></textarea>
<h3>eksikler<h3>
<textarea><?=var_export($eksik);?></textarea>
