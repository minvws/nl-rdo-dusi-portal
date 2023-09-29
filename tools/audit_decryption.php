<?php
// run this script with `php audit_decryption.php <encrypted>`
// before running this script export the following base64 encoded sodium keys as environment variables:
// ENCRYPTION_PUBLIC, DECRYPTION_SECRET
// to debug encryption, uncomment specific parts of the script below.

//$keypair1 = sodium_crypto_box_keypair();
//$keypair1_secret = sodium_crypto_box_secretkey($keypair1);
//$keypair1_public = sodium_crypto_box_publickey($keypair1);
//$keypair2 = sodium_crypto_box_keypair();
//$keypair2_secret = sodium_crypto_box_secretkey($keypair2);
//$keypair2_public = sodium_crypto_box_publickey($keypair2);
//
//echo base64_encode($keypair1_secret) . "\n";
//echo base64_encode($keypair1_public) . "\n";
//echo base64_encode($keypair2_secret) . "\n";
//echo base64_encode($keypair2_public) . "\n";
//exit 1;

$encrypted = base64_decode($argv[1]);
//
//$encryption_secret = base64_decode(getenv('ENCRYPTION_SECRET'));
$encryption_public = base64_decode(getenv('ENCRYPTION_PUBLIC'));
$decryption_secret = base64_decode(getenv('DECRYPTION_SECRET'));
//$decryption_public = base64_decode(getenv('DECRYPTION_PUBLIC'));

//$encryption_keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey(
//    $encryption_secret, $decryption_public);

$decryption_keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey(
    $decryption_secret, $encryption_public);

//$message = 'hello';
//
//$nonce = random_bytes(SODIUM_CRYPTO_BOX_NONCEBYTES);
//
//$encrypted = sodium_crypto_box($message, $nonce, $encryption_keypair);
//$encrypted = $nonce . $encrypted;

//echo base64_encode($encrypted) . "\n";

$nonce = substr($encrypted, 0, SODIUM_CRYPTO_BOX_NONCEBYTES);
$encrypted = substr($encrypted, SODIUM_CRYPTO_BOX_NONCEBYTES);

$decrypted = sodium_crypto_box_open($encrypted, $nonce, $decryption_keypair);

echo $decrypted . "\n";

?>