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
            'data'    => [],
            'error'   => [
                'code'    => 1,
                'message' => __('Nothing selected'),
            ]
        ];

        // Get info from url
        $module = $this->params('module');
        $token  = $this->params('token');

        // Check token
        $check = Pi::api('token', 'tools')->check($token, $module);
        if ($check['status'] == 1) {

            // Save statistics
            if (Pi::service('module')->isActive('statistics')) {
                Pi::api('log', 'statistics')->save(
                    'news', 'storyList', 0, [
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
            $options['getUser'] = true;

            // Get data
            $result['data'] = Pi::api('api', 'news')->jsonList($options);

            // Check data
            if (!empty($result['data'])) {
                $result['result'] =  true;
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
            'data'    => [],
            'error'   => [
                'code'    => 1,
                'message' => __('Nothing selected'),
            ]
        ];

        // Get info from url
        $module = $this->params('module');
        $token  = $this->params('token');
        $id     = $this->params('id');

        // Check token
        $check = Pi::api('token', 'tools')->check($token, $module);
        if ($check['status'] == 1) {

            // Save statistics
            if (Pi::service('module')->isActive('statistics')) {
                Pi::api('log', 'statistics')->save(
                    'news', 'storySingle', $this->params('id'), [
                        'source'  => $this->params('platform'),
                        'section' => 'api',
                    ]
                );
            }

            // Get Module Config
            $config = Pi::service('registry')->config->read($module);

            // Check id
            if (intval($id) > 0) {
                $result['data'] = Pi::api('story', 'news')->getStory(intval($id));

                // Update hits
                $this->getModel('story')->increment('hits', ['id' => $result['data']['id']]);

                // Set Additional images
                $result['data']['additional_images_url'] = [];
                if (!empty($result['data']['additional_images'])) {
                    $additionalImages = Pi::api('doc', 'media')->getGalleryLinkData($result['data']['additional_images'], 'large', null, null, false, [], 'news');
                    foreach ($additionalImages as $additionalImage) {
                        $result['data']['additional_images_url'][] = $additionalImage['resized_url'];
                    }
                }

                // Attribute
                $result['data']['attributeList'] = [];
                if ($config['show_attribute'] && $result['data']['attribute']) {
                    $attributeList = Pi::api('attribute', 'news')->Story($result['data']['id'], $result['data']['topic_main']);
                    $result['data']['attributeList'] = [];
                    foreach ($attributeList as $attributeKey => $attributeCategory) {
                        switch ($attributeKey) {
                            case 'video':
                                foreach ($attributeCategory as $attributeSingle) {
                                    $result['data']['attributeList'][$attributeSingle['name']] = $attributeSingle['data'];
                                }
                                break;

                            default:
                                foreach ($attributeCategory as $attributePosition) {
                                    foreach ($attributePosition['info'] as $attributeSingle) {
                                        $result['data']['attributeList'][$attributeSingle['name']] = $attributeSingle['data'];
                                    }
                                }
                                break;
                        }
                    }
                }

                // Tag
                //$result['data']['tagList'] = [];
                //if ($config['show_tag'] && Pi::service('module')->isActive('tag')) {
                //    $result['data']['tagList'] = Pi::service('tag')->get($module, $result['data']['id'], '');
                //}

                // Check data
                if (!empty($result['data'])) {
                    $result['result'] =  true;
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