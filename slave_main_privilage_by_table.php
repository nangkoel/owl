<?php
require_once('master_validation.php');
require_once('config/connection.php');

$parent=$_POST['parent'];
$sub=$_POST['sub'];
$assign=$_POST['assign'];
$_POST['parent']==''?$menu=$_POST['sub']:$menu=$_POST['parent'];
$proses=$_GET['proses'];

$menuId=$_POST['id_menu'];
$usrname=$_POST['usernm'];
$stat=$_POST['stat'];
switch($proses)
{
    case'getForm':
    
if($sub=='true')
{
	$str="select * from ".$dbname.".menu 
	      where parent=".$parent." order by urut";
}
else
{
	$str="select * from ".$dbname.".menu 
	      where type='master' order by urut";	
}
	$res=mysql_query($str);
	echo"<input type=hidden id=id_menu value='".$menu."' />
             <input type=button class=mybutton value='".$_SESSION['lang']['close']."' onclick=closeOrderEditor()>
             <input type=checkbox id=assigned onclick=showAssignEditor('".$menu."','true')>Filter by assigned only</input>";
		echo"<div style=overflow:auto;scroll;height:550px;>
                     <table width=100% cellspacing=1 border=0 class=data>
             <thead>
		     <tr>
			 <td>".$_SESSION['lang']['action']."</td>
			 <td id=usr_nm_".$no.">".$_SESSION['lang']['username']."</td>
			 <td>".$_SESSION['lang']['namakaryawan']."</td>
			 <td>".$_SESSION['lang']['lokasitugas']."</td>
			 <td>".$_SESSION['lang']['jabatan']."</td>
			 </tr>
			 </thead><tbody>";
                $sData="select distinct a.karyawanid,namauser,b.namakaryawan,b.lokasitugas,b.kodejabatan from ".$dbname.".user a
                        left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where status!=0";
                $qData=mysql_query($sData) or die(mysql_error($conn));
                while($rData=mysql_fetch_assoc($qData))
                {
                    $no++;
                    $sJbtn="select namajabatan from ".$dbname.".sdm_5jabatan where kodejabatan='".$rData['kodejabatan']."'";
                    $qJbtn=mysql_query($sJbtn) or die(mysql_error($conn));
                    $rJbtn=mysql_fetch_assoc($qJbtn);
                    $arrd="";
                    $sAuth="select distinct * from ".$dbname.".auth 
                            where namauser='".$rData['namauser']."' and menuid='".$menu."' and status=1";
                    $qAuth=mysql_query($sAuth) or die(mysql_error($conn));
                    $rAuth=mysql_num_rows($qAuth);
                    if($rAuth==1)
                    {
                        $arrd="checked";
                    }
                     echo"<tr class=rowcontent>
			 <td><input type=checkbox id=adddt_".$no." onclick=addData(".$no.",".$menu.") ".$arrd." /></td>
			 <td id=usr_nm_".$no.">".$rData['namauser']."</td>
			 <td>".$rData['namakaryawan']."</td>
			 <td>".$rData['lokasitugas']."</td>
			 <td>".$rJbtn['namajabatan']."</td>
			 </tr>";
                }
       echo"</tbody></table><br><table><tr><td>&nbsp;</td></tr></table></div>";
    break;
    case'addData':
        if($stat==1)
        {
          #==============
            $menu[]=$menuId;
            for($x=0;$x<=7;$x++){
                if($menuId!=''){
                        $str="select parent from ".$dbname.".menu where id=".$menuId;
                        $res=mysql_query($str);
                            while($bar=mysql_fetch_object($res)){
                                if($bar->parent!=0){
                                    $menu[]=$bar->parent;
                                    $menuId=$bar->parent;                    
                                }
                            }
                }
            }
            #================================   Add juga untuk semua parent nya
                foreach($menu as $key=>$val){      
                  $str="delete from ".$dbname.".auth where menuid=".$val." and namauser='".$usrname."'";
                  mysql_query($str);       
                  $str="insert into ".$dbname.".auth(namauser, menuid, status, lastuser, detail)
                                   values('".$usrname."',".$val.",1,".$_SESSION['standard']['userid'].",0)";
                  mysql_query($str);
                 }
       /*     
            $sDel="delete from ".$dbname.".auth where namauser='".$usrname."' and menuid='".$menuId."'";
            if(mysql_query($sDel))
            {
                $sInsert="insert into ".$dbname.".auth (namauser,menuid,lastuser,status)";
                $sInsert.=" values ('".$usrname."','".$menuId."','".$_SESSION['standard']['username']."','1')";
                mysql_query($sInsert) or die(mysql_error($conn));
            }
            else
            {
                $sInsert="insert into ".$dbname.".auth (namauser,menuid,lastuser,status)";
                $sInsert.=" values ('".$usrname."','".$menuId."','".$_SESSION['standard']['username']."','1')";
                mysql_query($sInsert) or die(mysql_error($conn));
            }
        
            */
        }
        else
        {
            $sDel="delete from ".$dbname.".auth where namauser='".$usrname."' and menuid='".$menuId."'";
            mysql_query($sDel) or die(mysql_error($conn));
        }
    break;
    default:
    break;
}

	   
	   
	
?>
