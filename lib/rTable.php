<?php
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

class rAction {
    private $_attr;
    public $_name;
    public $_title;
    public $_img;
    public $_altImg;
    public $_switchImg;
    
    function rAction($cName,$cTitle,$cImg=null,$cAttr=null) {
	$this->_name = $cName;
	$this->_title = $cTitle;
	$this->_switchImg = false;
	is_null($cAttr) ? $this->_attr = array() : $this->_attr = $cAttr;
	is_null($cImg) ? $this->_img = '/images/default.png' : $this->_img = $cImg;
	is_null($cImg) ? $this->_altImg = '/images/default.png' : $this->_altImg = $cImg;
    }
    
    function setAltImg($imgPath) {
	$this->_switchImg = true;
	$this->_altImg = $imgPath;
    }
    
    function setAttr($cAttr) {
	if(is_array($cAttr)) {
	    $this->_attr = $cAttr;
	} else {
	    return false;
	}
    }
    
    function chgAttr($cAttr,$vAttr) {
	$this->_attr[$cAttr] = $vAttr;
    }
    
    function getAttr() {
	return $this->_attr;
    }
    
    function addAttr($anAttr) {
	array_push($this->_attr,$anAttr);
    }
}

class rTable {
    private $_id;private $_idBody;
    private $_width;private $_align;
    private $_page;private $_totalPage;private $_rowPerPage;
    private $_where;
    public $_headers;
    public $_content;public $_contentShow;
    public $_footer;
    public $_actions;
    public $_tr;
    public $_print;
    public $_fullwidth;
    public $_printAttr;
    public $_switchException;
	public $_colElement;
    
    function rTable($cId,$cBody=null,$cHead=null,$cCont=null,$cContShow=null,$cFoot=null) {
	# Attribute Setting
	$this->_id = $cId;
	$this->_actions = array();
	$this->_tr = 'tr';
	$this->_print = true;
	$this->_printAttr = null;
	$this->_page = 1;$this->_totalPage = 1;$this->_rowPerPage = 10;
	$this->_where = null;
	$this->_fullwidth = true;
	$this->_switchException = array();
	$this->_colElement = array();
	is_null($cBody) ? $this->_idBody = array() : $this->_idBody = $cBody;
	is_null($cHead) ? $this->_headers = array() : $this->_headers = $cHead;
	is_null($cCont) ? $this->_content = array() : $this->_content = $cCont;
	is_null($cContShow) ? $this->_contentShow = $cCont : $this->_contentShow = $cContShow;
	is_null($cFoot) ? $this->_footer = array() : $this->_footer = $cFoot;
	
	# Setting Width
	$this->_width = array();
	$sumHead = count($this->_headers);
	for($i=0;$i<$sumHead;$i++) {
	    $this->_width[] = floor(100/$sumHead*0.9);
	}
	
	# Setting Align
	$this->_align = array();
	for($i=0;$i<$sumHead;$i++) {
	    $this->_align[] = 'center';
	}
    }
    
    function setWhere($where) {
	if(is_array($where)) {
	    $this->_where = $where;
	} else {
	    return false;
	}
    }
    
    /* Function pageSetting
     * Fungsi untuk setting paginating
     */
    function pageSetting($cPage,$cTotalRow,$cRowPerPage) {
	$this->_page = $cPage;
	$this->_totalPage = ceil($cTotalRow/$cRowPerPage);
	$this->_rowPerPage = $cRowPerPage;
    }
    
    /* Function setWidth
     * Fungsi untuk setting persentase panjang tiap kolom
     */
    function setWidth($width) {
	if(count($width) == count($this->_headers)) {
	    $sumLen = 0;
	    $newWidth = array();
	    foreach($width as $len) {
		$sumLen += $len;
		$newWidth = 0.9 * $len;
	    }
	    if($sumLen<=100) {
		$this->_width = $newWidth;
	    } else {
		return false;
	    }
	} else {
	    return false;
	}
    }
    
    /* Function setAlign
     * Fungsi untuk setting align tiap kolom
     */
    function setAlign($align) {
	if(count($align) == count($this->_headers)) {
	    foreach($align as $key=>$alg) {
		switch($alg) {
		    case 'center':
		    case 'ctr':
		    case 'c':
		    case 'C':
			$align[$key] = 'center';
			break;
		    case 'left':
		    case 'lt':
		    case 'l':
		    case 'L':
			$align[$key] = 'left';
			break;
		    case 'right':
		    case 'rt':
		    case 'r':
		    case 'R':
			$align[$key] = 'right';
			break;
		    default:
			$align[$key] = 'center';
			break;
		}
	    }
	    $this->_align = $align;
	} else {
	    return false;
	}
    }
    
    function addAction($cAct,$cTitle,$cAttr=null) {
	if(is_null($cAttr)) {
	    if(is_object($cAct)) {
		# cAct is object rAction
		$this->_actions[] = $cAct;
	    } else {
		# cAct is action name
		$this->_actions[] = new rAction($cAct,$cTitle);
	    }
	} else {
	    # Have Attr
	    $this->_actions[] = new rAction($cAct,$cTitle,$cAttr);
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
	if(!empty($this->_where)) {
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
	}
	$where .= "]'";
	
	$theTable  = "";
	if($this->_print) {
	    $theTable .= "<fieldset style='float:left;clear:right'>";
	    $theTable .= "<legend><b>".$_SESSION['lang']['print']."</b></legend>";
	    $theTable .= "<img class='zImgBtn' src='images/".$_SESSION['theme']."/print.png'".
		"style='cursor:pointer' onclick='print()' title='Print Page' />&nbsp;&nbsp;";
	    $theTable .= "<img class='zImgBtn' src='images/".$_SESSION['theme']."/pdf.jpg'".
		"style='cursor:pointer' onclick=\"printPDF(event";
	    if(!is_null($this->_printAttr)) {
		foreach($this->_printAttr as $attr) {
		    $theTable .= ",'".$attr."'";
		}
	    }
	    $theTable .= ")\" title='Print PDF' />";
	    $theTable .= "</fieldset>";
	}
	$theTable .= "<fieldset style='clear:left'>";
	$theTable .= "<legend><b>".$_SESSION['lang']['list']."</b></legend>";
	$theTable .= "<table id='".$this->_id."' class='sortable' cellspacing='1' ".
	    "style='width:100%' border='0'>";
	
	# Header
	$theTable .= "<thead><tr class='rowheader'>";
	foreach($this->_headers as $key=>$head) {
	    $theTable .= "<td align='center' style='width:".$this->_width[$key]."%'>".$head."</td>";
	}
	if(!empty($this->_actions)) {
	    $theTable .= "<td align='center' style='width:10%' colspan='".count($this->_actions)."'>".$_SESSION['lang']['action']."</td>";
	}
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
		    if($id!='switched' and $id!='posted' and $id!='konfirmed' and $id!='noSwitchList' and $id!='noAction') {
			$theTable .= "<td align='".$this->_align[$ct].
			    "' id='".$id."_".$key."' value='".$val."'>";
			if(isset($this->_colElement[$id])) {
				$attr = array();
				if(isset($row['switched']) and $row['switched']==true) {
					$attr['disabled'] = 'disabled';
				}
				$theTable .= makeElement($id."_".$key."_el",$this->_colElement[$id]['type'],
					$this->_contentShow[$key][$id],$attr);
			} else {
				$theTable .= $this->_contentShow[$key][$id];
			}
			$theTable .= "</td>";
			$ct++;
		    }
                    foreach($this->_actions as $act) {
                        foreach($this->_switchException as $switch) {
                            if($act->_name==$switch) {
//                                $postVal=$row[strtolower($act->_title)];
                                
                            }
                        }
                    }
		}
		# Actions
		foreach($this->_actions as $act) {
		    $switched = true;
		    # Search if it's on exception
		    foreach($this->_switchException as $switch) {
			if($act->_name==$switch) {
			    $switched=false;
			}
		    }
		    if(isset($row['noSwitchList'])) {
			foreach($row['noSwitchList'] as $name) {
			    if($name==$act->_name) {
				$switched=false;
			    }
			}
		    }
		    
		    if(isset($row['switched']) and $switched==true) {
                        $theTable .= "<td><img src='".$act->_altImg."' class='zImgOffBtn'";
		    } elseif(isset($row['noAction'])) {
			$theTable .= "<td></td>";
		    } else {
                        if (isset($row['posted']) and $row['posted']==true){
                            $theTable .= "<td><img src='".$act->_altImg."' class='zImgOffBtn'";
                        } else {
                            $theTable .= "<td><img src='".$act->_img."' class='zImgBtn'".
                                "onclick=\"".$act->_name."(".$key;
                            $tmpAttr = $act->getAttr();
                            if(!empty($tmpAttr)) {
                                foreach($tmpAttr as $attr) {
                                    if($attr=='event') {
                                        $theTable .= ",".$attr;
                                    } else {
                                        $theTable .= ",'".$attr."'";
                                    }
                                }
                            }
                            $theTable .= ")\" ";
                        }
		    }
		    $theTable .= "title='".$act->_title."'/></td>";
		}
		$theTable .= "</tr>";
	    }
	}
	$theTable .= "</tbody>";
	
	#=== Footer
	$theTable .= "<tfoot><tr>";
	$theTable .= "<td colspan='".(count($this->_headers)+count($this->_actions))."' align='center'>";
	
	# First
	$theTable .= "<img src='images/".$_SESSION['theme']."/first.png'";
	if($this->_page>1) {
	    $theTable .= " style='cursor:pointer' onclick=\"goToPages(1,".
		$this->_rowPerPage.",".$where.")\"";
	}
	$theTable .= ">&nbsp;";
	
	# Previous
	$theTable .= "<img src='images/".$_SESSION['theme']."/prev.png'";
	if($this->_page>1) {
	    $theTable .= " style='cursor:pointer' onclick=\"goToPages(".
		(($this->_page)-1).",".$this->_rowPerPage.",".$where.")\"";
	}
	$theTable .= ">&nbsp;";
	
	# Pages
	$theTable .= makeElement('pages','select',$this->_page,array('style'=>'width:50px',
	    'onchange'=>'choosePage(this,'.$this->_rowPerPage.",".$where.")"),$optPage)."&nbsp;";
	
	# Next
	$theTable .= "<img src='images/".$_SESSION['theme']."/next.png'";
	if($this->_page<$this->_totalPage) {
	    $theTable .= " style='cursor:pointer' onclick=\"goToPages(".
		(($this->_page)+1).",".$this->_rowPerPage.",".$where.")\"";
	}
	$theTable .= ">&nbsp;";
	
	# Last
	$theTable .= "<img src='images/".$_SESSION['theme']."/last.png'";
	if($this->_page<$this->_totalPage) {
	    $theTable .= " style='cursor:pointer' onclick=\"goToPages(".
		$this->_totalPage.",".$this->_rowPerPage.",".$where.")\"";
	}
	$theTable .= ">";
	$theTable .= "</td>";
	$theTable .= "</tr></tfoot>";
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