<?php
/**
 * Elementor widget: Pracovné ponuky.
 *
 * @package Ascensio
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Výpis pracovných ponúk s filtrom podľa profesie a krajiny.
 */
class Asc_Jobs_Widget extends Widget_Base {

	/** Názov. */
	public function get_name() {
		return 'asc-jobs';
	}

	/** Titulok v paneli. */
	public function get_title() {
		return __( 'Pracovné ponuky', 'ascensio' );
	}

	/** Ikona. */
	public function get_icon() {
		return 'eicon-posts-grid';
	}

	/** Kategória. */
	public function get_categories() {
		return array( 'ascensio' );
	}

	/** Kľúčové slová vyhľadávania. */
	public function get_keywords() {
		return array( 'ponuky', 'práca', 'jobs', 'ascensio', 'pozície' );
	}

	/**
	 * Možnosti výberu z taxonómie.
	 *
	 * @param string $taxonomy Taxonómia.
	 */
	private function term_options( $taxonomy ) {
		$out   = array();
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			)
		);
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $t ) {
				$out[ $t->slug ] = $t->name;
			}
		}
		return $out;
	}

	/** Ovládacie prvky. */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Ponuky', 'ascensio' ) )
		);

		$this->add_control(
			'info',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Ponuky pridávate v menu <strong>Pracovné ponuky</strong> v administrácii. Tu nastavíte iba, ktoré a ako sa zobrazia.', 'ascensio' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'count',
			array(
				'label'   => __( 'Max. počet ponúk', 'ascensio' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 50,
				'min'     => 1,
				'max'     => 200,
			)
		);

		$this->add_control(
			'columns',
			array(
				'label'   => __( 'Stĺpce', 'ascensio' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				'options' => array(
					'2' => '2',
					'3' => '3',
				),
			)
		);

		$this->add_control(
			'filters',
			array(
				'label'        => __( 'Zobraziť filter', 'ascensio' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Áno', 'ascensio' ),
				'label_off'    => __( 'Nie', 'ascensio' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'only_featured',
			array(
				'label'        => __( 'Iba TOP ponuky', 'ascensio' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Áno', 'ascensio' ),
				'label_off'    => __( 'Nie', 'ascensio' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'trades',
			array(
				'label'       => __( 'Iba profesie', 'ascensio' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $this->term_options( 'asc_trade' ),
				'label_block' => true,
				'description' => __( 'Prázdne = všetky.', 'ascensio' ),
			)
		);

		$this->add_control(
			'countries',
			array(
				'label'       => __( 'Iba krajiny', 'ascensio' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $this->term_options( 'asc_country' ),
				'label_block' => true,
				'description' => __( 'Prázdne = všetky.', 'ascensio' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => __( 'Text na karte', 'ascensio' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Zobraziť ponuku', 'ascensio' ),
			)
		);

		$this->add_control(
			'empty_text',
			array(
				'label'   => __( 'Text, keď nie sú ponuky', 'ascensio' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Momentálne tu nemáme zverejnenú vhodnú ponuku. Pošli nám všeobecnú prihlášku – ozveme sa ti, keď sa niečo objaví.', 'ascensio' ),
			)
		);

		$this->end_controls_section();
	}

	/** Výstup. */
	protected function render() {
		$s = $this->get_settings_for_display();
		echo asc_render_jobs( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'count'         => isset( $s['count'] ) ? (int) $s['count'] : 50,
				'filters'       => isset( $s['filters'] ) && 'yes' === $s['filters'],
				'columns'       => isset( $s['columns'] ) ? (int) $s['columns'] : 3,
				'only_featured' => isset( $s['only_featured'] ) && 'yes' === $s['only_featured'],
				'trades'        => ! empty( $s['trades'] ) ? (array) $s['trades'] : array(),
				'countries'     => ! empty( $s['countries'] ) ? (array) $s['countries'] : array(),
				'button_text'   => isset( $s['button_text'] ) ? $s['button_text'] : '',
				'empty_text'    => ! empty( $s['empty_text'] ) ? $s['empty_text'] : '',
			)
		);
	}
}
