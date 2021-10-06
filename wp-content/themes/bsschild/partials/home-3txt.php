<?php
// récupération

$textes[] = get_field('texte_1');
$textes[] = get_field('texte_2');
$textes[] = get_field('texte_3');

// affichage
?>
<div class="col-12 mt-4">
    <div class="row">
        <?php foreach($textes as $texte) : ?>
            <div class="col-md-4"><?php echo $texte ?></div>
        <?php endforeach ?>
    </div>
</div>
