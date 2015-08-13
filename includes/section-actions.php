<?php

function AddNewCourseToMailChimp($segmentName) {
    $conditions = array(
        'field' => 'COURSES',
        'op' => 'like',
        'value' => ('[' . strval($segmentName) . ']')
    );
    $segParam = array(
        'match' => 'any',
        'conditions' => $conditions
    );
   $optsParam = array(
        'type' => 'saved',
        'name' => strval($segmentName),
       'segment_opts' => $segParam
    );
    $sendObj = new MailChimpSend('createSegment', $userEmail);
    //$sendObj->$parameters->opts = $optsParam;
    //$sendObj -> SendToMailChimp();
}


function UpdateUserOnMailChimp($studentId, $courseId, $status) {
    $wpCurrentUser = wp_get_current_user();
    if ($status == 'enrolled') {
        $sendGetUserObj = new MailChimpSend('getUserData', $wpCurrentUser ->user_email);

        $userMChObjJson = $sendGetUserObj ->SendToMailChimp();
        $userMChObjJson = json_decode($userMChObjJson)->data;

        $sOldCourses = $userMChObjJson[0]->merges->COURSES;
        $sNewCourse = '[' . $courseId . ']';
        $pos = strripos($sOldCourses, $sNewCourse);
        if ($pos === false) {
            // не подписан -> подписываем
            $sendObj = new MailChimpSend('setUserData', $userEmail);
            $sendObj->$parameters->merge_vars ->COURSES = $sOldCourses . $sNewCourse;;
            $sendObj -> SendToMailChimp();
        }
    }
}




?>