<?php

/**
 * Base class for all model classes.
 *
 * @author Nyein Chan Aung<developernca@gmail.com>
 */
class MY_Model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Generate unique key.
	 *
	 * @param string $table_name table name
	 * @param string $column_name key column name
	 * @param integer $key_length primary key length
	 * @param boolean $use_time_value use current time value in primary key
	 * @param boolean $use_unique_id use unique id in primary key
	 * @return string unique key
	 */
	protected function create_unique_key_in_table($table_name, $column_name, $key_length, $use_time_value = FALSE, $use_unique_id = FALSE) {
		do {
			$key = KeyGenerator::getAlphaNumString($key_length, $use_time_value, $use_unique_id);
			$query = $this->db->get_where($table_name, [$column_name => $key]);
			$result = $query->result();
		} while (!empty($result));
		return $key;
	}

	/**
	 * Map array with colum name of given table.
	 * 
	 * @param string $table_name table name
	 * @param array $source_array an associative array
	 */
	protected function map_field($table_name, &$source_array) {
		$table_field_list = $this->db->list_fields($table_name);
		foreach ($source_array as $source_key => $source_value) {
			$found = FALSE;
			foreach ($table_field_list as $column_name) {
				if ($source_key === $column_name) {
					$found = TRUE;
					break;
				}
			}
			if (!$found) {
				unset($source_array[$source_key]);
			}
		}
	}

}
