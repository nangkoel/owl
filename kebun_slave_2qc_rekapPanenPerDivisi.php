<?php
require_once('master_validation.php');
require_once('config/connection.php');;
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php'); 
require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_line.php');




$optNmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');

$per=$_GET['per'];
$reg=$_GET['reg'];

$bln=substr($per,5,2);
$thn=substr($per,0,4);

$nmBulan=numToMonth($bln,'I','long');



$xdata=array();
$xdata[0]='';

$i="select distinct substr(kodeblok,1,4) as divisi from ".$dbname.".kebun_qc_panendt where substr(kodeblok,1,4) in (select kodeunit from ".$dbname.".bgt_regional_assignment 
	where regional='".$reg."') and tanggalcek like '%".$per."%' group by substr(kodeblok,1,4) order by substr(kodeblok,1,4) asc";
$n=mysql_query($i) or die (mysql_error($conn));
while($d=mysql_fetch_assoc($n))
{
	$x++;
	$xdata[$x]=$d['divisi'];
}


$yPanen=array();
$yTdkPanen=array();
$yRasio=array();
$str="	select sum(jjgpanen) as jjgpanen,sum(jjgtdkpanen) as jjgtdkpanen,sum(brdtdkdikutip)/sum(jjgpanen) as rasio
		from ".$dbname.".kebun_qc_panendt where substr(kodeblok,1,4) in (select kodeunit from ".$dbname.".bgt_regional_assignment 
		where regional='".$reg."') and tanggalcek like '%".$per."%' group by substr(kodeblok,1,4) order by substr(kodeblok,1,4) asc";
$yPanen[$y]=0;
$yTdkPanen[$y]=0;
$yRasio[$y]=0;
$res=mysql_query($str) or die (mysql_error($conn));
while($bar=mysql_fetch_assoc($res))
{
	$y++;
	$yPanen[$y]=$bar['jjgpanen'];
	$yTdkPanen[$y]=$bar['jjgtdkpanen'];
	$yRasio[$y]=$bar['rasio'];
}

if($xdata[$x]=='')
{
	exit("Error:Data Kosong");
}

//$xdata = array(0=>"",1=>"a",2=>"a",3=>"a");
//$xdata = array(a,b,1,2);

// Create the graph. These two calls are always required
$graph = new Graph(500,300);
$graph->SetScale('textlin');

$graph->img->SetMargin(40,30,40,40);
$graph->xaxis->SetTickLabels($xdata);

$graph->xaxis->title->Set($_SESSION['lang']['divisi']); 	
$graph->yaxis->title->Set(); 	

$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$graph->title->SetShadow('gray@0.4',5);
$graph->title->Set(strtoupper($_SESSION['lang']['regional']." ".$reg." ".$nmBulan." ".$thn));
 
// Create the linear plot
//6,12,23

#add line 
$lineplot=new LinePlot($yPanen);
$lineplot2=new LinePlot($yTdkPanen);
$lineplot3=new LinePlot($yRasio);

#line color
//$lineplot=new LinePlot();
$lineplot->SetColor('blue');
$lineplot2->SetColor('red');
$lineplot3->SetColor('');
 
 // Create the third line
//$p3 = new LinePlot($datay3);
//$p3->SetColor("orange");

#legend
$graph->legend->SetPos(0.1,0.99,'left','bottom');
$graph->legend->SetShadow('gray@0.4',-10);
$lineplot->SetLegend(strtoupper($_SESSION['lang']['tbs']).'  '.$_SESSION['lang']['panen']);

$graph->legend->SetPos(0.1,0.99,'left','bottom');
$graph->legend->SetShadow('gray@0.4',-10);
$lineplot2->SetLegend(strtoupper($_SESSION['lang']['tbs']).' '.$_SESSION['lang']['no'].' '.$_SESSION['lang']['panen']);

$graph->legend->SetPos(0.1,0.99,'left','bottom');
$graph->legend->SetShadow('gray@0.4',-10);
$lineplot3->SetLegend($_SESSION['lang']['rasio'].' '.$_SESSION['lang']['brondolan']);

//$graph->Add($lineplot);

 
 
// Add the plot to the graph
$graph->Add($lineplot);
$graph->Add($lineplot2);
$graph->Add($lineplot3);

// Display the graph
//$graph->Stroke();
$graph->StrokeCSIM();







$tab="<link rel=stylesheet tyle=text href='style/generic.css'>
            <script language=javascript src='js/generic.js'></script>"; 
$tab.="<br /><br />

	<table class=sortable cellspacing=1 cellpadding=1 border=0>
	     <thead>
			 <tr class=rowheader>
			 	 <td align=center>".$_SESSION['lang']['kode']."</td>
				 <td align=center>".$_SESSION['lang']['divisi']."</td>
			 	 <td align=center>".$_SESSION['lang']['jjg']."<br />".$_SESSION['lang']['panen']."</td>
				 <td align=center>".$_SESSION['lang']['jjg']." ".$_SESSION['lang']['no']."<br />".$_SESSION['lang']['panen']."</td>
				 <td align=center>".$_SESSION['lang']['jjg']."<br />".$_SESSION['lang']['tidakdikumpul']."</td>
				 <td align=center>".$_SESSION['lang']['brondolan']."<br />".$_SESSION['lang']['tdkdikutip']."</td> 
				 <td align=center>".$_SESSION['lang']['rasio']."<br />".$_SESSION['lang']['brondolan']."</td> 	
			</tr></thead>";
			
			
$str1="select sum(jjgtdkpanen) as jjgtdkpanen,sum(jjgpanen) as jjgpanen, sum(jjgtdkkumpul) as jjgtdkkumpul,
		sum(brdtdkdikutip) as brdtdkdikutip,substr(kodeblok,1,4) as divisi 
		from ".$dbname.".kebun_qc_panendt where substr(kodeblok,1,4) in (select kodeunit from ".$dbname.".bgt_regional_assignment 
		where regional='".$reg."') and tanggalcek like '%".$per."%' group by substr(kodeblok,1,4) order by substr(kodeblok,1,4) asc";
//echo $str1;		
$res1=mysql_query($str1) or die (mysql_error($conn));			
while($bar1=mysql_fetch_assoc($res1))
{
	//$y++;
	//$ydata[$y]=$bar['jjgtdkpanen'];
	$tab.="
			<tr class=rowcontent>
				<td align=left>".$bar1['divisi']."</td>
				<td align=left>".$optNmOrg[$bar1['divisi']]."</td>
				<td align=right>".$bar1['jjgpanen']."</td>
				<td align=right>".$bar1['jjgtdkpanen']."</td>
				<td align=right>".$bar1['jjgtdkkumpul']."</td>
				<td align=right>".$bar1['brdtdkdikutip']."</td>
				<td align=right>".number_format($bar1['brdtdkdikutip']/$bar1['jjgpanen'],2)."</td>
			</tr>";	
}
$tab.="</table>";

            echo $tab;

?>
 



