<?php
echo"<script language=javascript1.2>
	qmad=new Object();
	qmad.bvis=\"\";
	qmad.bhide=\"\";


		/*******  Menu 0 Add-On Settings *******/
		var a = qmad.qm0 = new Object();

		// Slide Animation Add On
		a.slide_animation_frames = 15;
		a.slide_accelerator = 1;
		a.slide_sub_subs_left_right = true;
		a.slide_offxy = 1;

		// Rounded Corners Add On
		a.rcorner_size = 4;
		a.rcorner_border_color = \"#E8750D\";
		a.rcorner_bg_color = \"#275370\";//\"#E8F2FE\";
		//a.rcorner_bg_color = \"#FF9933\";//\"#E8F2FE\";
		a.rcorner_apply_corners = new Array(false,true,true,true);
		a.rcorner_top_line_auto_inset = true;

		// Rounded Items Add On
		a.ritem_size = 4;
		a.ritem_apply = \"main\";
		a.ritem_main_apply_corners = new Array(true,true,false,false);
		a.ritem_show_on_actives = true;
		</script>";
?>