<?php
/**
 * User: Sam Washington
 * Date: 3/11/17
 * Time: 12:22 PM
 */

use Sm\App\App;
use Sm\Entity\Property\Property;
use Sm\EvaluableStatement\Constructs\And_;
use Sm\EvaluableStatement\EqualityCondition\GreaterThanCondition;
use Sm\EvaluableStatement\EqualityCondition\LessThanCondition;
use Sm\EvaluableStatement\EvaluableStatementFactory;
use Sm\Formatter\FormatterFactory;
use Sm\Query\Interpreter\QueryInterpreterFactory;
use Sm\Resolvable\FunctionResolvable;
use Sm\Storage\Modules\Sql\MySql\MysqlDatabaseSource;
use Sm\Storage\Modules\Sql\MySql\MysqlPdoAuthentication;
use Sm\Storage\Modules\Sql\MySql\MysqlQueryInterpreter;
use Sm\Storage\Modules\Sql\SqlModule;
use Sm\Storage\Source\Database\TableSource;
use Sm\Type\Variable_\Variable_;


$SqlModule = SqlModule::init(function (App $App, SqlModule $SqlModule) {
    $FormatterFactory = new FormatterFactory();
    $SqlModule->setFormatterFactory($FormatterFactory);
    $path            = __DIR__ . '/mysql.sql.sm.formatter.php';
    $formatter_array = include $path ?? [];
    $FormatterFactory->register($formatter_array);
    $Authentication = MysqlPdoAuthentication::init()->setCredentials('codozsqq',
                                                                     '^bzXfxDc!Dl6',
                                                                     'localhost',
                                                                     'factshift');
    $Authentication->connect();
    $DatabaseSource = MysqlDatabaseSource::init();
    $DatabaseSource->authenticate($Authentication);
    $SqlModule->registerDatabaseSource($DatabaseSource);
    
    $App->Factories->resolve(QueryInterpreterFactory::class)->register(function ($item = null) use ($SqlModule) {
        $Interpreter = new MysqlQueryInterpreter;
        $Interpreter->setSqlModule($SqlModule);
        return $Interpreter;
    }, MysqlDatabaseSource::class);
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


//    var_dump($self->format($TitleProperty));
    # endregion
};
$SqlModule->setDispatch(FunctionResolvable::coerce($dispatch));

return $SqlModule;