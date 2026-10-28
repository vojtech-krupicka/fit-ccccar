<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid\Columns;

use App\Model\Vehicle\Color\VehicleColorEntity;
use App\Model\BaseEntity;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 * @extends BaseColumn<T>
 */
class ColorColumn extends BaseColumn
{
    public function getCellContent(BaseEntity $entity): Html|null
    {
        $value = $this->getFieldValue($entity);
        if (!$value instanceof VehicleColorEntity) {
            return null;
        }

        $hex = ($value->hex !== null && preg_match('/^[0-9A-Fa-f]{6}$/', $value->hex) === 1) ? $value->hex : 'AAAAAA';

        $swatch = Html::el('span', [
            'class' => 'color-box',
            'style' => "background-color: #{$hex}",
        ]);

        return Html::el()
            ->addHtml($swatch)
            ->addText(" {$value->name}");
    }
}
