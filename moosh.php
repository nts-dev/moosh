<?php

ini_set('display_errors', '1');
header("Access-Control-Allow-Origin: *");
require __DIR__ . '/config.php';
// Make the connection:
$dbc = mysqli_connect('localhost', 'root', '', 'moodle');

if (!$dbc) {
    trigger_error('Could not connect to MySQL: ' . mysqli_connect_error());
}

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_NUMBER_INT);
$mooshDir = "C:\xampp\moosh";

switch ($action) {

    case 1:

        $course_id = filter_input(INPUT_POST, 'course', FILTER_SANITIZE_NUMBER_INT);
        $section_id = filter_input(INPUT_POST, 'section', FILTER_SANITIZE_NUMBER_INT);
        $section_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

        $page_id = shell_exec('"C:\xampp\moosh" -n activity-add --name="' . $section_name . '" --section=' . $section_id . ' --options="--content=' . $section_name . '" page ' . $course_id);

        if ($page_id) {
            $data['data'] = array('response' => true, 'text' => 'Successfully Added', 'row_id' => $page_id);
        } else {
            $data['data'] = array('response' => false, 'text' => 'An Error Occured While Saving');
        }
        echo json_encode($data);
        break;

    case 2:

        $course_id = filter_input(INPUT_GET, 'course', FILTER_SANITIZE_NUMBER_INT);
        $section_id = filter_input(INPUT_GET, 'section', FILTER_SANITIZE_NUMBER_INT);

        $page_id = shell_exec('"C:\xampp\moosh" -n activity-add --name="' . $section_id . '" --section=' . $section_id . ' --options="--content=Long content description" page 12');

        if ($page_id) {
            $data['data'] = array('response' => true, 'text' => 'Successfully Added', 'row_id' => $page_id);
        } else {
            $data['data'] = array('response' => false, 'text' => 'An Error Occured While Saving');
        }
        echo json_encode($data);
        break;

    case 3:

        $course_id = filter_input(INPUT_GET, 'course', FILTER_SANITIZE_NUMBER_INT);
        $index = filter_input(INPUT_POST, 'index');
        $fieldvalue = filter_input(INPUT_POST, 'fieldvalue');
        $id = filter_input(INPUT_POST, 'id');
        $field = filter_input(INPUT_POST, 'colId');
        $colType = filter_input(INPUT_POST, 'colType');
        $fieldvalue = mysqli_real_escape_string($dbc, $fieldvalue);

        $updateResult = shell_exec('"C:\xampp\moosh" -n activity-config-set activity ' . $id . ' page name "' . $fieldvalue . '"');

        if ($updateResult) {

            shell_exec('"C:\xampp\moosh" -n cache-course-rebuild ' . $course_id);
            $data['data'] = array('response' => $updateResult, 'text' => 'Successfully Updated');
        } else {
            $data['data'] = array('response' => $updateResult, 'text' => 'An Error Occured While Saving');
        }

        echo json_encode($data);
        break;

    case 4:

        $course_id = filter_input(INPUT_GET, 'course', FILTER_SANITIZE_NUMBER_INT);
        $fieldvalue = filter_input(INPUT_POST, 'fieldvalue');
        $id = filter_input(INPUT_POST, 'id');
        $fieldvalue = mysqli_real_escape_string($dbc, $fieldvalue);

        $updateResult = shell_exec('"C:\xampp\moosh" -n activity-config-set activity ' . $id . ' page content "' . $fieldvalue . '"');

        if ($updateResult) {

            shell_exec('"C:\xampp\moosh" -n cache-course-rebuild ' . $course_id);
            $data['data'] = array('response' => $updateResult, 'text' => 'Successfully Updated');
        } else {
            $data['data'] = array('response' => $updateResult, 'text' => 'An Error Occured While Saving');
        }

        echo json_encode($data);
        break;

    case 5:

        $course_id = filter_input(INPUT_POST, 'course_id', FILTER_SANITIZE_NUMBER_INT);
        $section_id = filter_input(INPUT_POST, 'section_id', FILTER_SANITIZE_NUMBER_INT);
        $section_name = filter_input(INPUT_POST, 'page_name', FILTER_SANITIZE_STRING);
        $intro = '';

        $lesson_id = shell_exec('"C:\xampp\moosh" -n activity-add --name="' . $section_name . '" --section=' . $section_id . ' --options="--title=' . $intro . '" lesson ' . $course_id);
        $dataobject = (object)array('id' => $lesson_id, 'intro' => '', 'allowofflineattempts' => 1);


        $updateResult = $DB->update_record('lesson', $dataobject, $bulk = false);

        $module_id = $DB->get_field('course_modules', 'id', ['instance' => $lesson_id, 'module' => 14], $strictness = IGNORE_MISSING);

        if ($lesson_id) {
            $data['data'] = array('response' => true, 'text' => 'Successfully Added', 'row_id' => $lesson_id, 'module_id' => $module_id);
        } else {
            $data['data'] = array('response' => false, 'text' => 'An Error Occured While Saving');
        }
        echo json_encode($data);
        break;

        break;

    case 6:

        $course_id = filter_input(INPUT_POST, 'course_id', FILTER_SANITIZE_NUMBER_INT);
        $docid = filter_input(INPUT_POST, 'docid', FILTER_SANITIZE_NUMBER_INT);
        $course_name = filter_input(INPUT_POST, 'doc_name', FILTER_SANITIZE_STRING);

        $counter = 1;
        $version = 'Version';
        // $filename = 'Version_' . $counter . str_replace(" ", "", $course_name);
        $srcFile = 'http://192.168.1.2/CourseFiles/documentFiles/Copy of C001 Moodle tutorial (1)/images/image1.png';
        //  $filename = dirname( __FILE__ ) . "/".$course_name.".zip";
//        $mform->addElement('filepicker', 'userfile', get_string('file'), null,
//            array('maxbytes' => 1024, 'accepted_types' => '*'));
//        var_dump($mform);
//        exit;
//        $success = $mform->save_file('userfile', $srcFile, true);


        $response = shell_exec('"C:\xampp\moosh" file-upload --filepath=backup --contextid=579 -f course image1.png');


        if ($response) {
            $data['data'] = array('response' => true, 'text' => 'Successfully Added', 'row_id' => $response);
        } else {
            $data['data'] = array('response' => false, 'text' => $response . ' An Error Occured While Saving');
        }
        echo json_encode($data);
        exit();
        $dst = 'my_backups/' . $course_name;
        if (!file_exists($dst)) {
            if (!mkdir($dst, 0755) && !is_dir($dst)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $course_name));
            }
        }
        if (is_dir($dst)) {
            echo "Exist";
            recurse_copy($src, $dst);
        } else {
            echo "Not exist";
        }


        $response = shell_exec('"C:\xampp\moosh" -n course-backup -f my_backups/"' . $filename . '".mbz ' . $course_id);

        $data = [
            'response' => true,
            'version' => $course_name . ' ' . $version . ' ' . $counter,
            'file_name' => $filename . '.mbz',
            'docid' => $docid,
            'text' => $response,
        ];

        while ($response == null) {

            $filename = 'Version_' . $counter . str_replace(" ", "", $course_name);
            $response = shell_exec('"C:\xampp\moosh" -n course-backup -f my_backups/"' . $filename . '".mbz ' . $course_id);

            $data = [
                'response' => true,
                'version' => $course_name . ' ' . $version . ' ' . $counter,
                'file_name' => $filename . '.mbz',
                'docid' => $docid,
                'text' => $response,
            ];

            $counter++;

        }

        echo json_encode($data);
        break;

    case 7:

        $course_id = filter_input(INPUT_POST, 'course_id', FILTER_SANITIZE_NUMBER_INT);
        //$section_id = filter_input(INPUT_POST, 'section_id', FILTER_SANITIZE_NUMBER_INT);
        $course_name = filter_input(INPUT_POST, 'doc_name', FILTER_SANITIZE_STRING);

        $filename = 'mdl_backup' . str_replace(" ", "", $course_name);

        $response = shell_exec('"C:\xampp\moosh" course-restore --overwrite my_backups/"' . $course_name . '" ' . $course_id);

        if ($response) {
            $data['data'] = array('response' => true, 'text' => $response);
        } else {
            $data['data'] = array('response' => false, 'text' => $response);
        }
        echo json_encode($data);

        break;

    case 8:

        $module_id = filter_input(INPUT_GET, 'course', FILTER_SANITIZE_NUMBER_INT);
        // $section_id = filter_input(INPUT_GET, 'section', FILTER_SANITIZE_NUMBER_INT);

        $response = shell_exec('"C:\xampp\moosh" activity-delete' . $module_id);

        if ($response) {
            $data['data'] = array('response' => true, 'text' => 'Deleted!');
        } else {
            $data['data'] = array('response' => false, 'text' => 'Problem Occured!');
        }
        echo json_encode($data);
        break;

    case 9:

        $module_id = filter_input(INPUT_POST, 'module_id', FILTER_SANITIZE_NUMBER_INT);
        $module_id_to = filter_input(INPUT_POST, 'module_id_to', FILTER_SANITIZE_NUMBER_INT);
        $section_id = filter_input(INPUT_POST, 'section', FILTER_SANITIZE_NUMBER_INT);
        $course_id = filter_input(INPUT_POST, 'course_id', FILTER_SANITIZE_NUMBER_INT);

        $response = shell_exec('"C:\xampp\moosh" -n activity-move -s ' . $section_id . ' ' . $module_id . ' ' . $module_id_to);
        // $response = shell_exec('"C:\xampp\moosh" activity-move 15942 ' );
		
        shell_exec('"C:\xampp\moosh" -n cache-course-rebuild ' . $course_id);

        if ($response) {
            $data['data'] = array('response' => true, 'text' => $module_id . ' Moved to ' . $module_id_to);
        } else {
            $data['data'] = array('response' => false, 'text' => 'Problem Occured!');
        }
        echo json_encode($data);
        break;


    case 10:

        $path = filter_input(INPUT_POST, 'path', FILTER_SANITIZE_STRING);
        $module_id_to = filter_input(INPUT_POST, 'module_id_to', FILTER_SANITIZE_NUMBER_INT);
        $section_id = filter_input(INPUT_POST, 'section', FILTER_SANITIZE_NUMBER_INT);
        $course_id = filter_input(INPUT_POST, 'course_id', FILTER_SANITIZE_NUMBER_INT);
        $response = shell_exec('"C:\xampp\moosh" file-upload image.png');
        // $response = shell_exec('"C:\xampp\moosh" activity-move 15942 ' );
        //shell_exec('"C:\xampp\moosh" -n cache-course-rebuild ' . $course_id);
        if ($response) {
            $data['data'] = array('response' => true, 'text' => $response);
        } else {
            $data['data'] = array('response' => false, 'text' => $response);
        }
        echo json_encode($data);
        break;

    case 11:
        $page_id = filter_input(INPUT_POST, 'page_id', FILTER_SANITIZE_NUMBER_INT);
        $module_id = filter_input(INPUT_POST, 'module_id', FILTER_SANITIZE_NUMBER_INT);
        $file_name = filter_input(INPUT_POST, 'image_name');
        $image_content = filter_input(INPUT_POST, 'image_content');
        $isPage = filter_input(INPUT_POST, 'isPage');

        if($isPage) {
            $module_id = $DB->get_field('course_modules', 'id', ['instance' => $page_id, 'module' => 16], $strictness = IGNORE_MISSING);
        }

        $context = get_context_instance(CONTEXT_MODULE, $module_id);
        $fs = get_file_storage();

        $fileinfo = array(
            'contextid' => $context->id, // ID of context
            'component' => 'mod_lesson',     // usually = table name
            'filearea' => 'page_contents',     // usually = table name
            'itemid' => $page_id,               // usually = ID of row in table
            'filepath' => '/',           // any path beginning and ending in /
            'filename' => $file_name); // any filename

        $file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
            $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);
        if ($file) {
            $file->delete();
        }
        $fs->create_file_from_string($fileinfo, $image_content);
        $files = $fs->get_area_files($context->id, 'mod_lesson', 'page_contents', $page_id);
       $imageUrl='';
        foreach ($files as $file) {
            $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename(), false);
            if (strpos($url, $file_name) !== false) {
                $imageUrl = $url;
            }
        }
        $response = [
            'response' => true,
            'image'=> "".$imageUrl,
        ];
      echo json_encode($response);
  
        break;
		case 12:

        $course_id = filter_input(INPUT_POST, 'course', FILTER_SANITIZE_NUMBER_INT);
	
        $response = shell_exec('"C:\xampp\moosh" -n course-delete '. $course_id );
                   
        if ($response) {
            $data['data'] = array('response' => true, 'text' => $response);
        } else {
            $data['data'] = array('response' => false, 'text' => $response);
        }
        echo json_encode($data);

    default:
        break;
}
function recurse_copy($src, $dst)
{
    $dir = opendir($src);
    if (!mkdir($dst) && !is_dir($dst)) {
        echo "dir not found";
    }
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

// Create zip
function createZip($zip, $dir, $filename)
{
    if (is_dir($dir)) {
        echo "Is Dir";
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {

                // If file
                if (is_file($dir . $file)) {
                    if ($file != '' && $file != '.' && $file != '..') {

                        $zip->addFile($dir . $file);
                    }
                } else {
                    // If directory
                    if (is_dir($dir . $file)) {

                        if ($file != '' && $file != '.' && $file != '..') {

                            // Add empty directory
                            $zip->addEmptyDir($dir . $file);

                            $folder = $dir . $file . '/';

                            // Read data of the folder
                            createZip($zip, $folder);
                        }
                    }

                }

            }
            closedir($dh);
        }

    }

//    //$filename = "myzipfile.zip";
//
//    if (file_exists($filename)) {
//        header('Content-Type: application/zip');
//        header('Content-Disposition: attachment; filename="'.basename($filename).'"');
//        header('Content-Length: ' . filesize($filename));
//
//        flush();
//        readfile($filename);
//        // delete file
//        unlink($filename);
//
//    }

}