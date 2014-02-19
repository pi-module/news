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
 * Pi::api('image', 'news')->process($image, $path);
 */

class Image extends AbstractApi
{  

	public function process($image, $path)
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

        // Resize to large
        Pi::service('image')->resize(
        	$original, 
        	array($config['image_largew'], $config['image_largeh']), 
        	$large
        );

        // Resize to medium
        Pi::service('image')->resize(
        	$original, 
        	array($config['image_mediumw'], $config['image_mediumh']), 
        	$medium
        );

        // Resize to thumb
        Pi::service('image')->resize(
        	$original, 
        	array($config['image_thumbw'], $config['image_thumbh']), 
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