<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
return array(
    'category' => array(
        array(
            'title' => __('Admin'),
            'name' => 'admin'
        ),
        array(
            'title' => __('Show'),
            'name' => 'show'
        ),
        array(
            'title' => __('Feed'),
            'name' => 'feed'
        ),
        array(
            'title' => __('Image'),
            'name' => 'image'
        ),
        array(
            'title' => __('Social'),
            'name' => 'social'
        ),
        array(
            'title' => __('Spotlight'),
            'name' => 'spotlight'
        ),
        array(
            'title' => __('Vote'),
            'name' => 'vote'
        ),
        array(
            'title' => __('Texts'),
            'name' => 'text'
        ),
        array(
            'title' => __('File'),
            'name' => 'file'
        ),
        array(
            'title' => __('Favorite'),
            'name' => 'favorite'
        ),
    ),
    'item' => array(
        // Generic
        'advertisement' => array(
            'title' => __('Advertisement'),
            'edit' => 'textarea',
        ),
        // Admin
        'admin_perpage' => array(
            'category' => 'admin',
            'title' => __('Perpage'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 10
        ),
        // Show
        'style' => array(
            'title' => __('Show Style'),
            'description' => ' ',
            'edit' => array(
                'type' => 'select',
                'options' => array(
                    'options' => array(
                        'news' => __('News'),
                        'list' => __('List'),
                        'table' => __('Table'),
                        'media' => __('Media'),
                        'spotlight' => __('Spotlight'),
                    ),
                ),
            ),
            'filter' => 'string',
            'value' => 'news',
            'category' => 'show',
        ),
        'show_perpage' => array(
            'category' => 'show',
            'title' => __('Perpage'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 5
        ),
        'show_columns' => array(
            'title' => __('Columns'),
            'description' => ' ',
            'edit' => array(
                'type' => 'select',
                'options' => array(
                    'options' => array(
                        1 => __('One column'),
                        2 => __('Two columns'),
                        3 => __('Three columns'),
                        4 => __('Four columns'),
                    ),
                ),
            ),
            'filter' => 'number_int',
            'value' => 1,
            'category' => 'show',
        ),
        'show_topic' => array(
            'category' => 'show',
            'title' => __('Show Topic'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'show_topicinfo' => array(
            'category' => 'show',
            'title' => __('Show Topic Information'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'show_writer' => array(
            'category' => 'show',
            'title' => __('Show writer'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'show_date' => array(
            'category' => 'show',
            'title' => __('Show Date'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'show_pdf' => array(
            'category' => 'show',
            'title' => __('Show Pdf'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'show_print' => array(
            'category' => 'show',
            'title' => __('Show Print'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'show_mail' => array(
            'category' => 'show',
            'title' => __('Show Mail'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'show_hits' => array(
            'category' => 'show',
            'title' => __('Show Hits'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'show_tag' => array(
            'category' => 'show',
            'title' => __('Show Tags'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'show_attach' => array(
            'category' => 'show',
            'title' => __('Show attach files'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'show_extra' => array(
            'category' => 'show',
            'title' => __('Show extra information'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'show_nav' => array(
            'category' => 'show',
            'title' => __('Show navigation'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'show_spotlight' => array(
            'category' => 'show',
            'title' => __('Show spotlight'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'show_related' => array(
            'category' => 'show',
            'title' => __('Show related news'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'related_num' => array(
            'category' => 'show',
            'title' => __('related number'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 10
        ),
        'daylimit' => array(
            'category' => 'show',
            'title' => __('Just show news from X days ago'),
            'description' => __('Set 0 for show all news, Or set day number for limit news in days'),
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 0
        ),
        // Feed 
        'feed_icon' => array(
            'category' => 'feed',
            'title' => __('Show feed icon'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'feed_num' => array(
            'category' => 'feed',
            'title' => __('Feed number'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 10
        ),
        // Image
        'image_size' => array(
            'category' => 'image',
            'title' => __('Image Size'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 1000000
        ),
        'image_path' => array(
            'category' => 'image',
            'title' => __('Image path'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'string',
            'value' => 'news/image'
        ),
        'image_extension' => array(
            'category' => 'image',
            'title' => __('Image Extension'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'string',
            'value' => 'jpg,jpeg,png,gif'
        ),
        'image_largeh' => array(
            'category' => 'image',
            'title' => __('Large Image height'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 800
        ),
        'image_largew' => array(
            'category' => 'image',
            'title' => __('Large Image width'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 800
        ),
        'image_mediumh' => array(
            'category' => 'image',
            'title' => __('Medium Image height'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 300
        ),
        'image_mediumw' => array(
            'category' => 'image',
            'title' => __('Medium Image width'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 300
        ),
        'image_thumbh' => array(
            'category' => 'image',
            'title' => __('Thumb Image height'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 150
        ),
        'image_thumbw' => array(
            'category' => 'image',
            'title' => __('Thumb Image width'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 150
        ),
        'image_lightbox' => array(
            'category' => 'image',
            'title' => __('Use lightbox'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'image_watermark' => array(
            'category' => 'image',
            'title' => __('Add Watermark'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 0
        ),
        'image_watermark_source' => array(
            'category' => 'image',
            'title' => __('Watermark Image'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'string',
            'value' => ''
        ),
        'image_watermark_position' => array(
            'title' => __('Watermark Positio'),
            'description' => '',
            'edit' => array(
                'type' => 'select',
                'options' => array(
                    'options' => array(
                        'top-left' => __('Top Left'),
                        'top-right' => __('Top Right'),
                        'bottom-left' => __('Bottom Left'),
                        'bottom-right' => __('Bottom Right'),
                    ),
                ),
            ),
            'filter' => 'text',
            'value' => 'bottom-right',
            'category' => 'image',
        ),
        // Print
        /*
        'print_logo' => array(
            'category'      => 'print',
            'title'         => __('Show logo'),
            'description'   => '',
            'edit'          => 'checkbox',
            'filter'        => 'number_int',
            'value'       => 1
        ),
        'print_float' => array(
           'title'         => __('Image float'),
	        'description'   => ' ',
	        'edit'          => array(
                'type'      => 'select',
                'options'   => array(
                    'options' => array(
	                    'center' => __('Center'),
	                    'left' => __('Left'),
	                    'right' => __('Right'),
	                ),
                ),
            ),
	        'filter'        => 'string',
	        'value'         => 'center',
	        'category'      => 'print',
        ),
        'print_path' => array(
            'category'      => 'print',
            'title'         => __('Logo path'),
            'description'   => '',
            'edit'          => 'text',
            'filter'        => 'string',
            'value'       => 'static/image/logo.ong'
        ),
        'print_title' => array(
            'category'      => 'print',
            'title'         => __('Show title'),
            'description'   => '',
            'edit'          => 'checkbox',
            'filter'        => 'number_int',
            'value'       => 1
        ),
        'print_image' => array(
            'category'      => 'print',
            'title'         => __('Show image'),
            'description'   => '',
            'edit'          => 'checkbox',
            'filter'        => 'number_int',
            'value'       => 1
        ),
         'print_short' => array(
            'category'      => 'print',
            'title'         => __('Show short'),
            'description'   => '',
            'edit'          => 'checkbox',
            'filter'        => 'number_int',
            'value'       => 1
        ),
        'print_text' => array(
            'category'      => 'print',
            'title'         => __('Show text'),
            'description'   => '',
            'edit'          => 'checkbox',
            'filter'        => 'number_int',
            'value'       => 1
        ),
        'print_date' => array(
            'category'      => 'print',
            'title'         => __('Show date'),
            'description'   => '',
            'edit'          => 'checkbox',
            'filter'        => 'number_int',
            'value'       => 1
        ),
        'print__uthor' => array(
            'category'      => 'print',
            'title'         => __('Show uid'),
            'description'   => '',
            'edit'          => 'checkbox',
            'filter'        => 'number_int',
            'value'       => 1
        ),
        'print_link' => array(
            'category'      => 'print',
            'title'         => __('Show link'),
            'description'   => '',
            'edit'          => 'checkbox',
            'filter'        => 'number_int',
            'value'       => 1
        ),
        */
        // Social
        'social_gplus' => array(
            'category' => 'social',
            'title' => __('Show Google Plus'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'social_facebook' => array(
            'category' => 'social',
            'title' => __('Show facebook'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'social_twitter' => array(
            'category' => 'social',
            'title' => __('Show Twitter'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        // Spotlight
        'spotlight_number' => array(
            'category' => 'spotlight',
            'title' => __('Number of stories'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 1
        ),
        'spotlight_image' => array(
            'category' => 'spotlight',
            'title' => __('Show Image for sub spotlights'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 0
        ),
        // Vote
        'vote_bar' => array(
            'category' => 'vote',
            'title' => __('Use vote system'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        // Texts
        'text_title' => array(
            'category' => 'text',
            'title' => __('Module main title'),
            'description' => __('Title for main page and all non-title pages'),
            'edit' => 'text',
            'filter' => 'string',
            'value' => 'Latest News from this website'
        ),
        'text_description' => array(
            'category' => 'text',
            'title' => __('Module main description'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'string',
            'value' => 'Latest News from this website'
        ),
        'text_keywords' => array(
            'category' => 'text',
            'title' => __('Module main keywords'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'string',
            'value' => 'news,topic,story,website'
        ),
        // File
        'file_size' => array(
            'category' => 'file',
            'title' => __('File Size'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 1000000
        ),
        'file_path' => array(
            'category' => 'file',
            'title' => __('File path'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'string',
            'value' => 'news'
        ),
        'file_extension' => array(
            'category' => 'file',
            'title' => __('File Extension'),
            'description' => '',
            'edit' => 'textarea',
            'filter' => 'string',
            'value' => 'jpg,png,gif,avi,flv,mp3,mp4,pdf,docs,xdocs,zip,rar'
        ),
        // favorite
        'favorite_bar' => array(
            'category' => 'favorite',
            'title' => __('Use favorite system'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
    ),
);