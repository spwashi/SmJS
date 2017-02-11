<?php

use Sm\App\App;
use Sm\Request\Request;
use Sm\Resolvable\FunctionResolvable;
use Sm\Resolvable\StringResolvable;

return [
    [ 'test_test_test_test_test' =>
          StringResolvable::coerce("TestFunction") ],
    
    [ 'Sm/Sm' =>
          StringResolvable::coerce("Hello! You've discovered the app! Well done!") ],
    [ 'Sm/fs' =>
          FunctionResolvable::coerce(function (Request $Request) {
              $App =
                  $Request->setChangePath("Sm/fs")->getApp()->duplicate()
                          ->register([ 'name' => 'Factshift' ]);
    
              return $App->Modules->routing->dispatch($Request);
          }),
    ],
    [ 'Sm/ea' =>
          FunctionResolvable::init(function (Request $Request) {
              /** @var App $App */
              $App                  = $Request->getApp()->duplicate();
              $App->name            = 'ExampleApp';
              $App->Paths->app_path = BASE_PATH . 'SmTest/ExampleApp/';
              $Request->setChangePath("Sm/ea");
              return $App->Modules->routing->dispatch($Request);
          }),
    ],
];