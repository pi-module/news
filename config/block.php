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
    // Item block
    'item'      => [
        'title'       => _a('Item'),
        'description' => '',
        'render'      => ['block', 'item'],
        'template'    => 'item',
        'config'      => [
            'topicid'           => [
                'title'       => _a('Topic'),
                'description' => '',
                'edit'        => 'Module\News\Form\Element\Topic',
                'filter'      => 'string',
                'value'       => 0,
            ],
            'number'            => [
                'title'       => _a('Number'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => 5,
            ],
            'showdesc'          => [
                'title'       => _a('Show text'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'textlimit'         => [
                'title'       => _a('Text width limit'),
                'description' => _a('Set 0 for no limit'),
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'showdate'          => [
                'title'       => _a('Show date'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'showhits'          => [
                'title'       => _a('Show hits'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'showimage'         => [
                'title'       => _a('Show Images'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'showmore'          => [
                'title'       => _a('Show More link for each story'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'showauthor'        => [
                'title'       => _a('Show authors'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'showFirstMagazine' => [
                'title'       => _a('Big first story on magazine'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'showblockmore'     => [
                'title'       => _a('Show More link to module page'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'day_limit'         => [
                'title'       => _a('Just show news from X days ago'),
                'description' => _a('Set 0 for show all news, Or set day number for limit news in days'),
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'linkblockmore'     => [
                'title'       => _a('Set More link to module page'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'string',
                'value'       => '',
            ],
            'order'             => [
                'title'       => _a('Story order'),
                'description' => _a('Story list order options'),
                'edit'        => [
                    'type'    => 'select',
                    'options' => [
                        'options' => [
                            'publishDESC' => _a('Publish time DESC'),
                            'publishASC'  => _a('Publish time ASC'),
                            'updateDESC'  => _a('Update time DESC'),
                            'updateASC'   => _a('Update time ASC'),
                            'hitsDESC'    => _a('Hits DSC'),
                            'random'      => _a('Random'),
                        ],
                    ],
                ],
                'filter'      => 'text',
                'value'       => 'publishDESC',
            ],
            'type'              => [
                'title'       => _a('Item list type'),
                'description' => '',
                'edit'        => [
                    'type'    => 'select',
                    'options' => [
                        'options' => [
                            'vertical'           => _a('Vertical'),
                            'verticalwide'       => _a('Vertical wide'),
                            'verticalcompressed' => _a('Vertical compressed'),
                            'horizontal'         => _a('Horizontal'),
                            'magazine'           => _a('Magazine'),
                            'magazinewide'       => _a('Magazine wide'),
                            'list'               => _a('List'),
                            'table'              => _a('Table'),
                            'slide'              => _a('Slide'),
                            'slidehover'         => _a('Slide by hover effect'),
                            'carousel'           => _a('Carousel'),
                            'image'              => _a('Image'),
                        ],
                    ],
                ],
                'filter'      => 'text',
                'value'       => 'vertical',
            ],
            'blockEffect'       => [
                'title'       => _a('Use block effects'),
                'description' => _a('Use block effects or set custom effect on theme'),
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'notShowSpotlight'  => [
                'title'       => _a('Not show top stories'),
                'description' => _a(
                    'By active this option, you skip show spotlight stories on this block and can show them on other blocks, it can help you to manage homepage to show all spotlight story on one block and other stories on other blocks'
                ),
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
        ],
    ],

    // Slotlight block
    'spotlight' => [
        'title'       => _a('Spotlight'),
        'description' => '',
        'render'      => ['block', 'spotlight'],
        'template'    => 'spotlight',
        'config'      => [
            'number'        => [
                'title'       => _a('Number'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => 5,
            ],
            'showdesc'      => [
                'title'       => _a('Show text'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'textlimit'     => [
                'title'       => _a('Text width limit'),
                'description' => _a('Set 0 for no limit'),
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'showdate'      => [
                'title'       => _a('Show date'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'showhits'      => [
                'title'       => _a('Show hits'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'showimage'     => [
                'title'       => _a('Show Images'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'showmore'      => [
                'title'       => _a('Show More link for each story'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'showauthor'    => [
                'title'       => _a('Show authors'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'showblockmore' => [
                'title'       => _a('Show More link to module page'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'linkblockmore' => [
                'title'       => _a('Set More link to module page'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'string',
                'value'       => '',
            ],
            'order'         => [
                'title'       => _a('Story order'),
                'description' => _a('Story list order options'),
                'edit'        => [
                    'type'    => 'select',
                    'options' => [
                        'options' => [
                            'publishDESC' => _a('Publish time DESC'),
                            'publishASC'  => _a('Publish time ASC'),
                            'updateDESC'  => _a('Update time DESC'),
                            'updateASC'   => _a('Update time ASC'),
                            'random'      => _a('Random'),
                        ],
                    ],
                ],
                'filter'      => 'text',
                'value'       => 'publishDESC',
            ],
            'type'          => [
                'title'       => _a('Item list type'),
                'description' => '',
                'edit'        => [
                    'type'    => 'select',
                    'options' => [
                        'options' => [
                            'vertical'           => _a('Vertical'),
                            'verticalwide'       => _a('Vertical wide'),
                            'verticalcompressed' => _a('Vertical compressed'),
                            'horizontal'         => _a('Horizontal'),
                            'magazine'           => _a('Magazine'),
                            'list'               => _a('List'),
                            'table'              => _a('Table'),
                            'slide'              => _a('Slide'),
                            'slidehover'         => _a('Slide by hover effect'),
                            'carousel'           => _a('Carousel'),
                            'image'              => _a('Image'),
                        ],
                    ],
                ],
                'filter'      => 'text',
                'value'       => 'vertical',
            ],
            'blockEffect'   => [
                'title'       => _a('Use block effects'),
                'description' => _a('Use block effects or set custom effect on theme'),
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
        ],
    ],

    // topic block
    'topic'     => [
        'title'       => _a('List of topic'),
        'description' => '',
        'render'      => ['block', 'topic'],
        'template'    => 'topic',
        'config'      => [
            'topicid'       => [
                'title'       => _a('Topic'),
                'description' => '',
                'edit'        => 'Module\News\Form\Element\Topic',
                'filter'      => 'string',
                'value'       => 0,
            ],
            'tree'          => [
                'title'       => _a('Tree view'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'showdesc'      => [
                'title'       => _a('Show text'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'showimage'     => [
                'title'       => _a('Show Images'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'showblockmore' => [
                'title'       => _a('Show More link to module page'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'linkblockmore' => [
                'title'       => _a('Set More link to module page'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'string',
                'value'       => '',
            ],
            'order'         => [
                'title'       => _a('Topic order'),
                'description' => _a('Topic list order options'),
                'edit'        => [
                    'type'    => 'select',
                    'options' => [
                        'options' => [
                            'createDESC' => _a('Create DESC'),
                            'createASC'  => _a('Create ASC'),
                            'titleDESC'  => _a('Title DESC'),
                            'titleASC'   => _a('Title ASC'),
                        ],
                    ],
                ],
                'filter'      => 'text',
                'value'       => 'createDESC',
            ],
            'type'          => [
                'title'       => _a('Topic list type'),
                'description' => '',
                'edit'        => [
                    'type'    => 'select',
                    'options' => [
                        'options' => [
                            'vertical'   => _a('Vertical'),
                            'horizontal' => _a('Horizontal'),
                            'list'       => _a('List'),
                            'listgroup'  => _a('List group'),
                            'slide'      => _a('Slide'),
                            'image'      => _a('Image'),
                        ],
                    ],
                ],
                'filter'      => 'text',
                'value'       => 'listgroup',
            ],
            'blockEffect'   => [
                'title'       => _a('Use block effects'),
                'description' => _a('Use block effects or set custom effect on theme'),
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
        ],
    ],

    // gallery block
    'gallery'   => [
        'title'       => _a('Gallery'),
        'description' => '',
        'render'      => ['block', 'gallery'],
        'template'    => 'gallery',
        'config'      => [
            'number'     => [
                'title'       => _a('Number'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => 12,
            ],
            'showeffect' => [
                'title'       => _a('Show gallery effect'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'widthclass' => [
                'title'       => _a('Image width class'),
                'description' => _a('Bootstrap 3 grid options like : col-6 col-sm-6 col-md-3'),
                'edit'        => 'text',
                'filter'      => 'string',
                'value'       => 'col-6 col-sm-6 col-md-3',
            ],
            'type'       => [
                'title'       => _a('Image list type'),
                'description' => '',
                'edit'        => [
                    'type'    => 'select',
                    'options' => [
                        'options' => [
                            'simple' => _a('Simple'),
                            'slide'  => _a('Slide'),
                        ],
                    ],
                ],
                'filter'      => 'text',
                'value'       => 'simple',
            ],
        ],
    ],

    // microblog block
    'microblog' => [
        'title'       => _a('Microblog'),
        'description' => '',
        'render'      => ['block', 'microblog'],
        'template'    => 'microblog',
        'config'      => [
            'topicid' => [
                'title'       => _a('Topic'),
                'description' => '',
                'edit'        => 'Module\News\Form\Element\Topic',
                'filter'      => 'string',
                'value'       => 0,
            ],
            'number'  => [
                'title'       => _a('Number'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => 12,
            ],
            'uid'     => [
                'title'       => _a('User ID'),
                'description' => _a('Set user ID, if you want show special user posts'),
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => '',
            ],
        ],
    ],

    // media block
    'media'     => [
        'title'       => _a('Media'),
        'description' => '',
        'render'      => ['block', 'media'],
        'template'    => 'media',
        'config'      => [
            'topicid'   => [
                'title'       => _a('Topic'),
                'description' => '',
                'edit'        => 'Module\News\Form\Element\Topic',
                'filter'      => 'string',
                'value'       => 0,
            ],
            'type'      => [
                'title'       => _a('Media type'),
                'description' => '',
                'edit'        => [
                    'type'    => 'select',
                    'options' => [
                        'options' => [
                            'video' => _a('Video'),
                            'audio' => _a('Audio'),
                        ],
                    ],
                ],
                'filter'      => 'text',
                'value'       => 'video',
            ],
            'showdesc'  => [
                'title'       => _a('Show text'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'textlimit' => [
                'title'       => _a('Text width limit'),
                'description' => _a('Set 0 for no limit'),
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
        ],
    ],

    // List block
    'list'      => [
        'title'       => _a('list'),
        'description' => '',
        'render'      => ['block', 'item'],
        'template'    => 'list',
        'config'      => [
            'topicid'       => [
                'title'       => _a('Topic'),
                'description' => '',
                'edit'        => 'Module\News\Form\Element\Topic',
                'filter'      => 'string',
                'value'       => 0,
            ],
            'number'        => [
                'title'       => _a('Number'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => 5,
            ],
            'showdate'      => [
                'title'       => _a('Show date'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'showhits'      => [
                'title'       => _a('Show hits'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'showblockmore' => [
                'title'       => _a('Show More link to module page'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'linkblockmore' => [
                'title'       => _a('Set More link to module page'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'string',
                'value'       => '',
            ],
            'order'         => [
                'title'       => _a('Story order'),
                'description' => _a('Story list order options'),
                'edit'        => [
                    'type'    => 'select',
                    'options' => [
                        'options' => [
                            'publishDESC' => _a('Publish time DESC'),
                            'publishASC'  => _a('Publish time ASC'),
                            'random'      => _a('Random'),
                        ],
                    ],
                ],
                'filter'      => 'text',
                'value'       => 'publishDESC',
            ],
        ],
    ],

    // Table block
    'table'     => [
        'title'       => _a('Table'),
        'description' => '',
        'render'      => ['block', 'item'],
        'template'    => 'table',
        'config'      => [
            'topicid'       => [
                'title'       => _a('Topic'),
                'description' => '',
                'edit'        => 'Module\News\Form\Element\Topic',
                'filter'      => 'string',
                'value'       => 0,
            ],
            'number'        => [
                'title'       => _a('Number'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => 5,
            ],
            'showdesc'      => [
                'title'       => _a('Show text'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'showdate'      => [
                'title'       => _a('Show date'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'showhits'      => [
                'title'       => _a('Show hits'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'showblockmore' => [
                'title'       => _a('Show More link to module page'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'linkblockmore' => [
                'title'       => _a('Set More link to module page'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'string',
                'value'       => '',
            ],
            'order'         => [
                'title'       => _a('Story order'),
                'description' => _a('Story list order options'),
                'edit'        => [
                    'type'    => 'select',
                    'options' => [
                        'options' => [
                            'publishDESC' => _a('Publish time DESC'),
                            'publishASC'  => _a('Publish time ASC'),
                            'random'      => _a('Random'),
                        ],
                    ],
                ],
                'filter'      => 'text',
                'value'       => 'publishDESC',
            ],
        ],
    ],
];
