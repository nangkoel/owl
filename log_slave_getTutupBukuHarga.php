<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

if(isTransactionPeriod())//check if transaction period is normal
{
		//========================
		  $pt=$_POST['pt'];
                  $gudang=$pt;
		  $user  =$_SESSION['standard']['userid'];
		  $period=$_POST['period'];

		//================================================
		//periksa periode
		$str="select tutupbuku,tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi 
                      where periode='".$period."'
		      and kodeorg ='".$gudang."'";	  
               // echo $str;
		$res=mysql_query($str);
		$awal='';
		$akhir='';
		$periode='benar';
		if(mysql_num_rows($res)>0)
		{
		    $periode='benar';	
                     while($bar=mysql_fetch_object($res))
			{
				//if($bar->tutupbuku==0)
				//{
					
					$awal	=str_replace("-","",$bar->tanggalmulai);
					$akhir	=str_replace("-","",$bar->tanggalsampai);					
				//}
				//else
				//{
				//	$periode='salah';
				//}
			}
		}
		else
		{
			$periode='salah';
		}
  		
//==========================================  
		if($periode=='salah')
		{
			echo " Error: Transaction period not defined";
		} 
		else
		{		  
//=====================================
/*ini di disable agar dapat menghitung nilai sementara
		//periksa apakah gudang sudah closing
			$str="select distinct a.kodegudang,b.tutupbuku,b.periode from ".$dbname.".log_5saldobulanan a
			      left join ".$dbname.".setup_periodeakuntansi b
				  on a.kodegudang=b.kodeorg
			      where a.kodeorg='".$pt."' and a.periode='".$period."'
				  and b.tutupbuku=0 and b.periode='".$period."'";
			  
			$res=mysql_query($str);
			if(mysql_num_rows($res)>0)
			{ 
			  echo " Error: transaction not closed for:\n";
			  while($bar=mysql_fetch_object($res))
			  {
			  	echo "-".$bar->kodegudang."\n";
			  }
			  exit(0);
			}	
*/
//=========================================			
/*//dicancel karena hanya akan menghitung yang sudah di posting saja pada table saldo bulanan
			//cek apakah sudah posting semua pada periode tersebut;
			$str="select distinct kodegudang from ".$dbname.".log_transaksiht
			      where kodept='".$pt."' and tanggal>=".$awal." and tanggal<=".$akhir."
				  and post=0";		      
			$res=mysql_query($str);
			$jlhNotPost=0;
			$jlhNotPost=mysql_num_rows($res);	
			if($jlhNotPost>0)
			{
				while($bar=mysql_fetch_object($res))
				{
					echo "-".$bar->kodegudang."\n";
				}
				echo " Error: ".$_SESSION['lang']['belumposting']." > 0";
			}  
			else
			{
*/
   //ambil semua daftar barang dari log_5saldobulanan berdasarkan pt
                           $str="select distinct a.kodeorg,a.kodegudang,a.kodebarang,b.namabarang,b.satuan from ".$dbname.".log_5saldobulanan a left join
                                 ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang 
                                   where a.kodegudang='".$gudang."' and a.periode='".$period."'";

                           $res=mysql_query($str);
                           $r=mysql_num_rows($res);
                           if($r>0)
                           {	
                           echo "<button class=mybutton onclick=saveSaldoHarga(".$r.");>".$_SESSION['lang']['proses']."</button>
                                 <button style='display:none;' onclick=lanjut(); id=lanjut>Lanjut</button>
                                 <table class=sortable cellspacing=1 border=0>
                                 <thead>
                                           <tr class=rowheader>
                                             <td>No</td>
                                                 <td>".$_SESSION['lang']['periode']."</td>
                                                 <td>".$_SESSION['lang']['tanggalmulai']."</td>
                                                 <td>".$_SESSION['lang']['tanggalsampai']."</td>
                                                 <td>".$_SESSION['lang']['transaksigudang']."</td>
                                                 <td>".$_SESSION['lang']['kodebarang']."</td>
                                                 <td>".$_SESSION['lang']['namabarang']."</td>
                                                 <td>".$_SESSION['lang']['satuan']."</td>
                                           </tr>
                                         </thead>
                                         <tbody>
                                        ";

                           $no=0;
                           while($bar=mysql_fetch_object($res))
                           {
                                $no+=1;
                                echo"<tr class=rowcontent id=row".$no.">
                                             <td>".$no."</td>
                                                 <td id=period".$no.">".$period."</td>
                                                 <td id=start".$no.">".$awal."</td>
                                                 <td id=end".$no.">".$akhir."</td>
                                                 <td id=pt".$no.">".$bar->kodegudang."</td>
                                                 <td id=kodebarang".$no.">".$bar->kodebarang."</td>
                                                 <td>".$bar->namabarang."</td>
                                                 <td>".$bar->satuan."</td>
                                           </tr>";   
                           }
                                echo"</tbody><tfoot></tfoot></table>";
                           }
                           else
                           {
                                echo "No data";
                           }
		//}
		 }

}
else
{
	echo " Error: Transaction Period missing";
}
?>