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
        'title' => __('Item'),
        'description' => '',
        'render' => 'block::item',
        'template' => 'item',
        'config' => array(
            'topicid' => array(
                'title' => __('Topic'),
                'description' => '',
                'edit' => 'Module\News\Form\Element\Topic',
                'filter' => 'string',
                'value' => 0,
            ),
            'number' => array(
                'title' => __('Number'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showdesc' => array(
                'title' => __('Shwo text'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showdate' => array(
                'title' => __('Show date'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showhits' => array(
                'title' => __('Show hits'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showimage' => array(
                'title' => __('Show Images'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showmore' => array(
                'title' => __('Show More link for each story'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showblockmore' => array(
                'title' => __('Show More link to module page'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'linkblockmore' => array(
                'title' => __('Set More link to module page'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'string',
                'value' => '',
            ),
        ),
    ),

    // List block
    'list' => array(
        'title' => __('list'),
        'description' => '',
        'render' => 'block::item',
        'template' => 'list',
        'config' => array(
            'topicid' => array(
                'title' => __('topic'),
                'description' => '',
                'edit' => 'Module\News\Form\Element\Topic',
                'filter' => 'string',
                'value' => 0,
            ),
            'number' => array(
                'title' => __('Number'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 10,
            ),
            'showdate' => array(
                'title' => __('Show date'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'showhits' => array(
                'title' => __('Show hits'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'showblockmore' => array(
                'title' => __('Show More link to module page'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'linkblockmore' => array(
                'title' => __('Set More link to module page'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'string',
                'value' => '',
            ),
        ),
    ),

    // Table block
    'table' => array(
        'title' => __('Table'),
        'description' => '',
        'render' => 'block::item',
        'template' => 'table',
        'config' => array(
            'topicid' => array(
                'title' => __('topic'),
                'description' => '',
                'edit' => 'Module\News\Form\Element\Topic',
                'filter' => 'string',
                'value' => 0,
            ),
            'number' => array(
                'title' => __('Number'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showdesc' => array(
                'title' => __('Shwo text'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'showdate' => array(
                'title' => __('Show date'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showhits' => array(
                'title' => __('Show hits'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showblockmore' => array(
                'title' => __('Show More link to module page'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'linkblockmore' => array(
                'title' => __('Set More link to module page'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'string',
                'value' => '',
            ),
        ),
    ),


    // Slotlight block
    'spotlight' => array(
        'title' => __('Spotlight'),
        'description' => '',
        'render' => 'block::spotlight',
        'template' => 'spotlight',
        'config' => array(
            'topicid' => array(
                'title' => __('topic'),
                'description' => '',
                'edit' => 'Module\News\Form\Element\Topic',
                'filter' => 'string',
                'value' => 0,
            ),
            'number' => array(
                'title' => __('Number'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 5,
            ),
            'subspotlight' => array(
                'title' => __('Number of sub spotlight'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 4,
            ),
            'showdesc' => array(
                'title' => __('Shwo text'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showdate' => array(
                'title' => __('Show date'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showhits' => array(
                'title' => __('Show hits'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showimage' => array(
                'title' => __('Show Images'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showblockmore' => array(
                'title' => __('Show More link to module page'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'linkblockmore' => array(
                'title' => __('Set More link to module page'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'string',
                'value' => '',
            ),
        ),
    ),

    // topic block
    'topic' => array(
        'title' => __('List of topic'),
        'description' => '',
        'render' => 'block::topic',
        'template' => 'topic',
        'config' => array(
            'topicid' => array(
                'title' => __('Topic'),
                'description' => '',
                'edit' => 'Module\News\Form\Element\Topic',
                'filter' => 'string',
                'value' => 0,
            ),
            'showdesc' => array(
                'title' => __('Shwo text'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showimage' => array(
                'title' => __('Show Images'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'showblockmore' => array(
                'title' => __('Show More link to module page'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'linkblockmore' => array(
                'title' => __('Set More link to module page'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'string',
                'value' => '',
            ),
        ),
    ),
);