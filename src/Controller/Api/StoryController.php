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

namespace Module\News\Controller\Api;

use Pi;
use Pi\Mvc\Controller\ApiController;

class StoryController extends ApiController
{
    public function listAction()
    {
        // Set default result
        $result = [
            'result' => false,
            'data'   => [],
            'error'  => [
                'code'    => 1,
                'message' => __('Nothing selected'),
            ],
        ];

        // Get info from url
        $token = $this->params('token');

        // Check token
        $check = Pi::api('token', 'tools')->check($token);
        if ($check['status'] == 1) {

            // Save statistics
            if (Pi::service('module')->isActive('statistics')) {
                Pi::api('log', 'statistics')->save(
                    'news',
                    'storyList',
                    0,
                    [
                        'source'  => $this->params('platform'),
                        'section' => 'api',
                    ]
                );
            }

            // Set options
            $options            = [];
            $options['page']    = $this->params('page', 1);
            $options['title']   = $this->params('title');
            $options['topic']   = $this->params('topic');
            $options['tag']     = $this->params('tag');
            $options['limit']   = $this->params('limit');
            $options['type']    = $this->params('type');
            $options['fields']  = $this->params('fields');
            $options['getUser'] = true;

            // Check fields
            $options['fields'] = empty($options['fields']) ? [] : explode(',', $options['fields']);

            // Get data
            $result['data'] = Pi::api('api', 'news')->jsonList($options);

            // Check data
            if (!empty($result['data'])) {
                $result['result'] = true;
            } else {
                // Set error
                $result['error'] = [
                    'code'    => 4,
                    'message' => __('Data is empty'),
                ];
            }
        } else {
            // Set error
            $result['error'] = [
                'code'    => 2,
                'message' => $check['message'],
            ];
        }

        // Check final result
        if ($result['result']) {
            $result['error'] = [];
        }

        // Return result
        return $result;
    }

    public function singleAction()
    {
        // Set default result
        $result = [
            'result' => false,
            'data'   => [],
            'error'  => [
                'code'    => 1,
                'message' => __('Nothing selected'),
            ],
        ];

        // Get info from url
        $module  = $this->params('module');
        $token   = $this->params('token');
        $storyId = $this->params('id');

        // Check token
        $check = Pi::api('token', 'tools')->check($token);
        if ($check['status'] == 1) {

            // Check id
            if (intval($storyId) > 0) {
                $story = Pi::api('story', 'news')->getStory(intval($storyId));

                // Check status
                if (!$story || $story['status'] != 1 || !in_array($story['type'], ['text', 'article', 'magazine', 'image', 'gallery', 'media', 'download'])) {
                    // Set error
                    $result['error'] = [
                        'code'    => 4,
                        'message' => __('The story not found.'),
                    ];
                    return $result;
                }

                // Check time_publish
                if ($story['time_publish'] > time()) {
                    // Set error
                    $result['error'] = [
                        'code'    => 5,
                        'message' => __('The Story not publish.'),
                    ];
                    return $result;
                }

                // Get Module Config
                $config = Pi::service('registry')->config->read($module);

                // Save statistics
                if (Pi::service('module')->isActive('statistics')) {
                    Pi::api('log', 'statistics')->save(
                        'news',
                        'storySingle',
                        $this->params('id'),
                        [
                            'source'  => $this->params('platform'),
                            'section' => 'api',
                        ]
                    );
                }

                // Update hits
                if ($config['story_all_hits']) {
                    $this->getModel('link')->increment('hits', ['story' => $story['id']]);
                    $this->getModel('story')->increment('hits', ['id' => $story['id']]);
                } else {
                    if (!isset($_SESSION['hits_news'][$story['id']])) {
                        if (!isset($_SESSION['hits_news'])) {
                            $_SESSION['hits_news'] = [];
                        }

                        $_SESSION['hits_news'][$story['id']] = false;
                    }

                    if (!$_SESSION['hits_news'][$story['id']]) {
                        $this->getModel('story')->increment('hits', ['id' => $story['id']]);
                        $this->getModel('link')->increment('hits', ['story' => $story['id']]);
                        $_SESSION['hits_news'][$story['id']] = true;
                    }
                }

                // Set Additional images
                $story['additional_images_url'] = [];
                if (!empty($story['additional_images'])) {
                    $additionalImages = Pi::api('doc', 'media')->getGalleryLinkData(
                        $story['additional_images'],
                        'large',
                        null,
                        null,
                        false,
                        [],
                        'news'
                    );
                    foreach ($additionalImages as $additionalImage) {
                        $story['additional_images_url'][] = $additionalImage['resized_url'];
                    }
                }

                // Attribute
                $story['attributeList'] = [];
                if ($config['show_attribute'] && $story['attribute']) {
                    $attributeList          = Pi::api('attribute', 'news')->Story($story['id'], $story['topic_main']);
                    $story['attributeList'] = [];
                    foreach ($attributeList as $attributeKey => $attributeCategory) {
                        switch ($attributeKey) {
                            case 'video':
                                foreach ($attributeCategory as $attributeSingle) {
                                    $story['attributeList'][$attributeSingle['name']] = $attributeSingle['data'];
                                }
                                break;

                            default:
                                foreach ($attributeCategory as $attributePosition) {
                                    foreach ($attributePosition['info'] as $attributeSingle) {
                                        $story['attributeList'][$attributeSingle['name']] = $attributeSingle['data'];
                                    }
                                }
                                break;
                        }
                    }
                }

                // Tag
                //$story['tagList'] = [];
                //if ($config['show_tag'] && Pi::service('module')->isActive('tag')) {
                //    $story['tagList'] = Pi::service('tag')->get($module, $story['id'], '');
                //}

                // Set default result
                $result = [
                    'result' => true,
                    'data'   => [
                        $story,
                    ],
                    'error'  => [
                        'code'    => 0,
                        'message' => '',
                    ],
                ];
            } else {
                // Set error
                $result['error'] = [
                    'code'    => 3,
                    'message' => __('Id not selected'),
                ];
            }
        } else {
            // Set error
            $result['error'] = [
                'code'    => 2,
                'message' => $check['message'],
            ];
        }

        // Check final result
        if ($result['result']) {
            $result['error'] = [];
        }

        // Return result
        return $result;
    }
}
