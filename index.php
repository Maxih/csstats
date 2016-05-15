<?php
/*
Your Steam Web API Key
Key: EF936026AC9D744FC68AF0C4F5F35129
Domain Name: hlmodders.com
*/

include("includes/SteamRest.php");


$apikey = "EF936026AC9D744FC68AF0C4F5F35129";
$steamid = "76561198056988928";

/*
ISteamUser/GetPlayerSummaries
*/

$method = "ISteamUser/GetPlayerSummaries/v0002";

$args = array(
  "key" => "EF936026AC9D744FC68AF0C4F5F35129",
  "steamids" => "76561198056988928"
);

$steamRestObj = new SteamRest($args, $method);

echo $steamRestObj->formatUrl();

var_dump($steamRestObj->requestResponse());


/*
$service_url = "http://api.steampowered.com/ISteamUserStats/GetUserStatsForGame/v0002/?appid=730&key=".$apikey."&steamid=".$steamid;
$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$curl_response = curl_exec($curl);

if ($curl_response === false) {
    $info = curl_getinfo($curl);
    curl_close($curl);
    die('error occured during curl exec. Additional info: ' . var_export($info));
}

curl_close($curl);

$decoded = json_decode($curl_response);

echo $decoded->{"playerstats"}->{"steamID"};

foreach( $decoded->{"playerstats"}->{"stats"} as $stats=>$stat)
{
  echo $stat->{"name"};
}
*/
//var_dump($decoded);

 ?>
