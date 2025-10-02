<?php
set_time_limit(0);
ob_implicit_flush();

$host = "0.0.0.0";
$port = 9000;

$server = stream_socket_server("tcp://$host:$port", $errno, $errstr);
if (!$server) {
    die("Error creando servidor: $errstr ($errno)\n");
}
echo "Servidor WebSocket escuchando en ws://$host:$port\n";

$clients = [];

while (true) {
    $read = $clients;
    $read[] = $server;

    if (stream_select($read, $write, $except, 0, 200000)) {
        if (in_array($server, $read)) {
            $conn = stream_socket_accept($server);
            if ($conn) {
                $request = fread($conn, 1500);
                if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $request, $matches)) {
                    $key = trim($matches[1]);
                    $accept = base64_encode(sha1($key . "258EAFA5-E914-47DA-95CA-C5AB0DC85B11", true));
                    $upgrade = "HTTP/1.1 101 Switching Protocols\r\n" .
                               "Upgrade: websocket\r\n" .
                               "Connection: Upgrade\r\n" .
                               "Sec-WebSocket-Accept: $accept\r\n\r\n";
                    fwrite($conn, $upgrade);
                    $clients[] = $conn;
                    echo "Nuevo cliente conectado\n";
                }
            }
            unset($read[array_search($server, $read)]);
        }

        foreach ($read as $client) {
            $data = fread($client, 1500);
            if (!$data) { 
                unset($clients[array_search($client, $clients)]);
                fclose($client);
                echo "Cliente desconectado\n";
                continue;
            }

            $decoded = unmask($data);
            echo "Mensaje recibido: $decoded\n";

            foreach ($clients as $sendto) {
                if ($sendto != $server && $sendto != $client) {
                    fwrite($sendto, mask($decoded));
                }
            }
        }
    }
}

function unmask($payload) {
    $length = ord($payload[1]) & 127;
    if ($length == 126) {
        $masks = substr($payload, 4, 4);
        $data = substr($payload, 8);
    } elseif ($length == 127) {
        $masks = substr($payload, 10, 4);
        $data = substr($payload, 14);
    } else {
        $masks = substr($payload, 2, 4);
        $data = substr($payload, 6);
    }
    $text = "";
    for ($i = 0; $i < strlen($data); ++$i) {
        $text .= $data[$i] ^ $masks[$i % 4];
    }
    return $text;
}

function mask($text) {
    $b1 = 0x81;  
    $len = strlen($text);
    if ($len <= 125) {
        return chr($b1) . chr($len) . $text;
    } elseif ($len <= 65535) {
        return chr($b1) . chr(126) . pack("n", $len) . $text;
    } else {
        return chr($b1) . chr(127) . pack("xxxxN", $len) . $text;
    }
}
