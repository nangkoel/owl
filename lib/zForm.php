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
			    }else if ($key==='btn2'){
					if($tempky!=$key){
						$tempky=$key;
						$areto=1;
						$rowbr="";
					}else{
						$areto+=1;
					}
					if($areto<=$maxL){
						$rowbr.=$row2;
					}
					if($areto==$maxL){
						$els .= "<td align='left' colspan='".$maxL."'>".$rowbr."</td>";
					}
			    }else {
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
 function genElementMultiDim($title='Form',$elements,$width=1,$height=null,$padding='1',$plain=false,$note=null) {
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
	      $els .= "<fieldset style='float:left'><legend id='title_Form'><b>".$title."</b></legend>";
       }
       $els .= "<div id='".$title."'><table border='0' cellspacing='0' cellpadding='".$padding."'>";
       $maxL = 0;
       foreach($resEls as $h=>$in1) {
	      $els .= "<tr>";
	      foreach($in1 as $w=>$content) {
		     foreach($content as $ni) {
			    $els .= "<td style='padding-right:20px;font-size:12px'>".$ni."</td>";
		     }
	      }
	      $els .= "</tr>";
       }
       # Add Button
       if(isset($button)) {
	      $els .= "<tr><td colspan='".($width*2)."'>".$button."</td></tr>";
       }
       $els .= "</table></div>";
       if(!$plain) {
	      $els .= "</fieldset>";
       }
       if ($note!=null){
           $els .= $note;
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
 function genFormBtn($field,$table,$id,$page=null,$freeze=null,$empty=null,$pageEdit=null,$emptyField='##',$disabled='##',$opt='{}') {
       if($empty==null) {
	      $empty = ",false";
       } else {
	      $empty = ",true";
       }
       if($page==null) {
	      if($freeze==null) {
		     if($pageEdit==null) {
			    $formBtn = makeElement('add','btn',$_SESSION['lang']['save'],array('onclick'=>"addData('".$field."','".$id."','".$table."',null,null".$empty.",'".$emptyField."','".$opt."')")).
				   makeElement('edit','btn',$_SESSION['lang']['save'],array('style'=>'display:none','onclick'=>"editData('".$field."','".$id."','".$table."',null,'".$opt."')")).
				   makeElement('cancel','btn',$_SESSION['lang']['cancel'],array('onclick'=>"clearData('".$field."','".$disabled."')"));
		     } else {
			    $formBtn = makeElement('add','btn',$_SESSION['lang']['save'],array('onclick'=>"addData('".$field."','".$id."','".$table."',null,null".$empty.",'".$emptyField."','".$opt."')")).
				   makeElement('edit','btn',$_SESSION['lang']['save'],array('style'=>'display:none','onclick'=>"editData('".$field."','".$id."','".$table."','".$pageEdit."','".$opt."')")).
				   makeElement('cancel','btn',$_SESSION['lang']['cancel'],array('onclick'=>"clearData('".$field."','".$disabled."')"));
		     }
	      } else {
		     if($pageEdit==null) {
			    $formBtn = makeElement('add','btn',$_SESSION['lang']['save'],array('onclick'=>"addData('".$field."','".$id."','".$table."',null,'".$freeze."'".$empty.",'".$emptyField."','".$opt."')")).
				   makeElement('edit','btn',$_SESSION['lang']['save'],array('style'=>'display:none','onclick'=>"editData('".$field."','".$id."','".$table."',null,'".$opt."')")).
				   makeElement('cancel','btn',$_SESSION['lang']['cancel'],array('onclick'=>"clearData('".$field."','".$disabled."')"));
		     } else {
			    $formBtn = makeElement('add','btn',$_SESSION['lang']['save'],array('onclick'=>"addData('".$field."','".$id."','".$table."',null,'".$freeze."'".$empty.",'".$emptyField."','".$opt."')")).
				   makeElement('edit','btn',$_SESSION['lang']['save'],array('style'=>'display:none','onclick'=>"editData('".$field."','".$id."','".$table."','".$pageEdit."','".$opt."')")).
				   makeElement('cancel','btn',$_SESSION['lang']['cancel'],array('onclick'=>"clearData('".$field."','".$disabled."')"));
		     }
	      }
       } else {
	      if($freeze==null) {
		     if($pageEdit==null) {
			    $formBtn = makeElement('add','btn',$_SESSION['lang']['save'],array('onclick'=>"addData('".$field."','".$id."','".$table."','".$page."',null".$empty.",'".$emptyField."','".$opt."')")).
				   makeElement('edit','btn',$_SESSION['lang']['save'],array('style'=>'display:none','onclick'=>"editData('".$field."','".$id."','".$table."',null,'".$opt."')")).
				   makeElement('cancel','btn',$_SESSION['lang']['cancel'],array('onclick'=>"clearData('".$field."','".$disabled."')"));
		     } else {
			    $formBtn = makeElement('add','btn',$_SESSION['lang']['save'],array('onclick'=>"addData('".$field."','".$id."','".$table."','".$page."',null".$empty.",'".$emptyField."','".$opt."')")).
				   makeElement('edit','btn',$_SESSION['lang']['save'],array('style'=>'display:none','onclick'=>"editData('".$field."','".$id."','".$table."','".$pageEdit."','".$opt."')")).
				   makeElement('cancel','btn',$_SESSION['lang']['cancel'],array('onclick'=>"clearData('".$field."','".$disabled."')"));
		     }
	      } else {
		     if($pageEdit==null) {
			    $formBtn = makeElement('add','btn',$_SESSION['lang']['save'],array('onclick'=>"addData('".$field."','".$id."','".$table."','".$page."','".$freeze."'".$empty.",'".$emptyField."','".$opt."')")).
				   makeElement('edit','btn',$_SESSION['lang']['save'],array('style'=>'display:none','onclick'=>"editData('".$field."','".$id."','".$table."',null,'".$opt."')")).
				   makeElement('cancel','btn',$_SESSION['lang']['cancel'],array('onclick'=>"clearData('".$field."','".$disabled."')"));
		     } else {
			    $formBtn = makeElement('add','btn',$_SESSION['lang']['save'],array('onclick'=>"addData('".$field."','".$id."','".$table."','".$page."','".$freeze."'".$empty.",'".$emptyField."','".$opt."')")).
				   makeElement('edit','btn',$_SESSION['lang']['save'],array('style'=>'display:none','onclick'=>"editData('".$field."','".$id."','".$table."','".$pageEdit."','".$opt."')")).
				   makeElement('cancel','btn',$_SESSION['lang']['cancel'],array('onclick'=>"clearData('".$field."','".$disabled."')"));
		     }
	      }
       }
       $formBtn .= makeElement('currRow','hidden','0');
       
       return $formBtn;
 }
 
/*
 * Function makeElement
 * Fungsi untuk membuat element form standard
 * @param	string	$id				ID of Element
 * @param	string	$type			Type of Element
 * @param	string	$value			Default Value of the Element
 * @param	array	$attr			List of Element Attributes
 * @param	array	$options		For Select as List Option, For Text as Datalist reference
 * @param	string	$nameValue		Text Value of Additional Element (Satuan, Nama Barang, Nama Supplier, etc)
 * @param	string	$targetSatuan	ID of Satuan Element for Inventory Element
 * @param	string	$targetHarga	ID of Harga Element for Inventory Element
 * @param	string	$parentEl		ID of Parent of Element (use if parent id use for reference)
 * @return 	HTML					Element Form in HTML format
 */
function makeElement($id,$type,$value="",$attr=array(),$options=array(),$nameValue=null,$targetSatuan=null,$targetHarga=null,$parentEl=null) {
	# Init
	$el = "";
	is_null($targetSatuan) ? $targetSatuan='' : null;
	is_null($targetHarga) ? $targetHarga='' : null;
       
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
		      
	      # Tipe Date
	      case 'tanggal' :
	      case 'date' :
		      $el .= "<input id='".$id."' name='".$id."' class='myinputtext' type='text'";
		      $el .= " onmousemove='setCalendar(this.id)' readonly='readonly' value='".$value."'";
		      if(is_array($attr) and $attr!=array()) {
			      foreach($attr as $key=>$row) {
				      $el .= " ".$key."=\"".$row."\"";
			      }
		      }
		      $el .= " style='cursor:pointer' />";
		      break;
		     
	      # Tipe Range Period
	      case 'rangedate' :
	      case 'period' :
	      case 'periode' :
		      $el .= "<input id='".$id."_from' name='".$id."_from' class='myinputtext' type='text'";
		      $el .= " onmousemove='setCalendar(this.id)' readonly='readonly' value='".$value."'";
		      if(is_array($attr) and $attr!=array()) {
			      foreach($attr as $key=>$row) {
				      $el .= " ".$key."=\"".$row."\"";
			      }
		      }
		      $el .= " style='cursor:pointer' />";
		      $el .= " s/d ";
		      $el .= "<input id='".$id."_until' name='".$id."_until' class='myinputtext' type='text'";
		      $el .= " onmousemove='setCalendar(this.id)' readonly='readonly' value='".$value."'";
		      if(is_array($attr) and $attr!=array()) {
			      foreach($attr as $key=>$row) {
				      $el .= " ".$key."=\"".$row."\"";
			      }
		      }
		      $el .= " style='cursor:pointer' />";
		      break;
		     
	    # Tipe Text Standard
	    case 'txt' :
	    case 'text' :
		    $el .= "<input id='".$id."' name='".$id."' class='myinputtext' type='text'";
			if(!empty($options)) {
				$el .= " list='".$id."_list'";
			}
                    $onkeypress=false;
		    if(is_array($attr) and $attr!=array()) {
				foreach($attr as $key=>$row) {
                                    if ($key=="onkeypress") $onkeypress=true;
				    $el .= " ".$key."=\"".$row."\"";
			    }
		    }
                    if (!$onkeypress){
                        $el .= " onkeypress='return tanpa_kutip(event)' value='".$value."'";
                    }
		    $el .= "/>";
			if(!empty($options)) {
				$el .= "<datalist id='".$id."_list'>";
				foreach($options as $val=>$name) {
					$el .= "<option value='".$name."'>";
				}
				$el .= "</datalist>";
			}
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
		     $el .= "<input id='".$id."' name='".$id."' class='myinputtextnumber' ";
		     $el .= "onkeypress='return angka_doang(event)' type='text' value='".$value."'";
		     if(is_array($attr) and $attr!=array()) {
			    foreach($attr as $key=>$row) {
				   $el .= " ".$key."=\"".$row."\"";
			    }
		     }
		     $el .= "/>";
		     break;
	      # Tipe Text Number/Numeric wint minus sign for spesific cash/bank transaktion
	      case 'textnumw-' :
		     $el .= "<input id='".$id."' name='".$id."' class='myinputtextnumber' ";
		     $el .= "onkeypress='return tanpa_kutip_dan_sepasi(event)' type='text' value='".$value."'";
		     if(is_array($attr) and $attr!=array()) {
			    foreach($attr as $key=>$row) {
				   $el .= " ".$key."=\"".$row."\"";
			    }
		     }
		     $el .= "/>";
		     break;
                     
	      
	      # Tipe Text Number/Numeric dengan Satuan(Span)
	      case 'textnumwsatuan' :
		     $el .= "<input id='".$id."' name='".$id."' class='myinputtextnumber' ";
		     $el .= "onkeypress='return angka_doang(event)' type='text' value='".$value."'";
		     if(is_array($attr) and $attr!=array()) {
			    foreach($attr as $key=>$row) {
				   $el .= " ".$key."=\"".$row."\"";
			    }
		     }
		     $el .= "/>&nbsp;";
		     $el .= "<input id='".$id."_satuan' name='".$id."_satuan' class='myinputtext' type='text'";
		     $el .= " disabled='disabled' value='".$nameValue."' style='width:30px'";
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
		
		# Tipe Select with End Label
		case 'selectlabel' :
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
			$el .= "&nbsp;".$nameValue;
			break;
		
		# Tipe Select with search
		case 'selectsearch' :
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
			$el .= "<img id='".$id."_find' onclick='z.elSearch(\"".$id."\",event";
			if(!is_null($parentEl)) {
				$el .= ',"'.$parentEl.'"';
			}
			$el .= ")' ";
			$el .= "class=zImgBtn src='images/onebit_02.png' style='position:relative;top:5px'>";
			break;
		
	      # Tipe Debet Kredit
	      case 'dk' :
		     $el .= "<select id='".$id."_dk' name='".$id."_dk' style='width:70px'>";
		     $el .= "<option value='D' ";
			 if($value>=0) {$el .= "selected";}
			 $el .= ">".$_SESSION['lang']['debet']."</option>";
		     $el .= "<option value='K' ";
			 if($value<0) {$el .= "selected";}
			 $el .= ">".$_SESSION['lang']['kredit']."</option>";
		     $el .= "</select>";
		     $el .= "<input id='".$id."_nilai' name='".$id."_nilai' class='myinputtextnumber' ";
		     $el .= "onkeypress='return angka_doang(event)' type='text' value='".abs($value)."'";
		     if(is_array($attr) and $attr!=array()) {
			    foreach($attr as $key=>$row) {
				   $el .= " ".$key."=\"".$row."\"";
			    }
		     }
		     $el .= "/>";
		     break;
		     
	      # Tipe Checkbox
	      case 'chk' :
	      case 'check' :
	      case 'checkbox' :
		     $el .= "<input id='".$id."' name='".$id."' type='checkbox'";
		     $el .= " value='".$value."'";
                     if($value==1) $el .= " checked";
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
	    
	      # Tipe Hidden
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
	      
	      # Tipe Search Barang
	      case 'searchBarang':
		     $el .= "<input id='".$id."' name='".$id."' class='myinputtext' ";
		     $el .= "type='text' style='width:50px' value='".$value."' disabled />&nbsp;";
		     $el .= "<input id='".$id."_name' name='".$id."_name' class='myinputtext' ";
		     $el .= "value='".$nameValue."' type='text' style='width:150px' disabled />";
		     $el .= "<button id='".$id."' name='".$id."' class='mybutton' ";
		     if(isset($attr['disabled']) and $attr['disabled']=='disabled') {
			    $el .= "disabled ";
		     }
		     $el .= "onclick=\"getInvName(event,'".$id."','".$targetSatuan."','".$targetHarga."')\"/>".$_SESSION['lang']['find']."</button>";
		     break;
	      
	      # Tipe Search Kegiatan
	      case 'searchKegiatan':
	      case 'searchKeg':
		     $el .= "<input id='".$id."' name='".$id."' class='myinputtext' ";
		     $el .= "type='text' style='width:50px' value='".$value."' disabled />&nbsp;";
		     $el .= "<input id='".$id."_name' name='".$id."_name' class='myinputtext' ";
		     $el .= "value='".$nameValue."' type='text' style='width:150px' disabled />";
		     $el .= "<button id='".$id."' name='".$id."' class='mybutton' ";
		     if(isset($attr['disabled']) and $attr['disabled']=='disabled') {
			    $el .= "disabled ";
		     }
		     $el .= "onclick=\"getSearch(event,'".$id."','kegiatan')\"/>".$_SESSION['lang']['find']."</button>";
		     break;
	      
	      # Tipe Search Asset
	      case 'searchAsset':
		     $el .= "<input id='".$id."' name='".$id."' class='myinputtext' ";
		     $el .= "type='text' style='width:50px' value='".$value."' disabled />&nbsp;";
		     $el .= "<input id='".$id."_name' name='".$id."_name' class='myinputtext' ";
		     $el .= "value='".$nameValue."' type='text' style='width:150px' disabled />";
		     $el .= "<button id='".$id."' name='".$id."' class='mybutton' ";
		     if(isset($attr['disabled']) and $attr['disabled']=='disabled') {
			    $el .= "disabled ";
		     }
		     $el .= "onclick=\"getSearch(event,'".$id."','asset')\"/>".$_SESSION['lang']['find']."</button>";
		     break;
	      
	      # Tipe Search Customer
	      case 'searchCustomer':
	      case 'searchCust':
		     $el .= "<input id='".$id."' name='".$id."' class='myinputtext' ";
		     $el .= "type='text' style='width:50px' value='".$value."' disabled />&nbsp;";
		     $el .= "<input id='".$id."_name' name='".$id."_name' class='myinputtext' ";
		     $el .= "value='".$nameValue."' type='text' style='width:150px' disabled />";
		     $el .= "<button id='".$id."' name='".$id."' class='mybutton' ";
		     if(isset($attr['disabled']) and $attr['disabled']=='disabled') {
			    $el .= "disabled ";
		     }
		     $el .= "onclick=\"getSearch(event,'".$id."','customer')\"/>".$_SESSION['lang']['find']."</button>";
		     break;
	      
	      # Tipe Search Supplier
	      case 'searchSupplier':
	      case 'searchSupl':
		     $el .= "<input id='".$id."' name='".$id."' class='myinputtext' ";
		     $el .= "type='text' style='width:50px' value='".$value."' disabled />&nbsp;";
		     $el .= "<input id='".$id."_name' name='".$id."_name' class='myinputtext' ";
		     $el .= "value='".$nameValue."' type='text' style='width:150px' disabled />";
		     $el .= "<button id='".$id."' name='".$id."' class='mybutton' ";
		     if(isset($attr['disabled']) and $attr['disabled']=='disabled') {
			    $el .= "disabled ";
		     }
		     $el .= "onclick=\"getSearch(event,'".$id."','supplier')\"/>".$_SESSION['lang']['find']."</button>";
		     break;
	      case 'jammenit':
		     $optJam = array();$optMenit = array();
		     $tmpVal = explode(":",$value);
		     $valueJam = $tmpVal[0];
		     if(count($tmpVal)>1) {
			    $valueMenit = $tmpVal[1];
		     } else {
			    $valueMenit = "00";
		     }
		     for($i=0;$i<60;$i++) {
			    if($i<24) {
				   $optJam[addZero($i,2)] = addZero($i,2);
			    }
			    $optMenit[addZero($i,2)] = addZero($i,2);
		     }
		     # Jam
		     $el .= "<select id='".$id."_jam' name='".$id."'_jam";
		     $el .=">";
		     foreach($optJam as $val) {
			    if($valueJam==$val) {
				   $el .= "<option value='".$val."' selected>".$val."</option>";
			    } else {
				   $el .= "<option value='".$val."'>".$val."</option>";
			    }
		     }
		     $el .= "</select>";
		     $el .= ":";
		     # Menit
		     $el .= "<select id='".$id."_menit' name='".$id."'_menit";
		     $el .=">";
		     foreach($optMenit as $val) {
			    if($valueMenit==$val) {
				   $el .= "<option value='".$val."' selected>".$val."</option>";
			    } else {
				   $el .= "<option value='".$val."'>".$val."</option>";
			    }
		     }
		     $el .= "</select>";
		     break;
	      case 'bulantahun':
		     # Make Bulan
		     $optBulan = array();
		     for($i=1;$i<13;$i++) {
			    $optBulan[$i] = numToMonth($i,substr($_SESSION['language'],0,1),'long');
		     }
		     
		     # Value
		     $tmpVal = explode("-",$value);
		     $valueBulan = $tmpVal[0];
		     if(count($tmpVal)>1) {
			    $valueTahun = $tmpVal[1];
		     } else {
			    $valueTahun = date('Y');
		     }
		     
		     # Bulan
		     $el .= "<select id='".$id."_bulan' name='".$id."'_bulan";
		     $el .=">";
		     foreach($optBulan as $val=>$text) {
			    if($valueBulan==$val) {
				   $el .= "<option value='".$val."' selected>".$text."</option>";
			    } else {
				   $el .= "<option value='".$val."'>".$text."</option>";
			    }
		     }
		     $el .= "</select>";
		     
		     # Tahun
		     $el .= "<input id='".$id."_tahun' name='".$id."_tahun' class='myinputtextnumber' ";
		     $el .= "onkeypress='return angka_doang(event)' type='text' value='".$valueTahun."' ";
		     $el .= "maxlength='4' style='width:40px'";
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
 function makeOption($dbname,$tableName,$column,$where=null,$mode='0',$empty=false) {
       # Get Data
       $cols = explode(',',$column);
       
       # Init Options
       if($empty==true) {
	      $option = array(''=>'');
       } else {
	      $option = array();
       }
       
       # Iterate make Option
       switch($mode) {
	      case '1':
		     $query = selectQuery($dbname,$tableName,$column,$where,$cols[1],true);
		     $data = fetchData($query);
		     foreach($data as $row) {
			    $option[$row[$cols[0]]] = $row[$cols[1]]." (".$row[$cols[0]].")";
		     }
		     break;
	      case '2':
		     $query = selectQuery($dbname,$tableName,$column,$where,$cols[0],true);
		     $data = fetchData($query);
		     foreach($data as $row) {
				if(isset($cols[2])) {
					$option[$row[$cols[0]]] = $row[$cols[0]]." ".$row[$cols[1]]." - ".$row[$cols[2]];
				} else {
					$option[$row[$cols[0]]] = $row[$cols[0]]." ".$row[$cols[1]];
				}
				/*echo "<pre>";
				print_r($option);
				echo "</pre>";*/
		     }
		     break;
	      case '3':
		     foreach($column as $row=>$isi) {
			    $option[$row] = $isi;
		     }
		     break;
	      case '4':
		     $query = selectQuery($dbname,$tableName,$column,$where,$cols[1],true);
                     $data = fetchData($query);
		     foreach($data as $row) {
			    $option[$row[$cols[0]]] = $row[$cols[1]]." (".$row[$cols[2]].")";
		     }
		     break;
	      case '5':
		     $query = selectQuery($dbname,$tableName,$column,$where,$cols[1],true);
                     $data = fetchData($query);
		     foreach($data as $row) {
			    $option[$row[$cols[0]]] = $row[$cols[1]]." - ".$row[$cols[2]];
		     }
		     break;
              case '6':
		     $query = selectQuery($dbname,$tableName,$column,$where,$cols[3],true);
                     $data = fetchData($query);
		     foreach($data as $row) {
			    $option[$row[$cols[0]]] = $row[$cols[3]]." - ".$row[$cols[1]]." (".$row[$cols[2]].")";//kdblok - kdblok
		     }
		     break;
			 
			case '8':
			
			$namaOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
			if(isset($cols[2])) {
				$order = $cols[2];
			} else {
				$order = $cols[1];
			}
			
		     $query = selectQuery($dbname,$tableName,$column,$where,$order,true);
             $data = fetchData($query);
		     foreach($data as $row) {
				if(isset($cols[2])) {
					$option[$row[$cols[0]]] = $row[$cols[0]]." - ".$row[$cols[1]]." (".$namaOrg[$row[$cols[2]]].")";//kdblok = kdblok->kdbloklama->nama
				} else {
					$option[$row[$cols[0]]] = $row[$cols[0]]." - ".$row[$cols[1]];//kdblok = kdblok->kdbloklama->nama
				}
		     }
		     break;
			 
			case '9':
			
			$namaOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaalias');
			if(isset($cols[2])) {
				$order = $cols[2];
			} else {
				$order = $cols[1];
			}
			
		     $query = selectQuery($dbname,$tableName,$column,$where,$order,true);
             $data = fetchData($query);
		     foreach($data as $row) {
				if(isset($cols[2])) {
					$option[$row[$cols[0]]] = $row[$cols[0]]." - ".$row[$cols[1]]." (".$namaOrg[$row[$cols[2]]].")";//kdblok = kdblok->kdbloklama->nama
				} else {
					$option[$row[$cols[0]]] = $row[$cols[0]]." - ".$row[$cols[1]];//kdblok = kdblok->kdbloklama->nama
				}
		     }
		     break;
			 
              case'7':
                     $query = " SHOW COLUMNS FROM `".$dbname."`.`".$tableName."` LIKE '".$cols[0]."' ";
                  //exit("error:\n".$query);
                       $result = mysql_query( $query ) or die( 'error getting enum field ' . mysql_error() );
                       $row = mysql_fetch_array( $result , MYSQL_NUM );
                       #extract the values
                       #the values are enclosed in single quotes
                       #and separated by commas
                       $regex = "/'(.*?)'/";
                       preg_match_all( $regex , $row[1], $enum_array );
                       $enum_fields = array();
                       foreach($enum_array[1] as $row) {
                              $enum_fields[$row] = $row;
                       }
                       
                        foreach($enum_fields as $row){
                               $option[$row] = $row;
                        }  
                     break;
                     
                     
              default:
		     $query = selectQuery($dbname,$tableName,$column,$where,$cols[1],true);
		     $data = fetchData($query);
		     foreach($data as $row) {
			    $option[$row[$cols[0]]] = $row[$cols[1]];
		     }
		     break;
       }
       return $option;
 }
 
 /*
 * Function makeOptionAkun
 * Fungsi untuk membentuk array untuk acuan drop down field akun
 * I : Nama DB, Kondisi, isEmpty
 * O : array result
 * U : $option = makeOption(str,str,str,str);
 */
 function makeOptionAkun($dbname,$where=null,$empty=false) {
       # Init Options
       if($empty==true) {
	      $option = array(''=>'');
       } else {
	      $option = array();
       }
       
       # Get Kelompok
       $optKel = makeOption($dbname,'keu_5akun','noakun,namaakun',"length(trim(noakun))=3");
       
       if(!is_null($where)) {
	      $where = "detail=1 and ".$where;
       } else {
	      $where = "detail=1";
       }
       $query = selectQuery($dbname,'keu_5akun','noakun,namaakun',$where,'noakun',true);
       $data = fetchData($query);
       foreach($data as $row) {
	      if(isset($optKel[substr($row['noakun'],0,3)])) {
		     $option[$row['noakun']] = $row['noakun']." - ".$row['namaakun'].
			    " (".$optKel[substr($row['noakun'],0,3)].")";
	      }
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
       $len = strlen($arrEl);
       $strArr = $len;
       $num = array();
       for($i=0;$i<$arrEl;$i++){
	      $tmpI = strval($i);
	      $tmpNo = addZero($tmpI,$strArr);
	      $num[$i] = $tmpNo;
       }
       
       return $num;
 }
 
 /*
 * Function makeFieldset
 * Fungsi untuk membentuk fieldset
 * I : judul, id div,content, bold title (T/F)
 * O : fieldset
 * U : echo makeFieldset($title,$content,true);
 */
 function makeFieldset($title,$id,$cont=null,$bold=null) {
       $fs = "<fieldset>";
       $fs .= "<legend>";
       !is_null($bold) ? $fs .= "<b>" : null;
       $fs .= $title;
       !is_null($bold) ? $fs .= "</b>" : null;
       $fs .= "</legend>";
       $fs .= "<div id='".$id."'>";
       $fs .= $cont;
       $fs .= "</div></fieldset>";
       
       return $fs;
 }
 
 /*
 * Function getFirstKey
 * Fungsi mengambil key pertama dari array
 * I : array
 * O : key of array
 */
 function getFirstKey($arr) {
       return end(array_reverse(array_keys($arr)));
 }
 
 /*
 * Function getFirstContent
 * Fungsi mengambil content pertama dari array
 * I : array
 * O : content of array
 */
 function getFirstContent($arr) {
       return end(array_reverse(array_values($arr)));
 }
?>