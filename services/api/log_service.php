<?php

class log_service {
	
	static private $trace = 1;
	static private $error_code = 200;

	// static public function getLogUrl() {
	// 	$url = str_replace("services/", "", self::$data["directory"]);
	// 	$url .= "log/" . self::getLogFileName();
	// 	return $url;
	// }
	
	static public function error_code() {
		return log_service::$error_code;
	}

	static public function enter_method($className, $functionName, $input = null)
	{
		if(APPLICATION_TRACE == 1)
		{
			log_service::writeln("$className::$functionName(): entering..." . (empty($input) ? "" : " input=$input"));
		}
	}

	static public function exit_method($className, $functionName, $result = null)
	{
		if(APPLICATION_TRACE == 1)
		{
			log_service::writeln("$className::$functionName(): exiting... result=$result");
		}
	}

	static public function error($e, $className, $functionName, $guiErrorMessage)
	{
		if(!empty($e))
		{
			$systemMessage = "ERROR! " . $className . "::" . $functionName . "(): " . $e->getMessage();
			log_service::writeln($systemMessage);
			log_service::$error_code = 417;
			return service_messaging::error($guiErrorMessage);
		}
	}

	static public function log($systemMessage)
	{
		log_service::writeln($systemMessage);
	}

	static public function writeln()
	{
		$str = date('l, F jS, Y h:i:s A') . ": ";
		foreach(func_get_args() as $a)
		{
			$str .= $a;
		}
		$str .= "\n";
		log_service::logLine($str);
	}

	static private function logLine($message)
	{
		$result = null;

		try
		{
			$myFile = log_service::getLogPhysicalDirectory() . log_service::getLogFileName();

			$fh = @fopen($myFile, 'a');
			if(!$fh)
			{
				throw new exception("FAILED to open log file");
			}
			fwrite($fh, $message);
			fclose($fh);
		}
		catch(exception $e)
		{
			$systemMessage = "ERROR! " . __FILE__ . "\n\t"
				. __CLASS__. "::" . __FUNCTION__ . "(): " . $e->getMessage();
		}

		return $result;
	}

	static private function getLogFileName()
	{
		return date("YmdH") . ".log";
	}

	static private function getLogPhysicalDirectory() {
		$logDirectory = RMPATH_BASE.DS."log".DS;

		if(!file_exists($logDirectory) && !mkdir($logDirectory, 0777, true))
		{
			throw new exception ("FAILED to create log directory!");
		}
		
		return $logDirectory;
	}
}

?>