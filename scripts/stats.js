var app = angular.module('stats', []);

app.controller('StatController', function($scope, $http) {
  this.newSteamId = null;

  $scope.userStats = [];
  $scope.recentUsers = [];

  this.getUsers = function getUsers(steamid) {
    if(steamid != null)
    {
      console.log(steamid);
      $http({method: 'GET', url: '/services/userstats.php?steamid='+steamid, headers: { }})
       .success(function(data, status) {
        data.forEach(function(stat){
          isNewUser = true;
          $scope.userStats.forEach(function(oldstat){
            if(stat.steamId==oldstat.steamId)
              isNewUser=false;
          });

          if(isNewUser)
            $scope.userStats.push(stat);
         });
        })
       .error(function(data, status) {
           alert("Error");
       });
     }
   }

   this.getRecentUsers = function getRecentUsers() {
     $http({method: 'GET', url: '/services/recent.php', headers: { }})
      .success(function(data, status) {
        $scope.recentUsers = data;
       })
      .error(function(data, status) {
          alert("Error");
      });
    }

   this.removeUser = function removeUser(steamId) {
     var newUserStats = [];
     $scope.userStats.forEach(function(stat){
       if(stat.steamId != steamId)
       {
         newUserStats.push(stat);
       }
     });

     $scope.userStats = newUserStats;
   }

   this.test = function test(string) {
     console.log(string);
   }

   this.getRecentUsers();
});
