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
        if($n < 0){
            throw new InvalidArgumentException("La séquence ne peut pas être négative!");
        }

        $this->max = $n; // Limite pour des soucis d'optimisation & pour utiliser la méthode valid()

        /*
        // On génère les premiers nombres de la séquence /!\ Plus besoin car on ne se base plus sur $sequence
        for ($i = 0; $i < $n; $i++) {
            $this->sequence[] = $this->fibonacci($i);
            $this->position++;
        }
        */

    }
    public static function first(int $n): self {

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
        // On indique ici que la limite de la séquence est égale au nombre de F voulu, pour éviter tout problème.
        return $this->position <= $this->max;
    }

    // On calcula la séquence, en prenant en compte les cas particuliers F_0, F_1 et F_2
    // qui valent respectivement 0, 1 et 1
    private function fibonacci(int $n): int {

        echo "Mémoire AVANT calcul pour F_$n: " . implode(', ', self::$cache) . "\n";
        // On regarde si des nombres calculés sont déjà dans le cache.
        // Si c'est le cas,
        if (isset(self::$cache[$n] )) {
            echo "F_$n est déjà calculée! \n";
            return self::$cache[$n];
        }

        if($n === 0){
            self::$cache[$n] = 0;
        } elseif ($n === 1 || $n === 2){
            self::$cache[$n] = 1;
        } else{

            // On regarde si n-1 a déjà été calcul
            if(!isset(self::$cache[$n-1])){
                echo "$n - 1 déjà en mémoire \n";
                self::$cache[$n - 1] = $this->fibonacci($n - 1);
            }

            // De la même manière, on regarde si n-2 a déjà été calculé
            if (!isset(self::$cache[$n - 2])) {
                echo "$n - 2 déjà en mémoire \n";
                self::$cache[$n - 2] = $this->fibonacci($n - 2);
            }

            echo "on calcule pour n = $n \n";
            self::$cache[$n] = self::$cache[$n - 1] + self::$cache[$n - 2];
        }


        echo "Mémoire APRÈS calcul pour F_$n: " . implode(', ', self::$cache) . "\n";


        return self::$cache[$n];
    }
}

// Valeur de test
$n = 10;

// On crée une nouvelle séquence
$sequence = new FibonacciSequence($n);
foreach($sequence as $key => $value){
    if ($key > 15) break;
    echo "Pour n = $key => F_$key = $value\n";
}


