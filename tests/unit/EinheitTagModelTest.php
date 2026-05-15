<?php

use App\Models\EinheitTagModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class EinheitTagModelTest extends CIUnitTestCase
{
    public function testTimestampAutomationIsDisabledForCreatedAtOnlyPivotTable(): void
    {
        $modelProperties = (new ReflectionClass(EinheitTagModel::class))->getDefaultProperties();

        $this->assertFalse(
            $modelProperties['useTimestamps'],
            'The pivot table has no updated_at column; automatic timestamps would add an empty batch-insert column.'
        );
    }
}
