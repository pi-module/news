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

namespace Module\News\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;

class JsonController extends IndexController
{
    /* public function indexAction()
    {
        // Set view
        $this->view()->setTemplate(false)->setLayout('layout-content');
        // Get info from url
        $story = array();
        $module = $this->params('module');
        $topic = $this->params('topic');
        $start = $this->params('start');
        $limit = $this->params('limit');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Check json
        if (!$config['admin_json']) {
            echo json_encode($story);
            exit;
        }
        // Get topic information from model
        $topic = $this->getModel('topic')->find($topic);
        // Check slug set
        if(empty($topic)) {
            echo json_encode($story);
            exit;
        }
        // Get topic or homepage setting
        $topic = Pi::api('topic', 'news')->canonizeTopic($topic);
        // Check topic
        if ($topic['status'] != 1) {
            echo json_encode($story);
            exit;
        }
        // Check topic style
        if ($topic['style'] == 'topic') {
            // Get topic list
            $where = array('status' => 1, 'pid' => $topic['id']);
            $order = array('id DESC');
            $select = $this->getModel('topic')->select()->where($where)->order($order);
            $rowset = $this->getModel('topic')->selectWith($select);
            foreach ($rowset as $row) {
                $topics[$row->id] = Pi::api('topic', 'news')->canonizeTopic($row);
            }
            echo json_encode($topics);
            exit;
        } else {
            // Get story List
            $storyList = $this->jsonList($topic, $start, $limit);
            echo json_encode($storyList);
            exit;
        }
    } */

    /* public function indexAction()
    {
        // Set return
        $return = [
            'website' => Pi::url(),
            'module'  => $this->params('module'),
        ];
        // Set view
        return $return;
    } */

    /* public function searchAction()
    {
        // Get info from url
        $module = $this->params('module');
        $page   = $this->params('page', 1);
        $title  = $this->params('title');
        $topic  = $this->params('topic');
        $tag    = $this->params('tag');
        $limit  = $this->params('limit');

        // Set has search result
        $hasSearchResult = true;

        // Clean title
        if (Pi::service('module')->isActive('search') && isset($title) && !empty($title)) {
            $title = Pi::api('api', 'search')->parseQuery($title);
        } elseif (isset($title) && !empty($title)) {
            $title = _strip($title);
        } else {
            $title = '';
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

        // Get topic information from model
        if (!empty($topic)) {
            // Get topic
            $topic = Pi::api('topic', 'news')->getTopicFull($topic, 'slug');
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
        if (!empty($tag)) {
            $storyIDTag = [];
            // Check favourite
            if (!Pi::service('module')->isActive('tag')) {
                return $result;
            }
            // Get id from tag module
            $tagList = Pi::service('tag')->getList($tag, $module);
            foreach ($tagList as $tagSingle) {
                $storyIDTag[] = $tagSingle['item'];
            }
            // Set header and title
            $pageTitle = sprintf(__('All stories from %s'), $tag);
        }

        // Set story ID list
        $checkTitle  = false;
        $storyIDList = [
            'title' => [],
        ];

        // Check title from story table
        if (isset($title) && !empty($title)) {
            $checkTitle = true;
            $titles     = is_array($title) ? $title : [$title];
            $columns    = ['id'];
            $select     = $this->getModel('story')->select()->columns($columns)->where(
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
            $rowset     = $this->getModel('story')->selectWith($select);
            foreach ($rowset as $row) {
                $storyIDList['title'][$row->id] = $row->id;
            }
        }

        // Set info
        $story = [];
        $count = 0;

        $limit  = (intval($limit) > 0) ? intval($limit) : intval($config['view_perpage']);
        $offset = (int)($page - 1) * $limit;

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
            // Get story
            $story = Pi::api('api', 'news')->getStoryList($whereLink, $order, $offset, $limit, 'full', 'link');
            $count = Pi::api('api', 'news')->getStoryCount($whereLink, 'link');
            $story = array_values($story);
        }

        // Set result
        $result = [
            'stories'   => $story,
            'paginator' => [
                'count' => $count,
                'limit' => $limit,
                'page'  => $page,
            ],
            'condition' => [
                'title' => $pageTitle,
            ],
        ];

        return $result;
    } */

    /* public function storyAllAction()
    {
        // Get info from url
        $update = $this->params('update', 0);
        // Check password
        if (!$this->checkPassword()) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('Password not set or wrong'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Set story info
        $where = [
            'status'          => 1,
            'time_update > ?' => $update,
            'type'            => [
                'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download',
            ],
        ];
        // Get story List
        $storyList = $this->storyJsonList($where);
        // Set view
        return $storyList;
    } */

    /* public function storyTopicAction()
    {
        // Get info from url
        $id     = $this->params('id', 0);
        $update = $this->params('update', 0);
        // Check password
        if (!$this->checkPassword()) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('Password not set or wrong'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get topic information from model
        $topic = $this->getModel('topic')->find($id);
        $topic = Pi::api('topic', 'news')->canonizeTopic($topic);
        // Check category
        if (!$topic || $topic['status'] != 1) {
            $storyList = [];
        } else {
            // Set story info
            $where = [
                'status'          => 1,
                'topic'           => $topic['ids'],
                'time_update > ?' => $update,
                'type'            => [
                    'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download',
                ],
            ];
            // Get story List
            $storyList = $this->storyJsonList($where);
        }
        // Set view
        return $storyList;
    } */

    /* public function storySingleAction()
    {
        // Get info from url
        $id     = $this->params('id');
        $module = $this->params('module');
        // Check password
        if (!$this->checkPassword()) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('Password not set or wrong'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get topic list
        $topicList = Pi::registry('topicList', 'news')->read();
        // Get author list
        $authorList = Pi::registry('authorList', 'news')->read();
        // Find story
        $story = $this->getModel('story')->find($id);
        $story = Pi::api('story', 'news')->canonizeStory($story, $topicList, $authorList);
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
            $story['text_summary'] = Pi::service('markup')->render($story['text_summary'], 'html', 'html');
            $story['text_summary'] = strip_tags($story['text_summary'], "<b><strong><i><p><br><ul><li><ol><h2><h3><h4>");
            $story['text_summary'] = str_replace("<p>&nbsp;</p>", "", $story['text_summary']);

            // Set text_description
            $story['text_description'] = Pi::service('markup')->render($story['text_description'], 'html', 'html');
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
        }
        $storySingle = [$storySingle];
        // Set view
        return $storySingle;
    } */

    /* public function storySubmitAction()
    {
        $result = array();
        // Check password
        if (!$this->checkPassword()) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('Password not set or wrong'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $data = $data->toArray();
            if (isset($data['uid']) &&
                !empty($data['uid']) &&
                $data['uid'] > 0 &&
                isset($data['title']) &&
                !empty($data['title']) &&
                isset($data['body']) &&
                !empty($data['body'])) {

                $id = uniqid('story-');
                $row = $this->getModel('story')->createRow();
                $row->title = _strip($data['title']);
                $row->slug = $id;
                $row->status = 2;
                $row->time_create = time();
                $row->type = 'text';
                $row->text_description = _strip($data['body']);
                $row->uid = intval($data['uid']);
                $row->main_image = '';
                $row->additional_images = '';
                $row->save();
                $result = array(
                    'status' => 1,
                    'message' => 'OK',
                );
            } else {
                $result = array(
                    'status' => 0,
                    'message' => 'Error story 1',
                );
            }
        } else {
            $result = array(
                'status' => 0,
                'message' => 'Error story 2',
            );
        }

        return $result;
    } */

    /* public function filterSearchAction()
    {
        // Get info from url
        $module  = $this->params('module');
        $keyword = $this->params('keyword');
        // Check keyword not empty
        if (empty($keyword)) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The keyword not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Set list
        $list = [];
        // Set info
        $where1                            = ['status' => 1, 'type' => [
            'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download',
        ]];
        $where1['title LIKE ?']            = '%' . $keyword . '%';
        $where2['text_summary LIKE ?']     = '%' . $keyword . '%';
        $where3['text_description LIKE ?'] = '%' . $keyword . '%';
        $order                             = ['time_create DESC', 'id DESC'];
        // Item list header
        $list[] = [
            'class' => ' class="dropdown-header"',
            'title' => sprintf(__('Stories related to %s'), $keyword),
            'url'   => '#',
            'image' => Pi::service('asset')->logo(),
        ];
        // Get list of product
        $select = $this->getModel('story')->select()->where($where1)->where($where2, 'OR')->where($where2, 'OR')->order($order)->limit(10);
        $rowset = $this->getModel('story')->selectWith($select);
        foreach ($rowset as $row) {
            $story  = Pi::api('story', 'news')->canonizeStoryLight($row);
            $list[] = [
                'class' => '',
                'title' => $story['title'],
                'url'   => $story['productUrl'],
                'image' => isset($story['thumbUrl']) ? $story['thumbUrl'] : Pi::service('asset')->logo(),
            ];
        }
        // Set view
        return $list;
    } */

    /* public function checkPassword()
    {
        // Get info from url
        $module   = $this->params('module');
        $password = $this->params('password');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Check password
        if ($config['json_check_password']) {
            if ($config['json_password'] == $password) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    } */
}