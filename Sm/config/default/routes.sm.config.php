<?php

use Sm\App\App;
use Sm\App\Module\Module;
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
              $App       = $Request->getApp()->duplicate();
              $App->name = 'ExampleApp';
              $App->Paths->register([ 'app_path' => 'ExampleApp/', ]);
              $App->resolve('app.module')->dispatch($App);
              /** @var Module $RoutingModule */
              $RoutingModule = $App->resolve('routing.module')->initialize();
              return $RoutingModule->dispatch($App, $Request);
          }),
    ],
];