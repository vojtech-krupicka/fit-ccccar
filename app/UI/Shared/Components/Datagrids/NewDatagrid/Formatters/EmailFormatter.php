<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\NewDatagrid\Formatters;

use App\Model\BaseEntity;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 * @extends BaseFormatter<T>
 */
class EmailFormatter extends BaseFormatter
{
    /**
     * @param T $entity
     */
    public function format(BaseEntity $entity, Html|string $value): Html|string
    {
        $email = $this->getEntityField($entity, $this->field);

        $el = Html::el('a')
            ->href('mailto:' . $email)
            ->title($email);

        return $this->setContent($value, $el);
    }
}
