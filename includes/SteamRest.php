<?php
class SteamRest {

  var $args = array();
  var $method;
  var $apiUrl;
  var $apiKey;

  public function formatUrl()
  {
    $requestUrl = $this->apiUrl.$this->method."/?key=".$this->apiKey;

    foreach($this->args as $argName=>$argValue)
    {
      if(is_array($argValue))
        $requestUrl .= "&".$argName."=".implode(",", $argValue);
      else
        $requestUrl .= "&".$argName."=".$argValue;
    }

    return $requestUrl;
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

  public function serializeResponse()
  {

  }
}

 ?>
