<?php
//  slick slider https://kenwheeler.github.io/slick/
$cnt=1;
$op="";
$lnx="";
$ca="";
if(isset($imgs['images'])){
    foreach($imgs['images'] as $i){
      $i = (array) $i;
      if(isset($i['copyright_attrib'])){
        $ca = "<p class='tmp-attrib'>".$i['copyright_attrib']."</p>";
      }
      if(isset($i['zoom'])){
          $op.="<div><div id='tmp-zoom-container-".$cnt."' class='tmp-zoom-container' data-src='".$iiifPath.$i['zoom']."/info.json' style='height:600px'></div>".$ca."</div>";
      }elseif($i['large']!=null){
          $op.="<div id='tmp-img-container-".$cnt."'><picture><source srcset='".$mediaPath.$i['large']."' media='(min-width: 500px)'/><source srcset='".$mediaPath.$i['mid']."' media='(min-width: 200px)'/><source srcset='".$mediaPath.$i['preview']."'/><img src='".$mediaPath.$i['large']."' alt='A beautiful responsive image'/></picture>".$ca."</div>";
      }
      $cnt++;
  }
}
if(isset($mmedia['multimedia'])){
  foreach($mmedia['multimedia'] as $i){
    $wpe = new WP_Embed(); // treat our 3D Sketchfab data as an embedding shortcode, using it in an oEmbed query. You must make sure that Sketchfab is enabled e.g. by adding this to your  theme's functios.php: wp_oembed_add_provider( '#https?://sketchfab\.com/.*#i', 'https://www.sketchfab.com/oembed', true );
      if(preg_match('/3D/',$i['type'])){
          $sf = $wpe->shortcode( ['width'=>600,'height'=>600], $i['location'] );  // ... the problem is, WP_Embed is returning an iframe with the wrong sandbox properties and the suggestion here https://help.sketchfab.com/hc/en-us/articles/203059088-Compatibility?page=4 doesn't fix it
          $op.="<div id='tmp-3d-container-".$cnt."'>".$sf."</div>";  
//      you could also hand-roll the iframe by messing around with the provided URL:
//          $sfurl = str_replace("\/3d-models\/","\/models\/", $i['location']);
//          $op.="<div id='tmp-mmedia-container-".$cnt."'><iframe width='600px' height='300px' src='".$sfurl ."/embed?' frameborder='0' allow='autoplay; fullscreen; vr' mozallowfullscreen='true' webkitallowfullscreen='true'></iframe></div>";
        }elseif(preg_match('/(?im)audio/',$i['type'])){
          if(preg_match('/soundcloud/',$i['location'])){  //we only use SoundCloud oEmbed at the moment, could also include MixCloud (or add others). Full list https://developer.wordpress.org/reference/hooks/oembed_providers/
            $sf = $wpe->shortcode( ['width'=>600,'height'=>600], $i['location'] );  //the problem is, WP_Embed is returning an iframe with the wrong sandbox properties and the suggestion here https://help.sketchfab.com/hc/en-us/articles/203059088-Compatibility?page=4 doesn't fix it
          }else{
            $sf = wp_audio_shortcode(['src'=>$i['location']]);
          }
          $op.="<div id='tmp-audio-container-".$cnt."'>".$sf."</div>";
        }elseif(preg_match('/(?im)video/',$i['type'])){
          $sf = $wpe->shortcode( ['width'=>600,'height'=>600], $i['location'] );  //the problem is, WP_Embed is returning an iframe with the wrong sandbox properties and the suggestion here https://help.sketchfab.com/hc/en-us/articles/203059088-Compatibility?page=4 doesn't fix it
          $op.="<div id='tmp-video-container-".$cnt."'>".$sf."</div>";
        }
      $cnt++;
  }
}  
  

if($cnt>1){
    echo "<div class='tmp-object-media'><div id='tmp-media-outer'><div id='tmp-media-slide'>".$op."</div></div></div>";
}else{
    echo "<div class='tmp-object-media'><div id='media-outer'><div id='tmp-media-container'>".$op."</div></div></div>";
}
echo "<script type='text/javascript' src='".$this->plugin_url."/example/lib/slick/slick.min.js'></script>";
?>
<script type="text/javascript">
    jQuery(document).ready(function(){
      jQuery('#tmp-media-slide').slick({
        dots: true,
        infinite: true,
        speed: 300,
        slidesToShow: 1
     });
    });
</script>