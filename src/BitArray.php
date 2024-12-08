<?php declare(strict_types=1);


class BitArrayIterator implements Iterator {
    private int $position = 0;
    private array $array;
    public function __construct() {
        $this->array = [];
        $this->position = 0;
    }
    public function current(): int {
        return $this->array[$this->position];
    }
    public function key(): int {
        return $this->position;
    }
    public function next(): void {
        $this->position++;
    }
    public function rewind(): void {
        $this->position = 0;
    }
    public function valid(): bool {
        return isset($this->array[$this->position]);
    }
}

class BitArray implements ArrayAccess, Countable, IteratorAggregate, Stringable {
    private const BYTE_SIZE = 8;
    private const INT_SIZE = PHP_INT_SIZE * self::BYTE_SIZE;

    // Tableau pour stocker les bits
    public array $bits;
    public function __construct() {
        $this->bits = [];
    }

    public static function fromInt(int $from){
        $bitArray = new self();
        $bitArray->bits = array_map('intval', str_split(decbin($from)));
        return $bitArray;
    }

    public static function fromString(string $from): self {
        $bitArray = new self();

        // On vérifie que 0b est présent => si c'est le cas, on le retire de la string
        if(str_starts_with($from, '0b')){
            $from = substr($from, 2);
        }

        // On vérifie que la string elle celle d'un bit
        $regex = '/^[01]+$/';
        if(!preg_match($regex, $from)){
            throw new InvalidArgumentException('La chaîne doit être uniquement composée de 0 et de 1');
        }
        // On réutilise fromInt() pour transformer en BitArray
        $value = bindec($from);

        return self::fromInt($value);
    }

    public function slice(int $start = 0, int $length = -1): self {
        // Si la longueur est négative, on remplace tous les bits de l'offset jusqu'au bout
        $length = $length === -1 ? count($this->bits) - $start : $length;

        // On "découpe" une partie du tableau de bits
        $slicedBits = array_slice($this->bits, $start, $length);

        $BitArray = new self();
        $BitArray->bits = array_values($slicedBits);

        return $BitArray;
    }

    public function set(array $bits, int $start = 0): void {
        array_splice($this->bits, $start, count($bits), $bits);
    }

    public function unset(int $start, int $length = -1): void {

        // Si la longueur est négative, on remplace tous les bits de l'offset jusqu'au bout
        $length = $length === -1 ? count($this->bits) - $start : $length;

        // On crée un tableau $replacement dans lequel on mettra la nouvelle version du bit,
        // après avoir remplacé les éléments voulus par des 0.
        $replacement = array_fill(0, $length, 0);
        array_splice($this->bits, $start, $length, $replacement);
    }

    /* Méthodes d'ArrayAccess */
    public function offsetExists(mixed $offset): bool {
        return isset($this->bits[$offset]);
    }

    public function offsetGet(mixed $offset): int {
        return $this->bits[$offset] ?? 0;
    }

    public function offsetSet(mixed $offset, mixed $value): void{
        if (is_null($offset)) {
            $this->bits[] = $value;
        } else{
            $this->bits[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void {
        unset($this->bits[$offset]);
    }

    /****************************************************************/

    /* Méthode de Countable*/
    public function count(): int {
        return count($this->bits);
    }

    /* Méthode d'IteratorAggregate */
    public function getIterator(): BitArrayIterator {
        return new BitArrayIterator();
    }

    /* Méthode de Stringable */
    public function __toString(): string {
        return implode('', $this->bits);
    }
}

// Série de tests pour les différentes méthodes de BitArray()

// Pour compter le nombre de bits
echo "\033[1;97m Pour compter le nombre de bits dans un tableau\033[0m" . PHP_EOL;
$bits = new BitArray();
$bits->bits = [1, 0, 1, 0, 0, 0, 1];
echo  'Il y a ' . $bits->count() . ' bits dans le tableau.' . PHP_EOL;

echo "\033[1;36m==============================================================\033[0m" . PHP_EOL;

// Exemples de conversions
echo "\033[1;97mExemples de conversions\033[0m" . PHP_EOL;

$n = 9;
$s_ob = '0b1000';
$s = '1000';

$bitsFromInt = BitArray::fromInt($n);
$bitsFromString = BitArray::fromString($s);
$bitsFromString = BitArray::fromString($s_ob);
echo "Conversion en bits de $n : " . $bitsFromInt . PHP_EOL; ;
echo "Conversion en bits de $s : " . $bitsFromString . PHP_EOL;
echo "Conversion en bits de $s_ob : " . $bitsFromString . PHP_EOL;

echo "\033[1;36m==============================================================\033[0m" . PHP_EOL;

echo "\033[1;97mExemple d'unset n°1'\033[0m" . PHP_EOL;
$bitArray = new BitArray();
$bitArray->bits = [1, 0, 1, 1, 0, 1, 1];
echo "Avant unset : " . implode('', $bitArray->bits) . PHP_EOL;

$bitArray->unset(2, 3); // Met à zéro les bits aux indices 2, 3, 4
echo "Après unset : " . implode('', $bitArray->bits) . PHP_EOL;

echo "\033[1;36m==============================================================\033[0m" . PHP_EOL;

echo "\033[1;97mExemple d'unset n°2'\033[0m" . PHP_EOL;
$bitArray = new BitArray();
$bitArray->bits = [1, 0, 1, 1, 0, 1, 1];
echo "Avant unset : " . implode('', $bitArray->bits) . PHP_EOL;

$bitArray->unset(4); // Met à zéro les bits à partir de l'indice 4
echo "Après unset : " . implode('', $bitArray->bits) . PHP_EOL;

echo "\033[1;36m==============================================================\033[0m" . PHP_EOL;

echo "\033[1;97mExemple de slice n°1'\033[0m" . PHP_EOL;

$bitArray = new BitArray();
$bitArray->bits = [1, 0, 1, 1, 0, 1, 1];
echo "Bits originaux : " . implode('', $bitArray->bits) . PHP_EOL;

$sliced = $bitArray->slice(2, 3); // Extraire les bits aux indices 2, 3, 4
echo "Après le slice : " . implode('', $sliced->bits) . PHP_EOL;

echo "\033[1;36m==============================================================\033[0m" . PHP_EOL;

echo "\033[1;97mExemple de slice n°2'\033[0m" . PHP_EOL;
$bitArray = new BitArray();
$bitArray->bits = [1, 0, 1, 1, 0, 1, 1];
echo "Bits originaux : " . implode('', $bitArray->bits) . PHP_EOL;

$sliced = $bitArray->slice(4); // Extraire les bits à partir de l'indice 4 jusqu'à la fin
echo "Après le slice : " . implode('', $sliced->bits) . PHP_EOL;

echo "\033[1;36m==============================================================\033[0m" . PHP_EOL;

echo "\033[1;97mExemple de slice n°3'\033[0m" . PHP_EOL;
$bitArray = new BitArray();
$bitArray->bits = [1, 0, 1, 1];
echo "Bits originaux : " . implode('', $bitArray->bits) . PHP_EOL;

$sliced = $bitArray->slice(2, 10); // Extraire à partir de l'indice 2, longueur 10
echo "Après le slice : " . implode('', $sliced->bits) . PHP_EOL;

