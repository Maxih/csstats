<?php
class MatchStats {
  var $kills;
  var $deaths;
  var $timeplayed;
  var $plants;
  var $defuses;
  var $wins;
  var $damage;

  public function __construct($kills, $deaths, $timeplayed, $plants, $defuses, $wins, $damage)
  {
    $this->kills = $kills;
    $this->deaths = $deaths;
    $this->timeplayed = $timeplayed;
    $this->plants = $plants;
    $this->defuses = $defuses;
    $this->wins = $wins;
    $this->damage = $damage;
  }
}

class SteamUserMatchStats extends SteamRest {

  public function __construct($apiKey)
  {
    $this->apiKey = $apiKey;

    $this->apiUrl = "http://api.steampowered.com/";
    $this->method = "ISteamUserStats/GetUserStatsForGame/v0002";
    $this->args = array(
      "appid" => "730",
      "steamid" => ""
    );
  }

  public function serializeResponse()
  {
    $response = $this->requestResponse();

    $curStats = new MatchStats(NULL,NULL,NULL,NULL,NULL,NULL,NULL);

    foreach($response->{"playerstats"}->{"stats"} as $stat)
    {
      switch($stat->{"name"})
      {
        case "total_kills":
          $curStats->kills = $stat->{"value"};
          break;
        case "total_deaths":
          $curStats->deaths = $stat->{"value"};
          break;
        case "total_time_played":
          $curStats->timeplayed = $stat->{"value"};
          break;
        case "total_planted_bombs":
          $curStats->plants = $stat->{"value"};
          break;
        case "total_defused_bombs":
          $curStats->defuses = $stat->{"value"};
          break;
        case "total_wins":
          $curStats->wins = $stat->{"value"};
          break;
        case "total_damage_done":
          $curStats->damage = $stat->{"value"};
          break;
      }
    }

    return $curStats;
  }
}
 ?>
