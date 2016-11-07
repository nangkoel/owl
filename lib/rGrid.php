<?php
include_once('lib/zForm.php');

/* Class rElement
 * Element Grid yang disederhanakan untuk low-bandwidth
 */
class rElement {
    public $_id;
    public $_name;
    public $_content;
    public $_type;
    public $_length;
    public $_refer;
    public $_attr;
    
    function rElement($cId,$cName,$cCont=null,$cType=null,$cAlign=null,$cLength=null,$cRefer=null) {
	# Default Value
	is_null($cCont) ? $this->_content='-' : $this->_content = $cCont;
	is_null($cType) ? $this->_type ='text' : $this->_type = $cType;
	is_null($cLength) ? $this->_length =40 : $this->_length = $cLength;
	is_null($cRefer) ? $this->_refer =array() : $this->_refer = $cRefer;
	is_null($cAlign) ? $this->_align =array() : $this->_align = $cAlign;
	
	# Setting Basic Attribute
	$this->_id = $cId;
	$this->_name = $cName;
	$this->_attr = array();
    }
    
    function genEls() {
	return makeElement($this->_id,$this->_type,$this->_content,$this->_attr,$this->_refer);
    }
}

/* Class rGrid
 * Grid yang disederhanakan untuk low-bandwidth
 */
class rGrid {
    /** Attribute **/
    private $_id;private $_name;
    private $_elements;
    private $_data;
    public $_tr;
    
    
    /** Method **/
    /* Constructor */
    function rGrid($cId,$cName,$cData=null) {
	$this->_id = $cId;
	$this->_name = $cName;
	$this->_elements = array();
	$this->_tr = 'tr';
	is_null($cData) ? $this->_data=array() : $this->_data=$cData;
    }
    
    /* Fungsi untuk menambah setting element
     */
    function addEls($cId,$cName,$cCont=null,$cType=null,$cAlign=null,$cLength=null,$cRefer=null) {
	$this->_elements[$cId] = new rElement($cId,$cName,$cCont,$cType,$cAlign,$cLength,$cRefer);
    }
    
    /* Grid Generator
     */
    function prepGrid() {
	# Element Setting
	$grid = "<div id='".$this->_id."_elements' style='display:none'>";
	$grid .= json_encode($this->_elements)."</div>";
	$grid .= "<table id='".$this->_id."' name='".$this->_name."' class='sortable' ";
	$grid .= "cellpadding=0 cellspacing=1 >";
	
	#=== Header
	$grid .= "<thead id='thead_".$this->_id."'>";
	$grid .= "<tr class='rowheader'>";
	$i=0;
	foreach($this->_elements as $els) {
	    $grid .= "<td id='".$els->_id."_header' align='center' style='width:".
		($els->_length*10)."px'>".$els->_name."</td>";
	    $i++;
	}
	$grid .= "<td colspan='2'>Action</td>";
	$grid .= "</tr>";
	$grid .= "</thead>";
	
	#=== Body
	$grid .= "<tbody id='tbody_".$this->_id."'>";
	if(!empty($this->_data)) {
	    foreach($this->_data as $key=>$row) {
		$grid .= "<tr id='".$this->_tr."_".$key."' class='rowcontent'>";
		foreach($row as $id=>$cont) {
		    $grid .= "<td id='".$this->_elements[$id]->_id."_".$key."'";
		    $grid .= ">";
		    $grid .= $cont;
		    $grid .= "</td>";
		}
		$grid .= "<td>";
		# Make Editable
		$grid .= "<img id='makeEdit_".$key."' src='images/".$_SESSION['theme']."/edit.png' class='zImgBtn' ";
		$grid .= "onclick=\"theRGrid.editElement(".$key.",'".$this->_id."_elements')\">";
		# Edit Data
		$grid .= "<img id='editData_".$key."' src='images/".$_SESSION['theme']."/edit.png' class='zImgBtn' ";
		$grid .= "style='display:none' ";
		$grid .= "onclick=\"theRGrid.editData(".$key.",'".$this->_id."_elements')\">";
		$grid .= "</td>";
		# Delete Data
		$grid .= "<td id='delDataTd_".$key."' style='display:none'>";
		$grid .= "<img id='delData_".$key."' src='images/".$_SESSION['theme']."/delete.png' class='zImgBtn' ";
		$grid .= "style='display:none' ";
		$grid .= "onclick=\"theRGrid.delData(".$key.",'".$this->_id."_elements')\">";
		$grid .= "</td>";
		$grid .= "</tr>";
	    }
	}
	# New Row
	$grid .= "</tbody>";
	
	#=== Footer
	$grid .= "<tfoot id='tfoot_".$this->_id."'>";
	$grid .= "</tfoot>";
	$grid .= "</table>";
	
	return $grid;
    }
    
    function showGrid() {
	echo $this->prepGrid();
	#echo "<pre>";
	#print_r(json_encode($this->_elements));
    }
}
?>