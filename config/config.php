<?php
/**
 * News module config
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Copyright (c) Pi Engine http://www.xoopsengine.org
 * @license         http://www.xoopsengine.org/license New BSD License
 * @author          Hossein Azizabadi <azizabadi@faragostaresh.com>
 * @since           3.0
 * @package         Module\News
 * @version         $Id$
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
        'show_homepage' => array(
            'title' => __('Topic homepage'),
            'description' => ' ',
            'edit' => array(
                'type' => 'select',
                'options' => array(
                    'options' => array(
                        'type1' => __('All storys from this topic and all subtopics'),
                        'type2' => __('All storys from just this topic'),
                        'type3' => __('All storys from just all subtopics'),
                    ),
                ),
            ),
            'filter' => 'string',
            'value' => 'type1',
            'category' => 'show',
        ),
        'show_shwotype' => array(
            'title' => __('Show type'),
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
        'show_tags' => array(
            'category' => 'show',
            'title' => __('Tags'),
            'description' => __('Number of tags in tag controller'),
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 50
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
        'show_author' => array(
            'category' => 'show',
            'title' => __('Show Author'),
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
        'show_hits' => array(
            'category' => 'show',
            'title' => __('Show Hits'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'show_nav' => array(
            'category' => 'show',
            'title' => __('Show Nav'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'show_coms' => array(
            'category' => 'show',
            'title' => __('Show Comments'),
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
        'show_reporter' => array(
            'category' => 'show',
            'title' => __('All user post'),
            'description' => __('Link to reporter page'),
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
            'value' => 'jpg,png,gif'
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
            'value' => 500
        ),
        'image_mediumw' => array(
            'category' => 'image',
            'title' => __('Medium Image width'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 500
        ),
        'image_thumbh' => array(
            'category' => 'image',
            'title' => __('Thumb Image height'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 250
        ),
        'image_thumbw' => array(
            'category' => 'image',
            'title' => __('Thumb Image width'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 250
        ),
        'image_lightbox' => array(
            'category' => 'image',
            'title' => __('Use lightbox'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'image_float' => array(
            'title' => __('Image float'),
            'description' => ' ',
            'edit' => array(
                'type' => 'select',
                'options' => array(
                    'options' => array(
                        'left' => __('Left'),
                        'right' => __('Right'),
                    ),
                ),
            ),
            'filter' => 'string',
            'value' => 'left',
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
            'title'         => __('Show Author'),
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
        'social_bookmark' => array(
            'category' => 'social',
            'title' => __('Show Bookmark'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
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
            'title' => __('Show twitter'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        // Spotlight
        'spotlight_number' => array(
            'category' => 'spotlight',
            'title' => __('Number of stores'),
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
        'vote_type' => array(
            'category' => 'vote',
            'title' => __('VoteBar type'),
            'description' => '',
            'filter' => 'string',
            'value' => 'plus',
            'edit' => array(
                'type' => 'select',
                'options' => array(
                    'options' => array(
                        'plus' => __('Plus'),
                        'star' => __('Star'),
                    ),
                ),
            ),
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