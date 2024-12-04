<?php declare(strict_types=1);

class FibonacciSequence implements Iterator {
    //private array $sequence; /!\ Plus besoin car on est en lazy evaluation
    private int $position = 0;
    private int $max;

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

    public function valid(): bool {
        // On indique ici que la limite de la séquence est égale au nombre de F voulu, pour éviter tout problème.
        return $this->position <= $this->max;
    }

    // On calcula la séquence, en prenant en compte les cas particuliers F_0, F_1 et F_2
    // qui valent respectivement 0, 1 et 1
    private function fibonacci(int $n): int {
        if($n == 0){
            return 0;
        } elseif ($n == 1 || $n ==2){
            return 1;
        } else{
            return ($this->fibonacci($n - 1) + $this->fibonacci($n - 2));
        }
    }
}

// Pour la mémorisation (point 5. de l'exo)

// Valeur de test
$n = 10;

$seq = new FibonacciSequence($n);
foreach($seq as $key => $value){
    echo "F_[$key] = $value\n";
}



