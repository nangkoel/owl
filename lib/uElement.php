<?php
include_once('lib/zForm.php');

/* class uElement
 * Kelas Elemen untuk layout form-table
 */
class uElement {
    public $_id;
    public $_name;
    public $_content;
    public $_type;
    public $_align;
    public $_length;
    public $_refer;
    public $_attr;
    public $_content2;
    public $_targetSatuan;
    public $_targetHarga;
	public $_parentEl;
    
    #== Constructor
    function uElement($cId,$cName,$cCont=null,$cType=null,$cAlign=null,$cLength=null,$cRefer=null,$cCont2=null,$cTSatuan=null,$cTHarga=null,$cParent=null) {
	
	# Default Value
	is_null($cCont) ? $this->_content='-' : $this->_content = $cCont;
	is_null($cType) ? $this->_type ='text' : $this->_type = $cType;
	
	is_null($cLength) ? $this->_length =40 : $this->_length = $cLength;
	is_null($cRefer) ? $this->_refer =array() : $this->_refer = $cRefer;
	is_null($cAlign) ? $this->_align ="left" : $this->_align = $cAlign;
	$this->_content2 = $cCont2;
	$this->_targetSatuan = $cTSatuan;
	$this->_targetHarga = $cTHarga;
	$this->_parentEl = $cParent;
	switch($this->_align) {
	    case "l":
	    case "L":
		$this->_align = "left";
		break;
	    case "r":
	    case "R":
		$this->_align = "right";
		break;
	    case "c":
	    case "C":
		$this->_align = "center";
		break;
	    default:
	    break;
	}
	
	# Setting Basic Attribute
	$this->_id = $cId;
	$this->_name = $cName;
	$this->_attr = array('style'=>'width:'.($this->_length*6.5).'px');
	#is_null($cAdd) ?  $this->_attr=$this->_attr : $this->_attr = $cAdd;
	//$this->_type ='text' ? $this->_attr=$this->_attr :$this->_attr=$cAdd;
//	echo"warning";
	//print_r($this->_attr);//exit();
    }
    
    #== Generator
    function genEls() {
	return makeElement($this->_id,$this->_type,$this->_content,$this->_attr,$this->_refer,$this->_content2,$this->_targetSatuan,$this->_targetHarga,$this->_parentEl);
    }
}
?>