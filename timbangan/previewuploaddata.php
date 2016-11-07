<?php
include('config/connection.php');
//include('function/functions.php');
include('lib/nangkoelib.php');
?>
<style>
	input{
		height:17px;
		font-size:11px;
		font-family:Tahoma,Arial Narrow;
	}
	table{
		font-size:11px;
		font-family:Tahoma,Arial Narrow;
		background-color:#dedede;
	}
	.header{
		background-color:skyblue;
	}
	.content{
		background-color:#ffffff;
	}
</style>
<script language=javascript1.2 src=js/HttpRequestRFC.js></script>
<script language=javascript1.2>
  array_id =new Array();
  current_index=-1;
  active_cell='';
  active_row='';
  remote_ip='';
  function upload(list_id,ip)
  {
	array_id=list_id.split(",");
	remote_ip=ip;
	if (array_id.length > 0 && remote_ip.length>11)
	{
		if (confirm('Anda yakin upload...?')) {
			do_upload(array_id[current_index]);
		}
	}
	else
	{
		alert('Data atau alamat server tidak lengkap ('+ip+')');
	}
  }

  function do_upload()
  {
    current_index++;
	if(current_index<array_id.length)
    {
		active_cell = '_status' + array_id[current_index];
		active_row = '_' + array_id[current_index];
		param='id='+array_id[current_index]+'&ip='+remote_ip;
		document.getElementById(active_row).style.backgroundColor='orange';
		hubungkan_post('douploadwbtobps.php',param,respo_oploade);
	}
	else
	{
		document.getElementById('btt').style.display='none';//hide stop button
		parent.document.getElementById('pro').style.display='none';//hide progress bar
		alert('Selesai..!\nTerima kasih telah mengunakan program WB');
	}
  }

function respo_oploade()
{
     if(con.readyState==4)
     {
        if(con.status==200)
        {
		   if(con.responseText.indexOf('Gagal')>-1 || con.responseText.indexOf('error')>-1)
            {
			 document.getElementById(active_cell).innerHTML='Failed'+con.responseText;
			}
			else
			{
			   document.getElementById(active_cell).innerHTML='Success';
			   document.getElementById(active_row).style.backgroundColor='green';
			}
//===========================================progress bar
			satu=(current_index/(array_id.length-1)*100);
			percent=parseInt(satu/100*200);
			satu=parseInt(satu);
			document.getElementById('subm').style.display='none';//hide submit button forever
			document.getElementById('btt').style.display='';//display stop button
			parent.document.getElementById('pro').style.display='';//display progressbar
			parent.document.getElementById('pro_in').style.color='white';
			parent.document.getElementById('pro_in').style.width=percent+'px';
			parent.document.getElementById('pro_in').innerHTML=satu+'%';
            //alert(percent+"px");
//============================================
		   unlock();
		  do_upload();
		}
        else
        {
		  unlock();
          err=error_catch(con.status);
		  alert(err);
        }
     }

}

function berhenti()
{
	if(confirm("Anda Yakin mau Stop..?"))
	{
		parent.document.getElementById('pro').style.display='none';
		window.location.reload();
	}
}
</script>
<body>
<center>
	<?php
	$mill='Undefined';
	$sta=" select millcode from wbridge.msuser where millcode!='' limit 1";
	$resa=mysql_query($sta);
		while($bara=mysql_fetch_array($resa))
		{
			$mill=$bara[0]; //ambil kode parik untuk email subject
		}
//get goods code from master
	$strp="select PRODUCTCODE from wbridge.msproduct where BPS=1 and PRODUCTCODE!=''";
	$resp=mysql_query($strp);
	$pcode="";
		while($barp=mysql_fetch_array($resp))
		{
			if($pcode=="")
			   $pcode="'".$barp[0]."'";
			else
			   $pcode.=",'".$barp[0]."'";//ambil semua kode barang yang di GI
		}

//====================================
$wilayah=trim($_GET['wilayah']);

//===============================
//===============================
	$strx="select * from newwbridge.mstrxtbs where
			 PRODUCTCODE in (".$pcode.")
			 AND BPS='0' AND OUTIN=0
			 AND TRANSACTIONTYPE=0
			 AND length(UNITCODE)=4
			 and UNITCODE in(
				 select distinct UNITCODE from newwbridge.msunit
				 where wilcode='".$wilayah."'
			 )
			 ";
	echo $strx;
	/*$strx="select * from wbridge.mstrxtbs where
			 PRODUCTCODE in (".$pcode.")
			 AND BPS='0' AND OUTIN=0
			 AND TRANSACTIONTYPE=0
			 AND left(TRPCODE,2)!='10'
			 and UNITCODE in(
				 select distinct UNITCODE from wbridge.msunit
				 where UNITCODE !='SAGE' and UNITCODE !='SDME' and UNITCODE !='SBHE'
			 )
			 ";*/
	//echo $strx;
	$resx=mysql_query($strx);
	//echo mysql_error($con);
?>
</div>
  <table cellspacing=1 border=0>
  	<tr class=header>
	 <td>STATUS</td>
	 <td>NO</td>
	 <td>TRX_ID</td>
	 <td>MILLCODE</td>
	 <td>TICKETNO</td>
	 <td>UNITCODE</td>
	 <td>SPBNO</td>
	 <td>PRODUCTCODE</td>
	 <td>DATEIN</td>
	 <td>VEHNOCODE</td>
	 <td>JMLHJJG</td>
	 <td>BRONDOLAN</td>
	 <td>DRIVER</td>
	 <td>NETTO</td>
  	</tr>


<?php
$no=0;
$post_id='';
while($barx=mysql_fetch_object($resx))
{
	$no+=1;
	if($post_id=='')
	   $post_id=$barx->id;
	else
	   $post_id.=",".$barx->id;


   echo"<tr class=content id=_".$barx->id.">
	 <td id=_status".$barx->id."> </td>
	 <td>$no</td>
	 <td>".$barx->id."</td>
	 <td>".$barx->MILLCODE."</td>
	 <td>".$barx->TICKETNO."</td>
	 <td>".$barx->UNITCODE."</td>
	 <td>".$barx->SPBNO."</td>
	 <td>".$barx->PRODUCTCODE."</td>
	 <td>".tanggalnormal(substr($barx->DATEOUT,0,10))."</td>
	 <td>".$barx->VEHNOCODE."</td>
	 <td>".$barx->JMLHJJG."</td>
	 <td>".$barx->BRONDOLAN."</td>
	 <td>".$barx->DRIVER."</td>
	 <td>".$barx->NETTO."</td>
  	</tr>";
}
//=======================================================
//get DESTINATION IP
	$ip='';
	$name='';
	$port='3306';
$str="select addr,name,port from ".$dbname.".bpssvr where wilayah='".$wilayah."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$ip=trim($bar->addr);
	$name=trim($bar->name);
	$port=trim($bar->port);

}

if($port=='')
{
	$port='3306';
}
//====================================================
@$conn_remote = mysql_connect($ip.':'.$port, $remote_uname, $remote_password);
if (@!mysql_ping($conn_remote))
{
	    echo '<font color=red><b>Tidak ada/dapat di upload</b>,Error Code: Koneksi ke Remote server '.$ip.'('.$name.') Gagal</font>';
        exit;
}
else if($no==0)
{
	    echo '<font color=orange><b>Tidak ada/data</b></font>';
        exit;

}
else
{

   echo"<tr class=content>
	 <td colspan=14 align=center><input type=button id=subm value=Upload onclick=\"upload('".$post_id."','".$ip.":".$port."')\"; style='height:20px;width:75px' title='Click untuk mengirim data'>
	 <input type=button id=btt value=Stop onclick=berhenti() title='Click untuk STOP' style='display:none;height:20px;width:75px'></td>
  	</tr>";
}
?>
  </table>

</center>
</body>
<div id=progress  style='display:none;background-color:orange;position:absolute;top:0px;right:0px;width:100px'>
Proccessing.....
</div>
