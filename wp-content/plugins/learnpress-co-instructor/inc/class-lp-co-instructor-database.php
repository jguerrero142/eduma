<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class LP_Database
 *
 * @since 3.0.8
 * @author tungnx
 */
class LP_CO_Instructor_DB {
	public static $_instance;
	public $wpdb;
	public $tb_lp_user_items;
	public $tb_lp_user_itemmeta;
	public $tb_lp_order_items;
	public $tb_postmeta;
	public $tb_posts;
	public $tb_lp_section_items;
	public $tb_lp_sections;

	protected function __construct() {
		/**
		 * @global wpdb
		 */
		global $wpdb;

		$this->wpdb                 = $wpdb;
		$this->tb_posts             = $this->wpdb->posts;
		$this->tb_postmeta          = $this->wpdb->postmeta;
		$this->tb_lp_user_items     = $this->wpdb->prefix . 'learnpress_user_items';
		$this->tb_lp_user_itemmeta  = $this->wpdb->prefix . 'learnpress_user_itemmeta';
		$this->tb_lp_order_items    = $this->wpdb->prefix . 'learnpress_order_items';
		$this->tb_lp_order_itemmeta = $this->wpdb->prefix . 'learnpress_order_itemmeta';
		$this->tb_lp_section_items  = $this->wpdb->prefix . 'learnpress_section_items';
		$this->tb_lp_sections       = $this->wpdb->prefix . 'learnpress_sections';
	}

	public static function getInstance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Get list course of instructor or co-instructor
	 *
	 * @param int $user_id
	 *
	 * @return array
	 */
	public function get_post_of_instructor( $user_id = 0 ) {
		$query = $this->wpdb->prepare(
			"SELECT DISTINCT p.ID FROM $this->tb_posts AS p
	                INNER JOIN $this->tb_postmeta AS pm ON p.ID = pm.post_id
	                WHERE ( p.post_author = %d AND p.post_type = %s  )
		            OR ( ( pm.meta_key = %s AND pm.meta_value= %d
		            AND p.post_type = %s ))",
			$user_id, LP_COURSE_CPT, '_lp_co_teacher', $user_id, LP_COURSE_CPT );

		$result = $this->wpdb->get_col( $query );

		return $result;
	}

	/**
	 * Get count list course of instructor or co-instructor
	 *
	 * @param int $user_id
	 *
	 * @return array
	 * @since 3.0.9
	 */
	public function get_count_post_of_instructor( $user_id = 0 ) {
		$query = $this->wpdb->prepare(
			"
				SELECT M.post_status, count(M.post_status) as num_posts
				FROM (SELECT post_status
				      FROM wp_posts AS p
				               INNER JOIN wp_postmeta AS pm ON p.ID = pm.post_id
				      WHERE (p.post_author = %d AND p.post_type = %s) OR
				          (pm.meta_key = %s AND pm.meta_value = %d AND p.post_type = %s)
				      GROUP BY p.ID) as M
				GROUP BY M.post_status
		        ",
			$user_id, LP_COURSE_CPT, '_lp_co_teacher', $user_id, LP_COURSE_CPT );

		$result = $this->wpdb->get_results( $query, ARRAY_A );

		return $result;
	}
}

LP_CO_Instructor_DB::getInstance();

