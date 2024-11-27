<?php declare(strict_types=1);

class FibonacciSequence implements Iterator {
    public function __construct() {}

    public static function first(int $n): self {}

    public static function range(int $start, int $length = -1): self {}

    public function current(): mixed {}

    public function key(): mixed {}

    public function next(): void {}

    public function rewind(): void {}

    public function valid(): bool {}

    private function fibonacci(int $n): int {}
}
?>
