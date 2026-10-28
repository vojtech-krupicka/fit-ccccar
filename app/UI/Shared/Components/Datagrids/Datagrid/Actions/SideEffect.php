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
    protected ?string $confirmMsg = null;

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
            $attrs['onclick'] = "return confirm('{json_encode($this->confirmMsg)}');";
        }

        return Html::el('button', $attrs)
            ->title($title)
            ->setHtml($icon);
    }

    public function setConfirmMsg(string $confirmMsg): static
    {
        $this->confirmMsg = $confirmMsg;
        return $this;
    }
}
