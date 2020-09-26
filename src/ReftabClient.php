<?php

namespace Reftab;

class ReftabClient {
  
  private $publicKey;
  
  private $secretKey;
  
  private $baseUrl = 'https://www.reftab.com/api/';
  
  function __construct($options) {
    $this->publicKey = $options['publicKey'];
    $this->secretKey = $options['secretKey'];
  }
  
  public function request($method, $endpoint, $id = null, $body = null) {
    $headers = [];
    $url = $this->baseUrl . $endpoint;
    if ($id) {
      $url .= '/' . $id;
    }
    $now = gmdate('D, d M Y H:i:s T');
    $contentMD5 = '';
    $contentType = '';
    if ($body) {
      $body = json_encode($body);
      $contentMD5 = md5($body);
      $contentType = 'application/json';
    }
    $signatureToSign = $method . "\n" .
      $contentMD5 . "\n" .
      $contentType . "\n" .
      $now . "\n" .
      $url;
    $token = base64_encode(hash_hmac('sha256', $signatureToSign, $this->secretKey));
    $signature = 'RT ' . $this->publicKey . ':' . $token;
    $headers[] = 'Authorization: ' .$signature;
    $headers[] = 'x-rt-date: ' . $now;
    
    $headers = implode("\r\n", $headers);
    
    $opts = [
      'http' => [
        'ignore_errors' => true,
        'method' => $method,
        'header' => $headers
      ]
    ];
    if ($contentType) {
      $opts['http']['header'] .= "\r\nContent-type: " . $contentType;
      $opts['http']['content'] = $body;
    }
    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);
    
    $response = json_decode($response);
    
    if (isset($response->error)) {
      throw new \Exception($response->error);
    }
    
    return $response;
  }
  
  public function get($endpoint, $id = null) {
    return $this->request('GET', $endpoint, $id);
  }
  
  public function put($endpoint, $id, $body) {
    return $this->request('PUT', $endpoint, $id, $body);
  }
  
  public function post($endpoint, $body) {
    return $this->request('POST', $endpoint, null, $body);
  }
  
  public function delete($endpoint, $id) {
    return $this->request('DELETE', $endpoint, $id);
  }
}