<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');



?>	

<?php		

$_POST['method']==''?$method=$_GET['method']:$method=$_POST['method'];
$notran=$_POST['notran'];
$pt=$_POST['pt'];
$tgl=tanggalsystem($_POST['tgl']);
$ket=$_POST['ket'];
$peti=$_POST['peti'];
$serah=$_POST['serah'];
$terima=$_POST['terima'];

$noPo=$_POST['noPo'];
//$=$_POST[''];

$txtBarang=$_POST['txtBarang'];
$kdOrg=$_POST['kdOrg'];
$satuan=$_POST['satuan'];

$nobpb=$_POST['nobpb'];
$nopo=$_POST['nopo'];
$nopp=$_POST['nopp'];
$kodebarang=$_POST['kodebarang'];
$jumlah=$_POST['jumlah'];
$satuanpo=$_POST['satuanpo'];
$matauang=$_POST['matauang'];
$kurs=$_POST['kurs'];
$hargasatuan=$_POST['hargasatuan'];
$keteranganpp=$_POST['keteranganpp'];

$tampung=$_POST['tampung'];


$notranDet=$_POST['notranDet'];
$nobpbDet=$_POST['nobpbDet'];
$nopoDet=$_POST['nopoDet'];
$kodebarangDet=$_POST['kodebarangDet'];



$txtBarang=$_POST['txtBarang'];

$arrSt=array("0"=>"X","1"=>"V");
//$arrSt=array("0"=>$_SESSION['lang']['no'],"1"=>$_SESSION['lang']['yes']);
$perSch=$_POST['perSch'];
$kdPtSch=$_POST['kdPtSch'];

//exit("Error:$mengetahui");
$nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
$nmKeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');
$nmCust=makeOption($dbname,'pmn_4customer','kodecustomer,namacustomer');
$nmBarang=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$nmTranp=makeOption($dbname,'log_5supplier','supplierid,namasupplier');



//$optMt="<option value=''>".$_SESSION['lang']['pil']."</option>";
$i="select kode,matauang from ".$dbname.".setup_matauang";
$j=mysql_query($i) or die (mysql_error($conn));
while($k=mysql_fetch_assoc($j))
{
	$optMt.="<option value='".$k['kode']."'>".$k['matauang']."</option>";
}

?>

<?php
switch($method)
{		
	case'updateAll':
		$i="update ".$dbname.".`log_packingdt`  set jumlah='".$jumlah."' where notransaksi='".$notranDet."' and nobpb='".$nobpbDet."' and nopo='".$nopoDet."' and kodebarang='".$kodebarangDet."'";
		if(mysql_query($i))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;
	
	
	case'update'://case update header
		$i="update ".$dbname.".`log_packinght`  set kodept='".$pt."',tanggal='".$tgl."',ukuranpeti='".$peti."',keterangan='".$ket."',menyerahkan='".$serah."',menerima='".$terima."',createby='".$_SESSION['standard']['userid']."' where notransaksi='".$notran."'";
		if(mysql_query($i))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;
	
	##########cari barang
	case'goCariBarang':
	//exit("Error:MASUK");
	//echo asd;
					echo"
						<table cellspacing=1 border=0 class=data>
						<thead>
							<tr class=rowheader>
								<td>No</td>
								<td>".$_SESSION['lang']['kodebarang']."</td>
								<td>".$_SESSION['lang']['namabarang']."</td>
								<td>".$_SESSION['lang']['satuan']."</td>
							</tr>
					</thead>
					</tbody>";
					
					$i="select * from ".$dbname.".log_5masterbarang where kodebarang like '%".$txtBarang."%' or namabarang like '%".$txtBarang."%'";
					//echo $i;
					$n=mysql_query($i) or die (mysql_error($conn));
					while ($d=mysql_fetch_assoc($n))
					{
						$no+=1;
					echo"
						<tr class=rowcontent  style='cursor:pointer;' title='Click It' onclick=\"goPickBarang('".$d['kodebarang']."','".$d['namabarang']."','".$d['satuan']."')\">
							<td>".$no."</td>
							<td>".$d['kodebarang']."</td>
							<td>".$d['namabarang']."</td>
							<td>".$d['satuan']."</td>
						</tr>
					";
					}
				
				break;
	
	
	
	################################################################## cari barang
		case'getFormBarang':
		echo"<fieldset>
				<legend>".$_SESSION['lang']['form']."</legend>
					<table cellspacing=1 border=0>
						<tr>
							<td>".$_SESSION['lang']['notransaksi']."</td> 
							<td>:</td>
							<td><input type=text id=notran value='".$notran."' onkeypress=\"return tanpa_kutip(event);\" class=myinputtext disabled style=\"width:150px;\"></td>
						</tr>
						<tr>
							<td>No. BPB</td>
							<td>:</td>
							<td><input type=text id=nobpb  class=myinputtext maxlength=100 onkeypress=\"return tanpa_kutip(event);\" style='width:100px;'></td>
						</tr>
						<tr>
							<td>".$_SESSION['lang']['nopo']."</td>
							<td>:</td>
							<td><input type=text id=nopo  class=myinputtext maxlength=100 onkeypress=\"return tanpa_kutip(event);\" style='width:100px;'></td>
						</tr>
						<tr>
							<td>".$_SESSION['lang']['nopp']."</td>
							<td>:</td>
							<td><input type=text id=nopp  class=myinputtext maxlength=100 onkeypress=\"return tanpa_kutip(event);\" style='width:100px;'></td>
						</tr>
						<tr>
							<td>".$_SESSION['lang']['kodebarang']."</td>
							<td>:</td>
							<td>
								<input type=text id=kodebarang disabled class=myinputtext maxlength=100 onkeypress=\"return tanpa_kutip(event);\" style='width:100px;'>
								<img src=images/zoom.png title='".$_SESSION['lang']['find']."'  class=resicon onclick=cariBarang('".$_SESSION['lang']['find']."',event)>
							</td>
						</tr>
						
						<tr>
							<td>".$_SESSION['lang']['namabarang']."</td>
							<td>:</td>
							<td>
								<input type=text id=namabarang disabled class=myinputtext maxlength=100 onkeypress=\"return tanpa_kutip(event);\" style='width:100px;'>
							</td>
						</tr>
						
						<tr>
							<td>".$_SESSION['lang']['jumlah']."</td>
							<td>:</td>
							<td><input type=text id=jumlah class=myinputtext maxlength=100 onkeypress=\"return tanpa_kutip(event);\" style='width:100px;'></td>
						</tr>
						<tr>
							<td>".$_SESSION['lang']['satuan']."</td>
							<td>:</td>
							<td><input type=text id=satuan disabled class=myinputtext maxlength=100 onkeypress=\"return tanpa_kutip(event);\" style='width:100px;'></td>
						</tr>
						<tr>
							<td>".$_SESSION['lang']['matauang']."</td>
							<td>:</td>
							<td><select id=matauang = style=\"width:150px;\">".$optMt."</select></td>						
						</tr>
						
						<tr>
							<td>".$_SESSION['lang']['kurs']."</td>
							<td>:</td>
							<td><input type=text id=kurs  class=myinputtext maxlength=100 onkeypress=\"return angka_doang(event);\" style='width:100px;'></td>
						</tr>
						
						<tr>
							<td>".$_SESSION['lang']['harga']."</td>
							<td>:</td>
							<td><input type=text id=hargasatuan  class=myinputtextnumber maxlength=100 onkeypress=\"return angka_doang(event);\" style='width:100px;'></td>
						</tr>
						
						
						
						
						<tr>
							<td>
								<button class=mybutton onclick=saveFormBarang()>Simpan</button>
								<button class=mybutton onclick=cancelFormBarang()>Hapus</button>
								<button class=mybutton onclick=closeDialog()>".$_SESSION['lang']['selesai']."</button>
							</td>
						</tr>
						
					</table>
				</fieldset>	";
		
		
		
		
		/*echo"
			<table cellspacing=1 border=0 class=data>
			<thead>
				<tr class=rowheader>
					<td align=center>".$_SESSION['lang']['nomor']."</td>
					<td align=center>".$_SESSION['lang']['notransaksi']."</td>
					<td align=center>".$_SESSION['lang']['kodebarang']."</td>
					<td align=center>".$_SESSION['lang']['namabarang']."</td>
				</tr>
		</thead>
		</tbody>";
		$i="select kodebarang,namabarang from ".$dbname.".log_5masterbarang where namabarang like '%".$txtBarang."%' or kodebarang like '%".$txtBarang."%'";
		$n=mysql_query($i) or die (mysql_error($conn));
		while ($d=mysql_fetch_assoc($n))
		{
			$no+=1;
			$trKlik="<tr class=rowcontent  style='cursor:pointer;' title='Click It' 
			onclick=\"saveDetail('".$tampung."','".$d['kodebarang']."','".$d['namabarang']."');\">";
			
			echo $trKlik;
			echo"
				<td>".$no."</td>
				<td>".$tampung."</td>
				<td>".$d['kodebarang']."</td>
				<td>".$d['namabarang']."</td>
			</tr>";
		}*/	
	break;
	
	

	
	#################################################################### cari PO
	
		
	case'goCariPo':
		echo"
			<table cellspacing=1 border=0 class=data>
			<thead>
				<tr class=rowheader>
					<td align=center>".$_SESSION['lang']['nomor']."</td>
					<td align=center>No. BPB</td>
					<td align=center>".$_SESSION['lang']['nopo']."</td>
					<td align=center>".$_SESSION['lang']['nopp']."</td>
					<td align=center>".$_SESSION['lang']['kodebarang']."</td>
					<td align=center>".$_SESSION['lang']['namabarang']."</td>
					<td align=center>".$_SESSION['lang']['jumlah']." BPB</td>
					<td align=center>".$_SESSION['lang']['jumlah']." Terkirim</td>
					<td align=center>".$_SESSION['lang']['jumlah']."</td>
					<td align=center>".$_SESSION['lang']['satuan']." PO</td>
					<td align=center>".$_SESSION['lang']['matauang']."</td>
					<td align=center>".$_SESSION['lang']['kurs']."</td>
					<td align=center>".$_SESSION['lang']['harga']."</td>
					<td align=center>".$_SESSION['lang']['keterangan']."</td>
				</tr>
		</thead>
		</tbody>";
		$i="select * from ".$dbname.".log_po_vw where  statuspo='3' and nopo like '%".$noPo."%' and kodeorg='".$pt."'  ";
            //echo $i;
		/*$i="SELECT * from ".$dbname.".log_po_vw a left join ".$dbname.".log_transaksi_vw b 
			on a.nopo=b.nopo and a.kodebarang=b.kodebarang 
			where a.statuspo='3' and a.nopo like '%".$noPo."%' and a.kodeorg='".$pt."' and (b.jumlah!=0 or b.jumlah!='')";*/
		$n=mysql_query($i) or die (mysql_error($conn));
		while ($d=mysql_fetch_assoc($n))
		{
			//$whi="kodebarang='".$d['kodebarang']."' ";
			$whbpb="post=1 and nopo='".$d['nopo']."' and nopp='".$d['nopp']."' and kodebarang='".$d['kodebarang']."'";
			$nobpb=makeOption($dbname,'log_transaksi_vw','nopo,notransaksi',$whbpb);
			//$whi="nopo='".$d['nopo']."' and kodebarang='".$d['kodebarang']."' and tipetransaksi=1 and post=1";
			$jumlah=makeOption($dbname,'log_transaksi_vw','notransaksi,jumlah',$whbpb);
        
			$whn="kodebarang='".$d['kodebarang']."' and nopp='".$d['nopp']."'";
			$ket=makeOption($dbname,'log_prapodt','nopp,keterangan',$whn);
			
                        $sCek="select sum(jumlah) as jumlah from ".$dbname.".log_packing_vw "
                            . "where nopo='".$d['nopo']."' and kodebarang='".$d['kodebarang']."' and nopp='".$d['nopp']."'";
			//echo $sCek;
//			$aCek="select a.nopo,a.kodebarang,sum(a.jumlah) as jumlah,nopp from ".$dbname.".log_packingdt a
//                               where a.nopo='".$d['nopo']."' and a.kodebarang='".$d['kodebarang']."' and a.nopp='".$d['nopp']."'  and a.nopo IS NOT NULL
//                               union
//                               select b.nopo,b.kodebarang,sum(b.jumlah) as jumlah,nopp from ".$dbname.".log_suratjalandt b
//                               where b.jenis='PO' and b.nopo='".$d['nopo']."' and b.kodebarang='".$d['kodebarang']."' and b.nopp='".$d['nopp']."'  and b.nopo IS NOT NULL
//                               union
//                               select c.nopo,c.kodebarang,sum(c.jumlah) as jumlah,nopp from ".$dbname.".log_konosemendt c
//                               where c.jenis='PO' and c.nopo='".$d['nopo']."' and c.kodebarang='".$d['kodebarang']."' and c.nopp='".$d['nopp']."'  and c.nopo  IS NOT NULL";
//                        exit("Error:".$aCek);
			$bCek=mysql_query($sCek) or die (mysql_error($conn));
			$cCek=mysql_fetch_assoc($bCek);
//			
				$jSelisih=$jumlah[$nobpb[$d['nopo']]]-$cCek['jumlah'];
			
			if($jSelisih=='' || $jSelisih=='0')
				$jSelisih='0';
			else
				$jSelisih=$jSelisih;
				
			if($cCek['jumlah']=='' || $cCek['jumlah']=='0')
				$cCek['jumlah']='0';
			else
				$cCek['jumlah']=$cCek['jumlah'];	
			
			//if($nobpb[$d['nopo']]!='')
			if($jSelisih=='' || $jSelisih=='0' || $jSelisih<='0')
			{
				$trKlik="<tr class=rowcontent style='background-color:orange;'>";
			}
			else
			{
				$trKlik="<tr class=rowcontent  style='cursor:pointer;' title='Click It' 
				onclick=\"saveDetail('".$tampung."','".$nobpb[$d['nopo']]."','".$d['nopo']."','".$d['nopp']."','".$d['kodebarang']."','".$jSelisih."',
				'".$d['satuan']."','".$d['matauang']."','".$d['kurs']."','".$d['hargasatuan']."','".$ket[$d['nopp']]."');\">";
			}
			
			
			
			
			
			if($jumlah[$nobpb[$d['nopo']]]==0 || $jumlah[$nobpb[$d['nopo']]]=='')
			{
			}
			else
			{
			echo $trKlik;
			$no+=1;
			echo"
				<td>".$no."</td>
				
				<td>".$nobpb[$d['nopo']]."</td>
				<td>".$d['nopo']."</td>
				<td>".$d['nopp']."</td>
				<td align=right>".$d['kodebarang']."</td>
				<td>".$d['namabarang']."</td>
				
				<td align=right>".$jumlah[$nobpb[$d['nopo']]]."</td>
				<td align=right>".$cCek['jumlah']."</td>
				<td align=right>".$jSelisih."</td>
				
				<td align=right>".$d['satuan']."</td>
				<td>".$d['matauang']."</td>
				<td align=right>".$d['kurs']."</td>
				<td align=right>".$d['hargasatuan']."</td>
				<td>".$ket[$d['nopp']]."</td>
			</tr>";
			}
		}	
	break;

	case'insert':
		$i="INSERT INTO ".$dbname.".`log_packinght` (`notransaksi`, `kodept`, `tanggal`, `ukuranpeti`, `keterangan`, `createby`, `menyerahkan`, `menerima`)	
		values ('".$notran."','".$pt."','".$tgl."','".$peti."','".$ket."','".$_SESSION['standard']['userid']."','".$serah."','".$terima."')";		
		if(mysql_query($i))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;
	
	case'saveFormBarang':
		$i="INSERT INTO ".$dbname.".`log_packingdt` (`notransaksi`, `nobpb`, `nopo`, `nopp`, `kodebarang`, `jumlah`, `satuanpo`, `matauang`, `kurs`, `harga`)
		values ('".$notran."','".$nobpb."','".$nopo."','".$nopp."',
		'".$kodebarang."','".$jumlah."','".$satuan."','".$matauang."','".$kurs."','".$hargasatuan."')";		
		if(mysql_query($i))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;
	
	

	case'saveDetail':
		/*$i="select * from ".$dbname.".log_po_vw where  where nopo='".$nopo."' and nopp='".$nopp."' and kodebarang='".$kodebarang."'  ";
		$n=mysql_query($i) or die (mysql_error($conn));
		$d=mysql_fetch_assoc($n);
		
		
		$a="select sum(jumlah) as jumlah,kodebarang from ".$dbname.".log_packingdt where nopo='".$nopo."' and nopp='".$nopp."' and kodebarang='".$kodebarang."'";
		$b=mysql_query($a) or die (mysql_error($conn));
		$c=mysql_fetch_assoc($b);
		if($c['jumlah']>$c)
        {
			exit("Error:Data Already input in ".$c['notransaksi']."");
		}*/
		$i="INSERT INTO ".$dbname.".`log_packingdt` (`notransaksi`, `nobpb`, `nopo`, `nopp`, `kodebarang`, `jumlah`, `satuanpo`, `matauang`, `kurs`, `harga`, `keteranganpp`)
		values ('".$notran."','".$nobpb."','".$nopo."','".$nopp."',
		'".$kodebarang."','".$jumlah."','".$satuanpo."','".$matauang."','".$kurs."','".$hargasatuan."','".$keteranganpp."')";
	//exit("Error:$i");	
		if(mysql_query($i))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;	
	
	case'updateDetail':
		$i="update ".$dbname.".`log_packingdt`  set jumlah='".$jumlah."' "
                 . "where notransaksi='".$notran."' and nobpb='".$nobpb."' and nopo='".$nopo."' and kodebarang='".$kodebarang."' and nopp='".$_POST['nopp']."'";
	//exit("Error:$i");	
		if(mysql_query($i))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;	
	

	#####LOAD DETAIL DATA	
	case 'loadDetail';/*<td align=center>".$_SESSION['lang']['jumlah']." BPB</td>
					<td align=center>".$_SESSION['lang']['jumlah']." Terkirim</td>*/
			echo"<table class=sortable cellspacing=1 border=0>
			 <thead>
				 <tr class=rowheader>
					<td>".$_SESSION['lang']['nourut']."</td>
					<td align=center>".$_SESSION['lang']['notransaksi']."</td>
					<td align=center>No. BPB</td>
					<td align=center>".$_SESSION['lang']['nopo']."</td>
					<td align=center>".$_SESSION['lang']['nopp']."</td>
					<td align=center>".$_SESSION['lang']['kodebarang']."</td>
					<td align=center>".$_SESSION['lang']['namabarang']."</td>
					
					<td align=center>".$_SESSION['lang']['jumlah']."</td>
					<td align=center>".$_SESSION['lang']['satuan']." PO</td>
					<td align=center>".$_SESSION['lang']['matauang']."</td>
					<td align=center>".$_SESSION['lang']['kurs']."</td>
					<td align=center>".$_SESSION['lang']['harga']."</td>
					<td align=center>".$_SESSION['lang']['keterangan']."</td>
					<td>*</td>
				 </tr>
			</thead>
			<tbody></fieldset>";
		$no=0;
		$a="select * from ".$dbname.".log_packingdt where notransaksi='".$notran."' ";
		//exit("Error:$a");
		$b=mysql_query($a) or die(mysql_error());
		while($c=mysql_fetch_assoc($b))
		{
			$no+=1;
			
//			$xCek="	select a.nopo,a.kodebarang,sum(a.jumlah) as jumlah from ".$dbname.".log_packingdt a
//					where a.nopo='".$c['nopo']."' and a.kodebarang='".$c['kodebarang']."'
//					union
//					select b.nopo,b.kodebarang,sum(b.jumlah) as jumlah from ".$dbname.".log_suratjalandt b
//					where b.jenis='PO' and b.nopo='".$c['nopo']."' and b.kodebarang='".$c['kodebarang']."'
//					union
//					select c.nopo,c.kodebarang,sum(c.jumlah) as jumlah from ".$dbname.".log_konosemendt c
//					where c.jenis='PO' and c.nopo='".$c['nopo']."' and c.kodebarang='".$c['kodebarang']."' ";
//					//echo $xCek;
        
			
			
                        $sCek="select sum(jumlah) as jumlah from ".$dbname.".log_rinciankono "
                            . "where nopo='".$c['nopo']."' and kodebarang='".$c['kodebarang']."' and nopp='".$c['nopp']."'";			
                        $yCek=mysql_query($sCek) or die (mysql_error($conn));
			$zCek=mysql_fetch_assoc($yCek);
        
			$whbpb="post=1 and nopo='".$c['nopo']."' and nopp='".$c['nopp']."' and kodebarang='".$c['kodebarang']."'";
			$nobpb=makeOption($dbname,'log_transaksi_vw','nopo,notransaksi',$whbpb);
			//$whi="nopo='".$d['nopo']."' and kodebarang='".$d['kodebarang']."' and tipetransaksi=1 and post=1";
			$jumlah=makeOption($dbname,'log_transaksi_vw','notransaksi,jumlah',$whbpb);
        
			$whn="kodebarang='".$c['kodebarang']."' and nopp='".$c['nopp']."'";
			$ket=makeOption($dbname,'log_prapodt','nopp,keterangan',$whn);
//			$nobpb=makeOption($dbname,'log_transaksi_vw','nopo,notransaksi');
//			$whi="nopo='".$d['nopo']."' and kodebarang='".$d['kodebarang']."' and tipetransaksi=1 ";
//			$jumlah=makeOption($dbname,'log_transaksi_vw','notransaksi,jumlah',$whi);
			
			//$jumlah[$nobpb[$d['nopo']]];
			
			$jumlahSimpan=$zCek['jumlah']-$c['jumlah'];/*
					<td>".$jumlah[$nobpb[$d['nopo']]]."</td><td>".$jumlahSimpan."</td>*/
			
			echo"<tr class=rowcontent  id=row".$no.">
					<td>".$no."</td>
					<td id=notranDet".$no.">".$c['notransaksi']."</td>
					<td id=nobpbDet".$no.">".$c['nobpb']."</td>
					<td id=nopoDet".$no.">".$c['nopo']."</td>
					<td>".$c['nopp']."</td>
					<td id=kodebarangDet".$no.">".$c['kodebarang']."</td>
					
					<td>".$nmBarang[$c['kodebarang']]."</td>
					
					<td><input type=text id=jumlah".$no." value=".$c['jumlah']." onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>
					<td>".$c['satuanpo']."</td>
					<td>".$c['matauang']."</td>
					<td>".$c['kurs']."</td>
					<td>".$c['harga']."</td>
					<td>".$c['keteranganpp']."</td>
					<td>
						<img src=images/icons/Grey/PNG/save.png class=resicon  title='update' onclick=\"updateDetail('".$c['notransaksi']."','".$c['nobpb']."','".$c['nopo']."','".$c['kodebarang']."',".$no.",'".$jumlah[$nobpb[$d['nopo']]]."','".$jumlahSimpan."','".$c['nopp']."');\" >
						<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"DelDetail('".$c['notransaksi']."','".$c['nobpb']."','".$c['nopo']."','".$c['kodebarang']."','".$c['nopp']."');\" >					
					</td>
				</tr>";
		}		
		echo"<tr>
				<td colspan=14 align=center>
					<button class=mybutton id=editAll onclick=editAll(".$no.")>".$_SESSION['lang']['edit']."</button>
					<button class=mybutton id=cancelDetail onclick=cancel()>".$_SESSION['lang']['selesai']."</button>
				</td>
			 </tr>";//<button class=mybutton id=editAll onclick=editAll()>".$_SESSION['lang']['edit']."</button>
		
		echo"</table>";
	break;	
	

	case'loadData':
                $kdPtLoad="kodept!='' ";
		if($kdPtSch!=''){
			$kdPtLoad="kodept like '%".$kdPtSch."%'";
                }
			
                $perLoad="";	
		if($perSch!=''){
			$perLoad="and tanggal like '%".$perSch."%'";
                }
			
                if($_POST['notransCari']!=''){
                    $perLoad.=" and notransaksi like '%".$_POST['notransCari']."%'";
                }
		echo"
			<table class=sortable cellspacing=1 border=0>
			 <thead>
				 <tr class=rowheader>
					 <td align=center>".$_SESSION['lang']['nourut']."</td>
					 <td align=center>".$_SESSION['lang']['notransaksi']."</td>
					 <td align=center>".$_SESSION['lang']['tanggal']."</td>
					 <td align=center>".$_SESSION['lang']['kodept']."</td>
					 <td align=center>".$_SESSION['lang']['dibuatoleh']."</td>
					 <td align=center>".$_SESSION['lang']['menyerahkan']."</td>
					 <td align=center>".$_SESSION['lang']['menerima']."</td>
					 <td align=center>".$_SESSION['lang']['action']."</td>
				 </tr>
			</thead>
			<tbody>";

			$limit=10;
			$page=0;
			if(isset($_POST['page']))
			{
			$page=$_POST['page'];
			if($page<0)
			$page=0;
			}
			$offset=$page*$limit;
			$maxdisplay=($page*$limit);
			
			$ql2="select count(*) as jmlhrow from ".$dbname.".log_packinght where ".$kdPtLoad."  ".$perLoad."  order by tanggal desc";// where kodeorg='".$kodeorg."' and periode='".$per."'
			//exit("Error:$ql2");
			//where kodeorg='".$kodeorg."' and periode='".$per."' order by lastupdate
			$query2=mysql_query($ql2) or die(mysql_error());
			while($jsl=mysql_fetch_object($query2)){
			$jlhbrs= $jsl->jmlhrow;
			}
			$i="select * from ".$dbname.".log_packinght where ".$kdPtLoad."  ".$perLoad."  order by tanggal desc  limit ".$offset.",".$limit."";
			
			//echo $i;
			$n=mysql_query($i) or die(mysql_error());
			$no=$maxdisplay;
			while($d=mysql_fetch_assoc($n))
			{
				$no+=1;
				echo "<tr class=rowcontent>";
				echo "<td align=center>".$no."</td>";
				echo "<td align=left>".$d['notransaksi']."</td>";
				echo "<td align=left>".tanggalnormal($d['tanggal'])."</td>";
				echo "<td align=left>".$d['kodept']."</td>";
				echo "<td align=left>".$nmKar[$d['createby']]."</td>";
				echo "<td>".$nmKar[$d['menyerahkan']]."</td>";
				echo "<td>".$d['menerima']."</td>";
				
				if($d['posting']=='0')
				{
						$post="<td align=center>
									<img src=images/application/application_edit.png  title='update' class=resicon  caption='Edit' onclick=\"edit('".$d['notransaksi']."','".$d['kodept']."','".tanggalnormal($d['tanggal'])."','".$d['ukuranpeti']."','".$d['keterangan']."','".$d['menyerahkan']."','".$d['menerima']."');\">
									<img src=images/application/application_delete.png  title='delete' class=resicon caption='Delete' onclick=\"delHead('".$d['notransaksi']."');\">
									<img src=images/hot.png  title='Posting' class=zImgBtn caption='Posting' onclick=\"posting('".$d['notransaksi']."');\">
									<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_splht','".$d['notransaksi']."','','log_slave_packing_pdf',event)\">
								</td>";
				}
				else
				{
						$post="<td align=center>
								<img src=images/buttongreen.png class=zImgBtn>
								<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_splht','".$d['notransaksi']."','','log_slave_packing_pdf',event)\">
							   </td>";
				}
					
				echo $post;	
				echo "</tr>";
			}
			echo"
			<tr class=rowheader><td colspan=43 align=center>
			".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
			<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
			<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
			</td>
			</tr>";
			echo"</tbody></table>";
		break;
		
		
		case'delHead':
			$i="delete from ".$dbname.".log_packinght where notransaksi='".$notran."'";
			//exit("Error:$i");
			if(mysql_query($i))
			{
			}
			else
			echo " Gagal,".addslashes(mysql_error($conn));
		break;
		
		
		case'posting':
			$sekarang=date('Y-m-d');
			$i="update  ".$dbname.".log_packinght set posting=1,postingdate='".$sekarang."' where notransaksi='".$notran."'";
			//exit("Error:$i");
			if(mysql_query($i))
			{
			}
			else
			echo " Gagal,".addslashes(mysql_error($conn));
		break;
		
		
		case'deleteDetail':
			$i="delete from ".$dbname.".log_packingdt where notransaksi='".$notran."' and nobpb='".$nobpb."' and nopo='".$nopo."' and kodebarang='".$kodebarang."'  and nopp='".$_POST['nopp']."'";
			//exit("Error:$i");
			if(mysql_query($i))
			echo"";
			else
			echo " Gagal,".addslashes(mysql_error($conn));
		break;
	
	
	
	
	default;
}
?>