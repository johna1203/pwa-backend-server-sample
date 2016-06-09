<?php
$endpoint = 'https://payments.amazon.com/';
$string_request = 'AWSAccessKeyId=AKIAJEJZILE2MJH7IB2Q&SignatureMethod=HmacSHA256&SignatureVersion=2&amazonOrderReferenceId=S01-8426302-5648482&authorizeAttributes.authorizationAmount.amount=49.99&authorizeAttributes.authorizationAmount.currencyCode=USD&orderTotal.amount=49.99&orderTotal.currencyCode=USD&paymentAction=AuthorizeAndCapture&sellerOrderAttributes.sellerOrderId=johna-test-001100&sellerOrderAttributes.sellerStoreName=Johna%20app%20test%20Store';
$parse_url = parse_url($endpoint);
$secret_key = '1WAsi5I87uSPH4IVl9vYgCA3GafJNlPaz1St19/g';
$string_signature = "POST\n{$parse_url["host"]}\n{$parse_url["path"]}\n$string_request";
//Zp3a32v2r0kkaZGpdYi5VWXjRyNm08Hl+rXHE5iA4Ak=
echo $signature = base64_encode(hash_hmac('sha256', $string_signature, $secret_key, true));
 ?>
