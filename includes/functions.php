<?php
function esc_attr($i){
	return htmlspecialchars($i, ENT_QUOTES);
}

function esc_html($i){
	return htmlspecialchars($i);
}
function array_interlace() {
    $args = func_get_args();
    $total = count($args);

    if($total < 2) {
        return FALSE;
    }
   
    $i = 0;
    $j = 0;
    $arr = array();
   
    foreach($args as $arg) {
        foreach($arg as $v) {
            $arr[$j] = $v;
            $j += $total;
        }
       
        $i++;
        $j = $i;
    }
   
    //ksort($arr);
    return array_values($arr);
}