<?php
require_once('master_validation.php');
require_once('config/connection.php');

$str="delete from ".$dbname.".rencana_gis_file where namafile='".$_POST['namafile']."' and karyawanid=".$_SESSION['standard']['userid'];
mysql_query($str);
if(mysql_affected_rows($conn)>0)
{
    echo"";
    unlink('filegis/'.$_POST['namafile']);
}
else
{
    echo "Error : Tidak ada file terhapus (".mysql_error($conn).")";
}
?>
