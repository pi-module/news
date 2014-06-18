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

/*
 * Pi::api('image', 'news')->process($image, $path, $part);
 */

class Image extends AbstractApi
{  

	public function process($image, $path, $part = 'story')
	{
        $config = Pi::service('registry')->config->read($this->getModule(), 'image');
        
        // Set original path
        $original = Pi::path(
        	sprintf('upload/%s/original/%s/%s', $config['image_path'], $path, $image)
        );
        
        // Set large path
        $large = Pi::path(
        	sprintf('upload/%s/large/%s/%s', $config['image_path'], $path, $image)
        );

        // Set medium path
        $medium = Pi::path(
        	sprintf('upload/%s/medium/%s/%s', $config['image_path'], $path, $image)
        );

        // Set thumb path
        $thumb = Pi::path(
        	sprintf('upload/%s/thumb/%s/%s', $config['image_path'], $path, $image)
        );

        switch ($part) {
            case 'story':
                $sizeLarge = array($config['image_largew'], $config['image_largeh']);
                $sizeMedium = array($config['image_mediumw'], $config['image_mediumh']);
                $sizeThumb = array($config['image_thumbw'], $config['image_thumbh']);
                break;

            case 'topic':
                $sizeLarge = array($config['image_topic_largew'], $config['image_topic_largeh']);
                $sizeMedium = array($config['image_topic_mediumw'], $config['image_topic_mediumh']);
                $sizeThumb = array($config['image_topic_thumbw'], $config['image_topic_thumbh']);
                break;

            case 'author':
                $sizeLarge = array($config['image_author_largew'], $config['image_author_largeh']);
                $sizeMedium = array($config['image_author_mediumw'], $config['image_author_mediumh']);
                $sizeThumb = array($config['image_author_thumbw'], $config['image_author_thumbh']);
                break;
        }

        // Resize to large
        Pi::service('image')->resize(
            $original, 
            $sizeLarge, 
            $large
        );

        // Resize to medium
        Pi::service('image')->resize(
            $original, 
            $sizeMedium, 
            $medium
        );

        // Resize to thumb
        Pi::service('image')->resize(
            $original, 
            $sizeThumb, 
            $thumb
        );

        // Watermark
        if ($config['image_watermark']) {
        	// Set watermark image
        	$watermarkImage = (empty($config['image_watermark_source'])) ? '' : Pi::path($config['image_watermark_source']);
        	$watermarkImage = (file_exists($watermarkImage)) ? $watermarkImage : '';
        	
            // Watermark large
        	Pi::service('image')->watermark(
        		$large,
                '',
        		$watermarkImage,
        		$config['image_watermark_position']
            );

            // Watermark medium
        	Pi::service('image')->watermark(
        		$medium,
                '',
        		$watermarkImage,
        		$config['image_watermark_position']
            );

            // Watermark thumb
        	Pi::service('image')->watermark(
        		$thumb,
                '',
        		$watermarkImage,
        		$config['image_watermark_position']
            );
        }
	}
}	