<?php
/**
 * User: Sam Washington
 * Date: 3/11/17
 * Time: 12:22 PM
 */

use Sm\App\App;
use Sm\EvaluableStatement\Constructs\And_;
use Sm\EvaluableStatement\EqualityCondition\GreaterThanCondition;
use Sm\EvaluableStatement\EqualityCondition\LessThanCondition;
use Sm\EvaluableStatement\EvaluableStatementFactory;
use Sm\Formatter\FormatterFactory;
use Sm\Query\Sql\SqlModule;
use Sm\Resolvable\FunctionResolvable;
use Sm\Type\Variable_\Variable_;
use function Sm\EvaluableStatement\EqualityCondition\newLessThanCondition;


$SqlModule = SqlModule::init(function (App $App, SqlModule $SqlModule) {
    $FormatterFactory = new FormatterFactory();
    $SqlModule->setFormatterFactory($FormatterFactory);
    $formatter_array = include $this->App->Paths->to_base('Sm/Query/Sql/MySql/mysql.sql.sm.formatter.php') ?? [];
    $FormatterFactory->register($formatter_array);
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
    # endregion
};
$SqlModule->setDispatch(FunctionResolvable::coerce($dispatch));

return $SqlModule;