<?php
    function fazAlgoArriscado() {}
    try {
        fazAlgoArriscado();
    } catch (MinhaException $e) {
        echo "Erro: ".$e->getMessage();
    } finally {
        echo "Fim";
    }