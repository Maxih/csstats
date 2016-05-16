<?php
class User {
  var $steamId;
  var $userName;
  var $url;
  var $avatar;
  var $avatarMedium;
  var $avatarLarge;
  var $state;

  var $matchstats;

  public function __construct($steamId, $userName, $url, $avatar, $avatarMedium, $avatarLarge, $state)
  {
    $this->steamId = $steamId;
    $this->userName = $userName;
    $this->url = $url;
    $this->avatar = $avatar;
    $this->avatarMedium = $avatarMedium;
    $this->avatarLarge = $avatarLarge;
    $this->state = $state;
  }
}

/* Class for serializing steams rest response into known format */

class SteamUserInfo extends SteamRest {

  public function __construct($apiKey)
  {
    $this->apiKey = $apiKey;

    $this->apiUrl = "http://api.steampowered.com/";
    $this->method = "ISteamUser/GetPlayerSummaries/v0002";
    $this->args = array(
      "steamids" => array()
    );
  }

  public function serializeResponse()
  {
    $response = $this->requestResponse();
    $userList = array();

    foreach($response->{"response"}->{"players"} as $player)
    {
      $curUser = new User($player->{"steamid"}, $player->{"personaname"}, $player->{"profileurl"}, $player->{"avatar"}, $player->{"avatarmedium"}, $player->{"avatarfull"}, $player->{"personastate"});
      array_push($userList, $curUser);
    }

    return $userList;
  }
}
 ?>
