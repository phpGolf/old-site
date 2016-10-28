<?php
if(!defined('INDEX')) {
    header('location: /');
}
include_class('image');

switch ($_GET['value'][0]) {
    case 'show':
        $Image = new Image(true);
        list($id)= explode('.',$_GET['value'][1]);
        if(!$Image->setImage($id)) {
            break;
        }
        $info = $Image->getInfo();
        $modified = gmdate('D, d M Y H:i:s',strtotime($info['uploaded'])). ' GMT';
        //Cache
        header('Cache-Control: private, must-revalidate');
        header('Pragma: no-cache');
        header('Expires:');
        if($_SERVER['HTTP_IF_MODIFIED_SINCE'] >= $modified) {
            header('HTTP/1.1 304 Not Modified');
            break;
        } else {
            header('Last-Modified: '.$modified);
        }
        //Print image
        header('Content-Type: image/'.$info['format']);
        echo $Image->getImage(($_GET['size']) ? $_GET['size'] : false);
        break;
    default:
        error(403);
        if($_FILES['image']) {
            $Image = new Image();
            if($id = $Image->uploadFile($_FILES['image'])) {
                print_r($Image->getInfo());
            }
            
        }
        ?>
        <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="<?=Image::getMaxSize()?>" />
        <input type="file" name="image"><br>
        <input type="submit">
        </form>
        <?php
        show_page('Upload');
        break;
}
