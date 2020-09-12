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
use Imagine\Image\Box;
use Imagine\Image\Point;

/*
 * Pi::api('image', 'news')->rename($image, $prefix, $path, $imagePath);
 * Pi::api('image', 'news')->process($image, $path, $imagePath);
 */

class Image extends AbstractApi
{
    public function __construct()
    {
        $this->module = Pi::service('module')->current();
    }

    protected $_convertTable
        = [
            '&amp;' => 'and', '@' => 'at', '©' => 'c', '®' => 'r', 'À' => 'a',
            'Á'     => 'a', 'Â' => 'a', 'Ä' => 'a', 'Å' => 'a', 'Æ' => 'ae', 'Ç' => 'c',
            'È'     => 'e', 'É' => 'e', 'Ë' => 'e', 'Ì' => 'i', 'Í' => 'i', 'Î' => 'i',
            'Ï'     => 'i', 'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Õ' => 'o', 'Ö' => 'o',
            'Ø'     => 'o', 'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'Ý' => 'y',
            'ß'     => 'ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', 'å' => 'a',
            'æ'     => 'ae', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì'     => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ò' => 'o', 'ó' => 'o',
            'ô'     => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u',
            'û'     => 'u', 'ü' => 'u', 'ý' => 'y', 'þ' => 'p', 'ÿ' => 'y', 'Ā' => 'a',
            'ā'     => 'a', 'Ă' => 'a', 'ă' => 'a', 'Ą' => 'a', 'ą' => 'a', 'Ć' => 'c',
            'ć'     => 'c', 'Ĉ' => 'c', 'ĉ' => 'c', 'Ċ' => 'c', 'ċ' => 'c', 'Č' => 'c',
            'č'     => 'c', 'Ď' => 'd', 'ď' => 'd', 'Đ' => 'd', 'đ' => 'd', 'Ē' => 'e',
            'ē'     => 'e', 'Ĕ' => 'e', 'ĕ' => 'e', 'Ė' => 'e', 'ė' => 'e', 'Ę' => 'e',
            'ę'     => 'e', 'Ě' => 'e', 'ě' => 'e', 'Ĝ' => 'g', 'ĝ' => 'g', 'Ğ' => 'g',
            'ğ'     => 'g', 'Ġ' => 'g', 'ġ' => 'g', 'Ģ' => 'g', 'ģ' => 'g', 'Ĥ' => 'h',
            'ĥ'     => 'h', 'Ħ' => 'h', 'ħ' => 'h', 'Ĩ' => 'i', 'ĩ' => 'i', 'Ī' => 'i',
            'ī'     => 'i', 'Ĭ' => 'i', 'ĭ' => 'i', 'Į' => 'i', 'į' => 'i', 'İ' => 'i',
            'ı'     => 'i', 'Ĳ' => 'ij', 'ĳ' => 'ij', 'Ĵ' => 'j', 'ĵ' => 'j', 'Ķ' => 'k',
            'ķ'     => 'k', 'ĸ' => 'k', 'Ĺ' => 'l', 'ĺ' => 'l', 'Ļ' => 'l', 'ļ' => 'l',
            'Ľ'     => 'l', 'ľ' => 'l', 'Ŀ' => 'l', 'ŀ' => 'l', 'Ł' => 'l', 'ł' => 'l',
            'Ń'     => 'n', 'ń' => 'n', 'Ņ' => 'n', 'ņ' => 'n', 'Ň' => 'n', 'ň' => 'n',
            'ŉ'     => 'n', 'Ŋ' => 'n', 'ŋ' => 'n', 'Ō' => 'o', 'ō' => 'o', 'Ŏ' => 'o',
            'ŏ'     => 'o', 'Ő' => 'o', 'ő' => 'o', 'Œ' => 'oe', 'œ' => 'oe', 'Ŕ' => 'r',
            'ŕ'     => 'r', 'Ŗ' => 'r', 'ŗ' => 'r', 'Ř' => 'r', 'ř' => 'r', 'Ś' => 's',
            'ś'     => 's', 'Ŝ' => 's', 'ŝ' => 's', 'Ş' => 's', 'ş' => 's', 'Š' => 's',
            'š'     => 's', 'Ţ' => 't', 'ţ' => 't', 'Ť' => 't', 'ť' => 't', 'Ŧ' => 't',
            'ŧ'     => 't', 'Ũ' => 'u', 'ũ' => 'u', 'Ū' => 'u', 'ū' => 'u', 'Ŭ' => 'u',
            'ŭ'     => 'u', 'Ů' => 'u', 'ů' => 'u', 'Ű' => 'u', 'ű' => 'u', 'Ų' => 'u',
            'ų'     => 'u', 'Ŵ' => 'w', 'ŵ' => 'w', 'Ŷ' => 'y', 'ŷ' => 'y', 'Ÿ' => 'y',
            'Ź'     => 'z', 'ź' => 'z', 'Ż' => 'z', 'ż' => 'z', 'Ž' => 'z', 'ž' => 'z',
            'ſ'     => 'z', 'Ə' => 'e', 'ƒ' => 'f', 'Ơ' => 'o', 'ơ' => 'o', 'Ư' => 'u',
            'ư'     => 'u', 'Ǎ' => 'a', 'ǎ' => 'a', 'Ǐ' => 'i', 'ǐ' => 'i', 'Ǒ' => 'o',
            'ǒ'     => 'o', 'Ǔ' => 'u', 'ǔ' => 'u', 'Ǖ' => 'u', 'ǖ' => 'u', 'Ǘ' => 'u',
            'ǘ'     => 'u', 'Ǚ' => 'u', 'ǚ' => 'u', 'Ǜ' => 'u', 'ǜ' => 'u', 'Ǻ' => 'a',
            'ǻ'     => 'a', 'Ǽ' => 'ae', 'ǽ' => 'ae', 'Ǿ' => 'o', 'ǿ' => 'o', 'ə' => 'e',
            'Ё'     => 'jo', 'Є' => 'e', 'І' => 'i', 'Ї' => 'i', 'А' => 'a', 'Б' => 'b',
            'В'     => 'v', 'Г' => 'g', 'Д' => 'd', 'Е' => 'e', 'Ж' => 'zh', 'З' => 'z',
            'И'     => 'i', 'Й' => 'j', 'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n',
            'О'     => 'o', 'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't', 'У' => 'u',
            'Ф'     => 'f', 'Х' => 'h', 'Ц' => 'c', 'Ч' => 'ch', 'Ш' => 'sh', 'Щ' => 'sch',
            'Ъ'     => '-', 'Ы' => 'y', 'Ь' => '-', 'Э' => 'je', 'Ю' => 'ju', 'Я' => 'ja',
            'а'     => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ж'     => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l',
            'м'     => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
            'т'     => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш'     => 'sh', 'щ' => 'sch', 'ъ' => '-', 'ы' => 'y', 'ь' => '-', 'э' => 'je',
            'ю'     => 'ju', 'я' => 'ja', 'ё' => 'jo', 'є' => 'e', 'і' => 'i', 'ї' => 'i',
            'Ґ'     => 'g', 'ґ' => 'g', 'א' => 'a', 'ב' => 'b', 'ג' => 'g', 'ד' => 'd',
            'ה'     => 'h', 'ו' => 'v', 'ז' => 'z', 'ח' => 'h', 'ט' => 't', 'י' => 'i',
            'ך'     => 'k', 'כ' => 'k', 'ל' => 'l', 'ם' => 'm', 'מ' => 'm', 'ן' => 'n',
            'נ'     => 'n', 'ס' => 's', 'ע' => 'e', 'ף' => 'p', 'פ' => 'p', 'ץ' => 'C',
            'צ'     => 'c', 'ק' => 'q', 'ר' => 'r', 'ש' => 'w', 'ת' => 't', '™' => 'tm',
        ];

    public function rename($image = '', $prefix = 'image_', $path = '', $imagePath = null)
    {
        // Get config
        $config = Pi::service('registry')->config->read('news', 'image');
        // Set image path
        $imagePath = empty($imagePath) ? $config['image_path'] : $imagePath;
        // Check image name
        if (empty($image)) {
            return $prefix . '%random%';
        }
        // Separating image name and extension
        $name      = pathinfo($image, PATHINFO_FILENAME);
        $extension = pathinfo($image, PATHINFO_EXTENSION);
        $extension = strtolower($extension);
        // strip name
        $name = _strip($name);
        $name = strtolower(trim($name));
        $name = strtr($name, $this->_convertTable);
        $name = preg_replace("/[^a-zA-Z0-9 ]+/", "", $name);
        $name = array_filter(explode(' ', $name));
        $name = implode('-', $name) . '.' . $extension;
        // Check text length
        if (mb_strlen($name, 'UTF-8') < 8) {
            $name = $prefix . '%random%';
        }
        // Set original path
        $original = Pi::path(
            sprintf('upload/%s/original/%s/%s', $imagePath, $path, $name)
        );
        // Check file exist
        if (Pi::service('file')->exists($original)) {
            return $prefix . '%random%';
        }
        // return
        return $name;
    }

    public function process($image, $path, $imagePath = null, $cropping = null)
    {
        // Get config
        $config = Pi::service('registry')->config->read('news', 'image');
        // Set image path
        $imagePath = empty($imagePath) ? $config['image_path'] : $imagePath;
        // Set original path
        $original = Pi::path(
            sprintf('upload/%s/original/%s/%s', $imagePath, $path, $image)
        );

        // Set large path
        $large = Pi::path(
            sprintf('upload/%s/large/%s/%s', $imagePath, $path, $image)
        );

        // Set medium path
        $medium = Pi::path(
            sprintf('upload/%s/medium/%s/%s', $imagePath, $path, $image)
        );

        // Set thumb path
        $thumb = Pi::path(
            sprintf('upload/%s/thumb/%s/%s', $imagePath, $path, $image)
        );

        // Set options
        $options = [
            'quality' => empty($config['image_quality']) ? 75 : $config['image_quality'],
        ];

        $originalForThumbProcessing = $original;

        $config        = Pi::service('registry')->config->read('news');
        $isCropEnabled = isset($config['image_crop']) && $config['image_crop'] == 1;

        // Get image size
        $size = getimagesize($originalForThumbProcessing);

        if ($cropping && $isCropEnabled) {
            $croppingData = json_decode($cropping);

            if (!empty($croppingData)) {
                $croppingData = (object)$croppingData;

                $imagine                    = new \Imagine\Gd\Imagine();
                $originalForThumbProcessing = $imagine->open($original);
                $originalForThumbProcessing->crop(new Point($croppingData->x, $croppingData->y), new Box($croppingData->width, $croppingData->height));
            }
        }

        // Resize to large
        if (!empty($croppingData) || ($size[0] > $config['image_largew'] && $size[1] > $config['image_largeh'])) {
            Pi::service('image')->resize(
                $originalForThumbProcessing,
                [$config['image_largew'], $config['image_largeh'], true],
                $large,
                '',
                $options
            );
        } else {
            Pi::service('file')->copy($original, $large, true);
        }

        // Resize to medium
        if (!empty($croppingData) || ($size[0] > $config['image_mediumw'] && $size[1] > $config['image_mediumh'])) {
            Pi::service('image')->resize(
                $originalForThumbProcessing,
                [$config['image_mediumw'], $config['image_mediumh'], true],
                $medium,
                '',
                $options
            );
        } else {
            Pi::service('file')->copy($original, $medium, true);
        }

        // Resize to thumb
        if (!empty($croppingData) || ($size[0] > $config['image_thumbw'] && $size[1] > $config['image_thumbh'])) {
            Pi::service('image')->resize(
                $originalForThumbProcessing,
                [$config['image_thumbw'], $config['image_thumbh'], true],
                $thumb,
                '',
                $options
            );
        } else {
            Pi::service('file')->copy($original, $thumb, true);
        }

        // Watermark
        if ($config['image_watermark']) {
            // Set watermark image
            $watermarkImage = (empty($config['image_watermark_source'])) ? '' : Pi::path($config['image_watermark_source']);
            if (empty($watermarkImage) || !file_exists($watermarkImage)) {
                $logoFile       = Pi::service('asset')->logo();
                $watermarkImage = Pi::path($logoFile);
            }

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
