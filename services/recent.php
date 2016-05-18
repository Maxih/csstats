<?php
include("../config.php");
include(ROOT_DIR."/includes/DB.php");
include(ROOT_DIR."/includes/SteamRest.php");
include(ROOT_DIR."/includes/User.php");
include(ROOT_DIR."/includes/MatchStats.php");

$userList = array();

$userQuery = "SELECT * FROM users ORDER BY id DESC LIMIT 10";
$selectResult = $mysqlConn->query($userQuery);

/* If there are old Steam Ids, get them from the database */
if($selectResult->num_rows > 0)
{
  while($selectedUser = $selectResult->fetch_assoc())
  {
    $curUser = new User($selectedUser["steamid"], $selectedUser["username"], $selectedUser["url"], $selectedUser["avatar"], $selectedUser["avatar_medium"], $selectedUser["avatar_large"], 1);

    array_push($userList, $curUser);
  }
}
/* echo json */

echo json_encode($userList);

?>
