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
use Pi\Application\Api\AbstractApi;
use Pi\File\Transfer\Upload;
use Pi\Filter;
use Pi\Paginator\Paginator;
use Zend\Db\Sql\Predicate\Expression;

/*
 * Pi::api('api', 'news')->addStory($values);
 * Pi::api('api', 'news')->editStory($values);
 * Pi::api('api', 'news')->setupLink($link);
 * Pi::api('api', 'news')->uploadImage($file, $prefix, $imagePath);
 * Pi::api('api', 'news')->removeImage($id);
 * Pi::api('api', 'news')->getStorySingle($parameter, $field, $type, $option);
 * Pi::api('api', 'news')->getStoryList($where, $order, $offset, $limit, $type, $table, $option);
 * Pi::api('api', 'news')->getStoryPaginator($template, $where, $page, $limit, $table);
 * Pi::api('api', 'news')->getStoryCount($where, $table);
 * Pi::api('api', 'news')->getStoryRelated($where, $order);
 * Pi::api('api', 'news')->jsonList($options);
 * Pi::api('api', 'news')->jsonSingle($id);
 * Pi::api('api', 'news')->jsonSubmit($data);
 */

/*
 * Sample link array
 * $link = array(
    'story' => 1,
    'time_publish' => time(),
    'time_update' => time(),
    'status' => 1,
    'uid' => 1,
    'type' => 'event',
    'module' => array(
        1 => array(
            'name' => 'event',
            'controller' => array(
                1 => array(
                    'name' => 'topic',
                    'topic' => array(
                        1, 2, 3, 4
                    ),
                ),
            ),
        ),
        2 => array(
            'name' => 'guide',
            'controller' => array(
                1 => array(
                    'name' => 'category',
                    'topic' => array(
                        1, 2, 3, 4
                    ),
                ),
                2 => array(
                    'name' => 'location',
                    'topic' => array(
                        1, 2, 3, 4
                    ),
                ),
                3 => array(
                    'name' => 'item',
                    'topic' => array(
                        1, 2, 3, 4
                    ),
                ),
                4 => array(
                    'name' => 'owner',
                    'topic' => array(
                        1
                    ),
                ),
            ),
        ),
    ),
);*/

class Api extends AbstractApi
{
    public function addStory($values, $processEventImage = false)
    {
        // Check type
        if (!isset($values['type'])
            || !in_array(
                $values['type'], [
                    'text', 'post', 'article', 'magazine', 'event',
                    'image', 'gallery', 'media', 'download', 'feed',
                ]
            )
        ) {
            return false;
        }
        // Get config
        $config = Pi::service('registry')->config->read('news');
        // Set seo_title
        $title               = ($values['seo_title']) ? $values['seo_title'] : $values['title'];
        $filter              = new Filter\HeadTitle;
        $values['seo_title'] = $filter($title);
        // Set seo_keywords
        $keywords = ($values['seo_keywords']) ? $values['seo_keywords'] : '';
        $filter   = new Filter\HeadKeywords;
        $filter->setOptions(
            [
                'force_replace_space' => (bool)$config['force_replace_space'],
            ]
        );
        $values['seo_keywords'] = $filter($keywords);
        // Set seo_description
        $description               = ($values['seo_description']) ? $values['seo_description'] : $values['title'];
        $filter                    = new Filter\HeadDescription;
        $values['seo_description'] = $filter($description);
        // Check time_create
        if (!isset($values['time_create']) || empty($values['time_create'])) {
            $values['time_create'] = time();
        }
        // Check time_update
        if (!isset($values['time_update']) || empty($values['time_update'])) {
            $values['time_update'] = time();
        }
        // Check time_publish
        if (!isset($values['time_publish']) || empty($values['time_publish'])) {
            $values['time_publish'] = time();
        }
        // Check uid
        if (!isset($values['uid']) || empty($values['uid'])) {
            $values['uid'] = Pi::user()->getId();
        }
        // Topics
        $values['topic'] = json_encode($values['topic']);
        // Save story
        $story = Pi::model('story', $this->getModule())->createRow();

        $imageToProcess = false;

        // check cropping udpate
        if ($processEventImage && isset($values['cropping']) && $values['cropping'] != $story['cropping']) {
            $imageToProcess = true;
        }

        $story->assign($values);
        $story->save();

        if ($processEventImage && $imageToProcess) {
            Pi::api('image', 'news')->process($story['image'], $story['path'], 'event/image', $story['cropping']);
        }

        $story = Pi::api('story', 'news')->canonizeStoryLight($story);
        // Return
        return $story;
    }

    public function editStory($values, $processEventImage = false, $updateSeo = true)
    {
        // Get config
        $config = Pi::service('registry')->config->read('news');
        if ($updateSeo) {
            // Set seo_title
            $title               = ($values['seo_title']) ? $values['seo_title'] : $values['title'];
            $filter              = new Filter\HeadTitle;
            $values['seo_title'] = $filter($title);
            // Set seo_keywords
            $keywords = ($values['seo_keywords']) ? $values['seo_keywords'] : '';
            $filter   = new Filter\HeadKeywords;
            $filter->setOptions(
                [
                    'force_replace_space' => (bool)$config['force_replace_space'],
                ]
            );
            $values['seo_keywords'] = $filter($keywords);
            // Set seo_description
            $description               = ($values['seo_description']) ? $values['seo_description'] : $values['title'];
            $filter                    = new Filter\HeadDescription;
            $values['seo_description'] = $filter($description);
        }
        // Check time_update
        if (!isset($values['time_update']) || empty($values['time_update'])) {
            $values['time_update'] = time();
        }
        // Topics
        $values['topic'] = json_encode($values['topic']);
        // Save story
        $story = Pi::model('story', $this->getModule())->find($values['id']);

        $imageToProcess = false;

        // check cropping udpate
        if ($processEventImage && isset($values['cropping']) && $values['cropping'] != $story['cropping']) {
            $imageToProcess = true;
        }

        $story->assign($values);
        $story->save();

        if ($processEventImage && $imageToProcess) {
            Pi::api('image', 'news')->process($story['image'], $story['path'], 'event/image', $story['cropping']);
        }

        $story = Pi::api('story', 'news')->canonizeStoryLight($story);
        // Return
        return $story;
    }

    public function uploadImage($file = [], $prefix = '', $imagePath = '', $cropping = null)
    {
        // Set result
        $result = [];
        // upload image
        if (!empty($file['image']['name'])) {
            $config = Pi::service('registry')->config->read('news');
            // Set image path
            $imagePath = empty($imagePath) ? $config['image_path'] : $imagePath;
            // Set upload path
            $result['path'] = sprintf('%s/%s', date('Y'), date('m'));
            $originalPath   = Pi::path(sprintf('upload/%s/original/%s', $imagePath, $result['path']));
            // Image name
            $imageName = Pi::api('image', 'news')->rename($file['image']['name'], $prefix, $result['path'], $imagePath);
            // Upload
            $uploader = new Upload;
            $uploader->setDestination($originalPath);
            $uploader->setRename($imageName);
            $uploader->setExtension($config['image_extension']);
            $uploader->setSize($config['image_size']);

            if (isset($config['image_crop']) && $config['image_crop'] && $config['image_largew'] && $config['image_largeh']) {
                $uploader->setImageSize(['minwidth' => $config['image_largew'], 'minheight' => $config['image_largeh']]);
            }

            if ($uploader->isValid()) {
                $uploader->receive();
                // Get image name
                $result['image'] = $uploader->getUploaded('image');
                // process image
                Pi::api('image', 'news')->process($result['image'], $result['path'], $imagePath, $cropping);
            } else {
                $uploaderMessages = $uploader->getMessages();

                $result = implode('; ', $uploaderMessages);
            }
        }
        return $result;
    }

    public function removeImage($id = 0)
    {
        // Set result
        $result = [
            'status'  => 0,
            'message' => '',
        ];
        // Check id
        if (isset($id) && intval($id) > 0) {
            // Get story
            $story = Pi::model('story', $this->getModule())->find($id);
            if ($story) {
                // clear DB
                $story->image = '';
                $story->path  = '';
                // Save
                $story->save();
                // Check
                if ($story->path == '' && $story->image == '') {
                    $result['message'] = sprintf(__('Image of %s removed'), $story->title);
                    $result['status']  = 1;
                } else {
                    $result['message'] = __('Image not remove');
                    $result['status']  = 0;
                }
            } else {
                $result['message'] = __('Please select story');
                $result['status']  = 0;
            }
        }
        return $result;
    }

    public function setupLink($link)
    {
        // Remove
        Pi::model('link', $this->getModule())->delete(
            [
                'story' => $link['story'],
            ]
        );
        // process link module
        foreach ($link['module'] as $module) {
            // Check module
            if (isset($module['controller'])
                && !empty($module['controller'])
                && is_array($module['controller'])
                && isset($module['name'])
                && !empty($module['name'])
            ) {
                // process module controller
                foreach ($module['controller'] as $controller) {
                    // Check module controller
                    if (isset($controller['topic'])
                        && !empty($controller['topic'])
                        && is_array($controller['topic'])
                        && isset($controller['name'])
                        && !empty($controller['name'])
                    ) {
                        // process controller topic
                        foreach ($controller['topic'] as $topic) {
                            // Check controller topic
                            if (isset($topic) && intval($topic) > 0) {
                                // Set link values
                                $values['story']        = intval($link['story']);
                                $values['time_publish'] = intval($link['time_publish']);
                                $values['time_update']  = intval($link['time_update']);
                                $values['status']       = intval($link['status']);
                                $values['uid']          = intval($link['uid']);
                                $values['type']         = $link['type'];
                                // Set topic / controller / module
                                $values['topic']      = intval($topic);
                                $values['controller'] = $controller['name'];
                                $values['module']     = $module['name'];
                                // Save
                                $row = Pi::model('link', $this->getModule())->createRow();
                                $row->assign($values);
                                $row->save();
                            }
                        }
                    }
                }
            }
        }
    }

    public function getStorySingle($parameter, $field, $type = 'full', $option = [])
    {
        // Set option
        $option['authorSet'] = isset($option['authorSet']) ? $option['authorSet'] : false;

        switch ($type) {
            case 'json':
                $story = Pi::api('story', 'news')->getStoryJson($parameter, $field, $option);
                break;

            case 'light':
                $story = Pi::api('story', 'news')->getStoryLight($parameter, $field, $option);
                break;

            default:
            case 'full':
                $story = Pi::api('story', 'news')->getStory($parameter, $field, $option);
                break;
        }
        return $story;
    }

    public function getStoryList($where = [], $order = [], $offset = '', $limit = 10, $type = 'full', $table = 'link', $option = [])
    {
        $list = [];
        switch ($table) {
            case 'story':
                // Select from story table
                $select = Pi::model('story', $this->getModule())->select();
                if (!empty($where)) {
                    $select->where($where);
                }
                if (!empty($order)) {
                    $select->order($order);
                }
                if (!empty($offset)) {
                    $select->offset($offset);
                }
                if (!empty($limit)) {
                    $select->limit($limit);
                }
                $rowSet = Pi::model('story', $this->getModule())->selectWith($select);
                break;

            default:
            case 'link':

                // Select from link table
                $select = Pi::model('link', $this->getModule())->select();
                if (!empty($where)) {
                    $select->where($where);
                }
                if (!empty($order)) {
                    $select->order($order);
                }
                if (!empty($offset)) {
                    $select->offset($offset);
                }
                if (!empty($limit)) {
                    $select->limit($limit);
                }
                $columns = ['story' => new Expression('DISTINCT story')];
                $select->columns($columns);
                $rowSetLink = Pi::model('link', $this->getModule())->selectWith($select);
                $storyId    = [];
                foreach ($rowSetLink as $id) {
                    $storyId[] = $id['story'];
                }
                // Select from story table
                if (!empty($storyId)) {
                    $whereStory = ['id' => $storyId];
                    $select     = Pi::model('story', $this->getModule())->select()->where($whereStory)->order($order);
                    $rowSet     = Pi::model('story', $this->getModule())->selectWith($select);
                } else {
                    return $list;
                }
                break;
        }

        // Set option
        $option['authorSet'] = isset($option['authorSet']) ? $option['authorSet'] : false;

        // Make list
        $topicList = Pi::registry('topicList', 'news')->read();
        foreach ($rowSet as $row) {
            switch ($type) {
                case 'json':
                    $list[$row->id] = Pi::api('story', 'news')->canonizeStoryJson($row, $option);
                    break;

                case 'light':
                    $list[$row->id] = Pi::api('story', 'news')->canonizeStoryLight($row, $option);
                    break;

                default:
                case 'full':
                    $list[$row->id] = Pi::api('story', 'news')->canonizeStory($row, $topicList, [], $option);
                    break;
            }
        }
        return $list;
    }

    public function getStoryPaginator($template, $where = [], $page = 1, $limit = 10, $table = 'link')
    {
        // Set count
        switch ($table) {
            case 'story':
                $columns = ['count' => new Expression('count(*)')];
                $select  = Pi::model('story', $this->getModule())->select()->where($where)->columns($columns);
                $count   = Pi::model('story', $this->getModule())->selectWith($select)->current()->count;
                break;

            default:
            case 'link':
                $columns = ['count' => new Expression('count(DISTINCT `story`)')];
                $select  = Pi::model('link', $this->getModule())->select()->where($where)->columns($columns);
                $count   = Pi::model('link', $this->getModule())->selectWith($select)->current()->count;
                break;
        }

        // Check template
        $template['module']     = (isset($template['module'])) ? $template['module'] : 'news';
        $template['controller'] = (isset($template['controller'])) ? $template['controller'] : 'index';
        $template['action']     = (isset($template['action'])) ? $template['action'] : 'index';
        $template['slug']       = (isset($template['slug'])) ? $template['slug'] : '';
        $template['id']         = (isset($template['id'])) ? $template['id'] : '';
        $template['status']     = (isset($template['status'])) ? $template['status'] : '';
        $template['topic']      = (isset($template['topic'])) ? $template['topic'] : '';
        $template['uid']        = (isset($template['uid'])) ? $template['uid'] : '';
        $template['title']      = (isset($template['title'])) ? $template['title'] : '';
        $template['status']     = (isset($template['status'])) ? $template['status'] : '';
        // Set paginator
        $paginator = Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(
            [
                //'router' => $this->getEvent()->getRouter(),
                //'route' => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
                'params' => array_filter(
                    [
                        'module'     => $template['module'],
                        'controller' => $template['controller'],
                        'action'     => $template['action'],
                        'slug'       => $template['slug'],
                        'id'         => $template['id'],
                        'status'     => $template['status'],
                        'topic'      => $template['topic'],
                        'uid'        => $template['uid'],
                        'title'      => $template['title'],
                        'status'     => $template['status'],
                    ]
                ),
            ]
        );

        return $paginator;
    }

    public function getStoryCount($where = [], $table = 'link')
    {
        // Set count
        switch ($table) {
            case 'story':
                $columns = ['count' => new Expression('count(*)')];
                $select  = Pi::model('story', $this->getModule())->select()->where($where)->columns($columns);
                $count   = Pi::model('story', $this->getModule())->selectWith($select)->current()->count;
                break;

            default:
            case 'link':
                $columns = ['count' => new Expression('count(DISTINCT `story`)')];
                $select  = Pi::model('link', $this->getModule())->select()->where($where)->columns($columns);
                $count   = Pi::model('link', $this->getModule())->selectWith($select)->current()->count;
                break;
        }


        return $count;
    }

    public function getStoryRelated($where, $order)
    {
        // Set info
        $config  = Pi::service('registry')->config->read($this->getModule());
        $related = [];
        $columns = ['story' => new Expression('DISTINCT story')];
        $limit   = intval($config['related_num']);
        // Get info from link table
        $select = Pi::model('link', $this->getModule())->select()->where($where)->columns($columns)->order($order)->limit($limit);
        $rowset = Pi::model('link', $this->getModule())->selectWith($select)->toArray();
        // Make list
        foreach ($rowset as $id) {
            $storyId[] = $id['story'];
        }
        // Get story
        if (!empty($storyId)) {
            $related = Pi::api('story', 'news')->getListFromIdLight($storyId);
        }
        return $related;
    }

    public function jsonList($options)
    {
        // Get info from url
        $module = $this->getModule();

        // Set has search result
        $hasSearchResult = true;

        // Clean title
        if (Pi::service('module')->isActive('search') && isset($options['title']) && !empty($options['title'])) {
            $options['title'] = Pi::api('api', 'search')->parseQuery($options['title']);
        } elseif (isset($options['title']) && !empty($options['title'])) {
            $options['title'] = _strip($options['title']);
        } else {
            $options['title'] = '';
        }

        // Clean params
        $paramsClean = [];
        foreach ($_GET as $key => $value) {
            $key               = _strip($key);
            $value             = _strip($value);
            $paramsClean[$key] = $value;
        }

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Set empty result
        $result = [
            'stories'   => [],
            'paginator' => [],
            'condition' => [],
        ];

        // Set where link
        $whereLink = [
            'status' => 1,
            'type'   => [
                'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download',
            ],
        ];

        // Set page title
        $pageTitle = __('List of stories');

        // Set order
        $order = ['time_publish DESC', 'id DESC'];

        // Check type
        $type = 'full';
        if (isset($options['type']) && in_array($options['type'], ['json', 'light', 'full'])) {
            $type = $options['type'];
        }

        // Get topic information from model
        if (!empty($options['topic'])) {
            // Get topic
            $topic = Pi::api('topic', 'news')->getTopicFull($options['topic'], 'slug');
            // Check topic
            if (!$topic || $topic['status'] != 1) {
                return $result;
            }
            $topicIDList   = [];
            $topicIDList[] = $topic['id'];
            if (isset($topic['ids']) && !empty($topic['ids'])) {
                foreach ($topic['ids'] as $topicSingle) {
                    $topicIDList[] = $topicSingle;
                }
            }
            // Set page title
            $pageTitle = sprintf(__('List of stories on %s topic'), $topic['title']);
        }

        // Get tag list
        if (!empty($options['tag'])) {
            $storyIDTag = [];
            // Check favourite
            if (!Pi::service('module')->isActive('tag')) {
                return $result;
            }
            // Get id from tag module
            $tagList = Pi::service('tag')->getList($options['tag'], $module);
            foreach ($tagList as $tagSingle) {
                $storyIDTag[] = $tagSingle['item'];
            }
            // Set header and title
            $pageTitle = sprintf(__('All stories from %s'), $options['tag']);
        }

        // Set story ID list
        $checkTitle  = false;
        $storyIDList = [
            'title' => [],
        ];

        // Check title from story table
        if (isset($options['title']) && !empty($options['title'])) {
            $checkTitle = true;
            $titles     = is_array($options['title']) ? $options['title'] : [$options['title']];
            $columns    = ['id'];
            $select     = Pi::model('story', $module)->select()->columns($columns)->where(
                function ($where) use ($titles) {
                    $whereMain = clone $where;
                    $whereKey  = clone $where;
                    $whereMain->equalTo('status', 1);
                    foreach ($titles as $title) {
                        $whereKey->like('title', '%' . $title . '%')->and;
                    }
                    $where->andPredicate($whereMain)->andPredicate($whereKey);
                }
            )->order($order);
            $rowset     = Pi::model('story', $module)->selectWith($select);
            foreach ($rowset as $row) {
                $storyIDList['title'][$row->id] = $row->id;
            }
        }

        // Set info
        $story = [];
        $count = 0;

        $limit  = (intval($options['limit']) > 0) ? intval($options['limit']) : intval($config['view_perpage']);
        $offset = (int)($options['page'] - 1) * $limit;

        // Set topic on where link
        if (isset($topicIDList) && !empty($topicIDList)) {
            $whereLink['topic'] = $topicIDList;
        }

        // Set story on where link from title and attribute
        if ($checkTitle) {
            if (!empty($storyIDList)) {
                $whereLink['story'] = $storyIDList;
            } else {
                $hasSearchResult = false;
            }
        }

        // Set tag story on where link
        if (!empty($tag) && isset($storyIDTag)) {
            if (isset($whereLink['story']) && !empty($whereLink['story'])) {
                $whereLink['story'] = array_intersect($storyIDTag, $whereLink['story']);
            } elseif (!isset($whereLink['story']) || empty($whereLink['story'])) {
                $whereLink['story'] = $storyIDTag;
            } else {
                $hasSearchResult = false;
            }
        }

        // Check has Search Result
        if ($hasSearchResult) {
            // Set option
            $storyOptions = [
                'getUser' => $options['getUser'],
                'fields'  => (!empty($options['fields']) && is_array($options['fields'])) ? $options['fields'] : [],
            ];

            // Get story
            $story = Pi::api('api', 'news')->getStoryList($whereLink, $order, $offset, $limit, $type, 'link', $storyOptions);
            $count = Pi::api('api', 'news')->getStoryCount($whereLink, 'link');
            $story = array_values($story);
        }

        // Set result
        $result = [
            'stories'   => $story,
            'paginator' => [
                'count' => $count,
                'limit' => $limit,
                'page'  => $options['page'],
            ],
            'condition' => [
                'title' => $pageTitle,
            ],
        ];

        return $result;
    }

    public function jsonSingle($id, $getUser = false)
    {
        // Find story
        $story = Pi::api('story', 'news')->getStory($id);
        // Check item
        if (!$story || $story['status'] != 1
            || !in_array(
                $story['type'], [
                    'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download',
                ]
            )
        ) {
            $storySingle = [];
        } else {

            // Set text_summary
            $story['text_summary'] = strip_tags($story['text_summary'], "<b><strong><i><p><br><ul><li><ol><h2><h3><h4>");
            $story['text_summary'] = str_replace("<p>&nbsp;</p>", "", $story['text_summary']);

            // Set text_description
            $story['text_description'] = strip_tags($story['text_description'], "<b><strong><i><p><br><ul><li><ol><h2><h3><h4>");
            $story['text_description'] = str_replace("<p>&nbsp;</p>", "", $story['text_description']);

            $storySingle = [
                'id'                => $story['id'],
                'title'             => $story['title'],
                'subtitle'          => $story['subtitle'],
                'topic'             => $story['topic'][0],
                'text_summary'      => $story['text_summary'],
                'text_description'  => $story['text_description'],
                'time_publish'      => $story['time_publish'],
                'time_publish_view' => $story['time_publish_view'],
                'storyUrl'          => $story['storyUrl'],
                'largeUrl'          => $story['largeUrl'],
                'mediumUrl'         => $story['mediumUrl'],
                'thumbUrl'          => $story['thumbUrl'],
            ];

            if ($getUser) {
                $user                      = Pi::user()->get(
                    $story['uid'], [
                        'id', 'identity', 'name', 'email',
                    ]
                );
                $storySingle['userName']   = $user['name'];
                $storySingle['userAvatar'] = '';
            }
        }
        $storySingle = [$storySingle];
        // Set view
        return $storySingle;
    }

    public function jsonSubmit($data)
    {
        $result           = [];
        $result['status'] = 0;

        // Set object to array
        $data = $data->toArray();
        // Check
        if (isset($data['uid'])
            && !empty($data['uid'])
            && $data['uid'] > 0
            && isset($data['title'])
            && !empty($data['title'])
            && isset($data['body'])
            && !empty($data['body'])
        ) {

            // Set slug
            $slug = uniqid('story-');

            // Set image
            $imageId = '';
            if (isset($data['image'])) {
                // Save image
                $imageFile = sprintf('upload/news/app/%s.jpg', $slug);
                $imagePath = Pi::path($imageFile);
                $ifp       = fopen($imagePath, 'wb');
                fwrite($ifp, base64_decode($data['image']));
                fclose($ifp);
                // Insert to media module
                if (Pi::service('file')->exists($imagePath)) {
                    // From media module
                    $options      = Pi::service('media')->getOption('local', 'options');
                    $rootPath     = $options['root_path'];
                    $baseFilename = basename($imagePath);
                    $path         = Pi::api('doc', 'media')->getMediaPath($baseFilename);
                    $slug         = Pi::api('doc', 'media')->getSlugFilename($baseFilename);
                    $destination  = $rootPath . $path . $slug;
                    $mediaData    = [
                        'active'       => 1,
                        'time_created' => time(),
                        'uid'          => intval($data['uid']),
                        'count'        => 1,
                        'title'        => _strip($data['title']),
                        'mimetype'     => 'image/jpeg',
                        'path'         => $path,
                        'filename'     => $slug,
                    ];

                    Pi::service('file')->mkdir($rootPath . $path);
                    if (!is_file($destination)) {
                        Pi::service('file')->copy($imagePath, $destination);
                    }

                    $mediaEntity = Pi::model('doc', 'media')->select(['filename' => $slug])->current();
                    if (!$mediaEntity || !$mediaEntity->id) {
                        $mediaEntity = Pi::model('doc', 'media')->createRow($mediaData);
                        $mediaEntity->save();
                        $imageId = $mediaEntity->id;
                    }
                }
            }

            // Save
            $row                    = Pi::model('story', 'news')->createRow();
            $row->title             = _strip($data['title']);
            $row->slug              = $slug;
            $row->status            = 2;
            $row->time_create       = time();
            $row->type              = 'text';
            $row->text_description  = _strip($data['body']);
            $row->uid               = intval($data['uid']);
            $row->main_image        = $imageId;
            $row->additional_images = '';
            $row->save();

            // Set status
            $result['status'] = 1;
        }

        return $result;
    }
}