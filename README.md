<h1>Hack Miner - Modificação do Mineros para Deroluna</h1>

<h2>Visão Geral</h2>
<p>O Hack Miner é um script modificado do Mineros projetado para monitorar mineradores de criptomoedas utilizando a API do Deroluna. Este script permite a coleta de estatísticas em tempo real sobre o desempenho dos mineradores, facilitando a análise e otimização das operações de mineração.</p>

<h2>Funcionalidades</h2>
<ul>
    <li><strong>Coleta de Estatísticas em Tempo Real</strong>: O script coleta dados essenciais como hashrate, uptime, ações aceitas e rejeitadas.</li>
    <li><strong>Formatação de Saída</strong>: Os dados são apresentados em um formato de array PHP, tornando mais fácil a leitura e análise.</li>
    <li><strong>Exibição de Erros</strong>: Mensagens de erro são exibidas em cores diferentes no terminal para facilitar a identificação de problemas.</li>
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
