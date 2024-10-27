<?php

// Função principal para obter estatísticas do minerador
function mstat_astrominer() {
    $url = 'http://localhost:44001/stats';

    // Iniciando a sessão cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retorna a resposta como string
    curl_setopt($ch, CURLOPT_HEADER, false); // Não inclui o cabeçalho na saída

    // Executando a requisição
    $stats_raw = curl_exec($ch);

    // Verificando se ocorreu um erro
    if (curl_errno($ch) || empty($stats_raw)) {
        echo "\033[33mFailed to read miner from $url\033[0m\n"; // Exibe mensagem de erro em amarelo
        curl_close($ch);
        return null;
    }

    // Fechando a sessão cURL
    curl_close($ch);

    // Separando os valores
    $stats_array = explode(" ", trim($stats_raw));

    // Atribuindo valores
    $hs = $stats_array[0] ?? 0; // Hashrate
    $uptime = $stats_array[1] ?? 0; // Uptime
    $ver = $stats_array[2] ?? ""; // Versão
    $acc = $stats_array[3] ?? 0; // Ações aceitas
    $rej = $stats_array[4] ?? 0; // Ações rejeitadas

    // Montando o Array de saída no formato desejado
    $output = [
        'uptime' => (int)$uptime,
        'speed' => [
            'total' => (int)$hs
        ],
        'shares' => [
            'total' => (int)$acc,
            'reject' => (int)$rej,
            'invalid' => 0 // Adicionei como 0 conforme você não mencionou outros valores
        ]
    ];

    return $output; // Retorna o array formatado
}

