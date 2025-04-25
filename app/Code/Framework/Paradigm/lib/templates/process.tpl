
    /**
     * METHOD SUMMARY HERE, REMOVE THE CONFIGURATION ANNOTATION IF NOT NEEDED
     * 
     * @param type $EVENT
     * @workflow use(PROCESS) configuration(/config/uri/here)
     */
    public function &&METHOD&&($EVENT=false) {
        if ($EVENT!==false) {
            $data = $EVENT->load();
            $cnf  = $EVENT->fetch();
            //put your code here
        }
        return;
    }    
