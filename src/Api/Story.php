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
use Laminas\Db\Sql\Predicate\Expression;

/*
 * Pi::api('story', 'news')->getStory($parameter, $field, $option);
 * Pi::api('story', 'news')->getStoryLight($parameter, $field, $option);
 * Pi::api('story', 'news')->getStoryJson($parameter, $field, $option)
 * Pi::api('story', 'news')->AttachCount($id);
 * Pi::api('story', 'news')->AttachList($id);
 * Pi::api('story', 'news')->attributeCount($id);
 * Pi::api('story', 'news')->Related($id, $topic;
 * Pi::api('story', 'news')->Link($id, $topic);
 * Pi::api('story', 'news')->getListFromId($id);
 * Pi::api('story', 'news')->getListFromIdLight($id);
 * Pi::api('story', 'news')->FavoriteList();
 * Pi::api('story', 'news')->canonizeStory($story, $topicList, $authorList, $option);
 * Pi::api('story', 'news')->canonizeStoryLight($story, $option);
 * Pi::api('story', 'news')->canonizeStoryJson($story, $option);
 * Pi::api('story', 'news')->sitemap();
 * Pi::api('story', 'news')->regenerateImage();
 */

class Story extends AbstractApi
{
    public function __construct()
    {
        $this->module = Pi::service('module')->current();
    }

    public function getStory($parameter, $field = 'id', $option = [])
    {
        // Get product
        $story = Pi::model('story', 'news')->find($parameter, $field);
        $story = $this->canonizeStory($story, '', '', $option);
        return $story;
    }

    public function getStoryLight($parameter, $field = 'id', $option = [])
    {
        // Get product
        $story = Pi::model('story', 'news')->find($parameter, $field);
        $story = $this->canonizeStoryLight($story, $option);
        return $story;
    }

    public function getStoryJson($parameter, $field = 'id', $option = [])
    {
        // Get product
        $story = Pi::model('story', 'news')->find($parameter, $field);
        $story = $this->canonizeStoryJson($story, $option);
        return $story;
    }

    /**
     * Set number of attach files for selected story
     */
    public function AttachCount($id)
    {
        // set info
        $where   = ['item_id' => $id, 'item_table' => 'story'];
        $columns = ['count' => new Expression('count(*)')];
        // Get attach count
        $select = Pi::model('attach', 'news')->select()->columns($columns)->where($where);
        $count  = Pi::model('attach', 'news')->selectWith($select)->current()->count;
        // Set attach count
        Pi::model('story', 'news')->update(['attach' => $count], ['id' => $id]);
    }

    /**
     * Get list of attach files
     */
    public function AttachList($id)
    {
        // Get config
        $config = Pi::service('registry')->config->read('news');
        // Set info
        $where = ['item_id' => $id, 'item_table' => 'story', 'status' => 1];
        $order = ['time_create DESC', 'id DESC'];
        // Get all attach files
        $select = Pi::model('attach', 'news')->select()->where($where)->order($order);
        $rowset = Pi::model('attach', 'news')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $file[$row->type][$row->id]                = $row->toArray();
            $file[$row->type][$row->id]['time_create'] = _date($file[$row->type][$row->id]['time_create']);
            // Set file link
            if ($file[$row->type][$row->id]['type'] == 'image') {
                $file[$row->type][$row->id]['largeUrl']  = Pi::url(
                    sprintf(
                        'upload/%s/large/%s/%s',
                        $config['image_path'],
                        $file[$row->type][$row->id]['path'],
                        $file[$row->type][$row->id]['file']
                    )
                );
                $file[$row->type][$row->id]['mediumUrl'] = Pi::url(
                    sprintf(
                        'upload/%s/medium/%s/%s',
                        $config['image_path'],
                        $file[$row->type][$row->id]['path'],
                        $file[$row->type][$row->id]['file']
                    )
                );
                $file[$row->type][$row->id]['thumbUrl']  = Pi::url(
                    sprintf(
                        'upload/%s/thumb/%s/%s',
                        $config['image_path'],
                        $file[$row->type][$row->id]['path'],
                        $file[$row->type][$row->id]['file']
                    )
                );
            } elseif ($file[$row->type][$row->id]['type'] == 'other') {
                $file[$row->type][$row->id]['fileUrl'] = Pi::url(
                    sprintf(
                        'upload/%s/%s/%s/%s',
                        $config['file_path'],
                        'file',
                        $file[$row->type][$row->id]['path'],
                        $file[$row->type][$row->id]['file']
                    )
                );
            } else {
                $file[$row->type][$row->id]['fileUrl'] = Pi::url(
                    sprintf(
                        'upload/%s/%s/%s/%s',
                        $config['file_path'],
                        $file[$row->type][$row->id]['type'],
                        $file[$row->type][$row->id]['path'],
                        $file[$row->type][$row->id]['file']
                    )
                );
            }
            // Set download url
            $file[$row->type][$row->id]['downloadUrl'] = Pi::url(
                Pi::service('url')->assemble(
                    'news', [
                        'module'     => 'news',
                        'controller' => 'media',
                        'action'     => 'download',
                        'id'         => $row->id,
                    ]
                )
            );
        }
        // return
        return $file;
    }

    /**
     * Set number of used attribute fields for selected story
     */
    public function attributeCount($id)
    {
        // set info
        $where   = ['story' => $id];
        $columns = ['count' => new Expression('count(*)')];
        // Get attribute count
        $select = Pi::model('field_data', 'news')->select()->columns($columns)->where($where);
        $count  = Pi::model('field_data', 'news')->selectWith($select)->current()->count;
        // Set attribute count
        Pi::model('story', 'news')->update(['attribute' => $count], ['id' => $id]);
    }

    /**
     * Get related stores
     */
    public function Related($id, $topic)
    {
        // Get config
        $config = Pi::service('registry')->config->read('news');

        // Set info
        $related = [];
        $storyId = [];
        $order   = ['time_publish DESC', 'id DESC'];
        $columns = ['story' => new Expression('DISTINCT story'), '*'];
        $limit   = intval($config['related_num']);
        $where   = [
            'status'            => 1,
            'story != ?'        => $id,
            'time_publish <= ?' => time(),
            'topic'             => $topic,
            'type'              => [
                'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download',
            ],
        ];

        // Get info from link table
        $select = Pi::model('link', 'news')->select()->where($where)->columns($columns)->order($order)->limit($limit);
        $rowset = Pi::model('link', 'news')->selectWith($select);

        // Make list
        if (!empty($rowset)) {
            $rowset = $rowset->toArray();
            foreach ($rowset as $id) {
                $storyId[] = $id['story'];
            }
        }

        // Get story
        if (!empty($storyId)) {
            $related = $this->getListFromIdLight($storyId);
        }
        return $related;
    }

    public function Link($id, $topic)
    {
        // Set info
        $link    = [];
        $columns = ['story'];
        // Select next
        $where  = [
            'status'            => 1,
            'story > ?'         => $id,
            'time_publish <= ?' => time(),
            'topic'             => $topic,
            'type'              => [
                'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download',
            ],
        ];
        $select = Pi::model('link', 'news')->select()->columns($columns)->where($where)->order(['id ASC'])->limit(1);
        $row    = Pi::model('link', 'news')->selectWith($select)->current();
        if (!empty($row)) {
            $row   = $row->toArray();
            $story = Pi::model('story', 'news')->find($row['story']);

            if (!empty($story)) {
                $story                 = $story->toArray();
                $link['next']['title'] = $story['title'];
                $link['next']['url']   = Pi::url(
                    Pi::service('url')->assemble(
                        'news', [
                            'module'     => 'news',
                            'controller' => 'story',
                            'slug'       => $story['slug'],
                        ]
                    )
                );
            }
        }
        // Select Prev
        $where  = [
            'status'            => 1,
            'story <  ?'        => $id,
            'time_publish <= ?' => time(),
            'topic'             => $topic,
            'type'              => [
                'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download',
            ],
        ];
        $select = Pi::model('link', 'news')->select()->columns($columns)->where($where)->order(['id ASC'])->limit(1);
        $row    = Pi::model('link', 'news')->selectWith($select)->current();
        if (!empty($row)) {
            $row   = $row->toArray();
            $story = Pi::model('story', 'news')->find($row['story']);

            if (!empty($story)) {
                $story                     = $story->toArray();
                $link['previous']['title'] = $story['title'];
                $link['previous']['url']   = Pi::url(
                    Pi::service('url')->assemble(
                        'news', [
                            'module'     => 'news',
                            'controller' => 'story',
                            'slug'       => $story['slug'],
                        ]
                    )
                );
            }
        }
        return $link;
    }

    public function getListFromId($id)
    {
        $list   = [];
        $where  = ['id' => $id, 'status' => 1];
        $select = Pi::model('story', 'news')->select()->where($where);
        $rowset = Pi::model('story', 'news')->selectWith($select);
        foreach ($rowset as $row) {
            $list[$row->id] = $this->canonizeStory($row);
        }
        return $list;
    }

    public function getListFromIdLight($id)
    {
        $list   = [];
        $where  = ['id' => $id, 'status' => 1];
        $order  = ['time_publish DESC', 'id DESC'];
        $select = Pi::model('story', 'news')->select()->where($where)->order($order);
        $rowset = Pi::model('story', 'news')->selectWith($select);
        foreach ($rowset as $row) {
            $list[$row->id] = $this->canonizeStoryLight($row);
        }
        return $list;
    }

    public function favoriteList($uid = null)
    {
        // Get user id
        if ($uid == null) {
            $uid = Pi::user()->getId();
        }

        // Check user
        if ($uid > 0) {
            $favoriteIds = Pi::api('favourite', 'favourite')->userFavourite($uid, 'news');
            // Check list of ides
            if (!empty($favoriteIds)) {
                // Get config

                // TOPIC
                $select = Pi::model('topic', 'news')->select();
                $rowset = Pi::model('topic', 'news')->selectWith($select);
                $topics = [];
                foreach ($rowset as $row) {
                    $topics[$row->id] = [
                        'title'       => $row->title,
                        'categoryUrl' => Pi::url(
                            Pi::service("url")->assemble(
                                "news", [
                                    "module"     => 'news',
                                    "controller" => "category",
                                    "slug"       => $row->slug,
                                ]
                            )
                        ),
                    ];
                }
                //

                $config = Pi::service('registry')->config->read('news');
                // Set list
                $list  = [];
                $where = [
                    'id'     => $favoriteIds,
                    'status' => 1,
                    'type'   => [
                        'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download',
                    ],
                ];


                $select = Pi::model('story', 'news')->select()->columns(['title', 'slug', 'time_publish', 'main_image', 'id', 'topic'])->where(
                    $where
                );
                $rowset = Pi::model('story', 'news')->selectWith($select);
                foreach ($rowset as $row) {
                    $story = $row->toArray();

                    $story['url'] = Pi::url(
                        Pi::service('url')->assemble(
                            'news', [
                                'module'     => 'news',
                                'controller' => 'story',
                                'slug'       => $row->slug,
                            ]
                        )
                    );

                    $story['categories'] = [];
                    $storyTopics         = json_decode($story['topic']);
                    foreach ($storyTopics as $idTopic) {
                        $story['categories'][] = $topics[$idTopic];
                    }

                    $story['image'] = '';
                    if ($row->main_image) {
                        $story["image"] = Pi::url(
                            (string)Pi::api('doc', 'media')->getSingleLinkUrl($row->main_image)->setConfigModule('news')->thumb('medium')
                        );
                    }
                    $list[$row->id] = $story;
                }
                return $list;
            } else {
                return [];
            }
        } else {
            return [];
        }
    }

    public function canonizeStory($story, $topicList = [], $authorList = [], $option = [])
    {
        // Check
        if (empty($story)) {
            return '';
        }
        // Get config
        if (!isset($this->config)) {
            $this->config = Pi::service('registry')->config->read('news');
        }

        // Set option
        $option['authorSet'] = isset($option['authorSet']) ? $option['authorSet'] : true;
        $option['imagePath'] = isset($option['imagePath']) ? $option['imagePath'] : $this->config['image_path'];
        // object to array
        $story = $story->toArray();
        // Set text_summary

        if (!isset($this->markupService)) {
            $this->markupService = Pi::service('markup');
        }

        $story['text_summary'] = $this->markupService->render($story['text_summary'], 'html', 'html');
        // Set text_description
        $story['text_description'] = $this->markupService->render($story['text_description'], 'html', 'html');
        // Set times
        $story['time_create_view']  = _date($story['time_create']);
        $story['time_publish_view'] = _date($story['time_publish']);
        $story['time_update_view']  = _date($story['time_update']);
        // Set story url
        $story['storyUrl'] = Pi::url(
            Pi::service('url')->assemble(
                'news', [
                    'module'     => 'news',
                    'controller' => 'story',
                    'slug'       => $story['slug'],
                ]
            )
        );
        // Set topic information
        $story['topic'] = json_decode($story['topic'], true);
        // Get topic list
        if (!empty($story['topic'])) {
            $topicList = (empty($topicList)) ? Pi::registry('topicList', 'news')->read() : $topicList;
            foreach ($story['topic'] as $topic) {
                if (!empty($topicList[$topic]['title'])) {
                    $story['topics'][$topic]['title'] = $topicList[$topic]['title'];
                    $story['topics'][$topic]['slug']  = $topicList[$topic]['slug'];
                    $story['topics'][$topic]['url']   = Pi::url(
                        Pi::service('url')->assemble(
                            'news', [
                                'module'     => 'news',
                                'controller' => 'topic',
                                'slug'       => $topicList[$topic]['slug'],
                            ]
                        )
                    );
                }
            }
        }
        // Get author list
        $story['authors'] = [];
        if ($this->config['show_author'] && !empty($authorList) && $option['authorSet']) {
            $story['author'] = json_decode($story['author'], true);
            if (!empty($story['author'])) {
                foreach ($story['author'] as $author) {
                    if (!empty($author['author'])) {
                        $authors                   = [];
                        $authors['authorName']     = $authorList['author'][$author['author']]['title'];
                        $authors['authorUrl']      = $authorList['author'][$author['author']]['url'];
                        $authors['authorThumbUrl'] = $authorList['author'][$author['author']]['thumbUrl'];
                        $authors['authorRole']     = $authorList['role'][$author['role']]['title'];
                        $story['authors'][]        = $authors;
                    }
                }
            }
        }

        if (isset($option['getUser']) && $option['getUser']) {
            $user          = Pi::user()->get(
                $story['uid'], [
                    'id', 'identity', 'name', 'email',
                ]
            );
            $story['user'] = $user['name'];
        }

        if ($story['main_image']) {
            $linkUrl            = Pi::api('doc', 'media')->getSingleLinkUrl($story['main_image'])->setConfigModule('news');
            $story['largeUrl']  = Pi::url((string)$linkUrl->thumb('large'));
            $story['mediumUrl'] = Pi::url((string)$linkUrl->thumb('medium'));
            $story['thumbUrl']  = Pi::url((string)$linkUrl->thumb('thumbnail'));
        } else {
            $story['largeUrl']  = '';
            $story['mediumUrl'] = '';
            $story['thumbUrl']  = '';
        }

        // return story
        return $story;
    }

    public function canonizeStoryLight($story, $option = [])
    {
        // Check
        if (empty($story)) {
            return '';
        }
        // Get config
        $config = Pi::service('registry')->config->read('news');
        // Set option
        $option['authorSet'] = isset($option['authorSet']) ? $option['authorSet'] : true;
        $option['imagePath'] = isset($option['imagePath']) ? $option['imagePath'] : $config['image_path'];
        // object to array
        $story = $story->toArray();
        // Set times
        $story['time_publish_view']   = _date($story['time_publish']);
        $story['time_publish_update'] = _date($story['time_update']);
        // Set story url
        $story['storyUrl'] = Pi::url(
            Pi::service('url')->assemble(
                'news', [
                    'module'     => 'news',
                    'controller' => 'story',
                    'slug'       => $story['slug'],
                ]
            )
        );

        if ($story['main_image']) {
            $story['largeUrl']  = Pi::url((string)Pi::api('doc', 'media')->getSingleLinkUrl($story['main_image'])->setConfigModule('news')->thumb('large'));
            $story['mediumUrl'] = Pi::url((string)Pi::api('doc', 'media')->getSingleLinkUrl($story['main_image'])->setConfigModule('news')->thumb('medium'));
            $story['thumbUrl']  = Pi::url((string)Pi::api('doc', 'media')->getSingleLinkUrl($story['main_image'])->setConfigModule('news')->thumb('thumbnail'));
        } else {
            $story['largeUrl']  = '';
            $story['mediumUrl'] = '';
            $story['thumbUrl']  = '';
        }

        // unset
        unset($story['text_summary']);
        unset($story['text_description']);
        unset($story['time_create']);
        unset($story['topic']);
        // return story
        return $story;
    }

    public function canonizeStoryJson($story, $option = [])
    {
        // Check
        if (empty($story)) {
            return '';
        }

        // Set allow fields
        $allowFields = ['id' => true, 'title' => true, 'subtitle' => true, 'slug' => true, 'time' => true, 'topic' => true, 'url' => true, 'image' => true, 'body' => true, 'type' => true, 'status' => true];
        if (isset($option['fields']) && !empty($option['fields']) && is_array($option['fields'])) {
            foreach ($allowFields as $key => $value) {
                if (!in_array($key, $option['fields'])) {
                    unset($allowFields[$key]);
                }
            }
        }

        // object to array
        $story = $story->toArray();

        // Set return array
        $storyJson = [
            'id'    => $story['id'],
            'title' => $story['title'],
        ];

        // Set time
        if (isset($allowFields['time'])) {
            $storyJson['time_publish']      = $story['time_publish'];
            $storyJson['time_publish_view'] = _date($story['time_publish']);
            $storyJson['time_update']       = $story['time_update'];
            $storyJson['time_update_view']  = _date($story['time_update']);
        }

        // Set time
        if (isset($allowFields['subtitle'])) {
            $storyJson['subtitle'] = $story['subtitle'];
        }

        // Set slug
        if (isset($allowFields['slug'])) {
            $storyJson['slug'] = $story['slug'];
        }

        // Set topic
        if (isset($allowFields['topic'])) {
            $storyJson['topic'] = $story['topic_main'];
        }

        // Set type
        if (isset($allowFields['type'])) {
            $storyJson['type'] = $story['type'];
        }

        // Set status
        if (isset($allowFields['status'])) {
            $storyJson['status'] = $story['status'];
        }

        // Set story url
        if (isset($allowFields['url'])) {
            $storyJson['storyUrl'] = Pi::url(
                Pi::service('url')->assemble(
                    'news', [
                        'module'     => 'news',
                        'controller' => 'story',
                        'slug'       => $story['slug'],
                    ]
                )
            );
        }

        // Set body
        if (isset($allowFields['body'])) {

            $storyJson['text_summary'] = Pi::service('markup')->render($story['text_summary'], 'html', 'html');
            $storyJson['text_summary'] = strip_tags($storyJson['text_summary'], "<b><strong><i><p><br><ul><li><ol><h1><h2><h3><h4><h5><h6>");
            $storyJson['text_summary'] = str_replace("<p>&nbsp;</p>", "", $storyJson['text_summary']);
            $storyJson['text_summary'] = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $storyJson['text_summary']);

            $storyJson['text_description'] = Pi::service('markup')->render($story['text_description'], 'html', 'html');
            $storyJson['text_description'] = strip_tags($storyJson['text_description'], "<b><strong><i><p><br><ul><li><ol><h1><h2><h3><h4><h5><h6>");
            $storyJson['text_description'] = str_replace("<p>&nbsp;</p>", "", $storyJson['text_description']);
            $storyJson['text_description'] = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $storyJson['text_description']);
        }

        // Set image
        if (isset($allowFields['image'])) {
            if ($story['main_image']) {
                $storyJson['largeUrl']  = Pi::url(
                    (string)Pi::api('doc', 'media')->getSingleLinkUrl($story['main_image'])->setConfigModule('news')->thumb('large')
                );
                $storyJson['mediumUrl'] = Pi::url(
                    (string)Pi::api('doc', 'media')->getSingleLinkUrl($story['main_image'])->setConfigModule('news')->thumb('medium')
                );
                $storyJson['thumbUrl']  = Pi::url(
                    (string)Pi::api('doc', 'media')->getSingleLinkUrl($story['main_image'])->setConfigModule('news')->thumb('thumbnail')
                );
            } else {
                $storyJson['largeUrl']  = '';
                $storyJson['mediumUrl'] = '';
                $storyJson['thumbUrl']  = '';
            }
        }

        // return item
        return $storyJson;
    }

    public function sitemap()
    {
        if (Pi::service('module')->isActive('sitemap')) {
            // Remove old links
            Pi::api('sitemap', 'sitemap')->removeAll('news', 'story');
            // find and import
            $columns = ['id', 'slug', 'status'];
            $where   = ['type' => [
                'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download',
            ]];
            $select  = Pi::model('story', 'news')->select()->columns($columns)->where($where);
            $rowset  = Pi::model('story', 'news')->selectWith($select);
            foreach ($rowset as $row) {
                // Make url
                $loc = Pi::url(
                    Pi::service('url')->assemble(
                        'news', [
                            'module'     => 'news',
                            'controller' => 'story',
                            'slug'       => $row->slug,
                        ]
                    )
                );
                // Add to sitemap
                Pi::api('sitemap', 'sitemap')->groupLink($loc, $row->status, 'news', 'story', $row->id);
            }
        }
    }

    public function regenerateImage()
    {
        // Get config
        $config = Pi::service('registry')->config->read('news');
        // Set info
        $columns = ['id', 'image', 'path'];
        $where   = ['type' => [
            'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download',
        ]];
        $order   = ['id ASC'];
        $select  = Pi::model('story', 'news')->select()->columns($columns)->where($where)->order($order);
        $rowset  = Pi::model('story', 'news')->selectWith($select);
        foreach ($rowset as $row) {
            if (!empty($row->image) && !empty($row->path)) {
                // Set image original path
                $original = Pi::path(
                    sprintf(
                        'upload/%s/original/%s/%s',
                        $config['image_path'],
                        $row->path,
                        $row->image
                    )
                );
                // Set image large path
                $images['large'] = Pi::path(
                    sprintf(
                        'upload/%s/large/%s/%s',
                        $config['image_path'],
                        $row->path,
                        $row->image
                    )
                );
                // Set image medium path
                $images['medium'] = Pi::path(
                    sprintf(
                        'upload/%s/medium/%s/%s',
                        $config['image_path'],
                        $row->path,
                        $row->image
                    )
                );
                // Set image thumb path
                $images['thumb'] = Pi::path(
                    sprintf(
                        'upload/%s/thumb/%s/%s',
                        $config['image_path'],
                        $row->path,
                        $row->image
                    )
                );
                // Check original exist of not
                if (file_exists($original)) {
                    // Remove old images
                    foreach ($images as $image) {
                        if (file_exists($image)) {
                            Pi::service('file')->remove($image);
                        }
                    }
                    // regenerate
                    Pi::api('image', 'news')->process($row->image, $row->path);
                }
            }
        }
    }

    public function migrateMedia()
    {
        if (Pi::service("module")->isActive("media")) {

            $msg = '';

            // Get config
            $config = Pi::service('registry')->config->read('news');

            $storyModel = Pi::model("story", 'news');

            $select          = $storyModel->select();
            $storyCollection = $storyModel->selectWith($select);

            foreach ($storyCollection as $story) {

                $toSave = false;

                $mediaData = [
                    'active'       => 1,
                    'time_created' => time(),
                    'uid'          => $story->uid,
                    'count'        => 0,
                ];

                /**
                 * Check if media item have already migrate or no image to migrate
                 */
                if (!$story->main_image) {

                    /**
                     * Check if media item exists
                     */
                    if (empty($story["image"]) || empty($story["path"])) {

                        $draft = $story->status == 3 ? ' (' . __('Draft') . ')' : '';

                        $msg .= __("Missing image or path value from db for Story ID") . " " . $story->id . $draft . "<br>";
                    } else {
                        $imagePath = sprintf(
                            "upload/%s/original/%s/%s",
                            $config["image_path"],
                            $story["path"],
                            $story["image"]
                        );

                        $mediaData['title'] = $story->title;
                        $mediaId            = Pi::api('doc', 'media')->insertMedia($mediaData, $imagePath);

                        if ($mediaId) {
                            $story->main_image = $mediaId;
                            $toSave            = true;
                        }
                    }
                }

                if (!$story->additional_images) {
                    $additionalImagesArray = [];

                    $attachList = Pi::api('attach', $this->module)->attachList($story->id);

                    foreach ($attachList as $type => $list) {
                        foreach ($list as $file) {
                            if (empty($file["file"]) || empty($file["path"])) {
                                $msg .= __("Missing file or path value from db for attachment ID") . " " . $file->id . "<br>";
                            } else {
                                $attachPath = sprintf(
                                    'upload/%s/original/%s/%s',
                                    $config['image_path'],
                                    $file['path'],
                                    $file['file']
                                );

                                $mediaData['title'] = $file['title'];
                                $mediaData['count'] = $file['hits'];

                                $mediaId = Pi::api('doc', 'media')->insertMedia($mediaData, $attachPath);

                                if ($mediaId) {
                                    $additionalImagesArray[] = $mediaId;
                                }
                            }
                        }
                    }

                    if ($additionalImagesArray) {
                        $story->additional_images = implode(',', $additionalImagesArray);
                        $toSave                   = true;
                    }
                }

                if ($toSave) {
                    $story->save();
                }
            }

            return $msg;
        }

        return false;
    }
}
