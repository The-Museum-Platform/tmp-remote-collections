<?php
echo "<link rel='stylesheet' id='tmp-collections-css'  href='".$this->plugin_url."/example/lib/tmp-collections.css' type='text/css' media='all' />";
//include search box
include('search-searchbox.php'); 

if (empty($items)){
    echo "<p>No records were found matching your query. Please try another search term or <a href='?s='>browse all records</a>.</p>";
}else{
    $rec = ($count>1)?($count<20000)?$count." matching records":"At least ".$count." matching records. You may want to refine your search.":"1 matching record";
    echo "<h4>".$rec."</h4>";
?>
<div class="tmp-panel-container">

    <div class="tmp-panel-facets">
        <?php 
        //do facets
        include('search-facets.php'); 
        ?>
    </div>		

    <div class="tmp-panel-results" id="tmp-panel-results">
        
        <div class="tmp-object-list">
            <?php
            $i=1;
            foreach($items as $obj) 
            {	        
            

                // $uuid = $hit['_source']['admin']['uuid'];
                $uuid = $obj->uuid;
                $accno = $obj->accession_number;
                $summary_title = $obj->summary_title;
                $imgs = $obj->images_arr;
                $tn = "<img src='".$this->plugin_url."/example/images/no-image-available.png' />";
                if($imgs['images'][0]){
                    $tn = "<img src='".$mediaPath.$imgs['images'][0]['mid']."' />";
                }
                $qs = $mypos = ($page-1)*$perPage + $i;
                $i++;  
            ?>
                <div class="tmp-object-list__item">
                    <div class="tmp-object-list__inner">
                        <a class="tmp-object-list__link" href="<?php echo home_url()."/".$this->single_record_path."/".$uuid;?>/?<?php echo urlencode($_SERVER['QUERY_STRING']);?>&pos=<?php echo $qs;?>">
                            <?php echo $tn; ?>
                            <h3 class="tmp-object-list__title"><?php echo $summary_title; ?></h3>
                        </a>
                    </div>
                </div>
            <?php 
            }
            ?>		
        </div>
            
    </div>

</div>
<?php
}
?>

<script>

<?php /* https://medium.com/@andybarefoot/a-masonry-style-layout-using-css-grid-8c663d355ebb */ ?>
function resizeGridItem(item){
    grid = document.getElementsByClassName("tmp-object-list")[0];
    rowHeight = parseInt(window.getComputedStyle(grid).getPropertyValue('grid-auto-rows'));
    rowGap = parseInt(window.getComputedStyle(grid).getPropertyValue('grid-row-gap'));
    rowSpan = Math.ceil((item.querySelector('.tmp-object-list__inner').getBoundingClientRect().height+rowGap)/(rowHeight+rowGap));
    item.style.gridRowEnd = "span "+rowSpan;
}

function resizeAllGridItems(){
    allItems = document.getElementsByClassName("tmp-object-list__item");
    for(x=0;x<allItems.length;x++){
        resizeGridItem(allItems[x]);
    }
}

function resizeInstance(instance){
    item = instance.elements[0];
    resizeGridItem(item);
}

window.addEventListener("load", resizeAllGridItems, false);
window.addEventListener("resize", resizeAllGridItems);

</script>

<?php
//do pagination
 include('search-pagination.php');
?>