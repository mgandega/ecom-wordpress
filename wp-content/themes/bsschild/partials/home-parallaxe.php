<?php

$image = get_field('image');

?>
    </div>
</div>
<div class="container-fluid my-5">
    <div class="row">
        <!--<div class="col parallaxe" style="background-image:url(<?php/* echo $image['url'] */?>)">-->
        <div class="col parallaxe" style="background-image:url(<?php echo $image['url'] ?>)">
        <img src="<?php echo $image['url']; ?>"  style="height : 400px"/>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
