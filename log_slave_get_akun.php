<?php
require_once('master_validation.php');
require_once('config/connection.php');

        $txtfind=$_POST['txtfind'];
        $str=" select * from ".$dbname.".keu_5akun where namaakun like '%".$txtfind."%' or namaakun1 like '%".$txtfind."%' or noakun like '%".$txtfind."%' or tipeakun like '%".$txtfind."%'";

 if($res=mysql_query($str))
  {
        echo"
        <fieldset>
        <legend>".$_SESSION['lang']['result']."</legend>
        <div style=\"width:450px; height:300px; overflow:auto;\">
        <table class=data cellspacing=1 cellpadding=2  border=0>
             <thead>
                 <tr class=rowheader>
                 <td class=firsttd>
                 No.
                 </td>
                 <td>".$_SESSION['lang']['noakun']."</td>
                 <td>".$_SESSION['lang']['namaakun']."</td>
                 <td>".$_SESSION['lang']['tipe']."</td>
                 <td>".$_SESSION['lang']['matauang']."</td>
                 <td>".$_SESSION['lang']['kodeorg']."</td>
                 </tr>
                 </thead>
                 <tbody>";
        $no=0;	 
        while($bar=mysql_fetch_object($res))
        {
                $no+=1;
                if($_SESSION['language']=='EN'){
                    $z=$bar->namaakun1;
                }else{
                    $z=$bar->namaakun;
                }
                echo"<tr class=rowcontent style='cursor:pointer;' onclick=\"setNoakun('".$bar->noakun."','".$z."','".$bar->tipeakun."','".$bar->matauang."','".$bar->kodeorg."')\" title='Click' >
                      <td class=firsttd>".$no."</td>
                      <td>".$bar->noakun."</td>
                          <td>".$z."</td><td>".$bar->tipeakun."</td>
                          <td>".$bar->matauang."</td><td>".$bar->kodeorg."</td>
                         </tr>";
        }	 
        echo "</tbody>
              <tfoot>
                  </tfoot>
                  </table></div></fieldset>";	   	
  }	
  else
        {
                echo " Gagal,".addslashes(mysql_error($conn));
        }	

?>