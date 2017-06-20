<?php

use Sm\Application\App;
use Sm\Communication\Request\Request;
use Sm\Core\Resolvable\FunctionResolvable;
use Sm\Core\Resolvable\StringResolvable;

return [
    [ 'test_test_test_test_test' =>
          StringResolvable::init("TestFunction") ],
    
    [ FRAMEWORK_FROM_SRC . 'Sm' =>
          StringResolvable::init("Hello! You've discovered the app! Well done!") ],
    [ FRAMEWORK_FROM_SRC . 'fs' =>
          FunctionResolvable::init(function (Request $Request) {
              $App =
                  $Request->setChangePath(FRAMEWORK_FROM_SRC . "fs")->getApp()->duplicate()
                          ->register([ 'name' => 'Factshift' ]);
            
              return $App->Modules->routing->dispatch($Request);
          }),
    ],
    [ FRAMEWORK_FROM_SRC . 'ea' =>
          FunctionResolvable::init(function (\Sm\Communication\Network\Http\HttpRequest $Request) {
              /** @var App $App */
              $App                  = $Request->getApp()->duplicate();
              $App->name            = 'ExampleApp';
              $App->Paths->app_path = EXAMPLE_APP_PATH;
              $Request->setChangePath(FRAMEWORK_FROM_SRC . "ea");
              return $App->Modules->routing->dispatch($Request);
          }),
    ],
];