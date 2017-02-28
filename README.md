# INTERACAO COM TELEGRAM...

#### Primeiros passos:
 * Procurar no Telegram o usuário "BotFather" e iniciar uma conversa com este...
    * Criar bot usando comando /newbot
    * Definir nome bot, exemplo "NossoPrimeiroBot"
    * Definir um nome para o usuario do Bot, tendo este que terminar com bot. Ex: "NossoPrimeiroUser_bot"

Será gerado uma HASH de acesso ao serviço, agora podemos iniciar nossa interação...

* procure o botUser e inicie uma conversa!

#### Capturando chat_id
Precisamos saber o ID de nossa conversa(chatId) para então podermos enviar nossas mensagens. 

```bash
curl "https://api.telegram.org/bot00000:11111111111111111111/getUpdates"
```
RETORNO:
```json
{"ok":true,"result":[
{"update_id":39178979,"message":{"message_id":16,"from":{"id":69542477,"first_name":"woney","last_name":"branga"},"chat":{"id":69542477,"first_name":"woney","last_name":"branga","type":"private"},"date":1477515895,"text":"c99 osayap"}},
{"update_id":39178986,"message":{"message_id":17,"from":{"id":69542477,"first_name":"woney","last_name":"branga"},"chat":{"id":69542477,"first_name":"woney","last_name":"branga","type":"private"},"date":1477527280,"text":"\/getid","entities":[{"type":"bot_command","offset":0,"length":6}]}}]}
```
Agora temos nosso Chat_id, no exemplo: **69542477**

#### Enviando nossa primeira mensagem
3- Vamos enviar Mensagem...
```bash
curl "https://api.telegram.org/bot00000:11111111111111111111/sendMessage?chat_id=69542477&text=Primeira Mensagem! :-P"
```
RETORNO:
```json
{"ok":true,"result":{"message_id":19,"from":{"id":297496366,"first_name":"NossoPrimeiroBot","username":"NossoPrimeiroUser_bot"},"chat":{"id":69542477,"first_name":"woney","last_name":"branga","type":"private"},"date":1477581576,"text":"Primeira Mensagem! :-P"}}
```
#### Enviando nossa primeira Imagem
4- Vamos enviar uma imagem...
```bash
curl -s -X POST "https://api.telegram.org/bot00000:11111111111111111111/sendPhoto" -F chat_id=69542477 -F photo="@/opt/telegram/cap.png" 
```
RETORNO:
```json
{"ok":true,"result":{"message_id":20,"from":{"id":297496366,"first_name":"NossoPrimeiroBot","username":"NossoPrimeiroUser_bot"},"chat":{"id":69542477,"first_name":"woney","last_name":"branga","type":"private"},"date":1477581586,"photo":[{"file_id":"AgADAQADrKcxGy5vuxGwzBtLLiGtYlOq5y8ABPRg6LNEKLFAqNYBAAEC","file_size":1100,"width":90,"height":25},{"file_id":"AgADAQADrKcxGy5vuxGwzBtLLiGtYlOq5y8ABCg2AAGelLPTE6nWAQABAg","file_size":5723,"width":200,"height":55}]}}
```
