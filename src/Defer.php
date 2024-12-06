<?php declare(strict_types=1);

class Callback {
    public function __construct(
        private mixed $cb,
        private array $args = [],
    ) {}

    public function call(): void {
        call_user_func_array($this->cb, $this->args);
    }
}

class Defer {
    private mixed $cb_defer ;

    public function __construct() {}

    /* Ajout d'un constructeur statique --> plus besoin après la question 7 ?
    public static function init(): self {
        return new self();
    } */

    /*
     * Méthode pour passer le callable dans la méthode defer
     * plutôt que pendant la création de l'instance
     *
     */
    public function __invoke(callable $cb, ...$args): void {
        $this->cb_defer[] = new Callback($cb, $args);
    }

    public function __destruct() {
        // Mise en place du LIFO/DEPS
        // On traverse le tableau (la pile?) dans le sens inverse
        while (!empty($this->cb_defer)) {
            $instanceDeCallback = array_pop($this->cb_defer);
            // On fait un appel au callable
            $instanceDeCallback->call();
        }

    }
}

// Example

$defer = new Defer();

echo "Pile d'exemple : " . PHP_EOL;

$defer(function (int $chiffre = 1) {
    echo $chiffre . PHP_EOL;
});

$defer(function (int $chiffre = 2) {
    echo $chiffre . PHP_EOL;
});

$defer(function (int $chiffre = 3) {
    echo $chiffre . PHP_EOL;
});

