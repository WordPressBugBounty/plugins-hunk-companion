<?php
$wp_customize->add_setting( 'open_mart_disable_product_list_sec', array(
                'default'               => false,
                'sanitize_callback'     => 'open_mart_sanitize_checkbox',
            ) );
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'open_mart_disable_product_list_sec', array(
                'label'                 => esc_html__('Disable Section', 'hunk-companion'),
                'type'                  => 'checkbox',
                'section'               => 'open_mart_product_slide_list',
                'settings'              => 'open_mart_disable_product_list_sec',
            ) ) );
// section heading
$wp_customize->add_setting('open_mart_product_list_heading', array(
	    'default' => __('Product List','hunk-companion'),
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'open_mart_sanitize_text',
        'transport'         => 'postMessage',
));
$wp_customize->add_control( 'open_mart_product_list_heading', array(
        'label'    => __('Section Heading', 'hunk-companion'),
        'section'  => 'open_mart_product_slide_list',
         'type'       => 'text',
));
//control setting for select options
	$wp_customize->add_setting('open_mart_product_list_cat', array(
	'default' => 0,
	'sanitize_callback' => 'open_mart_sanitize_select',
	) );
	$wp_customize->add_control( 'open_mart_product_list_cat', array(
	'label'   => __('Select Category','hunk-companion'),
	'section' => 'open_mart_product_slide_list',
	'type' => 'select',
	'choices' => open_mart_product_category_list(array('taxonomy' =>'product_cat'),true),
	) );

$wp_customize->add_setting('open_mart_product_list_optn', array(
        'default'        => 'recent',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'open_mart_sanitize_select',
    ));
$wp_customize->add_control('open_mart_product_list_optn', array(
        'settings' => 'open_mart_product_list_optn',
        'label'   => __('Choose Option','hunk-companion'),
        'section' => 'open_mart_product_slide_list',
        'type'    => 'select',
        'choices'    => array(
        'recent'     => __('Recent','hunk-companion'),
        'featured'   => __('Featured','hunk-companion'),
        'random'     => __('Random','hunk-companion'),   
        ),
    ));

$wp_customize->add_setting( 'open_mart_single_row_prdct_list', array(
                'default'               => false,
                'sanitize_callback'     => 'open_mart_sanitize_checkbox',
            ) );
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'open_mart_single_row_prdct_list', array(
                'label'                 => esc_html__('Enable Single Row Slide', 'hunk-companion'),
                'type'                  => 'checkbox',
                'section'               => 'open_mart_product_slide_list',
                'settings'              => 'open_mart_single_row_prdct_list',
            ) ) );


// Add an option to disable the logo.
  $wp_customize->add_setting( 'open_mart_product_list_slide_optn', array(
    'default'           => false,
    'sanitize_callback' => 'open_mart_sanitize_checkbox',
  ) );
  $wp_customize->add_control( new open_mart_Toggle_Control( $wp_customize, 'open_mart_product_list_slide_optn', array(
    'label'       => esc_html__( 'Slide Auto Play', 'hunk-companion' ),
    'section'     => 'open_mart_product_slide_list',
    'type'        => 'toggle',
    'settings'    => 'open_mart_product_list_slide_optn',
  ) ) );

            //For Normal Image Upload
    $wp_customize->add_setting('open_mart_pl_image', array(
       'default'        => '',
       'sanitize_callback' => 'sanitize_text_field'
   ));
   $wp_customize->add_control( new WP_Customize_Upload_Control(
       $wp_customize, 'open_mart_pl_image', array(
       'label'    => __('Banner Image', 'hunk-companion'),
       'section'  => 'open_mart_product_slide_list',
        )
   ));

    $wp_customize->add_setting('open_mart_product_list_img_side', array(
        'default'        => 'right',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'open_mart_sanitize_select',
    ));
    $wp_customize->add_control( 'open_mart_product_list_img_side', array(
        'settings'=> 'open_mart_product_list_img_side',
        'label'   => __('PLace Image On','hunk-companion'),
        'section' => 'open_mart_product_slide_list',
        'type'    => 'select',
        'choices' => array(
        'left'    => __('Left','hunk-companion'),
        'right'   => __('Right (Pro)','hunk-companion'),
            
        ),
    )); 

  $wp_customize->add_setting('open_mart_product_list_slide_doc', array(
    'sanitize_callback' => 'open_mart_sanitize_text',
    ));
  $wp_customize->add_control(new open_mart_Misc_Control( $wp_customize, 'open_mart_product_list_slide_doc',
            array(
        'section'    => 'open_mart_product_slide_list',
        'type'      => 'doc-link',
        'url'       => 'https://themehunk.com/docs/open-mart/#product-list',
        'description' => esc_html__( 'To know more go with this', 'hunk-companion' ),
        'priority'   =>100,
    )));