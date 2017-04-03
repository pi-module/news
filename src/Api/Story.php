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
namespace Module\News\Api;

use Pi;
use Pi\Application\Api\AbstractApi;
use Zend\Db\Sql\Predicate\Expression;

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
    public function getStory($parameter, $field = 'id', $option = array())
    {
        // Get product
        $story = Pi::model('story', $this->getModule())->find($parameter, $field);
        $story = $this->canonizeStory($story, '', '', $option);
        return $story;
    }

    public function getStoryLight($parameter, $field = 'id', $option = array())
    {
        // Get product
        $story = Pi::model('story', $this->getModule())->find($parameter, $field);
        $story = $this->canonizeStoryLight($story, $option);
        return $story;
    }

    public function getStoryJson($parameter, $field = 'id', $option = array())
    {
        // Get product
        $story = Pi::model('story', $this->getModule())->find($parameter, $field);
        $story = $this->canonizeStoryJson($story, $option);
        return $story;
    }

    /**
     * Set number of attach files for selected story
     */
    public function AttachCount($id)
    {
        // set info
        $where = array('item_id' => $id, 'item_table' => 'story');
        $columns = array('count' => new Expression('count(*)'));
        // Get attach count
        $select = Pi::model('attach', $this->getModule())->select()->columns($columns)->where($where);
        $count = Pi::model('attach', $this->getModule())->selectWith($select)->current()->count;
        // Set attach count
        Pi::model('story', $this->getModule())->update(array('attach' => $count), array('id' => $id));
    }

    /**
     * Get list of attach files
     */
    public function AttachList($id)
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Set info
        $where = array('item_id' => $id, 'item_table' => 'story', 'status' => 1);
        $order = array('time_create DESC', 'id DESC');
        // Get all attach files
        $select = Pi::model('attach', $this->getModule())->select()->where($where)->order($order);
        $rowset = Pi::model('attach', $this->getModule())->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $file[$row->type][$row->id] = $row->toArray();
            $file[$row->type][$row->id]['time_create'] = _date($file[$row->type][$row->id]['time_create']);
            // Set file link
            if ($file[$row->type][$row->id]['type'] == 'image') {
                $file[$row->type][$row->id]['largeUrl'] = Pi::url(
                    sprintf('upload/%s/large/%s/%s',
                        $config['image_path'],
                        $file[$row->type][$row->id]['path'],
                        $file[$row->type][$row->id]['file']
                    ));
                $file[$row->type][$row->id]['mediumUrl'] = Pi::url(
                    sprintf('upload/%s/medium/%s/%s',
                        $config['image_path'],
                        $file[$row->type][$row->id]['path'],
                        $file[$row->type][$row->id]['file']
                    ));
                $file[$row->type][$row->id]['thumbUrl'] = Pi::url(
                    sprintf('upload/%s/thumb/%s/%s',
                        $config['image_path'],
                        $file[$row->type][$row->id]['path'],
                        $file[$row->type][$row->id]['file']
                    ));
            } elseif ($file[$row->type][$row->id]['type'] == 'other') {
                $file[$row->type][$row->id]['fileUrl'] = Pi::url(
                    sprintf('upload/%s/%s/%s/%s',
                        $config['file_path'],
                        'file',
                        $file[$row->type][$row->id]['path'],
                        $file[$row->type][$row->id]['file']
                    ));
            } else {
                $file[$row->type][$row->id]['fileUrl'] = Pi::url(
                    sprintf('upload/%s/%s/%s/%s',
                        $config['file_path'],
                        $file[$row->type][$row->id]['type'],
                        $file[$row->type][$row->id]['path'],
                        $file[$row->type][$row->id]['file']
                    ));
            }
            // Set download url
            $file[$row->type][$row->id]['downloadUrl'] = Pi::url(Pi::service('url')->assemble('news', array(
                'module' => $this->getModule(),
                'controller' => 'media',
                'action' => 'download',
                'id' => $row->id,
            )));
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
        $where = array('story' => $id);
        $columns = array('count' => new Expression('count(*)'));
        // Get attribute count
        $select = Pi::model('field_data', $this->getModule())->select()->columns($columns)->where($where);
        $count = Pi::model('field_data', $this->getModule())->selectWith($select)->current()->count;
        // Set attribute count
        Pi::model('story', $this->getModule())->update(array('attribute' => $count), array('id' => $id));
    }

    /**
     * Get related stores
     */
    public function Related($id, $topic)
    {
        // Set info
        $config = Pi::service('registry')->config->read($this->getModule());
        $related = array();
        $order = array('time_publish DESC', 'id DESC');
        $where = array(
            'status' => 1,
            'story != ?' => $id,
            'time_publish <= ?' => time(),
            'topic' => $topic,
            'type' => array(
                'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download'
            ),
        );
        $columns = array('story' => new Expression('DISTINCT story'));
        $limit = intval($config['related_num']);
        // Get info from link table
        $select = Pi::model('link', $this->getModule())->select()->where($where)->columns($columns)->order($order)->limit($limit);
        $rowset = Pi::model('link', $this->getModule())->selectWith($select)->toArray();
        // Make list
        foreach ($rowset as $id) {
            $storyId[] = $id['story'];
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
        $link = array();
        $columns = array('story');
        // Select next
        $where = array(
            'status' => 1,
            'story > ?' => $id,
            'time_publish <= ?' => time(),
            'topic' => $topic,
            'type' => array(
                'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download'
            ),
        );
        $select = Pi::model('link', $this->getModule())->select()->columns($columns)->where($where)->order(array('id ASC'))->limit(1);
        $row = Pi::model('link', $this->getModule())->selectWith($select)->current();
        if (!empty($row)) {
            $row = $row->toArray();
            $story = Pi::model('story', $this->getModule())->find($row['story'])->toArray();
            $link['next']['title'] = $story['title'];
            $link['next']['url'] = Pi::url(Pi::service('url')->assemble('news', array(
                'module' => $this->getModule(),
                'controller' => 'story',
                'slug' => $story['slug'],
            )));
        }
        // Select Prev
        $where = array(
            'status' => 1,
            'story <  ?' => $id,
            'time_publish <= ?' => time(),
            'topic' => $topic,
            'type' => array(
                'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download'
            )
        );
        $select = Pi::model('link', $this->getModule())->select()->columns($columns)->where($where)->order(array('id ASC'))->limit(1);
        $row = Pi::model('link', $this->getModule())->selectWith($select)->current();
        if (!empty($row)) {
            $row = $row->toArray();
            $story = Pi::model('story', $this->getModule())->find($row['story'])->toArray();
            $link['previous']['title'] = $story['title'];
            $link['previous']['url'] = Pi::url(Pi::service('url')->assemble('news', array(
                'module' => $this->getModule(),
                'controller' => 'story',
                'slug' => $story['slug'],
            )));
        }
        return $link;
    }

    public function getListFromId($id)
    {
        $list = array();
        $where = array('id' => $id, 'status' => 1);
        $select = Pi::model('story', $this->getModule())->select()->where($where);
        $rowset = Pi::model('story', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $list[$row->id] = $this->canonizeStory($row);
        }
        return $list;
    }

    public function getListFromIdLight($id)
    {
        $list = array();
        $where = array('id' => $id, 'status' => 1);
        $order = array('time_publish DESC', 'id DESC');
        $select = Pi::model('story', $this->getModule())->select()->where($where)->order($order);
        $rowset = Pi::model('story', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $list[$row->id] = $this->canonizeStoryLight($row);
        }
        return $list;
    }

    public function favoriteList()
    {
        // Get user id
        $uid = Pi::user()->getId();
        // Check user
        if ($uid > 0) {
            $favoriteIds = Pi::api('favourite', 'favourite')->userFavourite($uid, $this->getModule(), 10);
            // Check list of ides
            if (!empty($favoriteIds)) {
                // Get config
                $config = Pi::service('registry')->config->read($this->getModule());
                // Set list
                $list = array();
                $where = array('id' => $favoriteIds, 'status' => 1);
                $select = Pi::model('story', $this->getModule())->select()->where($where);
                $rowset = Pi::model('story', $this->getModule())->selectWith($select);
                foreach ($rowset as $row) {
                    $story = array();
                    $story['title'] = $row->title;
                    $story['url'] = Pi::url(Pi::service('url')->assemble('news', array(
                        'module' => $this->getModule(),
                        'controller' => 'story',
                        'slug' => $row->slug,
                    )));
                    $story['image'] = '';
                    if ($row->image) {
                        $story['image'] = Pi::url(
                            sprintf('upload/%s/thumb/%s/%s',
                                $config['image_path'],
                                $row->path,
                                $row->image
                            ));
                    }
                    $list[$row->id] = $story;
                }
                return $list;
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    public function canonizeStory($story, $topicList = array(), $authorList = array(), $option = array())
    {
        // Check
        if (empty($story)) {
            return '';
        }
        // Get config
        if(!isset($this->config)){
            $this->config = Pi::service('registry')->config->read($this->getModule());
        }

        // Set option
        $option['authorSet'] = isset($option['authorSet']) ? $option['authorSet'] : true;
        $option['imagePath'] = isset($option['imagePath']) ? $option['imagePath'] : $this->config['image_path'];
        // boject to array
        $story = $story->toArray();
        // Set text_summary

        if(!isset($this->markupService)){
            $this->markupService = Pi::service('markup');
        }

        $story['text_summary'] = $this->markupService->render($story['text_summary'], 'html', 'html');
        // Set text_description
        $story['text_description'] = $this->markupService->render($story['text_description'], 'html', 'html');
        // Set times
        $story['time_create_view'] = _date($story['time_create']);
        $story['time_publish_view'] = _date($story['time_publish']);
        $story['time_update_view'] = _date($story['time_update']);
        // Set story url
        $story['storyUrl'] = Pi::url(Pi::service('url')->assemble('news', array(
            'module' => $this->getModule(),
            'controller' => 'story',
            'slug' => $story['slug'],
        )));
        // Set topic information
        $story['topic'] = json_decode($story['topic'], true);
        // Get topic list
        if (!empty($story['topic'])) {
            $topicList = (empty($topicList)) ? Pi::registry('topicList', 'news')->read() : $topicList;
            foreach ($story['topic'] as $topic) {
                if (!empty($topicList[$topic]['title'])) {
                    $story['topics'][$topic]['title'] = $topicList[$topic]['title'];
                    $story['topics'][$topic]['slug'] = $topicList[$topic]['slug'];
                    $story['topics'][$topic]['url'] = Pi::url(Pi::service('url')->assemble('news', array(
                        'module' => $this->getModule(),
                        'controller' => 'topic',
                        'slug' => $topicList[$topic]['slug'],
                    )));
                }
            }
        }
        // Get author list
        $story['authors'] = array();
        if ($this->config['show_author'] && !empty($authorList) && $option['authorSet']) {
            $story['author'] = json_decode($story['author'], true);
            if (!empty($story['author'])) {
                foreach ($story['author'] as $author) {
                    if (!empty($author['author'])) {
                        $authors = array();
                        $authors['authorName'] = $authorList['author'][$author['author']]['title'];
                        $authors['authorUrl'] = $authorList['author'][$author['author']]['url'];
                        $authors['authorThumbUrl'] = $authorList['author'][$author['author']]['thumbUrl'];
                        $authors['authorRole'] = $authorList['role'][$author['role']]['title'];
                        $story['authors'][] = $authors;
                    }
                }
            }
        }
        // Set image url
        if ($story['image']) {
            // Set image original url
            $story['originalUrl'] = Pi::url(
                sprintf('upload/%s/original/%s/%s',
                    $option['imagePath'],
                    $story['path'],
                    $story['image']
                ));
            // Set image large url
            $story['largeUrl'] = Pi::url(
                sprintf('upload/%s/large/%s/%s',
                    $option['imagePath'],
                    $story['path'],
                    $story['image']
                ));
            // Set image medium url
            $story['mediumUrl'] = Pi::url(
                sprintf('upload/%s/medium/%s/%s',
                    $option['imagePath'],
                    $story['path'],
                    $story['image']
                ));
            // Set image thumb url
            $story['thumbUrl'] = Pi::url(
                sprintf('upload/%s/thumb/%s/%s',
                    $option['imagePath'],
                    $story['path'],
                    $story['image']
                ));
        } elseif ($this->config['image_default']) {
            $story['originalUrl'] = Pi::service('asset')->getModuleAsset('image/news-original.jpg', $this->getModule());
            $story['largeUrl'] = Pi::service('asset')->getModuleAsset('image/news-large.jpg', $this->getModule());
            $story['mediumUrl'] = Pi::service('asset')->getModuleAsset('image/news-medium.jpg', $this->getModule());
            $story['thumbUrl'] = Pi::service('asset')->getModuleAsset('image/news-thumb.jpg', $this->getModule());
        } else {
            $story['originalUrl'] = '';
            $story['largeUrl'] = '';
            $story['mediumUrl'] = '';
            $story['thumbUrl'] = '';
        }
        // return story
        return $story;
    }

    public function canonizeStoryLight($story, $option = array())
    {
        // Check
        if (empty($story)) {
            return '';
        }
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Set option
        $option['authorSet'] = isset($option['authorSet']) ? $option['authorSet'] : true;
        $option['imagePath'] = isset($option['imagePath']) ? $option['imagePath'] : $config['image_path'];
        // boject to array
        $story = $story->toArray();
        // Set times
        $story['time_publish_view'] = _date($story['time_publish']);
        $story['time_publish_update'] = _date($story['time_update']);
        // Set story url
        $story['storyUrl'] = Pi::url(Pi::service('url')->assemble('news', array(
            'module' => $this->getModule(),
            'controller' => 'story',
            'slug' => $story['slug'],
        )));
        // Set image url
        if ($story['image']) {
            // Set image thumb url
            $story['thumbUrl'] = Pi::url(
                sprintf('upload/%s/thumb/%s/%s',
                    $option['imagePath'],
                    $story['path'],
                    $story['image']
                ));
        } elseif ($config['image_default']) {
            $story['thumbUrl'] = Pi::service('asset')->getModuleAsset('image/news-thumb.jpg', $this->getModule());
        } else {
            $story['thumbUrl'] = '';
        }
        // unset
        unset($story['text_summary']);
        unset($story['text_description']);
        unset($story['time_create']);
        unset($story['topic']);
        // return story
        return $story;
    }

    public function canonizeStoryJson($story, $option = array())
    {
        // Check
        if (empty($story)) {
            return '';
        }
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Set option
        $option['authorSet'] = isset($option['authorSet']) ? $option['authorSet'] : true;
        $option['imagePath'] = isset($option['imagePath']) ? $option['imagePath'] : $config['image_path'];
        // boject to array
        $story = $story->toArray();
        // Set story url
        $story['storyUrl'] = Pi::url(Pi::service('url')->assemble('news', array(
            'module' => $this->getModule(),
            'controller' => 'story',
            'slug' => $story['slug'],
        )));
        // Set image url
        if ($story['image']) {
            // Set image large url
            $story['largeUrl'] = Pi::url(
                sprintf('upload/%s/large/%s/%s',
                    $option['imagePath'],
                    $story['path'],
                    $story['image']
                ));
            // Set image medium url
            $story['mediumUrl'] = Pi::url(
                sprintf('upload/%s/medium/%s/%s',
                    $option['imagePath'],
                    $story['path'],
                    $story['image']
                ));
            // Set image thumb url
            $story['thumbUrl'] = Pi::url(
                sprintf('upload/%s/thumb/%s/%s',
                    $option['imagePath'],
                    $story['path'],
                    $story['image']
                ));
        } else {
            $story['largeUrl'] = '';
            $story['mediumUrl'] = '';
            $story['thumbUrl'] = '';
        }
        // Set topic
        //$topic = json_decode($story['topic'], true);
        // Set body
        $body = Pi::service('markup')->render($story['text_summary'] . $story['text_description'], 'html', 'html');
        $body = strip_tags($body,"<b><strong><i><p><br><ul><li><ol><h2><h3><h4>");
        $body = str_replace("<p>&nbsp;</p>", "", $body);
        // Set return array
        $storyJson = array(
            'id' => $story['id'],
            'title' => $story['title'],
            'subtitle' => $story['subtitle'],
            'time_publish' => $story['time_publish'],
            'time_publish_view' => _date($story['time_publish']),
            'time_update' => $story['time_update'],
            'time_update_view' => _date($story['time_update']),
            'thumbUrl' => $story['thumbUrl'],
            'mediumUrl' => $story['mediumUrl'],
            'largeUrl' => $story['largeUrl'],
            'storyUrl' => $story['storyUrl'],
            'topic' => $story['topic_main'],
            'image' => $story['image'],
            'body' => $body,
        );
        // return item
        return $storyJson;
    }

    public function sitemap()
    {
        if (Pi::service('module')->isActive('sitemap')) {
            // Remove old links
            Pi::api('sitemap', 'sitemap')->removeAll($this->getModule(), 'story');
            // find and import
            $columns = array('id', 'slug', 'status');
            $where = array('type' => array(
                    'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download'
            ));
            $select = Pi::model('story', $this->getModule())->select()->columns($columns)->where($where);
            $rowset = Pi::model('story', $this->getModule())->selectWith($select);
            foreach ($rowset as $row) {
                // Make url
                $loc = Pi::url(Pi::service('url')->assemble('news', array(
                    'module' => $this->getModule(),
                    'controller' => 'story',
                    'slug' => $row->slug,
                )));
                // Add to sitemap
                Pi::api('sitemap', 'sitemap')->groupLink($loc, $row->status, $this->getModule(), 'story', $row->id);
            }
        }
    }

    public function regenerateImage()
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Set info
        $columns = array('id', 'image', 'path');
        $where = array('type' => array(
            'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download'
        ));
        $order = array('id ASC');
        $select = Pi::model('story', $this->getModule())->select()->columns($columns)->where($where)->order($order);
        $rowset = Pi::model('story', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            if (!empty($row->image) && !empty($row->path)) {
                // Set image original path
                $original = Pi::path(
                    sprintf('upload/%s/original/%s/%s',
                        $config['image_path'],
                        $row->path,
                        $row->image
                    ));
                // Set image large path
                $images['large'] = Pi::path(
                    sprintf('upload/%s/large/%s/%s',
                        $config['image_path'],
                        $row->path,
                        $row->image
                    ));
                // Set image medium path
                $images['medium'] = Pi::path(
                    sprintf('upload/%s/medium/%s/%s',
                        $config['image_path'],
                        $row->path,
                        $row->image
                    ));
                // Set image thumb path
                $images['thumb'] = Pi::path(
                    sprintf('upload/%s/thumb/%s/%s',
                        $config['image_path'],
                        $row->path,
                        $row->image
                    ));
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

    public function migrateMedia(){
        if (Pi::service("module")->isActive("media")) {
            // Get config
            $config = Pi::service('registry')->config->read($this->getModule());

            $storyModel = Pi::model("story", $this->getModule());

            $select = $storyModel->select();
            $storyCollection = $storyModel->selectWith($select);

            foreach($storyCollection as $story){

                /**
                 * Check if media item have already migrate or no image to migrate
                 */
                if($story->main_image || empty($story["image"]) || empty($story["path"])){
                    continue;
                }

                $mediaData = array(
                    'active' => 1,
                    'time_created' => time(),
                    'uid'   => $story->uid,
                    'count' => 0,
                );

                $imagePath = sprintf("upload/%s/original/%s/%s",
                    $config["image_path"],
                    $story["path"],
                    $story["image"]
                );

                $mediaData['title'] = $story->title;
                $mediaId = Pi::api('doc', 'media')->insertMedia($mediaData, $imagePath);

                if($mediaId){
                    $story->main_image = $mediaId;
                }

                $additionalImagesArray = array();

                $attachList = Pi::api('attach', $this->module)->attachList($story->id);

                foreach($attachList as $type => $list){
                    foreach($list as $file){
                        $attachPath = sprintf('upload/%s/original/%s/%s',
                            $config['image_path'],
                            $file['path'],
                            $file['file']
                        );

                        $mediaData['title'] = $file['title'];
                        $mediaData['count'] = $file['hits'];

                        $mediaId = Pi::api('doc', 'media')->insertMedia($mediaData, $attachPath);

                        $additionalImagesArray[] = $mediaId;
                    }
                }

                if($additionalImagesArray){
                    $story->additional_images = implode(',', $additionalImagesArray);
                }

                $story->save();
            }
        }
    }
}