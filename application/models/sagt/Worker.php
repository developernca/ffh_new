<?php

class Worker extends MY_Model {

	private static $slash = DIRECTORY_SEPARATOR;
	private $additional_data;

	public function __construct() {
		parent::__construct();
		$this->additional_data = [];
	}

	/**
	 * Create new worker
	 *
	 * @param array $data data to insert
	 * @return string inserted id on success or false on failure
	 */
	public function create($data) {
		$id = $this->create_unique_key_in_table(Constant::TABLE_WORKER, Constant::TABLE_WORKER_COLUMN_ID, 10, TRUE, TRUE);
		$password = password_hash(KeyGenerator::getAlphaNumString(4), PASSWORD_DEFAULT);
		$this->additional_data[Constant::TABLE_WORKER_COLUMN_WORKER_USABLE_ID] = $this->create_unique_key_in_table(Constant::TABLE_WORKER, Constant::TABLE_WORKER_COLUMN_WORKER_USABLE_ID, 5);
		$this->additional_data[Constant::TABLE_WORKER_COLUMN_ID] = $id;
		$this->additional_data[Constant::TABLE_WORKER_COLUMN_PASSWORD] = $password;
		// create direcotry for user whether photo_data exist or not, so there is no need
		// to [call mkdir]create/make folder in edit action.
		$root_path = FCPATH . Constant::ROOT_SAGT . self::$slash;
		if (!file_exists($root_path)) {
			mkdir($root_path);
		}
		mkdir($root_path . self::$slash . $id);
		if ($data['msa_app'] === 'mm' && $data['font'] === 'zawgyi') {
			$data['name'] = Rabbit::zg2uni($data['name']);
		}
		if (isset($data['photo_data'])) {
			$this->handlePhotoData($data['photo_data'], $id);
		} else {
			$this->additional_data[Constant::TABLE_WORKER_COLUMN_PHOTO_PATH] = NULL;
			$this->additional_data[Constant::TABLE_WORKER_COLUMN_THUMBNAIL] = NULL;
		}
		$table_data = array_merge($data, $this->additional_data);
		$this->map_field(Constant::TABLE_WORKER, $table_data);
		$could_insert = $this->db->insert(Constant::TABLE_WORKER, $table_data);
		return $could_insert ? $id : NULL;
	}

	/**
	 * Edit worker
	 *
	 * @param array $data data to update
	 * @param string $id worker id to update
	 * @return boolean true on success, false on failure
	 */
	public function edit($data, $id) {
		$query = $this->db->get_where(Constant::TABLE_WORKER, array(Constant::TABLE_WORKER_COLUMN_ID => $id));
		$result = $query->result();
		if (empty($result)) {// invalid id
			return false;
		}
		if ($data['msa_app'] === 'mm' && isset($data['font']) && $data['font'] === 'zawgyi') {
			$data['name'] = Rabbit::zg2uni($data['name']);
		}
		if (isset($data['photo_data'])) {
			$this->handlePhotoData($data['photo_data'], $id);
		}
		$table_data = array_merge($data, $this->additional_data);
		$this->map_field(Constant::TABLE_WORKER, $table_data);
		$this->db->where(Constant::TABLE_WORKER_COLUMN_ID, $id);
		return $this->db->update(Constant::TABLE_WORKER, $table_data);
	}

	/**
	 * create or edit worker photo.
	 *
	 * @param string $photo_string base64 encoded photo string data
	 * @param type $id worker id for folder name
	 */
	private function handlePhotoData($photo_string, $id) {
		$original_img_path = FCPATH . Constant::ROOT_SAGT . self::$slash . $id . self::$slash . Constant::ORIGINAL_IMG;
		$full_size_img_path = Constant::ROOT_SAGT . self::$slash . $id . self::$slash . Constant::WORKER_FULL_IMG; // use in table
		$thumbnail_img_path = Constant::ROOT_SAGT . self::$slash . $id . self::$slash . Constant::WORKER_THUMBNAIL; // use in table
		ImgUtils::create_img_file_from_binary_data(base64_decode($photo_string), $original_img_path);
		ImgUtils::crop_image($original_img_path, FCPATH . $full_size_img_path, 500, 500);
		ImgUtils::crop_image($original_img_path, FCPATH . $thumbnail_img_path, 150, 150);
		$this->additional_data[Constant::TABLE_WORKER_COLUMN_PHOTO_PATH] = $full_size_img_path;
		$this->additional_data[Constant::TABLE_WORKER_COLUMN_THUMBNAIL] = $thumbnail_img_path;
		// after handling all photo data, delete original photo [i.e, photo that user uploaded]
		unlink($original_img_path);
	}

	/**
	 * Find worker
	 *
	 * @param array $params search keys
	 */
	public function find($params) {
		$this->db->limit(10, (int) $params['pagination']);
		$this->db->select('w.id, w.name, w.photo_path, w.thumbnail_path, w.age, w.gender, w.price, w.other, c.phone, p.country_part_id, ws.font, ws.msa_app ');
		$this->db->from('worker AS w');
		$this->db->join('place AS p', 'w.id = p.worker_id', 'LEFT');
		$this->db->join('contact AS c', 'w.id = c.worker_id', 'LEFT');
		$this->db->join('worker_spec AS ws', 'w.id = ws.worker_id', 'LEFT');
		// all input post_parameters
		$name = $params['name'];
		$from_age = $params['from_age'];
		$to_age = $params['to_age'];
		$from_price = $params['from_price'];
		$to_price = $params['to_price'];
		$gender = $params['gender'];
		$country_part = $params['country_part'];
		$phone = $params['phone'];
		// table join functions
		// name condition
		if ($params['msa_app'] === 'mm' && $params['font'] === 'zawgyi') {
			$name = Rabbit::zg2uni($name);
		}
		if (!empty($name)) {
			$this->db->like('w.name', $name, 'both');
		}
		// age condition
		if (!empty($from_age) && !empty($to_age)) {
			$this->db->where(['w.age >= ' => $from_age, 'w.age <= ' => $to_age]);
		} else if (!empty($from_age)) {
			$this->db->where(['w.age >= ' => $from_age]);
		} else if (!empty($to_age)) {
			$this->db->where(['w.age <= ' => $to_age]);
		}
		// price condition
		if (!empty($from_price) && !empty($to_price)) {
			$this->db->where(['w.price >= ' => $from_price, 'w.price <= ' => $to_price]);
		} else if (!empty($from_price)) {
			$this->db->where(['w.price >= ' => $from_price]);
		} else if (!empty($to_price)) {
			$this->db->where(['w.price <= ' => $to_price]);
		}
		if ($country_part !== '0000') {
			$this->db->where(['p.country_part_id = ' => $country_part]);
		}
		// phone condition
		if (!empty($phone)) {
			$this->db->where(['c.phone = ' => $phone]);
		}
		// no need to check because gender will
		// always have to be a value and they are default search keys.
		$this->db->where(['w.gender = ' => $gender]);
		$query = $this->db->get();
		$result = $query->result();
		if (count($result) > 0) {
			foreach ($result as &$value) {
				if ($value->font == 'zawgyi') {
					$value->name = Rabbit::uni2zg($value->name);
				}
				$value->price = number_format((float) $value->price);
				if (!is_null($value->photo_path)) {
					$value->photo_path = base_url() . str_replace('\\', '/', $value->photo_path);
				}
				if (!is_null($value->thumbnail_path)) {
					$value->thumbnail_path = base_url() . str_replace('\\', '/', $value->thumbnail_path);
				}
			}
			return $result;
		} else {
			return NULL;
		}
	}

}
