<?php
/**
 * main entry point for all views
 * @author Nyein Chan Aung<developernca@gmail.com>
 */
?>
<html>
    <head>
		<?php
		echo js_tag(base_url() . "js/common/jquery-3.2.1.min.js");
		echo js_tag('https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js');
		echo js_tag(base_url() . 'js/common/common.js?rfk=' . time());
		echo '<script src="https://apis.google.com/js/platform.js" async defer></script>';
		if ($view === Constant::EACH_POST_VIEW) {
			echo js_tag(base_url() . 'js/common/epv.js');
		}
		if ($is_mobile) {
			echo link_tag(base_url() . 'css/mob/base.css?rfk' . time());
			//echo js_tag(base_url() . 'js/mob/base.js');
		} else {
			echo link_tag(base_url() . 'css/web/base.css?rfk' . time());
			//echo js_tag(base_url() . 'js/web/base.js');
		}
		// title bar icon
		echo link_tag(base_url() . 'usr/favicon.png', 'shortcut icon', 'image/png');
		$title = isset(${Constant::VDN_TITLE_KEY}) ? ${Constant::VDN_TITLE_KEY} : Constant::COMMON_TITLE;
		$meta_desc = isset(${Constant::VDN_META_DESC_KEY}) ? ${Constant::VDN_META_DESC_KEY} : Constant::COMMON_DESCRIPTION_META;
		?>
		<title><?php echo ${Constant::VDN_TITLE_KEY}; ?></title>
		<meta name="description" content="<?php echo ${Constant::VDN_META_DESC_KEY}; ?>"></meta>
    </head>
	<?php
	// set base url to use in javascript
	$disschange_listener_function = 'listenDiscussionChange(\'' . base_url() . '\')';
	?>
    <body onload="<?php echo $disschange_listener_function; ?>">
        <div id="id-div-maincontainer">
			<?php
			$this->load->view(Constant::HEADER_VIEW);
			if ($view === Constant::WELCOME_VIEW) {
				$this->load->view('common/' . $view);
			} else if ($view === Constant::CONFIRMATION_VIEW) {
				$this->load->view('common/' . $view);
			} else {
				$this->load->view('common/nav_view');
				$this->load->view('common/info_view');
				($is_mobile) ? $this->load->view('mob/social_view') : $this->load->view('web/social_view');
				if ($view !== Constant::GENERAL_VIEW) {
					$this->load->view('common/searchform_view');
				}
				($is_mobile) ? $this->load->view('mob/' . $view) : $this->load->view('web/' . $view);
			}
			?>
        </div>
    </body>
</html>