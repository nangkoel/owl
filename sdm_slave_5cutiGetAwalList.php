<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');
$lokasitugas=$_POST['lokasitugas'];
$periode=$_POST['periode'];
$mitmk=$periode."1231";
if($_SESSION['empl']['tipelokasitugas']!='HOLDING'){
	$str1="select karyawanid,nik,namakaryawan,tanggalmasuk,lokasitugas,kodegolongan from ".$dbname.".datakaryawan
	       where lokasitugas='".$lokasitugas."' and alokasi=0
		   and tanggalmasuk<>'0000-00-00' and tanggalkeluar='0000-00-00' and
		   tanggalmasuk<".$mitmk." and tipekaryawan in(0,1,2,3,4)";
}
else
{
    if ($lokasitugas==''){
    	$str1="select karyawanid,nik,namakaryawan,tanggalmasuk,lokasitugas,kodegolongan from ".$dbname.".datakaryawan
	       where alokasi=1
		   and tanggalmasuk<>'0000-00-00' and tanggalkeluar='0000-00-00' and 
		   tanggalmasuk<".$mitmk." and tipekaryawan in(0,1,2,3)";
    } else {
    	$str1="select karyawanid,nik,namakaryawan,tanggalmasuk,lokasitugas,kodegolongan from ".$dbname.".datakaryawan
	       where lokasitugas='".$lokasitugas."' and alokasi=1
		   and tanggalmasuk<>'0000-00-00' and tanggalkeluar='0000-00-00' and 
		   tanggalmasuk<".$mitmk." and tipekaryawan in(0,1,2,3)";
    }
}
	$res1=mysql_query($str1); 
	$max=mysql_num_rows($res1);
	
	echo"<button class=mybutton onclick=simpanAwal(".$max.")>".$_SESSION['lang']['save']."</button>
	     <table class=sortable cellspacing=1 border=0>
	     <thead>
		 <tr class=rowheader>
		    <td>".$_SESSION['lang']['nokaryawan']."</td>
		    <td>".$_SESSION['lang']['nik']."</td>
		    <td>".$_SESSION['lang']['namakaryawan']."</td>
			<td>".$_SESSION['lang']['tanggalmasuk']."</td>
			<td>".$_SESSION['lang']['dari']."</td>
			<td>".$_SESSION['lang']['tanggalsampai']."</td>
			<td>".$_SESSION['lang']['periode']."</td>
			<td>".$_SESSION['lang']['hakcuti']."</td>
			<td>".$_SESSION['lang']['kodeorganisasi']."</td>
			</tr>
		 </thead>
		 <tbody id=container>"; 
	$no=-1;	 
	while($bar1=mysql_fetch_object($res1))
	{
            //=================================
            //default
            $x=readTextFile('config/jumlahcuti.lst');
            if(intval($x)>0)
                $hakcuti=$x;
            else
                $hakcuti=12;  
            
           if(substr($bar1->kodegolongan,0,1)>5){
               $hakcuti=24;
           }
            //=================================
            $no+=1;
		$tgl=substr(str_replace("-","",$bar1->tanggalmasuk),4,4);		
		$dari=mktime(0,0,0,substr($tgl,0,2),substr($tgl,2,2),$periode);
		$dari=date('Ymd',$dari);
		$sampai=mktime(0,0,0,substr($tgl,0,2),substr($tgl,2,2),$periode+1);		
		$sampai=date('Ymd',$sampai);
		#jika periode masuk masih belum 1tahun maka 0
                $d=str_replace("-","",$bar1->tanggalmasuk);
                if($d==$dari)
                    $hakcuti=0;

		echo"<tr class=rowcontent id=baris".$no.">
		           <td id=karyawanid".$no.">".$bar1->karyawanid."</td>
		           <td id=nik".$no.">".$bar1->nik."</td>
				   <td id=nama".$no.">".$bar1->namakaryawan."</td>
				   <td>".tanggalnormal($bar1->tanggalmasuk)."</td>
				   <td id=dari".$no." value=".$dari.">".$dari."</td>
				   <td id=sampai".$no." value=".$sampai.">".$sampai."</td>
				   <td id=periode".$no.">".$periode."</td>
				   <td id=hak".$no.">".$hakcuti."</td>
				   <td id=kodeorg".$no.">".substr($bar1->lokasitugas,0,4)."</td>
				   ";
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
?>