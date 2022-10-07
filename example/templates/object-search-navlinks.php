<div class="tmp-set-nav">
<?php

      if(isset($inSetNav->prev)){
        echo '<a href="'.$single_record_path."/".$inSetNav->prev.'">< Previous record</a>';
      }
      echo ' | <a href="'.$search_results_path."/".$inSetNav->curr.'">Current search</a> | ';
      
      if(isset($inSetNav->next)){
        echo '<a href="'.$single_record_path."/".$inSetNav->next.'">Next record ></a>';
      }
?>
</div>