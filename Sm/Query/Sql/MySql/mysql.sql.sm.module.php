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
    
    # todo this is part of a test
    
    $Condition_One = $_(LessThanCondition::class)->set(1, Variable_::init("title"));
    $Condition_Two = $_(GreaterThanCondition::class)->set(Variable_::init("name"), 5);
    $And           = $_(And_::class)->set($Condition_One, $Condition_Two);
    $result        = $self->format($And);
    
    
};
$SqlModule->setDispatch(FunctionResolvable::coerce($dispatch));

return $SqlModule;