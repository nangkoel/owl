<?php
//$filename = "D:\WB\\result.nangkoel";
//if(filesize($filename)==0)
//{
//	//echo 0;
//}
//else
//{
//$handle = fopen($filename, "r");
//$contents = fread($handle, filesize($filename));
//$content2 = substr($contents,0,6);
//$content3 = intval($content2);
//fclose($handle);
//echo $content3;
//}
exec("mode com1:BAUD=9600 PARITY=n DATA=8 STOP=1 to=off dtr=of rts=off");
$fp=fopen("com1",'r');
@$text=fread($fp,20);
@fclose($fp);
$text=substr($text,3,6);
//echo $text;
$text= preg_replace('/[^0-9]/','',$text);
$text=intval($text);
//echo $text;
echo "500";
?>
