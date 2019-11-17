<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
return [
    'category' => [
        [
            'title' => _a('Admin'),
            'name'  => 'admin',
        ],
        /* [
            'title' => _a('Json output'),
            'name'  => 'json',
        ], */
        [
            'title' => _a('Show'),
            'name'  => 'show',
        ],
        [
            'title' => _a('Feed'),
            'name'  => 'feed',
        ],
        [
            'title' => _a('Image'),
            'name'  => 'image',
        ],
        [
            'title' => _a('File'),
            'name'  => 'file',
        ],
        [
            'title' => _a('Social'),
            'name'  => 'social',
        ],
        [
            'title' => _a('Spotlight'),
            'name'  => 'spotlight',
        ],
        [
            'title' => _a('Vote'),
            'name'  => 'vote',
        ],
        [
            'title' => _a('Favourite'),
            'name'  => 'favourite',
        ],
        [
            'title' => _a('Micro blog'),
            'name'  => 'microblog',
        ],
    ],
    'item'     => [
        // Admin
        'admin_perpage'            => [
            'category'    => 'admin',
            'title'       => _a('Perpage'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 50,
        ],
        'admin_setpage'            => [
            'category'    => 'admin',
            'title'       => _a('Set topic as system page for support customise layout for blocks'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 0,
        ],
        'admin_setauthor'          => [
            'category'    => 'admin',
            'title'       => _a('Set story authors'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'admin_time_publish'       => [
            'category'    => 'admin',
            'title'       => _a('Set custom publish time'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 0,
        ],
        'admin_text_extra'         => [
            'category'    => 'admin',
            'title'       => _a('Set custom html code'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 0,
        ],
        'admin_deactivate_view'    => [
            'category'    => 'admin',
            'title'       => _a('Deactivate module front pages'),
            'description' => _a(
                'Deactivate module front pages by 404 error code, useful if you want use module as service for event and blog and dont want publish any story as news'
            ),
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 0,
        ],
        'admin_confirmation_limit' => [
            'category'    => 'admin',
            'title'       => _a('Confirmation limit'),
            'description' => _a(
                'Set limit for confirmation and publish stories, if checked just selected role can be publish news'
            ),
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 0,
        ],
        'admin_confirmation_role'  => [
            'category'    => 'admin',
            'title'       => _a('Confirmation role'),
            'description' => _a('Set allowed role name than have access to confirm and publish news'),
            'edit'        => 'text',
            'filter'      => 'string',
            'value'       => 'admin',
        ],
        // Json
        /* 'admin_json'               => [
            'category'    => 'json',
            'title'       => _a('Active json output'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'json_perpage'             => [
            'category'    => 'json',
            'title'       => _a('Perpage on json output'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 100,
        ],
        'json_check_password'      => [
            'category'    => 'json',
            'title'       => _a('Check password for json output'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 0,
        ],
        'json_password'            => [
            'category'    => 'json',
            'title'       => _a('Password for json output'),
            'description' => _a('After use on mobile device , do not change it'),
            'edit'        => 'text',
            'filter'      => 'string',
            'value'       => md5(rand(1, 99999)),
        ], */
        // Show
        'style'                    => [
            'title'       => _a('Show Style'),
            'description' => ' ',
            'edit'        => [
                'type'    => 'select',
                'options' => [
                    'options' => [
                        'news'      => _a('News'),
                        'list'      => _a('List'),
                        'table'     => _a('Table'),
                        'media'     => _a('Media'),
                        'spotlight' => _a('Spotlight'),
                        'topic'     => _a('Topic'),
                    ],
                ],
            ],
            'filter'      => 'string',
            'value'       => 'news',
            'category'    => 'show',
        ],
        'show_perpage'             => [
            'category'    => 'show',
            'title'       => _a('Perpage'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 5,
        ],
        'show_columns'             => [
            'title'       => _a('Columns'),
            'description' => ' ',
            'edit'        => [
                'type'    => 'select',
                'options' => [
                    'options' => [
                        1 => _a('One column'),
                        2 => _a('Two columns'),
                        3 => _a('Three columns'),
                        4 => _a('Four columns'),
                    ],
                ],
            ],
            'filter'      => 'number_int',
            'value'       => 1,
            'category'    => 'show',
        ],
        'show_order_link'          => [
            'title'       => _a('Story order'),
            'description' => _a('Story list order options'),
            'edit'        => [
                'type'    => 'select',
                'options' => [
                    'options' => [
                        'publishDESC' => _a('Publish time DESC'),
                        'publishASC'  => _a('Publish time ASC'),
                    ],
                ],
            ],
            'filter'      => 'string',
            'value'       => 'publishDESC',
            'category'    => 'show',
        ],
        'show_subid'               => [
            'category'    => 'show',
            'title'       => _a('Show Subtopic stories on main topic'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'show_topic'               => [
            'category'    => 'show',
            'title'       => _a('Show Topic'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'show_topicinfo'           => [
            'category'    => 'show',
            'title'       => _a('Show Topic Information'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'show_author'              => [
            'category'    => 'show',
            'title'       => _a('Show author'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'show_date'                => [
            'category'    => 'show',
            'title'       => _a('Show Date'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'show_hits'                => [
            'category'    => 'show',
            'title'       => _a('Show Hits'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'show_tag'                 => [
            'category'    => 'show',
            'title'       => _a('Show Tags'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'show_attach'              => [
            'category'    => 'show',
            'title'       => _a('Show attach files'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'show_attribute'           => [
            'category'    => 'show',
            'title'       => _a('Show attribute information'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'show_nav'                 => [
            'category'    => 'show',
            'title'       => _a('Show navigation'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'show_spotlight'           => [
            'category'    => 'show',
            'title'       => _a('Show spotlight'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 0,
        ],
        'view_breadcrumbs'         => [
            'category'    => 'show',
            'title'       => _a('Show breadcrumbs'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'view_breadcrumbs_topic'   => [
            'category'    => 'show',
            'title'       => _a('Show topics on breadcrumbs'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'show_related'             => [
            'category'    => 'show',
            'title'       => _a('Show related news'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'related_num'              => [
            'category'    => 'show',
            'title'       => _a('related number'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 10,
        ],
        'show_latest'              => [
            'category'    => 'show',
            'title'       => _a('Show latest news'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'latest_num'               => [
            'category'    => 'show',
            'title'       => _a('latest number'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 10,
        ],
        'day_limit'                => [
            'category'    => 'show',
            'title'       => _a('Just show news from X days ago'),
            'description' => _a('Set 0 for show all news, Or set day number for limit news in days'),
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 0,
        ],
        'story_all_hits'           => [
            'category'    => 'show',
            'title'       => _a('Include all hits'),
            'description' => _a('Include all page refresh as hits, if not check use SESSION check for update hits'),
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 0,
        ],
        // Feed
        'feed_icon'                => [
            'category'    => 'feed',
            'title'       => _a('Show feed icon'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'feed_num'                 => [
            'category'    => 'feed',
            'title'       => _a('Feed number'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 10,
        ],
        // Image
        'image_minw'               => [
            'category'    => 'image',
            'title'       => _t('Min Image width (upload)'),
            'description' => _t('This config can override media module value'),
            'edit'        => 'text',
            'filter'      => 'string',
            'value'       => '',
        ],
        'image_minh'               => [
            'category'    => 'image',
            'title'       => _t('Min Image height (upload)'),
            'description' => _t('This config can override media module value'),
            'edit'        => 'text',
            'filter'      => 'string',
            'value'       => '',
        ],
        'image_quality'            => [
            'category'    => 'image',
            'title'       => _a('Image quality'),
            'description' => _a('Between 0 to 100 and support both of JPG and PNG, default is 75. This config can override media module value'),
            'edit'        => 'text',
            'filter'      => 'string',
            'value'       => 75,
        ],

        'image_default'            => [
            'category'    => 'image',
            'title'       => _a('Use default if image not set'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 0,
        ],
        'image_size'               => [
            'category'    => 'image',
            'title'       => _a('Image Size'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 1000000,
        ],
        'image_path'               => [
            'category'    => 'image',
            'title'       => _a('Image path'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'string',
            'value'       => 'news/image',
        ],
        'image_extension'          => [
            'category'    => 'image',
            'title'       => _a('Image Extension'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'string',
            'value'       => 'jpg,jpeg,png,gif',
        ],
        'image_largeh'             => [
            'category'    => 'image',
            'title'       => _a('Large Image height'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 1200,
        ],
        'image_largew'             => [
            'category'    => 'image',
            'title'       => _a('Large Image width'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 1200,
        ],
        'image_itemh'              => [
            'category'    => 'image',
            'title'       => _a('Item Image height'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 800,
        ],
        'image_itemw'              => [
            'category'    => 'image',
            'title'       => _a('Item Image width'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 800,
        ],
        'image_mediumh'            => [
            'category'    => 'image',
            'title'       => _a('Medium Image height'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 500,
        ],
        'image_mediumw'            => [
            'category'    => 'image',
            'title'       => _a('Medium Image width'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 500,
        ],
        'image_thumbh'             => [
            'category'    => 'image',
            'title'       => _a('Thumb Image height'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 250,
        ],
        'image_thumbw'             => [
            'category'    => 'image',
            'title'       => _a('Thumb Image width'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 250,
        ],
        'image_lightbox'           => [
            'category'    => 'image',
            'title'       => _a('Use lightbox'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'image_watermark'          => [
            'category'    => 'image',
            'title'       => _a('Add Watermark'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 0,
        ],
        'image_watermark_source'   => [
            'category'    => 'image',
            'title'       => _a('Watermark Image'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'string',
            'value'       => '',
        ],
        'image_watermark_position' => [
            'title'       => _a('Watermark Positio'),
            'description' => '',
            'edit'        => [
                'type'    => 'select',
                'options' => [
                    'options' => [
                        'top-left'     => _a('Top Left'),
                        'top-right'    => _a('Top Right'),
                        'bottom-left'  => _a('Bottom Left'),
                        'bottom-right' => _a('Bottom Right'),
                    ],
                ],
            ],
            'filter'      => 'text',
            'value'       => 'bottom-right',
            'category'    => 'image',
        ],
        // Social
        'social_sharing'           => [
            'title'       => _t('Social sharing items'),
            'description' => '',
            'edit'        => [
                'type'    => 'multi_checkbox',
                'options' => [
                    'options' => Pi::service('social_sharing')->getList(),
                ],
            ],
            'filter'      => 'array',
            'category'    => 'social',
        ],
        // Spotlight
        'spotlight_number'         => [
            'category'    => 'spotlight',
            'title'       => _a('Number of stories'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        'spotlight_image'          => [
            'category'    => 'spotlight',
            'title'       => _a('Show Image for sub spotlights'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 0,
        ],
        'spotlight_registry'       => [
            'category'    => 'spotlight',
            'title'       => _a('Spotlight story number for registry'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 7,
        ],
        // Vote
        'vote_bar'                 => [
            'category'    => 'vote',
            'title'       => _a('Use vote system'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        // favourite
        'favourite_bar'            => [
            'category'    => 'favourite',
            'title'       => _a('Use favourite system'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        // Texts
        'text_description_index'   => [
            'category'    => 'head_meta',
            'title'       => _a('Description for index page'),
            'description' => '',
            'edit'        => 'textarea',
            'filter'      => 'string',
            'value'       => '',
        ],
        'force_replace_space'      => [
            'category'    => 'head_meta',
            'title'       => _a('Force replace space by comma(,)'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 1,
        ],
        // File
        'file_size'                => [
            'category'    => 'file',
            'title'       => _a('File Size'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 1000000,
        ],
        'file_path'                => [
            'category'    => 'file',
            'title'       => _a('File path'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'string',
            'value'       => 'news',
        ],
        'file_extension'           => [
            'category'    => 'file',
            'title'       => _a('File Extension'),
            'description' => '',
            'edit'        => 'textarea',
            'filter'      => 'string',
            'value'       => 'jpg,png,gif,avi,flv,mp3,mp4,pdf,docs,xdocs,zip,rar',
        ],
        // microblog
        'microblog_active'         => [
            'category'    => 'microblog',
            'title'       => _a('Active microblog system'),
            'description' => '',
            'edit'        => 'checkbox',
            'filter'      => 'number_int',
            'value'       => 0,
        ],
        'microblog_perpage'        => [
            'category'    => 'microblog',
            'title'       => _a('Micro blog perpage'),
            'description' => '',
            'edit'        => 'text',
            'filter'      => 'number_int',
            'value'       => 20,
        ],
    ],
];