<?php wp_head(); ?>
<?php
$sc_id = !empty($sc_id) ? $sc_id : get_the_ID();
//get post-meta
$cshms_section_data = get_post_meta($sc_id, '_cshms-sections-data', true);
if (!empty($cshms_section_data)) {
    $cshms_section_count = count($cshms_section_data);
} else {
    $cshms_section_count = 0;
}
wp_enqueue_style('jquery_multiscroll');
wp_enqueue_style('cshms_public_style');
wp_enqueue_script('jquery_multiscroll');
wp_enqueue_script('jquery_multiscroll_easings');
wp_enqueue_script('cshms_public_script');
?>
    <div id="CSHMSMultiScroll">
        <?php
        if ($cshms_section_count >= 1) {
            $inline_style = '';
            ?>
            <div class="ms-left">
                <div class="wpb_wrapper">
            <?php
            for ($i = 1; $i <= $cshms_section_count; $i++) {
                $left_text = $cshms_section_data['section_' . $i]['left_text'];
                $right_text = $cshms_section_data['section_' . $i]['right_text'];
                $same_class = '';
                $left_text_check = str_replace(' ','',$left_text);
                $right_text_check = str_replace(' ','',$right_text);
                if ($left_text_check == $right_text_check){
                    if (!empty($left_text)){
                        $same_class = 'cshms-same';
                    }
                }
                $left_image = $cshms_section_data['section_' . $i]['left_image'];
                $class_section = 'section'.$i.'-left';
                $inline_style .= '#'.$class_section.'{background-image: url("'.$left_image.'");} ';
                ?>
                    <div class="cshms-row ms-section section1" id="<?php echo $class_section ?>">
                        <div class="cshms-content <?php echo esc_attr($same_class)?>">
                            <?php echo $left_text; ?>
                        </div>
                    </div>
                <?php
            }

            ?>
                </div>
            </div>

            <div class="ms-right">
            <?php
            for ($i = 1; $i <= $cshms_section_count; $i++) {
                $left_text = $cshms_section_data['section_' . $i]['left_text'];
                $right_text = $cshms_section_data['section_' . $i]['right_text'];
                $same_class = '';
                $left_text_check = str_replace(' ','',$left_text);
                $right_text_check = str_replace(' ','',$right_text);
                if ($left_text_check == $right_text_check){
                    if (!empty($left_text)){
                        $same_class = 'cshms-same';
                    }
                }
                $right_image = $cshms_section_data['section_' . $i]['right_image'];
                $class_section = 'section'.$i.'-right';
                $inline_style .= '#'.$class_section.'{background-image: url("'.$right_image.'");} ';
                ?>
                <div class="cshms-row ms-section section2" id="<?php echo $class_section ?>">
                    <div class="cshms-content <?php echo esc_attr($same_class)?>">
                        <?php echo $right_text; ?>
                    </div>
                </div>
                <?php
            }
            ?></div><?php
            wp_add_inline_style('cshms_public_style', $inline_style);
        }
        ?>
    </div>
<?php wp_footer(); ?>
<?php
