<h1>Hack Miner - Modificação do MinerOS para Monitoramento do Deroluna</h1>

<h2>Visão Geral</h2>
<p>
    O Hack Miner é uma modificação do Astrominer do MinerOS que permite  monitorar o DeroLuna utilizando a API do MinerOS. Este script permite a coleta de estatísticas em tempo real sobre o desempenho dos minerador, facilitando a análise e otimização das operações de mineração.
</p>

<p>
    Nesta caso, vamos habilitar o modo hack do MinerOS, ativando o parâmetro em <code>SKIP_SYNC=true</code> no arquivo <code>nano /config/hack.txt</code>.
</p>

<p>
    Assim, é necessário reiniciar o MinerOS para ativar o modo. Após reiniciar, execute a screen <code> client</code>; nela deverá conter a advertência:
</p>
<blockquote>
    Sync WARNING Synchronization disabled by hack.txt SKIP_SYNC=1
</blockquote>
<p>que indica que o modo hack está ativo.</p>

<p>
    Agora, antes de instalar o Deroluna, precisamos desativar a função que substitui arquivos alterados no MinerOS. Apesar de o modo hack garantir que isso não aconteça, acaba ocorrendo.
</p>

<p>
    Vamos comentar a função de substituição <code>compare()</code> nos dois arquivos abaixo:
</p>
<ul>
    <li><code>/home/client/restore/sync.php</code></li>
    <li><code>/home/client/modules/syncService.php</code></li>
</ul>
<p>
    Para que fique no formato abaixo, comentando a comparação e substituição de diretórios de arquivos, para não apagar nosso Deroluna personalizado.
</p>

<pre><code>
private function compare($serverFiles, $systemFiles, $folderType, $folderDestinationPath)
{
    $hasChanges = false;

    if (!is_array($serverFiles) || !is_array($systemFiles)) {
        return false;
    }

/*
    foreach ($serverFiles as $filePath => $fileParams) {
        Log::info("File: ".Log::wrap($filePath, "bold"));

        if (isset($systemFiles[$filePath])) {
            if ($fileParams['crc32'] != $systemFiles[$filePath]['crc32']) {
                $hasChanges = true;
                Log::error(Log::wrap(" Error ", "red.reverse")." ".Log::wrap("CRC does not match", "red"));
                $this->statistics['changed_files_count'] += 1;
                $result = $this->download($filePath, $fileParams, $folderType, $folderDestinationPath);
                if($result) {
                    $this->statistics['downloaded_files_count'] += 1;
                } else {
                    $this->statistics['downloading_errors_count'] += 1;
                }
            } else if ($fileParams['perm'] != $systemFiles[$filePath]['perm']) {
                $hasChanges = true;
                Log::error("Wrong permissions, doing chmod (".$folderDestinationPath.$filePath.")");
                chmod($folderDestinationPath.$filePath, $fileParams['perm'] & 0xFFF);
            } else {
                Log::info(Log::wrap(" OK ", "green.reverse")." ".Log::wrap("Already the latest version", "dim"));
            }

            unset($systemFiles[$filePath]);
        } else {
            $hasChanges = true;
            Log::error("Not found");
            if ($fileParams['perm'] & 0x4000) {
                Log::info("Creating dir ".$filePath);
                mkdir($folderDestinationPath.$filePath, ($fileParams['perm'] & 0xFFF), 1);
            } else {
                $this->statistics['new_files_count'] += 1;
                $this->download($filePath, $fileParams, $folderType, $folderDestinationPath);
            }
        }
    }

    // Removing files
    foreach($systemFiles as $filePath => $fileParams) {
        if ($fileParams['perm'] & 0x4000) {
            continue;
        }

        Log::info("Removing old file: ".$filePath);
        if (unlink($folderDestinationPath . '/' . $filePath)) {
            $this->statistics['removed_files'] += 1;
        }
        unset($systemFiles[$filePath]);
    }

    // Removing dirs
    $dirs = array_reverse($systemFiles);
    foreach($dirs as $filePath => $fileParams) {
        if ($fileParams['perm'] & 0x4000) {
            Log::info("Removing old folder: ".$filePath);
            if (rmdir($folderDestinationPath . '/' . $filePath)) {
                $this->statistics['removed_dirs'] += 1;
            };
        }
        unset($systemFiles[$filePath]);
    } 
*/

    return $hasChanges;
}
</code></pre>

<h2>Baixando e Instalando o Deroluna</h2>
<p>
    Agora vamos baixar o Deroluna e descompactá-lo do diretório oficial.
</p>

<pre><code># Baixar o arquivo
curl -L -o deroluna-miner-linux-amd64.tar.gz https://github.com/DeroLuna/dero-miner/releases/download/v1.13-beta/deroluna-miner-linux-amd64.tar.gz

# Descompactar o arquivo
tar -xzvf deroluna-miner-linux-amd64.tar.gz
</code></pre>

<p>
    Agora vamos substituir o arquivo do Astrominer pelo script abaixo. Este script captura as variáveis enviadas pela API do MinerOS, não precisando que você as configure, e personaliza para executar o Deroluna. Quando você configurar a versão e o minerador na API Web do MinerOS, esta versão que estamos substituindo utilizará a carteira e a pool especificadas lá.
</p>

<pre><code>#!/bin/bash

# Definir valores padrão
# POOL="community-pools.mysrv.cloud:10300"
# WALLET="dero1qy3wt54g5p2reegnjx4m9dy3y0dwwdxfhalegenws20r80r885chjqgucq3t2"

API_LISTEN="127.0.0.1:44001"

# Verificar e capturar parâmetros da linha de comando
if [[ $@ =~ -r[[:space:]]+([^[:space:]]+) ]]; then
    POOL="${BASH_REMATCH[1]}"
fi

if [[ $@ =~ -w[[:space:]]+([^[:space:]]+) ]]; then
    WALLET="${BASH_REMATCH[1]}"
fi

# Função para mostrar uso do script
usage() {
    echo "Uso: $0 [-r POOL] [-w WALLET]"
    echo " -r POOL Especifica o pool de mineração (padrão: $POOL)"
    echo " -w WALLET Especifica o endereço da carteira (padrão: $WALLET)"
    exit 1
}

# Montar a linha de comando final
cmd="./deroluna -w $WALLET -d $POOL --api-listen $API_LISTEN"

echo "Iniciando o deroluna com o seguinte comando:"
echo "$cmd"

# Executar o comando em um loop infinito
while :
do
    $cmd
    sleep 1
done
</code></pre>

<p>
    Após criar o script, copie o arquivo descompactado do Deroluna para <code>/home/miners/astrominer/1.9.2.R5</code>. No diretório, terá o primeiro arquivo com o script Astromine para compatibilizar as variáveis da interface web e a execução automática do sistema e o segundo arquivo que é o minerador Deroluna.
</p>

<p>
    Depois, vamos modificar o script <code>astrominer.php</code> para conter nosso script de monitoramento, que é <code>script astromine.php</code>. No diretório <code>/home/client/mstat</code>, este script é que permite que o guarda da rig e o monitoramento da interface funcionem.
</p>

<h2>Funcionalidades</h2>
<ul>
    <li><strong>Coleta de Estatísticas em Tempo Real:</strong> O script coleta dados essenciais como hashrate, uptime, ações aceitas e rejeitadas.</li>
    <li><strong>Formatação de Saída:</strong> Os dados são apresentados em um formato de array PHP, tornando mais fácil a leitura e análise.</li>
    <li><strong>Exibição de Erros:</strong> Mensagens de erro são exibidas em cores diferentes no terminal para facilitar a identificação de problemas.</li>
</ul>

<h2>Estrutura do Script</h2>
<p>O script realiza as seguintes etapas:</p>

<ol>
    <li>
        <strong>Inicialização da Sessão cURL</strong>:<br>
        O script inicia uma sessão cURL para se conectar ao endpoint da API do Deroluna e solicitar estatísticas.
        <pre><code>$url = 'http://localhost:44001/stats';<br>$ch = curl_init($url);<br>curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);<br>curl_setopt($ch, CURLOPT_HEADER, false);</code></pre>
    </li>
    <li>
        <strong>Execução da Requisição</strong>:<br>
        Os dados são recuperados através da execução da requisição cURL.
        <pre><code>$stats_raw = curl_exec($ch);</code></pre>
    </li>
    <li>
        <strong>Tratamento de Erros</strong>:<br>
        Se ocorrer um erro, o script exibe uma mensagem de erro apropriada.
        <pre><code>if (curl_errno($ch) || empty($stats_raw)) {<br>&nbsp;&nbsp;echo "\033[33mFailed to read miner from $url\033[0m\n";<br>}</code></pre>
    </li>
    <li>
        <strong>Processamento de Dados</strong>:<br>
        Os dados brutos são processados e convertidos em um array associativo, facilitando a análise.
        <pre><code>$stats_array = explode(" ", trim($stats_raw));</code></pre>
    </li>
    <li>
        <strong>Formato de Saída</strong>:<br>
        Os dados são formatados em um array PHP, apresentando as informações relevantes.
        <pre><code>return [<br>&nbsp;&nbsp;'uptime' => (int)$uptime,<br>&nbsp;&nbsp;'speed' => [<br>&nbsp;&nbsp;&nbsp;&nbsp;'total' => (float)$hs,<br>&nbsp;&nbsp;],<br>&nbsp;&nbsp;'shares' => [<br>&nbsp;&nbsp;&nbsp;&nbsp;'total' => (int)$acc,<br>&nbsp;&nbsp;&nbsp;&nbsp;'reject' => (int)$rej,<br>&nbsp;&nbsp;&nbsp;&nbsp;'invalid' => 0,<br>&nbsp;&nbsp;],<br>];</code></pre>
    </li>
</ol>

<h2>Exemplo de Uso</h2>
<p>Ao executar o script, o resultado será um array PHP formatado assim:</p>
<pre><code>Array<br>(<br>&nbsp;&nbsp;[uptime] => 41<br>&nbsp;&nbsp;[speed] => Array<br>(&nbsp;&nbsp;&nbsp;&nbsp;[total] => 27124.40<br>        )<br>&nbsp;&nbsp;[shares] => Array<br>(&nbsp;&nbsp;&nbsp;&nbsp;[total] => 827<br>&nbsp;&nbsp;&nbsp;&nbsp;[reject] => 51<br>&nbsp;&nbsp;&nbsp;&nbsp;[invalid] => 0<br>        )<br>)</code></pre>

<h2>Contribuições</h2>
<p>Sinta-se à vontade para contribuir com melhorias, correções ou novas funcionalidades. Para contribuir, faça um fork deste repositório, crie uma nova branch e envie um pull request.</p>

<h2>Licença</h2>
<p>Este projeto está licenciado sob a MIT License - veja o arquivo <a href="LICENSE">LICENSE</a> para detalhes.</p>
