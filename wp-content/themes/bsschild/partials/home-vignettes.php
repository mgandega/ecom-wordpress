<?php

$vignettes[] = get_field('vignette_1');
$vignettes[] = get_field('vignette_2');
$vignettes[] = get_field('vignette_3');
$vignettes[] = get_field('vignette_4');

?>
<div class="col-12">
    <div class="row">
        <?php foreach($vignettes as $vignette) : ?>
            <div class="col-md-3">
                <img src="<?php echo $vignette['url'] ?>" alt="<?php echo $vignette['alt'] ?>" class="img-fluid rounded-circle">
            </div>
        <?php endforeach ?>
    </div>
</div>