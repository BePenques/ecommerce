<?php //classe responsavel por enviar emails

namespace Hcode;

use Rain\Tpl;

use PHPMailer;

class Mailer {

	const USERNAME = "307711@aluno.unilins.edu.br";//email utilizado para enviar pelo sistema
	const PASSWORD = "beramones8182";

	const NAME_FROM = "Hcode Store";

	private $mail;

	public function __construct($toAddress, $toName, $subject, $tplname, $data = array())/*qual o endereço q vmos mandar, qual o nome do destinatario, assunto, nome do template que iremos mandar, dados que quer mandar */

	{

	$config = array(
			"tpl_dir"       => $_SERVER["DOCUMENT_ROOT"]."/views/email/"/* onde vai salvar os emails*/,
			"cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
			"debug"         => false
		
		);

	    Tpl::configure( $config );

	   $tpl = new Tpl;

	   //passar os dados para o template:

	   foreach ($data as $key => $value) {
	   		$tpl->assign($key, $value);
	   }

	   $html = $tpl->draw($tplname, true);

	//Create a new PHPMailer instance
	//$mail = new PHPMailer;
	$this->mail = new PHPMailer();

	//Tell PHPMailer to use SMTP
	$this->mail->isSMTP();

	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$this->mail->SMTPDebug = 0;

	//Set the hostname of the mail server
	$this->mail->Host = 'smtp.gmail.com';
	// use
	// $this->mail->Host = gethostbyname('smtp.gmail.com');
	// if your network does not support SMTP over IPv6

	//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
	$this->mail->Port = 587;

	//Set the encryption system to use - ssl (deprecated) or tls
	$this->mail->SMTPSecure = 'tls';

	//Whether to use SMTP authentication
	$this->mail->SMTPAuth = true;

	//Username to use for SMTP authentication - use full email address for gmail
	$this->mail->Username = Mailer::USERNAME;

	//Password to use for SMTP authentication
	$this->mail->Password = Mailer::PASSWORD;

	//Set who the message is to be sent from
	$this->mail->setFrom(Mailer::USERNAME, Mailer::NAME_FROM);

	//Set an alternative reply-to address
	//$this->mail->addReplyTo('replyto@example.com', 'First Last');

	//Set who the message is to be sent to
	$this->mail->addAddress($toAddress, $toName);

	//Set the subject line
	$this->mail->Subject = $subject;//assunto

	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$this->mail->msgHTML($html);

	//Replace the plain text body with one created manually
	$this->mail->AltBody = 'This is a plain-text message body';

	//Attach an image file
	//$this->mail->addAttachment('images/phpmailer_mini.png');

	//send the message, check for errors
	

	}

	public function send()
	{
		return $this->mail->send();
	}
	

	}

?>