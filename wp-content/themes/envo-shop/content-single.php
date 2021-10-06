<!-- start content container -->
<div class="row">      
    <article class="col-md-<?php envo_shop_main_content_width_columns(); ?>">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>                         
                <div <?php post_class(); ?>>
                    <?php envo_shop_thumb_img('envo-shop-single', '', false, true); ?>
                    <div class="single-head <?php echo esc_attr(has_post_thumbnail() ? 'has-thumbnail' : 'no-thumbnail' ) ?>">
                        <?php the_title('<h1 class="single-title">', '</h1>'); ?>
                        <?php envo_shop_widget_date(); ?>
                        <?php envo_shop_author_meta(); ?>
                        <?php envo_shop_widget_comments(); ?>
                    </div>
                    <div class="single-content">
                        <div class="single-entry-summary">
                            <?php do_action('envo_shop_before_content'); ?> 
                            <?php the_content(); ?>
                            <?php do_action('envo_shop_after_content'); ?> 
                        </div><!-- .single-entry-summary -->
                        <?php wp_link_pages(); ?>
                        <?php envo_shop_entry_footer(); ?>
                    </div>
                    <?php envo_shop_prev_next_links(); ?>
                    <?php
                    $authordesc = get_the_author_meta('description');
                    if (!empty($authordesc)) {
                        ?>
                        <div class="single-footer row">
                            <div class="col-md-4">
                                <?php get_template_part('template-parts/template-part', 'postauthor'); ?>
                            </div>
                            <div class="col-md-8">
                                <?php comments_template(); ?> 
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="single-footer">
                            <?php comments_template(); ?> 
                        </div>
                    <?php } ?>
                </div>        
            <?php endwhile; ?>        
        <?php else : ?>            
            <?php get_template_part('content', 'none'); ?>        
        <?php endif; ?>    
    </article> 
    <?php get_sidebar('right'); ?>
</div>
<!-- end content container -->
