<script languange=javascript1.2 src='js/zGrid.js'></script>
<link rel=stylesheet type=text/css href='style/zGrid/<?php echo $_SESSION['theme']?>.css'>
<?php
/** Necessary Inclusion **/
require_once('lib/zForm.php');

/* Class ColumnGrid
 * Kelas untuk Kolom pada Grid
 */
class ColumnGrid {
    public $_id;
    public $_name;
    public $_content;
    public $_type;
    public $_length;
    public $_refer;
    
    #== Constructor
    function ColumnGrid($cId=null,$cName=null,$cCont=null,$cType=null,$cAlign=null,$cLength=null,$cRefer=null) {
	# Default Value
	is_null($cId) ? $cId='-' : null;
	is_null($cName) ? $cName='-' : null;
	is_null($cCont) ? $cCont='-' : null;
	is_null($cType) ? $cType='text' : null;
	is_null($cLength) ? $cLength=40 : null;
	is_null($cRefer) ? $cRefer=array() : null;
	
	# Setting Basic Attribute
	$this->_id = $cId;
	$this->_name = $cName;
	$this->_type = $cType;
	$this->_content = $cCont;
	$this->_align = $cAlign;
	$this->_length = $cLength;
	$this->_refer = $cRefer;
    }
    
    #== Getter
    function getInfo() {
	$tmpArr = array();
	$tmpArr['id'] = $this->_id;
	$tmpArr['name'] = $this->_name;
	$tmpArr['type'] = $this->_type;
	$tmpArr['length'] = $this->_length;
	$tmpArr['refer'] = $this->_refer;
	
	return $tmpArr;
    }
    
    /* Function gridElement
     * Fungsi untuk generate element di dalam grid
     */
    function gridElement($num,$data,$attr=array()) {
	# Merge Attr
	$newAttr = array('maxlength'=>$this->_length,
	    'style'=>'margin:0;width:'.(string)($this->_length*6.5).'px');
	$attr = array_merge($attr,$newAttr);
	
	# Create Element
	$el = makeElement($this->_id."_".$num,$this->_type,$data,$attr,$this->_refer);
	return $el;
    }
}

/* Class HeadGrid
 * Kelas untuk Header dari Grid
 */
class HeadGrid {
    private $_name;
    private $_sumColumn;
    private $_column;
    
    #== Constructor
    function HeadGrid($cName=null,$cCols=null) {
	# Default Value
	is_null($cName) ? $cName='zGrid' : null;
	is_null($cCols) ? $cCols=array() : null;
	$cSumCol = count($cCols);
	
	# Setting Basic Attribute
	$this->_name = $cName;
	$this->_sumColumn = $cSumCol;
	$this->_column = $cCols;
    }
    
    #== Getter
    function getName() {
	return $this->_name;
    }
    
    function getColumn() {
	return $this->_column;
    }
    
    function getSumColumn() {
	return $this->_sumColumn;
    }
    
    #== Setter
    function setColumn($col) {
	if(is_array($col)) {
	    $this->_column = $col;
	} else {
	    echo "Error : Attribute must array in HeadGrid->setColumn";
	}
    }
    
    #== Column Management
    function insertCol($col=null) {
	# Set Default
	is_null($col) ? $col = new ColumnGrid() : null;
	
	# Insert Column to List
	array_push($this->_column,$col);
	
	# Add Sum Column
	$this->_sumColumn++;
    }
}

/* Class zGrid
 * Kelas untuk Grid
 */
class Grid {
    private $_num;
    private $_head;
    private $_data;
    
    #== Constructor
    function Grid($cNum=null,$cHead=null,$cData=null) {
	# Default Value
	is_null($cNum) ? $cNum=1 : null;
	is_null($cHead) ? $cHead=new HeadGrid() : null;
	is_null($cData) ? $cData=array() : null;
	
	# Setting Basic Attribute
	$this->_num = $cNum;
	$this->_head = $cHead;
	$this->_data = $cData;
    }
    
    /* Function prepLayout
     * Fungsi untuk mempersiapkan grid
     */
    function prepLayout() {
	# Prep
	$num = $this->_num;
	$col = $this->_head->getColumn();
	
	# Begin
	$grid = "<table class='zGrid' border='1' cellpadding='0' cellspacing='0'>";
	
	# Header
	$grid .= "<thead><tr>";
	$tmpCols = $this->_head->getColumn();
	foreach($tmpCols as $cols) {
	    $grid .= "<th style='width:".$cols->_length*6.5."px'>".$cols->_name."</th>";
	}
	$grid .= "<th>";
	$grid .= "<img class='zImgBtn'".
	    "src='images/newfile.png' onclick='theGrid[".$num."].addRowGrid()'></img>";
	$grid .= "</th></tr>";
	$grid .= "</thead>";
	
	# Data / Content
	$grid .= "<tbody id='zGridBody_".$this->_num."'>";
	$sumCols = $this->_head->getSumColumn();
	if(empty($this->_data)) {
	    $grid .= "<tr id='grid_tr_empty' align='center'>";
	    $grid .= "<td colspan='".(string)($sumCols+1)."'>Data Empty";
	    $grid .= "</td></tr>";
	} else {
	    $grid .= "<tr id='grid_tr_empty' align='center' style='display:none'>";
	    $grid .= "<td colspan='".(string)($sumCols+1)."'>Data Empty";
	    $grid .= "</td></tr>";
	    foreach($this->_data as $key=>$row) {
		$grid .= "<tr id='grid_tr_".$key."'>";
		$i=0;
		foreach($row as $name=>$content) {
		    $grid .= "<td id='grid_td_".$name."_".$key."'>";
		    $grid .= $col[$i]->gridElement($num,$content)."</td>";
		    $i++;
		}
		
		# Edit / Delete Row
		$grid .= "<td id='grid_action_".$key."'>";
		$grid .= "<img class='zImgBtn'".
		    "src='images/001_45.png' onclick='theGrid[".$num."].editRowGrid(".$key.")'></img>&nbsp;";
		$grid .= "<img class='zImgBtn'".
		    "src='images/delete1.png' onclick='theGrid[".$num."].delRowGrid(".$key.")'></img>";
		$grid .= "</td>";
		$grid .= "</tr>";
	    }
	}
	$grid .= "</tbody>";
	
	# End
	$grid .= "</table>";
	
	return $grid;
    }
    
    /* Function showGrid
     * Fungsi untuk menampilkan grid
     */
    function showGrid() {
	$grid = $this->prepLayout();
	echo $grid;
    }
}
?>