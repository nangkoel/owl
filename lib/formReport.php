<?php
include_once('lib/uElement.php');
//include_once('conf/connection.php');
class formReport {
    /** Attribute **/
    private $_id;
    public $_name;
    public $_primeEls;
    public $_advanceEls;
    public $_page;
    public $_workField;
    public $_detailHeight;
    public $_pdfButton;
    public $_excelButton;
   
	
    /** Constructor **/
    function formReport($cId,$cPage,$cName=null,$pEls=null,$aEls=null) {
        $this->_id = $cId;
	$this->_page = $cPage;
        $this->_workField = 'workField';
	$this->_detailHeight = 70;
	$this->_pdfButton = true;
	$this->_excelButton = true;
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
    global $dbname;
	##=== Prep
	# Field
	$primeStr = "";
	$advanceStr = "";
	foreach($this->_primeEls as $els) {
	    switch($els->_type) {
		case 'bulantahun':
		    $primeStr .= "##".$els->_id."_bulan##".$els->_id."_tahun";
		    break;
		default:
		    $primeStr .= "##".$els->_id;
	    }
	}
	foreach($this->_advanceEls as $els) {
	    switch($els->_type) {
		case 'bulantahun':
		    $advanceStr .= "##".$els->_id."_bulan##".$els->_id."_tahun";
		    break;
		default:
		    $advanceStr .= "##".$els->_id;
	    }
	}
	
        ##=== Form
        $fReport = "";
        #$fReport .= "<div align='center'><h3>".$this->_name."</h3></div>";
        $fReport .= "<fieldset><legend><b>".$this->_name."</b></legend>";
        $fReport .= "<div id='".$this->_id."'><table align='left'>";
		//$fReport .= "<tr><td>".$_SESSION['lang']['kebun']."</td><td>:</td><td><select id=kebun name=kebun>".$optKbn."</select></td></tr>";
        foreach($this->_primeEls as $els) {
            $fReport .= "<tr><td>".makeElement($els->_id,'label',$els->_name)."</td>";
            $fReport .= "<td>:</td><td>".$els->genEls()."</td></tr>";
        }
	$fReport .= "<tr><td colspan='4' align='left'>".
	    makeElement('btnPreview','btn','Preview',
	    #array('onclick'=>"biPrint(0,'".$this->_page."','".$this->_workField."','".$primeStr.$advanceStr."')")).
	    array('onclick'=>"formPrint('preview',0,'".$primeStr."','".$advanceStr."','".$this->_page."',event)"));
        if ($this->_pdfButton){
	$fReport .= makeElement('btnPDF','btn','PDF',
	    array('onclick'=>"formPrint('pdf',0,'".$primeStr."','".$advanceStr."','".$this->_page."',event)"));
        }
        if ($this->_excelButton){
	$fReport .= makeElement('btnExcel','btn','Excel',
	    array('onclick'=>"formPrint('excel',0,'".$primeStr."','".$advanceStr."','".$this->_page."',event)"));
        }
	$fReport .= "</td></tr>";
        $fReport .= "</table></div></fieldset>";
        
        ##=== Work Field
        $fReport .= "<fieldset><legend><b>Preview</b></legend>";
        $fReport .= "<div id='".$this->_workField."' style='overflow:auto;height:".$this->_detailHeight."%'></div></fieldset>";
        
        return $fReport;
    }
    
    function render() {
        echo $this->prep();
    }
}
?>