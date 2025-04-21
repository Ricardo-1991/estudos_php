<?php   
    // UMA CLASSE SIMPLES
    class Pessoa {
        public $nome;
    
        public function __construct($nome) {
            $this->nome = $nome;
        }
    
        public function falar() {
            return "Olá, eu sou " . $this->nome;
        }
    }
    
    // Instanciando e usando
    $p = new Pessoa("Alice");
    echo $p->falar();  // Saída: Olá, eu sou Alice


// -- CLASSES COM DEFINIÇÃO DE MODIFICADORES DE VISIBILIDADE E ACESSO
class Exemplo {
    public $aberto = "visível para todos";
    protected $heranca = "visível em subclasses";
    private $interno = "visível apenas nesta classe";

    public function testar() {
        echo $this->aberto;
        echo $this->heranca;
        echo $this->interno;
    }
}

// HERANÇA
class SubExemplo extends Exemplo {
    public function testarSub() {
        echo $this->aberto;    // OK (public)
        echo $this->heranca;   // OK (protected herdado)
        // echo $this->interno; // ERRO (private não acessível)
    }
}

trait Greetings {
    public function greet($name) {
        echo "Olá, mundo!", $name;
    }
}

class Person {
    use Greetings;
    private $_name;

    public function __construct($name) {
        $this->_name = $name;
    }

    public function getName() {
        return $this->_name;
    }
}

$newPerson = new Person("Bob");
$personName = $newPerson->getName();
$newPerson->greet($personName);
