#!/bin/bash +x

 
# Exemplo de BashScript para envio de mensagem via para telegram via CLI
# Possibilidades são inúmeras, podemos monitorar:
# ** Tentativas de Acesso Indevido;
# ** Ocupação de Disco;
# ** carga de CPU elevada;
# ** etc...;

# Por Woney.branga@gmail.com

HASH="000000:11111111111111111111"
CHATID="1234"
TIME="10"
URL="https://api.telegram.org/bot$HASH/sendMessage"
TEXT="Hello world"
 
curl -s --max-time $TIME -d "chat_id=$CHATID&disable_web_page_preview=1&text=$TEXT" $URL >/dev/null
