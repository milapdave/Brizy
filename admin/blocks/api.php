<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/18/18
 * Time: 10:48 AM
 */


class Brizy_Admin_Blocks_Api extends Brizy_Admin_AbstractApi {

	const nonce = 'brizy-api';

	const CREATE_GET_GLOBAL_BLOCKS_ACTION = 'brizy-get-global-blocks';
	const CREATE_GET_SAVED_BLOCKS_ACTION = 'brizy-get-saved-blocks';

	const CREATE_GLOBAL_BLOCK_ACTION = 'brizy-create-global-block';
	const CREATE_SAVED_BLOCK_ACTION = 'brizy-create-saved-block';

	const UPDATE_GLOBAL_BLOCK_ACTION = 'brizy-update-global-block';
	const UPDATE_SAVED_BLOCK_ACTION = 'brizy-saved-global-block';
	const DELETE_GLOBAL_BLOCK_ACTION = 'brizy-delete-global-block';

	const DELETE_SAVED_BLOCK_ACTION = 'brizy-delete-saved-block';

	/**
	 * @return Brizy_Admin_Blocks_Api
	 */
	public static function _init() {
		static $instance;

		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	protected function getRequestNonce() {
		return $this->param( 'hash' );
	}

	protected function initializeApiActions() {
		add_action( 'wp_ajax_' . self::CREATE_GET_GLOBAL_BLOCKS_ACTION, array( $this, 'actionGetGlobalBlocks' ) );
		add_action( 'wp_ajax_' . self::CREATE_GET_SAVED_BLOCKS_ACTION, array( $this, 'actionGetSavedBlocks' ) );
		add_action( 'wp_ajax_' . self::CREATE_GLOBAL_BLOCK_ACTION, array( $this, 'actionCreateGlobalBlock' ) );
		add_action( 'wp_ajax_' . self::UPDATE_GLOBAL_BLOCK_ACTION, array( $this, 'actionUpdateGlobalBlock' ) );
		add_action( 'wp_ajax_' . self::UPDATE_SAVED_BLOCK_ACTION, array( $this, 'actionUpdateSavedBlock' ) );
		add_action( 'wp_ajax_' . self::DELETE_GLOBAL_BLOCK_ACTION, array( $this, 'actionDeleteGlobalBlock' ) );
		add_action( 'wp_ajax_' . self::CREATE_SAVED_BLOCK_ACTION, array( $this, 'actionCreateSavedBlock' ) );
		add_action( 'wp_ajax_' . self::DELETE_SAVED_BLOCK_ACTION, array( $this, 'actionDeleteSavedBlock' ) );
	}

	public function actionGetGlobalBlocks() {
		$this->verifyNonce( self::nonce );

		try {
			$blocks = $this->getBlocksByType( Brizy_Admin_Blocks_Main::CP_GLOBAL );

			$this->success( $blocks );

		} catch ( Exception $exception ) {
			$this->error( 400, $exception->getMessage() );
		}
	}

	public function actionGetSavedBlocks() {
		$this->verifyNonce( self::nonce );

		try {
			$blocks = $this->getBlocksByType( Brizy_Admin_Blocks_Main::CP_SAVED );

			$this->success( $blocks );

		} catch ( Exception $exception ) {
			$this->error( 400, $exception->getMessage() );
		}
	}

	public function actionCreateGlobalBlock() {
		$this->verifyNonce( self::nonce );

		if ( ! $this->param( 'uid' ) ) {
			$this->error( 400, 'Invalid uid' );
		}

		if ( ! $this->param( 'data' ) ) {
			$this->error( 400, 'Invalid data' );
		}

		if ( ! $this->param( 'status' ) ) {
			$this->error( 400, 'Invalid block status' );
		}

		try {
			$data  = stripslashes( $this->param( 'data' ) );
			$block = $this->createBlock( $this->param( 'uid' ), $this->param( 'status' ), Brizy_Admin_Blocks_Main::CP_GLOBAL );
			$block->set_editor_data( $data );
			$block->set_needs_compile( true );
			$block->save();
			$this->success( $block );

		} catch ( Exception $exception ) {
			$this->error( 400, $exception->getMessage() );
		}
	}

	public function actionCreateSavedBlock() {
		$this->verifyNonce( self::nonce );

		if ( ! $this->param( 'uid' ) ) {
			$this->error( 400, 'Invalid uid' );
		}

		if ( ! $this->param( 'data' ) ) {
			$this->error( 400, 'Invalid data' );
		}

		if ( ! $this->param( 'status' ) ) {
			$this->error( 400, 'Invalid block status' );
		}

		try {
			$data  = stripslashes( $this->param( 'data' ) );
			$block = $this->createBlock( $this->param( 'uid' ), $this->param( 'status' ), Brizy_Admin_Blocks_Main::CP_SAVED );
			$block->set_editor_data( $data );
			$block->set_needs_compile( true );
			$block->save();

			$this->success( $block );

		} catch ( Exception $exception ) {
			$this->error( 400, $exception->getMessage() );
		}
	}

	public function actionUpdateGlobalBlock() {
		$this->verifyNonce( self::nonce );

		try {

			if ( ! $this->param( 'uid' ) ) {
				$this->error( '400', 'Invalid uid' );
			}

			if ( ! $this->param( 'data' ) ) {
				$this->error( '400', 'Invalid data' );
			}

			$block = $this->getBlock( $this->param( 'uid' ), Brizy_Admin_Blocks_Main::CP_GLOBAL );

			$block->set_editor_data( stripslashes( $this->param( 'data' ) ) );

			if ( (int) $this->param( 'is_autosave' ) ) {
				$block->auto_save_post();
			} else {
				$block->save();
			}

			$this->success( $block->convertToOptionValue() );
		} catch
		( Exception $exception ) {
			$this->error( 400, $exception->getMessage() );
		}
	}

	public function actionUpdateSavedBlock() {
		$this->verifyNonce( self::nonce );

		try {
			if ( ! $this->param( 'uid' ) ) {
				$this->error( '400', 'Invalid uid' );
			}

			if ( ! $this->param( 'data' ) ) {
				$this->error( '400', 'Invalid data' );
			}

			$block = $this->getBlock( $this->param( 'uid' ), Brizy_Admin_Blocks_Main::CP_SAVED );

			$block->set_editor_data( stripslashes( $this->param( 'data' ) ) );

			if ( (int) $this->param( 'autosave' ) ) {
				$block->auto_save_post();
			} else {
				$block->save();
			}

			$this->success( $block->convertToOptionValue() );
		} catch ( Exception $exception ) {
			$this->error( 400, $exception->getMessage() );
		}
	}

	public function actionDeleteGlobalBlock() {
		$this->verifyNonce( self::nonce );

		if ( ! $this->param( 'uid' ) ) {
			$this->error( '400', 'Invalid uid' );
		}

		if ( $this->deleteBlock( $this->param( 'uid' ), Brizy_Admin_Blocks_Main::CP_GLOBAL ) ) {
			$this->success( null );
		}

		$this->error( '404', 'Block not found' );
	}

	public function actionDeleteSavedBlock() {
		$this->verifyNonce( self::nonce );

		if ( ! $this->param( 'uid' ) ) {
			$this->error( '400', 'Invalid uid' );
		}

		if ( $this->deleteBlock( $this->param( 'uid' ), Brizy_Admin_Blocks_Main::CP_SAVED ) ) {
			$this->success( null );
		}

		$this->error( '404', 'Block not found' );
	}

	/**
	 * @param $type
	 * @param array $arags
	 *
	 * @return array
	 * @throws Brizy_Editor_Exceptions_NotFound
	 */
	private function getBlocksByType( $type, $arags = array() ) {

		$filterArgs = array(
			'post_type'      => $type,
			'posts_per_page' => - 1,
			'post_status'    => 'any',
			'orderby'        => 'ID',
			'order'          => 'ASC',
		);
		$filterArgs = array_merge( $filterArgs, $arags );

		$wpBlocks = get_posts( $filterArgs );
		$blocks   = array();

		foreach ( $wpBlocks as $wpPost ) {
			$blocks[] = self::postData( Brizy_Editor_Block::get( $wpPost ) );
		}

		return $blocks;
	}

	/**
	 * @param $uid
	 * @param $postType
	 *
	 * @return string|null
	 */
	private function getBlockIdByUidAndBlockType( $uid, $postType ) {
		global $wpdb;

		$prepare = $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} p 
								JOIN {$wpdb->postmeta} pm  ON 
								pm.post_id=p.ID and 
								meta_key='brizy_post_uid' and 
								meta_value='%s'   
								WHERE p.post_type IN ('%s')
								LIMIT 1", array( $uid, $postType ) );

		return $wpdb->get_var( $prepare );
	}

	/**
	 * @param $uid
	 * @param $postType
	 *
	 * @return string|null
	 */
	private function getBlockIdByUid( $uid ) {
		global $wpdb;

		$prepare = $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} p 
								JOIN {$wpdb->postmeta} pm  ON 
								pm.post_id=p.ID and 
								meta_key='brizy_post_uid' and 
								meta_value='%s'   
								LIMIT 1", array( $uid, ) );

		return $wpdb->get_var( $prepare );
	}

	/**
	 * @param $id
	 * @param $postType
	 *
	 * @return Brizy_Editor_Block|null
	 * @throws Brizy_Editor_Exceptions_NotFound
	 */
	private function getBlock( $id, $postType ) {
		$postId = $this->getBlockIdByUidAndBlockType( $id, $postType );

		return Brizy_Editor_Block::get( $postId );
	}

	/**
	 * @param $uid
	 * @param $status
	 * @param $type
	 *
	 * @return Brizy_Editor_Block|null
	 * @throws Brizy_Editor_Exceptions_NotFound
	 */
	private function createBlock( $uid, $status, $type ) {
		$post = wp_insert_post( array(
			'post_status' => $status,
			'post_type'   => $type
		) );

		if ( $post ) {
			$brizyPost = Brizy_Editor_Block::get( $post, $uid );
			$brizyPost->set_uses_editor( true );
			$brizyPost->set_needs_compile( true );

			return $brizyPost;
		}

		throw new Exception( 'Unable to create block' );
	}


	/**
	 * @param $postUid
	 * @param $postType
	 *
	 * @return false|WP_Post|null
	 */
	private function deleteBlock( $postUid, $postType ) {

		$postId = $this->getBlockIdByUidAndBlockType( $postUid, $postType );

		return wp_delete_post( $postId );
	}


	/**
	 * @param Brizy_Editor_Block $post
	 *
	 * @return array
	 */
	public static function postData( Brizy_Editor_Block $post ) {

		$p_id = (int) $post->get_id();

		$global = array(
			'data'   => $post->get_editor_data(),
			'uid'    => $post->get_uid(),
			'status' => get_post_status( $p_id ),
		);

		return $global;
	}
}