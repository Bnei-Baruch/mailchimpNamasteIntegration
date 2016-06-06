<?php
class CreateGroupAndForumForCourse {
	static public function SavePost($post_id, $post, $update) {
		if (wp_is_post_revision ( $post_id ) || $post->post_status != 'publish' || $post->post_type != 'namaste_course')
			return;
		$meta = get_post_meta ( $post_id, 'buddypress_id', true );
		
		if (empty ( $meta )) {
			$group_id = groups_create_group ( array (
					'creator_id' => get_current_user_id (),
					'name' => $post->post_title,
					'description' => 'Обсуждение курса ' . $post->post_title,
					'enable_forum' => 1 
			) );
			
			update_post_meta ( $post_id, 'buddypress_id', $group_id );
			
			groups_edit_group_settings ( $group_id, 1, 'private', 'mods' );
			
			$forum_id = bbp_insert_forum ( $forum_data = array (
					'post_status' => bbp_get_private_status_id (), // bbp_get_public_status_id(),
					'post_type' => bbp_get_forum_post_type (),
					'post_author' => bbp_get_current_user_id (),
					'post_content' => 'Обсуждение курса ' . $post->post_title,
					'post_title' => $post->post_title 
			), $forum_meta = array () );
			
			bbp_update_group_forum_ids ( $group_id, ( array ) $forum_id );
			bbp_update_forum_group_ids ( $forum_id, ( array ) $group_id );
		}
		
		bbp_add_user_forum_subscription ( bbp_get_current_user_id (), $forum_id );
		update_post_meta ( $forum_id, '_forum_course_id', $post_id );
	}
	static public function EnrolledCourse($student_id, $course_id, $status) {
		global $wpdb;
		$group_id = get_post_meta ( $course_id, 'buddypress_id', true );
		// Get BuddyPress
		$bp = buddypress ();
		$group = $wpdb->get_row ( $wpdb->prepare ( "SELECT g.* FROM {$bp->groups->table_name} g WHERE g.id = %d", $group_id ) );
		
		groups_invite_user ( array (
				'user_id' => $student_id,
				'group_id' => $group_id,
				'inviter_id' => $group->creator_id,
				'date_modified' => bp_core_current_time (),
				'is_confirmed' => 1 
		) );
		
		$forums = bbp_get_group_forum_ids ( $group_id );
		bbp_add_user_forum_subscription ( bbp_get_current_user_id (), $forums [0] );
	}
	static public function UnsubscribeCourse($meta_id, $post_id, $meta_key, $meta_value) {
		// bbp_remove_user_forum_subscription ( get_current_user_id (), $_POST ['unsubscribe'] );
	}
	private static function sendEmail() {
	}
}
?>