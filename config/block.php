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
    // Item block
    'item' => array(
        'title' => _a('Item'),
        'description' => '',
        'render' => array('block', 'item'),
        'template' => 'item',
        'config' => array(
            'topicid' => array(
                'title' => _a('Topic'),
                'description' => '',
                'edit' => 'Module\News\Form\Element\Topic',
                'filter' => 'string',
                'value' => 0,
            ),
            'number' => array(
                'title' => _a('Number'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 5,
            ),
            'showdesc' => array(
                'title' => _a('Show text'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'textlimit' => array(
                'title' => _a('Text width limit'),
                'description' => _a('Set 0 for no limit'),
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'showdate' => array(
                'title' => _a('Show date'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showhits' => array(
                'title' => _a('Show hits'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showimage' => array(
                'title' => _a('Show Images'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showmore' => array(
                'title' => _a('Show More link for each story'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showauthor' => array(
                'title' => _a('Show authors'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'showblockmore' => array(
                'title' => _a('Show More link to module page'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'linkblockmore' => array(
                'title' => _a('Set More link to module page'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'string',
                'value' => '',
            ),
            'order' => array(
                'title' => _a('Story order'),
                'description' => _a('Story list order options'),
                'edit' => array(
                    'type' => 'select',
                    'options' => array(
                        'options' => array(
                            'publishDESC' => _a('Publish time DESC'),
                            'publishASC' => _a('Publish time ASC'),
                            'updateDESC' => _a('Update time DESC'),
                            'updateASC' => _a('Update time ASC'),
                            'random' => _a('Random'),
                        ),
                    ),
                ),
                'filter' => 'text',
                'value' => 'publishDESC',
            ),
            'type' => array(
                'title' => _a('Item list type'),
                'description' => '',
                'edit' => array(
                    'type' => 'select',
                    'options' => array(
                        'options' => array(
                            'vertical' => _a('Vertical'),
                            'verticalwide' => _a('Vertical wide'),
                            'verticalcompressed' => _a('Vertical compressed'),
                            'horizontal' => _a('Horizontal'),
                            'list' => _a('List'),
                            'table' => _a('Table'),
                            'slide' => _a('Slide'),
                            'slidehover' => _a('Slide by hover effect'),
                            'carousel' => _a('Carousel'),
                            'image' => _a('Image'),
                        ),
                    ),
                ),
                'filter' => 'text',
                'value' => 'vertical',
            ),
            'blockEffect' => array(
                'title' => _a('Use block effects'),
                'description' => _a('Use block effects or set custom effect on theme'),
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'notShowSpotlight' => array(
                'title' => _a('Not show top stories'),
                'description' => _a('By active this option, you skip show spotlight stories on this block and can show them on other blocks, it can help you to manage homepage to show all spotlight story on one block and other stories on other blocks'),
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
        ),
    ),

    // Slotlight block
    'spotlight' => array(
        'title' => _a('Spotlight'),
        'description' => '',
        'render' => array('block', 'spotlight'),
        'template' => 'spotlight',
        'config' => array(
            'number' => array(
                'title' => _a('Number'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 5,
            ),
            'showdesc' => array(
                'title' => _a('Show text'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'textlimit' => array(
                'title' => _a('Text width limit'),
                'description' => _a('Set 0 for no limit'),
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'showdate' => array(
                'title' => _a('Show date'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showhits' => array(
                'title' => _a('Show hits'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showimage' => array(
                'title' => _a('Show Images'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showmore' => array(
                'title' => _a('Show More link for each story'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showauthor' => array(
                'title' => _a('Show authors'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'showblockmore' => array(
                'title' => _a('Show More link to module page'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'linkblockmore' => array(
                'title' => _a('Set More link to module page'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'string',
                'value' => '',
            ),
            'order' => array(
                'title' => _a('Story order'),
                'description' => _a('Story list order options'),
                'edit' => array(
                    'type' => 'select',
                    'options' => array(
                        'options' => array(
                            'publishDESC' => _a('Publish time DESC'),
                            'publishASC' => _a('Publish time ASC'),
                            'updateDESC' => _a('Update time DESC'),
                            'updateASC' => _a('Update time ASC'),
                            'random' => _a('Random'),
                        ),
                    ),
                ),
                'filter' => 'text',
                'value' => 'publishDESC',
            ),
            'type' => array(
                'title' => _a('Item list type'),
                'description' => '',
                'edit' => array(
                    'type' => 'select',
                    'options' => array(
                        'options' => array(
                            'vertical' => _a('Vertical'),
                            'verticalwide' => _a('Vertical wide'),
                            'verticalcompressed' => _a('Vertical compressed'),
                            'horizontal' => _a('Horizontal'),
                            'list' => _a('List'),
                            'table' => _a('Table'),
                            'slide' => _a('Slide'),
                            'slidehover' => _a('Slide by hover effect'),
                            'carousel' => _a('Carousel'),
                            'image' => _a('Image'),
                        ),
                    ),
                ),
                'filter' => 'text',
                'value' => 'vertical',
            ),
            'blockEffect' => array(
                'title' => _a('Use block effects'),
                'description' => _a('Use block effects or set custom effect on theme'),
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
        ),
    ),

    // topic block
    'topic' => array(
        'title' => _a('List of topic'),
        'description' => '',
        'render' => array('block', 'topic'),
        'template' => 'topic',
        'config' => array(
            'topicid' => array(
                'title' => _a('Topic'),
                'description' => '',
                'edit' => 'Module\News\Form\Element\Topic',
                'filter' => 'string',
                'value' => 0,
            ),
            'showdesc' => array(
                'title' => _a('Show text'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showimage' => array(
                'title' => _a('Show Images'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showblockmore' => array(
                'title' => _a('Show More link to module page'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'linkblockmore' => array(
                'title' => _a('Set More link to module page'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'string',
                'value' => '',
            ),
            'order' => array(
                'title' => _a('Topic order'),
                'description' => _a('Topic list order options'),
                'edit' => array(
                    'type' => 'select',
                    'options' => array(
                        'options' => array(
                            'createDESC' => _a('Create DESC'),
                            'createASC' => _a('Create ASC'),
                            'titleDESC' => _a('Title DESC'),
                            'titleASC' => _a('Title ASC'),
                        ),
                    ),
                ),
                'filter' => 'text',
                'value' => 'createDESC',
            ),
            'type' => array(
                'title' => _a('Topic list type'),
                'description' => '',
                'edit' => array(
                    'type' => 'select',
                    'options' => array(
                        'options' => array(
                            'vertical' => _a('Vertical'),
                            'horizontal' => _a('Horizontal'),
                            'list' => _a('List'),
                            'listgroup' => _a('List group'),
                            'slide' => _a('Slide'),
                            'image' => _a('Image'),
                        ),
                    ),
                ),
                'filter' => 'text',
                'value' => 'listgroup',
            ),
            'blockEffect' => array(
                'title' => _a('Use block effects'),
                'description' => _a('Use block effects or set custom effect on theme'),
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
        ),
    ),

    // gallery block
    'gallery' => array(
        'title' => _a('Gallery'),
        'description' => '',
        'render' => array('block', 'gallery'),
        'template' => 'gallery',
        'config' => array(
            'number' => array(
                'title' => _a('Number'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 12,
            ),
            'showeffect' => array(
                'title' => _a('Show gallery effect'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'widthclass' => array(
                'title' => _a('Image width class'),
                'description' => _a('Bootstrap 3 grid options like : col-xs-6 col-sm-6 col-md-3'),
                'edit' => 'text',
                'filter' => 'string',
                'value' => 'col-xs-6 col-sm-6 col-md-3',
            ),
            'type' => array(
                'title' => _a('Image list type'),
                'description' => '',
                'edit' => array(
                    'type' => 'select',
                    'options' => array(
                        'options' => array(
                            'simple' => _a('Simple'),
                            'slide' => _a('Slide'),
                        ),
                    ),
                ),
                'filter' => 'text',
                'value' => 'simple',
            ),
        ),
    ),

    // microblog block
    'microblog' => array(
        'title' => _a('Microblog'),
        'description' => '',
        'render' => array('block', 'microblog'),
        'template' => 'microblog',
        'config' => array(
            'topicid' => array(
                'title' => _a('Topic'),
                'description' => '',
                'edit' => 'Module\News\Form\Element\Topic',
                'filter' => 'string',
                'value' => 0,
            ),
            'number' => array(
                'title' => _a('Number'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 12,
            ),
            'uid' => array(
                'title' => _a('User ID'),
                'description' => _a('Set user ID, if you want show special user posts'),
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => '',
            ),
        ),
    ),

    // media block
    'media' => array(
        'title' => _a('Media'),
        'description' => '',
        'render' => array('block', 'media'),
        'template' => 'media',
        'config' => array(
            'topicid' => array(
                'title' => _a('Topic'),
                'description' => '',
                'edit' => 'Module\News\Form\Element\Topic',
                'filter' => 'string',
                'value' => 0,
            ),
            'type' => array(
                'title' => _a('Media type'),
                'description' => '',
                'edit' => array(
                    'type' => 'select',
                    'options' => array(
                        'options' => array(
                            'video' => _a('Video'),
                            'audio' => _a('Audio'),
                        ),
                    ),
                ),
                'filter' => 'text',
                'value' => 'video',
            ),
            'width' => array(
                'title' => _a('Width'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 640,
            ),
            'height' => array(
                'title' => _a('Height'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 480,
            ),
            'showdesc' => array(
                'title' => _a('Show text'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'textlimit' => array(
                'title' => _a('Text width limit'),
                'description' => _a('Set 0 for no limit'),
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 0,
            ),
        ),
    ),

    // List block
    'list' => array(
        'title' => _a('list'),
        'description' => '',
        'render' => array('block', 'item'),
        'template' => 'list',
        'config' => array(
            'topicid' => array(
                'title' => _a('Topic'),
                'description' => '',
                'edit' => 'Module\News\Form\Element\Topic',
                'filter' => 'string',
                'value' => 0,
            ),
            'number' => array(
                'title' => _a('Number'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 5,
            ),
            'showdate' => array(
                'title' => _a('Show date'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'showhits' => array(
                'title' => _a('Show hits'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'showblockmore' => array(
                'title' => _a('Show More link to module page'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'linkblockmore' => array(
                'title' => _a('Set More link to module page'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'string',
                'value' => '',
            ),
            'order' => array(
                'title' => _a('Story order'),
                'description' => _a('Story list order options'),
                'edit' => array(
                    'type' => 'select',
                    'options' => array(
                        'options' => array(
                            'publishDESC' => _a('Publish time DESC'),
                            'publishASC' => _a('Publish time ASC'),
                            'random' => _a('Random'),
                        ),
                    ),
                ),
                'filter' => 'text',
                'value' => 'publishDESC',
            ),
        ),
    ),

    // Table block
    'table' => array(
        'title' => _a('Table'),
        'description' => '',
        'render' => array('block', 'item'),
        'template' => 'table',
        'config' => array(
            'topicid' => array(
                'title' => _a('Topic'),
                'description' => '',
                'edit' => 'Module\News\Form\Element\Topic',
                'filter' => 'string',
                'value' => 0,
            ),
            'number' => array(
                'title' => _a('Number'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 5,
            ),
            'showdesc' => array(
                'title' => _a('Show text'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'showdate' => array(
                'title' => _a('Show date'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showhits' => array(
                'title' => _a('Show hits'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showblockmore' => array(
                'title' => _a('Show More link to module page'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'linkblockmore' => array(
                'title' => _a('Set More link to module page'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'string',
                'value' => '',
            ),
            'order' => array(
                'title' => _a('Story order'),
                'description' => _a('Story list order options'),
                'edit' => array(
                    'type' => 'select',
                    'options' => array(
                        'options' => array(
                            'publishDESC' => _a('Publish time DESC'),
                            'publishASC' => _a('Publish time ASC'),
                            'random' => _a('Random'),
                        ),
                    ),
                ),
                'filter' => 'text',
                'value' => 'publishDESC',
            ),
        ),
    ),
);