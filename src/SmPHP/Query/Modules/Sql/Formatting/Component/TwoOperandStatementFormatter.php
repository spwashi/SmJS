<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 11:17 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Component;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Evaluation\TwoOperandStatement;
use Sm\Query\Modules\Sql\Data\Column\ColumnSchema;
use Sm\Query\Modules\Sql\Formatting\Proxy\Column\ColumnIdentifierFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;
use Sm\Query\Modules\Sql\SqlExecutionContext;

class TwoOperandStatementFormatter extends SqlQueryFormatter {
    public function format($stmt): string {
        if (!($stmt instanceof TwoOperandStatement)) throw new InvalidArgumentException("Can only format TwoOperandStatements");
        $left             = $stmt->getLeftSide();
        $operator         = $stmt->getOperator();
        $right            = $stmt->getRightSide();
        $formatterFactory = $this->queryFormatter;
        $context          = $formatterFactory->getContext();
        
        if ($context instanceof SqlExecutionContext) {
            # THIS IS WHAT CREATE THE PLACEHOLDERS
            if (!($right instanceof ColumnSchema)) $right = $formatterFactory->placeholder($right);
        }
        
        if ($right instanceof ColumnSchema) $right = $formatterFactory->proxy($right, ColumnIdentifierFormattingProxy::class);
        if ($left instanceof ColumnSchema) $left = $formatterFactory->proxy($left, ColumnIdentifierFormattingProxy::class);
        
        return $formatterFactory($left) . ' ' . $operator . ' ' . $formatterFactory($right);
    }
}