<?php
/**
 * 邮件发送类
 * @author Tongle Xu <xutongle@gmail.com> 2013-2-26
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id$
 */
final class Mail {
	/**
	 * 通过mail函数发送
	 *
	 * @var Mail
	 */
	const TYPE_MAIL = 'Mail';

	/**
	 * 通过SMTP协议发送(支持ESMTP验证)
	 *
	 * @var string
	 */
	const TYPE_ESMTP = 'Esmtp';

	/**
	 * 通过SOCKET连接SMTP服务器发送
	 *
	 * @var string
	 */
	const TYPE_SMTP = 'Smtp';

	/**
	 * 当前使用操作类型
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * 驱动
	 *
	 * @var Mail_Driver_Mail
	 */
	protected $driver;

	/**
	 * 邮件配置
	 *
	 * @var unknown
	 */
	private $setting;

	/**
	 *
	 * @param string $type 指定驱动类型
	 */
	public function __construct($type = null) {
		$this->setting = C ( 'mail' ); // 加载邮件配置
		                               // 邮件头的分隔符
		$this->delimiter = $this->setting ['delimiter'] == 1 ? "\r\n" : ($this->setting ['delimiter'] == 2 ? "\r" : "\n");
		// 收件人地址中包含用户名
		$this->mailusername = $this->setting ['mailusername'];
		if (! empty ( $this->setting ['cc'] )) $this->Cc = $this->setting ['cc']; // 抄送
		if (! empty ( $this->setting ['bcc'] )) $this->Bcc = $this->setting ['bcc']; // 暗送
		                                                                             // 加载网站配置
		$this->site_seting = S ( 'common/common' );
		$this->site_name = $this->site_seting ['site_name']; // 站点名称
		$this->site_email = $this->site_seting ['system_email']; // 系统邮箱
		$this->type = $this->setting ['type'];
		$this->server = $this->setting ['server'];
		$this->port = $this->setting ['port'];
		$this->auth = $this->setting ['auth'];
		$this->user = $this->setting ['auth_username'];
		$this->password = $this->setting ['auth_password'];
	}

	/**
	 * 发送电子邮件
	 *
	 * @param unknown $to 发送到
	 * @param unknown $subject 标题
	 * @param unknown $message 内容
	 * @param string $from 来源
	 */
	public function send($to, $subject, $message, $from = '') {
		// 收件人
		$emails = explode ( ',', $to );
		foreach ( $emails as $touser ) {
			$tousers [] = preg_match ( '/^(.+?) \<(.+?)\>$/', $touser, $to ) ? ($this->mailusername ? '=?' . CHARSET . '?B?' . base64_encode ( $to [1] ) . "?= <$to[2]>" : $to [2]) : $touser;
		}
		// 发信标题
		$subject = '=?' . CHARSET . '?B?' . base64_encode ( str_replace ( "\r", '', $subject ) ) . '?=';
		// 发信内容
		$message = str_replace ( "\r\n.", " \r\n..", str_replace ( "\n", "\r\n", str_replace ( "\r", "\n", str_replace ( "\r\n", "\n", str_replace ( "\n\r", "\r", $message ) ) ) ) );
		$to = implode ( ',', $tousers ); // 构造过滤后的Email列表
		                                 // 发信者
		$adminemail = $this->type != 1 ? $this->user : $this->site_email;
		$from = $from == '' ? '=?' . CHARSET . '?B?' . base64_encode ( $this->site_name ) . "?= <$adminemail>" : (preg_match ( '/^(.+?) \<(.+?)\>$/', $from, $from ) ? '=?' . CHARSET . '?B?' . base64_encode ( $from [1] ) . "?= <$from[2]>" : $from);
		// Header头
		$headers = 'MIME-Version: 1.0' . $this->delimiter;
		$headers .= 'Content-type: text/html; charset=' . CHARSET . '' . $this->delimiter;
		$headers .= 'X-Priority: 3' . $this->delimiter;
		$headers .= 'X-Mailer: TintSoft ' . $this->delimiter;
		$headers .= "From: $from" . $this->delimiter;
		if ($this->Cc) $headers .= "Cc: " . $this->Cc . $this->delimiter;
		if ($this->Bcc) $headers .= "Bcc: " . $this->Bcc . $this->delimiter;
		// mail 发送模式
		if ($this->type == 1) { // sendmail
			return @mail ( $to, $subject, $message, $headers );
		} elseif ($this->type == 2) { // smtp
			return $this->esmtp ( $to, $subject, $message, $from, $headers );
		} elseif ($this->type == 3) { // mail
			return $this->smtp ( $to, $subject, $message, $from, $headers );
		}
	}

	/**
	 * Windows下的SMTP发送邮件
	 *
	 * @param string $email_to
	 * @param string $email_subject
	 * @param string $email_message
	 * @param string $email_from
	 * @param string $headers
	 */
	public function smtp($to, $subject, $message, $from = '', $headers = '') {
		ini_set ( 'SMTP', $this->server );
		ini_set ( 'smtp_port', $this->port );
		ini_set ( 'sendmail_from', $from );
		return @mail ( $to, $subject, $message, $headers );
	}

	/**
	 * ESMTP发送电子邮件
	 *
	 * @param string $email_to
	 * @param string $email_subject
	 * @param string $email_message
	 * @param string $email_from
	 * @param string $headers
	 */
	public function esmtp($email_to, $email_subject, $email_message, $email_from = '', $headers = '') {
		if (! $fp = fsockopen ( $this->server, $this->port, $errno, $errstr, 10 )) {
			$this->errorlog ( 'SMTP', "($this->server:$this->port) CONNECT - Unable to connect to the SMTP server", 0 );
			return false;
		}
		stream_set_blocking ( $fp, true );
		$lastmessage = fgets ( $fp, 512 );
		if (substr ( $lastmessage, 0, 3 ) != '220') {
			$this->errorlog ( 'SMTP', "$this->server:$this->port CONNECT - $lastmessage", 0 );
			return false;
		}
		fputs ( $fp, ($this->auth ? 'EHLO' : 'HELO') . " {$_SERVER['HTTP_HOST']}\r\n" );
		$lastmessage = fgets ( $fp, 512 );
		if (substr ( $lastmessage, 0, 3 ) != 220 && substr ( $lastmessage, 0, 3 ) != 250) {
			$this->errorlog ( 'SMTP', "($this->server:$this->port) HELO/EHLO - $lastmessage", 0 );
			return false;
		}
		while ( 1 ) {
			if (substr ( $lastmessage, 3, 1 ) != '-' || empty ( $lastmessage )) {
				break;
			}
			$lastmessage = fgets ( $fp, 512 );
		}
		fputs ( $fp, "AUTH LOGIN\r\n" );
		$lastmessage = fgets ( $fp, 512 );
		if (substr ( $lastmessage, 0, 3 ) != 334) {
			$this->errorlog ( 'SMTP', "($this->server:$this->port) AUTH LOGIN - $lastmessage", 0 );
			return false;
		}
		fputs ( $fp, base64_encode ( $this->user ) . "\r\n" );
		$lastmessage = fgets ( $fp, 512 );
		if (substr ( $lastmessage, 0, 3 ) != 334) {
			$this->errorlog ( 'SMTP', "($this->server:$this->port) USERNAME - $lastmessage", 0 );
			return false;
		}
		fputs ( $fp, base64_encode ( $this->password ) . "\r\n" );
		$lastmessage = fgets ( $fp, 512 );
		if (substr ( $lastmessage, 0, 3 ) != 235) {
			$this->errorlog ( 'SMTP', "($this->server:$this->port) PASSWORD - $lastmessage", 0 );
			return false;
		}
		fputs ( $fp, "MAIL FROM: <" . preg_replace ( "/.*\<(.+?)\>.*/", "\\1", $email_from ) . ">\r\n" );
		$lastmessage = fgets ( $fp, 512 );
		if (substr ( $lastmessage, 0, 3 ) != 250) {
			fputs ( $fp, "MAIL FROM: <" . preg_replace ( "/.*\<(.+?)\>.*/", "\\1", $email_from ) . ">\r\n" );
			$lastmessage = fgets ( $fp, 512 );
			if (substr ( $lastmessage, 0, 3 ) != 250) {
				$this->errorlog ( 'SMTP', "($this->server:$this->port) MAIL FROM - $lastmessage", 0 );
				return false;
			}
		}
		$email_tos = array ();
		$emails = explode ( ',', $email_to );
		foreach ( $emails as $touser ) {
			$touser = trim ( $touser );
			if ($touser) {
				fputs ( $fp, "RCPT TO: <" . preg_replace ( "/.*\<(.+?)\>.*/", "\\1", $touser ) . ">\r\n" );
				$lastmessage = fgets ( $fp, 512 );
				if (substr ( $lastmessage, 0, 3 ) != 250) {
					fputs ( $fp, "RCPT TO: <" . preg_replace ( "/.*\<(.+?)\>.*/", "\\1", $touser ) . ">\r\n" );
					$lastmessage = fgets ( $fp, 512 );
					$this->errorlog ( 'SMTP', "($this->server:$this->port) RCPT TO - $lastmessage", 0 );
					return false;
				}
			}
		}
		// 抄送
		if ($this->Cc) {
			fputs ( $fp, "RCPT TO: <" . preg_replace ( "/.*\<(.+?)\>.*/", "\\1", $this->Cc ) . ">\r\n" );
			$lastmessage = fgets ( $fp, 512 );
			if (substr ( $lastmessage, 0, 3 ) != 250) {
				$this->errorlog ( 'SMTP', "($this->server:$this->port) RCPT Cc - $lastmessage", 0 );
				return false;
			}
		}
		// 密送
		if ($this->Bcc) {
			fputs ( $fp, "RCPT To: <" . preg_replace ( "/.*\<(.+?)\>.*/", "\\1", $this->Bcc ) . ">\r\n" );
			$lastmessage = fgets ( $fp, 512 );
			if (substr ( $lastmessage, 0, 3 ) != 250) {
				$this->errorlog ( 'SMTP', "($this->server:$this->port) RCPT Bcc - $lastmessage", 0 );
				return false;
			}
		}

		fputs ( $fp, "DATA\r\n" );
		$lastmessage = fgets ( $fp, 512 );
		if (substr ( $lastmessage, 0, 3 ) != 354) {
			$this->errorlog ( 'SMTP', "($this->server:$this->port) DATA - $lastmessage", 0 );
		}
		$headers .= 'Message-ID: <' . gmdate ( 'YmdHs' ) . '.' . substr ( md5 ( $email_message . microtime () ), 0, 6 ) . rand ( 100000, 999999 ) . '@' . $_SERVER ['HTTP_HOST'] . ">{$this->delimiter}";
		fputs ( $fp, "Date: " . gmdate ( 'r' ) . "\r\n" );
		fputs ( $fp, "To: " . $email_to . "\r\n" );
		fputs ( $fp, "Subject: " . $email_subject . "\r\n" );
		fputs ( $fp, $headers . "\r\n" );
		fputs ( $fp, "\r\n\r\n" );
		fputs ( $fp, "$email_message\r\n.\r\n" );
		$lastmessage = fgets ( $fp, 512 );
		fputs ( $fp, "QUIT\r\n" );
		return true;
	}

	/**
	 * 邮件错误信息
	 *
	 * @param $type
	 * @param $message
	 * @param $is
	 */
	public function errorlog($type, $message, $is) {
		$this->error [] = array ($type,$message,$is );
	}
}