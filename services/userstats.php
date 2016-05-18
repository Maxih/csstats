<?php
include("../config.php");
include(ROOT_DIR."/includes/DB.php");
include(ROOT_DIR."/includes/SteamRest.php");
include(ROOT_DIR."/includes/User.php");
include(ROOT_DIR."/includes/MatchStats.php");

//$targetSteamIds = array("76561198056988928", "76561197999131282", "76561198029991763");
$targetSteamIds = array($_GET["steamid"]);
$userList = array();

/* Check and see whether the Steam Ids are already in the database*/
$steamIdString = "";

foreach($targetSteamIds as $steamId)
  $steamIdString .= "\"".$steamId."\",";

$steamIdString = rtrim($steamIdString, ",");

$userQuery = "SELECT * FROM users as u JOIN matchstats as m ON u.id=m.user_id WHERE steamid IN (".$steamIdString.")";
$selectResult = $mysqlConn->query($userQuery);

/* If there are old Steam Ids, get them from the database */
if($selectResult->num_rows > 0)
{
  while($selectedUser = $selectResult->fetch_assoc())
  {
    $curUser = new User($selectedUser["steamid"], $selectedUser["username"], $selectedUser["url"], $selectedUser["avatar"], $selectedUser["avatar_medium"], $selectedUser["avatar_large"], 1);
    $curUser->matchstats = new MatchStats($selectedUser["kills"], $selectedUser["deaths"], $selectedUser["timeplayed"], $selectedUser["plants"], $selectedUser["defuses"], $selectedUser["wins"], $selectedUser["played"], $selectedUser["damage"], $selectedUser["matcheswon"], $selectedUser["matchesplayed"], $selectedUser["mvps"], $selectedUser["shotstaken"], $selectedUser["shotshit"]);

    array_push($userList, $curUser);

    /* Remove the found Steam Ids from the search list */
    if(($key = array_search($selectedUser["steamid"], $targetSteamIds)) !== false)
      unset($targetSteamIds[$key]);
  }
}

/* If there are new Steam Ids, get them from the API */
if(count($targetSteamIds))
{
  /* Retrieve from the steam API the information for the new users */
  $steamUserInfo = new SteamUserInfo($config["apiKey"]);
  $steamUserInfo->args["steamids"] = $targetSteamIds;

  $newUserList = $steamUserInfo->serializeResponse();

  /* Insert the newly retrieved users into the database */
  $insertQuery = "INSERT INTO users (steamid, username, url, avatar, avatar_medium, avatar_large) VALUES ";

  foreach($newUserList as $newUser)
    $insertQuery .= "('".$newUser->steamId."', '".$newUser->userName."', '".$newUser->url."', '".$newUser->avatar."', '".$newUser->avatarMedium."', '".$newUser->avatarLarge."'),";

  $insertQuery = rtrim($insertQuery, ",");
  $mysqlConn->query($insertQuery);

  /* Insert the new users Match Stats into the database*/

  $insertMatchQuery = "INSERT INTO matchstats (user_id, kills, deaths, timeplayed, plants, defuses, wins, played, damage, matcheswon, matchesplayed, mvps, shotstaken, shotshit) VALUES ";

  foreach($newUserList as $newUser)
  {
    /* Retrieve from the steam API the match stats for the user */
    if($newUser->state)
    {
      $userStats = new SteamUserMatchStats($config["apiKey"]);
      $userStats->args["steamid"] = $newUser->steamId;

      $userMatchStats = $userStats->serializeResponse();

      $newUser->matchstats = $userMatchStats;
    }
    else {
      $newUser->matchstats = new MatchStats(NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
    }

    $insertMatchQuery .= "((SELECT id FROM users WHERE steamid='".$newUser->steamId."'), ".$newUser->matchstats->kills.", ".$newUser->matchstats->deaths.", ".$newUser->matchstats->timeplayed.", ".$newUser->matchstats->plants.", ".$newUser->matchstats->defuses.", ".$newUser->matchstats->wins.", ".$newUser->matchstats->played.", ".$newUser->matchstats->damage.", ".$newUser->matchstats->matcheswon.", ".$newUser->matchstats->matchesplayed.", ".$newUser->matchstats->mvps.", ".$newUser->matchstats->shotstaken.", ".$newUser->matchstats->shotshit."),";
  }

  $insertMatchQuery = rtrim($insertMatchQuery, ",");
  $mysqlConn->query($insertMatchQuery);

  /* Merge the new users with the old users */
  $userList = array_merge($userList, $newUserList);


}
/* echo json */

echo json_encode($userList);

?>
