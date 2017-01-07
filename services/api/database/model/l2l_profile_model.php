<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

class l2l_profile_model extends abstract_db_table_model {
	
	function __construct()
	{
		$this->table_name = str_replace("_model", "", get_class($this));
		$this->primary_key = "profile_id";
		$this->alias = "p";
		$this->distinct_retrieval_key = "";

		$table_columns = array();
		array_push($table_columns, 
			new abstract_table_column_model("profile_id", table_column_data_type::Number, null, 
				new abstract_condition_model(condition_operator::In), 
				true, null, null, 
				true));
		array_push($table_columns, 
			new abstract_table_column_model("username", table_column_data_type::Text, null,  
				new abstract_condition_model(condition_operator::In), 
				true, true, null, null));
		array_push($table_columns, 
			new abstract_table_column_model("nickname", table_column_data_type::Text, null,  
				new abstract_condition_model(condition_operator::In), 
				true, true, null, null));
		array_push($table_columns, 
			new abstract_table_column_model("last_change_user_id", table_column_data_type::Number, null, null, null, null, null, null));
		array_push($table_columns, 
			new abstract_table_column_model("date_created", table_column_data_type::Number, null, null, null, null, null, null));
		array_push($table_columns, 
			new abstract_table_column_model("last_modified_date", table_column_data_type::Number, null, null, null, null, null, null));
		$this->table_columns = $table_columns;
	}
	
	function __destruct()
	{
		parent::deinit($this);
	}
}

?>