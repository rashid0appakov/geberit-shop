<?php
// Отвечаем только на Ajax
if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {return;}

function _log($var, $clear=FALSE, $path=NULL) {
	
    if ($var) {
		
		$date = date('Y-m-d H:i:s');
        $result = $var;
        if (is_array($var) || is_object($var)) {
            $result = print_r($var, 1);
        }
        $result .="\n";
        if(!$path)
            $path = dirname($_SERVER['SCRIPT_FILENAME']) . '/js_log.txt';
        if($clear)
            file_put_contents($path, ''); 
		
        @error_log($date.' - '.$result, 3, $path);
        
		return true;
    }
    return false;
}

_log($_POST['log']);
?>