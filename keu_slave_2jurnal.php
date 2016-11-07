<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$pt=$_POST['pt'];
	$gudang=$_POST['gudang'];
	$periode=$_POST['periode'];
//print_r($_POST);
//exit;	
if($periode=='' and $gudang=='')
{
		$str="select a.*,b.namaakun,substr(a.kodeorg,1,4) as bussunitcode,c.induk from ".$dbname.".keu_jurnaldt a
		left join ".$dbname.".keu_5akun b
		on a.noakun=b.noakun
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where c.induk = '".$pt."'
		order by a.nojurnal 
		";
//		$str="select *,b.namaakun from ".$dbname.".keu_jurnaldt a
//		left join ".$dbname.".keu_5akun b
//		on a.noakun=b.noakun";
}
else if($periode=='' and $gudang!='')
{
//print_r($_POST);
//exit;	
		$str="select a.*,substr(a.kodeorg,1,4) as bussunitcode,b.namaakun,c.induk,c.namaorganisasi from ".$dbname.".keu_jurnaldt a
		left join ".$dbname.".keu_5akun b
		on a.noakun=b.noakun
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where c.induk = '".$pt."' and substr(a.kodeorg,1,4) = '".$gudang."'
		order by a.nojurnal 
		";
}
else{
	if($gudang=='')
	{
//print_r($periode);
//exit;	
		$str="select a.*,b.namaakun,substr(a.kodeorg,1,4) as bussunitcode,c.induk from ".$dbname.".keu_jurnaldt a
		left join ".$dbname.".keu_5akun b
		on a.noakun=b.noakun
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where c.induk = '".$pt."' and substr(tanggal,1,7)='".$periode."'
		order by a.nojurnal 
		";
//		$str="select a.*,b.namaakun from ".$dbname.".keu_jurnaldt a
//		left join ".$dbname.".keu_5akun b
//		on a.noakun=b.noakun  where substr(tanggal,1,7)='".$periode."'";
	}
	else
	{
		$str="select a.*,substr(a.kodeorg,1,4) as bussunitcode,b.namaakun,c.induk,c.namaorganisasi from ".$dbname.".keu_jurnaldt a
		left join ".$dbname.".keu_5akun b
		on a.noakun=b.noakun
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where c.induk = '".$pt."' and substr(a.kodeorg,1,4) = '".$gudang."' and substr(tanggal,1,7)='".$periode."'
		order by a.nojurnal 
		";
//		$str="select *,b.namaakun from ".$dbname.".keu_jurnaldt a
//		left join ".$dbname.".keu_5akun b
//		on a.noakun=b.noakun where substr(tanggal,0,7)='".$periode."'";
	}	
}
//=================================================
if($periode=='')
{
	 $sawalQTY		='';
		 $masukQTY		='';
	 $keluarQTY		='';
	 $kuantitas=0;
	$res=mysql_query($str);
#	$xx=fetchData($str);
#	print_r($xx);
#	exit;
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo"<tr class=rowcontent><td colspan=11>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	}
	else
	{
		while($bar=mysql_fetch_object($res))
		{
			$no+=1;
			$periode=date('Y-m-d H:i:s');
			$nojurnal=$bar->nojurnal;
			$tanggal=$bar->tanggal; 
			$kodeorg=$bar->induk; 
			$noakun =$bar->noakun;
			$namaakun =$bar->namaakun;
			$uraian =$bar->keterangan;
			$jumlah =$bar->jumlah;
			$debet = $kredit = 0;
			if ($jumlah>0){
			$debet = $jumlah;
			}
			else {
			$kredit = -$jumlah;
			}
			echo"<tr class=rowcontent  style='cursor:pointer;' title='Click' onclick=\"detailJurnal(event,'".$pt."','".$periode."','','','','');\">
				  <td>".$no."</td>
				  <td>".$nojurnal."</td>
				  <td>".$tanggal."</td>
				  <td align=center>".$kodeorg."</td>
				  <td>".$noakun."</td>
				  <td>".$namaakun."</td>
				  <td>".$uraian."</td>
				  <td align=right width=100>".number_format($debet,2)."</td>
				  <td align=right width=100>".number_format($kredit,2)."</td>
				</tr>";
		}
	}
}
else
	{
		$salakqty	=0;
		$masukqty	=0;
		$keluarqty	=0;
		$sawalQTY	=0;
	 

	//
	$res=mysql_query($str);
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo"<tr class=rowcontent><td colspan=11>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	}
	else
	{
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
			$periode=date('Y-m-d H:i:s');
			$nojurnal=$bar->nojurnal;
			$tanggal=$bar->tanggal; 
			$kodeorg=$bar->induk; 
			$noakun =$bar->noakun;
			$namaakun =$bar->namaakun;
			$uraian =$bar->keterangan;
			$jumlah =$bar->jumlah;
			$debet = $kredit = 0;
			if ($jumlah>0){
			$debet = $jumlah;
			}
			else {
			$kredit = -$jumlah;
			}
			  
		echo"<tr class=rowcontent style='cursor:pointer;' title='Click' onclick=\"detailJurnal(event,'".$pt."','".$periode."','".$gudang."','".$kodebarang."','".$namabarang."','".$bar->satuan."');\">
				  <td align=center width=50>".$no."</td>
				  <td>".$nojurnal."</td>
				  <td align=center>".tanggalnormal($tanggal)."</td>
				  <td align=center>".$kodeorg."</td>
				  <td align=center>".$noakun."</td>
				  <td>".$namaakun."</td>
				  <td>".$uraian."</td>
				  <td align=right width=100>".number_format($debet,2)."</td>
				  <td align=right width=100>".number_format($kredit,2)."</td>
			</tr>"; 		
	}	
  }
}	

?>