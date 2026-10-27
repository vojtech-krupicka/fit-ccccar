<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\NewDatagrid\Actions;

use App\Model\BaseEntity;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 * @extends BaseAction<T>
 */
class Signal extends BaseAction
{
    /**
     * @param T $entity
     * @return Html
     */
    public function render(BaseEntity $entity): Html
    {
        $link = $this->getLink($entity);
        $icon = $this->getIcon($entity);
        $title = $this->title ?? '';

        return Html::el('a', [
            'class' => self::DEFAULT_ACTION_CSS,
        ])
            ->href($link)
            ->title($title)
            ->setHtml($icon);
    }
}
