<?php

use Sm\Communication\Request\Request;
use Sm\Core\Application\App;
use Sm\Core\Resolvable\FunctionResolvable;
use Sm\Core\Resolvable\StringResolvable;

return [
    [ 'test_test_test_test_test' =>
          StringResolvable::coerce("TestFunction") ],
    
    [ FRAMEWORK_FROM_SRC . 'Sm' =>
          StringResolvable::coerce("Hello! You've discovered the app! Well done!") ],
    [ FRAMEWORK_FROM_SRC . 'fs' =>
          FunctionResolvable::coerce(function (Request $Request) {
              $App =
                  $Request->setChangePath(FRAMEWORK_FROM_SRC . "fs")->getApp()->duplicate()
                          ->register([ 'name' => 'Factshift' ]);
    
              return $App->Modules->routing->dispatch($Request);
          }),
    ],
    [ FRAMEWORK_FROM_SRC . 'ea' =>
          FunctionResolvable::init(function (Request $Request) {
              /** @var App $App */
              $App                  = $Request->getApp()->duplicate();
              $App->name            = 'ExampleApp';
              $App->Paths->app_path = EXAMPLE_APP_PATH;
              $Request->setChangePath(FRAMEWORK_FROM_SRC . "ea");
              return $App->Modules->routing->dispatch($Request);
          }),
    ],
];