<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');

//==================================================================================================================================================================
 echo"<div>
     <fieldset style='width:200px;color:#333399;'>
	 <legend>[Info] ".$_SESSION['lang']['menulevel'].":</legend>
	 ".$_SESSION['lang']['menuleveldesc']."
	 </fieldset><br>
     <input type=radio name=rad1 onclick=expandAllOrder()>".$_SESSION['lang']['expandall']."
	 <input type=radio name=rad1 onclick=collapsAllOrder() checked>".$_SESSION['lang']['colapsall']."
	 <hr>";
echo"<ul>";


$opt='<option>0</option>';
for($d=1;$d<25;$d++)
{
	$opt.="<option>".$d."</option>";
}

$str="select * from ".$dbname.".menu 
      where type='master' order by urut";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	echo "<li class=mmgr><img title=expand class=arrow src='images/foldc_.png' height=17px  onclick=show_sub('orderchild".$bar->id."',this);>
	<a class=lab id=orderlab".$bar->id.">".$bar->caption."</a>
	<select onchange=\"updateMenuLevel(this,this.options[this.selectedIndex].text,'".$bar->id."')\"><option>".$bar->access_level."</option>".$opt."</select>";
//=========================================================
	     $str1="select * from ".$dbname.".menu 
                where parent=".$bar->id." order by urut";
		 $res1=mysql_query($str1);	
 
			 echo"<ul id=orderchild".$bar->id." style='display:none;')>
			      <div id=ordergroup".$bar->id.">";
			 while($bar1=mysql_fetch_object($res1))
			 {
				if(strtolower($bar1->class)=='devider')
				{
				   $bar1->caption="------------";	
				}
				if(strtolower($bar1->class)=='title' or strtolower($bar1->class)=='devider')
				{
				  echo "<li class=mmgr><img src='images/menu/arrow_10.gif'> 
				  ".$bar1->caption;		
				}
				else
				{
				   echo "<li class=mmgr><img title=Expand class=arrow src='images/foldc_.png' height=17px  onclick=show_sub('orderchild".$bar1->id."',this);> 
						<a class=lab id=orderlab".$bar1->id.">".$bar1->caption."</a>
						<select onchange=\"updateMenuLevel(this,this.options[this.selectedIndex].text,'".$bar1->id."')\"><option>".$bar1->access_level."</option>".$opt."</select>";
				}
			//=========================================================
				     $str2="select * from ".$dbname.".menu 
			                where parent=".$bar1->id." order by urut";
					 $res2=mysql_query($str2);	
 
						 echo"<ul id=orderchild".$bar1->id." style='display:none;')>
						      <div id=ordergroup".$bar1->id.">";
						 while($bar2=mysql_fetch_object($res2))
						 {
							if(strtolower($bar2->class)=='devider')
							   $bar2->caption="------------";							
							if(strtolower($bar2->class)=='title' or strtolower($bar2->class)=='devider')
							{
							   echo "<li class=mmgr><img src='images/menu/arrow_10.gif'> 
							    ".$bar2->caption;		
							}
							else{
								echo "<li class=mmgr><img title=Expand class=arrow src='images/foldc_.png' height=17px onclick=show_sub('orderchild".$bar2->id."',this);> 
									<a class=lab id=orderlab".$bar2->id.">".$bar2->caption."</a>
									<select onchange=\"updateMenuLevel(this,this.options[this.selectedIndex].text,'".$bar2->id."')\"><option>".$bar2->access_level."</option>".$opt."</select>";			
							}
						//=========================================================
							     $str3="select * from ".$dbname.".menu 
						                where parent=".$bar2->id." order by urut";
								 $res3=mysql_query($str3);	
   
									 echo"<ul id=orderchild".$bar2->id." style='display:none;'>
									      <div id=ordergroup".$bar2->id.">";
									 while($bar3=mysql_fetch_object($res3))
									 {
									 if(strtolower($bar3->class)=='devider')
									   $bar3->caption="------------";							
									 if(strtolower($bar3->class)=='title' or strtolower($bar3->class)=='devider')
									 {
									   echo "<li class=mmgr><img src='images/menu/arrow_10.gif'> 
									   ".$bar3->caption;		
									 }
									 else{
										echo "<li class=mmgr><img title=Expand class=arrow src='images/foldc_.png' height=17px onclick=show_sub('orderchild".$bar3->id."',this);> 
										 	 <a class=lab id=orderlab".$bar3->id.">".$bar3->caption."</a>
						                    <select onchange=\"updateMenuLevel(this,this.options[this.selectedIndex].text,'".$bar3->id."')\"><option>".$bar3->access_level."</option>".$opt."</select>";	
									 }
										//=========================================================
										     $str4="select * from ".$dbname.".menu 
									                where parent=".$bar3->id." order by urut";
											 $res4=mysql_query($str4);	
  
												 echo"<ul id=orderchild".$bar3->id." style='display:none;'>
												      <div id=ordergroup".$bar3->id.">";
												 while($bar4=mysql_fetch_object($res4))
												 {
												 if(strtolower($bar4->class)=='devider')
												   $bar4->caption="------------";							
												  if(strtolower($bar4->class)=='title' or strtolower($bar4->class)=='devider')
												  {
												     echo "<li class=mmgr><img src='images/menu/arrow_10.gif'> 
													 ".$bar4->caption;	
												  }
												  else{
													 echo "<li class=mmgr><img title=Expand class=arrow src='images/foldc_.png' height=17px onclick=show_sub('orderchild".$bar4->id."',this);> 
														<a class=lab id=orderlab".$bar4->id.">".$bar4->caption."</a>
														<select onchange=\"updateMenuLevel(this,this.options[this.selectedIndex].text,'".$bar4->id."')\"><option>".$bar4->access_level."</option>".$opt."</select>";
												  }
												//=========================================================
													     $str5="select * from ".$dbname.".menu 
												                where parent=".$bar4->id." order by urut";
														 $res5=mysql_query($str5);	
  
															 echo"<ul id=orderchild".$bar4->id." style='display:none;'>
																  <div id=ordergroup".$bar4->id.">";
															 while($bar5=mysql_fetch_object($res5))
															 {
															 if(strtolower($bar5->class)=='devider')
															   $bar5->caption="------------";							
															  if(strtolower($bar5->class)=='title' or strtolower($bar5->class)=='devider')
															  {
															     echo "<li class=mmgr><img  src='images/menu/arrow_10.gif'> 
																 ".$bar5->caption;		
															  }
															  else{
																echo "<li class=mmgr><img class=arrow title='Expand' src='images/foldc_.png' height=17px onclick=show_sub('orderchild".$bar5->id."',this);> 
																	<a class=lab id=orderlab".$bar5->id.">".$bar5->caption."</a>
																	<select onchange=\"updateMenuLevel(this,this.options[this.selectedIndex].text,'".$bar5->id."')\"><option>".$bar5->access_level."</option>".$opt."</select>";	
															  }
															//=========================================================
																     $str6="select * from ".$dbname.".menu 
															                where parent=".$bar5->id." order by urut";
																	 $res6=mysql_query($str6);	
  
																		 echo"<ul id=orderchild".$bar5->id." style='display:none;'>
																		      <div id=ordergroup".$bar5->id.">";
																		 while($bar6=mysql_fetch_object($res6))
																		 {
																		 if(strtolower($bar6->class)=='devider')
																		   $bar6->caption="------------";							

																			echo "<li>".$bar6->caption; 
																			if($bar6->class!='devider' AND $bar6->class!='title')
																			     echo "<select onchange=\"updateMenuLevel(this,this.options[this.selectedIndex].text,'".$bar6->id."')\"><option>".$bar6->access_level."</option>".$opt."</select>";
																			echo " </li>";
																		 }
																		 echo"</div>
																		 </ul>";
																	 
															//========================================================																			
																echo "</li>";
															 }
															 echo"</div>
															  </ul>";
														 
												//========================================================																
													echo "</li>";
												 }
												 echo"</div>
													  </ul>";
											 
									//========================================================												
										echo "</li>";
									 }
									 echo"</div>
									      </ul>";
								 
						//========================================================
							echo "</li>";
						 }
						 echo"</div>
						      </ul>";
					
			//========================================================
				echo "</li>";
			 }
			 echo"</div>			 
			      </ul>";
		 
//========================================================
	echo "</li>";
}
echo "</ul></div><br>
<input type=button value='".$_SESSION['lang']['apply']."' class=mybutton onclick=window.location.reload()>
<input type=button value='".$_SESSION['lang']['close']."' class=mybutton onclick=\"hideDetailForm('ctr','ctrmenu');hideThis('lab0');\">
<br><br>";
?>
