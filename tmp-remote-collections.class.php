<?php
namespace TmpRest;

class TmpRestCollection
{
    private $plugin_directory;
    private $plugin_url;
    private $search_results_path;
    private $single_record_path;
    private $rest_route;
    private $iiif_path;
    private $media_path;

    function __construct($tmpRCSettings)
    {
        $this->plugin_directory = WP_PLUGIN_DIR . '/tmp-remote-collections';
        $this->plugin_url = plugins_url() . '/tmp-remote-collections';
        // search_results_default_empty - do a search when there's no querystring? There's no UI for this setting
        if(isset($tmpRCSettings[''])){  
            $this->search_results_default_empty .= dirname( __FILE__ ) . $tmpRCSettings['search_results_default_empty'];
        }else{
            $this->search_results_default_empty = true;
        }
        // single_record_template
        if(null!==get_option('tmp_remote_single_record_template') && get_option('tmp_remote_single_record_template')!==""){
            $this->single_record_template = get_stylesheet_directory()."/".get_option('tmp_remote_single_record_template');
        }elseif(isset($tmpRCSettings['single_record_template'])){
            $this->single_record_template .= dirname( __FILE__ ) . $tmpRCSettings['single_record_template'];
        }else{
            $this->single_record_template = "";
        }
        // search_results_template
        if(null!==get_option('tmp_remote_search_results_template') && get_option('tmp_remote_search_results_template')!==""){
             $this->search_results_template = get_stylesheet_directory()."/".get_option('tmp_remote_search_results_template');
        }elseif(isset($tmpRCSettings['search_results_template'])){
            $this->search_results_template .= dirname( __FILE__ ) . $tmpRCSettings['search_results_template'];
        }else{
            $this->search_results_template = "";
        }
        // rest_route
        if(null!==get_option('tmp_remote_rest_route')){
            $this->rest_route = get_option('tmp_remote_rest_route');
        }elseif(isset($tmpRCSettings['rest_route'])){
            $this->rest_route .= dirname( __FILE__ ) . $tmpRCSettings['rest_route'];
        }else{
            $this->rest_route = "https://appstg.themuseumplatform.com/demo/wp-json/tmp/v1/collections/";
        }        
        // tmp_remote_media_path. There's no tmpRCSettings setting for this
        if(null!==get_option('tmp_remote_media_path')){
            $this->media_path = get_option('tmp_remote_media_path');
        }else{
            $this->media_path = "";
        }        
        // tmp_remote_iiif_path
        if(null!==get_option('tmp_remote_iiif_path')){
            $this->iiif_path = get_option('tmp_remote_iiif_path');
        }else{
            $this->iiif_path = "";
        }        
        // search_results_path
        if(null!==get_option('tmp_remote_search_results_path')){
            $this->search_results_path = get_option('tmp_remote_search_results_path');
        }elseif(isset($tmpRCSettings['search_results_path'])){
            $this->search_results_path .= dirname( __FILE__ ) . $tmpRCSettings['rest_rsearch_results_pathoute'];
        }else{
            $this->search_results_path = "";
        }
        // single_record_path
        if(null!==get_option('tmp_remote_single_record_path')){
            $this->single_record_path = get_option('tmp_remote_single_record_path');
        }elseif(isset($tmpRCSettings['search_results_path'])){
            $this->single_record_path .= dirname( __FILE__ ) . $tmpRCSettings['single_record_path'];
        }else{
            $this->single_record_path = "";
        }
        add_action('init', array($this, 'register_permalinks'));
        add_filter('the_content', array($this, 'construct_collection_content'));  
        add_filter('document_title_parts', array($this, 'construct_collection_title'));  
        add_action('parse_request', array($this, 'parse_request'), 20);
        add_action('admin_menu', array($this, 'add_menu_item'));
        add_action('admin_init', array($this, 'register_settings'));
    }

//=====================================

function validate_url($url)
{
    return filter_var($url, FILTER_VALIDATE_URL) ? $url : '';
}
function register_settings()
{
    add_settings_section('tmp_remote_collections_remote_settings', 'Connection Settings', array($this, 'generate_settings_group_content'), 'tmp_remote_collections_settings');

    register_setting('tmp_remote_collections_settings', 'tmp_remote_rest_route', ['sanitize_callback' => array($this, 'validate_url')]);
    add_settings_field('tmp_remote_rest_route', 'Remote REST route. ', array($this, 'generate_settings_field_input_text'), 'tmp_remote_collections_settings', 'tmp_remote_collections_remote_settings', array('field' => 'tmp_remote_rest_route','default'=>"https://appstg.themuseumplatform.com/<CHANGE FOR YOUR SITE>/wp-json/tmp/v1/collections/"));
    register_setting('tmp_remote_collections_settings', 'tmp_remote_media_path', ['sanitize_callback' => array($this, 'validate_url')]);
    add_settings_field('tmp_remote_media_path', 'Base URL for images. If unset then the path returned with the REST response will be used.', array($this, 'generate_settings_field_input_text'), 'tmp_remote_collections_settings', 'tmp_remote_collections_remote_settings', array('field' => 'tmp_remote_media_path'));
    register_setting('tmp_remote_collections_settings', 'tmp_remote_iiif_path', ['sanitize_callback' => array($this, 'validate_url')]);
    add_settings_field('tmp_remote_iiif_path', 'Base URL for IIIF images. If unset then the path returned with the REST response will be used.', array($this, 'generate_settings_field_input_text'), 'tmp_remote_collections_settings', 'tmp_remote_collections_remote_settings', array('field' => 'tmp_remote_iiif_path'));


    add_settings_section('tmp_remote_collections_local_settings', 'Local setup', array($this, 'generate_settings_group_content'), 'tmp_remote_collections_settings');

    register_setting('tmp_remote_collections_settings', 'tmp_remote_search_results_path');
    register_setting('tmp_remote_collections_settings', 'tmp_remote_single_record_path');
    register_setting('tmp_remote_collections_settings', 'tmp_remote_search_results_template');
    register_setting('tmp_remote_collections_settings', 'tmp_remote_single_record_template');
    add_settings_field('tmp_remote_search_results_path', 'Path to page for search results', array($this, 'generate_settings_field_input_text'), 'tmp_remote_collections_settings', 'tmp_remote_collections_local_settings', array('field' => 'tmp_remote_search_results_path'));
    add_settings_field('tmp_remote_single_record_path', 'Path to page for item records', array($this, 'generate_settings_field_input_text'), 'tmp_remote_collections_settings', 'tmp_remote_collections_local_settings', array('field' => 'tmp_remote_single_record_path'));
    add_settings_field('tmp_remote_search_results_template', 'Override template for search results. This is relative to your currently active (sub)theme directory', array($this, 'generate_settings_field_input_text'), 'tmp_remote_collections_settings', 'tmp_remote_collections_local_settings', array('field' => 'tmp_remote_search_results_template','default'=>''));
    add_settings_field('tmp_remote_single_record_template', 'Override template for item records. This is relative to your currently active (sub)theme directory', array($this, 'generate_settings_field_input_text'), 'tmp_remote_collections_settings', 'tmp_remote_collections_local_settings', array('field' => 'tmp_remote_single_record_template'));
}

function add_menu_item()
{
    add_options_page('The Museum Platform remote collections configuration', 'TMP Remote Collections', 'manage_options', 'tmp_objects', array($this, 'generate_settings_page'));
}

function generate_settings_page()
{
    include($this->plugin_directory . '/views/settings.php');
}

function generate_settings_group_content($group)
{
    $group_id = $group['id'];
    switch ($group_id) {
        case 'tmp_remote_collections_remote_settings':
            $message = 'These settings relate to the URLs to remote resources';
            break;
        case 'tmp_remote_collections_local_settings':
            $message = 'These settings relate to the presentation of results on this site. The system will use defaults if these are not overridden.';
            break;
        default:
            $message = '';
    }
    echo $message;
}

function generate_settings_field_input_text($args)
{
    $field = $args['field'];
    $value = get_option($field);
    if (empty($value) && isset($args['default'])) $value = $args['default'];
    $width = '500px';
    echo sprintf('<input type="text" name="%s" id="%s" value="%s" style="width: %s" />', $field, $field, $value, $width);
}

/**
 *  we've ended up not using the pagename part of the rewrite rules so the settings don't exist
 */
    function register_permalinks()
    {

        add_rewrite_tag('%object%', '([^&]+)');
        add_rewrite_rule('^' . $this->search_results_path . '/page/([0-9]*)/?', 'index.php?pagename=' . $this->search_results_path . '&page=$matches[1]', 'top');
        add_rewrite_rule('^' . $this->single_record_path . '/(.*)/?', 'index.php?pagename=' . $this->single_record_path . '&object=$matches[1]', 'top');

        //From ESObjects:
//      add_rewrite_rule('^' . $slug . '/page/([0-9]*)/?', 'index.php?pagename=' . $pagename . '&page=$matches[1]', 'top');
//      add_rewrite_rule('^' . $slug . '/(.*)/?', 'index.php?pagename=' . $pagename . '&object=$matches[1]', 'top');


    }

    function parse_request($q)
    {
    if (!isset($q->query_vars['pagename']) || $q->query_vars['pagename'] != $this->search_results_path) return $q; //not our page
    // else unset the 
        try {
            if (isset($q->query_vars['s'])||isset($q->query_vars['page'])) {
                unset($q->query_vars['s']);
                unset($q->query_vars['page']);
            }
        } catch (Exception $e) {
        }
        return $q;



    }
   
    function construct_collection_content( $content ) {
        global $wp;
//        $q= preg_replace( "/[?&]page=[0-9]+/", "",$_SERVER['QUERY_STRING']);
        /**
         * Get 
         *      ID and search context for a single object 
         *      search params for a search
         * Query the API 
         * 
         * The WP_REST_Response process seems to be responsible for converting some of the elements of tmpObj from arrays into objects,
         * which means they won't work with other TMP code unless we first convert them all back to arrays.
         */
        
//        $search = isset($wp->query_vars['s'])?$wp->query_vars['s']:false;//doesn't matter if "s" is empty as long as it's there, this is a search
        if ( $wp->query_vars['pagename'] == $this->single_record_path) {
                // Replace out the_content with our stuff
            $uuid = (isset($wp->query_vars['object']) && ""!==$wp->query_vars['object'])?$wp->query_vars['object']:false;
            $qctx = $_SERVER['QUERY_STRING'];//query context i.e. the other stuff in the querystring, used to make navigation links
            $options = "content=both&depth=011";
            if($uuid){
                //make a request
                $json = $this->get_object_by_id($uuid,$options,$qctx);
                if($json){
                    $decoded = json_decode($json);
                    $obj = $this->item_to_tmpObj($decoded->items[0]);
                    $facets = (array) $decoded->facets;
                    $fieldLabels = (array) $decoded->meta->fieldLabels;
                    $iiifPath = isset($this->iiif_path)?$this->iiif_path:$decoded->meta->iiifPath;
                    $mediaPath = isset($this->media_path)?$this->media_path:$decoded->meta->mediaPath;
                    $inSetNav = $decoded->links->inSetNav;
                    $content = include $this->single_record_template; //include dirname( __FILE__ ) . '/templates/object-record.php';
                }else{
                    return $content;
                }     
            }else{
                return $content;

            }
        } elseif ( $wp->query_vars['pagename'] == $this->search_results_path) {
            // Replace out the_content with our stuff
            $options = "content=both&depth=010";
            if($_SERVER['QUERY_STRING'] || $this->search_results_default_empty){   //we could remove this test if we want to always show results rather than default content
                //make a request
                $json = $this->get_objects_by_search($_SERVER['QUERY_STRING'],$options);
                if($json){
                    $decoded = json_decode($json);
                    $items = [];
                    foreach($decoded->items as $item){
                        $obj = $this->item_to_tmpObj($item);
                        array_push($items,$obj);
                    }
                    $iiifPath = isset($this->iiif_path)?$this->iiif_path:$decoded->meta->iiifPath;
                    $mediaPath = isset($this->media_path)?$this->media_path:$decoded->meta->mediaPath;
                    $count = $decoded->meta->count;
                    $page = $decoded->meta->page;
                    $perPage = $decoded->meta->perPage;
                    $filters = $decoded->links->filters;
                    $fieldLabels = (array) $decoded->fieldLabels;
                    $pagination = $decoded->links->pagination;
                    $content = include $this->search_results_template;
                }     
            }
        }  else {
            // Just normal the_content
            return $content;
        }  
    }
   
    function construct_collection_title( $title ) {
        global $wp;
        if ( $wp->query_vars['pagename'] == $this->single_record_path) {
                // Replace out the_content with our stuff
            $uuid = (isset($wp->query_vars['object']) && ""!==$wp->query_vars['object'])?$wp->query_vars['object']:false;
            $qctx = $_SERVER['QUERY_STRING'];//query context i.e. the other stuff in the querystring, used to make navigation links
            $options = "content=both&depth=011";
            if($uuid){
                //make a request
                $json = $this->get_object_by_id($uuid,$options,$qctx);
                if($json){
                    $decoded = json_decode($json);
                    $obj = $this->item_to_tmpObj($decoded->items[0]);
                    $arr[0] = $obj->compound_title;
                    $title = $arr;
                }     
            }
        }   // Just return the unaltered title
        return $title;
    }

    function get_object_by_id($uuid,$options,$qctx){
        $url = $this->rest_route."id/".$uuid."/?".$options."&".$qctx;
        $response = wp_remote_get( $url  );
        if( is_array($response) ) {
            return $response['body']; // use the content
        }
        return false;
    }

    function get_objects_by_search($q,$options){
        $url = $this->rest_route."search/?".$q."&".$options;
//        var_dump($url);
        $response = wp_remote_get( $url  );
        if( is_array($response) ) {
            return $response['body']; // use the content
        }
        return false;
    }

    /*
     * unf*** some of the f***ing-up that's been done to the "native" tmpObject by the json_encode in WP_REST_Response
     * tmpObject is an object, but none of its properties or their children are objects, but JSON turns some of them into objects
     * here we keep the top level object but put each property through a function to make any nested objects back into nested arrays
     * 
    */
    function item_to_tmpObj($obj){
        foreach ($obj as $key => $value){
            if (!empty($value)){
                $arr[$key] = array();
                $this->object_to_array($value, $arr[$key]);
             }else{
                 $arr[$key] = $value;
             }
             $obj->$key = $arr[$key];
         }
        return $obj;
    }

    function object_to_array($obj, &$arr){
        if (!is_object($obj) && !is_array($obj)){
            $arr = $obj;
            return $arr;
        }
        foreach ($obj as $key => $value){
           if (!empty($value)){
            $arr[$key] = array();
            $this->object_to_array($value, $arr[$key]);
            }else{
                $arr[$key] = $value;
            }
        }
        return $arr;
    }

	function arrayToString($obj, $arr, $sep, $filter=false, $url=false) {		//generic string-maker for flat arrays, with optional facet-making shizzle
		if($filter){
			$op = "";
			$s=$sep;
			$a = $obj->$arr;
			foreach($a as $i){
				if ($i === end($a)){
					$s="";
				}
				if($filter){
					$lnkStart="<a href='".$this->filteredLink($filter,$i,$url)."'>";
					$lnkEnd="</a>";
				}
				$op.=$lnkStart.$i.$lnkEnd.$s;
			}
			return $op;
		}else{
			return implode($sep, $obj->$arr);
		}
	}

    function arrayToList($obj,$arr, $inner, $outer, $class, $filter=false, $url=false) {
        $lnkStart=$lnkEnd="";
        $inner1 = "<".$inner." class='".$class."-departments'>";
        $inner2 = "</".$inner.">";
        $outer1 = "<".$outer." class='".$class."-departments-container'>";
        $outer2 = "</".$outer.">";
        $op = $outer1;
        $a = $obj->$arr;
        foreach($a as $i){
            if($filter){
                $lnkStart="<a href='".$this->filteredLink($filter,$i,$url)."'>";
                $lnkEnd="</a>";
            }
            $op.=$inner1.$lnkStart.$i.$lnkEnd.$inner2;
        }
        $op.=$outer2;
        return $op;
    }
    
    function filteredLink($facetField,$facetValue,$url){
        $fieldParm="filter[".$facetField."][]=";
        $url=$url."?s=&".$fieldParm.urlencode($facetValue);
        return $url;
    }    

    function is_ready()
    {
        return !(empty($this->host) || empty($this->index) || empty($this->source) || empty($this->hook_page) || $this->host == 'http://localhost:9200/uninitialised');
    }

/*

    function register_object_query_var($qvs)
    {
        $qvs[] = 'object';
        return $qvs;
    }
    function get_hook_page_slug()
    {
        // Unsure about this. We need a way to get the full slug for a page which might not be top level, this seems sane - but might need more thought.
        return substr(str_replace(home_url(), '', get_permalink($this->hook_page)), 1, -1);
    }

    function get_hook_page_name()
    {
        return get_post_field('post_name', $this->hook_page);
    }

    function register_permalinks()
    {
        $slug = $this->get_hook_page_slug();
        $pagename = $this->get_hook_page_name();

        add_rewrite_tag('%object%', '([^&]+)');
        add_rewrite_rule('^' . $slug . '/page/([0-9]*)/?', 'index.php?pagename=' . $pagename . '&page=$matches[1]', 'top');
        add_rewrite_rule('^' . $slug . '/(.*)/?', 'index.php?pagename=' . $pagename . '&object=$matches[1]', 'top');
    }

    function parse_aggregations($fields) {
        $aggs_lines = preg_split ('/$\R?^/m', $fields);
        $all_aggs = [];
        foreach($aggs_lines as $aggs_line) {
            $aggs = array_map('trim', explode(':', $aggs_line));
            if (count($aggs) !== 2) continue;
            $all_aggs[] = $aggs;
        }
        if (empty($all_aggs)) return [];
        $return_aggs = [];
        foreach($all_aggs as $agg) {
            $return_aggs[$agg[0]] = ['terms' => ['field' => $agg[1]]];
        }
        return $return_aggs;
    }

    function parse_filters($f=null) {
        if (isset($_GET['filter'])) {
            $aggs = $_GET['filter'];
        } else if (isset($_POST['filter'])) {
            $aggs = $_POST['filter'];
        } else if (is_array($f)) {
            $aggs = $f;
        } else {
            return [];
        }

        $structured_aggs = [];

        foreach($aggs as $agg_key => $agg_value) {
            $op="term";
            if (is_array($agg_value)) {
                foreach($agg_value as $v) {
                    if(is_array($v) && array_key_first($v)=="range"){
                        $op="range";
                        $v=$v['range'];
                    }
                    $structured_aggs[] = [$op => [$agg_key => str_replace("\'","'",$v)]];
                }
            } else {
                $structured_aggs[] = [$op => [$agg_key => str_replace("\'","'",$agg_value)]];
            }
        }
//var_dump($structured_aggs);
        return $structured_aggs;
    }
*/
}
