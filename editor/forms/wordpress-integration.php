<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/20/18
 * Time: 4:48 PM
 */

class Brizy_Editor_Forms_WordpressIntegration extends Brizy_Editor_Forms_AbstractIntegration {

	/**
	 * @var string
	 */
	protected $emailTo;

	/**
	 * @var string
	 */
	protected $subject;

	/**
	 * Brizy_Editor_Forms_WordpressIntegration constructor.
	 */
	public function __construct() {
		parent::__construct( 'wordpress' );
	}

	/**
	 * @param $fields
	 *
	 * @return bool|mixed
	 * @throws Exception
	 */
	public function handleSubmit( $fields ) {

		$headers   = array();
		$headers[] = 'Content-type: text/html; charset=UTF-8';

		$field_string = array();
		foreach ( $fields as $field ) {
			$field_string[] = "{$field->label}: " . esc_html( $field->value );
		}

		$email_body = implode( '<br>', $field_string );

		$headers    = apply_filters( 'brizy_form_email_headers', $headers, $fields );
		$email_body = apply_filters( 'brizy_form_email_body', $email_body, $fields );

		if ( ! function_exists( 'wp_mail' ) ) {
			throw new Exception( 'Please check your wordpress configuration.' );
		}

		return wp_mail(
			$this->getEmailTo(),
			$this->getSubject(),
			$email_body,
			$headers
		);

	}

	/**
	 * @return array|mixed
	 */
	public function jsonSerialize() {

		$get_object_vars = parent::jsonSerialize();

		$get_object_vars['emailTo'] = $this->getEmailTo();
		$get_object_vars['subject'] = $this->getSubject();

		return $get_object_vars;
	}

	/**
	 * @return string
	 */
	public function serialize() {
		return serialize( $this->jsonSerialize() );
	}

	/**
	 * @return string
	 */
	public function getEmailTo() {
		return $this->emailTo;
	}

	/**
	 * @param string $emailTo
	 *
	 * @return Brizy_Editor_Forms_WordpressIntegration
	 */
	public function setEmailTo( $emailTo ) {
		$this->emailTo = $emailTo;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * @param string $subject
	 *
	 * @return Brizy_Editor_Forms_WordpressIntegration
	 */
	public function setSubject( $subject ) {
		$this->subject = $subject;

		return $this;
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
		}

		return $instance;
	}
}