<?php

/**
 * S agent API.
 */
class Sagt extends MY_Controller {

	const ERR_FLG = -1;
	const OK_FLG = 0;

	public function __construct() {
		// helper, library, model, db
		parent::__construct(
				NULL
				, ['Constant', 'KeyGenerator', 'ImgUtils', 'Rabbit']
				, ['sagt/worker', 'sagt/WorkerSpec', 'sagt/contact', 'sagt/place']
				, 'sagt');
	}

	/**
	 * Create worker account.
	 */
	public function create_edit_worker() {
		if (!$this->is_post()) {
			return;
		}
		$post_param = $this->input->post();
		if (isset($post_param['id'])) {// edit account
			$id_to_edit = $post_param['id'];
			unset($post_param['id']);
			$could_edit_worker = $this->worker->edit($post_param, $id_to_edit);
			if (!$could_edit_worker) {
				$this->return_data_to_client(json_encode(['flag' => self::ERR_FLG, 'data' => 'W404']));
				return;
			}
			if (isset($post_param['photo_data'])) {
				unset($post_param['photo_data']);
			}
			$could_edit_workerspec = $this->WorkerSpec->edit($post_param, $id_to_edit);
			$could_edit_place = $this->place->edit($post_param, $id_to_edit);
			$could_edit_contact = $this->contact->edit($post_param, $id_to_edit);
			if ($could_edit_workerspec && $could_edit_place && $could_edit_contact) {
				$this->return_data_to_client(json_encode(['flag' => self::OK_FLG, 'data' => NULL]));
			} else {
				$this->return_data_to_client(json_encode(['flag' => self::ERR_FLG, 'data' => NULL]));
			}
		} else {// create account
			$worker_id = $this->worker->create($post_param);
			if (is_null($worker_id)) {
				$this->return_data_to_client(json_encode(['flag' => self::ERR_FLG, 'data' => NULL]));
				return;
			}
			if (isset($post_param['photo_data'])) {
				unset($post_param['photo_data']);
			}
			$worker_spec_id = $this->WorkerSpec->create($post_param, $worker_id);
			$place_id = $this->place->create($post_param, $worker_id);
			$contact_id = $this->contact->create($post_param, $worker_id);
			if (!is_null($worker_spec_id) && !is_null($place_id) && !is_null($contact_id)) {
				$this->return_data_to_client(json_encode(['flag' => self::OK_FLG, 'data' => $worker_id]));
			} else {// some errors occured
				$this->return_data_to_client(json_encode(['flag' => self::ERR_FLG, 'data' => NULL]));
			}
		}
	}

	/**
	 * Find worker.
	 */
	public function find_worker() {
		if (!$this->is_post()) {
			return;
		}
		$find_result = $this->worker->find($this->input->post());
		$this->return_data_to_client(json_encode(array('msg' => $find_result)));
	}

	/**
	 * return response data to client
	 *
	 * @param array $data JSON response data
	 */
	private function return_data_to_client($response_data) {
		$this->output->set_content_type('application/json', 'utf8');
		$this->output->set_header('HTTP/1.0 200 OK');
		$this->output->set_header('HTTP/1.0 200 OK');
		$this->output->set_header('Cache-Control:no-store, no-cache, must-revalidate');
		$this->output->set_output($response_data);
	}

}
