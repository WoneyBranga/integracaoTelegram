<?php 
/**
* Classe para interação Básica com Telegram API por meio de CURL
* 
* Podemos deixar nossas imagens mais simpáticas utilizando Emojis. Para isso vamos capturar 
* os BYTES(utf-8) de nosso emoji(ver http://apps.timwhitlock.info/emoji/tables/unicode).
*
* Poreríamos trabalhar com WebHooks no lugar do nosso modo de monitoramento da resposta, mas 
* em alguns casos, como em minha empresa, não consigo subir o webHook, então essa nossa soluçao quebra o galho!
*
* @author Woney Branga 
*/


/*Renomear arquivo config.php.exemplo e ajustar HashBot para o de seu Bot*/
require_once("config.php");


class InterageTelegramWebApi{

	/*Hash criada para funcionamento de nosso bot.(icebots_bot)*/
	protected $botHash = null;
	/*id do chat criado para resolver calpchar quando necessario*/
	public $chatId = null;
	/*Url default do Telegram com nosso hash*/
	public $urlTelegramApi = "https://api.telegram.org";
	/*Url que utilizaremos com nosso botHash*/
	public $urlBot = null;
	/*Para guardar as respostas do telegram*/
	public $retornoCurl = null;

/**
 * @param [type]
 */
	function __construct($chatId){
		/*Id de nosso chat*/
		$this->chatId = $chatId;
		/*Vamos popular nossa variavel com hash do nosso bot*/
		$this->botHash = constant("HASHBOT");
		/*Vamos montar nossa URL com bot*/
		$this->urlBot = $this->urlTelegramApi . "/bot" . $this->botHash;
	}


/**
 * trataCurl Função coringa para tratar requisições curl
 * @param  $parametrosCurl	array 	Array com configurações do Curl
 * @return boolean 
 */
	function trataCurl($parametrosCurl){
		$ch = curl_init();
		curl_setopt_array($ch, $parametrosCurl);
		$this->retornoCurl = curl_exec($ch);

		$parametrosRequisicao = curl_getinfo($ch);
		curl_close($ch);
		/*Sabemos que nossa requisição quando sucesso retorna http_code 200, vamos validar nosso curl baseado neste parametro*/
		if($parametrosRequisicao['http_code'] == 200) return true;
		else return false;
	}

/**
 * enviaMensagemTelegramWeb
 * @param  $mensagem 	Mensagem a enviar para chat
 * @param  $idChat		identificação do chat
 * @return boolean
 */
	function enviaMensagemTelegramWeb($mensagem,$idChat=false){
		if(!$idChat) $idChat = $this->chatId;
		if($this->trataCurl([
			CURLOPT_URL => $this->urlBot . "/sendMessage?chat_id=" . $idChat . "&text=$mensagem",
			CURLOPT_RETURNTRANSFER => true,
			])
			) return true;
			else return false;
	}

/**
 * enviaImagem
 * @return boolean
 */
	function enviaImagem(){
		$url        = $this->urlBot . "/sendPhoto?chat_id=" . $this->chatId;
		$post_fields = array('chat_id'   => $this->chatId,
			'photo'     => new CURLFile(realpath("img.png"))
			);

# Caso queiramos carregar uma imagen vinda de um captcha por exemplo, podemos usar o bloco abaixo..
#		/*vamos capturar img a ser enviada ao telegram*/
#		$img=file_get_contents("http://site/img.png");
#		/*vamos criar um arquivo captcha.png*/
#		$fWrite = fopen("img.png","w+");
#		/*vamos popupar captcha.png*/
#		$wrote = fwrite($fWrite, $img);
#		fclose($fWrite);

		if($this->trataCurl([
			CURLOPT_HTTPHEADER => array("Content-Type:multipart/form-data"),
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => $post_fields,
			])
			) return true;
			else return false;
	}


/**
 * consultaUltimaMensagemTelegram
 * @return boolean
 */
	function consultaUltimaMensagemTelegram(){
		if($this->trataCurl([
			CURLOPT_URL => $this->urlBot . "/getUpdates?offset=-1",
			CURLOPT_RETURNTRANSFER => true,
			])
			) return true;
			else return false;
	}

/**
 * esperaRespostaTelegram captura mensagem com base num ID
 * Utilizamos esta para monitorar a resposta, sabemos da ultima mensagem enviada, este ID +1 será nosso alvo com a resposta...
 * @param  mensagemId	id da mensagem que desejamos capturar
 * @return boolean
 */
	function esperaRespostaTelegram($mensagemId){
		if($this->trataCurl([
			CURLOPT_URL => $this->urlBot . "/getUpdates?offset=$mensagemId",
			CURLOPT_RETURNTRANSFER => true,
			])
			)
		{
			$dadosTelegram=json_decode($this->retornoCurl,1);
			if ($dadosTelegram['ok']) {
				if(isset($dadosTelegram['result'][0]['update_id'])){
					$this->enviaMensagemTelegramWeb("RESPOSTA RECEBIDA [".preg_replace("@/resp @", "", $dadosTelegram['result'][0]['message']['text'])."] \xF0\x9F\x98\x8E",0);
					echo "resporta:".preg_replace("@/resp @", "", $dadosTelegram['result'][0]['message']['text']);
					return $dadosTelegram['result'][0]['message']['text'];
				}else{
					return false;
				}
			}
		}else{
			echo "Processo interação Telegram Falhou...";
			return false;
		} 
	}

	/**
	 * monitoraRespostaImagem Monitora resposta do telegram
	 * A cada 3 segundos interrogamos o telegramWeb aguardando a resposta, realizamos isto por 40x(2min), caso contrário abandonamos monitoração...
	 * @return boolean
	 */
	function monitoraRespostaImagem(){
		if($this->trataCurl([
			CURLOPT_URL => $this->urlBot . "/getUpdates?offset=-1",
			CURLOPT_RETURNTRANSFER => true,			])
			)
		{
			$dadosTelegram=json_decode($this->retornoCurl,1);
			if ($dadosTelegram['ok']) {
				$cont=1;
				while(!$this->esperaRespostaTelegram(($dadosTelegram['result'][0]['update_id']+1))){

					if(($cont % 10 == 0) && ($cont < 31) )$this->enviaMensagemTelegramWeb("\xF0\x9F\x94\xB4\xF0\x9F\x94\xB4 AGUARDANDO IMAGEM(".($cont/10)."/3)... \xF0\x9F\x94\xB4\xF0\x9F\x94\xB4",0);
					if( $cont > 41 ){
						$this->enviaMensagemTelegramWeb("Esperei por 2min, ninguem quis me ajudar... Chutei o BALDE! \xF0\x9F\x98\x92",0);
						exit;
					}
					sleep(3);
					$cont++;
				}
			}
		}else{
			echo "Processo interação Telegram Falhou...";
			return false;
		}
	}

	/**
	 * trataImagem Chamada principal para tratamento completo
	 * @param  $nomeIntegrador Nome a ser echoado no alerta do telegram 
	 */
	function trataImagem($nomeIntegrador=false){
		$this->enviaMensagemTelegramWeb("\xF0\x9F\x94\xB4\xF0\x9F\x94\xB4 ATENCAO \xF0\x9F\x94\xB4\xF0\x9F\x94\xB4");
		$this->enviaMensagemTelegramWeb("=====================%0A \xF0\x9F\x98\xB1 FALHA $nomeIntegrador \xF0\x9F\x98\xB1%0A=====================%0APor Favor Resolva a imagem abaixo!%0AResponda: /resp SoLuCao");
		$this->enviaImagem();
		$this->monitoraRespostaImagem();
	}

}//FimClasse

$obj = new InterageTelegramWebApi("69542477");
$obj->trataImagem("IntegradorTeste");
?>