<?php
require_once('config/connection.php');
echo "
<div id=locker style=\"background-image:url(images/menu/center_tile_skyblue.gif)\">
	<table cellpadding=0 cellspacing=0 style=\"width:100%;\">
	<tr><td>
		<div style=\"font-size:1px;width:6px;height:34px;background-image:url(images/menu/left_cap_blue.gif);\">
		</div></td><td style=\"width:100%;\">
<ul id=\"qm0\" class=\"qmmc\">";

//get menu for user by auth type or level

if($_SESSION['security']=='off')
{
	$ssq='';
}
else if($_SESSION['access_type']=='detail')
{
	$ssq=" and id in (".$_SESSION['allpriv'].")";
}
else
{
    $ssq=" and access_level <=".$_SESSION['standard']['access_level'];
}

$arrow_location="<img src='images/menu/arrow_4.gif' class=arrow>";
$str_m1="select * from ".$dbname.".menu
         where type='master' ".$ssq."
		 and hide=0 order by urut";

$res_m1=mysql_query($str_m1);
	while($bar_m1=mysql_fetch_object($res_m1))
	{
		$master_id=$bar_m1->id;
		echo"<li><a class=\"utama\" href=\"javascript:void(0)\">".strtoupper($bar_m1->caption)."</a>";
		//=======================================================
			$str_m2="select * from ".$dbname.".menu
			         where parent=".$master_id."  ".$ssq."
					  and hide=0 order by urut";
			$res_m2=mysql_query($str_m2);
			if(mysql_num_rows($res_m2)>0)
			{
				echo"<ul>";
				while($bar_m2=mysql_fetch_object($res_m2))
				{
					$master_m2=$bar_m2->id;
					if($bar_m2->class=='devider')
					  echo"<li><span class=\"qmdivider qmdividerx\" ></span></li>";
					else if($bar_m2->class=='title')
					  echo"<li><span class=\"mtitle\" >".$bar_m2->caption."</span></li>";
				    else
						{

							if($bar_m2->type=='parent')
							{
							 echo "<li><a class=\"induk\" href=\"javascript:void(0);\"><img src=images/menu/star.png style='border:0px;vertical-align:middle;height:11px'> ".$bar_m2->caption."  ".$arrow_location."</a>";
							     //===============================================
								 $str_m3="select * from ".$dbname.".menu
								         where parent=$master_m2  ".$ssq."
										  and hide=0 order by urut";
								$res_m3=mysql_query($str_m3);
								if(mysql_num_rows($res_m3)>0)
								{
									echo"<ul>";
									while($bar_m3=mysql_fetch_object($res_m3))
									{
										$master_m3=$bar_m3->id;
										if($bar_m3->class=='devider')
										  echo"<li><span class=\"qmdivider qmdividerx\" ></span></li>";
										else if($bar_m3->class=='title')
										  echo"<li><span class=\"mtitle\" >".$bar_m3->caption."</span></li>";
									    else
											{

												if($bar_m3->type=='parent')
												{
												echo "<li><a class=\"induk\" href=\"javascript:void(0);\"><img src=images/menu/star.png style='border:0px;vertical-align:middle;height:11px'> ".$bar_m3->caption."  ".$arrow_location."</a>";
												     //===============================================
													 $str_m4="select * from ".$dbname.".menu
													         where parent=$master_m3  ".$ssq."
															  and hide=0 order by urut";

													$res_m4=mysql_query($str_m4);
													if(mysql_num_rows($res_m4)>0)
													{
														echo"<ul>";
														while($bar_m4=mysql_fetch_object($res_m4))
														{
															$master_m4=$bar_m4->id;
															if($bar_m4->class=='devider')
															  echo"<li><span class=\"qmdivider qmdividerx\" ></span></li>";
															else if($bar_m4->class=='title')
															  echo"<li><span class=\"mtitle\" >".$bar_m4->caption."</span></li>";
														    else
															  {
																if($bar_m4->type=='parent')
																{
																echo "<li><a class=\"induk\" href=\"javascript:void(0);\"><img src=images/menu/star.png style='border:0px;vertical-align:middle;height:11px'> ".$bar_m4->caption."  ".$arrow_location."</a>";
																     //===============================================
																	 $str_m5="select * from ".$dbname.".menu
																	         where parent=$master_m4  ".$ssq."
																			  and hide=0 order by urut";
																	$res_m5=mysql_query($str_m5);
																	if(mysql_num_rows($res_m5)>0)
																	{
																		echo"<ul>";
																		while($bar_m5=mysql_fetch_object($res_m5))
																		{
																			$master_m5=$bar_m5->id;
																			if($bar_m5->class=='devider')
																			  echo"<li><span class=\"qmdivider qmdividerx\" ></span></li>";
																			else if($bar_m5->class=='title')
																			  echo"<li><span class=\"mtitle\" >".$bar_m5->caption."</span></li>";
																		    else
																			  {
																					if($bar_m5->type=='parent')
																					{
																					echo "<li><a class=\"induk\" href=\"javascript:void(0);\"><img src=images/menu/star.png style='border:0px;vertical-align:middle;height:11px'> ".$bar_m5->caption."  ".$arrow_location."</a>";
																					     //===============================================
																						 $str_m6="select * from ".$dbname.".menu
																						         where parent=$master_m5   ".$ssq."
																								  and hide=0 order by urut";
																						$res_m6=mysql_query($str_m6);
																						if(mysql_num_rows($res_m6)>0)
																						{
																							echo"<ul>";
																							while($bar_m6=mysql_fetch_object($res_m6))
																							{
																								$master_m6=$bar_m6->id;
																								if($bar_m6->class=='devider')
																								  echo"<li><span class=\"qmdivider qmdividerx\" ></span></li>";
																								else if($bar_m6->class=='title')
																								  echo"<li><span class=\"mtitle\" >".$bar_m6->caption."</span></li>";
																							    else
																								  {
																								  	if($bar_m6->type=='parent')
																									{
																									echo "<li><a class=\"induk\" href=\"javascript:void(0);\"><img src=images/menu/star.png style='border:0px;vertical-align:middle;height:11px'> ".$bar_m6->caption."  ".$arrow_location."</a>";
																									     //===============================================
																										 $str_m7="select * from ".$dbname.".menu
																										         where parent=$master_m6  ".$ssq."
																												  and hide=0 order by urut";
																										$res_m7=mysql_query($str_m7);
																										if(mysql_num_rows($res_m7)>0)
																										{
																											echo"<ul>";
																											while($bar_m7=mysql_fetch_object($res_m7))
																											{
																												$master_m7=$bar_m7->id;
																												if($bar_m7->class=='devider')
																												  echo"<li><span class=\"qmdivider qmdividerx\" ></span></li>";
																												else if($bar_m7->class=='title')
																												  echo"<li><span class=\"mtitle\" >".$bar_m7->caption."</span></li>";
																											    else
																												  echo "<li><a href=\"javascript:do_load('".$bar_m7->action."')\"><img src=images/menu/star.png style='border:0px;vertical-align:middle;height:11px'> ".$bar_m7->caption."</a></li>";
																											}
																											echo"</ul>";
																										}
																										 //===============================================
																									echo "</li>";
																									}
																									else
																									{
																									 echo "<li><a href=\"javascript:do_load('".$bar_m6->action."')\"><img src=images/menu/star.png style='border:0px;vertical-align:middle;height:11px'> ".$bar_m6->caption."</a></li>";
																									}
																								  }
																							}
																							echo"</ul>";
																						}
																						 //===============================================
																					echo "</li>";
																					}
																					else
																					{
																					 echo "<li><a href=\"javascript:do_load('".$bar_m5->action."')\"><img src=images/menu/star.png style='border:0px;vertical-align:middle;height:11px'> ".$bar_m5->caption."</a></li>";
																					}
																			  }

																		}
																		echo"</ul>";
																	}
																	 //===============================================
																echo "</li>";
																}
																else
																{
																 echo "<li><a href=\"javascript:do_load('".$bar_m4->action."')\"><img src=images/menu/star.png style='border:0px;vertical-align:middle;height:11px'> ".$bar_m4->caption."</a></li>";
																}

															  }

														}
														echo"</ul>";
													}
													 //===============================================
												echo "</li>";
												}
												else
												{
												 echo "<li><a href=\"javascript:do_load('".$bar_m3->action."')\"><img src=images/menu/star.png style='border:0px;vertical-align:middle;height:11px'> ".$bar_m3->caption."</a></li>";
												}

											}
									}
									echo"</ul>";
								}
								 //===============================================
							echo "</li>";

							}
							else
							{
							 echo "<li><a href=\"javascript:do_load('".$bar_m2->action."')\"><img src=images/menu/star.png style='border:0px;vertical-align:middle;height:11px'> ".$bar_m2->caption."</a></li>";
							}

						}
				}
				echo"</ul></li>";
			}
		//=========================================================

		echo"<li><span class=\"qmdivider qmdividery\"></span></li>";
	}

echo"
<li class=\"qmclear\">&nbsp;</li></ul>
<!-- Ending Page Content [menu nests within] -->
</td>
<td>
<span onclick=logout() title='Logout'class=logout>LOGOUT</span>
</td>
<td>
<td>
<div style=\"font-size:1px;width:6px;height:34px;background-image:url(images/menu/right_cap_blue.gif);\">
</div>
</td>
</tr>
</table>
</div>
<!-- Create Menu Settings: (Menu ID, Is Vertical, Show Timer, Hide Timer, On Click (options: 'all' * 'all-always-open' * 'main' * 'lev2'), Right to Left, Horizontal Subs, Flush Left, Flush Top) -->
<script type=\"text/javascript\">qm_create(0,false,0,500,false,false,false,false,false);</script>
";
?>

<div id='progress' style='display:none;border:orange solid 1px;width:150px;position:fixed;right:20px;top:65px;color:#ff0000;font-family:Tahoma;font-size:13px;font-weight:bolder;text-align:center;background-color:#FFFFFF;z-index:10000;'>
Please wait.....! <br>
<img src='images/progress.gif'>
</div>
<div id='screenlocker' style='display:none; width:100%;height:2000px;color:#666666;font-family:Tahoma;font-size:13px;font-weight:bolder;text-align:center;background-color:#FFFFFF;z-index:10000;'>
<br>
</div>