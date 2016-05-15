<?php
class SteamRest {

  var $args = array();
  var $method;
  var $apiUrl;

  public function __construct($argList, $methodUrl)
  {
    $this->apiUrl = "http://api.steampowered.com/";
    $this->args = $argList;
    $this->method = $methodUrl;
  }

  public function formatUrl()
  {
    $requestUrl = $this->apiUrl.$this->method."/?";

    foreach($this->args as $argName=>$argValue)
    {
      $requestUrl .= $argName."=".$argValue."&";
    }

    return rtrim($requestUrl, "&");
  }

  public function requestResponse()
  {
    $requestUrl = $this->formatUrl();

    $curl = curl_init($requestUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $curlResponse = curl_exec($curl);

    if ($curlResponse === false) {
        $info = curl_getinfo($curl);
        curl_close($curl);
        die('error occured during curl exec. Additional info: ' . var_export($info));
    }

    curl_close($curl);

    return json_decode($curlResponse);
  }
}

 ?>
