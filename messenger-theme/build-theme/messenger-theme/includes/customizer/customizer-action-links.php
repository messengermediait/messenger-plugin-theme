<?php
namespace MessengerTheme\Includes\Customizer;

use MessengerTheme\Includes\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Messenger_Customizer_Action_Links extends \WP_Customize_Control {

	// Whitelist content parameter
	public $content = '';

	/**
	 * Render the control's content.
	 *
	 * Allows the content to be overridden without having to rewrite the wrapper.
	 *
	 * @return void
	 */
	public function render_content() {
		$this->print_customizer_action_links();

		if ( isset( $this->description ) ) {
			echo '<span class="description customize-control-description">' . wp_kses_post( $this->description ) . '</span>';
		}
	}

	/**
	 * Get the customizer action links HTML.
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	private function get_customizer_action_links_html( $data ) {
		if (
			empty( $data )
			|| ! isset( $data['image'] )
			|| ! isset( $data['alt'] )
			|| ! isset( $data['title'] )
			|| ! isset( $data['message'] )
			|| ! isset( $data['link'] )
			|| ! isset( $data['button'] )
		) {
			return;
		}

		return sprintf(
			'<div class="messenger-action-links">
				<img src="%1$s" alt="%2$s">
				<p class="messenger-action-links-title">%3$s</p>
				<p class="messenger-action-links-message">%4$s</p>
				<a class="button button-primary" target="_blank" href="%5$s">%6$s</a>
			</div>',
			$data['image'],
			$data['alt'],
			$data['title'],
			$data['message'],
			$data['link'],
			$data['button'],
		);
	}
}
