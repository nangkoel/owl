<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=JavaScript1.2 src=js/languageconf.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('');
echo OPEN_THEME($_SESSION['lang']['langconf']);
$sta="select * from ".$dbname.".namabahasa order by code";
$res=mysql_query($sta);
$opt="";
$langlist="";
$deffind='';
$newCap='';
$newText='';
$count=0;
while($bar=mysql_fetch_object($res))
{
   	
	$count+=1;
	$opt.="<option value=".$bar->code.">".$bar->name."</option>";
	$langlist.=" &nbsp &nbsp<a href=# onclick=loadLang('".$bar->code."')>".$bar->name."</a>";
    $deffind=$bar->code;
	$newCap.="<tr class=rowcontent><td>".$bar->name."</td>
	          <td><input type=hidden value='".$bar->code."' id=hidden".$count."><input type=text class=myinputtext size=120 id=lang".$count." onkeypress=\"return tanpa_kutip(event)\"></td></tr>"; 
}
///
$tabcont[2]="
    <table class=data border=0 cellspacing=0>
	<thead>
	</thead>
	<tbody>
	<tr class=rowcontent><td>
     ".$_SESSION['lang']['newlang']."</td><td> <input type=text class=myinputtext size=3 maxlength=2 id=lang onkeypress=\"return tanpa_kutip(event)\"></td>
	 </tr>
	 <tr class=rowcontent><td>
	 ".$_SESSION['lang']['langname']."</td><td> <input type=text class=myinputtext size=30 maxlength=45 id=langname onkeypress=\"return tanpa_kutip(event)\"></td>
	 </tr>
	 <tr class=rowcontent><td>
	 ".$_SESSION['lang']['deflangtonew']."</td><td><select id=def>".$opt."</select>
	 </td></tr>
	 </tbody>
	 <tfoot>
	 </tfoot>
	 </table>
	 <button class=mybutton onclick=addNewLanguage()>".$_SESSION['lang']['save']."</button>
	 ";
$tabcont[1]="<fieldset><legend>".$_SESSION['lang']['addnewcaption']."</legend>
             <table class=data cellspacing=0 border=0 width=100%>
             <thead>
			 </thead>
			 <tbody>
			 <tr class=rowcontent><td>".$_SESSION['lang']['legend']."</td><td><input type text class=myinputtext id=legend onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=45></td></tr>
			 <tr class=rowcontent><td>".$_SESSION['lang']['location']."</td><td><input type text class=myinputtext id=location onkeypress=\"return tanpa_kutip(event);\" size=55></td></tr>
			 ".$newCap."			  		 
			 </tbody>
			 <tfoot>
			 </tfoot>
			 </table>
			 <center><button onclick=saveNewCaption('".$count."') class=mybutton>".$_SESSION['lang']['save']."</button></center>
			 </fieldset>
            ";
$tabcont[0]="
	  <span id=avlanguage>
	  <fieldset style='width:850px;'>
	  <legend>".$_SESSION['lang']['availlang']."</legend>
	  ".$langlist."
	  </fieldset>
	  </span>	 
	  <br> 
	  <b>".$_SESSION['lang']['findlegendandloc']."</b>
	  <input type=text id=searclang class=myinputtext size=20 onkeypress=\"return duaevent(event);\">
      <button class=mybutton onclick=findComp()>".$_SESSION['lang']['find']."</button> ".$_SESSION['lang']['on']." <span id=defaultfind style='font-weight:bolder;'>".$deffind."</span>
      <fieldset style='height:330px;width:850px;overflow:scroll;'>
      <legend>".$_SESSION['lang']['detailconfigfor']." [<span id=defaultfind1></span>]</legend>
	  <div  style='height:320px;width:840px;overflow:scroll;'>
	  <span id=langdetailconf>
	  
	  </span>
	  </div>
     </fieldset>";		 
	 
$arrhead[2]=$_SESSION['lang']['addnewlanguage'];
$arrhead[1]=$_SESSION['lang']['addnewcaption'];
$arrhead[0]=$_SESSION['lang']['detailconfiguration'];
echo"<br>";	  
drawTab('LANG',$arrhead,$tabcont,200,900);//write employee lis tab
echo CLOSE_THEME();	
CLOSE_BOX();
echo close_body();
?>