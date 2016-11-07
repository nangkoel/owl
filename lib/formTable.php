<?php
include_once('lib/zForm.php');
include_once('lib/uElement.php');

/* class uForm
 * Kelas Form untuk layout form-table
 */
class uForm {
    public $_id;
    public $_name;
    public $_elements;
    public $_width;
    
    #== Constructor
    function uForm($cId,$cName,$cWidth=null,$cEls=null) {
	$this->_id = $cId;
	$this->_name = $cName;
	is_null($cEls) ? $this->_elements=array() : $this->_elements=$cEls;
	is_null($cWidth) ? $this->_width=1 : $this->_width=$cWidth;
    }
    
    /*
     */
    function addEls($cId,$cName,$cCont=null,$cType=null,$cAlign=null,$cLength=null,$cRefer=null,$cCont2=null,$cTSatuan=null,$cTHarga=null,$cParent=null) {
	$this->_elements[] = new uElement($cId,$cName,$cCont,$cType,$cAlign,$cLength,$cRefer,$cCont2,$cTSatuan,$cTHarga,$cParent);
    }
}

/* class uTable
 * Kelas Table untuk layout form-table
 */
class uTable {
    public $_id;
    public $_name;
    public $_data;
    public $_dataShow;
    
    function uTable($cId,$cName,$cCols=null,$cData=null,$cDataShow=null) {
	$this->_id = $cId;
	$this->_name = $cName;
	is_null($cData) ? $this->_data=array() : $this->_data=$cData;
	is_null($cDataShow) ? $this->_dataShow=$this->_data : $this->_dataShow=$cDataShow;
    }
}

class uFormTable
{
    private $_id;
    private $_form;
    private $_table;
    private $_elements;
    private $_addElements;
    private $_freezeEls;
    public $_tableWidth;
    public $_target;
    public $_nourut;
    public $_noaction;
    public $_onedata;
    public $_noClearField; // Field yang tidak di clear setelah CRUD
	public $_noEnable; // Field yang tidak di enable setelah CRUD
    public $_detailFieldset;
    public $_addActions;
    public $_numberFormat;
	public $_afterCrud; // JS Function to be executed after
	public $_beforeEditMode; // Execute function before edit mode
    
    #== Constructor
    function uFormTable($cId,$cForm,$cTable,$cEls=null,$cAddEls=null) {
	$this->_id = $cId;
	$this->_tableWidth = 200;
	$this->_target = "slave_ft";
	$this->_freezeEls = null;
	$this->_nourut = false;
	$this->_noaction = false;
	$this->_onedata = false;
	$this->_noClearField = '##';
	$this->_noEnable = '##';
	$this->_defValue = '##';
	$this->_numberFormat = '##';
	$this->_detailFieldset = array();
	$this->_addActions = array();
	$this->_afterCrud = '';
	$this->_beforeEditMode = '';
	is_object($cForm)? $this->_form = $cForm : false;
	is_object($cTable)? $this->_table = $cTable : false;
	
	# Elements
	if(!is_null($cEls)) {
	    $this->_elements = $cEls;
	} else {
	    $this->_elements = array();
	    if(is_object($cForm)) {
		foreach($this->_form->_elements as $els) {
		    $this->_elements[] = $els->_id;
		}
	    }
	}
	
	is_null($cAddEls) ? $this->_addElements=$this->_elements : $this->_addElements=$cAddEls;
    }
    
    /* setFreezeEls
     * Fungsi untuk setting element yang disable pada saat editmode
     */
    function setFreezeEls($cFreeze) {
	if(is_array($cFreeze)) {
	    $tmpStr = "";
	    foreach($cFreeze as $row) {
		$tmpStr .= "##".$row;
	    }
	    $this->_freezeEls = $tmpStr;
	} else {
	    $this->_freezeEls = $cFreeze;
	}
    }
    
    function prep() {
	#==== Prep
	# Recalculate Els Array
	$theEls = $this->_form->_elements;
	$align = array();
	foreach($this->_form->_elements as $el) {
	    $align[] = $el->_align;
	}
	$elsWidth = $this->_form->_width;
	$newEls = array();
	$i=0;$j=0;
	$maxHeight=ceil(count($theEls)/$elsWidth);
	foreach($theEls as $els) {
	    $newEls[$i][$j] = $els;
	    $i++;
	    if($i==$maxHeight) {
		$j++;
		$i=0;
	    }
	}
	
	# Number Formatted
	$numFormatArr = explode('##',$this->_numberFormat);
	
	# Additional Action
	$addActionJs = str_replace('"',"##",json_encode($this->_addActions));
	
	# Elements Parameter
	$elsParam = "";
	$addElsParam = "";
	$alignParam = "";
	foreach($this->_elements as $elId) {
	    $elsParam .= "##".$elId;
	}
	foreach($this->_addElements as $addEl) {
	    $addElsParam .= "##".$addEl;
	}
	foreach($align as $alg) {
	    $alignParam .= "##".$alg;
	}
	
	# Prep Render
	$formTab = "";
	
	$formTab .= "<div id='".$this->_id."'>";
	
	#==== Form
	$formTab .= "<div id='form_".$this->_id."'";
	if($this->_noaction==true) {
	    $formTab .= " style='display:none'";
	}
	$formTab .= ">";
	$formTab .= "<fieldset>";
	$formTab .= "<legend id='form_".$this->_id."_title'>";
	$formTab .= "<b>".$this->_form->_name." : <span id='form_".$this->_id."_mode'>".$_SESSION['lang']['addmode']."</span></b></legend>";
	$formTab .= "<table>";
	foreach($newEls as $row) {
	    $formTab .= "<tr>";
	    foreach($row as $els) {
		if(empty($this->_detailFieldset)) {
		    $formTab .= "<td>".makeElement($els->_id,'label',$els->_name)."</td>";
		    $formTab .= "<td>:</td>";
		    $formTab .= "<td id='".$this->_id."_".$els->_id."'>".$els->genEls()."</td>";
		} else {
		    $notShow=false;
		    foreach($this->_detailFieldset as $keyDet=>$rowDet) {
			foreach($rowDet['element'] as $keyEl=>$rowEl) {
			    if($els->_id==$rowEl) {
				$this->_detailFieldset[$keyDet]['element'][$keyEl] = $els;
				$notShow = true;
			    }
			}
		    }
		    
		    if($notShow==false) {
			$formTab .= "<td>".makeElement($els->_id,'label',$els->_name)."</td>";
			$formTab .= "<td>:</td>";
			$formTab .= "<td id='".$this->_id."_".$els->_id."'>".$els->genEls()."</td>";
		    }
		}
	    }
	    $formTab .= "</tr>";
	}
	
	if(!empty($this->_detailFieldset)) {
	    $formTab .= "<tr><td colspan='".($elsWidth*3)."'>";
	    foreach($this->_detailFieldset as $rowDet) {
		$formTab .= "<fieldset><legend><b>".$rowDet['name']."</b></legend><table>";
		foreach($rowDet['element'] as $rowEl) {
		    $formTab .= "<tr>";
		    $formTab .= "<td>".makeElement($rowEl->_id,'label',$rowEl->_name)."</td>";
		    $formTab .= "<td>:</td>";
		    $formTab .= "<td id='".$this->_id."_".$rowEl->_id."'>".$rowEl->genEls()."</td>";
		    $formTab .= "</tr>";
		}
		$formTab .= "</table></fieldset>";
	    }
	    $formTab .= "</td></tr>";
	}
	
	$formTab .= "<tr><td colspan='".($elsWidth*3)."'>";
	$formTab .= makeElement($this->_id.'_numRow','hidden',"0");
	if($this->_onedata==false) {
	    $formTab .= makeElement('addFTBtn_'.$this->_id,'btn',$_SESSION['lang']['save'],
		array('onclick'=>"theFT.addFT('".$this->_id."','".$elsParam.
		    "','".$addElsParam."','".$this->_target."','".$alignParam.
		    "','".$_SESSION['lang']['editmode']."',false,'".$this->_noClearField.
		    "','".$this->_noEnable.
		    "','".$this->_defValue.
		    "','".$addActionJs.
		    "','".$this->_freezeEls.
		    "','".$this->_numberFormat."')"));
	} else {
	    $formTab .= makeElement('addFTBtn_'.$this->_id,'btn',$_SESSION['lang']['save'],
		array('onclick'=>"theFT.addFT('".$this->_id."','".$elsParam.
		    "','".$addElsParam."','".$this->_target."','".$alignParam.
		    "','".$_SESSION['lang']['editmode']."',true,'".$this->_noClearField.
		    "','".$this->_noEnable.
		    "','".$this->_defValue.
		    "','".$addActionJs.
		    "','".$this->_freezeEls.
		    "','".$this->_numberFormat."')"));
	}
	$formTab .= makeElement('editFTBtn_'.$this->_id,'btn',$_SESSION['lang']['save'],
		array('onclick'=>"theFT.editFT('".$this->_id."','".$elsParam.
		"','".$addElsParam."','".$this->_target.
		"','".$this->_numberFormat."','".$_SESSION['lang']['addmode']."','".$this->_noClearField."','".$this->_noEnable."','".$this->_defValue."')",
		'style'=>'display:none'));
	$formTab .= makeElement('clearFTBtn_'.$this->_id,'btn',$_SESSION['lang']['cancel'],
	    array('onclick'=>"theFT.clearFT('".$this->_id."','".$elsParam."','".
		$addElsParam."','".$_SESSION['lang']['addmode']."','".$this->_noClearField."','".$this->_noEnable."','".$this->_defValue."')",
		'style'=>'display:none'));
	$formTab .= "</td></tr>";
	$formTab .= "</table>";
	$formTab .= "</fieldset>";
	$formTab .= "</div>";
	
	#==== Table
	$formTab .= "<div id='table_".$this->_id."'>";
	$formTab .= "<fieldset>";
	$formTab .= "<legend id='table_".$this->_id."_title'>";
	$formTab .= "<b>".$this->_table->_name."</b></legend>";
	$formTab .= "<div style='max-height:".$this->_tableWidth."px;overflow:auto'>";
	$formTab .= "<table class='sortable' cellspacing='1' border='0' ";
	$formTab .= "id='".$this->_table->_id."'>";
	
	# Thead
	$formTab .= "<thead id='thead_".$this->_id."'><tr class='rowheader'>";
	if($this->_nourut==true) {
	    $formTab .= "<td>#</td>";
	}
	if($this->_noaction==false and $this->_onedata==false) {
	    $formTab .= "<td colspan='".(2+count($this->_addActions))."'>".$_SESSION['lang']['action']."</td>";
	}
	foreach($this->_form->_elements as $cols) {
	    $formTab .= "<td id='head_".$cols->_id."' align='center' ";
	    $formTab .= "style='width:".($cols->_length*10)."px'>".$cols->_name."</td>";
	}
	$formTab .= "</tr></thead>";
	
	# Tbody
	$formTab .= "<tbody id='tbody_".$this->_id."'>";
	foreach($this->_table->_data as $key=>$row) {
	    $formTab .= "<tr id='tr_".$this->_id."_".$key."' class='rowcontent'>";
	    if($this->_nourut==true) {
		$formTab .= "<td>".$key."</td>";
	    }
	    if($this->_noaction==false) {
		$formTab .= "<td><img id='editmodeFTBtn' class='zImgBtn' ";
		$formTab .= "src='images/".$_SESSION['theme']."/edit.png' ";
		if(empty($this->_beforeEditMode)) {
			$formTab .= "onclick=\"theFT.editmodeFT(".$key.",'".$this->_id."','".
				$elsParam."','".$addElsParam."','".$_SESSION['lang']['editmode']."','".$this->_freezeEls."','".$this->_numberFormat."','".$_SESSION['lang']['addmode']."','".$this->_noClearField."','".$this->_noEnable."','".$this->_defValue."')\"></td>";
		} else {
			$formTab .= "onclick=\"theFT.editmodeFT(".$key.",'".$this->_id."','".
				$elsParam."','".$addElsParam."','".$_SESSION['lang']['editmode']."','".$this->_freezeEls."','".$this->_numberFormat."','".$_SESSION['lang']['addmode']."','".$this->_noClearField."','".$this->_noEnable."','".$this->_defValue."');".
				$this->_beforeEditMode."(".$key.",'".$this->_id."','".
				$elsParam."','".$addElsParam."','".$_SESSION['lang']['editmode']."','".$this->_freezeEls."','".$this->_numberFormat."','".$_SESSION['lang']['addmode']."','".$this->_noClearField."','".$this->_noEnable."','".$this->_defValue."')\"></td>";
		}
		$formTab .= "<td><img id='delFTBtn' class='zImgBtn' ";
		$formTab .= "src='images/".$_SESSION['theme']."/delete.png' ";
		$formTab .= "onclick=\"theFT.delFT(".$key.",'".$this->_id."','".
		    $elsParam."','".$addElsParam."','".$this->_target."','".$_SESSION['lang']['addmode']."','".$this->_noClearField."','".$this->_noEnable."','".$this->_defValue."')\"></td>";
		foreach($this->_addActions as $id=>$attr) {
		    $formTab .= "<td><img id='".$id."' class='zImgBtn' ";
		    $formTab .= "src='images/".$_SESSION['theme']."/".$attr['img']."' ";
		    $formTab .= "onclick=\"".$attr['onclick']."(".$key.",event)\"></td>";
		}
	    }
	    $i=0;
	    foreach($row as $id=>$cont) {
		# Check Number Formatted
		$isNF = false;
		foreach($numFormatArr as $rowNF) {
		    if($id==$rowNF) {
			$isNF = true;
		    }
		}
		
		# Show Row
		$formTab .= "<td id='".$this->_id."_".$id."_".$key."' ";
		$formTab .= "align='".$this->_form->_elements[$i]->_align."' ";
		$formTab .= "style='width:".$this->_form->_elements[$i]->_length."px' ";
		$formTab .= "value='".$cont."'>";
		if($isNF==true) {
		    $formTab .= number_format($this->_table->_dataShow[$key][$id],2);
		} else {
		    $formTab .= $this->_table->_dataShow[$key][$id];
		}
		$formTab .= "</td>";
		$i++;
	    }
	    $formTab .= "</tr>";
	}
	$formTab .= "</tbody>";
	
	# Tfoot
	$formTab .= "<tfoot>";
	$formTab .= "</tfoot>";
	$formTab .= "</table>";
	$formTab .= "</div>";
	$formTab .= "</fieldset>";
	$formTab .= "</div>";
	
	$formTab .= "</div>";
	if(!empty($this->_afterCrud)) {
		$formTab .= "<script>theFT.afterCrud='".$this->_afterCrud."';console.log(theFT);</script>";
	}
	
	return $formTab;
    }
    
    function render() {
	echo $this->prep();
    }
}
?>