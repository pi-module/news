<?php

namespace Module\News\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;

class CronController extends ActionController
{
    /**
     * Generate all media files Main image only
     * @return array
     */
    public function generatePicturesAction()
    {
        Pi::service('log')->mute();

        $nbPicturesToGenerate = 0;
        $sizes = array('thumbnail', 'medium', 'item', 'large');

        $storyCollection = Pi::model('story', 'news')->select(array());

        foreach($storyCollection as $storyEntity){
            foreach($sizes as $size){
                $mainImage = (string) Pi::api('doc','media')->getSingleLinkUrl($storyEntity['main_image'])->setConfigModule('news')->thumb($size);
                if($mainImage){
                    $nbPicturesToGenerate++;
                }

                foreach(explode(',', $storyEntity['additional_images']) as $mediaId){
                    $image = (string) Pi::api('doc','media')->getSingleLinkUrl($mediaId)->setConfigModule('news')->thumb($size);
                    if($image){
                        $nbPicturesToGenerate++;
                    }
                }
            }
        }

        echo sprintf(__('%s picture(s) has been generated or already exists'), $nbPicturesToGenerate);
        echo "\n";
        exit;
    }
}