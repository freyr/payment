<?php
declare(strict_types=1);

namespace Freyr\Exchange\ReadModel;

readonly class StocksReadModelRepository
{

    public function getList(): array
    {
        $sql = 'select * from stocks';
        $result =  $this->db->fetchAllAssociative($sql);
        $list = [];
        foreach ($result as $row) {
            $list[$row['name']] = Uuid::fromBinary($row['id'])->toRfc4122();
        }

        return $list;
    }
}
