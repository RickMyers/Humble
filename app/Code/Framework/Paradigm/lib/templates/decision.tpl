
    /**
     * METHOD SUMMARY HERE, REMOVE THE CONFIGURATION ANNOTATION IF NOT NEEDED
     * 
     * @param type $EVENT
     * @return boolean
     * @workflow use(DECISION) configuration(/config/uri/here)
     */
    public function &&METHOD&&($EVENT=false) {
        $result = false;
        if ($EVENT!==false) {
            $data = $EVENT->load();
            $cnf  = $EVENT->fetch();
            //Your code here
        }
        return $result;
    }    
