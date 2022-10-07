<div class="tmp-object-search">
	<form action="<?php echo home_url()."/".$this->search_results_path; ?>" method="get">
		<label for="tmp-search-box">Find objects:</label>
		<input type="text" name="s" id="tmp-search-box"/>
		<input type="submit" value="Search" />
	</form>
</div>
<script>
try{
	var params = (new URL(document.location)).searchParams;
	var s = params.get("s");
	var deparam = decodeURIComponent(params);
	if(s==null){	//no search param, see if it's encoded instead
		var durl = window.location.protocol+"//"+window.location.hostname+window.location.pathname+"?"+deparam;
		let params = (new URL(durl)).searchParams;
		s = params.get("s").replace("=","");
	}
	document.getElementById("tmp-search-box").value=s;
}catch{
}
</script>
