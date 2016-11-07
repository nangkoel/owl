<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
?>



<?php
OPEN_BOX();

$optThn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//$sql ="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where tipe='WORKSHOP' and induk='".$_SESSION['empl']['lokasitugas']."' ORDER BY kodeorganisasi";
$sql ="SELECT distinct tahunbudget FROM ".$dbname.".bgt_budget ORDER BY tahunbudget desc";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optThn.="<option value=".$data['tahunbudget'].">".$data['tahunbudget']."</option>";
			}


$optWs="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where tipe='WORKSHOP' ORDER BY kodeorganisasi";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optWs.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}
$arr="##thnbudget##kdWs";	
?>

<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>

<script language=javascript>
	function batal()
	{
		document.getElementById('thnbudget').value='';	
		document.getElementById('kdWs').value='';
		document.getElementById('printContainer').innerHTML='';
	}
	
	function getDet(id)
	{
		kdTrak=document.getElementById('kdTrak_'+id).getAttribute('value');
		kdeWs=document.getElementById('kdeWs_'+id).getAttribute('value');
		thnbudget=document.getElementById('thnbudget').options[document.getElementById('thnbudget').selectedIndex].value;
		param="kdTrak="+kdTrak+"&brsKe="+id+"&kdeWs="+kdeWs+"&thnbudget="+thnbudget;
		 
		tujuan="bgt_slave_laporan_rp_jam_bengkel.php";
		//alert(param);	
		
		 function respon() {
			if (con.readyState == 4) 
			{
				if (con.status == 200) 
				{
					busy_off();
					if (!isSaveResponse(con.responseText)) 
					{
						alert('ERROR TRANSACTION,\n' + con.responseText);
					} else
					{
						// Success Response
					//	alert(con.responseText);
						document.getElementById('detail_'+id).innerHTML=con.responseText;
					}
				} 
				else 
				{
					busy_off();
					error_catch(con.status);
				}
			}
		}
	  //  alert(fileTarget+'.php?proses=preview', param, respon);
	  post_response_text(tujuan+'?'+'proses=getDetail', param, respon);
}

function printFile(param,tujuan,title,event)
{
   tujuan=tujuan+"?"+param;  
   width='200';
   height='150';
   
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,event); 
}

function dataKeExcel(event,kdTrak,kdeWs,thnBudget)
{
	kodeTraksi=kdTrak;
	kodeWs=kdeWs;
	thnBudget=thnbudget;
	param='kdTrak='+kodeTraksi+'&kdWs='+kodeWs+'&thnbudget='+thnBudget+'&proses=ExcelAlokasi';
	//alert (param);
	
	tujuan='bgt_slave_laporan_rp_jam_bengkel.php';
	judul='Report Ms.Excel'; 
	printFile(param,tujuan,judul,event) 

}

function closeDet(id)
{
	document.getElementById('detail_'+id).innerHTML='';
}



function printFile2(param,tujuan,title,event)
{
   tujuan=tujuan+"?"+param;  
   width='1200';
   height='450';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   

   showDialog1(title,content,width,height,event); 
}

function dataKePdf(event,kdTrak,kdeWs,thnBudget)
{
	kodeTraksi=kdTrak;
	kodeWs=kdeWs;
	thnBudget=thnbudget;
	param='kdTrak='+kodeTraksi+'&kdWs='+kodeWs+'&thnbudget='+thnBudget+'&proses=pdfAlokasi';
	//alert (param);
	
	tujuan='bgt_slave_laporan_rp_jam_bengkel.php';
	judul='Report Detail PDF '+ kdeWs +' Tahun '+ thnBudget +' '; 
	printFile2(param,tujuan,judul,event) 
	//alert (param);

}




/*function previewpdf(event,kdTrak,kdeWs,thnbudget)
{
	kodeTraksi=kdTrak;
	kodeWs=kdeWs;
	thnBudget=thnbudget;
	param='kdTrak='+kodeTraksi+'&kdWs='+kodeWs+'&thnbudget='+thnBudget+'&proses=pdfAlokasi';
	tujuan='bgt_slave_laporan_rp_jam_bengkel.php';
 //display window

   title=kdTrak;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,event);
   
}*/





</script>


<div style="margin-bottom: 30px;">
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['laporanrpjambengkel'] ?></b></legend>

<table width="285" border="0" cellspacing="1" >
    <tr><td><label><?php echo $_SESSION['lang']['budgetyear']?></label></td><td>:</td><td><select id="thnbudget" name="thnbudget" style="width:150px;" ></option><?php echo $optThn?></select></td></tr>
    <tr><td><label><?php echo $_SESSION['lang']['workshop']?></label></td><td>:</td><td><select id="kdWs" name="kdWs" style="width:150px;"></option><?php echo $optWs?></select></td></tr>
</table>
    
    <table width="365" border="0" cellspacing="1" >   
    <tr>
    <td width="95"></td>
    <td width>
      
        <button onclick="zPreview('bgt_slave_laporan_rp_jam_bengkel','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview"><?php echo $_SESSION['lang']['preview']?></button>
        <button onclick="zExcel(event,'bgt_slave_laporan_rp_jam_bengkel.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview"><?php echo $_SESSION['lang']['excel']?></button>   
        <button onclick="zPdf('bgt_slave_laporan_rp_jam_bengkel','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview"><?php echo $_SESSION['lang']['pdf']?></button>
        <button onclick="batal()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel']?></button></td></tr>
</table>
</fieldset>
</div>

<?php
CLOSE_BOX();
OPEN_BOX();
?>

<fieldset style='clear:both'><legend><b><?php echo $_SESSION['lang']['printArea']?></b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>

<?php
CLOSE_BOX();
echo close_body();
?>