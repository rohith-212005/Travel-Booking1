<?php
session_start();

// Generate a random 4-character alphanumeric code (simpler than 5-digit number)
$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$text = substr(str_shuffle($chars), 0, 4);
$_SESSION['captcha'] = $text;

// Create a clean, simple image
$width = 100;
$height = 30;
$im = imagecreate($width, $height);

// White background
$bg = imagecolorallocate($im, 255, 255, 255);
$textcolor = imagecolorallocate($im, 0, 0, 0);

// Fill background
imagefilledrectangle($im, 0, 0, $width, $height, $bg);

// Add text (centered)
$font = 5; // Built-in GD font
$x = ($width - imagefontwidth($font) * strlen($text)) / 2;
$y = ($height - imagefontheight($font)) / 2;
imagestring($im, $font, $x, $y, $text, $textcolor);

// Output the image
header('Content-type: image/png');
imagepng($im);
imagedestroy($im);
?>