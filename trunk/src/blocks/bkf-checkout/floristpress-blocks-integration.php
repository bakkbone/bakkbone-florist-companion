<?php
use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

    class BKF_Blocks_Integration implements IntegrationInterface {

	public function get_name() {
		return 'floristpress';
	}

	public function initialize() {
		require __DIR__ . '/floristpress-extend-store-endpoint.php';
		$this->register_block_frontend_scripts();
		$this->register_block_editor_scripts();
		$this->register_block_editor_styles();
		$this->register_main_integration();
		$this->extend_store_api();
	}

	private function extend_store_api() {
		BKF_Extend_Store_Endpoint::init();
	}

	private function register_main_integration() {
		$script_path = '/build/index.js';
		$style_path  = '/build/style-floristpress.css';

		$script_url = plugins_url( $script_path, __FILE__ );
		$style_url  = plugins_url( $style_path, __FILE__ );

		$script_asset_path = dirname( __FILE__ ) . '/build/index.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: [
				'dependencies' => [],
				'version'      => $this->get_file_version( $script_path ),
			];

		wp_enqueue_style(
			'floristpress-blocks-integration',
			$style_url,
			[],
			$this->get_file_version( $style_path )
		);

		wp_register_script(
			'floristpress-blocks-integration',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);
		wp_set_script_translations(
			'floristpress-blocks-integration',
			'bakkbone-florist-companion',
			__BKF_PATH__ . '/lang'
		);
	}

	public function get_script_handles() {
		return [ 'floristpress-blocks-integration', 'card-message-block-frontend' ];
	}

	public function get_editor_script_handles() {
		return [ 'floristpress-blocks-integration', 'card-message-block-editor' ];
	}

	public function get_script_data() {
		$data = [
			'floristpress-active' => true,
		];

		return $data;

	}

	public function register_block_editor_styles() {
		$style_path = '/build/style-floristpress.css';

		$style_url = plugins_url( $style_path, __FILE__ );
		wp_enqueue_style(
			'floristpress-block',
			$style_url,
			[],
			$this->get_file_version( $style_path )
		);
	}

	public function register_block_editor_scripts() {
		$script_path       = '/build/floristpress.js';
		$script_url        = plugins_url( $script_path, __FILE__ );
		$script_asset_path = dirname( __FILE__ ) . '/build/floristpress.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: [
				'dependencies' => [],
				'version'      => $this->get_file_version( $script_asset_path ),
			];

		wp_register_script(
			'floristpress-editor',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		wp_set_script_translations(
			'floristpress-editor',
			'bakkbone-florist-companion',
			__BKF_PATH__ . '/lang'
		);
	}

	public function register_block_frontend_scripts() {
		$script_path       = '/build/floristpress-frontend.js';
		$script_url        = plugins_url( $script_path, __FILE__ );
		$script_asset_path = dirname( __FILE__ ) . '/build/floristpress-frontend.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: [
				'dependencies' => [],
				'version'      => $this->get_file_version( $script_asset_path ),
			];

		wp_register_script(
			'floristpress-frontend',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);
		wp_set_script_translations(
			'floristpress-frontend',
			'bakkbone-florist-companion',
			__BKF_PATH__ . '/lang'
		);
	}

	protected function get_file_version( $file ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && file_exists( $file ) ) {
			return filemtime( $file );
		}
		return __BKF_VERSION__;
	}
}
