<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

//THIS IS THE ODD-BALL OBJECT. I was forced to drop the "s" in "oauth_users"
//in order to achieve the proper json key of "user"
class oauth_user_model extends abstract_db_table_model {
	
	function __construct()
	{
		$this->table_name = str_replace("_model", "", get_class($this)) . "s";
		$this->primary_key = "username";
		$this->alias = "ou";
		$this->distinct_retrieval_key = "";

		$table_columns = array();
		array_push($table_columns, 
			new abstract_table_column_model("username", 
				table_column_data_type::Text, 
				null, 
				new abstract_condition_model(condition_operator::Equal), 
				true, 
				true, 
				null, 
				true));
		array_push($table_columns, 
			new abstract_table_column_model("first_name", table_column_data_type::Text, null, 
				new abstract_condition_model(condition_operator::In), 
				true, true, null, null));
		array_push($table_columns, 
			new abstract_table_column_model("last_name", table_column_data_type::Text, null,  
				new abstract_condition_model(condition_operator::In), 
				true, true, null, null));
		array_push($table_columns, 
			new abstract_table_column_model("password", table_column_data_type::Text, null,  
				null, null, true, null, null, null, true));
		$this->table_columns = $table_columns;
	}
	
	function __destruct()
	{
		parent::deinit($this);
	}
}

?>