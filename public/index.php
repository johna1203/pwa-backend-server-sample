<?php
require '../vendor/autoload.php';
use PayWithAmazon\Client as Client;

//cors settings
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

$response = array();
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
      header("Access-Control-Allow-Methods: POST, OPTIONS");

  if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
      header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

  //end request
  exit(0);
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $config = include("../config/config.local.php");
  $client = new Client($config);

  $postdata = file_get_contents("php://input");
  $request = json_decode($postdata);
  @$action = $request->action;

  switch ($action) {
    case 'GetAuthorizationDetails':
        $params = array();
        if (isParamOK('AmazonAuthorizationId', $request)) {
            $params['amazon_authorization_id'] = $request->AmazonAuthorizationId;
        }
        $clientResponse = $client->getAuthorizationDetails($params);
        $response = $clientResponse->toArray();
        break;
    case 'GetCaptureDetails':
        $params = array();
        if (isParamOK('AmazonCaptureId', $request)) {
            $params['amazon_capture_id'] = $request->AmazonCaptureId;
        }
        $clientResponse = $client->getCaptureDetails($params);
        $response = $clientResponse->toArray();
        break;
    case 'GetOrderReferenceDetails':
        $params = array();
        if (isParamOK('AmazonOrderReferenceId', $request)) {
            $params['amazon_order_reference_id'] = $request->AmazonOrderReferenceId;
        }
        if (isParamOK('AddressConsentToken', $request)) {
            $params['address_consent_token'] = $request->AddressConsentToken;
        }
        $clientResponse = $client->getOrderReferenceDetails($params);
        $response = $clientResponse->toArray();
        break;
    case 'Signature':
        $params = (array)$request;
        $string_signature = "";
        if (isset($params['stringToSign'])) {
          $string_signature = $params['stringToSign'];
          $string_signature = str_replace('____AWSAccessKeyId____', $config['access_key'], $string_signature);
        }
        $signature = base64_encode(hash_hmac('sha256', $string_signature, $config['secret_key'], true));
        $response = ['signature' => $signature, 'AWSAccessKeyId' => $config['access_key'], 'string_signature'=> $string_signature];
        break;
  }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;

function isParamOK($key, $request)
{
    return property_exists($request, $key);
}
