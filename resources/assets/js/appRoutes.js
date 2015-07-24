angular.module('appRoutes', []).config(['$routeProvider', '$locationProvider', '$httpProvider',
  function ($routeProvider, $locationProvider, $httpProvider) {
    $routeProvider
      .when('/', {
        templateUrl: '/partials/index',
        controller: 'MainController'
      })
      .when('/:category/:action?/:id?', {
        templateUrl: function (params) {
          var allowedParams = ['category', 'action', 'id'];
          var paramVals = [];
          for (var key in params) {
            if (allowedParams.indexOf(key) !== -1) {
              paramVals.push(params[key]);
            }
          }
          return '/partials/' + paramVals.join('/');
        }
      })
      .otherwise({
        redirectTo: '/'
      });
    
    $locationProvider.html5Mode(true);

    $httpProvider.interceptors.push(['$rootScope', '$q', '$localStorage',
      function ($rootScope, $q, $localStorage) {
        return {
          request: function (config) {
            config.params = config.params || {};
            config.headers = config.headers || {};
            if ($localStorage.token) {
              config.headers.Authorization = 'Bearer ' + $localStorage.token;
              config.params.token = $localStorage.token; 
            }
            return config;
          },
          response: function (res) {
            return res || $q.when(res);
          },
          'responseError': function(response) {
              if(response.status === 401 || response.status === 400) {
                //console.log("Not logged in");
                // Handle unauthenticated user
                $rootScope.$broadcast('unauthorized');
                //$location.path('auth/login');
              }
              return $q.reject(response);
          }
        };
      }
    ]);
  }
]);
