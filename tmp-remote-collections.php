<?php
/**
 * Plugin Name: The Museum Platform Remote Collections
 * Plugin URI: https://themuseumplatform.com/
 * Description: Functionality to pull in TMP collections into any WordPress site
 * Version: 0.1
 * Author: Jeremy Ottevanger / The Museum Platform
 */

require_once('tmp-remote-collections.class.php');
$tmpRCSettings = [];
if (file_exists(__DIR__ .'/tmp-remote-collections-settings.php' )){
    require_once('tmp-remote-collections-settings.php');
}else{
    $tmpRCSettings = [];
}
$tmpRC = new \TmpRest\TmpRestCollection($tmpRCSettings);

 
?>