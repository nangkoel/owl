<?php
include_once('lib/uElement.php');

class uReport {
    /** Attribute **/
    private $_id;
    public $_name;
    private $_primeEls;
    private $_advanceEls;
    public $_page;
    
    /** Constructor **/
    function uReport($cId,$cPage,$cName=null,$pEls=null,$aEls=null) {
        $this->_id = $cId;
	$this->_page = $cPage;
        is_null($cName) ? $this->_name = ucfirst($cId) : $this->_name = $cName;
        is_null($pEls) ? $this->_primeEls = array() : $this->_primeEls = $pEls;
        is_null($aEls) ? $this->_advanceEls = array() : $this->_advanceEls = $aEls;
    }
    
    /* Add Primary Filter */
    function addPrime($cId,$cName,$cCont=null,$cType=null,$cAlign=null,$cLength=null,$cRefer=null) {
	$this->_primeEls[] = new uElement($cId,$cName,$cCont,$cType,$cAlign,$cLength,$cRefer);
    }
    
    /* Add Advance Filter */
    function addAdvance($cId,$cName,$cCont=null,$cType=null,$cAlign=null,$cLength=null,$cRefer=null) {
	$this->_advanceEls[] = new uElement($cId,$cName,$cCont,$cType,$cAlign,$cLength,$cRefer);
    }
    
    function prep() {
	##=== Prep
	# Field
	$primeStr = "";
	$advanceStr = "";
	foreach($this->_primeEls as $els) {
	    $primeStr .= "##".$els->_id;
	}
	foreach($this->_advanceEls as $els) {
	    $advanceStr .= "##".$els->_id;
	}
	
        ##=== Form
        $fReport = "<div align='center'><h3>".$this->_name."</h3></div>";
        $fReport .= "<fieldset><legend>Filter</legend>";
        $fReport .= "<div id='".$this->_id."'><table align='center'>";
        foreach($this->_primeEls as $els) {
            $fReport .= "<tr><td>".makeElement($els->_id."_check",'checkbox',1,
		array('checked'=>'checked','onclick'=>"toggleActive(this,'".$els->_id."')"))."</td>";
            $fReport .= "<td>".makeElement($els->_id,'label',$els->_name)."</td>";
            $fReport .= "<td>:</td><td>".$els->genEls()."</td></tr>";
        }
	$fReport .= "<tr><td colspan='4' align='center'>".makeElement('btnPreview','btn','Preview',
	    array('onclick'=>"print('preview','".$primeStr."','".$advanceStr."','".$this->_page."')"))."</td></tr>";
        $fReport .= "</table></div></fieldset>";
        
        ##=== Print Format
        $fReport .= "<fieldset><legend>Print Format</legend>";
        $fReport .= "<div id='printFormat' align='center'>";
	# PDF
	$fReport .= "<img id='report_pdf' title='PDF' src='images/".$_SESSION['theme']."/pdf.jpg' ";
	$fReport .= "class='zImgPrint' onclick=\"print('pdf','".$primeStr."','".$advanceStr."','".$this->_page."')\"></img>&nbsp;";
	# Excel
	$fReport .= "<img id='report_xls' title='Excel' src='images/".$_SESSION['theme']."/excel.jpg' ";
	$fReport .= "class='zImgPrint' onclick=\"print('excel','".$primeStr."','".$advanceStr."','".$this->_page."')\"></img>";
	$fReport .= "</div></fieldset>";
        
        ##=== Work Field
        $fReport .= "<fieldset><legend>Preview</legend>";
        $fReport .= "<div id='workField'></div></fieldset>";
        
        return $fReport;
    }
    
    function render() {
        echo $this->prep();
    }
}
?>