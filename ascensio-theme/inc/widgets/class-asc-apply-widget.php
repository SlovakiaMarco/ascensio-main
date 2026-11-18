<?php
/**
 * Elementor widget: Prihláška / dopyt.
 *
 * @package Ascensio
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Formulár prihlášky uchádzača alebo dopytu firmy.
 */
class Asc_Apply_Widget extends Widget_Base {

	/** Názov. */
	public function get_name() {
		return 'asc-apply';
	}

	/** Titulok v paneli. */
	public function get_title() {
		return __( 'Prihláška / dopyt', 'ascensio' );
	}

	/** Ikona. */
	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	/** Kategória. */
	public function get_categories() {
		return array( 'ascensio' );
	}

	/** Kľúčové slová. */
	public function get_keywords() {
		return array( 'formulár', 'prihláška', 'form', 'kontakt', 'dopyt', 'ascensio' );
	}

	/** Ovládacie prvky. */
	protected function register_controls() {
		$this->start_controls_section(
			'section_form',
			array( 'label' => __( 'Formulár', 'ascensio' ) )
		);

		$this->add_control(
			'variant',
			array(
				'label'   => __( 'Typ formulára', 'ascensio' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'full',
				'options' => array(
					'quick'   => __( 'Rýchla prihláška (meno, telefón, pozícia)', 'ascensio' ),
					'full'    => __( 'Plná prihláška uchádzača', 'ascensio' ),
					'partner' => __( 'Dopyt firmy (B2B)', 'ascensio' ),
				),
			)
		);

		$this->add_control(
			'lang',
			array(
				'label'     => __( 'Jazyk formulára', 'ascensio' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'sk',
				'options'   => array(
					'sk' => 'Slovenčina',
					'de' => 'Deutsch',
					'fr' => 'Français',
					'en' => 'English',
				),
				'condition' => array( 'variant' => 'partner' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Nadpis', 'ascensio' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Prihláška do práce', 'ascensio' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'label'   => __( 'Popis pod nadpisom', 'ascensio' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => '',
				'rows'    => 3,
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => __( 'Text tlačidla', 'ascensio' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Odoslať prihlášku', 'ascensio' ),
			)
		);

		$this->add_control(
			'position',
			array(
				'label'       => __( 'Predvybraná pozícia', 'ascensio' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => __( 'Nepovinné, napr. Zvárač (pre landing page jednej pozície).', 'ascensio' ),
				'condition'   => array( 'variant!' => 'partner' ),
			)
		);

		$this->add_control(
			'help',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Odoslané prihlášky nájdete v administrácii v menu <strong>Prihlášky</strong>. E-mail pre upozornenia nastavíte vo Vzhľad → Prispôsobiť → Ascensio → Prihlášky.', 'ascensio' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->end_controls_section();
	}

	/** Výstup. */
	protected function render() {
		$s       = $this->get_settings_for_display();
		$variant = isset( $s['variant'] ) ? $s['variant'] : 'full';
		echo asc_render_form( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'variant'  => $variant,
				'title'    => isset( $s['title'] ) ? $s['title'] : '',
				'subtitle' => isset( $s['subtitle'] ) ? $s['subtitle'] : '',
				'button'   => isset( $s['button_text'] ) ? $s['button_text'] : '',
				'lang'     => ( 'partner' === $variant && ! empty( $s['lang'] ) ) ? $s['lang'] : 'sk',
				'position' => isset( $s['position'] ) ? $s['position'] : '',
			)
		);
	}
}
