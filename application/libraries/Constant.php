<?php

/**
 * Constants for the whole application.
 */
class Constant {

	// ===== View name constants =====
	const WELCOME_VIEW = 'welcome_view';
	const HEADER_VIEW = 'header_view';
	const CONFIRMATION_VIEW = 'confirmation_view';
	const HOME_VIEW = 'home_view';
	const MY_POST_VIEW = 'mypost_view';
	const EACH_POST_VIEW = 'each_post_view';
	const GENERAL_VIEW = 'general_view';
	const SEARCH_VIEW = 'search_view';
	// ===== Session name constants =====
	const SESSION_USSID = 'sussid';
	const SESSION_EMAIL = 'email';
	const SESSION_CURRENTUSER_NAME = 'cu_name';
	const SESSION_SEARCH_KEY_STRING = 'search_key';
	const SESSION_SEARCH_TYPE = 'search_type';
	// ===== AUTHANTICATION CODE =====
	const AUTH_ALREADY_LOGIN = 'alr_lgin'; // already logged in [authentication OK]
	const AUTH_SESSION_NOT_EXIST = 'no_sess'; // session not exist
	const AUTH_ACTIVATION_REQUIRED = 'act_req'; // activation require
	// ===== ERROR MESSAGES =====
	const ERR_EMAIL_FORMAT = 'Incorrect email format';
	const ERR_SIGNUP_EMAIL_EXIST = 'The email address is already registered';
	const ERR_SIGNUP_PASS_LENTH = 'Password must be at least 6 characters';
	const ERR_SIGNUP_PASS_MISMATCH = 'Password did not match';
	const ERR_EMAIL_BLANK = 'Email cannot be blank';
	const ERR_EMAIL_EXCEED_LENGTH = 'Email cannot exceed 200 characters';
	const ERR_EMAIL_NOT_EXIST = 'This email address is not registered';
	const ERR_PASSWORD_BLANK = 'Password cannot be blank';
	const ERR_PASSWORD_LENGTH = 'Password must be greater than 5 characters';
	const ERR_BLANK_ACTCODE = 'Please enter activation code.';
	const ERR_LONGER_ACTCODE = 'Activation code has to be excatly 6 characters';
	const ERR_ACTVCODE_MISMATCH = 'Activation code did not match';
	const ERR_ACTVCODE_UPDATE = 'Sorry, unexpected error occured during activation process. Try again or click resend link.';
	const ERR_NAME_MINLENGTH = 'Name must be at least 3 chracters';
	const ERR_NAME_MAXLENGTH = 'Name cannot be greater than 200 characters';
	const ERR_NAME_BLANK = 'Account name cannot be empty';
	const ERR_PASS_MISMATCH_OLD = 'Your current password is incorrect.';
	// ===== NAME ATTRIBUTE CONSTANTS FOR HTML TAG =====
	// name for input text field
	const NAME_TEXT_SIGNIN_FORM_EMAIL = 'sif_email';
	const NAME_TEXT_SIGNUP_FORM_EMAIL = 'suf_email';
	const NAME_TEXT_ACTIVATION_CODE = 'actv_code';
	const NAME_TEXT_RESEND_EMAIL = 're_email';
	const NAME_TEXT_POST_TITLE = 'post_title';
	const NAME_TEXT_POST_CONTENT = 'post_content';
	const NAME_TEXT_CONTACT_EMAIL = 'contact_email';
	const NAME_TEXT_CONTACT_PHONE = 'contact_phone';
	const NAME_TEXT_POST_REMARK = 'remark';
	const NAME_TEXT_CURRENT_NAME = 'current_name';
	const NAME_TEXT_CURRENT_EMAIL = 'current_email';
	const NAME_TEXT_SEARCH_KEY = 'search_key';
	// name for input password field
	const NAME_PASS_SIGNIN_FORM_PASSWORD = 'sif_pass';
	const NAME_PASS_SIGNUP_FORM_PASSWORD = 'suf_passs';
	const NAME_PASS_SIGNUP_FORM_REPASSWORD = 'suf_repass';
	const NAME_PASS_CURRENT_PASSWORD = 'current_pass';
	const NAME_PASS_NEW_PASSWORD = 'new_pass';
	const NAME_PASS_NEW_REPASSWORD = 'new_repass';
	// name for input button field
	const NAME_BTN_SIGNIN_FORM = 'sif_btn';
	const NAME_BTN_SIGNUP_FORM = 'suf_btn';
	// name for checkbox
	const NAME_CHECKBOX_PASSCHANGE = 'pass_change_check';
	// name for hidden field
	const NAME_HIDDEN_SIGNUP_TIME = 'suf_usr_time';
	const NAME_HIDDEN_POST_CREATEDAT = 'pc_time';
	const NAME_HIDDEN_POST_UPDATEDAT = 'pc_time';
	const NAME_HIDDEN_POST_ID = 'pid';
	// name for submit button
	const NAME_SUBMIT_GENERAL_ACCOUNT = 'submit_acc';
	// name for select field
	const NAME_SELECT_POST_TYPE = 'type';
	// link param name
	const LINK_PARAM_RESEND_CONCODE = 'rscc';
	// ============== DATABASE NAME ===============================
	const DB_SEX_AGENT = 'sagt';
	// ============== DATABSE TABLES AND COLUMN NAME ==============
	// ACCOUNTS TABLE
	const TABLE_ACCOUNTS = 'accounts';
	const TABLE_ACCOUNTS_COLUMN_ID = '_id';
	const TABLE_ACCOUNTS_COLUMN_EMAIL = 'email';
	const TABLE_ACCOUNTS_COLUMN_NAME = 'name';
	const TABLE_ACCOUNTS_COLUMN_PASSWORD = 'password';
	const TABLE_ACCOUNTS_COLUMN_CREATEDTIME = 'created_time';
	const TABLE_ACCOUNTS_COLUMN_NUMBEROF_USER = 'number_of_user';
	const TABLE_ACCOUNTS_COLUMN_IS_ACTIVATED = 'is_activated';
	const TABLE_ACCOUNTS_COLUMN_ACTIVATION_CODE = 'activation_code';
	// POSTS TABLE
	const TABLE_POSTS = 'posts';
	const TABLE_POSTS_COLUMN_ID = '_id';
	const TABLE_POSTS_COLUMN_POST_TITLE = 'post_title';
	const TABLE_POSTS_COLUMN_TEXT_FILENAME = 'post_text_file_name';
	const TABLE_POSTS_COLUMN_CONTACT_EMAIL = 'contact_email';
	const TABLE_POSTS_COLUMN_CONTACT_PHONE = 'contact_phone';
	const TABLE_POSTS_COLUMN_REMARK = 'remark';
	const TABLE_POSTS_COLUMN_TYPE = 'type';
	const TABLE_POSTS_COLUMN_POSTED_TIME = 'posted_time';
	const TABLE_POSTS_COLUMN_UPDATED_TIME = 'updated_time';
	const TABLE_POSTS_COLUMN_ACCOUNT_ID = 'account_id';
	// DISCUSSIONS TABLE
	const TABLE_DISCUSSIONS = 'discussions';
	const TABLE_DISCUSSION_COLUMN_ID = '_id';
	const TABLE_DISCUSSION_COLUMN_FILENAME = 'filename';
	const TABLE_DISCUSSION_COLUMN_DISCUSSEDBY = 'discussed_by';
	const TABLE_DISCUSSION_COLUMN_UPDATEDAT = 'updated_at';
	const TABLE_DISCUSSION_COLUMN_SEEN = 'seen';
	const TABLE_DISCUSSION_COLUMN_POST_ID = 'post_id';
	// WORKER TABLE
	const TABLE_WORKER = 'worker';
	const TABLE_WORKER_COLUMN_ID = 'id';
	const TABLE_WORKER_COLUMN_WORKER_USABLE_ID = 'worker_usable_id';
	const TABLE_WORKER_COLUMN_PASSWORD = 'password';
	const TABLE_WORKER_COLUMN_NAME = 'name';
	const TABLE_WORKER_COLUMN_PHOTO_PATH = 'photo_path';
	const TABLE_WORKER_COLUMN_THUMBNAIL = 'thumbnail_path';
	const TABLE_WORKER_COLUMN_AGE = 'age';
	const TABLE_WORKER_COLUMN_GENDER = 'gender';
	const TABLE_WORKER_COLUMN_PRICE = 'price';
	const TABLE_WORKER_COLUMN_OTHER = 'other';
	const TABLE_WORKER_COLUMN_CREATEAT = 'created_at';
	const TABLE_WORKER_COLUMN_UPDATEDAT = 'updated_at';
	// WORKER SPEC TABLE
	const TABLE_WORKER_SPEC = 'worker_spec';
	const TABLE_WORKER_SPEC_COLUMN_ID = 'id';
	const TABLE_WORKER_SPEC_COLUMN_font = 'font';
	const TABLE_WORKER_SPEC_COLUMN_MSAAPP = 'msa_app';
	const TABLE_WORKER_SPEC_COLUMN_WORKER_ID = 'worker_id';
	// PLACE TABLE
	const TABLE_WORKER_PLACE = 'place';
	const TABLE_WORKER_PLACE_COLUMN_ID = 'id';
	const TABLE_WORKER_PLACE_COLUMN_COUNTRY_PART_ID = 'country_part_id';
	const TABLE_WORKER_PLACE_COLUMN_WORKER_ID = 'worker_id';
	// CONTACT TABLE
	const TABLE_WORKER_CONTACT = 'contact';
	const TABLE_WORKER_CONTACT_COLUMN_ID = 'id';
	const TABLE_WORKER_CONTACT_COLUMN_PHONE = 'phone';
	const TABLE_WORKER_CONTACT_COLUMN_WORKER_ID = 'worker_id';
	// =============== VIEW DATA KEY NAME ===================
	const VDN_POST_TYPES_OPTIONS = 'vdn_post_types_options';
	const VDN_CURRENTUSER_POST_LISTS = 'vdn_current_user_post_lists';
	const VDN_ALL_POSTS = 'vdn_all_posts';
	const VDN_PAGINATION_LINK = 'links';
	const VDN_SESSION_EMAIL = 'vdn_email';
	const VDN_TOTAL_POSTS_COUNT = 'total_post_counts';
	const VDN_EACH_POST = 'each_post';
	const VDN_DISCUSSION_LIST_EACH = 'discussion_list';
	const VDN_CURRENT_USRNAME = 'current_user_name';
	const VDN_CURRENT_USREMAIL = 'current_user_email';
	const VDN_ACCFORM_ERRMSG = 'accorm_errmsg';
	const VDN_IS_SEARCH = 'search';
	const VDN_SEARCHED_SELECT = 'searched_selected';
	const VDN_SEARCHED_KEY = 'searched_key';
	const VDN_TITLE_KEY = 'title';
	const VDN_META_DESC_KEY = 'meta_desc';
	// =============== DIRECTORY AND FILE CONSTANT ===========
	const ROOT_SAGT = 'sagt';
	const ROOT_WORKER = 'worker';
	const ORIGINAL_IMG = 'original.jpeg';
	const WORKER_FULL_IMG = 'full.jpeg';
	const WORKER_THUMBNAIL = 'thumbnail.jpeg';
	const WORKER_DEFAULT_THUMNAIL_PATH = self::ROOT_SAGT . '/usr/img/' . self::WORKER_THUMBNAIL;
	const WORKER_DEFAULT_FULL_IMG_PATH = self::ROOT_SAGT . '/usr/img/' . self::WORKER_FULL_IMG;
	// text
	const TEXT_FORGET_PASSWORD_LINK = 'Forgot password?';
	// Type options array
	const POST_TYPE_OPTIONS_ARR = [
		'IT_Computing',
		'Politic',
		'Business',
		'Other',
		'Art',
		'Research',
		'Engineering'
	];
	// mail subject
	const ACTIVATION_MAIL_SUBJECT = 'Activation code for your ffh account';
	const ACTIVATION_MAIL_BODY = '<HTML><HEAD>Acivation code</HEAD><BODY><PRE>Thank you for creating FFH accounts. <BR>Recommend you to copy and paste the code. <BR>Some characters (I,l,1,0,O,D) are easy to wrong.<BR>Below is your activation code.<BR><BR>%s</PRE></BODY></HTML>';
	const MAIL_HEADER = 'Content-type: text/html; charset=iso-8859-1;From: contact@theffh.com; Reply-to: contact@theffh.com;';
	const PASSRESET_MAIL_BODY = '<HTML><HEAD>New password</HEAD><BODY><PRE>Dear user,<BR>Following is your new password.<BR>Now, you can login with this password. <BR> Since this is a system generated password, please, change it immidiately after logging in.<BR><BR><BR>%s</PRE></BODY></HTML>';
	const USER_COMPLAINT = 'Since ffh is in beta version, may arise unexpected errors. If you do not know how to use this website, have some trouble while using this website or whatever, please let me know. Sorry for any inconveniences.Feel free to mail me.';
	const MAIL_SENDER = '-fcontact@theffh.com';
	// ====================== title and meta description =============================
	// for common use
	const COMMON_TITLE = 'Find partner for job';
	const COMMON_DESCRIPTION_META = 'FFH provide you to find partners for your job that cannot be done by only you.';
	// for welcome
	const WELCOME_TITLE = self::COMMON_TITLE;
	const WELCOME_DESCRIPTION_META = 'FFH provide you to find partners for your job that cannot be done by only you. Create an account to start finding the partners.';
	// for home
	const HOME_TITLE = 'Submit post or search to get partners';
	const HOME_DESCRIPTION_META = 'FFH provide you to find partners for your job that cannot be done by only you. Submit your needs to see other people around the world and you can get some help or partners or some suggestions. You can also search partner who need help.';
	// for my post
	const MYPOST_TITLE = self::HOME_TITLE;
	const MYPOST_DESCRIPTION_META = self::HOME_DESCRIPTION_META;
	// for general
	const GENERAL_TITLE = self::COMMON_TITLE;
	const GENERAL_DESCRIPTION_META = self::COMMON_DESCRIPTION_META;

	/**
	 * Get database configuration array depend on name.
	 *
	 * @param string $name database name
	 * @return array return db config array if name exist, otherwise return null
	 */
	public static function get_db_config($name) {
		$config['sagt'] = [
			'dsn' => 'mysql:host=localhost;port=3306;dbname=' . $name,
			'hostname' => 'localhost',
			'username' => 'root',
			'password' => '',
			'database' => '',
			'dbdriver' => 'pdo',
			'dbprefix' => '',
			'pconnect' => FALSE,
			'db_debug' => (ENVIRONMENT !== 'production'),
			'cache_on' => FALSE,
			'cachedir' => '',
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci',
			'swap_pre' => '',
			'encrypt' => FALSE,
			'compress' => FALSE,
			'stricton' => FALSE,
			'failover' => array(),
			'save_queries' => TRUE
		];

		foreach ($config as $key => $value) {
			if ($key === $name) {
				return $value;
			}
		}
		return NULL;
	}

}
