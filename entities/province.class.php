<?php
/*************************************************************************
* Class Name:       province
* File Name:        province.user.php
*  - for Table:     user
*   - in Database:  user
**************************************************************************/
 
// Begin Class "province"
class province {
    // Variable declaration
    private $idProvince; // Primary Key
	private $core;
	private $batimentProvince;
	private $idJoueur;
	private $niveauBatimentProvince;
	private $underConstruction;

	function __construct($core,$idProvince) {
        $this->core = $core;
    	$data = array('idProvince' => $idProvince);
        $sql = "SELECT * FROM provinces_joueur WHERE idProvince = :idProvince";
        $request = $this->getCore()->getDatabase()->prepare($sql);
		$result = $request->execute($data);
		$lines = $request->fetch(PDO::FETCH_ASSOC);
		if($request->rowCount()){
			foreach($lines as $property => $value){
				$this->__set($property, $value);
			}
		}
	}
	// GET Function
    public function __get($property) {
    	 return $this->$property;
    }
	// SET Function
    public function __set($property, $value) {
    	$this->$property = $value;
    }
	
    public function select($field) {

    }
 
    public function insert() {
        $sql = "INSERT INTO user () VALUES ();";
        $result = $this->getCore()->getDatabase()->query($sql);
        $this->idProvince = $this->getCore()->getDatabase()->lastinsertid;
    }
 
    function update($idProvince, $property, $value) {
        $sSQL = "UPDATE provinces_joueur SET ($property = '$value') 
                 WHERE idProvince = $idProvince;";
        $request = $this->getCore()->getDatabase()->Query($sql);
    }
 
    public function delete($idProvince) {
        $sql = "DELETE FROM user WHERE username = $idProvince;";
        $request = $this->getCore()->getDatabase()->Query($sql);
    }
 	public function getCore()
	{
		return $this->core;
	}
}
// End Class "user"
?>