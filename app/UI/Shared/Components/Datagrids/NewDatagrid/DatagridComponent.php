<?php declare(strict_types=1);

// namespace App\UI\Shared\Components\Datagrids\NewDatagrid;

// use App\Model\BaseEntity;

// abstract class DatagridComponent
// {
//     protected ?string $field;

//     /**
//      * @param T $entity
//      */
//     protected function getEntityValue(BaseEntity $entity): mixed
//     {
//         $parts = explode('.', $this->field);
//         $current = $entity;
//         foreach ($parts as $part) {
//             if ($current === null) {
//                 return null;
//             }
//             $current = $current->{$part} ?? null;
//         }

//         return $current;
//     }
// }
