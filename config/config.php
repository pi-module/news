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
            'title'  => _a('Admin'),
            'name'   => 'admin'
        ),
        array(
            'title'  => _a('Show'),
            'name'   => 'show'
        ),
        array(
            'title'  => _a('Feed'),
            'name'   => 'feed'
        ),
        array(
            'title'  => _a('Image'),
            'name'   => 'image'
        ),
        array(
            'title'  => _a('File'),
            'name'   => 'file'
        ),
        array(
            'title'  => _a('Social'),
            'name'   => 'social'
        ),
        array(
            'title'  => _a('Spotlight'),
            'name'   => 'spotlight'
        ),
        array(
            'title'  => _a('Vote'),
            'name'   => 'vote'
        ),
        array(
            'title'  => _a('Favourite'),
            'name'   => 'favourite'
        ),
        array(
            'title'  => _a('Texts'),
            'name'   => 'text'
        ),
    ),
    'item' => array(
        // Generic
        'advertisement' => array(
            'title'        => _a('Advertisement'),
            'edit'         => 'textarea',
        ),
        // Admin
        'admin_perpage' => array(
            'category'     => 'admin',
            'title'        => _a('Perpage'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 50
        ),
        'admin_setpage' => array(
            'category'     => 'admin',
            'title'        => _a('Set topic as system page for support customise layout for blocks'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 0
        ),
        'admin_setauthor' => array(
            'category'     => 'admin',
            'title'        => _a('Set story authors'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'admin_json' => array(
            'category'     => 'admin',
            'title'        => _a('Active json output'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        // Show
        'style' => array(
            'title'        => _a('Show Style'),
            'description'  => ' ',
            'edit'         => array(
                'type'         => 'select',
                'options'      => array(
                    'options'  => array(
                        'news'          => _a('News'),
                        'list'          => _a('List'),
                        'table'         => _a('Table'),
                        'media'         => _a('Media'),
                        'spotlight'     => _a('Spotlight'),
                        'topic'         => _a('Topic'),
                    ),
                ),
            ),
            'filter'       => 'string',
            'value'        => 'news',
            'category'     => 'show',
        ),
        'show_perpage' => array(
            'category'     => 'show',
            'title'        => _a('Perpage'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 5
        ),
        'show_columns' => array(
            'title'        => _a('Columns'),
            'description'  => ' ',
            'edit'         => array(
                'type'         => 'select',
                'options'      => array(
                    'options'  => array(
                        1 => _a('One column'),
                        2 => _a('Two columns'),
                        3 => _a('Three columns'),
                        4 => _a('Four columns'),
                    ),
                ),
            ),
            'filter'       => 'number_int',
            'value'        => 1,
            'category'     => 'show',
        ),
        'show_subid' => array(
            'category'     => 'show',
            'title'        => _a('Show Subtopic stories on main topic'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'show_topic' => array(
            'category'     => 'show',
            'title'        => _a('Show Topic'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'show_topicinfo' => array(
            'category'     => 'show',
            'title'        => _a('Show Topic Information'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'show_author' => array(
            'category'     => 'show',
            'title'        => _a('Show author'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'show_date' => array(
            'category'     => 'show',
            'title'        => _a('Show Date'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'show_pdf' => array(
            'category'     => 'show',
            'title'        => _a('Show Pdf'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'show_print' => array(
            'category'     => 'show',
            'title'        => _a('Show Print'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'show_mail' => array(
            'category'     => 'show',
            'title'        => _a('Show Mail'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'show_hits' => array(
            'category'     => 'show',
            'title'        => _a('Show Hits'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'show_tag' => array(
            'category'     => 'show',
            'title'        => _a('Show Tags'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'show_attach' => array(
            'category'     => 'show',
            'title'        => _a('Show attach files'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'show_extra' => array(
            'category'     => 'show',
            'title'        => _a('Show extra information'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'show_nav' => array(
            'category'     => 'show',
            'title'        => _a('Show navigation'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'show_spotlight' => array(
            'category'     => 'show',
            'title'        => _a('Show spotlight'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 0
        ),
        'show_related' => array(
            'category'     => 'show',
            'title'        => _a('Show related news'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'related_num' => array(
            'category'     => 'show',
            'title'        => _a('related number'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 10
        ),
        'daylimit' => array(
            'category'     => 'show',
            'title'        => _a('Just show news from X days ago'),
            'description'  => _a('Set 0 for show all news, Or set day number for limit news in days'),
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 0
        ),
        // Feed 
        'feed_icon' => array(
            'category'     => 'feed',
            'title'        => _a('Show feed icon'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'feed_num' => array(
            'category'     => 'feed',
            'title'        => _a('Feed number'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 10
        ),
        // Image
        'image_size' => array(
            'category'     => 'image',
            'title'        => _a('Image Size'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 1000000
        ),
        'image_path' => array(
            'category'     => 'image',
            'title'        => _a('Image path'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'string',
            'value'        => 'news/image'
        ),
        'image_extension' => array(
            'category'     => 'image',
            'title'        => _a('Image Extension'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'string',
            'value'        => 'jpg,jpeg,png,gif'
        ),
        'image_largeh' => array(
            'category'     => 'image',
            'title'        => _a('Large Image height'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 600
        ),
        'image_largew' => array(
            'category'     => 'image',
            'title'        => _a('Large Image width'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 800
        ),
        'image_mediumh' => array(
            'category'     => 'image',
            'title'        => _a('Medium Image height'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 300
        ),
        'image_mediumw' => array(
            'category'     => 'image',
            'title'        => _a('Medium Image width'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 400
        ),
        'image_thumbh' => array(
            'category'     => 'image',
            'title'        => _a('Thumb Image height'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 150
        ),
        'image_thumbw' => array(
            'category'     => 'image',
            'title'        => _a('Thumb Image width'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 200
        ),
        'image_topic_largeh' => array(
            'category'     => 'image',
            'title'        => _a('Large Image height for topic'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 800
        ),
        'image_topic_largew' => array(
            'category'     => 'image',
            'title'        => _a('Large Image width for topic'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 560
        ),
        'image_topic_mediumh' => array(
            'category'     => 'image',
            'title'        => _a('Medium Image height for topic'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 300
        ),
        'image_topic_mediumw' => array(
            'category'     => 'image',
            'title'        => _a('Medium Image width for topic'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 210
        ),
        'image_topic_thumbh' => array(
            'category'     => 'image',
            'title'        => _a('Thumb Image height for topic'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 200
        ),
        'image_topic_thumbw' => array(
            'category'     => 'image',
            'title'        => _a('Thumb Image width for topic'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 140
        ),
        'image_author_largeh' => array(
            'category'     => 'image',
            'title'        => _a('Large Image height for author'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 800
        ),
        'image_author_largew' => array(
            'category'     => 'image',
            'title'        => _a('Large Image width for author'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 560
        ),
        'image_author_mediumh' => array(
            'category'     => 'image',
            'title'        => _a('Medium Image height for author'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 300
        ),
        'image_author_mediumw' => array(
            'category'     => 'image',
            'title'        => _a('Medium Image width for author'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 210
        ),
        'image_author_thumbh' => array(
            'category'     => 'image',
            'title'        => _a('Thumb Image height for author'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 200
        ),
        'image_author_thumbw' => array(
            'category'     => 'image',
            'title'        => _a('Thumb Image width for author'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 140
        ),
        'image_lightbox' => array(
            'category'     => 'image',
            'title'        => _a('Use lightbox'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'image_watermark' => array(
            'category'     => 'image',
            'title'        => _a('Add Watermark'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 0
        ),
        'image_watermark_source' => array(
            'category'     => 'image',
            'title'        => _a('Watermark Image'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'string',
            'value'        => ''
        ),
        'image_watermark_position' => array(
            'title'        => _a('Watermark Positio'),
            'description'  => '',
            'edit'         => array(
                'type'         => 'select',
                'options'      => array(
                    'options'  => array(
                        'top-left'      => _a('Top Left'),
                        'top-right'     => _a('Top Right'),
                        'bottom-left'   => _a('Bottom Left'),
                        'bottom-right'  => _a('Bottom Right'),
                    ),
                ),
            ),
            'filter'       => 'text',
            'value'        => 'bottom-right',
            'category'     => 'image',
        ),
        // Print
        /*
        'print_logo' => array(
            'category'      => 'print',
            'title'         => _a('Show logo'),
            'description'   => '',
            'edit'          => 'checkbox',
            'filter'        => 'number_int',
            'value'       => 1
        ),
        'print_float' => array(
           'title'         => _a('Image float'),
	        'description'   => ' ',
	        'edit'          => array(
                'type'      => 'select',
                'options'   => array(
                    'options' => array(
	                    'center' => _a('Center'),
	                    'left' => _a('Left'),
	                    'right' => _a('Right'),
	                ),
                ),
            ),
	        'filter'        => 'string',
	        'value'         => 'center',
	        'category'      => 'print',
        ),
        'print_path' => array(
            'category'      => 'print',
            'title'         => _a('Logo path'),
            'description'   => '',
            'edit'          => 'text',
            'filter'        => 'string',
            'value'       => 'static/image/logo.ong'
        ),
        'print_title' => array(
            'category'      => 'print',
            'title'         => _a('Show title'),
            'description'   => '',
            'edit'          => 'checkbox',
            'filter'        => 'number_int',
            'value'       => 1
        ),
        'print_image' => array(
            'category'      => 'print',
            'title'         => _a('Show image'),
            'description'   => '',
            'edit'          => 'checkbox',
            'filter'        => 'number_int',
            'value'       => 1
        ),
         'print_short' => array(
            'category'      => 'print',
            'title'         => _a('Show short'),
            'description'   => '',
            'edit'          => 'checkbox',
            'filter'        => 'number_int',
            'value'       => 1
        ),
        'print_text' => array(
            'category'      => 'print',
            'title'         => _a('Show text'),
            'description'   => '',
            'edit'          => 'checkbox',
            'filter'        => 'number_int',
            'value'       => 1
        ),
        'print_date' => array(
            'category'      => 'print',
            'title'         => _a('Show date'),
            'description'   => '',
            'edit'          => 'checkbox',
            'filter'        => 'number_int',
            'value'       => 1
        ),
        'print_author' => array(
            'category'      => 'print',
            'title'         => _a('Show uid'),
            'description'   => '',
            'edit'          => 'checkbox',
            'filter'        => 'number_int',
            'value'       => 1
        ),
        'print_link' => array(
            'category'      => 'print',
            'title'         => _a('Show link'),
            'description'   => '',
            'edit'          => 'checkbox',
            'filter'        => 'number_int',
            'value'       => 1
        ),
        */
        // Social
        'social_gplus' => array(
            'category'     => 'social',
            'title'        => _a('Show Google Plus'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'social_facebook' => array(
            'category'     => 'social',
            'title'        => _a('Show facebook'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'social_twitter' => array(
            'category'     => 'social',
            'title'        => _a('Show Twitter'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        // Spotlight
        'spotlight_number' => array(
            'category'     => 'spotlight',
            'title'        => _a('Number of stories'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        'spotlight_image' => array(
            'category'     => 'spotlight',
            'title'        => _a('Show Image for sub spotlights'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 0
        ),
        // Vote
        'vote_bar' => array(
            'category'     => 'vote',
            'title'        => _a('Use vote system'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        // favourite
        'favourite_bar' => array(
            'category'     => 'favourite',
            'title'        => _a('Use favourite system'),
            'description'  => '',
            'edit'         => 'checkbox',
            'filter'       => 'number_int',
            'value'        => 1
        ),
        // Texts
        'text_title' => array(
            'category'     => 'text',
            'title'        => _a('Module main title'),
            'description'  => _a('Title for main page and all non-title pages'),
            'edit'         => 'text',
            'filter'       => 'string',
            'value'        => _a('Latest News from this website'),
        ),
        'text_description' => array(
            'category'     => 'text',
            'title'        => _a('Module main description'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'string',
            'value'        => _a('Latest News from this website'),
        ),
        'text_keywords' => array(
            'category'     => 'text',
            'title'        => _a('Module main keywords'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'string',
            'value'        => _a('news,topic,story,website'),
        ),
        // File
        'file_size' => array(
            'category'     => 'file',
            'title'        => _a('File Size'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 1000000
        ),
        'file_path' => array(
            'category'     => 'file',
            'title'        => _a('File path'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'string',
            'value'        => 'news'
        ),
        'file_extension' => array(
            'category'     => 'file',
            'title'        => _a('File Extension'),
            'description'  => '',
            'edit'         => 'textarea',
            'filter'       => 'string',
            'value'        => 'jpg,png,gif,avi,flv,mp3,mp4,pdf,docs,xdocs,zip,rar'
        ),
    ),
);