<?php
namespace Code\Framework\Admin\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Web Socket Server Interactions
 *
 * see title
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick <rick@humbleprogramming.com>
 */
class Sockets extends Model
{

    use \Code\Framework\Humble\Traits\EventHandler;
	
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
     * Will download a zip containing the hub server and set it up on the local machine
     * 
     * @return string
     */
    public function install() {
        $port    = $this->getPort();
        $host    = $this->getHost();
        $message = [ 'message' =>'Installation Failed','RC'=>16];
        if ($project = json_decode(file_get_contents('../Humble.project'),true)) {
            $remote = $project['framework_url'].'/distro/socketserver';
            file_put_contents('sockets.zip',file_get_contents($remote));
            $project['hub_host'] = $host;
            $project['hub_port'] = $port;
            file_put_contents('../Humble.project',json_encode($project,JSON_PRETTY_PRINT));
            $zip = new \ZipArchive;
            if ($zip->open('sockets.zip') === TRUE) {
                @mkdir('../Hub',0775);
                file_put_contents('../Hub/main.js',$zip->getFromName('main.js'));
                file_put_contents('../Hub/.gitignore',$zip->getFromName('.gitignore'));
                file_put_contents('../Hub/package.json',$zip->getFromName('package.json'));
                $zip->close();            
                @unlink('sockets.zip');
                chdir('../Hub');
                exec('npm install',$result,$rc);
                $message['message'] = 'Installation Successful';
                $message['RC']      = $rc;
                chdir('../app');
            }
        }
        return $message;
    }
    
    /**
     * Starts the socket server in the background
     * 
     * @return string
     */
    public function start() {
        $message = "Socket Server Is Already Running, Or You May Have To Clear the PID";
        if (!file_exists('PIDS/sockets.pid') && (is_dir('../Hub'))) {
            chdir('../Hub');
            $cmd = "nohup node main.js > /dev/null 2>&1 &";
            exec($cmd,$results,$rc);
            $message = "Socket Server Started [".$rc."]";
        } 
        return $message;
    }

    /**
     * Stops the socket server
     * 
     * @return string
     */    
    public function stop() {
        $message = "Could not stop Socket Server, you will have to do it manually";
        $pid_file = 'PIDS/sockets.pid';
        if (file_exists($pid_file)) {
            $pid    = trim(file_get_contents($pid_file));
            $rc     = posix_kill($pid,15);
            @unlink($pid_file);
            $message = "Socket Server Stopped [".$rc."]";
        }
        return $message;
    }
    
    /**
     * Restarts the socket server
     * 
     * @return string
     */    
    public function restart() {
        $this->stop();
        $this->start();
    }
    
    /**
     * Checks the status of the socket server
     * 
     * @return string
     */    
    public function status() {
        $status     = false;
        $project    = Environment::project();
        if ($project->hub_host && $project->hub_port) {
            $status     = @file_get_contents($project->hub_host.':'.$project->hub_port.'/status');
        }
        return $status;
    }
}