<?php
    global $ae_post_factory, $user_ID;
    $post_obj = $ae_post_factory->get('mjob_order');
    $default = array();
    if( is_page_template('page-dashboard.php') ){
        $default = array('posts_per_page'=> 5);
    }
?>
<div class="list-order list-task-wrapper">
    <?php
    $args = array(
        'post_type' => 'mjob_order',
        'post_status' => array(
            'publish',
            'delivery',
            'disputed',
            'disputing',
            'late',
            'finished'
        ),
        'meta_key' => 'seller_id',
        'meta_value' => array($user_ID),
        'meta_compare' => 'IN'
    );
    $args = wp_parse_args($args, $default);
    $postdata = array();
    $task_query = new WP_Query($args);
    if($task_query->have_posts()) {
        ?>
        <ul class="list-tasks mjob-list mjob-list--horizontal">
            <?php
            while($task_query->have_posts()) {
                $task_query->the_post();
                $convert = $post_obj->convert($post);
                $postdata[] = $convert;
                get_template_part('template/task-list', 'item');
            }

            wp_reset_postdata();
            ?>
        </ul>

        <?php if(is_page_template('page-dashboard.php')) : ?>
            <div class="view-all float-center"><a href="<?php echo et_get_page_link('my-list-order'); ?>"><?php _e('View all', 'enginethemes'); ?></a></div>
        <?php endif; ?>
    <?php } else { ?>
        <p class="no-items"><?php _e('There are no tasks found!', 'enginethemes'); ?></p>
    <?php } ?>
</div>
<?php
if( !is_page_template('page-dashboard.php') ):
    echo '<div class="paginations-wrapper float-center">';
    $task_query->query = array_merge($task_query->query, array('is_task' => true));
    ae_pagination($task_query, get_query_var('paged'), 'load');
    echo '</div>';
    /**
     * render post data for js
     */
    echo '<script type="data/json" class="task_postdata" >' . json_encode($postdata) . '</script>';
endif;
