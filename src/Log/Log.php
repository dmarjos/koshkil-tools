<?php
namespace Koshkil\Tools\Log;

use Koshkil\Core\Application;
use Koshkil\Tools\Sanitize;

class Log {
    public static function addLog($logFile,$log,$data=false) {
        $debugLevel=intval(Application::get('DEBUG_LEVEL','0'));
        if ($debugLevel==0) return;
		$thePath = Application::getLink('/resources/logs/'.$logFile.'.log');
		$currentLog = @file_get_contents($_SERVER["DOCUMENT_ROOT"] . $thePath);
		if (!$currentLog)
			$currentLog = "";
		if (is_array($log) || is_object($log)) $log=print_r($log,true);
        if ($data) {
            switch ($debugLevel) {
                case 1:
                    $data=Sanitize::mask($data,Sanitize::MASK_PASSWORDS | Sanitize::MASK_FULL_CC);
                    break;
                case 2:
                    $data=Sanitize::mask($data,Sanitize::MASK_PASSWORDS | Sanitize::MASK_CC);
                    break;
                case 3:
                    $data=Sanitize::mask($data,Sanitize::MASK_CC);
                    break;
            }
            $log.=print_r($data,true);
        }

		$currentLog.="[" . date("Y-m-d H:i:s", time()) . "] {$log}\n";
		if (!is_writable(dirname($_SERVER["DOCUMENT_ROOT"] . $thePath))) {
			throw new Exception('Logs folder is not writable');
			return false;
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"] . $thePath) && !is_writable($_SERVER["DOCUMENT_ROOT"] . $thePath)) {
			throw new Exception('Log file {$_SERVER["DOCUMENT_ROOT"]}{$thePath} is not writable');
			return false;
		}

		file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $thePath, $currentLog);
	}

	public static function error($text,$data=false) {
		self::addLog('error',$text,$data);
	}

	public static function debug($text,$data=false) {
		self::addLog('debug',$text,$data);
	}


}

