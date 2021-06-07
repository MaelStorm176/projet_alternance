<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/class/tools.class.php");
ini_set('memory_limit', '4046M'); 
class Mysql extends Tools
{
	public
	$Serveur     = '',
	$Bdd         = '',
	$Identifiant = '',
	$Mdp         = '',
	$Email_Site  = '',
	$Lien        = '',
	$Debogue     = true,
	$NbRequetes  = 0;
	
	/**
	 * Constructeur de la classe
	 * Connexion aux serveur de base de donn�e et s�lection de la base
	 *
	 * $Serveur     = L'h�te (ordinateur sur lequel Mysql est install�)
	 * $Bdd         = Le nom de la base de donn�es
	 * $Identifiant = Le nom d'utilisateur
	 * $Mdp         = Le mot de passe
	 */
	public
	function __construct()
	{
		/*
		$this->Serveur     = $param["BDD"]["hote"];
		$this->Bdd         = $param["BDD"]["nombdd"];
		$this->Identifiant = $param["BDD"]["identifiant"];
		$this->Mdp         = $param["BDD"]["motdepasse"];
		*/
		$param_ini = parse_ini_file($_SERVER["DOCUMENT_ROOT"]."/_config/projet.ini");
		$base = 0;
		$serveur = null;
		$loginadm = 0;
		$pwdadm = 0;
		$portadm = 0;
		$email_site = "";
		/***************** Liste des bases de données *****************/
		$tab_base = array();
		foreach ($param_ini as $Key => $valeur)
		{
			if (substr($Key,0,7)=="serveur")
				array_push($tab_base,$valeur);
		}
		foreach ($tab_base as $Key=>$valeur)
		{
			$serveur = $param_ini['serveur'.$Key];
			$portadm = $param_ini['portadm'.$Key];
			$base = $param_ini['base'.$Key];
			$loginadm = $param_ini['loginadm'.$Key];
			$pwdadm = $param_ini['pwdadm'.$Key];
			$email_site= $param_ini['email'.$Key];
		}
		if ($serveur == null)
		{
			Tools::Warning("Problème de connexion au serveur de données!!!");
			die();
		}
		
		if(isset($portadm) && isset($serveur) && $portadm!=0)
			$adresseadm = $serveur.":".$portadm;
		else if(isset($serveur) && (!isset($portadm) || $portadm==0))
			$adresseadm = $serveur;
		
		$this->Serveur = $adresseadm;
		$this->Bdd = $base;
		$this->Identifiant = $loginadm;
		$this->Mdp = $pwdadm;
		$this->Lien= "";
		$this->Email_Site = $email_site;
		if ($this->Identifiant != "" && $this->Bdd != "" && $this->Serveur != "")
		{
			
			$this->Lien=new mysqli($this->Serveur, $this->Identifiant, $this->Mdp,$this->Bdd) or die(parent::WarningSQL('Erreur de connexion au serveur MySqli!!!'));
			if (!$this->Lien)
			{
				die("Erreur de connexion à la base de données ! Veuillez vous reconnecter à l'application.");
			}
			
			//$this->Lien=new PDO($this->Serveur.";".$this->Bdd,.$this->Identifiant, $this->Mdp) or die(parent::WarningSQL('Erreur de connexion au serveur MySqli!!!'));
		//if (!$this->Lien && $this->Debogue) throw new Erreur ('Erreur de connexion au serveur MySql!!!');
			//$Base = mysql_select_db($this->Bdd,$this->Lien) or die(parent::WarningSQL('Erreur de connexion à la base de donnees!!!'));
			//$_SESSION['db'] = "1";
			mysqli_query($this->Lien,"SET NAMES 'utf8'");
		}
		else
		{
			Tools::Warning("Impossible de se connecter à la base de données SQL !!! <br />Vous pouvez éventuellement faire CTRL+F5 afin de corriger ce problème.");
		}
		
		
		//$charset = mysql_client_encoding($this->Lien);
		//echo $charset;
		//if (!$Base && $this->Debogue) throw new Erreur ('Erreur de connexion � la base de donnees!!!');
	}
	public function Close()
	{
		if(!isset($this->Lien) || $this->Lien=="")
			parent::WarningSQL("Impossible de fermer la connexion car la connexion a été perdue");
		else
			mysqli_close($this->Lien);
	}
	/**
	 * Retourne le nombre de requ�tes SQL effectu� par l'objet
	 */
	public
	function ChangeSetName($name)
	{
		mysqli_query($this->Lien,"SET NAMES '".$name."'");
	}
	
	
	/**
	 * Retourne le nombre de requ�tes SQL effectu� par l'objet
	 */
	public
	function RetourneNbRequetes()
	{
		if(!isset($this->Lien) || $this->Lien=="")
			parent::WarningSQL("La connexion à la base de données est perdue");
		else
			return $this->NbRequetes;
	}
	/**
	 * Retourne le nombre de requ�tes SQL effectu� par l'objet
	 */
	public
	function ErreurMysql()
	{
		if(!isset($this->Lien) || $this->Lien=="")
			parent::WarningSQL("La connexion à la base de données est perdue pour afficher l'erreur");
		else
		{
			return mysql_error($this->Lien);
		}
	}
	public
	function ErreurNoMysql()
	{
		if(!isset($this->Lien) || $this->Lien=="")
			parent::WarningSQL("La connexion à la base de données est perdue");
		else
			return mysql_errno($this->Lien);
	}
	/**
	 * Envoie une requ�te SQL et r�cup�re le r�sult�t dans un tableau pr� format�
	 *
	 * $Requete = Requ�te SQL
	 */
	public
	function TabResSQL($Requete)
	{
		if(!isset($this->Lien) || $this->Lien=="")
			parent::WarningSQL("La connexion à la base de données est perdue");
		else
		{
			$i = 0;
			//self::Securise_post();
			/*
			if (get_magic_quotes_gpc()) {
				$Requete = mysqli_real_escape_string($this->Lien,stripslashes($Requete));	 
			} else {

			} 
			*/
			$Requete = mysqli_real_escape_string($this->Lien,$Requete);	
			$Requete = str_replace('\n','',$Requete);
			$Requete = str_replace('\r','',$Requete);
			$Requete= str_replace("\\","",$Requete);
			$Ressource = mysqli_query($this->Lien,$Requete);
			$TabResultat=array();
			if (!$Ressource)
			{
			}
			else
			{
				//on test si load data in file dans la requete
				if (strpos($Requete,"LOAD DATA INFILE") === false)
				{
					
					//while ($Ligne = mysqli_fetch_assoc($Ressource))
					while ($Ligne = $Ressource->fetch_assoc())
					{
						//foreach ($Ligne as $clef => $valeur) $TabResultat[$i][$clef] = stripslashes(htmlspecialchars($valeur, ENT_QUOTES,"UTF-8"));
						foreach ($Ligne as $clef => $valeur) $TabResultat[$i][$clef] =  htmlspecialchars(stripslashes($valeur));
						$i++;
					}
					mysqli_free_result($Ressource);
					$this->NbRequetes++;
				}
			}
			return $TabResultat;
		}
	}
	/**
	 * Retourne le dernier identifiant g�n�r� par un champ de type AUTO_INCREMENT
	 *
	 */
	public
	function DernierId()
	{
		if(!isset($this->Lien) || $this->Lien=="")
			parent::WarningSQL("La connexion à la base de données est perdue");
		else
			return mysqli_insert_id($this->Lien);
	}
	/**
	 * Envoie une requ�te SQL et retourne le nombre de table affect�
	 *
	 * $Requete = Requ�te SQL
	 */
	public function ExecuteSQL($Requete)
	{
		if(!isset($this->Lien) || $this->Lien=="")
			parent::WarningSQL("La connexion à la base de données est perdue");
		else
		{
			/*
			if (get_magic_quotes_gpc()) {
				$Requete = mysqli_real_escape_string($this->Lien,stripslashes($Requete));	 
			} else {
				
			} 
			*/
			
			if(!isset($this->Lien) || $this->Lien=="")
				self::__construct();
			$_ = array();
			foreach($_POST as $key=>$val)
			{
				if (is_array($_POST[$key]) === false)
				{
					$_POST[$key]=mysqli_real_escape_string($this->Lien,str_replace("'", "`",trim($val)));
				}
			}			
			//$Requete = mysqli_real_escape_string($this->Lien,$Requete);
			//$Requete= str_replace("\\","",str_replace('\r\n','',$Requete));
			$Ressource = mysqli_query($this->Lien,$Requete);// or die(parent::WarningSQL($Requete,mysql_error($this->Lien)));
			//if (!$Ressource and $this->Debogue) throw new Erreur ('Erreur de requ�te SQL!!!');
			$this->NbRequetes++;
			$NbAffectee = mysqli_affected_rows($this->Lien);
			if ($NbAffectee == -1)
				die(parent::WarningSQL($Requete,mysqli_error($this->Lien)));
			return $NbAffectee;
		}
	}
	public function Test_requeteSQL($Requete)
	{
		if(!isset($this->Lien) || $this->Lien=="")
			parent::WarningSQL("La connexion à la base de données est perdue");
		else
		{
			$Ressource = mysqli_query($this->Lien,$Requete);
			//if (!$Ressource and $this->Debogue) throw new Erreur ('Erreur de requ�te SQL!!!');
			$NbAffectee = mysqli_affected_rows($this->Lien);
			return $NbAffectee;
		}
	}
	public function Securise_post()
	{
		if(!isset($this->Lien) || $this->Lien=="")
			self::__construct();
		$_ = array();
		foreach($_POST as $key=>$val)
		{
			if (is_array($_POST[$key]) === false)
			{
				$_POST[$key]=mysqli_real_escape_string($this->Lien,str_replace("'", "`",trim($val)));
			}
		}
	}
}
?>