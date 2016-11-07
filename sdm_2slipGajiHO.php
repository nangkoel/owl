<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/sdm_payrollHO.js></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?php
include('master_mainMenu.php');
//+++++++++++++++++++++++++++++++++++++++++++++
//list employee
/*
echo"<pre>";
print_r($_SESSION);
echo"</pre>";
*/
//get all payroll operator
$strd="select uname from ".$dbname.".sdm_ho_payroll_user";
$optx="<option value=''>Default</option>";
$red=mysql_query($strd);
while($badx=mysql_fetch_object($red))
{
	$optx.="<option value='".$badx->uname."'>".$badx->uname."</option>";
}

//check whether admin
$strd="select `type` from ".$dbname.".sdm_ho_payroll_user
       where uname='".$_SESSION['standard']['username']."'";
$status='operator';
$red=mysql_query($strd);
while($badx1=mysql_fetch_object($red))
{
	$status=$badx1->type;
}
if($status=='admin')
{
	$add=" ";
}
else
{
	$add=" style='display:none;' ";
}
echo"<div class=drag id=pdf style=\" width:750px; display:none;position:absolute;background-image:url('images/title_bg.gif');\">
     </div>";
$opt="";
for($x=0;$x<24;$x++)
{
	$dt=mktime(0,0,0,(date('m')-$x),15,date('Y'));
	$opt.="<option value='".date('Y-m',$dt)."'>".date('m-Y',$dt)."</option>";
}

$opt1="<option value='regular'>Regular</option>";
$opt1.="<option value='thr'>THR</option>";
$opt1.="<option value='jaspro'>Jasa Produksi</option>";

	OPEN_BOX('','<b>PAYROLL PRINT:</b>');
		echo"<div id=EList>";
		echo OPEN_THEME("Payroll Print Preview: PERIOD <select id=periode>".$opt."</option></select> Tipe:<select id=tipe>".$opt1."</select><span  ".$add."> Operator <select id=user>".$optx."</select></span>
		 <button class=mybutton onclick=pyPreview()>Preview</button> &nbsp
		 <img src=images/excel.jpg onclick=pyPreviewExcel() style='cursor:pointer;' title='Convert to Excel'> &nbsp 
		 <img src='images/printer.png' title='Print Slyp/PDF' style='cursor:pointer' onclick=pPDF(event)> 
		 <img src='images/bca.jpg'  onclick=printBank(event,'BCA')  title='Convert to Ms.Excel for BCA Transfer' style='cursor:pointer'>");
//cek if new employee exist
		 $prestr="select distinct karyawanid from ".$dbname.".sdm_ho_employee order by karyawanid";
		 $preres=mysql_query($prestr,$conn);	
		 $arrid='';
		 while($prebar=mysql_fetch_object($preres))
		 {
		 	if($arrid=='')
			 $arrid.=$prebar->karyawanid;
			else 
			 $arrid.=",".$prebar->karyawanid;			
		 }
         if($arrid=='')
		 {
		 	$arrid="'null'";
		 }

		 $str="select karyawanid,namakaryawan,statuspajak,tanggalkeluar,npwp from ".$dbname.".datakaryawan
		       where karyawanid not in(".$arrid.") and alokasi=1";
		$newempl=mysql_num_rows(mysql_query($str,$conn));	 
		if($newempl>0)
		{
	       	echo "<font size=4 color=orange><b>Warning!!!</b></font><br>
				      <img src=images/onebit_36.png height=30px align=middle>
					  Ada karyawan baru yang belum terdaftar d payroll.<br>
					  .";		
		}

			//ccheck if all employee has been assign to payroll operator
			$str="select count(*) as d from ".$dbname.".sdm_ho_employee
			      where operator is null or operator=''";
			$res=mysql_query($str,$conn);
			$count=0;
			while($bar=mysql_fetch_object($res))
			{
				$count=$bar->d;
			}
	        if($count>0){
	        	echo "Forbidden!!!<br>
				      <img src=images/stop1.png height=100px align=middle>
					  Masih ada karyawan yang belum di <b>set operator</b> payroll-nya.";
	        }
			else
			{
	     //ccheck if all employee bankaccount has been set
				$str="select count(*) as d from ".$dbname.".sdm_ho_employee
				      where bank is null or bankaccount='' or length(firstpayment)<>7";
				$res=mysql_query($str,$conn);
				$count=0;
				while($bar=mysql_fetch_object($res))
				{
					$count=$bar->d;
				}
		        if($count>0){
		        	echo "Forbidden!!!<br>
					      <img src=images/stop1.png height=100px align=middle>
						  Account bank karyawan atau periode gaji pertama belum si set, lakukan <b>Setup Employee's Payroll Data</b>.";
		        }
				else
				{
					echo"<div id=output style='height:450px;width:950px;overflow:scroll;'>
					</div>";
				}	
			}
		echo CLOSE_THEME();
		echo"</div>";
	CLOSE_BOX();		
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>