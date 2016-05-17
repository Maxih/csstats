var app = angular.module('stats', []);

app.controller('StatController', function($scope, $http) {
  this.newSteamId = null;

  $scope.userStats = [];

  this.getUsers = function getUsers() {
    if(this.newSteamId != null)
    {
      $http({method: 'GET', url: '/api/userstats.php?steamid='+this.newSteamId, headers: { }})
       .success(function(data, status) {
        var newUserStats = [];
        data.forEach(function(stat){
          if($scope.userStats.indexOf(stat) == -1)
          {
            newUserStats.push(stat);
          }
         });


         $scope.userStats = $scope.userStats.concat(newUserStats);
        })
       .error(function(data, status) {
           alert("Error");
       });
       this.newSteamId = null;
     }
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

});
