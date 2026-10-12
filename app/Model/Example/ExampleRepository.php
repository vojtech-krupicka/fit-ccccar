<?php declare(strict_types=1);

namespace App\Model\Example;

use App\Model\BaseRepository;

class ExampleRepository extends BaseRepository
{
    public function getById(int $id): ?ExampleEntity
    {
        $row = $this->dbconn->table('example_items')->get($id);

        return $row ? ExampleEntity::fromActiveRow($row) : null;
    }

    /**
     * @return list<ExampleEntity>
     */
    public function getAll(): array
    {
        // return ExampleEntity::fromSelection($this->dbconn->table('example_items')->order('name ASC'));
        return [];
    }

    public function persist(ExampleEntity $item): void
    {
        $data = ['name' => $item->name, 'created' => $item->created];

        if ($item->id === null) {
            $row = $this->dbconn->table('example_items')->insert($data);
            $item->id = (int) $row['id'];
        } else {
            $this->dbconn->table('example_items')->where('id', $item->id)->update($data);
        }
    }

    public function delete(ExampleEntity $item): void
    {
        $this->dbconn->table('example_items')->where('id', $item->id)->delete();
    }
}
