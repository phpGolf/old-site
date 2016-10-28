<?php
if(!defined('INDEX')) {
    header('location: /');
}
class Image {
    const E_INVALID_FORMAT=2;
    const E_IMAGE_BIG=3;
    const E_IMAGE_RATIO=4;
    const E_INVALID_URL=5;
    const E_INVALID_ID=6;
    const E_LOADED_IMAGE=7;
    const E_NOT_LOADED_IMAGE=8;
    
    static private $max_size = '3072000';
    private $path = '/home/phpgolf/uploaded_images/';
    private $size = '200';
    private $maxSize = '400';
    private $errorCode = false;
    private $file;
    private $image;
    private $name;
    private $md5;
    private $format;
    private $uploaded;
    private $hasImage = false;
    private $imageError = false;
    
    public function __construct($imageError=false) {
        $this->imageError = $imageError;
    }
    
    public function setImage($id) {
         //Get information about image
        $PDO =&DB::$PDO;
        $pre = $PDO->prepare('SELECT name,md5,uploaded,format FROM images WHERE id=:id');
        $pre->execute(array(':id'=>$id));
        $res = $pre->fetch();
        if(!$res) {
            if(!$this->imageError) {
                msg('Did not find image',0);
            } else {
                $this->imageError('Did not find image');
            }
            $this->errorCode=self::E_INVALID_ID;
            return false;
        }
        if(!file_exists($this->path.$id.'.'.$res['format'])) {
            if(!$this->imageError) {
                msg('Did not find image',0);
            } else {
                $this->imageError('Did not find image');
            }
            $this->errorCode=self::E_INVALID_ID;
            return false;
        }
        $this->id = $id;
        $this->uploaded=$res['uploaded'];
        $this->format=$res['format'];
        $this->name=$res['name'];
        $this->md5=$res['md5'];
        $this->image = new Imagick($this->path.$id.'.'.$this->format);
        $this->hasImage = true;
        return true;
    }
    
    public function uploadFile(array $FILE) {
        if($this->hasImage) {
            $this->errorCode=self::E_LOADED_IMAGE;
            return false;
        }
        $this->file = $FILE;
        if($FILE['size'] > self::$max_size || $FILE['error'] == UPLOAD_ERR_INI_SIZE) {
            msg('Imagesize is too big',0);
            $this->errorCode=self::E_IMAGE_BIG;
            return false;
        }
        //Check image
        $mime = explode('/',mime_content_type($FILE['tmp_name']));
        if($mime[0] != 'image') {
            msg('Unknown image format',0);
            $this->errorCode=self::E_INVALID_FORMAT;
            return false;
        }
        //Imagemagic
        $image = new Imagick($FILE['tmp_name']);
        if(!in_array($image->getImageFormat(),array('PNG','JPEG','GIF'))) {
            msg('Unknown image format',0);
            $this->errorCode=self::E_INVALID_FORMAT;
            return false;
        }
        //Check ratio
        $size = $image->getImageGeometry();
        $highest = ($size['width'] > $size['height']) ? $size['width'] : $size['height'];
        $lowest = ($size['width'] < $size['height']) ? $size['width'] : $size['height'];
        if(($size['width']/$size['height']) > 1.1 || ($size['height']/$size['width']) > 1.1) {
            msg('Image ratio is to high, the image needs to be more square. Max 1:1.1 ratio',0);
            $this->errorCode=self::E_IMAGE_RATIO;
            return false;
        }
        //Make smaller image
        $image->cropImage($lowest,$lowest,0,0);
        if($lowest > $this->size) {
            $image->thumbnailImage($this->size,$this->size,true);
        }
        //Save image to tmp
        $tmp = $FILE['tmp_name'];
        $image->writeImage($tmp);
        //Make md5 sum of image
        $md5 = md5(file_get_contents($tmp));
        //Set variabels
        $this->image = &$image;
        $this->md5 = $md5;
        $this->format = strtolower($image->getImageFormat());
        $format = $this->format;
        //Save image info to DB
        $PDO =&DB::$PDO;
        $pre = $PDO->prepare('SELECT id,uploaded FROM images WHERE md5=:md5');
        $pre->execute(array(':md5' => $md5));
        $res = $pre->fetch();
        $pre->fetchAll();
        if($res) {
            //Image exists, return id
            unlink($tmp);
            $this->id = $res['id'];
            $this->uploaded = $res['uploaded'];
            $this->hasImage=true;
            return $res['id'];
        } else {
            //Save to DB
            $pre = $PDO->prepare('INSERT INTO images SET name=:name, md5=:md5, format=:format, uploaded=:uploaded');
            $uploaded = date('Y-m-d H:i:s');
            $pre->execute(array(':name' => pathinfo($FILE['name'],PATHINFO_FILENAME), ':md5' => $md5,':format'=>$format, ':uploaded'=>$uploaded));
            $id = $PDO->lastInsertId();
            //Move image
            copy($tmp,$this->path.$id.'.'.$format);
            unlink($tmp);
            $this->id = $id;
            $this->uploaded = $uploaded;
            $this->hasImage=true;
            return $id;
        }
    }
    
    public function uploadUrl($Url) {
        if($this->hasImage) {
            $this->errorCode=self::E_LOADED_IMAGE;
            return false;
        }
        //Validate url
        if(!filter_var($Url,FILTER_VALIDATE_URL)) {
            msg('Invalid url',0);
            $this->errorCode=self::E_INVALID_URL;
            return false;
        }
        //Get image
        $image = @file_get_contents($Url);
        if(!$image) {
            msg('Invalid url',0);
            $this->errorCode=self::E_INVALID_URL;
            return false;
        }
        $tmp = tempnam('/tmp','url-upload-');
        file_put_contents($tmp,$image);
        unset($image);
        //Make file array
        $FILE['name'] = pathinfo($Url,PATHINFO_BASENAME);
        $FILE['tmp_name'] = $tmp;
        $FILE['size'] = filesize($tmp);
        return $this->uploadFile($FILE);
    }
    
    //Get info about image
    public function getInfo() {
        if(!$this->hasImage) {
            $this->errorCode=self::E_NOT_LOADED_IMAGE;
            return false;
        }
        $return['id'] = $this->id;
        $return['name'] = $this->name;
        $return['format'] = $this->format;
        $return['uploaded'] = $this->uploaded;
        $return['hash'] = $this->md5;
        $return['path'] = $this->path.$this->id.'.'.$this->format;
        return $return;
    }
    
    //Get image
    public function getImage($size=false,$header=false) {
        if(!$this->hasImage) {
            $this->errorCode=self::E_NOT_LOADED_IMAGE;
            return false;
        }
        if($header) {
            header('Content-Type: image/'.$this->format,true);
        }
        $image = $this->image->clone();
        if($size && is_numeric($size)) {
            $size = ($size > $this->maxSize) ? $this->maxSize : $size;
            $image->scaleImage($size,0);
        }
        return $image->getImagesBlob();
    }
    
    public function imageError($msg) {
        $Image = new Imagick();
        $Image->newImage(200,200,new ImagickPixel('white'));
        $Image->setImageFormat('png');

        //Text
        $Draw = new ImagickDraw();

        $Draw->setFontSize(20);
        $Draw->setFillColor(new ImagickPixel('black'));
        $Draw->setTextAlignment(2);
        $Draw->setStrokeAntialias(true);
        $Draw->setTextAntialias(true);
        $Draw->annotation(100,50,$msg);

        $Image->drawImage($Draw);
        
        header('Content-Type: image/png',true);
        echo $Image;
    }
    
    //Get max size
    public function getMaxSize() {
        return self::$max_size;
    }
    
    //Error
    public function errorCode() {
        if($this->errorCode) {
            $errorCode = $this->errorCode;
            $this->errorCode = false;
            return $errorCode;
        } else {
            return false;
        }
    }
    
    //Clean
    private function clean() {
        @unlink($this->file['tmp_name']);
    }
}
