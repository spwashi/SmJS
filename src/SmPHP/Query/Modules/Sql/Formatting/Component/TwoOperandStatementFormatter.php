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
        $left     = $stmt->getLeftSide();
        $operator = $stmt->getOperator();
        $right    = $stmt->getRightSide();
        $context  = $this->queryFormatter->getContext();
    
        # If we are executing something
        if ($context instanceof SqlExecutionContext) {
            # This is what turns the right side of the conditional into a placeholder (only if we are executing something)
            if (!($right instanceof ColumnSchema)) $right = $this->queryFormatter->placeholder($right);
        }
    
        # Format each side like we're talking about columns
        if ($right instanceof ColumnSchema) $right = $this->proxy($right, ColumnIdentifierFormattingProxy::class);
        if ($left instanceof ColumnSchema) $left = $this->proxy($left, ColumnIdentifierFormattingProxy::class);
    
        $formattedLeft  = $this->formatComponent($left);
        $formattedRight = $this->formatComponent($right);
        return $formattedLeft . ' ' . $operator . ' ' . $formattedRight;
    }
}