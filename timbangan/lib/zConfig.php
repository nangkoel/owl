<?php
/* Function readLst
 * Fungsi untuk membaca file .lst
 * I : Nama File
 * O : array of content
 */
function readLst($file)
{
    $comment = "#";
    $fp = fopen($file, "r");
    
    $list = array();
    $lin=0;
    while (!feof($fp)) {
        $line = fgets($fp, 4096); // Read a line.
        if(!ereg("^#",$line) AND $line!=''){
            $pieces = explode("=", $line);
            
            foreach($pieces as $content) {
                $list[$lin][] = $content;
            }
            $lin++;
        }
    }
    
    fclose($fp);
    return $list;
}

/* Function lst2opt
 * Fungsi untuk membaca file .lst
 * I : array hasil readLst, int urutan nomor untuk kode dari lst, int urutan nomor untuk nama dari lst
 * O : array untuk format opt
 */
function lst2opt($arr,$kode,$nama){
    $resArr = array();
    
    foreach($arr as $row) {
        $resArr[$row[$kode]] = $row[$nama];
    }
    
    return $resArr;
}
?>