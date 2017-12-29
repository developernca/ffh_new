<?php

class ImgUtils {

	/**
	 * Crop image of existing file and write new image to desired output file.
	 *
	 * @param string $input_file full image file path to read in
	 * @param string $output_file full image file path to write out
	 * @param int $desired_width desired width
	 * @param int $desired_height desired height
	 * @return boolean true on success, false on failure
	 */
	public static function crop_image($input_file, $output_file, $desired_width, $desired_height) {
		$original_image = imagecreatefromjpeg($input_file);
		$cropped_img = imagecreatetruecolor($desired_width, $desired_height);
		imagealphablending($cropped_img, FALSE);
		imagesavealpha($cropped_img, TRUE);
		imagecopyresampled($cropped_img, $original_image, 0, 0, 0, 0, $desired_width, $desired_height, imagesx($original_image), imagesy($original_image));
		// $file_name = FCPATH . DIRECTORY_SEPARATOR . 'temp/' . time() . '.jpeg';
		$could_file_open = fopen($output_file, 'w+');
		if ($could_file_open !== FALSE) {
			return imagejpeg($cropped_img, $output_file);
		}
		return false;
	}

	/**
	 * Create image file from raw binary data.
	 *
	 * @param string $raw_binary_data Base64 decoded string
	 * @param string $output_file_path file name with full file path
	 * @return boolean true on success false on failure
	 */
	public static function create_img_file_from_binary_data($raw_binary_data, $output_file_path) {
		$file_handle = fopen($output_file_path, 'w+');
		if ($file_handle === FALSE) {
			return FALSE;
		}
		fwrite($file_handle, $raw_binary_data);
	}

}
