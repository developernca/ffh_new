<?php
/**
 * Custom key generator for whole application.
 * 
 * @author Nyein Chan Aung<developernca@gmail.com>
 * 
 */
class KeyGenerator{
    /**
     * Generate a key. 
     * Generated key include A~B, a~b, 0~9 and optional
     * [php time() function value and php uniqid() value]
     * 
     * @param integer $key_length Key length
     * @param boolean $use_time add current time to key
     * @param boolean $use_uniqid add unique id to key
     */ 
    public static function getAlphaNumString($key_length, $use_time = false, $use_uniqid = false){
        $code = '';
        if($use_time){
            $code .= time();
        }
        $value = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$value .= 'abcdefghijklmnopqrstuvwxyz';
		$value .= '0123456789';
		$min = 0;
		$max = strlen ( $value ) - 1;
		for($i = 1; $i <= $key_length; $i ++) {
			$randomIndex = rand ( $min, $max );
			$code .= $value [$randomIndex];
		}
		if($use_uniqid){
		    $code .= uniqid();
		}
		return $code;
    }
}