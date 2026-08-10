<?php

namespace MessengerTheme\Includes\Settings;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Settings_Footer extends Tab_Base {

	public function get_id() {
		return 'messenger-settings-footer';
	}

	public function get_title() {
		return esc_html__( 'Messenger Theme Footer', 'messenger-theme' );
	}

	public function get_icon() {
		return 'eicon-footer';
	}

	public function get_help_url() {
		return '';
	}

	public function get_group() {
		return 'theme-style';
	}

	protected function register_tab_controls() {
		$start = is_rtl() ? 'right' : 'left';
		$end = ! is_rtl() ? 'right' : 'left';

		$this->start_controls_section(
			'messenger_footer_section',
			[
				'tab' => 'messenger-settings-footer',
				'label' => esc_html__( 'Footer', 'messenger-theme' ),
			]
		);

		$this->add_control(
			'messenger_footer_logo_display',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => esc_html__( 'Site Logo', 'messenger-theme' ),
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'messenger-theme' ),
				'label_off' => esc_html__( 'Hide', 'messenger-theme' ),
				'selector' => '.site-footer .site-branding',
			]
		);

		$this->add_control(
			'messenger_footer_tagline_display',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => esc_html__( 'Tagline', 'messenger-theme' ),
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'messenger-theme' ),
				'label_off' => esc_html__( 'Hide', 'messenger-theme' ),
				'selector' => '.site-footer .site-description',
			]
		);

		$this->add_control(
			'messenger_footer_menu_display',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => esc_html__( 'Menu', 'messenger-theme' ),
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'messenger-theme' ),
				'label_off' => esc_html__( 'Hide', 'messenger-theme' ),
				'selector' => '.site-footer .site-navigation',
			]
		);

		$this->add_control(
			'messenger_footer_copyright_display',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => esc_html__( 'Copyright', 'messenger-theme' ),
				'default' => 'yes',
				'label_on' => esc_html__( 'Show', 'messenger-theme' ),
				'label_off' => esc_html__( 'Hide', 'messenger-theme' ),
				'selector' => '.site-footer .copyright',
			]
		);

		$this->add_control(
			'messenger_footer_disable_note',
			[
				'type' => Controls_Manager::ALERT,
				'alert_type' => 'warning',
				'content' => sprintf(
					/* translators: %s: Link that opens the theme settings page. */
					__( 'Note: Hiding all the elements, only hides them visually. To disable them completely go to <a href="%s">Theme Settings</a> .', 'messenger-theme' ),
					admin_url( 'themes.php?page=messenger-theme-settings' )
				),
				'render_type' => 'ui',
				'condition' => [
					'messenger_footer_logo_display' => '',
					'messenger_footer_tagline_display' => '',
					'messenger_footer_menu_display' => '',
					'messenger_footer_copyright_display' => '',
				],
			]
		);

		$this->add_control(
			'messenger_footer_layout',
			[
				'type' => Controls_Manager::CHOOSE,
				'label' => esc_html__( 'Layout', 'messenger-theme' ),
				'options' => [
					'inverted' => [
						'title' => esc_html__( 'Inverted', 'messenger-theme' ),
						'icon' => "eicon-arrow-$start",
					],
					'stacked' => [
						'title' => esc_html__( 'Centered', 'messenger-theme' ),
						'icon' => 'eicon-h-align-center',
					],
					'default' => [
						'title' => esc_html__( 'Default', 'messenger-theme' ),
						'icon' => "eicon-arrow-$end",
					],
				],
				'toggle' => false,
				'selector' => '.site-footer',
				'default' => 'default',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'messenger_footer_tagline_position',
			[
				'type' => Controls_Manager::CHOOSE,
				'label' => esc_html__( 'Tagline Position', 'messenger-theme' ),
				'options' => [
					'before' => [
						'title' => esc_html__( 'Before', 'messenger-theme' ),
						'icon' => "eicon-arrow-$start",
					],
					'below' => [
						'title' => esc_html__( 'Below', 'messenger-theme' ),
						'icon' => 'eicon-arrow-down',
					],
					'after' => [
						'title' => esc_html__( 'After', 'messenger-theme' ),
						'icon' => "eicon-arrow-$end",
					],
				],
				'toggle' => false,
				'default' => 'below',
				'selectors_dictionary' => [
					'before' => 'flex-direction: row-reverse; align-items: center;',
					'below' => 'flex-direction: column; align-items: stretch;',
					'after' => 'flex-direction: row; align-items: center;',
				],
				'condition' => [
					'messenger_footer_tagline_display' => 'yes',
					'messenger_footer_logo_display' => 'yes',
				],
				'selectors' => [
					'.site-footer .site-branding' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'messenger_footer_tagline_gap',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__( 'Tagline Gap', 'messenger-theme' ),
				'size_units' => [ 'px', 'em ', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
					'rem' => [
						'max' => 10,
					],
				],
				'condition' => [
					'messenger_footer_tagline_display' => 'yes',
					'messenger_footer_logo_display' => 'yes',
				],
				'selectors' => [
					'.site-footer .site-branding' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'messenger_footer_width',
			[
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__( 'Width', 'messenger-theme' ),
				'options' => [
					'boxed' => esc_html__( 'Boxed', 'messenger-theme' ),
					'full-width' => esc_html__( 'Full Width', 'messenger-theme' ),
				],
				'selector' => '.site-footer',
				'default' => 'boxed',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'messenger_footer_custom_width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__( 'Content Width', 'messenger-theme' ),
				'size_units' => [ '%', 'px', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'max' => 2000,
					],
					'em' => [
						'max' => 100,
					],
					'rem' => [
						'max' => 100,
					],
				],
				'condition' => [
					'messenger_footer_width' => 'boxed',
				],
				'selectors' => [
					'.site-footer .footer-inner' => 'width: {{SIZE}}{{UNIT}}; max-width: 100%;',
				],
			]
		);

		$this->add_responsive_control(
			'messenger_footer_gap',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__( 'Side Margins', 'messenger-theme' ),
				'size_units' => [ '%', 'px', 'em ', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 5,
					],
					'rem' => [
						'max' => 5,
					],
				],
				'selectors' => [
					'.site-footer' => 'padding-inline-end: {{SIZE}}{{UNIT}}; padding-inline-start: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'messenger_footer_layout!' => 'stacked',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'messenger_footer_background',
				'label' => esc_html__( 'Background', 'messenger-theme' ),
				'types' => [ 'classic', 'gradient' ],
				'separator' => 'before',
				'selector' => '.site-footer',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'messenger_footer_logo_section',
			[
				'tab' => 'messenger-settings-footer',
				'label' => esc_html__( 'Site Logo', 'messenger-theme' ),
				'condition' => [
					'messenger_footer_logo_display!' => '',
				],
			]
		);

		$this->add_control(
			'messenger_footer_logo_type',
			[
				'label' => esc_html__( 'Type', 'messenger-theme' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'logo',
				'options' => [
					'logo' => esc_html__( 'Logo', 'messenger-theme' ),
					'title' => esc_html__( 'Title', 'messenger-theme' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'messenger_footer_logo_width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__( 'Logo Width', 'messenger-theme' ),
				'size_units' => [ '%', 'px', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'max' => 1000,
					],
					'em' => [
						'max' => 100,
					],
					'rem' => [
						'max' => 100,
					],
				],
				'condition' => [
					'messenger_footer_logo_display' => 'yes',
					'messenger_footer_logo_type' => 'logo',
				],
				'selectors' => [
					'.site-footer .site-branding .site-logo img' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'messenger_footer_title_typography',
				'label' => esc_html__( 'Typography', 'messenger-theme' ),
				'condition' => [
					'messenger_footer_logo_display' => 'yes',
					'messenger_footer_logo_type' => 'title',
				],
				'selector' => '.site-footer .site-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'messenger_footer_title_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'messenger-theme' ),
				'condition' => [
					'messenger_footer_logo_display' => 'yes',
					'messenger_footer_logo_type' => 'title',
				],
				'selector' => '.site-footer .site-title a',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'messenger_footer_title_text_stroke',
				'label' => esc_html__( 'Text Stroke', 'messenger-theme' ),
				'condition' => [
					'messenger_footer_logo_display' => 'yes',
					'messenger_footer_logo_type' => 'title',
				],
				'selector' => '.site-footer .site-title a',
			]
		);

		$this->start_controls_tabs( 'messenger_footer_title_colors' );

		$this->start_controls_tab(
			'messenger_footer_title_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'messenger-theme' ),
				'condition' => [
					'messenger_footer_logo_display' => 'yes',
					'messenger_footer_logo_type' => 'title',
				],
			]
		);

		$this->add_control(
			'messenger_footer_title_color',
			[
				'label' => esc_html__( 'Text Color', 'messenger-theme' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'messenger_footer_logo_display' => 'yes',
					'messenger_footer_logo_type' => 'title',
				],
				'selectors' => [
					'.site-footer .site-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'messenger_footer_title_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'messenger-theme' ),
				'condition' => [
					'messenger_footer_logo_display' => 'yes',
					'messenger_footer_logo_type' => 'title',
				],
			]
		);

		$this->add_control(
			'messenger_footer_title_hover_color',
			[
				'label' => esc_html__( 'Text Color', 'messenger-theme' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'messenger_footer_logo_display' => 'yes',
					'messenger_footer_logo_type' => 'title',
				],
				'selectors' => [
					'.site-footer .site-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'messenger_footer_title_hover_color_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'messenger-theme' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's', 'ms', 'custom' ],
				'default' => [
					'unit' => 's',
				],
				'selectors' => [
					'.site-footer .site-title a' => 'transition-duration: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'messenger_footer_tagline',
			[
				'tab' => 'messenger-settings-footer',
				'label' => esc_html__( 'Tagline', 'messenger-theme' ),
				'condition' => [
					'messenger_footer_tagline_display' => 'yes',
				],
			]
		);

		$this->add_control(
			'messenger_footer_tagline_color',
			[
				'label' => esc_html__( 'Text Color', 'messenger-theme' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'messenger_footer_tagline_display' => 'yes',
				],
				'selectors' => [
					'.site-footer .site-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'messenger_footer_tagline_typography',
				'label' => esc_html__( 'Typography', 'messenger-theme' ),
				'condition' => [
					'messenger_footer_tagline_display' => 'yes',
				],
				'selector' => '.site-footer .site-description',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'messenger_footer_tagline_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'messenger-theme' ),
				'condition' => [
					'messenger_footer_tagline_display' => 'yes',
				],
				'selector' => '.site-footer .site-description',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'messenger_footer_menu_tab',
			[
				'tab' => 'messenger-settings-footer',
				'label' => esc_html__( 'Menu', 'messenger-theme' ),
				'condition' => [
					'messenger_footer_menu_display' => 'yes',
				],
			]
		);

		$available_menus = wp_get_nav_menus();

		$menus = [ '0' => esc_html__( '— Select a Menu —', 'messenger-theme' ) ];
		foreach ( $available_menus as $available_menu ) {
			$menus[ $available_menu->term_id ] = $available_menu->name;
		}

		if ( 1 === count( $menus ) ) {
			$this->add_control(
				'messenger_footer_menu_notice',
				[
					'type' => Controls_Manager::ALERT,
					'alert_type' => 'info',
					'heading' => esc_html__( 'There are no menus in your site.', 'messenger-theme' ),
					'content' => sprintf(
						__( 'Go to <a href="%s" target="_blank">Menus screen</a> to create one.', 'messenger-theme' ),
						admin_url( 'nav-menus.php?action=edit&menu=0' )
					),
					'render_type' => 'ui',
				]
			);
		} else {
			$this->add_control(
				'messenger_footer_menu_warning',
				[
					'type' => Controls_Manager::ALERT,
					'alert_type' => 'info',
					'content' => sprintf(
						__( 'Go to the <a href="%s" target="_blank">Menus screen</a> to manage your menus. Changes will be reflected in the preview only after the page reloads.', 'messenger-theme' ),
						admin_url( 'nav-menus.php' )
					),
					'render_type' => 'ui',
				]
			);

			$this->add_control(
				'messenger_footer_menu',
				[
					'label' => esc_html__( 'Menu', 'messenger-theme' ),
					'type' => Controls_Manager::SELECT,
					'options' => $menus,
					'default' => array_keys( $menus )[0],
				]
			);

			$this->add_control(
				'messenger_footer_menu_color',
				[
					'label' => esc_html__( 'Color', 'messenger-theme' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'footer .footer-inner .site-navigation a' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'messenger_footer_menu_typography',
					'label' => esc_html__( 'Typography', 'messenger-theme' ),
					'selector' => 'footer .footer-inner .site-navigation a',
				]
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				[
					'name' => 'messenger_footer_menu_text_shadow',
					'label' => esc_html__( 'Text Shadow', 'messenger-theme' ),
					'selector' => 'footer .footer-inner .site-navigation a',
				]
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'messenger_footer_copyright_section',
			[
				'tab' => 'messenger-settings-footer',
				'label' => esc_html__( 'Copyright', 'messenger-theme' ),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'messenger_footer_copyright_display',
							'operator' => '=',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'messenger_footer_copyright_text',
			[
				'type' => Controls_Manager::TEXTAREA,
				'label' => esc_html__( 'Text', 'messenger-theme' ),
				'default' => esc_html__( 'All rights reserved', 'messenger-theme' ),
			]
		);

		$this->add_control(
			'messenger_footer_copyright_color',
			[
				'label' => esc_html__( 'Text Color', 'messenger-theme' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'messenger_footer_copyright_display' => 'yes',
				],
				'selectors' => [
					'.site-footer .copyright p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'messenger_footer_copyright_typography',
				'label' => esc_html__( 'Typography', 'messenger-theme' ),
				'condition' => [
					'messenger_footer_copyright_display' => 'yes',
				],
				'selector' => '.site-footer .copyright p',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'messenger_footer_copyright_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'messenger-theme' ),
				'condition' => [
					'messenger_footer_copyright_display' => 'yes',
				],
				'selector' => '.site-footer .copyright p',
			]
		);

		$this->end_controls_section();
	}

	public function on_save( $data ) {
		// Save chosen footer menu to the WP settings.
		if ( isset( $data['settings']['messenger_footer_menu'] ) ) {
			$menu_id = $data['settings']['messenger_footer_menu'];
			$locations = get_theme_mod( 'nav_menu_locations' );
			$locations['menu-2'] = (int) $menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}
}
