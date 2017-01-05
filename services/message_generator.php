<?

class message_generator {
	//************************************************************************************
	protected function getJsonNotice($msg)
	{
		$arr = array('notice' => $msg);
		$result = json_encode($arr);
		unset($arr);
		return $result;
	}
	
	//************************************************************************************
	protected function getJsonError($msg, $responseDescription = "Conflict")
	{
		$arr = array('error_description' => $msg);
		$result = json_encode($arr);
		unset($arr);
		return $result;
	}
	
	//************************************************************************************
	protected function getJsonMessage($key, $msg)
	{
		$arr = array($key => $msg);
		$result = json_encode($arr);
		unset($arr);
		return $result;
	}
}
?>