<?php

ini_set('display_errors', '1');
header("Access-Control-Allow-Origin: *");

require __DIR__ . '/config.php';

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_NUMBER_INT);

switch ($action) {

    case 1:

        $fieldvalue = filter_input(INPUT_POST, 'fieldvalue');
        $id = filter_input(INPUT_POST, 'id');
        $dataobject = (object) array('id' => $id, 'contents' => $fieldvalue);

        $updateResult = $DB->update_record('lesson_pages', $dataobject, $bulk = false);

        if ($updateResult) {
            $data['data'] = array('response' => $updateResult, 'text' => 'Successfully Updated');
        } else {
            $data['data'] = array('response' => $updateResult, 'text' => 'An Error Occured While Saving');
        }

        echo json_encode($data);
        break;

    case 2:

        $fieldvalue = filter_input(INPUT_POST, 'fieldvalue');
        $id = filter_input(INPUT_POST, 'id');
        $colid = filter_input(INPUT_POST, 'colId');

        $dataobject = (object) array('id' => $id, $colid => $fieldvalue);

        $updateResult = $DB->update_record('lesson_pages', $dataobject, $bulk = false);

        if ($updateResult) {

            $data['data'] = array('response' => $updateResult, 'text' => 'Successfully Updated');
        } else {
            $data['data'] = array('response' => $updateResult, 'text' => 'An Error Occured While Saving');
        }

        echo json_encode($data);
        break;

    case 3:


        $page_name = filter_input(INPUT_POST, 'page_name');
        $section_id = filter_input(INPUT_POST, 'section_id');
        $parent_id = filter_input(INPUT_POST, 'parent_id');
        $content = filter_input(INPUT_POST, 'content');
        $course_id = filter_input(INPUT_POST, 'course_id');
        $is_section = false;
        $section = null;
//
//        echo json_encode(array('command' => "moosh -n activity-add --name='" . $page_name . "' --section=" . $section_id . " --options='--content=" . $content . "' page " . $course_id));
//                exit;
//        //moosh script
        $page_id = shell_exec('"C:\xampp\moosh" -n activity-add --name="' . $page_name . '" --section=' . $section_id . ' page ' . $course_id);

        $dataobject = (object) array('id' => $page_id, 'content' => $content);

        $updateResult = $DB->update_record('page', $dataobject, $bulk = false);

        if ($parent_id == 0) {

            $is_section = true;

            $section = $DB->get_field('course_modules', 'section', ['instance' => $page_id, 'module' => 16], $strictness = IGNORE_MISSING);
			
        }

        $data = array('is_section' => $is_section, 'section' => $section, 'page_id' => $page_id);

        echo json_encode($data);

        break;

    case 4:

        $lesson_id = filter_input(INPUT_POST, 'lesson', FILTER_SANITIZE_NUMBER_INT);
        $module_id = filter_input(INPUT_POST, 'module', FILTER_SANITIZE_NUMBER_INT);

        $content = $DB->get_field('lesson_pages', 'contents', ['id' => $module_id, 'lessonid' => $lesson_id], $strictness = IGNORE_MISSING);

        $item = ['content' => $content];
        echo json_encode(['item' => $item]);

        break;

    case 5:

        $lessonid = filter_input(INPUT_POST, 'lessonid');
        $title = filter_input(INPUT_POST, 'title');
        $contents = filter_input(INPUT_POST, 'contents');

        $dataobject = (object) [
                    'lessonid' => $lessonid,
                    'qtype' => 20,
                    'layout' => 1,
                    'display' => 1,
                    'title' => $title,
                    'contents' => $contents,
                    'contentsformat' => 1,
        ];

        $updateResult = $DB->insert_record('lesson_pages', $dataobject, $returnid = true, $bulk = false);

        if ($updateResult) {

            $data['data'] = array('response' => $updateResult, 'text' => 'Successfully Updated');
        } else {
            $data['data'] = array('response' => $updateResult, 'text' => 'An Error Occured While Saving');
        }

        echo json_encode($data);
        break;

    case 6:

        $course_id = filter_input(INPUT_GET, 'course', FILTER_SANITIZE_NUMBER_INT);

        $query = "
            SELECT
              lesson_pages.id,
              lesson_pages.title
            FROM
              mdl_lesson_pages lesson_pages
              JOIN mdl_course_modules course_modules
                ON course_modules.instance = lesson_pages.lessonid
            WHERE course_modules.course = ?
              AND course_modules.module = ?";

        $params = ['course' => $course_id, 'module' => 14];

        $pages = $DB->get_records_sql_menu($query, $params);

        $values[] = [0, ''];
        foreach ($pages as $id => $name) {
            $values[] = array($id, $name);
        }
        echo json_encode($values);
        break;

    case 7:

        $page_id = filter_input(INPUT_POST, 'page_id');
        $content = filter_input(INPUT_POST, 'content');

        $dataobject = (object) array('id' => $page_id, 'content' => $content);

        $updateResult = $DB->update_record('page', $dataobject, $bulk = false);

        echo json_encode($updateResult);

        break;
    case 8:

        $fieldvalue = filter_input(INPUT_POST, 'content');
        $id = filter_input(INPUT_POST, 'page_id');
        $dataobject = (object) array('id' => $id, 'contents' => $fieldvalue);

        $updateResult = $DB->update_record('lesson_pages', $dataobject, $bulk = false);

        if ($updateResult) {
            $data['data'] = array('response' => $updateResult, 'text' => 'Successfully Updated');
        } else {
            $data['data'] = array('response' => $updateResult, 'text' => 'An Error Occured While Saving');
        }

        echo json_encode($updateResult);
        break;

    case 9:

        $instanceid = filter_input(INPUT_POST, 'instance_id');
        $module_id = filter_input(INPUT_POST, 'module_id');
        $course_id = filter_input(INPUT_POST, 'course_id');

        $conditions = ['instance' => $instanceid, 'module' => $module_id];

        $deleteResult = $DB->delete_records('course_modules', $conditions);


        if ($deleteResult) {
            $data['data'] = array('response' => $deleteResult, 'text' => "Deleted");
        } else {
            $data['data'] = array('response' => $deleteResult, 'text' => "Not Deleted");
        }

        shell_exec('moosh -n cache-course-rebuild ' . $course_id);
        echo json_encode($data);

        break;

    case 10:


        $instanceid = filter_input(INPUT_POST, 'instance_id');
        // $module_id = filter_input(INPUT_POST, 'module_id');
        $course_id = filter_input(INPUT_POST, 'course_id');

        $conditions = ['id' => $instanceid];

        $deleteResult = $DB->delete_records('lesson_pages', $conditions);


        if ($deleteResult) {
            $data['data'] = array('response' => $deleteResult, 'text' => "Lesson Page Deleted");
        } else {
            $data['data'] = array('response' => $deleteResult, 'text' => "Lesson Page Not Deleted");
        }

        shell_exec('moosh -n cache-course-rebuild ' . $course_id);
        echo json_encode($data);

        break;

    case 11:

        $lessonid = filter_input(INPUT_POST, 'lessonid');
        $prevpageid = filter_input(INPUT_POST, 'prevpageid');
        $qtype = filter_input(INPUT_POST, 'qtype');
        $title = filter_input(INPUT_POST, 'title');
        $contents = filter_input(INPUT_POST, 'contents');

        $dataobject = (object) [
                    'lessonid' => $lessonid,
                    'prevpageid' => $prevpageid,
                    'qtype' => $qtype,
                    'layout' => 1,
                    'display' => 1,
                    'title' => $title,
                    'contents' => $contents,
                    'contentsformat' => 1
        ];

        $pageid = $DB->insert_record("lesson_pages", $dataobject, $returnid = true, $bulk = false);

        $data = [
            'pageid' => $pageid
        ];

        echo json_encode($data);

        break;

    case 12:

        $lessonid = filter_input(INPUT_POST, 'lessonid');
        $pageid = filter_input(INPUT_POST, 'pageid');
        $score = filter_input(INPUT_POST, 'score');
        $answer = filter_input(INPUT_POST, 'answer');
        $response = filter_input(INPUT_POST, 'response');
        $responseformat = filter_input(INPUT_POST, 'responseformat');

        $object = (object) [
                    'lessonid' => $lessonid,
                    'pageid' => $pageid,
                    'score' => $score,
                    // 'jumpto' => $jumpto,
                    'answer' => $answer,
                    'response' => $response,
                    'responseformat' => $responseformat
        ];

        $insertResult = $DB->insert_record("lesson_answers", $object, $returnid = true, $bulk = false);

        $data = [
            'status' => 'success'
        ];

        echo json_encode($data);

        break;


    case 13:

        $lessonid = filter_input(INPUT_POST, 'lessonid');
        $page_id = filter_input(INPUT_POST, 'prevpageid');
        $object = filter_input(INPUT_POST, 'question');

        $questions = unserialize($object);

        $prevpageid = $page_id;

        $pageIds = array();
        $answerIds = array();

        foreach ($questions as $question) {

            $obj = (object) [
                        'lessonid' => $lessonid,
                        'prevpageid' => $prevpageid,
                        'qtype' => $question->qtype,
                        'qoption' => $question->qoption,
                        'layout' => 1,
                        'display' => 1,
                        'title' => $question->title,
                        'contents' => $question->contents,
                        'contentsformat' => 1
            ];

            $pageid = $DB->insert_record("lesson_pages", $obj, $returnid = true, $bulk = false);
            $prevpageid = $pageid;

            $pageIds[$question->id] = $pageid;

            if ($pageid) {

                $choices = $question->choices;

                if (count($choices) > 0) {

                    $insertObjects = array();

                    foreach ($choices as $choice) {

                        $answer = (object) [
                                    'lessonid' => $lessonid,
                                    'pageid' => $pageid,
                                    'score' => $choice->score,
                                    'jumpto' => $choice->jumpto,
                                    'answer' => $choice->answer,
                                    'response' => $choice->response,
                                    'responseformat' => $choice->responseformat
                        ];

                        $answerid = $DB->insert_record("lesson_answers", $answer, $returnid = true, $bulk = false);
                        $answerIds[$choice->id] = $answerid;
                    }
                }
            }
        }

        $data = [
            'success' => true,
            'page_ids' => $pageIds,
            'choice_ids' => $answerIds
        ];

        echo json_encode($data);

        break;


    case 14:

        $lessonid = filter_input(INPUT_POST, 'lessonid');
        // $module_id = filter_input(INPUT_POST, 'module_id');
        $course_id = filter_input(INPUT_POST, 'course_id');

        $conditions = ['id' => $lessonid];

        $deleteResult = $DB->delete_records('lesson', $conditions);


        if ($deleteResult) {
            $data['data'] = array('response' => $deleteResult, 'text' => "Lesson Page Deleted");
        } else {
            $data['data'] = array('response' => $deleteResult, 'text' => "Lesson Page Not Deleted");
        }

        shell_exec('moosh -n cache-course-rebuild ' . $course_id);
        echo json_encode($data);

        break;

    case 15:

        $section_id = filter_input(INPUT_POST, 'section_id');
        // $module_id = filter_input(INPUT_POST, 'module_id');
        $course_id = filter_input(INPUT_POST, 'course_id');

        $conditions = ['id' => $section_id, 'course' => $course_id];

        $deleteResult = $DB->delete_records('course_sections', $conditions);


        if ($deleteResult) {
            $data['data'] = array('response' => $deleteResult, 'text' => "Lesson Page Deleted");
        } else {
            $data['data'] = array('response' => $deleteResult, 'text' => "Lesson Page Not Deleted");
        }

        shell_exec('moosh -n cache-course-rebuild ' . $course_id);
        echo json_encode($data);

        break;

    case 16:

        $lesson_id = filter_input(INPUT_POST, 'lesson_id');
        $name = filter_input(INPUT_POST, 'name');
        $course_id = filter_input(INPUT_POST, 'course_id');

        $dataobject = (object) array('id' => $lesson_id, 'name' => $name);

        $updateResult = $DB->update_record('lesson', $dataobject, $bulk = false);
        shell_exec('moosh -n cache-course-rebuild ' . $course_id);
        if ($updateResult) {
            $data['data'] = array('response' => $updateResult, 'text' => 'Successfully Updated');
        } else {
            $data['data'] = array('response' => $updateResult, 'text' => 'An Error Occured While Saving');
        }

        echo json_encode($updateResult);
        //update topic name
        break;
    case 17:

        $id = filter_input(INPUT_POST, 'id');
        $name = filter_input(INPUT_POST, 'name');
        $course_id = filter_input(INPUT_POST, 'course_id');
        $dataobject = (object) array('id' => $id, 'title' => $name);
        $updateResult = $DB->update_record('lesson_pages', $dataobject, $bulk = false);

        shell_exec('moosh -n cache-course-rebuild ' . $course_id);
        if ($updateResult) {
            $data['data'] = array('response' => $updateResult, 'text' => 'Successfully Updated');
        } else {
            $data['data'] = array('response' => $updateResult, 'text' => 'An Error Occured While Saving');
        }

        echo json_encode($updateResult);

        break;

    case 18:

        $page_id = filter_input(INPUT_POST, 'page_id');
        $name = filter_input(INPUT_POST, 'name');
        $content = filter_input(INPUT_POST, 'content');

        $dataobject = (object) array('id' => $page_id, 'name' => $name, 'content' => $content);

        $updateResult = $DB->update_record('page', $dataobject, $bulk = false);

        echo json_encode($updateResult);

        break;
    default:
        break;
}