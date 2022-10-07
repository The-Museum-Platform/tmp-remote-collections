<?php
/**
 * This template is the default that will be used by construct_collection_content within the TmpRestCollection class
 * $this in this context is the instance of that TmpRestCollection class, which has some accessory methods
 * $obj here is the variable containing the object record details retrieved via the API
 * Dependencies:
 *  object-media-slick.php
 * 
 * Copy this template and its dependencies into your active theme directory and edit it there if you wish to change the content, appearance or behaviour 
 * You might also need to change the paths to scripts and stylesheets if your WordPress installation doesn't put these into standard locations.
 * To get the plugin to use your own templates, go to the TMP configuration page (Settings > TMP Configuration) and put the path to your templates there (relative to your active theme directory).
 */
if($obj){
    $search_results_path = home_url()."/".$this->search_results_path;
	$single_record_path = home_url()."/".$this->single_record_path;
?>
<script src='https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.js'></script>
<link href='https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.css' rel='stylesheet' />
<script src="<?php echo $this->plugin_url;?>/example/lib/openseadragon-bin-2.4.1/openseadragon.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->plugin_url;?>/example/lib/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->plugin_url;?>/example/lib/slick/slick-theme.css"/>
<link rel='stylesheet' id='tmp-collections-css'  href='<?php echo $this->plugin_url;?>/example/lib/tmp-collections.css' type='text/css' media='all' />

<header class="tmp-object-title-container">
    <h2><?php echo $obj->compound_title; //summary_title is an alternative?></h2>
    <!--could add search box here, or links for in-set navigation-->
<?php
	if($inSetNav){
		include( dirname( __FILE__ ) . '/object-search-navlinks.php');
	}
?>
</header>
<div class="tmp-object-container">
<?php
	$imgs = (array) $obj->images_arr;
	$mmedia = (array) $obj->multimedia_arr;
	if(isset($imgs['images'])||isset($mmedia['multimedia'])){
		$noMedia=" with-media";
		include( dirname( __FILE__ ) . '/object-media-slick.php');
	}else{
		$noMedia=" no-media";	//this is just to append to the class for the "aside" so it doesn't act like it should make space for media when there's
	}

    //-------------------------	

	//Compile all the bits for output
	$op="";
	$op_sidebar="";
	//identifiers
	$op_sidebar.="<h3>Accession number</h3><p>" .  $obj->accession_number . "</p>";
	if(isset($obj->ids_str) && $obj->ids_str!=""){
		$op_sidebar.="<h3>Other identifiers</h3><p>" .  $obj->ids_str . "</p>";
	}
	//maker
	if(isset($obj->makers_arr) && $obj->makers_arr){
		if(isset($facets["maker"])){
			$op_sidebar.="<h3>".$fieldLabels['maker_label']."</h3><p>" . $this->arrayToString($obj,"makers_arr","; ","lifecycle.creation.maker.summary_title.keyword",$search_results_path)."</p>" ;
		}elseif(isset($obj->makers_str)){
			$op_sidebar.="<h3>".$fieldLabels['maker_label']."</h3><p>" . $obj->makers_str . "</p>";
		}
	} 
	//production_date
	if(isset($obj->production_dates_arr) && $obj->production_dates_arr){
		$op_sidebar.="<h3>".$fieldLabels['production_dates_label']."</h3><p>" . $obj->productionDates_str . "</p>";
	}
	//production_date
	if(isset($obj->production_places_arr) && $obj->production_places_arr){
		if(isset($facets["placemade"])){
			$op_sidebar.="<h3>".$fieldLabels['production_places_label']."</h3>" . $this->arrayToList($obj,"production_places_arr","li","ul","tmp-facets","lifecycle.creation.place.summary_title.keyword",$search_results_path);
		}elseif(isset($obj->productionPlaces_str)){
			$op_sidebar.="<h3>".$fieldLabels['production_places_label']."</h3><p>" . $obj->productionPlaces_str . "</p>";
		}
	}
	//materials
	if(isset($obj->materials_arr) && $obj->materials_arr){
		if(isset($facets["materials"])){
			$op_sidebar.="<h3>".$fieldLabels['materials_label']."</h3><p>" . $this->arrayToString($obj,"materials_arr","; ","materials.summary_title.keyword",$search_results_path)."</p>";
		}elseif(isset($obj->materials_str)){
			$op_sidebar.="<h3>".$fieldLabels['materials_label']."</h3><p>" . $obj->materials_str . "</p>";
		}
	}
	//department
	if(isset($obj->departments_arr) && $obj->departments_arr){
		if(isset($facets["department"])){
			$op_sidebar.="<h3>".$fieldLabels['departments_label']."</h3>" . $this->arrayToList($obj,"departments_arr","li","ul","tmp-facets","lifecycle.collection.department.summary_title.keyword",$search_results_path);
		}elseif(isset($obj->departments_str)){
			$op_sidebar.="<h3>".$fieldLabels['departments_label']."</h3>" . $obj->departments_str . "";
		}
	} 
	//description
	if(isset($obj->descriptions_str) && $obj->descriptions_str!=""){
		$op.="<h3>".$fieldLabels['descriptions_label']."</h3>" . $obj->descriptions_str;
	} 
	//dimensions
	if(isset($obj->dimensions_arr) && $obj->dimensions_arr){
		$op_sidebar.="<h3>".$fieldLabels['dimensions_label']."</h3>" . $this->arrayToList($obj,"dimensions_arr","li","ul","tmp-facets",false,$search_results_path);
	} 
	//provenance
	if(isset($obj->provenance_str) && $obj->provenance_str!=""){
		$op_sidebar.="<h3>".$fieldLabels['provenance_label']."</h3><p>" . $obj->provenance_str . "</p>";
	} 
		
	//subjects
	if(isset($obj->subjects_arr) && $obj->subjects_arr){
		if(isset($facets["subjects"])){
			$op_sidebar.="<h3>".$fieldLabels['subjects_label']."</h3>" . $this->arrayToList($obj,"subjects_arr","li","ul","tmp-facets","subjects.summary_title.keyword",$search_results_path);
		}elseif(isset($obj->subjects_str)){
			$op_sidebar.="<h3>".$fieldLabels['subjects_label']."</h3>" . $obj->subjects_str;
		}
	} 
	//place of collection
	if(isset($obj->collection_places_arr) && $obj->collection_places_arr){
		if(isset($facets["placecollected"])){
			$op_sidebar.="<h3>".$fieldLabels['place_of_collection_label']."</h3>" . $this->arrayToList($obj,"collection_places_arr","li","ul","tmp-facets","lifecycle.collection.place.summary_title.keyword",$search_results_path);
		}elseif(isset($obj->collectionPlaces_str)){
			$op_sidebar.="<h3>".$fieldLabels['place_of_collection_label']."</h3><p>" . $obj->collectionPlaces_str . "</p>";
		}
	}

	//agents
	if(isset($obj->people_arr) && $obj->people_arr){
		if(isset($facets["relpeople"])){
			$op_sidebar.="<h3>".$fieldLabels['associated_people_label']."</h3>" . $this->arrayToList($obj,"people_arr","li","ul","tmp-facets","agents.summary_title.keyword",$search_results_path);
		}elseif(isset($obj->people_str)){
			$op_sidebar.="<h3>".$fieldLabels['associated_people_label']."</h3><p>" . $obj->people_str . "</p>";
		}
	} 
	//events
	if(isset($obj->events_arr) && $obj->events_arr){
		if(isset($facets["events"])){
			$op_sidebar.="<h3>".$fieldLabels['associated_events_label']."</h3>" . $this->arrayToList($obj,"events_arr","li","ul","tmp-facets","events.summary_title.keyword",$search_results_path);
		}elseif(isset($obj->events_str)){
			$op_sidebar.="<h3>".$fieldLabels['associated_events_label']."</h3><p>" . $obj->events_str . "</p>";
		}
	} 
	//places - test first to see if we're actually expecting lat/long in here, in which case don't show
	if($obj->mapsEnabled<>'show' || $obj->mapField==false){
		if($obj->places_arr){			
			if(isset($facets["relplaces"])){
				$op_sidebar.="<h3>".$fieldLabels['associated_places_label']."</h3>" . $this->arrayToList($obj,"places_arr","li","ul","tmp-facets","places.summary_title.keyword",$search_results_path);
			}elseif(isset($obj->places_str)){
				$op_sidebar.="<h3>".$fieldLabels['associated_places_label']."</h3><p>" . $obj->places_str . "</p>";
			}	
		} 
	}
	
	if(isset($imgs['images']) && is_array($imgs['images'])){
		echo '<aside class="tmp-object-aside'.$noMedia.'">';
		echo $op_sidebar;
		echo '</aside>';
		echo '<div class="tmp-object-content'.$noMedia.'">';
		echo $op;
		echo '</div>';
	}else{	//very crude! just flip the order of sidebar and description if there's no media to show

		echo '<div class="tmp-object-content'.$noMedia.'">';
		echo $op;
		echo '</div>';
		echo '<aside class="tmp-object-aside'.$noMedia.'">';
		echo $op_sidebar;
		echo '</aside>';

	}
	echo '</div>'; // <-- /object-container -->    


    //-----------------------------------
    if(isset($obj->latLong['long']) && isset($obj->latLong['lat'])){
        $long=$obj->latLong['long'];
        $lat=$obj->latLong['lat'];
        if($long<>"" && $lat<>""){
?>
<div id='tmp-map' style='width:100%;height:400px; margin-top:40px'></div>
<script>
L.mapbox.accessToken = '';		// get a mapbox key and add it here

var mapboxTiles = L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=' + L.mapbox.accessToken, {
   attribution: '© <a href="https://www.mapbox.com/feedback/">Mapbox</a> © <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
   tileSize: 512,
   zoomOffset: -1
});

var map = L.map('tmp-map')
.addLayer(mapboxTiles)
.setView([<?php echo $lat.",".$long; ?>], 8);
var marker = L.marker([<?php echo $lat.",".$long;?>]).addTo(map);

</script>
<?php
        }
    }

    if($obj->images_arr['meta']['hasZooms']){
?>
<script type="text/javascript">
function intoTheSea(ts=null,tgt=null){
if(ts==null||tgt==null){
    document.getElementByClassName("tmp-zoom-container").innerHTML="";
}else{
    document.getElementById(tgt).innerHTML="";
    var viewer = OpenSeadragon({
        id:            tgt,
        prefixUrl: "<?php echo $this->plugin_url;?>/example/lib/openseadragon-bin-2.4.1/images/",
        sequenceMode:  true,
        crossOriginPolicy: false,
        tileSources:   [
            ts
            //"https://libimages1.princeton.edu/loris/pudl0001%2F4609321%2Fs42%2F00000007.jp2/info.json"
        ]
    });
}
;return false;
}

var zooms = document.getElementsByClassName("tmp-zoom-container");
for(i=0; i<zooms.length; i++){
var theId = zooms[i].id;
var theTileSource = zooms[i].dataset.src;
intoTheSea(theTileSource,theId);
}
</script>				
<?php
    }
//    var_dump($obj);
}else{
    echo "<h4>We couldn't find that item</h4>";
}
?>