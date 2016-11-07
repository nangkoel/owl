<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

   $tahunplafon=$_POST['tahunplafon'];
   $periode		=$_POST['periode'];
   $jenisbiaya	=$_POST['jenisbiaya'];
   $karyawanid	=$_POST['karyawanid'];
   $method		=$_POST['method'];
   $ygberobat	=$_POST['ygberobat'];
   $rs			=$_POST['rs'];
   $diagnosa	=$_POST['diagnosa'];
   $klaim		=$_POST['klaim'];
   $notransaksi	=$_POST['notransaksi'];
   $hariistirahat=$_POST['hariistirahat'];
   $tanggal		=$_POST['tanggal'];
   $keterangan	=$_POST['keterangan'];		   
   $byrs		=$_POST['byrs'];
   $byadmin		=$_POST['byadmin'];
   $bydr		=$_POST['bydr'];
   $byobat		=$_POST['byobat'];
   $total		=$_POST['total'];
   $bylab		=$_POST['bylab'];
   $bebanperusahaan	=$_POST['bebanperusahaan'];
   $bebankaryawan	=$_POST['bebankaryawan'];
   $bebanjamsostek	=$_POST['bebanjamsostek'];   
   if(!isset($_POST['tahunplafon'])){
   	$tahunplafon=date('Y');
   }
   
   #ngecekan plafon jika RWINP dan rwtinp
   if(($jenisbiaya=='RWINP')||($jenisbiaya=='RWJLN')){
       $optNmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
       $scek="select sum(jlhbayar) as totDibyr from ".$dbname.".sdm_pengobatanht 
              where karyawanid='".$karyawanid."' and tahunplafon='".$tahunplafon."'";
       $qcek=mysql_query($scek) or die(mysql_error($conn));
       $rcek=mysql_fetch_assoc($qcek);

       $sgapok="select distinct sum(jumlah) as jmlhgapok from ".$dbname.".sdm_5gajipokok where
                karyawanid='".$karyawanid."' and tahun='".$tahunplafon."' and idkomponen in (1,2)";
       $qgapok=mysql_query($sgapok) or die(mysql_error($conn));
       $rgapok=mysql_fetch_assoc($qgapok);
       
       $sprsn="select distinct persen from ".$dbname.".sdm_pengobatanplafond 
               where kodejenisbiaya='".$jenisbiaya."'";
       $qprsn=mysql_query($sprsn) or die(mysql_error($conn));
       $rprsn=mysql_fetch_assoc($qprsn);
       $totPlafon=$rgapok['jmlhgapok']*($rprsn['persen']/100);
//       if($rcek['totDibyr']>$totPlafon){
//           exit("error: Plafon untuk ".$optNmKar[$karyawanid]." sudah melewati batas!!\n
//                        Plafon= ".number_format($totPlafon,2).", Reimbursement=".number_format($rcek['totDibyr'],2));
//       }
   }
   $kodeorg=substr($_SESSION['empl']['lokasitugas'],0,4);
   if($karyawanid==''){
       $karyawanid=0;
   }
  if($method=='insert')
  {
  	$str="insert into ".$dbname.".sdm_pengobatanht (	
		  `notransaksi`, `kodeorg`, `karyawanid`,
		  `tahunplafon`, `kodebiaya`, `keterangan`,
		  `rs`, `updateby`, `jasars`,  `jasadr`,
		  `jasalab`, `byobat`, `bypendaftaran`,
		  `ygsakit`, `jlhbayar`, `tanggalbayar`,
		  `totalklaim`, `jlhhariistirahat`,
		  `klaimoleh`, `periode`, `tanggal`, `diagnosa`,
                                          `bebanperusahaan`, `bebankaryawan`, `bebanjamsostek`)
		  values(
		  '".$notransaksi."','".$kodeorg."',".$karyawanid.",
		   ".$tahunplafon.",'".$jenisbiaya."','".$keterangan."',
		    '".$rs."',".$_SESSION['standard']['userid'].",
			'".$byrs."','".$bydr."','".$bylab."','".$byobat."','".$byadmin."',
			'".$ygberobat."',0,'0000-00-00',
			'".$total."','".$hariistirahat."',
			'".$klaim."','".$periode."','".tanggalsystem($tanggal)."',
			'".$diagnosa."','".$bebanperusahaan."','".$bebankaryawan."','".$bebanjamsostek."'			
		  )";	  
        //exit("error:".$str);
  }
  else if($method=='del')
  {
  	$str="delete from ".$dbname.".sdm_pengobatanht where notransaksi='".$notransaksi."'";
  }
  else
  {
  	$str="select 1=1";
  }
	
	if(mysql_query($str))
	{
		$str="select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag from ".$dbname.".sdm_pengobatanht a left join
	      ".$dbname.".sdm_5rs b on a.rs=b.id 
		  left join ".$dbname.".datakaryawan c
		  on a.karyawanid=c.karyawanid
		  left join ".$dbname.".sdm_5diagnosa d
		  on a.diagnosa=d.id
		  where a.periode='".$tahunplafon."'  
		  and a.kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
		  order by a.updatetime desc, a.tanggal desc";
	  $stream='';
		$res=mysql_query($str);  
		  $no=0;
		  while($bar=mysql_fetch_object($res))
		  {
			   $no+=1;
			   echo"<tr class=rowcontent>
			   <td>";
			   
			   if($bar->posting==0)
			   {
			   	 echo"<img src=images/close.png title='delete' class=resicon onclick=deletePengobatan('".$bar->notransaksi."')>";
			   }
			     echo"<img src=images/zoom.png title='View' class=resicon onclick=previewPengobatan('".$bar->notransaksi."',event)>";
			   
			   echo"</td><td>".$no."</td>
				  <td>".$bar->notransaksi."</td>
				  <td>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</td>
				  <td>".tanggalnormal($bar->tanggal)."</td>
				  <td>".$bar->namakaryawan."</td>
				  <td>".$bar->namars."[".$bar->kota."]"."</td>
				  <td>".$bar->kodebiaya."</td>
				  <td align=right>".number_format($bar->totalklaim,2,'.',',')."</td>
				  <td align=right>".number_format($bar->jlhbayar,2,'.',',')."</td>
                                                                                <td align=right>".number_format($bar->bebanperusahaan,2,'.',',')."</td>
                                                                                <td align=right>".number_format($bar->bebankaryawan,2,'.',',')."</td>
                                                                                <td align=right>".number_format($bar->bebanjamsostek,2,'.',',')."</td>                                         
				  <td>".$bar->ketdiag."</td>
				  <td>".$bar->keterangan."</td>
				</tr>";  	
		  }			  		
	}
	else
	{
		echo " Error: ".addslashes(mysql_error($conn));
	} 	
?>
