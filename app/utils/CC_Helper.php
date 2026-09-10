<?php
/**
 * CC_Helper.php
 *
 * Pomocna trida pro formatovani retezcu pro zobrazeni v sablonach.
 *
 * @copyright 2014
 * @version 1.0.0
 */


abstract class CC_Helper
{
        
    public static function printDate(DateTime $date, $format = 'd.m.Y h:i:s')
    {
        return $date->format($format);
    }
    
    
    public static function printSelect($name, $items = array(), $selected = null, $prompt = null)
    {
        $id = 'select' . lcfirst($name);
        $html = '<select id="'.$id.'" name="'.$name.'" class="form-control">';
        
        if ($prompt) {
            $html .= '<option value="-1">--- ' . $prompt . ' ---</option>';
        }
        
        foreach ($items as $value => $text) {
            $html .= '<option value="'.$value.'"' . ($value == $selected ? ' selected="selected"' : '') . '>' . $text . '</option>';
        }
        
        $html .= '</select>';        
        return $html;
    }
    
    
}

