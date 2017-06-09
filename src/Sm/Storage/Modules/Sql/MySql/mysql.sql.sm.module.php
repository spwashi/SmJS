<?php
/**
 * User: Sam Washington
 * Date: 3/11/17
 * Time: 12:22 PM
 */

use Sm\App\App;
use Sm\Data\Variable_\Variable_;
use Sm\Entity\Property\Property;
use Sm\EvaluableStatement\Constructs\And_;
use Sm\EvaluableStatement\EqualityCondition\GreaterThanCondition;
use Sm\EvaluableStatement\EqualityCondition\LessThanCondition;
use Sm\EvaluableStatement\EvaluableStatementFactory;
use Sm\Formatter\FormatterFactory;
use Sm\Query\Interpreter\QueryInterpreterFactory;
use Sm\Resolvable\FunctionResolvable;
use Sm\Storage\Database\TableSource;
use Sm\Storage\Modules\Sql\MySql\Interpreter\MysqlQueryInterpreter;
use Sm\Storage\Modules\Sql\MySql\MysqlDatabaseSource;
use Sm\Storage\Modules\Sql\MySql\MysqlPdoAuthentication;
use Sm\Storage\Modules\Sql\SqlModule;


$SqlModule = SqlModule::init(function (App $App, SqlModule $SqlModule) {
    $FormatterFactory = new FormatterFactory;
    $path             = __DIR__ . '/mysql.sql.sm.formatter.php';
    $FormatterFactory->register(null, include $path ?? []);
    $SqlModule->setFormatterFactory($FormatterFactory);
    
    
    #region registerDatabaseSource
    $Authentication = MysqlPdoAuthentication::init()->setCredentials('codozsqq',
                                                                     '^bzXfxDc!Dl6',
                                                                     'localhost',
                                                                     'factshift');
    $Authentication->connect();
    $DatabaseSource = MysqlDatabaseSource::init();
    $DatabaseSource->authenticate($Authentication);
    $SqlModule->registerDatabaseSource($DatabaseSource);
    #endregion
    
    
    $get_mysql_interpreter_fn = function ($item = null) use ($SqlModule) {
        return (new MysqlQueryInterpreter)->setSqlModule($SqlModule);
    };
    $App->Factories->resolve(QueryInterpreterFactory::class)
                   ->register(MysqlDatabaseSource::class, $get_mysql_interpreter_fn);
});
$dispatch  = function (App $App, SqlModule $self) {
    /** @var EvaluableStatementFactory $_ */
    $_ = $App->Factories->resolve(EvaluableStatementFactory::class);
    # region todo this is part of a test
    $and           = function () use ($_) { return $_(And_::class)->set(...func_get_args()); };
    $greater       = function () use ($_) { return $_(GreaterThanCondition::class)->set(...func_get_args()); };
    $less          = function () use ($_) { return $_(LessThanCondition::class)->set(...func_get_args()); };
    $Condition_One = $less(1, Variable_::init("title"));
    $Condition_Two = $greater(Variable_::init("name"), 5);
    $And           = $and($Condition_One, $Condition_Two);
    $result        = $self->format($And);
    
    $Authentication = MysqlPdoAuthentication::init()->setCredentials('codozsqq',
                                                                     '^bzXfxDc!Dl6',
                                                                     'localhost',
                                                                     'factshift');
    $DatabaseSource = MysqlDatabaseSource::init();
    $DatabaseSource->authenticate($Authentication);
    
    $Source        = new TableSource($DatabaseSource, 'sections');
    $TitleProperty = new Property('title', $Source);
    
    
    # endregion
};
$SqlModule->setDispatch(FunctionResolvable::coerce($dispatch));

return $SqlModule;