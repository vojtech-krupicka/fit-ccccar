<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid\Actions;

use App\Model\BaseEntity;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 * @extends BaseAction<T>
 */
class SideEffect extends BaseAction
{
    /** @var (\Closure(T): string)|string|null */
    protected \Closure|string|null $confirmMsg = null;

    /**
     * @param T $entity
     * @return Html
     */
    public function render(BaseEntity $entity): Html
    {
        $link = $this->getLink($entity);
        $icon = $this->getIcon($entity);
        $title = $this->title ?? '';
        $attrs = [
            'class' => self::DEFAULT_ACTION_CSS,
            'type' => 'submit',
            'form' => 'postForm',
            'formaction' => $link,
        ];

        if ($this->confirmMsg !== null) {
            $attrs['data-confirm'] = $this->confirmMsg instanceof \Closure ? ($this->confirmMsg)($entity) : $this->confirmMsg;
            $attrs['onclick'] = 'return confirm(this.dataset.confirm);';
        }

        return Html::el('button', $attrs)
            ->title($title)
            ->setHtml($icon);
    }

    /**
     * @param (\Closure(T): string)|string|null $confirmMsg
     */
    public function setConfirmMsg(\Closure|string|null $confirmMsg): static
    {
        $this->confirmMsg = $confirmMsg;
        return $this;
    }
}
