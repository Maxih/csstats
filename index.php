<?php
/*
Your Steam Web API Key
Key: EF936026AC9D744FC68AF0C4F5F35129
Domain Name: hlmodders.com
*/
include("config.php");
include("includes/DB.php");
include("includes/User.php");

$targetSteamIds = array("76561198056988928", "76561197999131282", "76561198029991763");
$userList = array();

$steamIdString = "";

foreach($targetSteamIds as $steamId)
{
  $steamIdString .= "\"".$steamId."\",";
}

$steamIdString = rtrim($steamIdString, ",");

$userQuery = "SELECT * FROM users WHERE steamid IN (".$steamIdString.")";
$selectResult = $mysqlConn->query($userQuery);

if($selectResult->num_rows > 0)
{
  while($selectedUser = $selectResult->fetch_assoc())
  {
    $curUser = new User($selectedUser["steamid"], $selectedUser["username"], $selectedUser["url"], $selectedUser["avatar"], $selectedUser["avatar_medium"], $selectedUser["avatar_large"]);
    array_push($userList, $curUser);

    if(($key = array_search($selectedUser["steamid"], $targetSteamIds)) !== false) {
      unset($targetSteamIds[$key]);
    }
  }
  echo $selectResult->num_rows." of the requested IDs are already in the database<br /><br />";
}

/*
ISteamUser/GetPlayerSummaries
*/

if(count($targetSteamIds))
{
  echo count($targetSteamIds)." of the requested IDs are new additions<br />";

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

  /* Merge the new users with the old users */
  $userList = array_merge($userList, $newUserList);
}

var_dump($userList);
 ?>
