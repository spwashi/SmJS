<?php

use Sm\App\App;
use Sm\Request\Request;
use Sm\Resolvable\FunctionResolvable;
use Sm\Resolvable\StringResolvable;

return [
    [ 'test_test_test_test_test' =>
        
          StringResolvable::coerce("TestFunction") ],
    
    [ 'localhost/Sm/Sm' =>
        
          StringResolvable::coerce("Hello! You've discovered the app! Well done!") ],
    
    [ 'localhost/Sm/fs' =>
        
          FunctionResolvable::init(function (Request $Request) {
              /** @var App $App */
              $App                  = $Request->getApp()->duplicate();
              $App->name            = 'ExampleApp';
              $App->Paths->app_path = BASE_PATH . 'SmTest/ExampleApp/';
    
              $Request->setChangePath(FunctionResolvable::coerce(function ($path) {
                  return preg_replace('~(localhost/Sm/fs)/?~', '', $path);
              }));
              $App = $App
                  ->resolve('app.module')
                  ->dispatch($App);
              return $App
                  ->resolve('routing.module')
                  ->initialize()
                  ->dispatch($App, $Request);
          }),
    ],
];