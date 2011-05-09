<?

// From the php.net documentation: http://www.php.net/manual/en/function.imagecopymerge.php

/**
 * PNG ALPHA CHANNEL SUPPORT for imagecopymerge();
 * This is a function like imagecopymerge but it handle alpha channel well!!!
 **/

// A fix to get a function like imagecopymerge WITH ALPHA SUPPORT
// Main script by aiden dot mail at freemail dot hu
// Transformed to imagecopymerge_alpha() by rodrigo dot polo at gmail dot com 
function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
    if(!isset($pct)){
        return false;
    }
    $pct /= 100;
    // Get image width and height
    $w = imagesx( $src_im );
    $h = imagesy( $src_im );
    // Turn alpha blending off
    imagealphablending( $src_im, false );
    // Find the most opaque pixel in the image (the one with the smallest alpha value)
    $minalpha = 127;
    for( $x = 0; $x < $w; $x++ )
    for( $y = 0; $y < $h; $y++ ){
        $alpha = ( imagecolorat( $src_im, $x, $y ) >> 24 ) & 0xFF;
        if( $alpha < $minalpha ){
            $minalpha = $alpha;
        }
    }
    //loop through image pixels and modify alpha for each
    for( $x = 0; $x < $w; $x++ ){
        for( $y = 0; $y < $h; $y++ ){
            //get current alpha value (represents the TANSPARENCY!)
            $colorxy = imagecolorat( $src_im, $x, $y );
            $alpha = ( $colorxy >> 24 ) & 0xFF;
            //calculate new alpha
            if( $minalpha !== 127 ){
                $alpha = 127 + 127 * $pct * ( $alpha - 127 ) / ( 127 - $minalpha );
            } else {
                $alpha += 127 * $pct;
            }
            //get the color index with new alpha
            $alphacolorxy = imagecolorallocatealpha( $src_im, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha );
            //set pixel with the new color + opacity
            if( !imagesetpixel( $src_im, $x, $y, $alphacolorxy ) ){
                return false;
            }
        }
    }
    // The image copy
    imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
}


$img_a = imagecreatefrompng('root/images/dynamo/7-1.png');
$img_b = imagecreatefrompng('root/images/dynamo/6-1.png');
$img_c = imagecreatefrompng('root/images/dynamo/5-1.png');
$img_d = imagecreatefrompng('root/images/dynamo/4-1.png');
$img_e = imagecreatefrompng('root/images/dynamo/3-3.png');
$img_f = imagecreatefrompng('root/images/dynamo/2-3.png');
$img_g = imagecreatefrompng('root/images/dynamo/1-3.png');

// SAME COMMANDS:
imagecopymerge_alpha($img_a, $img_b, 0, 0, 0, 0, imagesx($img_b), imagesy($img_b),100);
imagecopymerge_alpha($img_a, $img_c, 0, 0, 0, 0, imagesx($img_c), imagesy($img_c),100);
imagecopymerge_alpha($img_a, $img_d, 0, 0, 0, 0, imagesx($img_d), imagesy($img_d),100);
imagecopymerge_alpha($img_a, $img_e, 0, 0, 0, 0, imagesx($img_e), imagesy($img_e),100);
imagecopymerge_alpha($img_a, $img_f, 0, 0, 0, 0, imagesx($img_e), imagesy($img_e),100);
imagecopymerge_alpha($img_a, $img_g, 0, 0, 0, 0, imagesx($img_e), imagesy($img_e),100);

// OUTPUT IMAGE:
header("Content-Type: image/png");
imagesavealpha($img_a, true);
imagepng($img_a, NULL);
?>
