<?php

class Contact extends MY_Model {

	private $additional_data;

	public function __construct() {
		parent::__construct();
		$this->additional_data = [];
	}

	/**
	 * Create worker contact data.
	 *
	 * @param type $data data to insert
	 * @param type $worker_id worker id for foreign key
	 * @return type
	 */
	public function create($data, $worker_id) {
		$id = $this->create_unique_key_in_table(Constant::TABLE_WORKER_CONTACT, Constant::TABLE_WORKER_CONTACT_COLUMN_ID, 10, TRUE, TRUE);
		$this->additional_data[Constant::TABLE_WORKER_CONTACT_COLUMN_ID] = $id;
		$this->additional_data[Constant::TABLE_WORKER_CONTACT_COLUMN_WORKER_ID] = $worker_id;
		$table_data = array_merge($data, $this->additional_data);
		$this->map_field(Constant::TABLE_WORKER_CONTACT, $table_data);
		$could_insert = $this->db->insert(Constant::TABLE_WORKER_CONTACT, $table_data);
		return $could_insert ? $id : NULL;
	}

	/**
	 * Edit worker contact data
	 *
	 * @param array $data data to update
	 * @param string $worker_id worker id to update
	 * @return true on edit success, otherwise false
	 */
	public function edit($data, $worker_id) {
		$this->db->where(Constant::TABLE_WORKER_CONTACT_COLUMN_WORKER_ID, $worker_id);
		$this->map_field(Constant::TABLE_WORKER_CONTACT, $data);
		return $this->db->update(Constant::TABLE_WORKER_CONTACT, $data);
	}

}
