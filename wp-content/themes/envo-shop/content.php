<article class="col-md-6">
    <div <?php post_class(); ?>>                    
        <div class="news-item <?php echo esc_attr(has_post_thumbnail() ? 'has-thumbnail' : 'no-thumbnail' ) ?>">
            <?php envo_shop_thumb_img('envo-shop-med'); ?>
            <div class="news-text-wrap">
                <?php the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>'); ?>
                <?php envo_shop_author_meta(); ?>
                <div class="content-date-comments">
                    <?php envo_shop_widget_date(); ?>
                    <?php envo_shop_widget_comments(); ?>
                </div>  
                <div class="post-excerpt">
                    <?php the_excerpt(); ?>
                </div><!-- .post-excerpt -->
            </div><!-- .news-text-wrap -->
        </div><!-- .news-item -->
    </div>
</article>
