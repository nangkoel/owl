<?php
class mColumn {
    public $_id;
    public $_width;
    
    function mColumn($cId,$cWidth=null) {
	is_null($cWidth) ? $this->_width = 0 : $this->_width = $cWidth;
    }
}

class mTable {
    public $_id;public $_idBody;private $_columns;
    private $_headers;private $_content;private $_footers;
    public $_tr;public $_print;public $_fullwidth;
    public $_pdfLink;
    
    function mTable($cId,$cBody=null,$cHead=null,$cCont=null,$cFoot=null) {
	# Attribute Setting
	$this->_id = $cId;
	$this->_actions = array();
	$this->_tr = 'tr';
	$this->_print = true;
	$this->_pdfLink = "#";
	$this->_fullwidth = false;
	is_null($cBody) ? $this->_idBody = array() : $this->_idBody = $cBody;
	is_null($cHead) ? $this->_headers = array() : $this->_headers = $cHead;
	is_null($cCont) ? $this->_content = array() : $this->_content = $cCont;
	is_null($cFoot) ? $this->_footers = array() : $this->_footers = $cFoot;
    }
    
    function setColumn($arrId,$arrWidth=array()) {
	foreach($arrId as $key=>$id) {
	    if(empty($arrWidth)) {
		$this->_columns[$key] = new mColumn($id);
	    } else {
		$this->_columns[$key] = new mColumn($id,$arrWidth[$key]);
	    }
	}
    }
    
    function prepTable() {
	#== Prep Page Drop Down
	$optPage = array();
	$this->_totalPage<1 ? $this->_totalPage=1 : null;
	for($i=1;$i<=$this->_totalPage;$i++) {
	    $optPage[$i] = $i;
	}
	
	#== Prep Where
	$where = "'[";
	foreach($this->_where as $r1) {
	    $where .= "[";
	    $i=0;
	    foreach($r1 as $r2) {
		if($i>0) $where .= ",";
		if(is_int($r2)) {
		    $where .= $r2;
		} else {
		    $where .= "\'".$r2."\'";
		}
		$i++;
	    }
	    $where .= "]";
	}
	$where .= "]'";
	
	$theTable  = "";
	if($this->_print) {
	    $theTable .= "<fieldset style='float:left;clear:right'>";
	    $theTable .= "<legend><b>".$_SESSION['lang']['print']."</b></legend>";
	    $theTable .= "<img class='zImgBtn' src='images/".$_SESSION['theme']."/print.png'".
		"style='cursor:pointer' onclick='print()' title='Print Page' />&nbsp;&nbsp;";
	    $theTable .= "<img class='zImgBtn' src='images/".$_SESSION['theme']."/pdf.jpg'".
		"style='cursor:pointer' onclick='printPDF()' title='Print PDF' />";
	    $theTable .= "</fieldset>";
	}
	$theTable .= "<fieldset style='clear:left'>";
	$theTable .= "<legend><b>".$_SESSION['lang']['list']."</b></legend>";
	$theTable .= "<table id='".$this->_id."' class='sortable' cellspacing='1' ";
	if($this->_fullwidth) {
	    $theTable .= "style='width:100%' ";
	}
	$theTable .= "border='0'>";
	
	# Header
	$theTable .= "<thead><tr class='rowheader'>";
	foreach($this->_headers as $key=>$head) {
	    $theTable .= "<td align='center' style='width:".$this->_columns[$key]->_width."%'>".$head."</td>";
	}
	$theTable .= "<td align='center' style='width:10%' colspan='".count($this->_actions)."'>".$_SESSION['lang']['action']."</td>";
	$theTable .= "</tr></thead>";
	
	# Body
	$theTable .= "<tbody id='".$this->_idBody."'>";
	# Content
	if(empty($this->_content)) {
	    $theTable .= "<tr id='".$this->_tr."_empty' class='rowcontent'>";
	    $theTable .= "<td align='center' colspan='".(count($this->_headers)+1)."'>".$_SESSION['lang']['dataempty']."</td>";
	    $theTable .= "</tr>";
	} else {
	    foreach($this->_content as $key=>$row) {
		$theTable .= "<tr id='".$this->_tr."_".$key."' class='rowcontent'>";
		$ct=0;
		foreach($row as $id=>$val) {
		    if($id!='switched') {
			$theTable .= "<td align='".$this->_align[$ct]."' id='".$id."_".$key."'>".$val."</td>";
			$ct++;
		    }
		}
		# Actions
		foreach($this->_actions as $act) {
		    if(isset($row['switched'])) {
			$theTable .= "<td><img src='".$act->_altImg."' class='zImgBtn'".
			    "onclick=\"".$act->_name."(".$key;
		    } else {
			$theTable .= "<td><img src='".$act->_img."' class='zImgBtn'".
			    "onclick=\"".$act->_name."(".$key;
		    }
		    $tmpAttr = $act->getAttr();
		    if(!empty($tmpAttr)) {
			foreach($tmpAttr as $attr) {
			    $theTable .= ",'".$attr."'";
			}
		    }
		    $theTable .= ")\" title='".$act->_title."' style='cursor:pointer' /></td>";
		}
		$theTable .= "</tr>";
	    }
	}
	$theTable .= "</tbody>";
	
	#=== Footer
	$theTable .= "<tfoot>";
	$theTable .= "</tfoot>";
	$theTable .= "</table>";
	$theTable .= "</fieldset>";
	
	return $theTable;
    }
    
    function renderTable() {
	$theTable = $this->prepTable();
	echo $theTable;
    }
}
?>