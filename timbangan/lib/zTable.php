<?php
/*
 * Function makeTable
 * Fungsi untuk membuat table standard
 * I : array header, array content, array footer
 * O : table dalam format HTML
 * U : $table = makeTable(arr,arr,arr,bin);
 */
 function makeTable($id,$bodyId='',$header=array(),$content=array(),$footer=array(),$sortable=false,$tr='tr',$click=null) {
       # Start Table
       if($sortable) {
	      # Sortable Table
	      $tables = "<table id='".$id."' name='".$id."' class='sortable' cellspacing='1' border='0'>";
       } else {
	      # Plain Table
	      $tables = "<table id='".$id."' name='".$id."' class='data' cellspacing='1' border='0'>";
       }
       
       # Create Header
       $tables .= "<thead><tr class='rowheader'>";
       foreach($header as $hName) {
	       $tables .= "<td>".$hName."</td>";
       }
       $tables .= "</tr></thead>";
       
       # Iterate Content
       $tables .= "<tbody id='".$bodyId."'>";
       foreach($content as $key=>$row) {
	      if($click!=null) {
		     $tables .= "<tr id='".$tr."_".$key."' class='rowcontent' onclick='".$click."'>";
	      } else {
		     $tables .= "<tr id='".$tr."_".$key."' class='rowcontent'>";
	      }
	      $i=0;
	      foreach($row as $c) {
		     $tables .= "<td id='col_".$header[$i]."_".$key."'>".$c."</td>";
		     $i++;
	      }
	      $tables .= "</tr>";
       }
       $tables .= "</tbody>";
       
       # Create Footer
       $tables .= "<tfoot>";
       foreach($footer as $fName) {
	       $tables .= "<td>".$hName."</td>";
       }
       $tables .= "</tfoot>";
       
       # End Table
       $tables .= "</table>";
       
       return $tables;
 }
 
 /*
 * Function makeCompleteTable
 * Fungsi untuk membuat table lengkap
 * I : array header, array content, array footer
 * O : table dalam format HTML
 * U : $table = makeTable(arr,arr,arr,bin);
 */
 function makeCompleteTable($id,$bodyId='',$header=array(),$content=array(),$footer=array(),$sortable=false,$tr='tr',$click=null) {
       # Start Table
       if($sortable) {
	      # Sortable Table
	      $tables = "<table id='".$id."' name='".$id."' class='sortable' cellspacing='1' border='0'>";
       } else {
	      # Plain Table
	      $tables = "<table id='".$id."' name='".$id."' class='data' cellspacing='1' border='0'>";
       }
       
       # Create Header
       $tables .= "<thead><tr class='rowheader'>";
       $field = array();
       foreach($header as $hField=>$hName) {
	       $tables .= "<td>".$hName."</td>";
	       $field[] = $hField;
       }
       $tables .= "</tr></thead>";
       
       # Iterate Content
       $tables .= "<tbody id='".$bodyId."'>";
       foreach($content as $key=>$row) {
	      if($click!=null) {
		     $tables .= "<tr id='".$tr."_".$key."' class='rowcontent' onclick='".$click."'>";
	      } else {
		     $tables .= "<tr id='".$tr."_".$key."' class='rowcontent'>";
	      }
	      $i=0;
	      foreach($row as $c) {
		     $tables .= "<td id='col_".$field[$i]."_".$key."'>".$c."</td>";
		     $i++;
	      }
	      $tables .= "</tr>";
       }
       $tables .= "</tbody>";
       
       # Create Footer
       $tables .= "<tfoot>";
       foreach($footer as $fName) {
	       $tables .= "<td>".$hName."</td>";
       }
       $tables .= "</tfoot>";
       
       # End Table
       $tables .= "</table>";
       
       return $tables;
 }
 
/*
 * Function masterTable
 * Fungsi untuk membuat table master
 * I : Nama DB, Nama Table, Kolom, Kondisi Query, Nama field pada Form, Halaman Tujuan, Setting untuk Header
 * O : table dalam format HTML
 * U : $table = masterTable(str,str,arr/str,str,arr,str,obj);
 * headerSetting[] = array(
 * 	'name'=>'string',
 * 	'align'=>'left/center/right',
 * 	'span'=>span_long
 * );
 */
 function masterTable($dbname,$table,$column="*",$headerSetting=array(),$dataSetting=array(),$cond=array(),$fForm=array(),$printTo=null,$freezeField=null,$printShow=true,$postTo=null) {
       #====================== Prep
       if($postTo==null) {
	      $postTo = 'null';
       }
       if($printTo==null) {
	      $printTo = 'null';
       }
       
       #====================== Select Query
       $query = "select ";
       # Column
       $colStr = "";
       if(is_array($column) and $column!=array()) {
	      for($i=0;$i<count($column);$i++) {
		     if($i==0) {
			    $query .= $column[$i];
			    $colStr .= $column[$i];
		     } else {
			    $query .= ",".$column[$i];
			    $colStr .= ",".$column[$i];
		     }
	      }
       } else {
	      $query .= "*";
       }
       
       # From Table
       $query .= " from ".$dbname.".".$table;
       
       # Condition
       if($cond!=null) {
	      $condStr = "";
	      if(is_array($cond)) {
		     $condPdf = $cond['sep']."^^";
		     unset($cond['sep']);
		     foreach($cond as $row) {
			    foreach($row as $attr=>$val) {
				   if($row==end($cond)) {
					  $condPdf .= $attr."**".$val;
					  if(is_string($val)) {
						 $condStr .= $attr."='".$val."'";
					  } else {
						 $condStr .= $attr."=".$val;
					  }
					  
				   } else {
					  $condPdf .= $attr."**".$val."~~";
					  if(is_string($val)) {
						 $condStr .= $attr."='".$val."' OR ";
					  } else {
						 $condStr .= $attr."=".$val." OR ";
					  }
				   }
			    }
		     }
	      } else {
		     $condPdf = $cond;
		     $condStr = $cond;
	      }
	      $query .= " where ".$condStr;
       } else {
	      $condPdf = null;
       }
       #exit;
       #======================= Execute Query
       $res=mysql_query($query);
       
       #======================= Extract Field Related
       $j = mysql_num_fields($res);
       $i = 0;
       $field = array();
       $fieldStr = "";
       $primary = array();
       $primaryStr = "";
       
       # Get Names
       while ($i < $j) {
	      $meta = mysql_fetch_field($res, $i);
	      # Get Field Name
	      $field[] = strtolower($meta->name);
	      $fieldStr .= "##".strtolower($meta->name);
	      
	      # Get Primary Key and Value
	      if($meta->primary_key=='1') {
		     $primary[] = strtolower($meta->name);
		     $primaryStr .= "##".strtolower($meta->name);
	      }
	      
	      $i++;
       }
       
       if($fForm==array()) {
	       $fForm = $field;
       }
       
       #======================= Rearrange Result and Extract Values
       $result = array();
       while($bar=mysql_fetch_assoc($res)) {
	      $result[] = $bar;
       }
       
       #======================= Create Print
       $tables = "<fieldset><legend><b>".$_SESSION['lang']['list']." : ".$table."</b></legend>";
       $tables .= "<img src='images/pdf.jpg' title='PDF Format'
	     style='width:20px;height:20px;cursor:pointer' onclick=\"masterPDF('".$table."','".$colStr."','".$condPdf."','".$printTo."',event)\">&nbsp;";
       $tables .= "<img src='images/printer.png' title='Print Page'
	     style='width:20px;height:20px;cursor:pointer' onclick='javascript:print()'>";
       
       #======================= Create Table
       # Start Table
       if($printShow) {
	     $tables .= "<div style='height:170px;overflow:auto'>";
       }
       $tables .= "<table id='masterTable' class='sortable' cellspacing='1' border='0'>";
       
       # Create Header
       $tables .= "<thead><tr class='rowheader'>";
       if($headerSetting==null) {
	      foreach($field as $hName) {
		     $tables .= "<td>".$_SESSION['lang'][$hName]."</td>";
	      }
       } else {
	      foreach($headerSetting as $hSet) {
		     if(!isset($hSet['span'])) {
			    $hSet['span'] = '0';
		     }
		     if(!isset($hSet['align'])) {
			    $hSet['align'] = 'left';
		     }
		     $tables .= "<td colspan='".$hSet['span']."' align='".$hSet['align']."'>".$hSet['name']."</td>";
	      }
       }
       
       $tables .= "<td colspan='2'></td>";
       $tables .= "</tr></thead>";
       
       # Iterate Content
       $tables .= "<tbody id='mTabBody'>";
       $i=0;
       foreach($result as $row) {
	      $tables .= "<tr id='tr_".$i."' class='rowcontent'>";
	      $tmpVal = "";
	      $tmpKey = "";
	      $j=0;
	      foreach($row as $b=>$c) {
		     # For Tipe Tanggal
		     $tmpC = explode("-",$c);
		     if(count($tmpC)==3) {
			    $c = $tmpC[2]."-".$tmpC[1]."-".$tmpC[0];
		     }
		     if(!isset($dataSetting[$b]['type'])) {
			    $dataSetting[$b]['type'] = 'default';
		     }
		     switch($dataSetting[$b]['type']) {
			    case 'numeric':
				   $tables .= "<td id='".$fForm[$j]."_".$i."' align='right'>".number_format($c,0)."</td>";
				   break;
			    case 'currency':
				   $tables .= "<td id='".$fForm[$j]."_".$i."' align='right'>".number_format($c,2)."</td>";
				   break;
			    case 'string':
				   $tables .= "<td id='".$fForm[$j]."_".$i."' align='left'>".$c."</td>";
				   break;
			    default:
				   $tables .= "<td id='".$fForm[$j]."_".$i."'>".$c."</td>";
				   break;
		     }
		     $tmpVal .= "##".$c;
		     if(in_array($fForm[$j],$primary)) {
			    $tmpKey .= "##".$c;
		     }
		     $j++;
	      }
	      # Edit, Delete Row
	      if($freezeField!=null) {
		     $tables .= "<td><img id='editRow".$i."' title='Edit' onclick=\"editRow(".$i.",'".$fieldStr."','".$tmpVal."','".$freezeField."')\"
		     class='zImgBtn' src='images/001_45.png' /></td>";
	      } else {
		     $tables .= "<td><img id='editRow".$i."' title='Edit' onclick=\"editRow(".$i.",'".$fieldStr."','".$tmpVal."')\"
		     class='zImgBtn' src='images/001_45.png' /></td>";
	      }
	      if($postTo=='null') {
		     $tables .= "<td><img id='delRow".$i."' title='Hapus' onclick=\"delRow(".$i.",'".$primaryStr."','".$tmpKey."',null,'".$table."')\"
			    class='zImgBtn' src='images/delete_32.png' /></td>";
	      } else {
		     $tables .= "<td><img id='delRow".$i."' title='Hapus' onclick=\"delRow(".$i.",'".$primaryStr."','".$tmpKey."','".$postTo."','".$table."')\"
			    class='zImgBtn' src='images/delete_32.png' /></td>";
	      }
	      $tables .= "</tr>";
	      $i++;
       }
       $tables .= "</tbody>";
       
       # Create Footer
       $tables .= "<tfoot>";
       #foreach($footer as $fName) {
       #	$tables .= "<td>".$hName."</td>";
       #}
       $tables .= "</tfoot>";
       
       # End Table
       $tables .= "</table>";
       if($printShow) {
	     $tables .= "</div>";
       }
       $tables .= "</fieldset>";
       
       return $tables;
 }
?>