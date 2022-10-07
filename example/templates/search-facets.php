<?php
/**
 * Two choices here: 
 * 	use the ready-made HTML returned by the API. This obviously has a pre-determined order of the facet fields and layout of the content; or
 *  use the arrays of links
 * Both sequence the filters according to the order in which they are set in TMP itself, which can be changed. Of course if using the array of links you can change the sequence here instead
 * Instead of echoing the output here you could echo the variables elsewhere on the search page (after this file has been included)
 */
if(isset($filters->foo)){
	echo $filters->html;
}elseif(isset($filters->links)){
	/**
	 * do something using $filters->links. The structure is:
	 * $filters	->links	->(field1 e.g. "maker")	->facetLinks		->links[term,count,querystring]
	 * 											->removeFacetLinks	->links[term,count,querystring]
	 * In this case we'll put the links to remove applied facets at the top, followed by those to add
	 **/
	$remove = '';
	$add = '';
	 foreach($filters->links as $filter){
		if(!empty($filter->removeFacetLinks->links)){
			foreach($filter->removeFacetLinks->links as $link){
				$remove .= "<li><a href='".$link[1]."'>".$link[0]. "</a></li>";
			}
		}
		if(!empty($filter->facetLinks->links)){
			$add.='<div class="tmp-facet-container"><h3>'.$filter->label.'</h3><ul>';
				foreach($filter->facetLinks->links as $link){
					$add.="<li><a href='".$link[2]."'>".$link[0]. " (".$link[1].")</a></li>";
				}
			$add.= "</ul></div>";
		}
	}
	$remove = !empty($remove)?'<div class="tmp-remove-facet-container"><h5>Currently applied filters (click to remove)</h5><ul>'.$remove.'</ul></div>':false;
	$add = !empty($add)?'<div class="tmp-facet-container"><h5>Add filters</h5>'.$add.'</div>':false;
	echo $remove;
	echo $add;
}

?>
