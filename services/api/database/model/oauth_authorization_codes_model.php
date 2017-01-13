<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

class oauth_authorization_codes_model extends abstract_db_table_model {
	
	function __construct()
	{
		$this->table_name = str_replace("_model", "", get_class($this));
		$this->primary_key = "authorization_code";
		$this->alias = "oac";
		$this->distinct_retrieval_key = "";

		$table_columns = array();
		array_push($table_columns, 
			new abstract_table_column_model("authorization_code", table_column_data_type::Text, null, 
				new abstract_condition_model(condition_operator::Equal), 
				true, null, null, 
				true));
		array_push($table_columns, 
			new abstract_table_column_model("client_id", table_column_data_type::Text, null, 
				new abstract_condition_model(condition_operator::Equal), 
				true, true, null, null));
		array_push($table_columns, 
			new abstract_table_column_model("user_id", table_column_data_type::Text, null,  
				new abstract_condition_model(condition_operator::Equal), 
				true, true, null, null));
		array_push($table_columns, 
			new abstract_table_column_model("redirect_uri", table_column_data_type::Text, null,  
				new abstract_condition_model(condition_operator::Equal), 
				true, true, null, null));
		array_push($table_columns, 
			new abstract_table_column_model("expires", table_column_data_type::Number, null,  
				new abstract_condition_model(condition_operator::Equal), 
				true, true, null, null));
		array_push($table_columns, 
			new abstract_table_column_model("scope", table_column_data_type::Text, null,  
				new abstract_condition_model(condition_operator::Equal), 
				true, true, null, null));
		$this->table_columns = $table_columns;
	}
	
	function __destruct()
	{
		parent::deinit($this);
	}
}

?>