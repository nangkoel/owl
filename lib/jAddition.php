<?php
	
	function createHours($id='hours_select', $selected=null)
    {
        /*** range of hours ***/
        $r = range(0, 23);

					
        /*** current hour ***/
        $selected = is_null($selected) ? date('h') : $selected;

        $select = "<select name=\"$id\" id=\"$id\">\n";
        foreach ($r as $hour)
        {
            if(strlen($hour)==1) 
			  {$hour = '0'.$hour;}
			$select .= "<option value=\"$hour\"";
            $select .= ($hour==$selected) ? ' selected="selected"' : '';
            $select .= ">$hour</option>\n";
        }
        $select .= '</select>';
        return $select;
    }
	
	function createMinutes($id='minute_select', $selected=null)
    {
        /*** array of mins ***/
        $minutes = range(0, 59);

   		$selected = in_array($selected, $minutes) ? $selected : 0;

        $select = "<select name=\"$id\" id=\"$id\">\n";
        foreach($minutes as $min)
        {
            if(strlen($min)==1) 
			$min = '0'.$min;
			$select .= "<option value=\"$min\"";
            $select .= ($min==$selected) ? ' selected="selected"' : '';
            $select .= ">".str_pad($min, 2, '0')."</option>\n";
        }
        $select .= '</select>';
        return $select;
    }
?>