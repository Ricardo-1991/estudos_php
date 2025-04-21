<?php
    $num = 1;

    $result = $num ?? 2; // Se o primeiro for null, usa o segundo

    $num2 = 4;

    // operador Spaceship <=> Compara dois valores num único operador.
    $array = [1,3,2,5,4];
    //usort modifica o array original e retorna apenas true/false
    usort($array, fn($firstValue, $secondValue) => $firstValue <=> $secondValue);

    foreach($array as $value){
        echo $value;
    }