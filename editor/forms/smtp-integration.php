<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/20/18
 * Time: 4:48 PM
 */

class Brizy_Editor_Forms_SmtpIntegration extends Brizy_Editor_Forms_WordpressIntegration {

	/**
	 * @var string
	 */
	protected $host;

	/**
	 * @var string
	 */
	protected $port;

	/**
	 * @var string
	 */
	protected $authentication;

	/**
	 * @var string
	 */
	protected $username;

	/**
	 * @var string
	 */
	protected $password;

	/**
	 * @var string
	 */
	protected $encryption;

	/**
	 * Brizy_Editor_Forms_WordpressIntegration constructor.
	 */
	public function __construct() {
		$this->id = 'smtp';
	}

	/**
	 * @param $fields
	 *
	 * @return bool|mixed
	 * @throws Exception
	 */
	public function handleSubmit( $fields ) {
		add_action( 'phpmailer_init', array( $this, 'decoratePhpMailer' ) );

		return parent::handleSubmit( $fields );
	}

	public function decoratePhpMailer( $phpmailer ) {
		$phpmailer->isSMTP();
		$phpmailer->Host     = $this->getHost();
		$phpmailer->SMTPAuth = $this->getAuthentication();
		$phpmailer->Port     = $this->getPort();
		$phpmailer->Username = $this->getUsername();
		$phpmailer->Password = $this->getPassword();
	}

	/**
	 * @return array|mixed
	 */
	public function jsonSerialize() {

		$get_object_vars = parent::jsonSerialize();

		$get_object_vars['emailTo']        = $this->getEmailTo();
		$get_object_vars['subject']        = $this->getSubject();
		$get_object_vars['host']           = $this->getHost();
		$get_object_vars['authentication'] = $this->getAuthentication();
		$get_object_vars['port']           = $this->getPort();
		$get_object_vars['username']       = $this->getUsername();
		$get_object_vars['password']       = $this->getPassword();
		$get_object_vars['encryption']     = $this->getEncryption();

		return $get_object_vars;
	}

	/**
	 * @param $json_obj
	 *
	 * @return Brizy_Editor_Forms_WordpressIntegration|null
	 */
	public static function createFromJson( $json_obj ) {
		$instance = null;
		if ( is_object( $json_obj ) ) {
			$instance = new self( $json_obj->id );

			if ( isset( $json_obj->emailTo ) ) {
				$instance->setEmailTo( $json_obj->emailTo );
			}

			if ( isset( $json_obj->subject ) ) {
				$instance->setSubject( $json_obj->subject );
			}
			if ( isset( $json_obj->host ) ) {
				$instance->setHost( $json_obj->host );
			}
			if ( isset( $json_obj->port ) ) {
				$instance->setPort( $json_obj->port );
			}
			if ( isset( $json_obj->authentication ) ) {
				$instance->setAuthentication( $json_obj->authentication );
			}
			if ( isset( $json_obj->username ) ) {
				$instance->setUsername( $json_obj->username );
			}
			if ( isset( $json_obj->password ) ) {
				$instance->setPassword( $json_obj->password );
			}
			if ( isset( $json_obj->encryption ) ) {
				$instance->setEncryption( $json_obj->encryption );
			}
		}

		return $instance;
	}

	/**
	 * @return string
	 */
	public function getHost() {
		return $this->host;
	}

	/**
	 * @param string $host
	 *
	 * @return Brizy_Editor_Forms_SmtpIntegration
	 */
	public function setHost( $host ) {
		$this->host = $host;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPort() {
		return $this->port;
	}

	/**
	 * @param string $port
	 *
	 * @return Brizy_Editor_Forms_SmtpIntegration
	 */
	public function setPort( $port ) {
		$this->port = $port;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getAuthentication() {
		return $this->authentication;
	}

	/**
	 * @param string $authentication
	 *
	 * @return Brizy_Editor_Forms_SmtpIntegration
	 */
	public function setAuthentication( $authentication ) {
		$this->authentication = $authentication;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @param string $username
	 *
	 * @return Brizy_Editor_Forms_SmtpIntegration
	 */
	public function setUsername( $username ) {
		$this->username = $username;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @param string $password
	 *
	 * @return Brizy_Editor_Forms_SmtpIntegration
	 */
	public function setPassword( $password ) {
		$this->password = $password;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getEncryption() {
		return $this->encryption;
	}

	/**
	 * @param string $encryption
	 *
	 * @return Brizy_Editor_Forms_SmtpIntegration
	 */
	public function setEncryption( $encryption ) {
		$this->encryption = $encryption;

		return $this;
	}

}