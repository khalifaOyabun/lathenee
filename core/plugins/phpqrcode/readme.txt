
include('libs/phpqrcode/qrlib.php'); 

QRcode::png($codeContents, $tempDir.''.$filename.'.png', QR_ECLEVEL_L, 5);
