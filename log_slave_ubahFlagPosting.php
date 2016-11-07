<?php
    require_once('master_validation.php');
    require_once('config/connection.php');

    $notransaksi=$_POST['notransaksi'];
    $status=$_POST['status'];
    $gudang=$_POST['gudang'];
    $user=$_SESSION['standard']['userid'];
    
    #periksa apakah semua detail sudah status saldo 1
    $str="select kodebarang from ".$dbname.".log_transaksidt where notransaksi='".$notransaksi."'  and statussaldo=0"; 
    $res=mysql_query($str);
    if(mysql_num_rows($res)>0)
	{
		#jika belum maka jangan ubah flag
        echo "Error : There is still unsucceed transaction, please re-process";
    }
    else
	{  #jika sudah, maka ubah flag      
		$str="update ".$dbname.".log_transaksiht set post=".$status.", postedby=".$user.",statusjurnal=1
				 where notransaksi='".$notransaksi."'  and kodegudang='".$gudang."'";   
		if(mysql_query($str))
		{
			if(mysql_affected_rows($conn)<1)
			{
					echo "Error : post status update nothing";
			}
			#condisi posting berhasil 
			else
			{
				#cek untuk tipe transaksi 7(mutasi) dan ke mutasinya ke HO bukan
				$i="select * from ".$dbname.".log_transaksiht where tipetransaksi='7' and gudangx like '%HO%' and notransaksi='".$notransaksi."' ";
				$n=mysql_query($i) or die (mysql_error($conn));
				if(mysql_num_rows($n)>0)
				{
					//exit("Error:MASUK");
					#jika ada maka kirim email ke HO
					$x="select nilai from ".$dbname.".setup_parameterappl where kodeparameter='EMAILPR'";
					$y=mysql_query($x);
					while($z=mysql_fetch_assoc($y))
					{
						$to=$z['nilai'];
						$namakaryawan=getNamaKaryawan($_SESSION['standard']['userid']);
						$subject="[Notifikasi] Mutasi Barang ke HO : ".$notransaksi." ";
						$body="<html>
								 <head>
								 <body>
								   <dd>Dengan Hormat, Mr./Mrs. ".$nmpnlk."</dd><br>
								   Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan.", melakukan pengiriman barang ke HO dengan nomor document ".$notransaksi." 
								   <br>
								   Regards,<br>
								   Owl-Plantation System.
								 </body>
								 </head>
							   </html>
							   ";//exit("Error:$body");
					   $x=kirimEmail($to,$subject,$body);#this has return but disobeying;	
					
					}
					
				}
			}
			
			
				
		}
		else
				{
					echo " Gagal,".(mysql_error($conn));
				}    
    }        
?>
