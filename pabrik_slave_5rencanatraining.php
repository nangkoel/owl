<?php
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/nangkoelib.php');

    $kamar          =$_POST['kamar'];
    if($kamar==''){
        $kamar=$_GET['kamar'];
        $kodetraining=$_GET['kodetraining'];
    }else{
    $tahunbudget        =$_POST['tahunbudget'];
    $listtahun          =$_POST['listtahun'];
    $kodetraining       =$_POST['kodetraining'];
    $namatraining       =$_POST['namatraining'];
    $levelpeserta       =$_POST['levelpeserta'];
    $levelpeserta       =$_POST['levelpeserta'];
    $penyelenggara      =$_POST['penyelenggara'];

    $hargaperpeserta    =$_POST['hargaperpeserta'];
    $deskripsitraining  =$_POST['deskripsitraining'];
    $hasildiharapkan    =$_POST['hasildiharapkan'];
    }
	
//kamus host
$str="select * from ".$dbname.".log_5supplier where kodekelompok = 'S001' order by namasupplier";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $host[$bar->supplierid]=$bar->namasupplier;
}
//kamus jabatan
$str="select * from ".$dbname.".sdm_5jabatan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $jab[$bar->kodejabatan]=$bar->namajabatan;
}

if($kamar=='tahun')
{
    $str="select distinct tahunbudget from ".$dbname.".sdm_5training order by tahunbudget desc";
    $res=mysql_query($str);
    $opttahun="<option value=''>".$_SESSION['lang']['all']."</option>";
    while($bar=mysql_fetch_object($res))
    {
        $opttahun.="<option value='".$bar->tahunbudget."'>".$bar->tahunbudget."</option>";
    }
    echo $opttahun;
}    

if($kamar=='list')
{
    $str="select * from ".$dbname.".sdm_5training where tahunbudget like '%".$listtahun."%'
          ";
    $res=mysql_query($str);
    $no=1;
    while($bar=mysql_fetch_object($res))
    {
        echo"<tr class=rowcontent>
        <td>".$no."</td>
        <td>".$bar->tahunbudget."</td>
        <td>".$bar->kode."</td>
        <td>".$bar->namatraining."</td>
        <td>".$jab[$bar->jabatan]."</td>
        <td>".$host[$bar->penyelenggara]."</td>

        <td align=right>".number_format($bar->hargasatuan,2,'.',',')."</td>
        <td>
            <img src=images/application/application_edit.png class=resicon  title='edit' onclick=\"edittraining('".$bar->tahunbudget."','".$bar->kode."','".$bar->namatraining."','".$bar->jabatan."','".$bar->penyelenggara."','".$bar->hargasatuan."','".$bar->desctraining."','".$bar->output."');\">
        </td>
        <td>
            <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"deletetraining('".$bar->kode."');\">
        </td>
        <td>
            <img src=images/application/application_form.png class=resicon  title='desc and result' onclick=\"desctraining('".$bar->kode."',event);\">
        </td>
        </tr>";	
        $no+=1;
    }	  
    
}  

if($kamar=='desc'){
?>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?php    
    $str="select * from ".$dbname.".sdm_5training
          where kode='".$kodetraining."'";
    $res=mysql_query($str);
    echo"<table class=sortable cellspacing=1 border=0 width=100%>";
    while($bar=mysql_fetch_object($res))
    {
        echo"<tr class=rowtitle><td>".$_SESSION['lang']['deskripsitraining']."</td></tr>
        <tr class=rowcontent><td align=center><textarea disabled=true>".$bar->desctraining."</textarea></td></tr>
        <tr class=rowtitle><td>".$_SESSION['lang']['hasildiharapkan']."</td></tr>
        <tr class=rowcontent><td align=center><textarea disabled=true>".$bar->output."</textarea></td></tr>
            <tr class=rowcontent><td align=center>&nbsp;</td></tr>
            <tr class=rowcontent><td align=center><button class=mybutton onclick=parent.closeDialog()>".$_SESSION['lang']['close']."</button></td></tr>";
    }
    echo"</table>";
}

if($kamar=='save')
{
    $strx="insert into ".$dbname.".sdm_5training
        (kode,namatraining,jabatan,
	penyelenggara,hargasatuan,desctraining,
	output,tahunbudget)
	values('".$kodetraining."','".$namatraining."','".$levelpeserta."',
	'".$penyelenggara."','".$hargaperpeserta."','".$deskripsitraining."',
	'".$hasildiharapkan."','".$tahunbudget."')";			   
  if(mysql_query($strx))
    {
    }	
  else
    {
        echo " Gagal,".addslashes(mysql_error($conn));
    }
}

if($kamar=='delete')
{
$strx="delete from ".$dbname.".sdm_5training 
    where kode='".$kodetraining."'";
if(mysql_query($strx))
{
	
} else {
    echo " Gagal,".addslashes(mysql_error($conn));
}
}

if($kamar=='edit')
{
    $strx="update ".$dbname.".sdm_5training set
        namatraining = '".$namatraining."',
        jabatan = '".$levelpeserta."',
        penyelenggara = '".$penyelenggara."',
        hargasatuan = '".$hargaperpeserta."',
        desctraining = '".$deskripsitraining."',
        output = '".$hasildiharapkan."',
        tahunbudget = '".$tahunbudget."'   
        where kode = '".$kodetraining."'";
    if(mysql_query($strx))
    {
    } else {
        echo " Gagal,".addslashes(mysql_error($conn));
    }
}
	
?>
