<?

class log_object {
	//*************************************************************************************
	protected function logEntry($className, $functionName, $input = null)
	{
		if($this->trace == 1)
		{
			$this->writeln("$className::$functionName(): entering..." . (empty($input) ? "" : " input=$input"));
		}
	}

	//*************************************************************************************
	protected function logExit($className, $functionName, $result = null)
	{
		if($this->trace == 1)
		{
			$this->writeln("$className::$functionName(): exiting... result=$result");
		}
	}

	//*************************************************************************************
	protected function logException($e, $className, $functionName, $guiErrorMessage)
	{
		if(!empty($e))
		{
			$systemMessage = "ERROR! " . $className . "::" . $functionName . "(): " . $e->getMessage();
			self::recordError($systemMessage);
			self::getJsonError($guiErrorMessage);
		}
	}
	
	//*************************************************************************************
	protected function recordError($systemMessage)
	{
		$this->writeln($systemMessage);
	}

	//*************************************************************************************
	protected function recordLogMessage($systemMessage)
	{
		$this->writeln($systemMessage);
	}

	//*************************************************************************************
	protected function writeln()
	{
		$str = date('l, F jS, Y h:i:s A') . ": ";
		foreach(func_get_args() as $a)
		{
			$str .= $a;
		}
		$str .= "\n";
		$this->logLine($str);
	}

	//*************************************************************************************
	private function logLine($message)
	{
		$result = null;

		try
		{
			$myFile = $this->getLogPhysicalDirectory() . self::getLogFileName();
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

	//************************************************************************************
	protected function getLogFileName()
	{
		return date("YmdH") . ".log";
	}
}
?>