<?php
/*
Plugin Name: LearnPress - Co-Instructors
Plugin URI: http://thimpress.com/learnpress
Description: Building courses with other instructors.
Author: ThimPress
Version: 3.1.0
Author URI: http://thimpress.com
Tags: learnpress, lms, add-on, co-instructor
Text Domain: learnpress-co-instructor
Domain Path: /languages/
*/

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

define( 'LP_ADDON_CO_INSTRUCTOR_FILE', __FILE__ );
define( 'LP_ADDON_CO_INSTRUCTOR_VER', '3.1.0' );
define( 'LP_ADDON_CO_INSTRUCTOR_REQUIRE_VER', '3.0.0' );

if ( ! class_exists( 'LP_Co_Instructor_Preload' ) ) {

	/**
	 * Class LP_Co_Instructor_Preload
	 */
	class LP_Co_Instructor_Preload {

		/**
		 * LP_Co_Instructor_Preload constructor.
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			//add_action( 'thim_core_activate_plugin', array( $this, 'install' ) );
			add_action( 'learn-press/ready', array( $this, 'add_on_loaded' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			register_activation_hook( __FILE__, array( $this, 'install' ) );
			register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );

			// Check can view post
			add_action( 'current_screen', array( $this, 'check_co_instructor_can_view_edit_post' ));

			// Check can save post
			add_action( 'wp_insert_post_data', array( $this, 'check_co_instructor_can_save_edit_post' ), 10, 2 );
			add_filter( 'learn-press/modal-search-items/args', array(
				$this,
				'load_all_items_instructor_on_course'
			), 10, 1 );
		}

		/**
		 * Load plugin main class.
		 *
		 * @since 3.0.0
		 */
		public function add_on_loaded() {
			$this->install();
			LP_Addon::load( 'LP_Addon_Co_Instructor', 'inc/load.php', __FILE__ );
			remove_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}

		/**
		 * Require Learnpress.
		 */
		public function admin_notices() {
			?>
            <div class="error">
                <p><?php echo wp_kses(
						sprintf(
							__( '<strong>%s</strong> addon version %s requires %s version %s or higher is <strong>installed</strong> and <strong>activated</strong>.', 'learnpress-co-instructor' ),
							__( 'LearnPress Co-Instructor', 'learnpress-co-instructor' ),
							LP_ADDON_CO_INSTRUCTOR_VER,
							sprintf( '<a href="%s" target="_blank"> <strong>%s</strong></a>', admin_url( 'plugin-install.php?tab=search&type=term&s=learnpress' ), __( 'LearnPress', 'learnpress-co-instructor' ) ),
							LP_ADDON_CO_INSTRUCTOR_REQUIRE_VER
						),
						array(
							'a'      => array(
								'href'  => array(),
								'blank' => array()
							),
							'strong' => array()
						)
					); ?>
                </p>
            </div>
			<?php
		}

		/**
		 * Plugin install, add teacher capacities.
		 *
		 * @since 3.0.0
		 *
		 */
		public function install() {
			// Set role for co-instructor
			$teacher_role = get_role( LP_TEACHER_ROLE );

			if ( $teacher_role ) {
				/*if ( ! $teacher_role->has_cap( 'edit_others_lp_lessons' ) ) {
					$teacher_role->add_cap( 'edit_others_lp_lessons' );
				}*/

				if ( ! $teacher_role->has_cap( 'edit_others_lp_courses' ) ) {
					$teacher_role->add_cap( 'edit_others_lp_courses' );
				}
			}
		}

		/**
		 * Check instructor can edit post of another instructor
		 *
		 * @Logic  1: instructor, co-instructor only edit items yourself, only see on frontend
		 * @Logic  2: instructor can view, edit item of co-instructor and vice versa (not use, not option)
		 *
		 * @param WP_Screen $data
		 *
		 * @return mixed
		 * @since  3.0.8
		 * @editor tungnx
		 */
		public function check_co_instructor_can_view_edit_post( $data ) {
			$user_id = get_current_user_id();

			if ( user_can( $user_id, 'administrator' ) ) {
				return $data;
			}

			if ( is_admin() && function_exists( 'get_current_screen' ) && user_can( $user_id, 'lp_teacher' ) ) {
				$current_screen   = get_current_screen();
				$custom_post_type = apply_filters( 'learn-press/co-instructor/case-post-type-can-edit', 'lp-instructor-post-type-can-edit' );
				$screen_check_arr = array(
					LP_COURSE_CPT,
					LP_LESSON_CPT,
					LP_QUESTION_CPT,
					LP_QUIZ_CPT,
					$custom_post_type
				);
				$flag_can_edit    = true;

				if ( $current_screen && in_array( $current_screen->id, $screen_check_arr ) ) {
					if ( isset( $_GET['post'] ) ) {
						$post_id = absint( $_GET['post'] );
						$post    = get_post( $post_id );

						if ( $user_id == $post->post_author ) {
							return $data;
						}

						// Check lp course post
						switch ( $post->post_type ) {
							case LP_COURSE_CPT:
								$co_instructor_ids = get_post_meta( $post->ID, '_lp_co_teacher', false );

								if ( ! is_array( $co_instructor_ids ) || empty( $co_instructor_ids ) || ! in_array( $user_id, $co_instructor_ids ) ) {
									$flag_can_edit = false;
								}
								break;
							case LP_LESSON_CPT:
							case LP_QUESTION_CPT:
							case LP_QUIZ_CPT:
							case $custom_post_type:
								try {
									// Logic 1: instructor, co-instructor only edit items your self, only see on frontend
									/*if ( $user_id != $post->post_author ) {
										$flag_can_edit = false;
									}*/

									/**
									 * @Logic 2: instructor can view, edit item of co-instructor and vice versa (not use, not option)
									 */
									// Case 1: Co-instructor access lesson of Instructor

									// Case 2: Instructor access lesson of Co-instructor
									/*** End Logic 2 ***/
								} catch ( Exception $e ) {

								}
								break;
							default:
								break;
						}
					}
				}

				if ( ! $flag_can_edit ) {
					wp_die( 'Sorry, you are not allowed to edit this post.' );
				}
			}

			return $data;
		}

		/**
		 * Check instructor can edit post of another instructor
		 *
		 * @Logic  1: instructor, co-instructor only edit items yourself, only see on frontend
		 * @Logic  2: instructor can view, edit item of co-instructor and vice versa (not use, not option)
		 *
		 * @param array | WP_Screen $data | variation of hook wp_insert_post_data
		 * @param array $datarr | variation of hook wp_insert_post_data
		 *
		 * @return mixed
		 * @since  3.0.8
		 * @editor tungnx
		 */
		public function check_co_instructor_can_save_edit_post( $data, $datarr = array() ) {
			$user_id = get_current_user_id();

			if ( user_can( $user_id, 'administrator' ) ) {
				return $data;
			}

			if ( is_admin() && function_exists( 'get_current_screen' ) && user_can( $user_id, 'lp_teacher' ) ) {
				$current_screen   = get_current_screen();
				$custom_post_type = apply_filters( 'learn-press/co-instructor/case-post-type-can-edit', 'lp-instructor-post-type-can-edit' );
				$screen_check_arr = array(
					LP_COURSE_CPT,
					LP_LESSON_CPT,
					LP_QUESTION_CPT,
					LP_QUIZ_CPT,
					$custom_post_type
				);
				$flag_can_edit    = true;

				if ( $current_screen && in_array( $current_screen->id, $screen_check_arr ) ) {
					if ( ! is_array( $datarr ) || ! isset( $datarr['post_author'] ) || ! isset( $datarr['post_type'] ) ) {
						$flag_can_edit = false;
					}

					switch ( $data['post_type'] ) {
						case LP_COURSE_CPT:
							$co_instructor_ids = get_post_meta( $datarr['ID'], '_lp_co_teacher', false );

							if ( ! is_array( $co_instructor_ids ) || empty( $co_instructor_ids ) || ! in_array( $user_id, $co_instructor_ids ) ) {
								if ( $datarr['post_author'] != $user_id ) {
									$flag_can_edit = false;
								}
							}
							break;
						case LP_LESSON_CPT:
						case LP_QUESTION_CPT:
						case LP_QUIZ_CPT:
						case $custom_post_type:
							try {
								// Logic 1: instructor, co-instructor only edit items your self, only see on frontend
								/*if ( $user_id != $post->post_author ) {
									$flag_can_edit = false;
								}*/

								/**
								 * @Logic 2: instructor can view, edit item of co-instructor and vice versa (not use, not option)
								 */
								// Case 1: Co-instructor access lesson of Instructor

								// Case 2: Instructor access lesson of Co-instructor
								/*** End Logic 2 ***/
							} catch ( Exception $e ) {

							}
							break;
						default:
							break;
					}
				}

				if ( ! $flag_can_edit ) {
					wp_die( 'Sorry, you are not allowed to edit this post.' );
				}
			}

			return $data;
		}

		public function load_all_items_instructor_on_course( $args_query ) {

			$user_id = get_current_user_id();

			if ( ! $user_id ) {
				return $args_query;
			}

			if ( ! user_can( $user_id, LP_TEACHER_ROLE ) ) {
				return $args_query;
			}

			if ( isset( $args_query['author'] ) ) {
				// Logic 1: only load items of instructor yourself
				unset( $args_query['author'] );
				$args_query['author__in'] = array( $user_id );

				// Logic 2: load items of instructor and co-instructors
			}

			return $args_query;
		}


		/**
		 * Plugin uninstall, remove teacher capacities.
		 *
		 * @since 3.0.0
		 */
		public function uninstall() {
			/*** Remove cab of instructor can edit post not yourself ***/
			$teacher_role = get_role( LP_TEACHER_ROLE );

			if ( $teacher_role ) {
				$teacher_role->remove_cap( 'edit_others_lp_lessons' );
				$teacher_role->remove_cap( 'edit_others_lp_courses' );
			}
			/*** End ***/
		}
	}
}

new LP_Co_Instructor_Preload();
