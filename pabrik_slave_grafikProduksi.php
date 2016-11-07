<?php
require_once('master_validation.php');
require_once('config/connection.php');;
require_once('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_scatter.php');


	$tampil=$_GET['tampil'];
	$pabrik=$_GET['pabrik'];
	$periode=$_GET['periode'];

if(strlen($periode)==4)
{
	//tahunan
$bln=array('01','02','03','04','05','06','07','08','09','10','11','12');
$datay1=array();
$datay2=array();
for($x=0;$x<count($bln);$x++)
{
	$str="select
		  sum(tbsdiolah) as tbsdiolah,
		  sum(oer)  as oer,
		  sum(oerpk) as oerpk from ".$dbname.".pabrik_produksi
		  where kodeorg='".$pabrik."' and tanggal like '".$periode."-".$bln[$x]."%'";  
	$datay1[$x]=0;
	$datay2[$x]=0;
	$res=mysql_query($str);
       while($bar=mysql_fetch_object($res))
        {
			if($bar->tbsdiolah!=0)
			{
			$datay1[$x]=number_format($bar->oer/$bar->tbsdiolah*100,2,'.',',');
			$datay2[$x]=number_format($bar->oerpk/$bar->tbsdiolah*100,2,'.',',');
			}
         }	
}
//===========
$labels = array("Jan","Feb","Mar","Apr","Mei","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

$graph = new Graph(750,450);
$graph->img->SetMargin(40,40,40,80);    
$graph->img->SetAntiAliasing();
$graph->SetScale("textlin");
$graph->SetShadow();
$graph->title->Set(strtoupper($pabrik)." OER DURING ".$periode);
$graph->title->SetFont(FF_VERDANA,FS_NORMAL,14);

$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,11);
$graph->xaxis->SetTickLabels($labels);
$graph->xaxis->SetLabelAngle(45);

$p1 = new ScatterPlot($datay1);
$p2 = new ScatterPlot($datay2);

$p1->SetLegend('CPO');
$p2->SetLegend('Kernel');
$graph->legend->Pos(0.02,0.03);

$p1->mark->SetType(MARK_SQUARE);
$p1->SetImpuls();
$p1->mark->SetFillColor("red");
$p1->mark->SetWidth(5);
$p1->SetColor("blue");
$p2->mark->SetType(MARK_FILLEDCIRCLE);
$p2->SetImpuls();
$p2->mark->SetFillColor("orange");
$p2->mark->SetWidth(5);
$p2->SetColor("black");

$p1->SetCenter();
$graph->Add(array($p1,$p2));	
$graph->Stroke();
	   
}
else
{
	//bulanan
$bln=array();
$num=date('t',mktime(0,0,0,substr($periode,5,2),2,substr($periode,0,4)));
$labels=array();
for($x=1;$x<=$num;$x++)
{
	array_push($labels, $x);
	if($x<10)
	  $y='0'.$x;
	else
	  $y=$x;  
	array_push($bln, $y);
}
$datay1=array();
$datay2=array();
for($x=0;$x<count($bln);$x++)
{
	$str="select
		  sum(tbsdiolah) as tbsdiolah,
		  avg(oer)  as oer,
		  avg(oerpk) as oerpk from ".$dbname.".pabrik_produksi
		  where kodeorg='".$pabrik."' and tanggal = '".$periode."-".$bln[$x]."'";  
	$datay1[$x]=0;
	$datay2[$x]=0;
	$res=mysql_query($str);
       while($bar=mysql_fetch_object($res))
        {
			if($bar->tbsdiolah!=0)
			{
			$datay1[$x]=number_format($bar->oer/$bar->tbsdiolah*100,2,'.',',');
			$datay2[$x]=number_format($bar->oerpk/$bar->tbsdiolah*100,2,'.',',');
			}
         }	
}
//===========

$graph = new Graph(750,450);
$graph->img->SetMargin(40,40,40,80);    
$graph->img->SetAntiAliasing();
$graph->SetScale("textlin");
$graph->SetShadow();
$graph->title->Set(strtoupper($pabrik)." OER DURING ".$periode);
$graph->title->SetFont(FF_VERDANA,FS_NORMAL,14);

$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,11);
$graph->xaxis->SetTickLabels($labels);
$graph->xaxis->SetLabelAngle(45);

$p1 = new ScatterPlot($datay1);
$p2 = new ScatterPlot($datay2);

$p1->SetLegend('CPO');
$p2->SetLegend('Kernel');
$graph->legend->Pos(0.02,0.03);

$p1->mark->SetType(MARK_SQUARE);
$p1->SetImpuls();
$p1->mark->SetFillColor("red");
$p1->mark->SetWidth(4);
$p1->SetColor("blue");
$p2->mark->SetType(MARK_FILLEDCIRCLE);
$p2->SetImpuls();
$p2->mark->SetFillColor("orange");
$p2->mark->SetWidth(4);
$p2->SetColor("black");
$p1->SetCenter();
$graph->Add(array($p1,$p2));	
$graph->Stroke();
}
	
?>