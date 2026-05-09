<?php

use App\Models\AusstattungsmerkmalModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class AusstattungsmerkmalModelTest extends CIUnitTestCase
{
    public function testGroupedAliasDelegatesToCategoryGrouping(): void
    {
        $expected = ['Wohnung' => [['id' => 1, 'bezeichnung' => 'Balkon']]];
        $model = $this->getMockBuilder(AusstattungsmerkmalModel::class)
            ->onlyMethods(['getGroupedByKategorie'])
            ->getMock();

        $model->expects($this->once())
            ->method('getGroupedByKategorie')
            ->willReturn($expected);

        $this->assertSame($expected, $model->getGrouped());
    }
}
