<?php declare(strict_types=1);

class FibonacciSequence implements Iterator {
    //private array $sequence; /!\ Plus besoin car on est en lazy evaluation
    private int $position = 0;
    private int $max; // Pour la limite

    // Pour le bonus (je crois) : utiliser et partager un seul et même cache entre toutes les instances de la classe
    private static array $cache = [];

    // Constructeur pour générer & sauvegarder les premiers nombres de la séquence
    public function __construct(int $n) {
        $this->position = 0;

        // On s'assure que la série ne soit pas négative
        if($n < -1){
            throw new InvalidArgumentException("La séquence ne peut pas être négative!");
        }

        $this->max = $n; // Limite pour des soucis d'optimisation & pour utiliser la méthode valid()
    }
    public static function first(int $n): self {
        $first = new self($n);
    }

    public static function range(int $start, int $length = -1): self {

    }

    public function current(): mixed {
        return $this->fibonacci($this->position);
    }

    public function key(): mixed {
        return $this->position;
    }

    public function next(): void {
        $this->position++;
    }

    public function rewind(): void {
        $this->position = 0;
    }

    // C'est dans cette méthode que l'on met en place les 2 modes : infini ou limite/borne finie
    public function valid(): bool {
        // Pour pouvoir calculer soit à l'infini soit à une borne définie (6.)
        // -1 pour l'infini, $max pour la borne
        if($this->max !== -1 && $this->position >= $this->max) {
            return false;
        }

        return true;
    }

    // On calcule la la séquence, en prenant en compte les cas particuliers F_0, F_1 et F_2
    // qui valent respectivement 0, 1 et 1
    private function fibonacci(int $n): int {
        if (isset(self::$cache[$n])) {
            echo "F_$n déjà en mémoire. ";
            return self::$cache[$n];

        }

        if($n === 0){
            self::$cache[$n] = 0;
        } elseif ($n === 1 || $n === 2){
            self::$cache[$n] = 1;
        } else{

            // On regarde si n-1 a déjà été calculé
            if(!isset(self::$cache[$n-1])){
                self::$cache[$n - 1] = $this->fibonacci($n - 1);
            }

            // De la même manière, on regarde si n-2 a déjà été calculé
            if (!isset(self::$cache[$n - 2])) {
                self::$cache[$n - 2] = $this->fibonacci($n - 2);
            }

            echo "on calcule pour n = $n : ";
            self::$cache[$n] = self::$cache[$n - 1] + self::$cache[$n - 2];
        }

        return self::$cache[$n];
    }

    // Méthode pour vider le cache, uniquement pour les exemples ci-dessous
    public static function resetCache(): void {
        self::$cache = [];
    }
}

// Exemples de séquences

// Avec une limite finie
echo "\033[1;97mExemple d'une séquence avec limite finie\033[0m" . PHP_EOL;
$sequence_finie = new FibonacciSequence(6);
foreach($sequence_finie as $key => $value){
    echo "Pour n = \033[1;31m$key\033[0m ➔ F_\033[1;31m$key\033[0m = \033[1;32m$value\033[0m" . PHP_EOL;
}
echo "\033[1;36m==============================================================\033[0m" . PHP_EOL;

FibonacciSequence::resetCache();

// Avec une limite limite infinie
echo "\033[1;97mExemple d'une séquence avec limite infinie\033[0m" . PHP_EOL;
$count = 0;
$sequence_inf = new FibonacciSequence(-1);
foreach($sequence_inf as $key => $value) {
    echo "Pour n = \033[1;31m$key\033[0m ➔ F_\033[1;31m$key\033[0m = \033[1;32m$value\033[0m" . PHP_EOL;
    $count++;
    if ($count >= 12) break;
}
echo "\033[1;36m==============================================================\033[0m" . PHP_EOL;

FibonacciSequence::resetCache();

// Séquence de taille 0
echo "\033[1;97mExemple d'une séquence de taille 0\033[0m" . PHP_EOL;
$sequence_zero = new FibonacciSequence(0);
foreach($sequence_zero as $key => $value) {
    echo "Pour n = \033[1;31m$key\033[0m ➔ F_\033[1;31m$key\033[0m = \033[1;32m$value\033[0m" . PHP_EOL;
    $count++;
    if ($count >= 20) break;
}
echo "\033[1;36m==============================================================\033[0m" . PHP_EOL;
FibonacciSequence::resetCache();

// En utilisant le cache
echo "\033[1;97mExemple d'utilisation du cache\033[0m" . PHP_EOL;
echo "\033[1;97mSéquence n°1 :\033[0m" . PHP_EOL;
$sequence_cache_1 = new FibonacciSequence(5);
foreach($sequence_cache_1 as $key => $value) {
    echo "Pour n = \033[1;31m$key\033[0m ➔ F_\033[1;31m$key\033[0m = \033[1;32m$value\033[0m" . PHP_EOL;
}

echo "\033[1;97mSéquence n°2 :\033[0m" . PHP_EOL;
$sequence_cache_2 = new FibonacciSequence(8);
foreach($sequence_cache_2 as $key => $value) {
    echo "Pour n = \033[1;31m$key\033[0m ➔ F_\033[1;31m$key\033[0m = \033[1;32m$value\033[0m" . PHP_EOL;
}
echo "\033[1;36m==============================================================\033[0m" . PHP_EOL;

FibonacciSequence::resetCache();

// Séquence de taille négative => on vérifie qu'une erreur se produit
echo "\033[1;97mExemple d'une séquence de taille négative, pour vérifier qu'une erreur se produit bien\033[0m" . PHP_EOL;foreach($sequence_negative as $key => $value) {
    echo "Pour n = \033[1;31m$key\033[0m ➔ F_\033[1;31m$key\033[0m = \033[1;32m$value\033[0m" . PHP_EOL;
}
echo "\033[1;36m==============================================================\033[0m" . PHP_EOL;