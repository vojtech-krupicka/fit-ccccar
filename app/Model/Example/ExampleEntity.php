<?php declare(strict_types=1);

namespace App\Model\Example;

use App\Model\BaseEntity;
use Nette\Database\Table\ActiveRow;

/**
 * Example entity: delete the Example feature (this folder, the migrations 0001/0002 and
 * tests/Model/ExampleFacadeTest.php) once you have a real model.
 */
final class ExampleEntity extends BaseEntity
{
    public function __construct(
        public ?int $id = null,
        public string $name = '',
        public \DateTime $created = new \DateTime(),
    ) {}

    public static function fromActiveRow(ActiveRow $row): static
    {
        return new self(
            id: (int) $row['id'],
            name: (string) $row['name'],
            created: $row['created'],
        );
    }
}
