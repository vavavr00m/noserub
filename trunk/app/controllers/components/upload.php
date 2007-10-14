<?php

class UploadComponent extends Object {

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function add($upload_form, $identity, $path) {
        $file = $upload_form['tmp_name'];
        $imageinfo = getimagesize($file);
        switch($imageinfo[2]) {
            case IMAGETYPE_GIF:
                $picture = imageCreateFromGIF($file);
                break;
                
            case IMAGETYPE_JPEG:
                $picture = imageCreateFromJPEG($file);
                break;
                
            case IMAGETYPE_PNG:
                $picture = imageCreateFromPNG($file);
                break;
                
            default:
                $picture = null;
        }
        
        if($picture) {
            # delete the old photo, if there was one
            if($identity['Identity']['photo']) {
                @unlink($path . $identity['Identity']['photo'] . '.jpg');
                @unlink($path . $identity['Identity']['photo'] . '-small.jpg');
            }
            
            # get random name for new photo and make sure it is unqiue
            $filename = '';
            $seed = $identity['Identity']['id'] . $file;
            while($filename == '') {
                $filename = md5($seed);
                if(file_exists($path.$filename.'.jpg')) {
                    $filename = '';
                    $seed = md5($seed . time());
                }
            }
            
            $this->data['Identity']['photo'] = $filename;
            
            $original_width  = $imageinfo[0];
            $original_height = $imageinfo[1];

            $this->save_scaled($picture, $original_width, $original_height, 150, 150, $path . $filename . '.jpg');
            $this->save_scaled($picture, $original_width, $original_height,  35,  35, $path . $filename . '-small.jpg');
            
            return $filename;
        }
        
        return false;
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function save_scaled($picture, $original_width, $original_height, $width, $height, $filename) {
        if($original_width==$width && $original_height==$height) {
            # original picture
            imagejpeg($picture, $filename, 100); # best quality
        } else {
            # resampling picture
            $resampled = imagecreatetruecolor($width, $height);
            imagecopyresampled($resampled, $picture, 0, 0, 0, 0, imagesx($resampled), imagesy($resampled), $original_width, $original_height);
            imagejpeg($resampled, $filename, 100); # best quality 
        }
    }
}

?>