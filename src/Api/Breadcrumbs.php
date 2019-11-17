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

namespace Module\News\Api;

use Pi;
use Pi\Application\Api\AbstractBreadcrumbs;

class Breadcrumbs extends AbstractBreadcrumbs
{
    /**
     * {@inheritDoc}
     */
    public function load()
    {
        // Get params
        $params = Pi::service('url')->getRouteMatch()->getParams();
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Check breadcrumbs
        if ($config['view_breadcrumbs']) {
            // Set module link
            $moduleData = Pi::registry('module')->read($this->getModule());
            // Make tree
            if (!empty($params['controller']) && $params['controller'] != 'index') {
                // Set index
                $result = [
                    [
                        'label' => $moduleData['title'],
                        'href'  => Pi::url(
                            Pi::service('url')->assemble(
                                'news', [
                                    'module' => $this->getModule(),
                                ]
                            )
                        ),
                    ],
                ];
                // Set
                switch ($params['controller']) {
                    case 'author':
                        if (!empty($params['slug'])) {
                            // Set link
                            $result[] = [
                                'label' => __('Author list'),
                                'href'  => Pi::url(
                                    Pi::service('url')->assemble(
                                        'news', [
                                            'controller' => 'author',
                                            //'action' => 'list',
                                        ]
                                    )
                                ),
                            ];
                            // Set link
                            $author   = Pi::api('author', 'news')->getAuthor($params['slug'], 'slug');
                            $result[] = [
                                'label' => $author['title'],
                            ];
                        } else {
                            $result[] = [
                                'label' => __('Author list'),
                            ];
                        }
                        break;

                    case 'favourite':
                        $result[] = [
                            'label' => __('Favourite list'),
                        ];
                        break;

                    case 'story':
                        $story = Pi::api('story', 'news')->getStory($params['slug'], 'slug');
                        // Check topic_mai
                        if ($story['topic_main'] > 0 && $config['view_breadcrumbs_topic']) {
                            $topicTree = $this->getParentList($story['topic_main']);
                            foreach ($topicTree as $topic) {
                                $result[] = [
                                    'label' => $topic['title'],
                                    'href'  => Pi::url($topic['url']),
                                ];
                            }
                        }
                        $result[] = [
                            'label' => $story['title'],
                        ];
                        break;

                    case 'tag':
                        if (!empty($params['slug'])) {
                            // Set link
                            $result[] = [
                                'label' => __('Tag list'),
                                'href'  => Pi::url(
                                    Pi::service('url')->assemble(
                                        'news', [
                                            'controller' => 'tag',
                                            'action'     => 'list',
                                        ]
                                    )
                                ),
                            ];
                            // Set link
                            $result[] = [
                                'label' => $params['slug'],
                            ];
                        } else {
                            $result[] = [
                                'label' => __('Tag list'),
                            ];
                        }
                        break;

                    case 'topic':
                        if (!empty($params['slug'])) {
                            // Set link
                            /* $result[] = array(
                                'label' => __('Topic list'),
                                'href' => Pi::url(Pi::service('url')->assemble('news', array(
                                    'controller' => 'topic',
                                    //'action' => 'list',
                                ))),
                            ); */
                            // Get topic
                            $topic = Pi::api('topic', 'news')->getTopic($params['slug'], 'slug');
                            // Get topic list
                            if ($topic['pid'] > 0 && $config['view_breadcrumbs_topic']) {
                                $topicList = $this->getParentList($topic['pid']);
                                foreach ($topicList as $topicSingle) {
                                    $result[] = [
                                        'label' => $topicSingle['title'],
                                        'href'  => Pi::url($topicSingle['url']),
                                    ];
                                }
                            }
                            // Set link
                            $result[] = [
                                'label' => $topic['title'],
                            ];
                        } else {
                            $result[] = [
                                'label' => __('Topic list'),
                            ];
                        }
                        break;

                    case 'microblog':
                        if (!empty($params['id'])) {
                            // Set link
                            $result[] = [
                                'label' => __('Post list'),
                                'href'  => Pi::url(
                                    Pi::service('url')->assemble(
                                        'news', [
                                            'controller' => 'microblog',
                                        ]
                                    )
                                ),
                            ];
                            // Set link
                            $microblog = Pi::api('microblog', 'news')->getMicroblog($params['id']);
                            $result[]  = [
                                'label' => $microblog['seo_title'],
                            ];
                        } else {
                            $result[] = [
                                'label' => __('Post list'),
                            ];
                        }
                        break;
                }
            } else {
                $result = [
                    [
                        'label' => $moduleData['title'],
                    ],
                ];
            }
            return $result;
        } else {
            return '';
        }
    }

    public function getParentList($id)
    {
        $result    = [];
        $topicList = Pi::registry('topicList', 'news')->read();

        $result[] = $topicList[$id];
        if ($topicList[$id]['pid'] > 0) {
            $id       = $topicList[$id]['pid'];
            $result[] = $topicList[$id];
            if ($topicList[$id]['pid'] > 0) {
                $id       = $topicList[$id]['pid'];
                $result[] = $topicList[$id];
                if ($topicList[$id]['pid'] > 0) {
                    $id       = $topicList[$id]['pid'];
                    $result[] = $topicList[$id];
                    if ($topicList[$id]['pid'] > 0) {
                        $id       = $topicList[$id]['pid'];
                        $result[] = $topicList[$id];
                        if ($topicList[$id]['pid'] > 0) {
                            $id       = $topicList[$id]['pid'];
                            $result[] = $topicList[$id];
                            if ($topicList[$id]['pid'] > 0) {
                                $id       = $topicList[$id]['pid'];
                                $result[] = $topicList[$id];
                                if ($topicList[$id]['pid'] > 0) {
                                    $id       = $topicList[$id]['pid'];
                                    $result[] = $topicList[$id];
                                    if ($topicList[$id]['pid'] > 0) {
                                        $id       = $topicList[$id]['pid'];
                                        $result[] = $topicList[$id];
                                        if ($topicList[$id]['pid'] > 0) {
                                            $id       = $topicList[$id]['pid'];
                                            $result[] = $topicList[$id];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        sort($result);
        return $result;

    }
}