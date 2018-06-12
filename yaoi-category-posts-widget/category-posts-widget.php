<?php
/*
Plugin Name: Category Posts Widget JP
Plugin URI: 
Description: 単一カテゴリの投稿を出力するウィジェットを追加する。
Author: Yuki AOI
Version: 0.1
Author URI: https://aoi.ooo
Text Domain: category-posts-widget
*/

class CategoryPostsWidget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'category-posts-widget', // Base ID
            'CategoryPostsWidget',   // Name
            array(
                'description' => '指定カテゴリの投稿を出力します。',
            )
        );
    }
 
    /**
     * 表側の Widget を出力する
     *
     * @param array $args      'register_sidebar'で設定した「before_title, after_title, before_widget, after_widget」が入る
     * @param array $instance  Widgetの設定項目
     */
    public function widget($args, $instance)
    {
        $title = $instance['title'];
        $type  = $instance['type'];
        $limit = $instance['limit'];

        $options = array(
            'posts_per_page' => $limit ?? 5,
            'category_name'  => $type ?? 'post',
        );
        $posts = get_posts($options);

        echo $args['before_widget'];
        echo $args['before_title'], $title, $args['after_title'];
        echo '<dl>', "\n";
        foreach ($posts as $post) {
            $date = date('Y.m.d', strtotime($post->post_date));
            $title = htmlspecialchars($post->post_title);
            if (strlen($post->post_content) > 0) {
                $title = sprintf('<a href="%s">%s</a>', get_permalink($post->ID), $title);
            }
            echo sprintf('<dt>%s</dt><dd>%s</dd>', $date, $title), "\n";
        }
        echo '</dl>';
        echo $args['after_widget'];
    }
 
    /** Widget管理画面を出力する
     *
     * @param array $instance 設定項目
     * @return string|void
     */
    public function form($instance)
    {
        $title  = $instance['title'];
        $type   = $instance['type'] ?? 'post';
        $limit  = $instance['limit'] ?? 5;
        $format = $instance['format'] ?? 'Y.m.d';

        $title_name = $this->get_field_name('title');
        $title_id   = $this->get_field_id('title');

        $type_name = $this->get_field_name('type');
        $type_id   = $this->get_field_id('type');

        $limit_name = $this->get_field_name('limit');
        $limit_id   = $this->get_field_id('limit');

        $format_name = $this->get_field_name('format');
        $format_id   = $this->get_field_id('format');


        ?>
        <p>
            <label for="<?php echo $title_id; ?>">Title:</label>
            <input class="widefat" id="<?php echo $title_id; ?>" name="<?php echo $title_name; ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $type_id; ?>">Type:</label>
            <input class="widefat" id="<?php echo $type_id; ?>" name="<?php echo $type_name; ?>" type="text" value="<?php echo esc_attr($type); ?>">
        </p>
        <p>
            <label for="<?php echo $limit_id; ?>">Limit:</label>
            <input class="widefat" id="<?php echo $limit_id; ?>" name="<?php echo $limit_name; ?>" type="text" value="<?php echo esc_attr($limit); ?>">
        </p>
        <p>
            <label for="<?php echo $format_id; ?>">format:</label>
            <input class="widefat" id="<?php echo $format_id; ?>" name="<?php echo $format_name; ?>" type="text" value="<?php echo esc_attr($format); ?>">
        </p>
        <?php
    }
 
    /** 新しい設定データが適切なデータかどうかをチェックする。
     * 必ず$instanceを返す。さもなければ設定データは保存（更新）されない。
     *
     * @param array $new_instance  form()から入力された新しい設定データ
     * @param array $old_instance  前回の設定データ
     * @return array               保存（更新）する設定データ。falseを返すと更新しない。
     */
    public function update($new_instance, $old_instance)
    {
        $range = array(
            'min_range' => 0,
        );
        if (false === filter_var($new_instance['limit'], FILTER_VALIDATE_INT, array('options' => $range))) {
            return false;
        }
        return $new_instance;
    }
}
 
add_action('widgets_init', function () {
    register_widget('CategoryPostsWidget');
});
