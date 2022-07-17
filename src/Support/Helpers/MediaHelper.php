<?php
namespace RA\Core\Support\Helpers;

use Intervention\Image\ImageManagerStatic as InterventionImage;

class MediaHelper
{
    public function getThumbUrl($url, $size = false) {
        if ( !$url ) {
            return false;
        }

        $pathinfo = pathinfo($url);
        $thumb = '_thumb';

        if ( $size == 'original' ) {
            $thumb = '_original';
        }
        else if ( $size == 'resized' ) {
            $thumb = '_resized';
        }
        else if ( $size ) {
            $thumb = '_thumb_'.$size;
        }
        else if ( $size === null ) {
            $thumb = '';
        }

        return config('path.uploads_url').str_replace($pathinfo['filename'].'.'.$pathinfo['extension'], $pathinfo['filename'].$thumb.'.'.$pathinfo['extension'], $url);
    }

    public function getThumbPath($url, $size = false) {
        if ( !$url ) {
            return false;
        }

        $pathinfo = pathinfo($url);
        $thumb = '_thumb';

        if ( $size == 'original' ) {
            $thumb = '_original';
        }
        else if ( $size ) {
            $thumb = '_thumb_'.$size;
        }
        else if ( $size === null ) {
            $thumb = '';
        }

        return config('path.uploads_path').str_replace($pathinfo['filename'].'.'.$pathinfo['extension'], $pathinfo['filename'].$thumb.'.'.$pathinfo['extension'], $url);
    }

    public function makeMediaThumb($image_name, $type, $thumb, $thumb_size) {
        if ( !$thumb_size[0] && !$thumb_size[1] ) {
            return;
        }

        if ( extension_loaded('imagick') ) {
            InterventionImage::configure(array('driver' => 'imagick'));
        }

        $original_path = config('path.uploads_path').'/'.$type.'/'.$image_name;
        $pathinfo = pathinfo($image_name);
        $thumb_path = config('path.uploads_path').'/'.$type.'/'.$pathinfo['filename'].($thumb == 'original' ? '' : '_'.$thumb).'.'.$pathinfo['extension'];

        if ( !file_exists($original_path) ) {
            return;
        }

        copy($original_path, $thumb_path);

        InterventionImage::make($thumb_path)->fit($thumb_size[0], $thumb_size[1])->save(null, 100);
    }
}
