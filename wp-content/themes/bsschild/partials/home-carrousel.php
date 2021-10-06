<?php

// récupération
$images[] = get_field('image_1'); // array (url, alt,...)
$images[] = get_field('image_2');
$images[] = get_field('image_3');
$images[] = get_field('image_4');

// affichage
?>
<div class="col-12">
    <div id="moncarrousel" class="carousel slide" data-ride="carousel">

        <!-- indicateurs -->
        <ol class="carousel-indicators">
            <?php foreach ($images as $key => $image) :
                $class = '';
                if ($key == 0) $class = 'active';
            ?>
                <li data-target="#moncarrousel" data-slide-to="<?php echo $key ?>" class="<?php echo $class ?>"></li>
            <?php endforeach ?>
        </ol>

        <!-- Images -->
        <div class="carousel-inner">
            <?php foreach ($images as $key => $image) :
                $class = '';
                if ($key == 0) $class = 'active';
            ?>
                <div class="carousel-item <?php echo $class ?>">
                    <img src="<?php echo $image['url'] ?>" class="d-block w-100" alt="<?php echo $image['alt'] ?>">
                </div>
            <?php endforeach ?>
        </div>

        <!-- fléches -->
        <a class="carousel-control-prev" href="#moncarrousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#moncarrousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</div>