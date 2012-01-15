<?php

/**
 * @author Benjamin Barbier
 *
 */
class WsiCommons {

	/**
	 * URL du plugin
	 */
	public static function getURL() {
		return WP_PLUGIN_URL.'/'.basename(dirname(__FILE__));
	}

	/**
	 * Retourne un tableau contenant la liste de toutes les options de WSI.
	 */
	public static function getOptionsList() {
		return array(
				'splash_active',
				'splash_test_active',
				'wsi_idle_time',
				'url_splash_image',
				'splash_image_width',
				'splash_image_height',
				'splash_color',
				'datepicker_start',
				'datepicker_end',
				'wsi_display_time',
				'wsi_fixed_splash',
				'wsi_picture_link_url',
				'wsi_picture_link_target',
				'wsi_close_esc_function',
				'wsi_hide_cross',
				'wsi_disable_shadow_border',
				'wsi_type',
				'wsi_opacity',
				'wsi_youtube',
				'wsi_youtube_autoplay',
				'wsi_youtube_loop',
				'wsi_yahoo',
				'wsi_dailymotion',
				'wsi_metacafe',
				'wsi_swf',
				'wsi_html');
	}
	
	/**
	 * Si la Splash Image n'est pas dans sa plage de validité, on retourne false (sinon true)
	 */
	public static function getdate_is_in_validities_dates() {
	
		$siBean = SplashImageManager::getInstance()->get();
		
		$today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
	
		// En cas de modication des paramètres dans la partie admin
		if ($_POST ['action'] == 'update') {
			if ($_POST['datepicker_start']!='') {
				$dpStart = strtotime($_POST['datepicker_start']);
				if ($today < $dpStart) {
					return "false";
				}
			}
			if ($_POST['datepicker_end']!='') {
				$dpEnd = strtotime($_POST['datepicker_end']);
				if ($today > $dpEnd) {
					return "false";
				}
			}
		// Sinon (front office)
		} else {
			if ($siBean->getDatepicker_start()!='') {
				$dpStart = strtotime($siBean->getDatepicker_start());
				if ($today < $dpStart) {
					return "false";
				}
			}
			if ($siBean->getDatepicker_end()!='') {
				$dpEnd = strtotime($siBean->getDatepicker_end());
				if ($today > $dpEnd) {
					return "false";
				}
			}
		}
		return "true";
	}
	
	/**
	 * Retourne true, si la période d'inactivité de l'utilisateur a été atteinte.
	 */
	public static function enough_idle_to_splash($lastSplash) {
		
		$siBean = SplashImageManager::getInstance()->get();
		
		// Si la variable n'est pas settée, c'est que l'utilisateur vient pour la 1ere fois.
		if (!isset($lastSplash)) return true;
		
		$endIdle = $lastSplash + ($siBean->getWsi_idle_time() * 60);
		if (time() > $endIdle) {
			return true;
		} else {
			return false;
		}
		
	}
	
	/**
	 * @return boolean, true if a new version of WSI exists
	 */
	public static function has_a_new_version() {
		
		$compare = version_compare(
				WsiCommons::getCurrentPluginVersion(),
				WsiCommons::getLastestPluginVersion());
		
		if ($compare == -1) {
			// Use old version
			return true;
		} else if ($compare == 0) {
			// Use last Version
			return false;
		} else if ($compare == 1) {
			// Use beta version
			return false; 
		}
		return false;
		
	}
	
	/**
	 * Generic function to show a message to the user using WP's
	 * standard CSS classes to make use of the already-defined
	 * message colour scheme.
	 *
	 * @param $message The message you want to tell the user.
	 * @param $errormsg If true, the message is an error, so use
	 * the red message style. If false, the message is a status
	 * message, so use the yellow information message style.
	 */
	public static function showMessage($message, $errormsg = false) {
		if ($errormsg) {
			echo '<div id="message" class="error">';
		}
		else {
			echo '<div id="message" class="updated fade">';
		}
		echo "<p><strong>$message</strong></p></div>";
	}
	
	/**
	 * Returns current plugin version.
	 * The information come from the wp-splash-image.php header comment.
	 *
	 * @return string current Plugin version
	 */
	function getCurrentPluginVersion() {

		$plugin_data = get_plugin_data( WP_PLUGIN_DIR."/wsi/wp-splash-image.php" );
		$plugin_version = $plugin_data['Version'];
		return $plugin_version;
		
	}
	
	/**
	 * Returns lastest plugin version.
	 *
	 * @return string lastest Plugin version
	 */
	public static function getLastestPluginVersion() {
		
		$current = get_site_transient( 'update_plugins' );
		$r = $current->response[ "wsi/wp-splash-image.php" ];
		return $r->new_version;
		
	}
	
}

?>