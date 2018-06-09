<?php

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr
{
    /**
     * Save the file to the specified path
     * @param string $path
     * @param int $size size of file in bytes
     * @return boolean TRUE on success
     */
    function save($path, $size)
    {
        @ini_set('max_execution_time', 600);  // 10 minutes max
        return jrCore_chunked_copy('php://input', $path);
    }

    function getName()
    {
        return $_REQUEST['field_name'];
    }

    function getSize()
    {
        if (isset($_SERVER["CONTENT_LENGTH"])) {
            return (int) $_SERVER["CONTENT_LENGTH"];
        }
        else {
            throw new Exception('Getting content length is not supported.');
        }
    }
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm
{
    /**
     * Save the file to the specified path
     * @param string $path
     * @return boolean TRUE on success
     */
    function save($path)
    {
        if (!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)) {
            return false;
        }
        return true;
    }

    function getName()
    {
        return $_FILES['qqfile']['name'];
    }

    function getSize()
    {
        return $_FILES['qqfile']['size'];
    }
}

class qqFileUploader
{
    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;
    private $uploadName;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760)
    {
        $allowedExtensions = array_map("strtolower", $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;
        $this->sizeLimit         = $sizeLimit;

        if (isset($_REQUEST['field_name'])) {
            $this->file = new qqUploadedFileXhr();
        }
        elseif (isset($_FILES['qqfile'])) {
            $this->file = new qqUploadedFileForm();
        }
        else {
            $this->file = false;
        }
    }

    public function getUploadName()
    {
        if (isset($this->uploadName)) {
            return $this->uploadName;
        }
        return false;
    }

    public function getName()
    {
        if ($this->file) {
            /** @noinspection PhpUndefinedMethodInspection */
            return $this->file->getName();
        }
        return false;
    }

    /**
     * Process upload
     * @param $uploadDirectory string
     * @param bool $replaceOldFile
     * @return array
     */
    function handleUpload($uploadDirectory, $replaceOldFile = true)
    {
        global $_user, $_post;
        if (!is_writable($uploadDirectory)) {
            return array('error' => "Server error. Upload directory isn't writable.");
        }
        if (!$this->file) {
            return array('error' => 'No files were uploaded.');
        }
        /** @noinspection PhpUndefinedMethodInspection */
        $size = $this->file->getSize();
        if ($size == 0) {
            return array('error' => 'File is empty');
        }
        if (!jrUser_is_admin()) {

            // Get form profile ID and make sure we're using the correct upload limit
            if (isset($_post['token'])) {
                $_sess = jrCore_form_get_session($_post['token']);
                if ($_sess && isset($_sess['form_profile_id']) && $_sess['form_profile_id'] > 0 && $_user['user_active_profile_id'] != $_sess['form_profile_id']) {
                    if ($qid = jrCore_db_get_item_key('jrProfile', $_sess['form_profile_id'], 'profile_quota_id')) {
                        if ($_qt = jrProfile_get_quota($qid)) {
                            if (isset($_qt['quota_jrCore_max_upload_size'])) {
                                $this->sizeLimit = (int) $_qt['quota_jrCore_max_upload_size'];
                            }
                        }
                    }
                }
                if ($size && $size > $this->sizeLimit) {
                    $_ln = jrUser_load_lang_strings();
                    return array('error' => $_ln['jrCore'][134] . ' ' . jrCore_format_size($this->sizeLimit));
                }
            }
        }

        $nam      = $_REQUEST['field_name'];
        $ext      = jrCore_file_extension($_REQUEST[$nam]);
        $filename = $_REQUEST[$nam];
        $these    = implode(', ', $this->allowedExtensions);

        // Check for valid extension
        if ($this->allowedExtensions && !in_array($ext, $this->allowedExtensions)) {
            $_ln = jrUser_load_lang_strings();
            return array('error' => $_ln['jrCore'][135] . ' ' . $these);
        }

        // Trigger upload_prepare event
        $_rs = array(
            'upload_directory'         => $uploadDirectory,
            'upload_name'              => $_REQUEST['upload_name'],
            'upload_order'             => $_REQUEST['orderid'],
            'field_name'               => $nam,
            'field_allowed_extensions' => $these,
            'file_name'                => $filename,
            'file_size'                => $size,
            'file_extension'           => $ext
        );

        $_rs = jrCore_trigger_event('jrCore', 'upload_prepare', $_rs);
        if (isset($_rs['error'])) {
            // We had an error in our upload from a listener
            return array('error' => $_rs['error']);
        }

        // Copy the file to our temp directory
        // See if are doing multiple uploads for the same "field" - if we are, we need
        // to increment the field number each time
        $fname = $uploadDirectory . $_REQUEST['orderid'] . '_' . $_REQUEST['upload_name'];

        /** @noinspection PhpUndefinedMethodInspection */
        if ($this->file->save($fname, $size)) {

            jrCore_write_to_file($fname . '.tmp', $filename);

            // Trigger upload_saved event
            $_rs = array(
                'success'                  => true,
                'upload_directory'         => $uploadDirectory,
                'upload_name'              => $_REQUEST['upload_name'],
                'upload_order'             => $_REQUEST['orderid'],
                'field_name'               => $nam,
                'field_allowed_extensions' => $these,
                'temp_name'                => $fname,
                'file_name'                => $filename,
                'file_size'                => $size,
                'file_extension'           => $ext,
            );
            $_rs = jrCore_trigger_event('jrCore', 'upload_saved', $_rs);
            if (is_array($_rs) && isset($_rs['error'])) {
                // We had an error from a listener - cleanup
                unlink($fname);
                unlink($fname . '.tmp');
            }
            return $_rs;

        }
        return array('error' => 'Could not save uploaded file - upload was cancelled or server error encountered');
    }
}
