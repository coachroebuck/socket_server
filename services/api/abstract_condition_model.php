<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

require_once(RMPATH_BASE . DS . API_DIRECTORY . DS . "abstract_model.php");

class abstract_condition_model extends abstract_model {
	public $operator;
	public $operand;
	public $minimum;
	public $maximum;

	function __construct(
		$operator = null,
		$operand = null,
		$minimum = null,
		$maximum = null)
	{
		$this->operator = $operator;
		$this->operand = $operand;
		$this->minimum = $minimum;
		$this->maximum = $maximum;
	}

	public function operatorText() {

		$result = "";

		switch($comparison)
		{
			case condition_operator::Equal:
			$result .= "  =  ";
			break;
			case condition_operator::NotEqual:
			$result .= "  <>  ";
			break;
			case condition_operator::GreaterThan:
			$result .= "  >  ";
			break;
			case condition_operator::LessThan:
			$result .= "  <  ";
			break;
			case condition_operator::GreaterThanOrEqual:
			$result .= "  >=  ";
			break;
			case condition_operator::LessThanOrEqual:
			$result .= "  <=  ";
			break;
			case condition_operator::Between:
			$result .= "  BETWEEN  ";
			break;
			case condition_operator::Like:
			$result .= "  LIKE  ";
			break;
			case condition_operator::In:
			$result .= "  IN ";
			break;
			case condition_operator::Is:
			$result .= "  IS  ";
			break;
			case condition_operator::IsNot:
			$result .= "  IS NOT  ";
			break;
			default:
			break;
		}

		return $result;
	}

	public function createConditionText(
		$alias = "",
		$key = null,
		$model_data_type = model_data_type::Number,
		$operand = null,
		$minimum = null, 
		$maximum = null) {

		$result = "";

		if(!isset($alias) || strlen($alias) == 0) {
			$alias = "";
		}
		else {
			$alias .= ".";
		}
		if(empty($operand)) {
			$operand = $this->operand;
		}
		if(empty($minimum)) {
			$minimum = $this->minimum;
		}
		if(empty($maximum)) {
			$maximum = $this->maximum;
		}
		
		if(!(empty($operand) && empty($minimum) && empty($maximum))) {

			switch($this->operator)
			{
				case condition_operator::Equal:
				$this->addSlashesIfNeeded($operand, $model_data_type);
				$result .= " $alias$key = $operand ";
				break;
				case condition_operator::NotEqual:
				$this->addSlashesIfNeeded($operand, $model_data_type);
				$result .= " $alias$key <> $operand ";
				break;
				case condition_operator::GreaterThan:
				$result .= " $alias$key > $operand ";
				break;
				case condition_operator::LessThan:
				$result .= " $alias$key < $operand ";
				break;
				case condition_operator::GreaterThanOrEqual:
				$result .= " $alias$key >= $operand ";
				break;
				case condition_operator::LessThanOrEqual:
				$result .= " $alias$key <= $operand ";
				break;
				case condition_operator::Between:
				$result .= " $alias$key BETWEEN $minimum AND $maximum ";
				break;
				case condition_operator::Like:
				$operand = "%" . $operand . "%";
				$this->addSlashesIfNeeded($operand, $model_data_type);
				$result .= " $alias$key LIKE $operand ";
				break;
				case condition_operator::In:
				$this->addSlashesForGroupIfNeeded($operand, $model_data_type);
				$result .= " $alias$key IN ($operand) ";
				break;
				case condition_operator::Is:
				$this->addSlashesIfNeeded($operand, $model_data_type);
				$result .= " $alias$key IS $operand ";
				break;
				case condition_operator::IsNot:
				$this->addSlashesIfNeeded($operand, $model_data_type);
				$result .= " $alias$key IS NOT $operand ";
				break;
				case condition_operator::RegExp:
				$this->addSlashesIfNeeded($operand, $model_data_type);
				$result .= " $alias$key RegExp $operand ";
				break;
				case condition_operator::NotRegExp:
				$this->addSlashesIfNeeded($operand, $model_data_type);
				$result .= " $alias$key NOT RegExp $operand ";
				break;
				case condition_operator::RegExpLike:
				$this->addSlashesIfNeeded($operand, $model_data_type);
				$result .= " $alias$key RLIKE $operand ";
				break;
				default:
				break;
			}
		}
			
		return $result;
	}

	private function addSlashesForGroupIfNeeded(&$value, $model_data_type) {
		if($model_data_type == table_column_data_type::Text) {
			$array = explode(",", $value);
			
			for($i = 0; $i < sizeof($array); $i++)
			{
				$this->addSlashesIfNeeded($array[$i], $model_data_type);
			}
			
			$value = implode(",", $array);
		}
	}

	private function addSlashesIfNeeded(&$value, $model_data_type) {

		if($model_data_type == table_column_data_type::Text) {
			$value = "'" . addslashes($value) . "'";
		}
	}
	
	function __destruct()
	{
		parent::deinit($this);
	}
}

?>