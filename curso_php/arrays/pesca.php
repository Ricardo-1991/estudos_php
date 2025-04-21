<?php 
    $a = [1, 2, 3, 4, 5];
    print_r($a);

    $b = ["nome" => "ricardo", "idade" => "34"]; //Array Associativo
    $name = "Ricardo";

    echo "<br><br>";
    echo "Olá, meu nome é: {$name}"; // interpolação
    echo "<br><br>";
    print_r($b);
    echo "<br><br>";

    echo $b['nome']; // acessando apenas uma chave
    echo "<br><br>";
    $isOdd = array_filter($a, fn($num) => $num % 2 === 0); //filtrar
    print_r($isOdd);

    echo "<br><br>";
    $sum = array_reduce($a, function($acc, $value){
        return $acc + $value; // REDUCE
    }, 0);  
    echo $sum;

    echo "<br><br>";

    $isTriple = array_map(fn($value) => $value * 3, $a);
    print_r($isTriple); //MAP
    echo "<br><br>";


// FUNÇÕES -----------------------

    // Função nomeada em PHP
    function soma(int $a, int $b) { // TIPADOS
        return $a + $b;
    }

    // Função anônima
    $multiplica = function($a, $b) { // SEM TIPO EXPLÍCITO
        return $a * $b;
    };

    // Arrow function (PHP 7.4+)
    $dobra = fn($num) => $num * 2;

    //Função com parâmetro padrão
    function saudacao($nome = "Visitante") {
        return "Olá, $nome";
    }

// ARRAYS----------------------

    // Adicionar e remover elementos
    array_push($array, "elemento1", "elemento2");  // Adiciona ao final
    array_pop($array);                            // Remove e retorna o último elemento
    array_unshift($array, "primeiro");            // Adiciona ao início
    array_shift($array);                          // Remove e retorna o primeiro elemento

    // Combinação de arrays
    array_merge($array1, $array2);                // Combina arrays
    array_combine($chaves, $valores);             // Cria array usando chaves e valores

    // Extração de informações
    count($array);                                // Conta elementos
    array_keys($array);                           // Retorna todas as chaves
    array_values($array);                  // Retorna todos os valores

    //Transformação
    array_map(fn($x) => $x * 2, $array);          // Transforma cada elemento
    array_filter($array, fn($x) => $x > 3);       // Filtra elementos
    array_reduce($array, fn($acc, $val) => $acc + $val, 0); // Reduz a um valor

    // Outras comuns
    array_slice($array, 2, 3);                    // Extrai uma parte do array
    array_chunk($array, 2);                       // Divide em grupos menores
    implode(", ", $array);                        // Junta elementos com separador
    explode(",", $string); 
    
    //Busca e Verificação
    in_array("agulha", $palheiro);                // Verifica se valor existe
    array_key_exists("chave", $array);            // Verifica se chave existe
    array_search("valor", $array);                // Encontra chave de um valor
    isset($array["chave"]);   // Verifica se existe e não é null          

    //Ordenação
    sort($array);                                 // Ordena em ordem crescente
    rsort($array);                                // Ordena em ordem decrescente
    asort($array);                                // Mantém associação chave-valor
    ksort($array);                                // Ordena pelas chaves
    usort($array, function($a, $b) {// Ordenação personalizada
        return $a <=> $b;
    });

    //Operações entre Arrays
    array_diff($array1, $array2);                 // Elementos de $array1 que não estão em $array2
    array_intersect($array1, $array2);            // Elementos comuns
    array_unique($array);                         // Remove duplicados
    array_column($registros, 'nome');             // Extrai coluna de arrays multidimensionais
    array_rand($array, 2);                        // Retorna chaves aleatórias
    
    