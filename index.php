<?php
include("config.php");
include("includes/DB.php");
include("includes/User.php");
include("includes/MatchStats.php");

$targetSteamIds = array("76561198056988928", "76561197999131282", "76561198029991763");
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
  echo $selectResult->num_rows." of the requested IDs are already in the database<br /><br />";

  while($selectedUser = $selectResult->fetch_assoc())
  {
    $curUser = new User($selectedUser["steamid"], $selectedUser["username"], $selectedUser["url"], $selectedUser["avatar"], $selectedUser["avatar_medium"], $selectedUser["avatar_large"]);
    $curUser->matchstats = new MatchStats($selectedUser["kills"], $selectedUser["deaths"], $selectedUser["timeplayed"], $selectedUser["plants"], $selectedUser["defuses"], $selectedUser["wins"], $selectedUser["damage"]);

    array_push($userList, $curUser);

    /* Remove the found Steam Ids from the search list */
    if(($key = array_search($selectedUser["steamid"], $targetSteamIds)) !== false)
      unset($targetSteamIds[$key]);
  }
}

/* If there are new Steam Ids, get them from the API */
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

  /* Insert the new users Match Stats into the database*/

  $insertMatchQuery = "INSERT INTO matchstats (user_id, kills, deaths, timeplayed, plants, defuses, wins, damage) VALUES ";

  foreach($newUserList as $newUser)
  {
    /* Retrieve from the steam API the match stats for the user */
    $userStats = new SteamUserMatchStats($config["apiKey"]);
    $userStats->args["steamid"] = $newUser->steamId;

    $userMatchStats = $userStats->serializeResponse();

    $newUser->matchstats = $userMatchStats;

    $insertMatchQuery .= "((SELECT id FROM users WHERE steamid='".$newUser->steamId."'), ".$userMatchStats->kills.", ".$userMatchStats->deaths.", ".$userMatchStats->timeplayed.", ".$userMatchStats->plants.", ".$userMatchStats->defuses.", ".$userMatchStats->wins.", ".$userMatchStats->damage."),";
  }

  $insertMatchQuery = rtrim($insertMatchQuery, ",");
  $mysqlConn->query($insertMatchQuery);

  /* Merge the new users with the old users */
  $userList = array_merge($userList, $newUserList);


}
//var_dump($userList);

/* Begin HTML */
?>
<style>
div.userInfo {
  display: table-row;
}
div.userInfo span {
  display: table-cell;
  padding: 10px;
}
</style>
<div class="userInfo">
  <span class="userAvatar">
  </span>
  <span class="userName">
    Name
  </span>
  <span class="userKills">
    Kills
  </span>
  <span class="userDeaths">
    Deaths
  </span>
  <span class="userKD">
    K/D
  </span>
</div>
<?php

foreach($userList as $user):

?>

<div class="userInfo">
  <span class="userAvatar">
    <img src="<?php echo $user->avatar; ?>"/>
  </span>
  <span class="userName">
    <a href="<?php echo $user->url; ?>"><?php echo $user->userName; ?></a>
  </span>
  <span class="userKills">
    <?php echo $user->matchstats->kills; ?>
  </span>
  <span class="userDeaths">
    <?php echo $user->matchstats->deaths; ?>
  </span>
  <span class="userKD">
    <?php echo $user->matchstats->kills/$user->matchstats->deaths; ?>
  </span>
</div>
<?php
endforeach;
 ?>
