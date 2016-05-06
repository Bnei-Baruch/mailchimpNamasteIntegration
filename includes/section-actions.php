<?php
function AddCourseToMailChimp($post_ID, $post) {
	$condition = array (
			'field' => 'COURSES',
			'op' => 'like',
			'value' => ('[' . strval ( $post_ID ) . ']') 
	);
	$segParam = array (
			'match' => 'all',
			'conditions' => array (
					$condition 
			) 
	);
	$optsParam = array (
			'type' => 'saved',
			'name' => strval ( $post_ID ),
			'segment_opts' => $segParam 
	);
	$sendObj = new MailChimpSend ( 'createSegment' );
	$sendObj->parameters ['opts'] = $optsParam;
	$sendObj->SendToMailChimp ();
}

?>