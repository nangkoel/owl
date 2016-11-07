<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/main_privilage_by_table.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX();
echo OPEN_THEME($_SESSION['lang']['menusettings'].":");
//=================================================================================
//menuSettings

//==================================================================================================================================================================
 echo"<div id=menuOrderContainer style='position:relative;display:block'>
	 <hr><b>Assign a menu for users:</b>:
	";
echo"<ul>
     <a  class=lab id=orderlab0 href=# onclick=showEditor('0','false',event) title='Click to arrange master menu (the top most menu)'>".$_SESSION['lang']['mastermenu']."</a>
     <div id=ordergroup0>";
$str="select * from ".$dbname.".menu 
      where type='master' order by urut";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	echo "<li class=mmgr><img title=expand class=arrow src='images/foldc_.png' height=17px   onclick=show_sub('orderchild".$bar->id."',this);>
	<a class=lab  title='Click to show this submenu order editor' id=orderlab".$bar->id." onclick=showEditor('".$bar->id."','true',event)>".$bar->caption."</a>";
//=========================================================
	     $str1="select * from ".$dbname.".menu 
                where hide=0 and parent=".$bar->id." order by urut";
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
				else{
				   echo "<li class=mmgr><img title=Expand class=arrow src='images/foldc_.png' height=17px   onclick=show_sub('orderchild".$bar1->id."',this);> 
				   <a class=lab title='Click to show this submenu order editor' id=orderlab".$bar1->id." onclick=showEditor('".$bar1->id."','true',event)>".$bar1->caption."</a>";			
				}
			//=========================================================
				     $str2="select * from ".$dbname.".menu 
			                where hide=0 and parent=".$bar1->id." order by urut";
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
								echo "<li class=mmgr><img title=Expand class=arrow src='images/foldc_.png' height=17px  onclick=show_sub('orderchild".$bar2->id."',this);> 
								 <a class=lab title='Click to show this submenu order editor' id=orderlab".$bar2->id." onclick=showEditor('".$bar2->id."','true',event)>".$bar2->caption."</a>";			
							}
						//=========================================================
							     $str3="select * from ".$dbname.".menu 
						                where hide=0 and parent=".$bar2->id." order by urut";
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
										echo "<li class=mmgr><img title=Expand class=arrow src='images/foldc_.png' height=17px  onclick=show_sub('orderchild".$bar3->id."',this);> 
										 <a class=lab title='Click to show this submenu order editor' id=orderlab".$bar3->id." onclick=showEditor('".$bar3->id."','true',event)>".$bar3->caption."</a>";	
									 }
										//=========================================================
										     $str4="select * from ".$dbname.".menu 
									                where hide=0 and parent=".$bar3->id." order by urut";
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
													 echo "<li class=mmgr><img title=Expand class=arrow src='images/foldc_.png' height=17px  onclick=show_sub('orderchild".$bar4->id."',this);> 
													  <a class=lab title='Click to show this submenu order editor' id=orderlab".$bar4->id." onclick=showEditor('".$bar4->id."','true',event)>".$bar4->caption."</a>";
												  }
												//=========================================================
													     $str5="select * from ".$dbname.".menu 
												                where hide=0 and parent=".$bar4->id." order by urut";
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
																echo "<li class=mmgr><img class=arrow title='Expand' src='images/foldc_.png' height=17px  onclick=show_sub('orderchild".$bar5->id."',this);> 
																   <a class=lab title='Click to show this submenu order editor' id=orderlab".$bar5->id." onclick=showEditor('".$bar5->id."','true',event)>".$bar5->caption."</a>";	
															  }
															//=========================================================
																     $str6="select * from ".$dbname.".menu 
															                where hide=0 and parent=".$bar5->id." order by urut";
																	 $res6=mysql_query($str6);	
  
																		 echo"<ul id=orderchild".$bar5->id." style='display:none;'>
																		      <div id=ordergroup".$bar5->id.">";
																		 while($bar6=mysql_fetch_object($res6))
																		 {
																		 if(strtolower($bar6->class)=='devider')
																		   $bar6->caption="------------";							

																			echo "<li>".$bar6->caption."</li>";
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
echo "</ul></div>";	
echo"</div>";

//*****************************
//menu order editor
echo"<div id=ordereditor style='display:none;position:absolute;'>";
echo OPEN_THEME('Choose user:');
  echo"<div id=ordereditorcontent></div>";  
echo CLOSE_THEME();
echo"</div>";

//end menuOrder
//==================================================================================================================================================================

echo CLOSE_THEME();
CLOSE_BOX();
echo close_body();
?>
