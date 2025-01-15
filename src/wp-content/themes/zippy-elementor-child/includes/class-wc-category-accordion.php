<?php
/*
Plugin Name: WooCommerce Category Accordion Widget
Description: Display store categories as menu accordion  ,
Version: 1.0
Author: Toan
*/

class WC_Category_Accordion_Widget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'wc_category_accordion_widget',
            __('WooCommerce Category Accordion', 'text_domain'),
            array('description' => __('Display store categories as menu accordion.', 'text_domain'),)
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        $this->display_accordion_menu();

        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : __('Danh mục sản phẩm', 'text_domain');
?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e(esc_attr('Title:')); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
    <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }

		private function display_accordion_menu()
		{
			echo '<div class="accordion-menu woocommerce novaapf-ajax-term-filter1 widget widget_novaapf-category-filter1">';
			
            $excluded_slugs = ['field', 'Uncategorized'];

            $excluded_ids = [];
            foreach ($excluded_slugs as $slug) {
                $term = get_term_by('slug', $slug, 'product_cat');
                if ($term) {
                    $excluded_ids[] = $term->term_id;
                }
            }

			$args = array(
				'taxonomy'     => 'product_cat',
				'orderby'      => 'name',
				'show_count'   => 0,
				'pad_counts'   => 0,
				'hierarchical' => 1,
				'title_li'     => '',
 				'hide_empty'   => false ,
				'exclude'      => $excluded_ids 
			);

			$all_categories = get_categories($args);

			foreach ($all_categories as $cat) {
				if ($cat->category_parent == 0) {
					$category_id = $cat->term_id;

					// Check if the category has subcategories
					$has_children = get_categories(array(
						'taxonomy' => 'product_cat',
						'parent'   => $category_id,
                        'hide_empty'   => false ,
					));
					echo '<div class="novaapf-layered-nav1">';
					echo '<ul>';
					echo '<li class="accordion-item" data-category-id="' . $category_id . '">';
					echo '<div class="accordion-header"><a href="' . get_term_link($cat->slug, 'product_cat') . '" data-key="product-cato" data-value="' . $category_id . '" data-multiple-filter>' . $cat->name . 					'</a>';

					// Show "+" only if the category has subcategories
					if (!empty($has_children)) {
						echo '<span class="accordion-icon">+</span>';
					}

					echo '</div>';

					// Show subcategories if available
					if (!empty($has_children)) {
						echo '<div class="accordion-content novaapf-layered-nav1">';
						$this->display_subcategories($category_id);
						echo '</div>';
					}

					echo '</li> </ul> </div>';
				}
			}
			echo '</div>';
		}


	private function display_subcategories($parent_id)
	{
		$args = array(
			'taxonomy'     => 'product_cat',
			'child_of'     => $parent_id,
			'parent'       => $parent_id,
			'orderby'      => 'name',
			'show_count'   => 0,
			'pad_counts'   => 0,
			'hierarchical' => 1,
            'hide_empty'   => false ,
			'title_li'     => ''
		);

		$sub_cats = get_categories($args);

		if ($sub_cats) {
			echo '<ul class="accordion-menu menu-sub">';
			foreach ($sub_cats as $sub_category) {
				echo '<li class="sub-category accordion-item" data-category-id="' . $sub_category->term_id . '">';
				echo '<div class="accordion-header"><a href="' . get_term_link($sub_category->slug, 'product_cat') . '" data-key="product-cata" data-value="' . $sub_category->term_id . '" data-multiple-filter>' . $sub_category->name . '</a>';

				// Check if this subcategory has further subcategories
				$has_children = get_categories(array(
					'taxonomy' => 'product_cat',
					'parent'   => $sub_category->term_id,
                    'hide_empty'   => false ,
				));

				// Show "+" icon only if there are subcategories
				if (!empty($has_children)) {
					echo '<span class="accordion-icon">+</span>';
					echo '</div>';
					echo '<div class="accordion-content">';
					$this->display_subcategories($sub_category->term_id); 
					echo '</div>';
				} else {
					echo '</div>'; 
				}

				echo '</li>';
			}
			echo '</ul>';
		}
	}

}

function register_wc_category_accordion_widget()
{
    register_widget('WC_Category_Accordion_Widget');
}
add_action('widgets_init', 'register_wc_category_accordion_widget');

function wc_category_accordion_styles()
{
    ?>
<script>
jQuery(document).ready(function($) {
    $('.accordion-header').on('click', function(e) {
        if ($(e.target).is('a')) {
            return;
        }
        e.preventDefault();

        var accordionItem = $(this).closest('.accordion-item');
        var icon = $(this).find('.accordion-icon');
        var content = accordionItem.children('.accordion-content:first');

        if (content.length > 0) {
            content.slideToggle();

            if (accordionItem.hasClass('active')) {
                icon.text('+');
            } else {
                icon.text('-');
            }

            accordionItem.toggleClass('active');

            accordionItem.siblings().removeClass('active').find('.accordion-content').slideUp();
            accordionItem.siblings().find('.accordion-icon').text('+');

            accordionItem.find('.sub-category').removeClass('active').find('.accordion-content').slideUp();
            accordionItem.find('.sub-category .accordion-icon').text('+');
        }

        var currentUrl = window.location.href;
        var categoryLink = accordionItem.find('a').attr('href');

        if (currentUrl === categoryLink) {
            accordionItem.addClass('active');
            accordionItem.find('.accordion-content').show();
            accordionItem.find('.accordion-icon').text('-');
        }
    });

    var currentUrl = window.location.href;

    $('.accordion-item').removeClass('active').find('.accordion-content').hide();
    $('.accordion-item').find('.accordion-icon').text('+');

    $('.accordion-item').each(function() {
        var categoryLink = $(this).find('a').attr('href');
        if (currentUrl === categoryLink) {
            var item = $(this);

            if (!item.hasClass('active')) {
                item.addClass('active');
                item.children('.accordion-content').show(); 
                item.find('.accordion-icon').first().text('-');
            }

            item.parents('.accordion-item').each(function() {
                var parent = $(this);
                if (!parent.hasClass('active')) {
                    parent.addClass('active');
                    parent.children('.accordion-content').show();
                    parent.find('.accordion-icon').first().text('-');
                }
            });

            item.find('.accordion-content .accordion-icon').text('+');
        }
    });
});
</script>
<?php
}
add_action('wp_head', 'wc_category_accordion_styles');
?>