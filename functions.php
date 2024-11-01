<?php

//REQUEST language based on user
$mylink = $wpdb->get_row("SELECT langid FROM ".$wpdb->prefix."vcos_user_locales WHERE userid ='$userid'");
$query = $wpdb->get_row("SELECT file FROM ".$wpdb->prefix."vcos_locale WHERE id=".$mylink->langid."");
require_once("locale/".$query->file.".php");
				//usage: echo lang('NO_PHOTO');
?>