<?php
/*
 * Function makeForm
 * Fungsi untuk membuat form standard
 * I : string id, string url action tanpa .php, array of elements, form method
 * O : form dalam format HTML
 * U : $form = makeForm(str,str,arr,str);
 */
 function makeForm($id,$action,$elements=array(),$method="POST") {
	# Start Form
	$form = "<form id='".$id."' name='".$id."' method='".$method."' action='".$action.".php'>";
	
	# Show Form Elements
	foreach($elements as $el) {
		$form .= $el;
	}
	
	# End Form
	$form .= "</form>";
	
	return $form;
 }
 
 /*
  * Function genElement
  * Fungsi untuk generate list of element dalam layout tabel
  * I : array of array of elements
  * O : element dalam layout tabel
  * U : $els = genElement(arr of arr)
  */
 function genElement($elements=null,$padding='1') {
       if($elements==null){
	      return NULL;
       } else {
	      $els = "<table border='0' cellspacing='0' cellpadding='".$padding."'>";
	      $maxL = 0;
	      foreach($elements as $key=>$row1) {
		     if(count($row1)>$maxL) {
			    $maxL = count($row1);
		     }
		     $els .= "<tr>";
		     foreach($row1 as $row2) {
			    if($key==='submit' or $key==='button' or $key==='btn') {
				   $els .= "<td align='left' colspan='".$maxL."'>".$row2."</td>";
			    } else {
				   $els .= "<td align='left'>".$row2."</td>";
			    }
		     }
		     $els .= "</tr>";
	      }
	      $els .= "</table>";
	      
	      return $els;
       }
 }
 
 /*
  * Function genElTitle
  * Fungsi untuk generate list of element dalam layout tabel
  * I : array of array of elements
  * O : element dalam layout tabel
  * U : $els = genElement(arr of arr)
  */
 function genElTitle($title='Form',$elements=null,$padding='1') {
       if($elements==null){
	      return NULL;
       } else {
	      $els = "<fieldset style='float:left'><legend><b>".$title."</b></legend>";
	      $els .= "<table border='0' cellspacing='0' cellpadding='".$padding."'>";
	      $maxL = 0;
	      foreach($elements as $key=>$row1) {
		     if(count($row1)>$maxL) {
			    $maxL = count($row1);
		     }
		     $els .= "<tr>";
		     foreach($row1 as $row2) {
			    if($key==='submit' or $key==='button' or $key==='btn') {
				   $els .= "<td align='left' colspan='".$maxL."'>".$row2."</td>";
			    } else {
				   $els .= "<td align='left'>".$row2."</td>";
			    }
		     }
		     $els .= "</tr>";
	      }
	      $els .= "</table></fieldset>";
	      
	      return $els;
       }
 }
 
  /*
  * Function genElementMultiDim
  * Fungsi untuk generate list of element dalam layout tabel
  * I : array of array of elements
  * O : element dalam layout tabel
  * U : $els = genElement(arr of arr)
  */
 function genElementMultiDim($title='Form',$elements,$width=1,$height=null,$padding='1',$plain=false) {
       #======== Extract Button =============
       if(isset($elements['submit']) and $elements['submit']!=null) {
	      $button = $elements['submit'][0];
       }
       if(isset($elements['button']) and $elements['button']!=null) {
	      $button = $elements['button'][0];
       }
       if(isset($elements['btn']) and $elements['btn']!=null) {
	      $button = $elements['btn'][0];
       }
       unset($elements['submit']);
       unset($elements['button']);
       unset($elements['btn']);
       
       #========= Calculate Width-Height ============
       $numEls = count($elements);
       
       # Case 1 : h = null
       if($height==null) {
	      $height = ceil($numEls/$width);
       }
       
       # Case 2 : w*h < numEls
       while($width*$height < $numEls) {
	      $height++;
       }
       
       #========= Rearrange Array ===============
       $resEls = array();
       $w = 0; $h = 0;
       foreach($elements as $el) {
	      if($h==$height) {
		     $w++;$h=0;
	      }
	      $resEls[$h][$w] = $el;
	      $h++;
       }
       #echo "<pre>";
       #print_r($button);
       
       #=========== Create Layout ===========
       $els = "";
       if(!$plain) {
	      $els .= "<fieldset style='float:left'><legend id='title_".$title."'><b>".$title."</b></legend>";
       }
       $els .= "<div id='".$title."'><table border='0' cellspacing='0' cellpadding='".$padding."'>";
       $maxL = 0;
       foreach($resEls as $h=>$in1) {
	      $els .= "<tr>";
	      foreach($in1 as $w=>$content) {
		     foreach($content as $ni) {
			    $els .= "<td style='padding-right:20px'>".$ni."</td>";
		     }
	      }
	      $els .= "</tr>";
       }
       # Add Button
       if(isset($button)) {
	      $els .= "<tr colspan='".$width."'><td>".$button."</td></tr>";
       }
       $els .= "</table></div>";
       if(!$plain) {
	      $els .= "</fieldset>";
       }
       
       return $els;
 }
 
 /*
  * Function genFormBtn
  * Fungsi untuk generate set of button untuk form
  * I : 
  * O : standar button untuk form
  * U : $ = genFormBtn()
  */
 function genFormBtn($field,$table,$id,$page=null) {
       if($page==null) {
	      $formBtn = makeElement('add','btn',$_SESSION['lang']['save'],array('onclick'=>"addData('".$field."','".$id."','".$table."')")).
		     makeElement('edit','btn',$_SESSION['lang']['save'],array('style'=>'display:none','onclick'=>"editData('".$field."','".$id."','".$table."')")).
		     makeElement('cancel','btn',$_SESSION['lang']['cancel'],array('onclick'=>"clearData('".$field."')"));
       } else {
	      $formBtn = makeElement('add','btn',$_SESSION['lang']['save'],array('onclick'=>"addData('".$field."','".$id."','".$table."','".$page."')")).
		     makeElement('edit','btn',$_SESSION['lang']['save'],array('style'=>'display:none','onclick'=>"editData('".$field."','".$id."','".$table."')")).
		     makeElement('cancel','btn',$_SESSION['lang']['cancel'],array('onclick'=>"clearData('".$field."')"));
       }
       $formBtn .= makeElement('currRow','hidden','0');
       
       return $formBtn;
 }
 
/*
 * Function makeElement
 * Fungsi untuk membuat element form standard
 * I : string id, string element type, string value, array option, array atribut
 * O : element form dalam format HTML
 * U : $el = makeElement(str,str,str,arr,arr);
 */
 function makeElement($id,$type,$value="",$attr=array(),$options=array()) {
       # Init
       $el = "";
       
       # Type?
       switch($type) {
	      # Tipe Label
	      case 'label' :
		      $el .= "<label for='".$id."'";
		      if(is_array($attr) and $attr!=array()) {
			      foreach($attr as $key=>$row) {
				      $el .= " ".$key."=\"".$row."\"";
			      }
		      }
		      $el .= ">".$value."</label>";
		      break;
		      
	      # Tipe Text Standard
	      case 'txt' :
	      case 'text' :
		      $el .= "<input id='".$id."' name='".$id."' class='myinputtext' type='text'";
		      $el .= " value='".$value."'";
		      if(is_array($attr) and $attr!=array()) {
			      foreach($attr as $key=>$row) {
				      $el .= " ".$key."=\"".$row."\"";
			      }
		      }
		      $el .= "/>";
		      break;
	      
	      # Tipe Text Uppercase
	      case 'textupper' :
		      $el .= "<input id='".$id."' name='".$id."' class='myinputtextuppercase' type='text' value='".$value."'";
		      if(is_array($attr) and $attr!=array()) {
			      foreach($attr as $key=>$row) {
				      $el .= " ".$key."=\"".$row."\"";
			      }
		      }
		      $el .= "/>";
		      break;
		      
	      # Tipe Text Lowercase
	      case 'textlower' :
		      $el .= "<input id='".$id."' name='".$id."' class='myinputtextlowercase' type='text' value='".$value."'";
		      if(is_array($attr) and $attr!=array()) {
			      foreach($attr as $key=>$row) {
				      $el .= " ".$key."=\"".$row."\"";
			      }
		      }
		      $el .= "/>";
		      break;
	      
	      # Tipe Text Number/Numeric
	      case 'textnum' :
	      case 'textnumber' :
	      case 'textnumeric' :
		     $el .= "<input id='".$id."' name='".$id."' class='myinputtextnumber' type='text' value='".$value."'";
		     if(is_array($attr) and $attr!=array()) {
			    foreach($attr as $key=>$row) {
				   $el .= " ".$key."=\"".$row."\"";
			    }
		     }
		     $el .= "/>";
		     break;
		     
	      # Tipe Select
	      case 'select' :
	      case 'dropdown' :
		      $el .= "<select id='".$id."' name='".$id."'";
		      if(is_array($attr) and $attr!=array()) {
			      foreach($attr as $key=>$row) {
				      $el .= " ".$key."=\"".$row."\"";
			      }
		      }
		      $el .=">";
		      foreach($options as $val=>$name) {
			      if($value==$val) {
				      $el .= "<option value='".$val."' selected>".$name."</option>";
			      } else {
				      $el .= "<option value='".$val."'>".$name."</option>";
			      }
		      }
		      $el .= "</select>";
		      break;
		      
	      # Tipe Checkbox
	      case 'chk' :
	      case 'check' :
	      case 'checkbox' :
		     $el .= "<input id='".$id."' name='".$id."' type='checkbox'";
		     $el .= " value='".$value."'";
		     if(is_array($attr) and $attr!=array()) {
			    foreach($attr as $key=>$row) {
				   $el .= " ".$key."=\"".$row."\"";
			    }
		     }
		     $el .= "/>";
		     break;
		   
	      # Tipe Password
	      case 'password' :
	      case 'pwd' :
		      $el .= "<input id='".$id."' name='".$id."' type='password' value='".$value."'";
		      if(is_array($attr) and $attr!=array()) {
			      foreach($attr as $key=>$row) {
				      $el .= " ".$key."=\"".$row."\"";
			      }
		      }
		      $el .= "/>";
		      break;
	      
	      # Tipe Button
	      case 'button' :
	      case 'btn' :
		      $el .= "<button id='".$id."' name='".$id."' class='mybutton'";
		      if(is_array($attr) and $attr!=array()) {
			      foreach($attr as $key=>$row) {
				      $el .= " ".$key."=\"".$row."\"";
			      }
		      }
		      $el .= ">".$value."</button>";
		      break;
	      
	      # Tipe Submit
	      case 'submit' :
		      $el .= "<input id='".$id."' name='".$id."' type='submit' value='".$value."'";
		      if(is_array($attr) and $attr!=array()) {
			      foreach($attr as $key=>$row) {
				      $el .= " ".$key."=\"".$row."\"";
			      }
		      }
		      $el .= "/>";
		      break;
	    
	      # Tipe Submit
	      case 'hidden' :
	      case 'hid' :
		      $el .= "<input id='".$id."' name='".$id."' type='hidden' value='".$value."'";
		      if(is_array($attr) and $attr!=array()) {
			      foreach($attr as $key=>$row) {
				      $el .= " ".$key."=\"".$row."\"";
			      }
		      }
		      $el .= "/>";
		      break;
		      
	      default :
		      break;
       }
       return $el;
 }
 
/*
 * Function makeOption
 * Fungsi untuk membentuk array untuk acuan drop down field
 * I : Nama DB, Nama Table, Kolom dalam format 'kolom a, kolom b', Jenis Option
 * O : array result
 * U : $option = makeOption(str,str,str,str);
 * mode = 0 : nama => sort by nama
 * mode = 1 : nama(kode) => sort by nama
 * mode = 2 : kode - nama => sort by kode
 */
 function makeOption($dbname,$tableName,$column,$where=null,$mode='0') {
       # Get Data
       $cols = explode(',',$column);
       
       # Iterate make Option
       switch($mode) {
	      case '1':
		     $query = selectQuery($dbname,$tableName,$column,$where,$cols[1],true);
		     $data = fetchData($query);
		     $option = array();
		     foreach($data as $row) {
			    $option[$row[$cols[0]]] = $row[$cols[1]]." (".$row[$cols[0]].")";
		     }
		     break;
	      case '2':
		     $query = selectQuery($dbname,$tableName,$column,$where,$cols[0],true);
		     $data = fetchData($query);
		     $option = array();
		     foreach($data as $row) {
			    $option[$row[$cols[0]]] = $row[$cols[0]]." - ".$row[$cols[1]];
		     }
		     break;
	      case '3':
		     break;
	      default:
		     $query = selectQuery($dbname,$tableName,$column,$where,$cols[1],true);
		     $data = fetchData($query);
		     $option = array();
		     foreach($data as $row) {
			    $option[$row[$cols[0]]] = $row[$cols[1]];
		     }
		     break;
       }
       
       return $option;
 }
 
/*
 * Function optionMonth
 * Fungsi yang membentuk array month untuk acuan drop down field
 * I : kode bahasa, format (long/short)
 * O : array result
 * U : $month = optionMonth(str,str);
 */
 function optionMonth($langcode='E',$format='short') {
       # Init
       $month = array();
       
       # Iterate
       for($i=1;$i<=12;$i++) {
	      $month[$i] = numToMonth($i,$langcode,$format);
       }
       
       return $month;
 }

/*
 * Function addZero
 * Fungsi menambahkan '0' di depan angka sesuai kebutuhan
 * I : angka, jumlah maksimum
 * O : angka hasil (string)
 * U : $num = addZero(str,str);
 */
 function addZero($tmpNum,$maxNum) {
       $len = $maxNum;
       while(strlen($tmpNum)<$len) {
	      $tmpNum = '0'.$tmpNum;
       }
       
       return $tmpNum;
 }
 
/*
 * Function optionNum
 * Fungsi yang membentuk array nomor untuk acuan drop down field
 * I : jumlah element array
 * O : array result
 * U : $num = optionNum(int);
 */
 function optionNum($arrEl) {
       $strArr = strval($arrEl);
       $num = array();
       for($i=0;$i<$arrEl;$i++){
	      $tmpI = strval($i);
	      $tmpNo = addZero($tmpI,$strArr);
	      $num[$i] = $tmpNo;
       }
       
       return $num;
 }
?>