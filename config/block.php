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
    'item'        => array(
        'title'         => _a('Item'),
        'description'   => '',
        'render'        => 'block::item',
        'template'      => 'item',
        'config'        => array(
            'topicid'        => array(
                'title'        => _a('Topic'),
                'description'  => '',
                'edit'         => 'Module\News\Form\Element\Topic',
                'filter'       => 'string',
                'value'        => 0,
            ),
            'number'         => array(
                'title'        => _a('Number'),
                'description'  => '',
                'edit'         => 'text',
                'filter'       => 'number_int',
                'value'        => 5,
            ),
            'showdesc'       => array(
                'title'        => _a('Shwo text'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 1,
            ),
            'showdate'       => array(
                'title'        => _a('Show date'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 1,
            ),
            'showhits'       => array(
                'title'        => _a('Show hits'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 1,
            ),
            'showimage'      => array(
                'title'        => _a('Show Images'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 1,
            ),
            'showmore'       => array(
                'title'        => _a('Show More link for each story'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 1,
            ),
            'showauthor'     => array(
                'title'        => _a('Show authors'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 0,
            ),
            'showblockmore'  => array(
                'title'        => _a('Show More link to module page'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 0,
            ),
            'linkblockmore'  => array(
                'title'        => _a('Set More link to module page'),
                'description'  => '',
                'edit'         => 'text',
                'filter'       => 'string',
                'value'        => '',
            ),
            'order'          => array(
                'title'        => _a('List order'),
                'description'  => '',
                'edit'         => array(
                    'type'            => 'select',
                    'options'         => array(
                        'options'     => array(
                            'last'    => _a('Latest'),
                            'random'  => _a('Random'),
                        ),
                    ),
                ),
                'filter'      => 'text',
                'value'       => 'last',
            ),
        ),
    ),

    // List block
    'list'        => array(
        'title'         => _a('list'),
        'description'   => '',
        'render'        => 'block::item',
        'template'      => 'list',
        'config'        => array(
            'topicid'        => array(
                'title'        => _a('Topic'),
                'description'  => '',
                'edit'         => 'Module\News\Form\Element\Topic',
                'filter'       => 'string',
                'value'        => 0,
            ),
            'number'         => array(
                'title'        => _a('Number'),
                'description'  => '',
                'edit'         => 'text',
                'filter'       => 'number_int',
                'value'        => 5,
            ),
            'showdate'       => array(
                'title'        => _a('Show date'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 0,
            ),
            'showhits'       => array(
                'title'        => _a('Show hits'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 0,
            ),
            'showblockmore'  => array(
                'title'        => _a('Show More link to module page'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 0,
            ),
            'linkblockmore'  => array(
                'title'        => _a('Set More link to module page'),
                'description'  => '',
                'edit'         => 'text',
                'filter'       => 'string',
                'value'        => '',
            ),
            'order'          => array(
                'title'        => _a('List order'),
                'description'  => '',
                'edit'         => array(
                    'type'            => 'select',
                    'options'         => array(
                        'options'     => array(
                            'last'    => _a('Latest'),
                            'random'  => _a('Random'),
                        ),
                    ),
                ),
                'filter'       => 'text',
                'value'        => 'last',
            ),
        ),
    ),

    // Table block
    'table'       => array(
        'title'         => _a('Table'),
        'description'   => '',
        'render'        => 'block::item',
        'template'      => 'table',
        'config'        => array(
            'topicid'        => array(
                'title'        => _a('Topic'),
                'description'  => '',
                'edit'         => 'Module\News\Form\Element\Topic',
                'filter'       => 'string',
                'value'        => 0,
            ),
            'number'         => array(
                'title'        => _a('Number'),
                'description'  => '',
                'edit'         => 'text',
                'filter'       => 'number_int',
                'value'        => 5,
            ),
            'showdesc'       => array(
                'title'        => _a('Shwo text'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 0,
            ),
            'showdate'       => array(
                'title'        => _a('Show date'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 1,
            ),
            'showhits'       => array(
                'title'        => _a('Show hits'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 1,
            ),
            'showblockmore'  => array(
                'title'        => _a('Show More link to module page'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 0,
            ),
            'linkblockmore'  => array(
                'title'        => _a('Set More link to module page'),
                'description'  => '',
                'edit'         => 'text',
                'filter'       => 'string',
                'value'        => '',
            ),
            'order'          => array(
                'title'        => _a('List order'),
                'description'  => '',
                'edit'         => array(
                    'type'            => 'select',
                    'options'         => array(
                        'options'     => array(
                            'last'    => _a('Latest'),
                            'random'  => _a('Random'),
                        ),
                    ),
                ),
                'filter'       => 'text',
                'value'        => 'last',
            ),
        ),
    ),


    // Slotlight block
    'spotlight'   => array(
        'title'         => _a('Spotlight'),
        'description'   => '',
        'render'        => 'block::spotlight',
        'template'      => 'spotlight',
        'config'        => array(
            'topicid'        => array(
                'title'        => _a('Topic'),
                'description'  => '',
                'edit'         => 'Module\News\Form\Element\Topic',
                'filter'       => 'string',
                'value'        => 0,
            ),
            'number'         => array(
                'title'        => _a('Number of stroies for each topic'),
                'description'  => '',
                'edit'         => 'text',
                'filter'       => 'number_int',
                'value'        => 5,
            ),
            'showtopicdesc'       => array(
                'title'        => _a('Shwo text'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 1,
            ),
            'showtopicimage'      => array(
                'title'        => _a('Show Images'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 1,
            ),
            'order'          => array(
                'title'        => _a('List order'),
                'description'  => '',
                'edit'         => array(
                    'type'            => 'select',
                    'options'         => array(
                        'options'     => array(
                            'last'    => _a('Latest'),
                            'random'  => _a('Random'),
                        ),
                    ),
                ),
                'filter'      => 'text',
                'value'       => 'last',
            ),
        ),
    ),

    // topic block
    'topic'       => array(
        'title'         => _a('List of topic'),
        'description'   => '',
        'render'        => 'block::topic',
        'template'      => 'topic',
        'config'        => array(
            'topicid'        => array(
                'title'        => _a('Topic'),
                'description'  => '',
                'edit'         => 'Module\News\Form\Element\Topic',
                'filter'       => 'string',
                'value'        => 0,
            ),
            'showdesc'       => array(
                'title'        => _a('Shwo text'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 1,
            ),
            'showimage'      => array(
                'title'        => _a('Show Images'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 1,
            ),
            'showblockmore'  => array(
                'title'        => _a('Show More link to module page'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 0,
            ),
            'linkblockmore'  => array(
                'title'        => _a('Set More link to module page'),
                'description'  => '',
                'edit'         => 'text',
                'filter'       => 'string',
                'value'        => '',
            ),
        ),
    ),

    // writer block
    'writer'      => array(
        'title'         => _a('Top writers'),
        'description'   => '',
        'render'        => 'block::writer',
        'template'      => 'writer',
        'config'        => array(
            'number'         => array(
                'title'        => _a('Number'),
                'description'  => '',
                'edit'         => 'text',
                'filter'       => 'number_int',
                'value'        => 10,
            ),
            'showblockmore'  => array(
                'title'        => _a('Show More link to module page'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 0,
            ),
        ),
    ),

    // gallery block
    'gallery'     => array(
        'title'         => _a('Gallery'),
        'description'   => '',
        'render'        => 'block::gallery',
        'template'      => 'gallery',
        'config'        => array(
            'number'         => array(
                'title'        => _a('Number'),
                'description'  => '',
                'edit'         => 'text',
                'filter'       => 'number_int',
                'value'        => 12,
            ),
            'showeffect'      => array(
                'title'        => _a('Show gallery effect'),
                'description'  => '',
                'edit'         => 'checkbox',
                'filter'       => 'number_int',
                'value'        => 1,
            ),
            'widthclass'  => array(
                'title'        => _a('Image width class'),
                'description'  => _a('Bootstrap 3 grid options like : col-xs-6 col-sm-6 col-md-3'),
                'edit'         => 'text',
                'filter'       => 'string',
                'value'        => 'col-xs-6 col-sm-6 col-md-3',
            ),
        ),
    ),

);