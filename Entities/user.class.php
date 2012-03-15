<?php
/*************************************************************************
* Class Name:       province
* File Name:        province.user.php
*  - for Table:     user
*   - in Database:  user
**************************************************************************/
 
// Begin Class "province"
class joueur {
    // Variable declaration
    public $idJoueur; // Primary Key
	public $core;
	private $pseudo;
	private $pass;	
	private $mail;
	private $publicMail	;
	private $mailNotification;	
	private $idTerritoire;	
	private $statut;
	private $dateInscription;	
	private $connecte;
	private $banni;
	private $recherches;
	private $unites;
	private $bois;
	private $argent;
	private $pierre;
	private $ble;
	private $chevaux;	
	private $couleur;
	private $derniereMaj;
	private $ennemis;
	private $points;
	private $rang;
	private $detailsRoyaume;
	private $y_min;
	private $x_min;
	private $y_max;
	private $x_max;

	function __construct($core,$pseudo) {
        $this->core = $core;
    	$data = array('pseudo' => $pseudo);
        $sql = "SELECT * FROM joueurs WHERE pseudo = :pseudo";
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
	
	public function correctPass($pass){
		return($this->pass == $pass);	
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