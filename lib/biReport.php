<?php
class biReport {
    public $_id;
    public $_name;
    public $_column;
    public $_data;
    public $_dataShow;
    
    function biReport($cId,$cName=null,$cCols=null,$cData=null,$cDataShow=null) {
        $this->_id = $cId;
        is_null($cName) ? $this->_name = $cId : $this->_name = $cName;
        is_null($cCols) ? $this->_column = array() : $this->_column = $cCols;
        is_null($cData) ? $this->_data = array() : $this->_data = $cData;
        is_null($cDataShow) ? $this->_dataShow = array() : $this->_dataShow = $cDataShow;
    }
    
    function prep() {
        $tab = "<table id='".$this->_id."' class='sortable'>";
        $tab .= "<thead><tr class='rowheader'>";
        foreach($this->_column as $head) {
            $tab .= "<td>".$head."</td>";
        }
        $tab .= "</tr></thead>";
        $tab .= "<tbody>";
        foreach($this->_data as $key=>$row) {
            $tab .= "<tr class='rowcontent'>";
            foreach($row as $field=>$cont) {
                $tab .= "<td id='".$field."_".$key."' value='".$cont."'>".$this->_dataShow[$key][$field]."</td>";
            }
            $tab .= "</tr>";
        }
        $tab .= "</tbody>";
        $tab .= "</table>";
        return $tab;
    }
    
    function render() {
        echo $this->prep();
    }
}
?>