<?php
namespace Code\Framework\Workflow\Models;
use Humble;
use Log;
Use Environment;
/** 
 * I/O Handler
 *
 * This class is used to add files, reports, or other data to the event
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Event
 * @author     Rick Myers <rick@humbleprogramming.com>
 * @since      File available since Release 1.0.0
 */
class FileManager extends Model
{

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Required for Helpers, Models, and Events, but not Entities
     *
     * @return system
     */
    public function getClassName() {
        return __CLASS__;
    }

    /**
     * Logs the processing of a file and also updates the configurable EVENT field with the file name
     * 
     * @param type $EVENT
     */
    public function logFile($EVENT=false) {
        if ($EVENT !== false) {
            $data = $EVENT->load();
            $cnf  = $EVENT->fetch();
            if ((isset($cnf['field']) && $cnf['field'])) {
                $EVENT->update([$cnf['field']=>$data['name']]);
            }
            $log = Humble::entity('paradigm/file/log')->setJobId($data['workflow_id']??'')->setDirectory($data['dir']??'N/A')->setFile($data['name']??'Unknown')->save();
            
        }
    }
    
    /**
     * Will allow a person to specify a file or web URL to add to the event
     *
     * @param type $EVENT
     * @workflow use(process) configuration(/workflow/file/add)
     */
    public function addFile($EVENT=false) {
        $added = false;
        if ($EVENT!==false) {
            $data = $EVENT->load();
            $cnf  = $EVENT->fetch();
            if (isset($cnf['field'])) {
                $data = @file_get_contents($cnf['resource']);
                if ($data) {
                    $EVENT->update([$cnf['field'] => $data]);
                    $loaded = true;
                }
            }
        }
        return $added;
    }
    
    /**
     * A field on the initial event has a filename that you want to load into the event (attaching the file contents to the event)
     * 
     * @param type $EVENT
     * @return boolean
     * @workflow use(process) configuration(/workflow/file/load)
     */
    public function loadFile($EVENT=false) {
        $loaded = false;
        if ($EVENT!==false) {
            $data = $EVENT->load();
            $cnf  = $EVENT->fetch();
            if (isset($cnf['field'])) {
                if (file_exists($cnf['field'])) {
                    $EVENT->update([$cnf['destination'] => file_get_contents($cnf['field'])]);
                    $loaded = true;
                }
            }
        }
        return $loaded;
    }
    
    /**
     * A field on the initial event has a filename that you want to read and save to another location
     * 
     * @param type $EVENT
     * @return boolean
     * @workflow use(process) configuration(/workflow/file/save)
     */
    public function saveFile($EVENT=false) {
        $saved = false;
        if ($EVENT!==false) {
            $data = $EVENT->load();
            $cnf  = $EVENT->fetch();
            if (isset($cnf['field'])) {
                if (file_exists($cnf['field'])) {
                    $saved = copy($cnf['field'],$cnf['destination']);
                    $EVENT->update(['save_file'=>[ 'source' => $cnf['field'],
                                    'destination_file' => $cnf['destination'],
                                    'save_result' => $saved]]);
                }
            }
        }
        return $saved;
    }    
    
    /**
     * Will take a file designated in the event and store to a location you specify in the configuration page, returning true if the file was successfully saved
     *
     * @TODO: Make this check to see if the instead of file data a file name is in the source... then fetch the file and store it, also optionally attach it to the event
     * @workflow use(process,decision) configuration(/workflow/file/store)
     * @param type $EVENT
     * @return boolean
     */
    public function storeFile($EVENT=false,$folder=false) {
        $stored = false;
        if ($EVENT!==false) {
            $data = $EVENT->load();
            $cnf  = $EVENT->fetch();
            if (isset($data[$cnf['source']])) {
                if (strpos($cnf['destination'],'/')!==false) {
                    $parts = explode('/',$cnf['destination']);
                    $dir   = implode('/',array_slice($parts,0,count($parts)-1));
                    if (!is_dir($dir)) {
                        @mkdir($dir,0775,true);
                    }
                }
                if ($cnf['timestamp_file']==='Y') {
                    $p = strrpos($cnf['destination'],'.');
                    $pre = substr($cnf['destination'],0,$p);
                    $post = substr($cnf['destination'],$p);
                    $cnf['destination'] = $pre.'_'.time().$post;
                }
                if ($data[$cnf['source']]) {
                    $stored = file_put_contents($cnf['destination'],$data[$cnf['source']]);
                } else {
                    $EVENT->error('No data to write to file '.$cnf['destination']);
                }
            }
        }
        return $stored;
    }

    /**
     * Uses a filename in an event field to move to another directory
     *
     * @workflow use(process) configuration(/workflow/file/move)
     * @param type $EVENT
     * @return boolean
     */
    public function moveFile($EVENT=false) {
        $copied = false;
        $result = ['moved'=>false,'message'=>'','RC'=>0];
        if ($EVENT!==false) {
            $data        = $EVENT->load();
            $cnf         = $EVENT->fetch();
         //   print_r($data);print_r($cnf); die();
            $source      = ($cnf['source_is']       ===  'Value')? $cnf['source'] : $data[$cnf['source']];
            $destination = ($cnf['destination_is']  ===  'Value')? $cnf['destination'] : $data[$cnf['destination']];
            $filename    = isset($data[$cnf['field']]) && $data[$cnf['field']] ? $data[$cnf['field']] : false;
            if ($source && $destination && $filename) {
                $file     = $source.DIRECTORY_SEPARATOR.$filename;
                $target   = $destination.DIRECTORY_SEPARATOR.$filename;
                print($file."==>".$target."\n");
                if ($copied = copy($file,$target)) {
                   @unlink($file);
                }
                $result['moved'] = true;
            } else {
                $result['message']  = 'Could not find file to copy, check configuration';
                $result['RC']       = 16;
            }
            $EVENT->update(['moveFile'=>$result]);
        }
        return $copied;
    }    
    
    /**
     * Uses a filename in an event field to copy to another file
     *
     * @workflow use(process) configuration(/workflow/file/copy)
     * @param type $EVENT
     * @return boolean
     */
    public function copyFile($EVENT=false) {
        $copied = false;
        if ($EVENT!==false) {
            $data = $EVENT->load();
            $cnf  = $EVENT->fetch();
            print_r($data);
            print_r($cnf);
            die();
            if (isset($data[$cnf['source']])) {
                $copied = file_put_contents($cnf['destination'],$data[$cnf['source']]);
            }
        }
        return $copied;
    }

    /**
     * Checks either an attached file or a filename to see if it is properly formatted XML
     *
     * @workflow use(decision) configuration(/workflow/file/validxml)
     * @param type $EVENT
     * @return boolean
     */
    public function isValidXML($EVENT=false) {
        $valid = false;
        if ($EVENT!==false) {
            $data = $EVENT->load();
            $cnf  = $EVENT->fetch();
            if (isset($data['xml_source'])) {
                libxml_use_internal_errors(true);
                $doc = simplexml_load_string($data['xml_source']);

                $valid = (!$doc) ? false : true;
                if (!$doc) {
                    $errors = libxml_get_errors();
                    $xml = explode("\n", $xmlstr);
                    foreach ($errors as $error) {
                        echo display_xml_error($error, $xml);
                    }
                    libxml_clear_errors();
                }
            }
            //##################################################################
            //OVERRIDDEN FOR NOW!!! REMOVE LATER!!!!!!!!!!!!!!!!!
            //##################################################################
            $valid = true;
        }
        return $valid;
    }
    
    /**
     * Returns a list of files that are of the type we wish to include
     * 
     * @param type $files
     * @param type $extensions
     * @return array
     */
    private function screenFileList($files,$extensions) {
        $extensions = explode(",",$extensions);
        $list       = [];
        foreach ($files as $file) {
            $include = false;
            foreach ($extensions as $extension) {
                $include = $include || (strpos($file,$extension));
            }
            if ($include) {
                $list[] = $file;
            }
        }
        return $list;
    }
    
    /**
     * Retrieves files from a remote site
     * 
     * @workflow use(process) configuration(/workflow/ftp/get)
     * @param event $EVENT
     */
    public function ftpGet($EVENT=false) {
        if ($EVENT!==false) {
            
        }
    }

    /**
     * Retrieves files from a remote site
     * 
     * @workflow use(process) configuration(/workflow/ftp/put)
     * @param event $EVENT
     */
    public function ftpPut($EVENT=false) {
        if ($EVENT!==false) {
            
        }        
    }
    
    /**
     * Retrieves files from a remote site
     * 
     * @workflow use(process) configuration(/workflow/sftp/get)
     * @param event $EVENT
     */
    public function sftpGet($EVENT=false) {
        require_once ('Net/SFTP.php');
        $success = false;
        $outcome = [];
        $files   = [];
        if ($EVENT!==false) {
            $cfg  = $EVENT->fetch();
            $host = $cfg['host'].':'.($cfg['port'] ? $cfg['port']: '22');
            $cfg['local_dir'] = str_replace("\\","/",$cfg['local_dir']);
            if (substr($cfg['local_dir'],strlen($cfg['local_dir'])-1,1) != "/") {
                $cfg['local_dir'] .= "/";
            }
            if (!$sftp = new \Net_SFTP($host)) {
                $outcome['sftp_get'] = ['Error' =>'Failed to connect to '.$cfg['host']];
            } else if (!$sftp->login($cfg['username'],$cfg['password'])) {
                $outcome['sftp_get'] = ['Error' =>'Authentication Failure, please check username and password'];
            } else {
                $sftp->chdir($cfg['remote_dir']);
                $file_list  = $sftp->nlist();
                if (isset($cfg['extensions']) && $cfg['extensions']) {
                    $file_list = $this->screenFileList($file_list,$cfg['extensions']);
                }
                $log = Humble::entity('humble/ftp_log');
                @mkdir($cfg['local_dir'],0775,true);
                foreach ($file_list as $filename){
                    $log->reset();
                    $include = true;
                    if (isset($cfg['new_files_only']) && ($cfg['new_files_only'] === 'Y')) {
                        $include = (!count($log->setTransport('sftp')->setHost($cfg['host'])->setFilename($filename)->load(true)));
                    }
                    if ($include) {
                        $log->setFilesize($filesize = $sftp->size($filename));
                        $filepath = $cfg['local_dir'].$filename;
                        if ($sftp->get($filename,$filepath)) {
                            $files[] = ['filename' => $filename, "filesize"=>$filesize, "destination" => $cfg['local_dir'], "filepath" => $filepath];
                            $log->save();
                        }
                    }
                }
            }
        }
        $event_field = (isset($cfg['event_field']) && $cfg['event_field']) ? $cfg['event_field'] : 'sftp_get';
        $EVENT->update([$event_field => $files]);
        return $success;
    }

    /**
     * Retrieves files from a remote site
     * 
     * @workflow use(process) configuration(/workflow/sftp/put)
     * @param event $EVENT
     */
    public function sftpPut($EVENT=false) {
        if ($EVENT!==false) {
            
        }        
    }
        

}