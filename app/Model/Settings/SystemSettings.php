<?php declare(strict_types=1);

namespace App\Model\Settings;

final class SystemSettings
{
    public readonly ?int $yearEnd;

    public function __construct(
        public readonly string $author,
        public readonly string $email,
        public readonly int $yearStart,
        public readonly bool $debugMode,
    ) {
        $now = new \DateTimeImmutable();
        $year = (int) $now->format('Y');
        $this->yearEnd = $this->yearStart != $year ? $year : null;
    }
}
