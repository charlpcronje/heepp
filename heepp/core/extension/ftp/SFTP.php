<?php

/** class.sftp.php
 *
 * 	Short and simple sftp class.
 *
 * 	@author Simon Samtleben <support@lemmingzshadow.net>
 * 	@version 0.4
 *
 * 	[Changelog] (Please comment changes here!)
 *
 * 		2010-08-12 by Simon Samtleben <support@lemmingzshadow.net>
 * 		Modified sftp_putfile to use sftp subsystem instead of scp. Update to v0.4.
 *
 * 		2009-09-11 by Simon Samtleben <support@lemmingzshadow.net>
 * 		Cleanup. Update to v0.31.
 *
 * 		2009-07-06 by Simon Samtleben <support@lemmingzshadow.net>
 * 		Added methods: sftp_rename, sftp_listdir
 */
namespace core\extension\ftp;

class SFTP extends \core\extension\Extension {

    private $sftp_error = null;
    private $ssh_connection = null;
    private $sftp_connection = null;

    // @DO: Auto connect if server data is passed.
    public function __construct() {
        
    }

    // @DO: Destroy open connections.
    public function __destruct() {
        
    }

    /**
     * Opens ssh2 connection and sets sftp handle.
     *
     * @param array $server_data Sould contain ftphost, ftpport, ftpuser and ftppass for destination server.
     * @return bool True if connection could be established False if not.
     */
    public function sftp_connect($server_data) {
        $server_data['ftpport'] = (!isset($server_data['ftpport'])) ? 22 : (int) $server_data['ftpport'];
        if (!isset($server_data['ftphost']) || !isset($server_data['ftpport']) || !isset($server_data['ftpuser']) || !isset($server_data['ftppass'])) {
            $this->set_sftp_error(1);
            return false;
        }
        $this->ssh_connection = \ssh2_connect($server_data['ftphost'], $server_data['ftpport']);
        if ($this->ssh_connection === false) {
            $this->set_sftp_error(2);
            return false;
        }
        if (ssh2_auth_password($this->ssh_connection, $server_data['ftpuser'], $server_data['ftppass']) === false) {
            $this->set_sftp_error(3);
            return false;
        }
        if (($this->sftp_connection = ssh2_sftp($this->ssh_connection)) === false) {
            $this->set_sftp_error(6);
            return false;
        }

        return true;
    }

    /**
     * Close ssh connection by unsetting connection handle.
     *
     * 	@return bool true if connection is closed false on error.
     */
    public function sftp_disconnect() {
        if ($this->ssh_connection == null || $this->ssh_connection === false) {
            $this->set_sftp_error(4);
            return false;
        }
        unset($this->ssh_connection);
        return true;
    }

    /**
     * Create a folder on destination server.
     *
     * 	@param string $path The path to create.
     * 	@param int $mode The chmod value the folder should have.
     * 	@param bool $recursive On true all parent foldes are created too.
     * 	@return bool True on success false on error.
     */
    public function sftp_mkdir($path, $mode = 0755, $recursive = false) {
        if (ssh2_sftp_mkdir($this->sftp_connection, $path, $mode, $recursive) === false) {
            $this->set_sftp_error(5);
            return false;
        }
        return true;
    }

    /** function sftp_putfile
     *
     * 	Uploads a file to destination server using scp.
     *
     * 	@param string $local_file Path to local file.
     * 	@param string $remote_file Path to destination file.
     * 	@param string $mode Chmod destination file to this value.
     * 	@return bool True on success false on error.
     */
    public function sftp_putfile($local_file, $remote_file, $mode = 0664) {
        $remote_file = (substr($remote_file, 0, 1) != '/') ? '/' . $remote_file : $remote_file;
        $sftp_stream = fopen('ssh2.sftp://' . $this->sftp_connection . $remote_file, 'w');

        if (!$sftp_stream) {
            $this->set_sftp_error(7);
            return false;
        }

        $data_to_send = file_get_contents($local_file);

        if ($data_to_send === false) {
            $this->set_sftp_error(7);
            return false;
        }

        if (fwrite($sftp_stream, $data_to_send) === false) {
            $this->set_sftp_error(7);
            return false;
        }
        fclose($sftp_stream);
        return true;
    }

    /**
     * Deletes a file on sftp server.
     *
     * @param string $file File to delete.
     * @return bool False on error true if file was deleted.
     */
    public function sftp_unlink($file) {
        if (!ssh2_sftp_unlink($this->sftp_connection, $file) === false) {
            $this->set_sftp_error(8);
            return false;
        }
        return true;
    }

    /**
     * List directory content.
     *
     * @param string $path Path to directory which should be listed.
     * @return array $filelist List of directory content.
     */
    public function sftp_listdir($path = '/') {
        $dir = 'ssh2.sftp://' . $this->sftp_connection . $path;
        $filelist = array();
        if (($handle = opendir($dir)) !== false) {
            while (false !== ($file = readdir($handle))) {
                if (substr($file, 0, 1) != ".") {
                    $filelist[] = $file;
                }
            }
            closedir($handle);
            return $filelist;
        } else {
            $this->set_sftp_error(9);
            return false;
        }
    }

    /**
     * Rename a file on sftp server.
     *
     * @param string $filename_from The current filename.
     * @param string $filename_to The new filename.
     * @return bool True on success false on error.
     */
    public function sftp_rename($filename_from, $filename_to) {
        if (ssh2_sftp_rename($this->sftp_connection, $filename_from, $filename_to) === false) {
            $this->set_sftp_error(10);
            return false;
        }
        return true;
    }

    /**
     * Sets an error message by passing an error code.
     *
     * 	@param int $error_code Numeric value representing an error message.
     * 	@return bool True if massage was set false on error.
     */
    protected function set_sftp_error($error_code) {
        switch ($error_code) {
            case 1:
                $this->sftp_error = 'Server data not complete.';
                break;

            case 2:
                $this->sftp_error = 'Connection to Server could not be established.';
                break;

            case 3:
                $this->sftp_error = 'Could not authenticate at server.';
                break;

            case 4:
                $this->sftp_error = 'No active connection to close.';
                break;

            case 5:
                $this->sftp_error = 'Could not create dir.';
                break;

            case 6:
                $this->sftp_error = 'Could not initialize sftp subsystem.';
                break;

            case 7:
                $this->sftp_error = 'Could not upload file to target server.';
                break;

            case 8:
                $this->sftp_error = 'Could not delete remote file.';
                break;

            case 9:
                $this->sftp_error = 'Could not open remote directoty.';
                break;

            case 10:
                $this->sftp_error = 'Could not rename file.';
                break;
        }
    }

    /**
     * Return the current error message.
     *
     * 	@return string The error message.
     */
    public function get_sftp_error() {
        return $this->sftp_error;
    }

}
