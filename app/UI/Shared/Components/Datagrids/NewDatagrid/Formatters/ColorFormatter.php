<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\NewDatagrid\Formatters;

use App\Model\BaseEntity;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 * @extends BaseFormatter<T>
 */
class ColorFormatter extends BaseFormatter
{
    /**
     * @param T $entity
     */
    public function format(BaseEntity $entity, Html|string $value): Html|string
    {
        $color = $this->getEntityField($entity, $this->field);
        $color = ($color !== null && preg_match('/^[0-9A-Fa-f]{6}$/', $color) === 1) ? $color : 'AAAAAA';
        $swatch = Html::el('span', [
            'class' => 'color-box',
            'style' => "background-color: #{$color}",
        ]);

        $el = Html::el()->addHtml($swatch);
        return $this->addContent($value, $el);
    }
}
