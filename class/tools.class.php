<?php

//----------------------------------------------------------------- INCLUDE

//------------------------------------------------------- Include personnel

//------------------------------------------------------------------ CLASSE

/*
**********************  Classe statique utilitaire
*/
include_once("_db.class.php");
class Tools
{
	//Fonctiojn retourne un heure en ajoutant ou enlevant des heures minutes
	// $heureretour = heure à modifier
	public static function ajout_soustrait_heure_minute($heureretour,$heuremodif,$minutemodif,$operateur)
	{
		if (($operateur == "-" || $operateur == "+") && is_numeric($heuremodif) && is_numeric($minutemodif))
			return date("H:i",strtotime($heureretour." ".$operateur.$heuremodif." hours ".$operateur.$minutemodif." minutes"));
		else
			return $heureretour;
	}

	public static function date_getHolidays( $datecontrol) 
	{
		
		$workingDays=false;
		if ($datecontrol != null) 
		{
			$year = substr($datecontrol,0,4);
			$ary_holidays = array_merge( self::date_getArrayHolidays( $year , "Y-m-d" ) , self::date_getArrayHolidays( $year+1 , "Y-m-d" ) );
			$easterDate  = easter_date($year);
			$easterDay   = date('j', $easterDate);
			$easterMonth = date('n', $easterDate);
			$easterYear   = date('Y', $easterDate);
			$holidays = array(
				// Dates fixes
				date( $datecontrol, mktime(0, 0, 0, 1,  1,  $year)),  // 1er janvier
				date( $datecontrol, mktime(0, 0, 0, 5,  1,  $year)),  // Fête du travail
				date( $datecontrol, mktime(0, 0, 0, 5,  8,  $year)),  // Victoire des alliés
				date( $datecontrol, mktime(0, 0, 0, 7,  14, $year)),  // Fête nationale
				date( $datecontrol, mktime(0, 0, 0, 8,  15, $year)),  // Assomption
				date( $datecontrol, mktime(0, 0, 0, 11, 1,  $year)),  // Toussaint
				date( $datecontrol, mktime(0, 0, 0, 11, 11, $year)),  // Armistice
				date( $datecontrol, mktime(0, 0, 0, 12, 25, $year)),  // Noel
				// Dates variables
				date( $datecontrol, mktime(0, 0, 0, $easterMonth, $easterDay + 1,  $easterYear)),  // Lundi de paques
				date( $datecontrol, mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear)),  // Jeudi de Ascension
				date( $datecontrol, mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear)),  // Lundi de Pentecôte
			);
			
			if( in_array( $datecontrol , $ary_holidays ) ) 
				$workingDays=true;
		}
		return $workingDays;  
	}
	//Fonction calcul de temps écoulé entre deux date au format Y-m-d H:i:s
	// Retourn Jour-Heure-Minute-Seconde écoulés
	
    public static function TempsEcoule($date_sqldeb,$date_sqlfin) 
	{
		$datetime1 = date_create($date_sqldeb);
		$datetime2 = date_create($date_sqlfin);
		$interval = date_diff($datetime1, $datetime2);
		return array($interval->format('%d'), $interval->format('%H'), $interval->format('%I'),$interval->format('%S')); 
	}
	public static function convertSeconde_Time($Time)
	{
		//date_default_timezone_set('UTC');
		/*
		echo "Seconde = ".$seconde;
		return date('h:i:s', $seconde);*/
		if($Time < 3600)
		{ 
			$heures = "00"; 
			if($Time < 60){$minutes = "00";} 
			else{$minutes = round($Time / 60);} 
			$secondes = floor($Time % 60); 
		} 
		else
		{ 
			$heures = round($Time / 3600); 
			$secondes = round($Time % 3600); 
			$minutes = floor($secondes / 60); 
		} 
		$secondes2 = round($secondes % 60); 
		IF ($secondes2==0) $secondes2="00";
		IF (strlen($secondes2)==1) $secondes2="0".$secondes2;
		IF (strlen($minutes)==1) $minutes="0".$minutes;
		return $heures.":".$minutes.":".$secondes2; 
		
	}
	public static function date_getArrayHolidays( $year = null , $format = "Y-m-d" ) 
	{
		$holidays = array();
		if ($year === null) {
			$year = intval(date('Y'));
		}
		if ($year>"1970" && $year<"2037")
		{	
			$easterDate  = easter_date($year);
			$easterDay   = date('j', $easterDate);
			$easterMonth = date('n', $easterDate);
			$easterYear   = date('Y', $easterDate);

			$holidays = array(
				// Dates fixes
				date( $format, mktime(0, 0, 0, 1,  1,  $year)),  // 1er janvier
				date( $format, mktime(0, 0, 0, 5,  1,  $year)),  // Fête du travail
				date( $format, mktime(0, 0, 0, 5,  8,  $year)),  // Victoire des alliés
				date( $format, mktime(0, 0, 0, 7,  14, $year)),  // Fête nationale
				date( $format, mktime(0, 0, 0, 8,  15, $year)),  // Assomption
				date( $format, mktime(0, 0, 0, 11, 1,  $year)),  // Toussaint
				date( $format, mktime(0, 0, 0, 11, 11, $year)),  // Armistice
				date( $format, mktime(0, 0, 0, 12, 25, $year)),  // Noel
				// Dates variables
				date( $format, mktime(0, 0, 0, $easterMonth, $easterDay + 1,  $easterYear)),  // Lundi de paques
				date( $format, mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear)),  // Jeudi de Ascension
				date( $format, mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear)),  // Lundi de Pentecôte
			);
			sort($holidays);
		}
		return $holidays;  
	}
	// $date_ts = Date de départ
	// $workingDays = Nombre de jours pour compter le nombre de jours fériés
	public static function date_addWorkingDays( $date_ts, $workingDays) 
	{
		// Tableau contenant les jours fériés
		$year = date("Y", $date_ts );
		$ary_holidays = array_merge( self::date_getArrayHolidays( $year , "Y-m-d" ) , self::date_getArrayHolidays( $year+1 , "Y-m-d" ) );

		// Ajout des jours ouvrés
		$i=0;
		while( $i < $workingDays) {
			$date_tmp = date( "Y-m-d" , strtotime ( $i . ' weekdays' , $date_ts ) );
			if( in_array( $date_tmp , $ary_holidays ) ) {
				$workingDays++;
			}
			$i++;
		}
		return strtotime ( $workingDays . ' weekdays' , $date_ts );
	}
		
	
	
	
	public static function getPays()
	{
	/*
		if (!isset($_SESSION['pays']) || $_SESSION['pays']=="")
			$_SESSION['pays']="fr";
		$onchange="";	
		$attrid="id='remove13'";
		if ($_SERVER['REQUEST_URI'] == "/menu/connexion.php" || $_SERVER['QUERY_STRING'] == "sess=1")
		{
			$onchange = "onclick='changePays(0);' ";
			$attrid="id='imgpays'";
		}	
		echo "<div id='pays' style='position:absolute;top:2px;right:2px;'><img style='display:none;' src='../images/pays/".$_SESSION['pays'].".gif' ".$attrid." width='20' ".$onchange."/>\n";
			echo '<div id="selectpays" style="display:none;" >';
				echo '<select name="selpays" id="selpays" onchange=\'changePays(1);\'>';
					echo '<option value="'.$_SESSION['pays'].'">'.$_SESSION['pays'].'</option>';
					echo "<option value='fr'>fr</option>";
					echo "<option value='en'>en</option>";
				echo "</select></div>";
		echo "</div>";
		*/
		
	}
	public static function Entete_page($texte)
	{
		echo "<div class='entete_page'>".$texte."</div>";
	}
	public static function file_ariane()
	{
		if ($_SERVER['SCRIPT_NAME'] != "/index.php")
		{
			$oListeAriane = Insert::acces_arborescence($_SERVER['SCRIPT_NAME']);
			if (count($oListeAriane[0]) > 0)
			{
				echo '<ul class="breadcrumb" style="font-family: Roboto,sans-serif;">';
					foreach ($oListeAriane[0] as $Key => $Valeur)
					{
						//On lit chaque array de retour
						// 0=> libelle 1=>Lien
						echo '<li><a href="'.$oListeAriane[1][$Key].'" id="ariane'.$Key.'">'.$oListeAriane[0][$Key].'</a><span class="divider">/</span></li>';
					}
				echo "</ul>";
			}
		}
	}
	public static function Bouton_Ajout($location,$excel = false,$texte =  "",$script = "")
	{
		if ($texte == "") $texte=constant("site_ajouter");
		//class='bouton_ajout'
		if ($script =="")
			echo '<button class="btn btn-primary" onclick="document.location=\''.$location.'\'" style="margin:2px;" >'.$texte.'</button>';
		else
			echo '<button class="btn btn-primary" onclick="'.$script.'" style="margin:2px;">'.$texte.'</button>';
		if ($excel == true)
			echo '<button class="btn btn-primary"  style="position:absolute;top:150px;left:10px;"><a class="nav-link active" href="'.$location.'">Exporter en CSV</a></button>';
	}
	public static function Bouton_Export_CSV($location)
	{
	//class='bouton_ajout'
		echo "<div id='bouton_ajout' class='btn btn-success' style='position:absolute;top:100px;left:10px;' onclick=\"document.location='".$location."'\" ><img src='../../img/fichier_excel_white.png' /> Exporter en CSV</div>";
	}

	public static function getJavaScript($chemin)
	{
		$zone = $_SERVER["DOCUMENT_ROOT"]."/commun/js/";
		
		if(file_exists($zone.$chemin.".js"))
		{

			echo '<script type="text/javascript" src="'.$zone.$chemin.'.js?v='.filemtime($zone.$chemin.".js").'"></script>';
		}
		else 
			Tools::Warning("Impossible de charger le fichier ".$zone.$chemin.".js !!!");
	}
	public static function getCSS($chemin,$type)
	{
		$zone = $_SERVER["DOCUMENT_ROOT"]."/commun/";
		if ($type=='') $type='screen';
		if(file_exists($zone.$chemin.".css"))
		{
			echo '<link rel="stylesheet" type="text/css" media="'.$type.'" href="'.$zone.$chemin.'.css?v='.filemtime($zone.$chemin.".css").'" />';
		}
		else 
		{
			Tools::Warning("Impossible de charger le fichier ".$zone.$chemin.".css !!!");
		}	
	}
	/**
	 * Convertit une date en sqlDate pour insertion en base
	 * @param $date la date au format yyyymmdd
	 * @return la sql date (au format dd/mm/yyyy)
	 */
	public static function ConvertDateBlanche($date)
	{
		return substr($date,6,2)."/".substr($date,4,2)."/".substr($date,0,4);
	}
	/**
	 * Convertit une date en sqlDate pour insertion en base
	 * @param $date la date au format dd/mm/yyyy
	 * @return la sql date (au format yyyy-mm-dd)
	 */
	public static function ConvertToSQLDate($date)
	{
		if ($date!="")
		{
			$elt = explode("/",$date);
			if(count($elt) < 3)
			{
				//on test désormais avec une date en -
				$elt = explode("-",$date);
				if(count($elt) < 3)
				{
					return "";
				}	
				else
					return $elt[2]."-".$elt[1]."-".$elt[0];
			}	
			else
				return $elt[2]."-".$elt[1]."-".$elt[0];
		} else
			return "0000-00-00";
	}
    public static function joursferies($month,$day,$year)
    {
        if ($day>0)
        {
            if (strlen($day)==1) $day="0".$day;
            //echo "Date ferie=".$day."-".$month."-".$year."";
            $resultat=false;
            //Initialisation de variables
            $iCstJour = 3600*24;
            // Détermination des dates toujours fixes
            $tbJourFerie["Jour de l'an"] = $year . "-01-01";
            $tbJourFerie["Armistice 39-45"] = $year . "-05-08";
            $tbJourFerie["Toussaint"] = $year . "-11-01";
            $tbJourFerie["Armistice 14-18"]= $year . "-11-11";
            $tbJourFerie["Assomption"]  = $year . "-08-15";
            $tbJourFerie["Fête du travail"] = $year . "-05-01";
            $tbJourFerie["Fête nationale"] = $year . "-07-14";
            $tbJourFerie["Noël"] = $year . "-12-25";
			$tbJourFerie["Statut1"] = "2016-12-26";
			$tbJourFerie["Statut2"] = "2017-01-02";
            // Récupération des fêtes mobiles
            $tbJourFerie["Lundi de Pâques"] = $year .'-' .date( "m-d", easter_date(intval($year)) + 1*$iCstJour );
            $tbJourFerie["Jeudi de l'ascenscion"] = $year .'-' .date( "m-d", easter_date(intval($year)) + 39*$iCstJour );
            $tbJourFerie["Lundi de Pentecôte"] = $year .'-' .date( "m-d", easter_date(intval($year)) + 50*$iCstJour );
            foreach($tbJourFerie as $nb => $ValeurJF)
            {
                if ($tbJourFerie[$nb]==$year."-".$month."-".$day)
                {
                    $resultat=true;
                    break;
                }
            }
            $libellejour = date("w",mktime (0, 0, 0, intval($month), intval($day), intval($year)));
            if ($libellejour == 0 || $libellejour == 6 ) $resultat=true;
            return($resultat);
        }
    }
	
	//*-----------------------------------------------------------------*//
	/**
	 * Convertit une date en sqlDate pour extraction de la base
	 * @param $date la date au sql format yyyy-mm-dd
	 * @return la date (au format dd/mm/yyyy)
	 */
	public static function ConvertFromSQLDate($date)
	{
		$elt = explode("-",$date);
		if(count($elt) < 3)
			return "";
		else
		{
			if($date == "0000-00-00 00:00:00")
				return "";
			else if ($date == "0000-00-00")
				return "";
			else
				return substr($elt[2],0,2)."/".$elt[1]."/".$elt[0];
		}
	}

	//*-----------------------------------------------------------------*//
	
	/**
	 * Remplit un tableau associatif d'informations sur une date
	 * @param str la chaîne de date (compatible mysql)
	 * @return un tableau associatif d'informations (month, day, year)
	 */
	public static function DateParse($str)
	{
		if(strlen($str) < 10)
			return false;
			
		$infos = array();
		$infos['year'] = substr($str,0,4);
		$infos['month'] = substr($str,5,2);
		$infos['day'] = substr($str,8,2);
		
		return $infos;
	}
	//*-----------------------------------------------------------------*//
	/**
	 * Vide récursivement un répertoire
	 * @param path le chemin d'accès au répertoire
	 * @return true si le répertoire a été vidé, false sinon
	 */
	public static function EmptyDirectory($path)
	{
		$handle = opendir($path);
		while(false!==($item = readdir($handle)))
		{
			if($item != '.' && $item != '..')
			{
				if(is_dir($path.'/'.$item))
				{
					if(!Tools::RemoveDirectory($path.'/'.$item))
						return false;
				}
				else
				{
					if(!unlink($path.'/'.$item))
						return false;
				}
			}
		}
		closedir($handle);
		return true;
	}
	//*-----------------------------------------------------------------*//
	/**
	 * Liste les fichiers d'un répertoire (pas ., ni .., ni .Thumbs)
	 * @param $dir Le répertoire à scanner
	 * @return Les fichiers du répertoire dans un tableau.
	 */
	public static function ListFiles($dir)
	{
		$return = array();
		if(is_dir($dir))
  		{
    		if($handle = opendir($dir))
    		{
      			while(($file = readdir($handle)) !== false)
      			{
        			if($file != "." && $file != ".." && $file != "Thumbs.db"/*pesky windows, images..*/)
        			{
          				$return []= $file;
        			}
      			}
      		closedir($handle);
    		}
  		}//
  		return $return;
	}
	//*-----------------------------------------------------------------*//
	/**
	 * Supprime récursivement un répertoire
	 * @param path le chemin d'accès au répertoire
	 * @return true si le répertoire a été supprimé, false sinon
	 */
	public static function RemoveDirectory($path)
	{
		Tools::EmptyDirectory($path);
		return rmdir($path);
	}
	//*-----------------------------------------------------------------*//
	//*-----------------------------------------------------------------*//
	/**
	 * Affiche au format HTML une information
	 * @param $str L'info à afficher
	 * @return L'info au format HTML par echo
	 */
	public static function Info($str)
	{
				echo "
	  <div style='position: absolute; top: 0; right: 0;'>
		<div class='toast' role='alert' aria-live='assertive' aria-atomic='true' data-delay='10000'>
		  <div class='toast-header bg-success' style='color:#fff;'>
			<strong class='mr-auto'>Information</strong>
			".date("H:m:i")."
			<button type='button' class='ml-2 mb-1 close' data-dismiss='toast' aria-label='Close' style='color:#fff;'>
			  <span aria-hidden='true'>&times;</span>
			</button>
		  </div>
		  <div class='toast-body bg-success' style='color:#fff;'>
			".$str."
		  </div>
		</div>
	</div>
		";
		
	}
	//*-----------------------------------------------------------------*//
	//*-----------------------------------------------------------------*//
	/**
	 * Affiche au format HTML une notes d'information
	 * @param $str L'info à afficher
	 * @return L'info au format HTML par echo
	 */
	public static function Notes($str)
	{
	//onclick=\"affiche_note();\"
		echo "<a class='inline' href='#inline_content'><img title='".constant("site_information")."' style='cursor:pointer;width:40px;height:40px;position:absolute;top:100px;left:20px;' src=\"".$_SERVER["DOCUMENT_ROOT"]."/img/information.png\" id='image' name='image' onmouseover=\"image.src='".$_SERVER["DOCUMENT_ROOT"]."/img/information_survol.png'\" onmouseout=\"image.src='/img/information.png'\" /></a><div style='display:none;' ><div id='inline_content' style='padding:10px; background:#fff;'><img style='float:left;padding: 10px;' src=\"".$_SERVER["DOCUMENT_ROOT"]."".$_SERVER["DOCUMENT_ROOT"]."/img/information.png\" />".$str."</div></div>";
		
	}
	/**
	 * Affiche au format HTML un warning
	 * @param $str Le warning à afficher
	 * @return Le warning au format HTML par echo
	 */
	public static function Warning($str)
	{
		echo "
	  <div style='position: bottom; bottom: 0; right: 0;'>
		<div class='toast' role='alert' aria-live='assertive' aria-atomic='true' data-delay='10000' style='opacity:1;'>
		  <div class='toast-header bg-danger' style='color:#fff;'>
			<strong class='mr-auto' style='color:#fff;'>Alerte</strong>
			".date("H:m:i")."
			<button type='button' class='ml-2 mb-1 close' data-dismiss='toast' aria-label='Close' style='color:#fff;'>
			  <span aria-hidden='true'>&times;</span>
			</button>
		  </div>
		  <div class='toast-body bg-danger' style='color:#fff;'>
			".$str."
		  </div>
		</div>
	</div>
";
		
		
		//echo "<div class='alert-danger' style='position: relative;padding: 0.75rem 1.25rem;margin-bottom: 1rem;border: 1px solid transparent;border-radius: 0.25rem;'>".$str."</div>";
	}
	/**
	 * Affiche au format HTML d'une erreur SQL 
	 * @param $str Le warning à afficher
	 * $erreur = Erreur SQL libellé
	 * $No = No d'erreur SQL
	 * @return Le warning au format HTML par echo
	 */
	public static function WarningSQL($str,$erreur = "",$No="")
	{
		$oDB = new Mysql();
		if (isset($_SESSION['iduser']))
		{
			$oUser = Insert::membres_id($_SESSION['iduser']);
			if ($oUser[0]['Role'] == 1)
				echo "<div class='alert  alert-danger' role='alert'>Erreur SQL --><br /><hr />Requete envoyÃ©e--> &nbsp;&nbsp;&nbsp;&nbsp;".$str."<br />Erreur retournÃ©e--><br />".$erreur."</div>";
			else
				echo "<div class='alert  alert-danger' role='alert'>Une erreur est survenue.Veuillez vous rapprocher de l'Administrateur Web.<br><center>NumÃ©ro d'erreur : ".$No."</center></div>";

		}
		else
			echo "<div class='alert  alert-danger' role='alert'>Une erreur est survenue.Veuillez vous rapprocher de l'Administrateur Web.<br><center>NumÃ©ro d'erreur : ".$No."</center></div>";
	}
	/*
	Fonction evoi de mail
	*/
	public static function envoi_email($destinataire,$email_expediteur,$email_reply,$idactivation)
	{
		$oDB = new Mysql();
		$idactivation = str_replace('*','-',$idactivation);
		//Ajout du port dans l'url s'il y en a un
		$message_texte=constant('entete_email_membre'); 
		if ($_SERVER['SERVER_PORT'] != "") $adresse_serveur = $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']; else $adresse_serveur = $_SERVER['SERVER_NAME'];
		$message_html='<html> 
		 <head> 
		 <title>'.constant("entete_email_membre").'</title> 
		 </head> 
		 <body>'.constant("message_email_membre").' http://'.$adresse_serveur.'/modules/activate'.$idactivation.'</body> 
		 </html>'; 

		 //----------------------------------------------- 
		 //GENERE LA FRONTIERE DU MAIL ENTRE TEXTE ET HTML 
		 //----------------------------------------------- 

		 $frontiere = '-----=' . md5(uniqid(mt_rand())); 

		 //----------------------------------------------- 
		 //HEADERS DU MAIL 
		 //----------------------------------------------- 

		 $headers = 'From: <'.$email_expediteur.'>'."\n"; 
		 $headers .= 'Return-Path: <'.$email_reply.'>'."\n"; 
		 $headers .= 'MIME-Version: 1.0'."\n"; 
		 $headers .= 'Content-Type: multipart/alternative; boundary="'.$frontiere.'"'; 

		 //----------------------------------------------- 
		 //MESSAGE TEXTE 
		 //----------------------------------------------- 
		 $message = 'This is a multi-part message in MIME format.'."\n\n"; 

		 $message .= '--'.$frontiere."\n"; 
		 $message .= 'Content-Type: text/plain; charset="utf8"'."\n"; 
		 $message .= 'Content-Transfer-Encoding: 8bit'."\n\n"; 
		 $message .= $message_texte."\n\n"; 

		 //----------------------------------------------- 
		 //MESSAGE HTML 
		 //----------------------------------------------- 
		 $message .= '--'.$frontiere."\n";
		 $message .= 'Content-Type: text/html; charset="utf8"'."\n"; 
		 $message .= 'Content-Transfer-Encoding: 8bit'."\n\n"; 
		 $message .= $message_html."\n\n"; 

		 $message .= '--'.$frontiere."\n"; 

		 if(mail($destinataire,$message_texte,$message,$headers)) 
		 { 
			 echo self::Info(constant("confirmation_email_membre")); 
		 } 
		 else 
		 { 
			echo self::Warning(constant("confirmation_err_email_membre"));
		 } 	
	}
	public static function envoi_email_contact($destinataire,$email_expediteur,$message_html,$sujet)
	{
		$message_texte=constant("entete_title")." ---- ".$sujet; 
		$frontiere = '-----=' . md5(uniqid(mt_rand())); 
		$headers = 'From: <'.$email_expediteur.'>'."\n"; 
		$headers .= 'MIME-Version: 1.0'."\n"; 
		$headers .= 'Content-Type: multipart/alternative; boundary="'.$frontiere.'"'; 
		$message = 'This is a multi-part message in MIME format.'."\n\n"; 
		$message .= '--'.$frontiere."\n"; 
		$message .= 'Content-Type: text/plain; charset="utf8"'."\n"; 
		$message .= 'Content-Transfer-Encoding: 8bit'."\n\n"; 
		$message .= $message_texte."\n\n"; 
		$message .= '--'.$frontiere."\n";
		$message .= 'Content-Type: text/html; charset="utf8"'."\n"; 
		$message .= 'Content-Transfer-Encoding: 8bit'."\n\n"; 
		$message .= $message_html."\n\n"; 
		$message .= '--'.$frontiere."\n"; 
		if(mail($destinataire,$message_texte,$message,$headers))
		{}
		else
			echo self::Warning(constant("confirmation_err_email_membre"));
	}
	public static function class_statut_bdt($statut)
	{
		$class_statut='';
		if ($statut == "Initialisé") $class_statut = "-secondary";
		else if ($statut == "Préparé") $class_statut = "-primary";
		else if ($statut == "Signé") $class_statut = '-success';
		else if ($statut == "Reçu") $class_statut = '-warning';
		else if ($statut == "En cours") $class_statut = '-danger';
		else if ($statut == "Soldé") $class_statut = '-light';
		else if ($statut == "Clôturé") $class_statut = '-dark';
		else if ($statut == "Point d'arrêt") $class_statut = '-check';
		return $class_statut;
	}
	
	public static function workflow($prestation)
	{
		if ($prestation == "F960")
		{	
			$oListeWorkFlow = array(
				0 => "0 - Initialisé",1 => "1 - Création ING Pilot",2 => "2 - Date de préparation",	3 => "3 - Retour préparation",	4 => " 4 - Attente devis règlement",5 => "5 - RDV de pose",	6 => "6 - Attente avis fin travaux",7 => "7 - RDV de dépose",8 => "8 - Terminée",9 => "9 - Sans Suite"	
			);
/*			$oListeWorkFlow = array(
				0 => "0 - Initialisé",1 => "1 - Création ING Pilot",2 => "2 - Date de préparation",	3 => "3 - Retour préparation",	4 => "5 - RDV de pose",	5 => "6 - Attente avis fin travaux",6 => "7 - RDV de dépose",7 => "8 - Terminée",8 => "9 - Sans Suite",9 => " 4 - Attente devis règlement"	
			);
			*/
			
		}
		else if ($prestation == "F200A" || $prestation == "F140A" )
		{	
			$oListeWorkFlow = array(
				0 => "0 - Initialisé",1 => "1 - Date appel client",2 => "2 - Création BDT",3 => "3 - Envoi BDT vers CPA Prog.",	4=> "4 - Clôturé",	5 => "5 - Annulé suite règlement",	8 => "8 - Sans suite"		
			);
		}
		else if ($prestation == "IRPRO")
		{	
			$oListeWorkFlow = array(
				0 => "0 - RDV Non pris",1 => "1 - RDV Pris",2 => "2 - Devis Envoyé",3 => "3 - Attente travaux client",	4=> "4 - Programmé",	5 => "5 - Travaux fait"		
			);
		}
		return $oListeWorkFlow;
	}
	
	public static function decodeur_amiante($code_amiante)
	{
		$localisation='';
		$oCode_Array = array(
			2 => "Ardoise",
			4 => "Bardeaux bitumeux",
			9 => "Calorifigeage",
			11 => "Clapets",
			15 => "Coffrage perdu",
			18 => "Conduit",
			19 => "Dalle de sol",
			21 => "Enduit",
			22 => "Enduits projetés",
			23 => "Entourage de poteau",
			24 => "Enveloppe de calorifugeage",
			26 => "Faux plafond",
			27 => "Flocage",
			32 => "Joints de porte coupe-feu",
			40 => "Panneau",
			42 => "Peinture",
			44 => "Plaques",
			49 => "Rebouchage",
			50 => "Rebouchage volet",
			53 => "Revêtement dur",
			60 => "Volets",
			61 => "Accessoire de couverture",
			"XX" => "Pas d'amiante",
		);
		//on control tous les code amiantes
		$content = "";
		for ($i=21;$i<=31;$i+=2)
		{	
			$code_recherche = substr($code_amiante,intval($i),2);
			if ($code_recherche !="" && array_key_exists($code_recherche , $oCode_Array)) 
			{
				if ($code_recherche != "XX")
					$content .=  '<h3><span class="badge badge-danger" >'.$oCode_Array[$code_recherche].'</span></h3>';

				else if ($code_recherche == "XX")
					$content .= '<h3><span class="badge badge-success" >'.$oCode_Array[$code_recherche].'</span></h3>';
			}
			//else
				//$content .=  '<h3><span class="badge badge-warning" >Code présence amiante '.$code_amiante.' inconnu</span></h3>';
		}
		return $content;
	}
	public static function ListeDefaillance()
	{
		$oDefaillance = array(
		0=>"",
		1=>"Fournisseur : Autres",
		2=>"Fournisseur : Erreur de PDL/PRM",
		3=>"Fournisseur : Mauvaise qualification contractuelle",
		4=>"Fournisseur : Prise de RDV",
		5=>"Distributeur : Autres",
		6=>"Distributeur : CRINT - Autres",
		7=>"Distributeur : CRINT - L'agent n'a pas CRINTE",
		8=>"Distributeur : CRINT - Pb de posture",
		9=>"Distributeur : Erreur de PDL/PRM",
		10=>"Distributeur : LINKY Compteur non CRINTE",
		11=>"Distributeur : Manque d'information pour CRINT",
		12=>"Distributeur : Pb technique sur place",
		13=>"Distributeur : Prise de RDV"
		);
		return $oDefaillance;
	}
	public static function Desherence_type($organe,$interventionarealiser)
	{
		$typeretour="";
			if ($organe == "6")
			{
				$typeretour = "Collectif";
			}
			else if ($organe == "5" )
			{
				$typeretour = "ARE";
			}
			else if (($organe == "2"||$organe == "3"))
			{
				$typeretour = "BDT CHP";
			}
			else if ($organe == "4")
			{
				$typeretour = "BDT grille";
			}
			else if ($organe == "1")
			{
				$typeretour = "TIP";
			}
			else if ($organe == "" || $organe == "0")
			{
				$typeretour = "Collecte CHP T";
			}
			
		return $typeretour;
	}
	//Fonction retour de la matrice déshérence
	// Type==0 ==> Retourne si désherence ok ou non
	// Type==1 ==> Retourne les code T et P
	// Type 2 =+> retourne les intitulés
	public static function Matrice_Desherence($champT,$champP,$type)
	{
		//echo $champT."-".$champP."-".$type."<br />";
		$ListeChampT=array(0=>"Pas collecté",1=>"ACC.CCI",2=>"ACC.COUP HTE",3=>"ACC.CC FCPI",4=>"ACC.BRCHT SOUT",5=>"ACC.SUR TRVX",6=>"ACC.COLL.SPEC");
		$ListeChampP=array(0=>"Non coupé",1=>"AU BRCHT",2=>"AU CCI",3=>"APR.CPTR",4=>"HAUTE",5=>"CCI N.ACC");
		//matrice T,P
		$Matrice = array();
		$Matrice[0][0]=false;
		$Matrice[0][1]=false;
		$Matrice[0][2]=false;
		$Matrice[0][3]=false;
		$Matrice[0][4]=false;
		$Matrice[0][5]=false;

		$Matrice[0][6]=false;
		$Matrice[0][9]=false;
		
		$Matrice[1][0]=false;
		$Matrice[1][1]=true;
		$Matrice[1][2]=true;
		$Matrice[1][3]=false;
		$Matrice[1][4]=true;
		$Matrice[1][5]=false;
		
		$Matrice[1][6]=false;
		$Matrice[1][9]=false;
		
		$Matrice[2][0]=false;
		$Matrice[2][1]=true;
		$Matrice[2][2]=false;
		$Matrice[2][3]=false;
		$Matrice[2][4]=true;
		$Matrice[2][5]=false;
		
		$Matrice[2][6]=false;
		$Matrice[2][9]=false;

		$Matrice[3][0]=false;
		$Matrice[3][1]=true;
		$Matrice[3][2]=false;
		$Matrice[3][3]=false;
		$Matrice[3][4]=true;
		$Matrice[3][5]=false;
		
		$Matrice[3][6]=false;
		$Matrice[3][9]=false;

		$Matrice[4][0]=false;
		$Matrice[4][1]=true;
		$Matrice[4][2]=false;
		$Matrice[4][3]=false;
		$Matrice[4][4]=true;
		$Matrice[4][5]=false;
		
		$Matrice[4][6]=false;
		$Matrice[4][9]=false;

		$Matrice[5][0]=false;
		$Matrice[5][1]=false;
		$Matrice[5][2]=false;
		$Matrice[5][3]=false;
		$Matrice[5][4]=false;
		$Matrice[5][5]=false;
		
		$Matrice[5][6]=false;
		$Matrice[5][9]=false;

		$Matrice[6][0]=false;
		$Matrice[6][1]=true;
		$Matrice[6][2]=true;
		$Matrice[6][3]=false;
		$Matrice[6][4]=false;
		$Matrice[6][5]=false;
		
		$Matrice[6][6]=false;
		$Matrice[6][9]=false;
		
		$Matrice[9][0]=false;
		$Matrice[9][1]=false;
		$Matrice[9][2]=false;
		$Matrice[9][3]=false;
		$Matrice[9][4]=false;
		$Matrice[9][5]=false;
		$Matrice[9][6]=false;
		if ($type==0)
		{
			//On recherche les codes
			
			/*
			$CodeT = array_search($champT,$ListeChampT);
			$CodeP = array_search($champP,$ListeChampP);
			*/
			if ($champT == "") $champT=0;
			if ($champP == "") $champP=0;
			
			//echo "T=".$champT."P".$champP;
			//echo "Code T = ".$CodeT." Code P = ".$CodeP;
			//echo "<br />";
			return $Matrice[$champT][$champP];
		}
		else if ($type==1)
		{
			$CodeT = array_search($champT,$ListeChampT);
			$CodeP = array_search($champP,$ListeChampP);
			if ($CodeT == "") $CodeT=0;
			if ($CodeP == "") $CodeP=0;
			return $CodeT."_".$CodeP;
		}
		else if ($type==2)
		{
			if (isset($ListeChampT[$champT])) $LibChampT = $ListeChampT[$champT];
			else $LibChampT = "Inconnu";
			if (isset($ListeChampP[$champP]))
				$LibChampP = $ListeChampP[$champP];
			else
				$LibChampP = "Inconnu";
			return array($LibChampT,$LibChampP);
		}
		
	}
	public static function Controle_Import_Fichier($fichier,$typefile)
	{	
		//$TypeFichierCorrect 
		// Array = Type => Champ => N: Numérique, Longeur

		$TypeFichierCorrect = Array (
		"C" => array (
				"DD" => "",
				"DR" => "",
				"IDENTIFIANT_BDT"=> "",
				"CODE_GDO"=> "",
				"ENTITE_NOM"=> "",
				"CATEGORIE_BDT"=> "",
				"RUBRIQUE"=> "",
				"TYPE_BDT"=> "",
				"OBJET"=> "",
				"ETAT"=> "",
				"TAUX_REALISATION"=> "",
				"DATE_D_INITIALISATION"=> "",
				"CATEGORIE_RVA"=> "",
				"DATE_BUTOIR_PROGRAMMATION"=> "",
				"DATE_BUTOIR_REALISATION"=> "",
				"DUREE_TRAVAUX"=> "",
				"OI_EOTP_PREPARES"=> "",
				"NBRE_RESSOURCES_UO_PREPAREES"=> "",
				"NOM_CLIENT"=> "",
				"REFERENCE_CLIENT"=> "",
				"TELEPHONE_CLIENT"=> "",
				"ADRESSE"=> "",
				"COMMUNE_NOM"=> "",
				"DERNIER_VALIDATEUR_NOM_PRENOM"=> "",
				"DATE_DE_PREPARATION"=> "",
				"PREPARATEUR_NOM_PRENOM"=> "",
				"METHODE_TRAVAIL"=> "",
				"TYPE_RESEAU"=> "",
				"ATTESTATION_CONSIGNATION"=> "",
				"ITST"=> "",
				"AGENT_IDENTIFICATION_OUVRAGE"=> "",
				"DATE_IDENTIFICATION_OUVRAGE"=> "",
				"POSTE_SOURCE_BT"=> "",
				"DEPART_HTA"=> "",
				"POSTE_SOURCE_HTA"=> "",
				"DIPOLE"=> "",
				"BESOIN_AGENT_HABILITATIONS"=> "",
				"BESOIN_AGENT_NOMBRE"=> "",
				"BESOIN_VEHICULES"=> "",
				"ANALYSE_RISQUES"=> "",
				"FICHE_MANOEUVRE"=> "",
				"IPS"=> "",
				"NIP_IMPR_EXPLOITATION"=> "",
				"DESCRIPTION_TRAVAUX"=> "",
				"DATE_PROGRAMMEE"=> "",
			
				"DATE_REALISATION"=> "",
				"CHARGE_TRAVAUX_NOM_PRENOM"=> "",
				"CHARGE_EXPLOITATION_NNI"=> "",
				"DATE_VISA_CEX"=> "",
				"CHARGE_CONSIGNATION_NOM_PRENOM"=> "",
				"DESCRIPTION_COMPTE_RENDU"=> "",
				"BRIEFING_REALISE"=> "",
				"TAUX_DEBRIEFING"=> "",
				"DEBRIEFING_REALISE"=> "",
				"DATE_CLOTURE_AFFAIRE"=> "",
				"SOURCE_SOLDE"=> ""
			),
			"A" => array (
				'Année'=> "",
				'Semaine'=> "",
				'DD'=> "",
				'DR'=> "",
				'AGENCE'=> "",
				'BO'=> "",
				'SI'=> "",
				'Centre'=> "",
				'PDL'=> "N-L14-P",
				'Etat materiel CCI'=> "",
				'Type Compteur'=> "",
				'Matricule compteur'=> "",
				'Emplacement Compteur'=> "",
				'Emplacement CCPI'=> "",
				'Lieu Coupure'=> "",
				'Point de coupure le plus accessible'=> "",
				'Nom Prenom'=> "",
				'Numéro Voie'=> "",
				'Type de voie'=> "",
				'Voie'=> "",
				"Complément d'Adresse"=> "",
				'Code INSEE'=> "",
				'Code Postal'=> "",
				'Commune'=> "",
				'Lattitude'=> "",
				'Longitude'=> "",
				'Date Fin Contrat'=> "",
				'Etat'=> "",
				'Sous-Etat'=> "",
				'Date Etat Branchement'=> "",
				'Type Raccordement'=> "",
				'Type Branchement'=> "",
				'Dipole'=> "",
				'Deshérence Potentielle'=> "",
				'Non Coupé'=> "",
				'Non Sécurisé'=> "",
				'Ancienneté de la cessation'=> "",
				'Indicateur E-RES 298'=> "",
				"Cas complexe en cours d'instruction"=> "",
				'Deshérence Potentielle lib'=> "",
				'Non Coupé lib'=> "",
				'Non Sécurisé lib'=> "",
				'Indicateur E-RES 298 lib'=> "",
			),			
			
		"D" => array (
				"Numéro Bdt" => "N-L11",
				"Code Gdo" => "",
				"Entité Réalisatrice"=> "",
				"Catégorie Bdt"=> "",
				"Rubrique Bdt"=> "",
				"Type Bdt"=> "",
				"Objet Bdt"=> "",
				"Statut Bdt"=> "",
				"Responsable Ouverture Bdt"=> "",
				"Date Ouverture Bdt"=> "",
				"Catégorie Rva"=> "",
				"Date Butoir de Programmation"=> "",
				"Date Butoir de Réalisation"=> "",
				"Plage Prévue"=> "",
				"Durée Travaux"=> "",
				"OI/EOTP"=> "",
				"Nombre total d'agent nécessaire"=> "",
				"Nom Client"=> "",
				"Réference Client"=> "RP",
				"Tel Client"=> "",
				"Adresse"=> "",
				"Commune"=> "",
				"Nom Dernier Validateur"=> "",
				"Date Préparation"=> "",
				"Preparateur"=> "",
				"Entite Appartenance"=> "",
				"Méthode de travail"=> "",
				"Hors Exploit"=> "",
				"Réseau"=> "",
				"Voisinage"=> "",
				"Atst"=> "",
				"Attestation Consignation"=> "",
				"Autorisation Travail"=> "",
				"Autre"=> "",
				"Interlocuteur"=> "",
				"Tel Fixe"=> "",
				"Tel Portable"=> "",
				"Identificateur Ouvrage"=> "",
				"Date Identification"=> "",
				"Ps 1"=> "",
				"Depart 1"=> "",
				"Poste Hta Bt 1"=> "",
				"Dipole1"=> "",
				"Besoin Agents"=> "",
				"Besoin Vehicules"=> "",
				"Analyse Risques"=> "",
				"Fiche Manoeuvre"=> "",
				"Ips"=> "",
				"Consuel"=> "",
				"Bon Intervention"=> "",
				"Plan"=> "",
				"Bon Magasin"=> "",
				"Autres"=> "",
				"Nip Imprime Exploitation"=> "",
				"Devis"=> "",
				"Date Devis"=> "",
				"Description Travaux"=> "",
				"Responsable Travaux"=> "",
				"Date Validation"=> "",
				"Date Programme"=> "",
				"Date Realisation"=> "",
				"Charge Travaux"=> "",
				"Charge Exploitation"=> "",
				"Date Validation Cex"=> "",
				"Charge Consignation"=> "",
				"Identification Ouvrage"=> "",
				"Delivrer Separation"=> "",
				"Remettre Delivrer Atst"=> "",
				"Delivrer Ate"=> "",
				"Description"=> "",
				"Briefing"=> "",
				"% de réalisation"=> "",
				"Debriefing"=> "",
				"Date Cloture"=> "",
				"ASL"=> "",
				"Origine du solde"=> "",
				"Source du bon"=> ""
			),
		"J" => array( 
			0 => array (
				"SEC" => "N-L4","TOU" => "N","FOL" => "N","CLE" => "N","PROPART" => "","NOM" => "","RUE" => "","LIBCOM" => "","CBP" => "","TELEFON" => "","IDLOCAL" => "N","IDCOM" =>"",
				"ACINT001" => "","ACINTA001" => "","ACOBJ1001" => "","ACLIBOB101" => "","ACNATOP101" => "","ACLIBOP101" => "","ACENERG101" => "","ACOBJ2001" => "",
				"RVZONE001" => "", "ACOBS001" => "", "ACCMJJ001" => "", "ACCMMM001" => "", "ACCMAA001" => "", "ACNATOP201" => "", "ACENERG201" => "", "RVDEST001" => "", "RVNOPN001" => "", "RVTR001" => "", "RVJJ001" => "", "RVMM001" => "", "RVAA001" => "", "RVPREC001" => "", "ACFO1001" => "", "ACMFO1001" => "", "ACFO2001" => "", "ACMFO2001" => "", "ACNATOP301" => "", "ACINT002" => "", "ACINTA002" => "", "ACOBJ1002" => "", "ACLIBOB102" => "", "ACNATOP102" => "", "ACLIBOP102" => "", "ACENERG102" => "", "ACOBJ2002" => "", "RVZONE002" => "", "ACOBS002" => "", "ACCMJJ002" => "", "ACCMMM002" => "", "ACCMAA002" => "", "ACNATOP202" => "", "ACENERG202" => "", "RVDEST002" => "", "RVNOPN002" => "", "RVTR002" => "", "RVJJ002" => "", "RVMM002" => "", "RVAA002" => "", "RVPREC002" => "","ACFO1002" => "", "ACMFO1002" => "", "ACFO2002" => "", "ACMFO2002" => "", "ACNATOP302" => ""
				, "ACINT003" => "", "ACINTA003" => "", "ACOBJ1003" => "", "ACLIBOB103" => "", "ACNATOP103" => "", "ACLIBOP103" => "", "ACENERG103" => "", "ACOBJ2003" => "", "RVZONE003" => "", "ACOBS003" => "", "ACCMJJ003" => "", "ACCMMM003" => "", "ACCMAA003" => "", "ACNATOP203" => "", "ACENERG203" => "", "RVDEST003" => "", "RVNOPN003" => "", "RVTR003" => "", "RVJJ003" => "","RVMM003" => "", "RVAA003" => "", "RVPREC003" => "", "ACFO1003" => "", "ACMFO1003" => "", "ACFO2003" => "","ACMFO2003" => "", "ACNATOP303" => ""
			),
			1 => array (
				"SEC" => "N-L4","TOU" => "N","FOL" => "N","CLE" => "N","PROPART" => "","NOM" => "","RUE" => "","LIBCOM" => "","CBP" => "","TELEFON" => "","IDLOCAL" => "N","IDCOM" =>"",
				"ACINT001" => "","ACINTA001" => "","ACOBJ1001" => "","ACLIBOB101" => "","ACNATOP101" => "","ACLIBOP101" => "","ACENERG101" => "","ACOBJ2001" => "",
				"RVZONE001" => "", "ACOBS001" => "", "ACCMJJ001" => "", "ACCMMM001" => "", "ACCMAA001" => "", "ACNATOP201" => "", "ACENERG201" => "", "RVDEST001" => "", "RVNOPN001" => "", "RVTR001" => "", "RVJJ001" => "", "RVMM001" => "", "RVAA001" => "", "RVPREC001" => "", "ACFO1001" => "", "ACMFO1001" => "", "ACFO2001" => "", "ACMFO2001" => "", "ACNATOP301" => "", "ACINT002" => "", "ACINTA002" => "", "ACOBJ1002" => "", "ACLIBOB102" => "", "ACNATOP102" => "", "ACLIBOP102" => "", "ACENERG102" => "", "ACOBJ2002" => "", "RVZONE002" => "", "ACOBS002" => "", "ACCMJJ002" => "", "ACCMMM002" => "", "ACCMAA002" => "", "ACNATOP202" => "", "ACENERG202" => "", "RVDEST002" => "", "RVNOPN002" => "", "RVTR002" => "", "RVJJ002" => "", "RVMM002" => "", "RVAA002" => "", "RVPREC002" => "","ACFO1002" => "", "ACMFO1002" => "", "ACFO2002" => "", "ACMFO2002" => "", "ACNATOP302" => ""
			)),			
		"E" => array (
			"IDPDLL" => "N-L14-P",
			"IDCOM" => "",
			"NOM" => "",
			"ADRESSE" => "",
			"COMMUNE" => "",
			"TELEFON" => "",
			"CBP" => "N",
			"OBJET001" => "N-L1",
			"NATOP001" => "L2",
			"ENERGIE001" => "L1",
			"DESTINAT01" => "",
			"REFERENCE1" => "N-L12",
			"RDV001" => "",
			"CREATION01" => "",
			"ACOBS001" => "",
			"NATOP2001" => "",
			"NATOP3001" => "",
			"ACNOAC001" => "",
			"RVTR001" => ""
			),
		"R" => array (
			"CCD" => "N",
			"REF" => "N-L12-R",
			"SEC" => "N",
			"IDLOCAL" => "",
			"IDPDLL" => "",
			"IDCOM" => "",
			"PROPART" => "",
			"NOM" => "",
			"COMPAD" => "",
			"RUE" => "",
			"LIBCOM" => "",
			"CBP" => "N",
			"TELEFON" => "",
			"ACINT" => "",
			"ACINTA" => "",
			"NDOSSIER" => "",
			"ACENERG1" => "",
			"ACOBJ1" => "",
			"ACLIBOB1" => "",
			"ACNATOP1" => "",
			"ACLIBOP1" => "",
			"ACOBJ2" => "",
			"ACOBJ3" => "",
			"ACNATOP2" => "",
			"ACNATOP3" => "",
			"ACOBS" => "",
			"CREATION" => "",
			"ACFO1" => "",
			"ACMFO1" => "",
			"ACFO2" => "",
			"ACMFO2" => "",
			"RVDEST" => ""
			),
		"S" => array (
			"Affaire" => "",
			"Date Demande" => "",
			"Heure Demande" => "",
			"PRM" => "N-L14-P",
			"Territoire" => "",
			"Code Postal" => "",
			"Commune" => "",
			"Domaine de tension" => "",
			"Segment" => "",
			"Code de la prestation" => "",
			"Prestation" => "",
			"Code de l'option de prestation" => "",
			"Option de prestation" => "",
			"Client" => "",
			"Catégorie" => "",
			"Standard demandé" => "",
			"Modalité RDV" => "",
			"Date d'effet" => "",
			"Type Engagement" => "",
			"Date butoir" => "",
			"Engagement respecté" => "",
			"Famille d'étape" => "",
			"Etape" => "",
			"Date tâche" => "",
			"Age tâche" => "",
			"Utilisateur tâche" => ""
			),
			/*
		"I" => array (
			"DR (Libellé)" =>"",
			"Date (Affichage)" =>"",
			"Identifiant du point" =>"N-L14-P",
			"Civilité" =>"",
			"Nom" =>"",
			"Prénom" =>"",
			"Numéro et nom de voie" =>"",
			"Escalier, Etage, Appartement" =>"",
			"Batiment" =>"",
			"Code postal" =>"",
			"Commune" =>"",
			"Catégorie de client (Libellé)" =>"",
			"Identifiant d'affaire" =>"",
			"Fournisseur" =>"",
			"Option de prestation (Code)" =>"",
			"Option de prestation (Libellé)" =>"",
			"Mode de réalisation (Libellé)" =>"",
			"Créance à recouvrir ?" =>"",
			"Montant de la créance" =>"",
			"Date (Affichage)" =>"",
			"Statut de l'affaire (Libellé)" =>"",
			"Etat Interne (Libellé)" =>"",
			"SI contractuel" =>"",
			"Mail" =>"",
			"Téléphone" =>"",
			"Téléphone portable" =>"",
			"Téléphone" =>"",
			"Téléphone portable" =>"",
			"Téléphone portable" =>"",
			"Téléphone" =>"",
			"Sélection du contexte AFFAIRE" =>"",
			"NATOPE1" =>"",
			"NATOPE2" =>"",
			"NATOPE3" =>"",
			"Type d'intervention" =>"",
			"Sous-type d'intervention" =>"",
			"Type d'intervention" =>"",
			"Sous-type d'intervention" =>"",
			"Type d'intervention" =>"",
			"Sous-type d'intervention" =>"",
			"Date d'intervention prévue" =>"",
			"Date d'intervention réelle" =>""),
			
			
		"I" => array (
			"Prestation" => "",
			"Segment" => "",
			"N° affaire" => "",
			"Initiateur" => "",
			"Date demande" => "",
			"Date butoir" => "",
			"Client" => "",
			"Etat" => "",
			"Point de mesure" => "",
			"Code postal" => "",
			"Commune" => ""
			),
			*/
		"H" => array (
			"DIR" => "",
			"DR" => "",
			"AGENCE_INTERVENTION" => "",
			"BO" => "",
			"PDL" => "N-L14-P",
			"PRO_PART" => "",
			"CCD" => "",
			"REFQE" => "N-L12-R",
			"NOM_CLIENT" => "",
			"ADRESSE" => "",
			"COMP_ADRESSE" => "",
			"CODE_POSTAL" => "",
			"COMMUNE" => "",
			"CODE_INSEE" => "",
			"CODE_BRANCHT_ELEC" => "",
			"SITUATION_CPT" => "",
			"ACCES_RELEVE" => "",
			"ORGANE_COUPURE" => "",
			"MAT_CPT" => "",
			"BTR" => "",
			"DATE_RESIL" => "",
			"NB_SEMAINES" => "",
			"CATEGORIE" => ""
			),
		"F" => array (
			"linky" => "",
			"affaire" => "",
			"SI" => "",
			"date_migration" => "",
			"pdl" => "N-L14-P",
			"date_valid_linkypilot" => "",
			"marche" => "",
			"secteur" => "N-L4",
			"commune" => "",
			"adresse_pdl" => "",
			"hors_concession" => "",
			"nature" => "",
			"priorite" => "",
			"statut" => "",
			"etat" => "",
			"orientation" => "",
			"date_update" => "",
			"nni_update" => "",
			"prenom_update" => "",
			"nom_update" => "",
			"date_creation" => "",
			"nni_creation" => "",
			"prenom_creation" => "",
			"nom_creation" => "",
			"DR" => "",
			"date_RDV_programme" => "",
			"poste_HTA_BT" => "",
			"fils_u" => "",
			"situ_cptr" => "",
			"techno_cptr" => "",
			"info_presence_client" => "",
			"info_nb_agents" => "",
			"meter_type"=> "",
			"entite_avant_cloture" => "",
			"commentaire_GRIP" => "",
			"commentaire_etape" => ""
			),
		"L" => array (
			"N° d'affaire" => "","Maille 8 du Distributeur (région)" => "","Maille 100 du Distributeur (centre)" => "","Maille P2 du Distributeur" => ""
			,"Maille P3 du Distributeur" => "","Code postal" => "","Commune (libellé)" => "","Code de la prestation" => "","Libellé de la prestation" => ""
			,"Segment" => "","Login du demandeur" => "","Libellé de l'entité du demandeur" => "","Statut de l'affaire" => "","Etat interne (libellé)" => "","Etape de la tâche en cours" => ""
			,"Date de début de la tâche en cours" => "","Login de l'utilisateur de la tâche en cours" => "","Entité de l'utilisateur de la tâche en cours" => ""
			,"Date de retard pour la tâche en cours" => "","Affaire SGE de référence" => "","Référence fournisseur" => "","Référence de regroupement" => ""
			,"Identifiant du PDM de l'affaire" => "","Voie (de l'adresse du point de livraison)" => "","Type d'offre" => "","Catégorie du client" => ""
			,"Client" => "","SIRET" => "","Date de validation de la demande" => "","Mode de Gestion" => "","Type de réclamation" => "","Sous type de demande" => "","Motif de demande" => ""
			,"Demande d'indemnisation" => "","Type de réclamant" => "","Emetteur" => "","Recours CRE" => "","Pose en masse" => "","Emetteur de la demande" => ""
			,"Détail de la demande" => "","Complément d'information demandé" => "","Date souhaitée pour le complément d'information" => ""
			,"Complément d'information apporté" => "","Renouvellement" => "","Motif défaut de compréhension" => "","Motif action programmée non réalisée" => ""
			,"Motif défaut sur la forme de la réponse apportée" => "","Commentaire de renouvellement" => "","Qualité demande - Claire" => "","Qualité demande - Compréhensible" => ""
			,"Motif d'utilisation inadaptée du canal réclamation" => "","Précision apportée" => "","Affaire sensible" => "","Date butoir" => "","Date de réception de la demande" => ""
			,"Processus concerné" => "","Portage de la réponse au client par le Distributeur" => "","Type de demande requalifiée" => "","Sous type de demande requalifiée" => ""
			,"Motif de demande requalifiée" => "","Niveau de recours de la réclamation" => "","Non-respect du code de bonne conduite" => "","Motif fondé pour le distributeur" => ""
			,"Précision demandée" => "","E-mail pour complément d'information" => "","Motif du renvoi hors mission distributeur" => "","Responsabilité partagée fournisseur/distributeur" => ""
			,"Login du chargé d'affaire" => "","Entité du chargé d'affaire" => "","Envoi d'une lettre d'attente" => "","Date d'envoi de la réponse d'attente" => ""
			,"Réponse du Distributeur" => "","Variation lente de tension avérée" => "","Intervention nécessaire ?" => "","Identifiant de l'affaire de suivi" => ""
			,"Indemnité versée" => "","Motif annulation" => "","Date de clôture" => "","Responsabilité avérée de l'entité de pose ?" =>""
		),
		//Capella	
		"K" => array (
			"N° de dossier" => "","Date de création"=> "","Date de modification"=> "","Canal du premier échange"=> "","Sens du canal"=> "","NNI interlocuteur Enedis"=> "","Nom interlocuteur Enedis"=> "","DR de l'interlocuteur Enedis"=> "","Marché"=> "","Niveau de création du dossier"=> "","Identifiant (PRM/SIRET/SIREN/GRP)"=> "","Segment technique"=> "","Dénomination Client"=> "","Code postal"=> "","Code INSEE"=> "","Commune"=> "","DR Client"=> "","Processus"=> "","Type"=> "","Sous-type"=> "","Description du dossier"=> "","Bureau de suivi"=> "","Bureau de traitement"=> "","Statut du dossier"=> "","Délai en jours"=> "","Date de clôture"=> "","Nb d'échanges"=> "","Nb d'actions à traiter"=> "","Nb d'actions totales"=> "","Entité de suivi"=> "","Entité de traitement"=> ""
		),
		//TBSA	
		"P" => array (
			"Date" => "","Debut"=> "","Fin"=> "","Durée en minutes"=> "","Perimetre 1"=> "","Perimetre 2"=> "","Perimetre 3"=> "","Perimetre 4"=> "","Groupe de travail"=> "","NNI Agent"=> "","Nom Agent"=> "","Prénom Agent"=> "","Nature d'activité"=> "","Activité"=> ""
		)
		/*
		,
		//BIICAM	
		"O" => array (
			"Acteur clôture tâche" => "","Nombre total de tâches"=> "","Type Affaire"=> ""
		)
		*/
		//Utilisateur CePiA
		,
		"U" => array (
		
		"Nom"=> "","Prenom"=> "","Email"=> "","NNI"=> "","Role"=> "","Telephone"=> "","DR"=> "","Service"=> "","Cedex"=> "","Adresse"=> "","Commune"=> "","Code Postal"=> "","Departement"=> "","Pays"=> "","Téléphone Fixe"=> "","LOG Bureau"=> "","LOG TAD"=> "","Nom du poste informartique"=> "","Téléphone personnel"=> "")
		,
		"V" => array (
		"id" => "","dd" => "","dr" => "","drCode" => "","agenceIntervention" => "","bo" => "","codeObjet" => "","natop" => "","libelleNatop" => "","numAc" => "","codeTarif" => "","destinataire" => "","dateModifAc" => "","dateRdv" => ""
		,"numSge" => "","etatInterneSge"=>""
		,"nbFrais" => "","observation" => "","anciennete" => "","banette" => "","destInitial" => "","infoMatrice" => "","centre" => "","pdl" => "N-L14-P",
		"DATE RELEVE" => "",
		"refQe" => "N-L12-R", 
		"proPart" => "","nomClient" => "","noRue" => "","adresse" => "","compAdresse" => "","emplacement" => "","codePostal" => "","commune" => "","codeInsee" => "","idcom" => "","dateCreation" => "","dateResil" => "","codeBranchtElec" => "","typeBranchement" => "","puissanceRacco" => "","presenceCpt" => "","codeCompteur" => "","situationCpt" => "","accesReleve" => "","organeCoupure" => "","pointDeCoupure" => "","dateCoupure" => "","limiteurPuissance" => "","dateLimitPuiss" => "","matCpt" => "","cbe" => "","btr" => "","idLinky" => "","ecgi" => "","eclnk" => "","destInterv" => "","dateLimitePromis" => "","dateLimiteLinky" => "","dateTraitement" => "","destinataireReleve" => "","gmc" => "","gr" => "","dateLastAction" => "","dateReport" => "","status" => "","promisAnomalieGinko" => "")
		/*,
		"T" => array (
		"client_id" => "",
		"client_lastname" => "",
		"client_name" => "",
		"client_email" => "",
		"client_tel" => "",
		"experience_id" => "",
		"experience_date_analyse" => "",
		"feedback_date" => "",
		"Satisfaction globale" => "",
		"Linky - Image Enedis" => "",
		"XPC - Indicateur d'effort" => "",
		"Branchements provisoires - Accompagnement Enedis" => "",
		"Branchements provisoires - Délai de réalisation" => "",
		"Post pdr raccordement - Clarté de la proposition" => "",
		"Post pdr raccordement - Délai de réception de la proposition" => "",
		"Post pdr raccordement - Echanges avec le conseiller" => "",
		"Post travaux raccordement - Échanges avec technicien" => "",
		"Post travaux raccordement - Remise en état du chantier" => "",
		"1ere mise en service - Accompagnement Enedis" => "",
		"1ere mise en service - Aide interlocuteur raccordement" => "",
		"1ere mise en service - Délai de réalisation raccordement" => "",
		"1ere mise en service - Information horaire RDV P1-P3" => "",
		"1ere mise en service - Respect du RDV P1-P3" => "",
		"1ere mise en service - Service professionnalisme technicien P1-P3" => "",
		"Mise en service sur existant - Accompagnement Enedis" => "",
		"Mise en service sur existant - Appel technicien" => "",
		"Mise en service sur existant - Communication mise en service à distance" => "",
		"Mise en service sur existant - Délai de mise en service" => "",
		"Mise en service sur existant - Délai de mise en service à distance" => "",
		"Mise en service sur existant - Écoute et prise en compte des besoins" => "",
		"Mise en service sur existant - Factures Enedis" => "",
		"Mise en service sur existant - Plage horaire du rdv" => "",
		"Mise en service sur existant - Prévenance rdv" => "",
		"Interventions techniques - Accompagnement Enedis" => "",
		"Interventions techniques - Accompagnement Enedis téléopération" => "",
		"Interventions techniques - Appel confirmation rdv" => "",
		"Interventions techniques - Délai de réalisation" => "",
		"Interventions techniques - Délai intervention à distance" => "",
		"Interventions techniques - Ecoute et prise en compte des besoins" => "",
		"Interventions techniques - Facture Enedis" => "",
		"Interventions techniques - Plage horaire du rdv" => "",
		"Interventions techniques - Prévenance rdv" => "",
		"Linky - Documentation déposée" => "",
		"Linky - Info. fournie" => "",
		"Linky - Mise en main du compteur" => "",
		"Linky - Plage horaire du rdv" => "",
		"Relevé de compteurs - Amabilité et professionnalisme" => "",
		"Relevé de compteurs - Date et plage horaire de passage" => "",
		"Coupure - Actions mises en oeuvre pour limiter impacts" => "",
		"Coupure - Information sur choix de la période" => "",
		"Coupure - Informations sur motifs de réalisation" => "",
		"Accueil dépannage - Informations délivrées par le conseiller" => "",
		"Accueil dépannage - Intervention dépannage" => "",
		"Accueil dépannage - Qualité de l'échange tél" => "",
		"Intervention dépannage - Intervention technicien" => "",
		"Intervention dépannage - Qualité de l'échange avec technicien" => "",
		"Réclamations - Clarté de la réponse" => "",
		"Réclamations - Délai de réponse" => "",
		"Réclamations - Échange téléphonique avec conseiller" => "",
		"Modifications branchement - Accompagnement Enedis" => "",
		"Modifications branchement - Aide de l'interlocuteur" => "",
		"Modifications branchement - Délai de réalisation" => "",
		"Déplacement d'ouvrage - Accompagnement Enedis" => "",
		"Déplacement d'ouvrage - Aide apporté par l'interlocuteur" => "",
		"Déplacement d'ouvrage - Délai de réalisation des travaux" => "",
		"Commentaire - Commentaires" => "",
		"Commentaire - Commentaires Linky" => "",
		"Commentaire - Mise en service sur existant - téléopérations" => "",
		"Commentaire - Communication - téléopérations" => "",
		"Commentaire - AIT - téléopérations" => "",
		"Commentaire - Commentaires Coupure P1-P3" => "",
		"Commentaire - qt1_q52_posttravauxracc_insat_remise_etat_chantier" => "",
		"Meta client - accessibilite_prm" => "",
		"Meta client - alp_p_racc_soutirage" => "",
		"Meta client - canal" => "",
		"Meta client - civilite_nom_prenom" => "",
		"Meta client - code_ape" => "",
		"Meta client - code_insee" => "",
		"Meta client - code_segment_final" => "",
		"Meta client - code_segment_routage" => "",
		"Meta client - collectivite_locale" => "",
		"Meta client - commune" => "",
		"Meta client - cp" => "",
		"Meta client - cp_commune" => "",
		"Meta client - date_enquete" => "",
		"Meta client - email" => "",
		"Meta client - famille_compteur_depose" => "",
		"Meta client - grand_compte_national" => "",
		"Meta client - grand_compte_regional" => "",
		"Meta client - id_point" => "N-L14-P",
		"Meta client - mhrv" => "",
		"Meta client - numero_rue" => "",
		"Meta client - raison_sociale" => "",
		"Meta client - segment_client" => "",
		"Meta client - siret" => "",
		"Meta client - tel_fixe" => "",
		"Meta client - tel_mobile" => "",
		"Meta client - type_client" => "",
		"Meta client - type_deploiement" => "",
		"Meta client - version_cpl_c" => "",
		"Meta site - code_agence" => "",
		"Meta site - dir" => "",
		"Meta site - dr_comparables" => "",
		"Meta site - entite_suivi" => "",
		"Meta site - entite_traitement" => "",
		"Meta site - epnr" => "",
		"Meta site - lib_agence" => "",
		"Meta site - lib_dr" => "",
		"Meta site - maille_dr" => "",
		"Meta site - maille_territoire" => "",
		"Meta site - nom_maille_de_pose" => "",
		"Meta site - tarif_materiel_depose" => "",
		"Meta site - type_agence" => "",
		"Meta parcours - 1ere_mes_accomp_interloc_racco" => "",
		"Meta parcours - accueil_depannage_interv_techn" => "",
		"Meta parcours - canal_avis" => "",
		"Meta parcours - canal_echange_ad" => "",
		"Meta parcours - code_option" => "",
		"Meta parcours - code_prestation" => "",
		"Meta parcours - compteur_communicant" => "",
		"Meta parcours - date_avis" => "",
		"Meta parcours - date_cloture" => "",
		"Meta parcours - date_coupure" => "",
		"Meta parcours - date_cr_intervention" => "",
		"Meta parcours - date_effet" => "",
		"Meta parcours - date_envoi" => "",
		"Meta parcours - date_envoi_45j" => "",
		"Meta parcours - date_envoi_courrier" => "",
		"Meta parcours - date_intervention" => "",
		"Meta parcours - deplacement_ouvrage_accompagt_interloc" => "",
		"Meta parcours - etat_affaire" => "",
		"Meta parcours - id_affaire_fonctionnel" => "",
		"Meta parcours - int_teleoperation" => "",
		"Meta parcours - interv_depannage_contact_tech" => "",
		"Meta parcours - interv_techn_prevenance" => "",
		"Meta parcours - jour_contact" => "",
		"Meta parcours - libelle_prestation" => "",
		"Meta parcours - libelle_processus" => "",
		"Meta parcours - marche_pose" => "",
		"Meta parcours - mes_existant_prevenance_mes" => "",
		"Meta parcours - mode_realisation" => "",
		"Meta parcours - modif_brancht_accompagt_interloc_racco" => "",
		"Meta parcours - motif_contact" => "",
		"Meta parcours - motif_coupure" => "",
		"Meta parcours - nature_de_la_demande" => "",
		"Meta parcours - nom_edp" => "",
		"Meta parcours - post_tvx_racco_respect_heure_rdv" => "",
		"Meta parcours - processus" => "",
		"Meta parcours - processus_ad" => "",
		"Meta parcours - questionnaire" => "",
		"Meta parcours - respect_horaires_rdv" => "",
		"Meta parcours - source_enrichissement" => "",
		"Meta parcours - sous_type_de_dossier_ad" => "",
		"Meta parcours - technique_de_branchement" => "",
		"Meta parcours - type_de_dossier" => "",
		"Meta parcours - type_de_travaux" => "",
		"Meta parcours - type_enquete" => "",
		"alerte_statut" => "",
		"alerte_type" => "",
		"alerte_traitement" =>""
		)*/
		, //Diffus renforcé 4 structure de fichier différentes
		"M" => array(
			//Suite résiliation sans déplacement pour coupure a programmer
			// Suite résliation sans déplacement pour pose à programmer
			0 => array (
				"DR" => "",
				"Agence" => "",
				"Base Opérationnelle" => "",
				"Référence PDS" => "",
				"Référence der. contrat" => "",
				"Référence affaire cess." => "",
				"Statut contrat" => "",
				"Date de résiliation" => "",
				"Ancienneté résiliation sem." => "",
				"Cat. ancienneté résiliation" => "",
				"reférence aff. D001 non programmée"=> "",
				"Accès compteur" =>"", 
				"PDS coupé"=> "",
				"Emplacement compteur"=> "",
				"Position organe coupure"=> "",
				"Secteur"=> "",
				"Numéro voie edl"=> "",
				"Complément de localisation EDL"=> "",
				"Type voie EDL"=> "",
				"Voie EDL"=> "",
				"Commune EDL"=> "",
				"Code postal EDL"=> "",
				"Code INSEE"=> "",
				"Eligibilité au déploiement" => "",
				"MES dans le futur"=>"","Date de programmation"=>""
			),
			//MES sans déplacement pour contact client
			1 => array (
				"DR" => "","Agence" => "","Base Opérationnelle" => "","Référence PDS" => "","Référence affaire MES" => "","Référence SGE " => "","Date de MES" => "","Ancienneté MES sem." => "","Cat. ancienneté MES" => "",
				"Accès compteur" => "","PDS coupé" => "","Emplacement compteur" => "","Secteur" => "","Numéro voie edl" => "","Complément de localisation EDL" => "","Type voie EDL" => "","Voie EDL" => "","Commune EDL" => "",
				"Code postal EDL" => "","Code INSEE" => "","Nom et Prénom du titulaire du contrat" => "","Tel titulaire contrat " => "","Email titulaire contrat" => "","Nom et Prénom de l'interlocuteur du contrat" => "",
				"Téléphone interlocuteur" => "","Email interlocuteur" => ""
			)
		),
		"W" => array (
			"Appels ACD servis (total)" => "C7",
			"Appels ACD non servis (total)" => "D7",
			"Appels non-ACD reçus (total)" => "E7",
			"Temps affecté et pas en retrait" => "F7",
			"Temps retrait" => "G7",
			"Temps wrap-up manuel" => "H7",
			"Temps indisponible" => "I7",
			"Temps total de travail non-ACD" => "J7",
			"Temps total de travail ACD" => "L7",
			"Temps total de conversation ACD arrivée" => "N7",
			"Temps total de conversation ACD départ" => "P7"
			),			
	);
		//var_dump($TypeFichierCorrect['T']);
		$messErreur = "";
		IF (isset($TypeFichierCorrect[$typefile]) && ($typefile!="W"))
		{
			$inputFileName  = $_SERVER["DOCUMENT_ROOT"].'/upload_fichier/'.$fichier;
			$fic = fopen($inputFileName, "a+");
			$ligne=0;
			$testfichier=true;
			if ($typefile == "O")
				$separateur = ',';
			else
				$separateur = ';';
			
			while($tab=fgetcsv($fic,8096,$separateur))
			{
				
				$champs = count($tab);
				//Deux structure différentes en fonction du type de fichier cas DJNDA
				if ($typefile == "J" && $ligne ==0 )
				{
					if ($champs == 93)
						$TypeFichierCorrect[$typefile] = $TypeFichierCorrect[$typefile][0];
					else 
						$TypeFichierCorrect[$typefile] = $TypeFichierCorrect[$typefile][1];
				}
				
				//Deux structure différentes en fonction du type de fichier cas Diffus renforcé
				else if ($typefile == "M" && $ligne ==0 )
				{
					if ($champs == 26 && $tab[22] == "Code INSEE")
						$TypeFichierCorrect[$typefile] = $TypeFichierCorrect[$typefile][0];
					else 
						$TypeFichierCorrect[$typefile] = $TypeFichierCorrect[$typefile][1];
				}
				//oN TEST LES ENTËTES DE COLONNES ET LE Nombre
				if ($ligne == 0)
				{
					
					if (count($TypeFichierCorrect[$typefile]) != $champs)
					{
						$testfichier=false;
						$messErreur .= "Incohérence sur le total de champs du fichier ".$champs." attendu ". count($TypeFichierCorrect[$typefile])."<br />";
						return $messErreur;		
					}
					else
					{
						//On test pas pour biicam car l'encodage change sans arrêt
						//On controle les intitulés
							$col=0;
							foreach ($TypeFichierCorrect[$typefile] as $Key => $Valeur)
							{
								$tab[$col] = self::csv_to_utf8($tab[$col]);	
								if (isset($tab[$col]) && isset($Key) && preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $Key) != preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', preg_replace('~(*BSR_ANYCRLF)\R~', " ",str_replace('"','',$tab[$col]))))
								{
									//die("test".var_dump($tab[$col])."-".var_dump($Key)."-");
									//$tab[$col] = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $tab[$col]); 
									//echo "<br>".preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $tab[$col])."-".preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $Key)."-<br>";
									$testfichier=false;
									$messErreur .= "Intitulé du champs ".$tab[$col]." # ".$Key."<br />";
								}
								else if (!isset($tab[$col]) || !isset($Key))
								{	
									$testfichier=false;
									$messErreur .= "Intitulé de champs inconnu\r\n";
								}
								$col++;
							}
					}
				}
				else if ($ligne >= 1)
				{
					//On controle les datas
					//On controle les intitulés
					$col=0;
					foreach ($TypeFichierCorrect[$typefile] as $Key => $Valeur)
					{
						if (isset($tab[$col]))
						{
							$tab[$col] = self::csv_to_utf8($tab[$col]);
							if (isset($Valeur) && $Valeur != "")
							{	
								$oAttribut = explode("-",$Valeur);
								foreach ($oAttribut as $KeyAtribut => $ValeurAtribut)
								{
									if (isset($ValeurAtribut) && $ValeurAtribut != "")
									{
										if ($ValeurAtribut == "N" && isset($tab[$col]) && is_numeric($tab[$col]) == false && $tab[$col] != "")
										{
											$testfichier=false;
											$messErreur .= $Key." doit être numérique : ".$tab[$col]."<br />";
										}
										if (strpos($ValeurAtribut,"L")!==false && isset($tab[$col]) && strlen($tab[$col]) != substr($ValeurAtribut,1,strlen($ValeurAtribut)))
										{
											$testfichier=false;
											$messErreur .= $Key." doit avoir une longueur de ".substr($ValeurAtribut,1,strlen($ValeurAtribut))." à la ligne ".$ligne."<br />";
										}
										if ($ValeurAtribut == "R" && isset($tab[$col]) && is_numeric($tab[$col]) == false && strlen(str_replace(" ","",$tab[$col])) != 12 && str_replace(" ","",$tab[$col]) != "")
										{
											$testfichier=false;
											$messErreur .= $Key." doit être une référence : ".$tab[$col]."<br />";
										}
										if ($ValeurAtribut == "P" && isset($tab[$col]) && is_numeric($tab[$col]) == false && (strlen(str_replace(" ","",$tab[$col])) != 14 && strlen(str_replace(" ","",$tab[$col])) != 13 )  && str_replace(" ","",$tab[$col]) != "") 
										{
											$testfichier=false;
											$messErreur .= $Key." doit être un PDL : ".$tab[$col]."<br />";
										}
										if ($ValeurAtribut == "RP" && isset($tab[$col]) && is_numeric($tab[$col]) == false && strpos(str_replace(" ","",$tab[$col]),"E") === true && str_replace(" ","",$tab[$col]) != "") 
										{
											$testfichier=false;
											$messErreur .= $Key." doit être un PDL ou une référence : ".$tab[$col]."<br />";
										}
										
									}
								}
							}
						}
						$col++;
					}
					
				}
				else if ($ligne == 30)
					break;
				$ligne++;
			}
		}
		//Test fichier excel
		else IF (isset($TypeFichierCorrect[$typefile]) && ($typefile=="W" || $typefile=="L"))
		{
			ini_set('display_errors', TRUE);
			ini_set('display_startup_errors', TRUE);

			$test_fichier=true;
			require_once $_SERVER["DOCUMENT_ROOT"].'/PHPExcel_1.8.0/Classes/PHPExcel.php';
			$inputFileName  = $_SERVER["DOCUMENT_ROOT"].'/upload_fichier/'.$fichier;
			$array_data = array();
			$info = new SplFileInfo($inputFileName);
			if ($info->getExtension() == "xlsx" || $info->getExtension() == "xls" || $info->getExtension() == "xlsm" || $info->getExtension() == "XLSX" || $info->getExtension() == "XLS" || $info->getExtension() == "XLSM")
			{
				//Autocom
				if ($typefile=="W")
				{
					$XLSXDocument = new PHPExcel_Reader_Excel2007();
					$objPHPExcel = $XLSXDocument->load($inputFileName);
					$worksheet = $objPHPExcel->getSheetByName('Summary_report');
					if ($worksheet != null)
					{
						$cellValue = $worksheet->getCell('C7')->getValue();
						foreach ($TypeFichierCorrect[$typefile] as $Key => $Valeur)	
						{
							if ($worksheet->getCell($Valeur)->getValue() !=  $Key)
							{
								$test_fichier=false;
								$messErreur .= "La colonne ".$Valeur." doit comporter cette entête : ".$Key."<br />";
							}
							//$cellValue = $worksheet->getCell('B'.$ligne)->getValue();
						}
					}
					else
					{
						$test_fichier=false;
						$messErreur .= "L'onglet Sumary_report n'existe pas !<br />";
					}
						
				}
			}
			else
			{
				$test_fichier=false;
				$messErreur .= "Il ne s'agit pas d'un fichier excel valide<br />";
			}
		}
		return $messErreur;
	}
	public static function csv_to_utf8( $str )
	{
		return self::corrige_cara(iconv( 'Windows-1252', 'UTF-8//IGNORE', $str ) );
	}
	 
	public static function corrige_cara( $str )
	{
		return str_replace( array( 'œ', '…' ), array( 'oe', '...' ), $str );
	}
	
	
	//---------------------------------------------------- Méthodes PRIVEES
	//*-----------------------------------------------------------------*//
	// Permet de mettre en place la gestion du multi écran (liste et saisie)
	// Attention l'appel de cette fonction doit être en bas de page php après le pied.php
	public static function multi_ecran()
	{
		echo "<div id='multi_ecran_div' style='position:absolute;top:45px;left:20px;width:40px;'><a class='btn btn-primary btn-large' style='padding:10px 10px 10px 10px;' onclick=\"multi_ecran_affiche();\"><img src='".$_SERVER["DOCUMENT_ROOT"]."/img/pla_agrandit.png' title='".constant('site_basculement')."' /></a></div>";
		?>
		<script type="text/javascript">
		$(document).ready(function()
		{
			$(window).load(function() 
			{ 
					multi_ecran_affiche();
			});
		});	
		</script>
		<?php
	}
		public static function toUtf8($string){
		$string = str_replace('Ã¢','â', $string);
		$string = str_replace('Ã¢','â', $string);
		$string = str_replace('Ã©','é', $string);
		$string = str_replace('à©','é', $string);
		$string = str_replace('í©','é', $string);
		$string = str_replace('Ã ','à', $string);
		$string = str_replace('Ã¨','è', $string);
		$string = str_replace('Ã§','ç', $string);
		$string = str_replace('Â«','«', $string);
		$string = str_replace('Â»','»', $string);
		$string = str_replace("â€™","'", $string);
		$string = str_replace('Ãª','ê', $string);
		$string = str_replace('àª','ê', $string);
		$string = str_replace('â‚¬','€', $string);
		$string = str_replace('Ã´','ô', $string);
		$string = str_replace('Ã¤','ä', $string);
		$string = str_replace('Ã¹','ù', $string);
		$string = str_replace('Ã®','î', $string);
		$string = str_replace('à¨','è', $string);
		$string = str_replace('àª','ê', $string);
		$string = str_replace('Å“','œ', $string);
		$string = str_replace('à§','ç', $string);
		$string = str_replace('à»','û', $string);
		$string = str_replace('à®','î', $string);
		$string = str_replace('à´','ô', $string);
		$string = str_replace('à‰','é', $string);
		$string = str_replace('à€','à', $string);
		$string = str_replace('Â','', $string);
		$string = str_replace('Ã','à', $string);
		
		return $string;
	}

	public static function ConvertSeconde($DiffDate)
	{
		// $oDB = new Mysql();
		$jours = intval($DiffDate[0]);
		$heures = intval($DiffDate[1]);
		$minutes = intval($DiffDate[2]);
		$secondes = intval($DiffDate[3]);
		$secondesTotal = $jours*86400;
		$secondesTotal += $heures*3600;
		$secondesTotal += $minutes *60;
		$secondesTotal += $secondes;
		return $secondesTotal;

	}
	//Durée en seconde
	//Retourne la durée en heures minutes seconde
	public static function Temp($duree)
	{
		$jours=intval($duree / 86400);
		$heures=intval($duree % 86400 / 3600)+intval($jours * 24);
		$minutes=intval(($duree % 3600) / 60);
		$secondes=intval((($duree % 3600) % 60));
		if (strlen($heures) == 1 )$heures="0".$heures;
		if (strlen($minutes) == 1 )$minutes="0".$minutes;
		if (strlen($secondes) == 1 )$secondes="0".$secondes;

		return $heures.":".$minutes.":".$secondes;
	}
	public static function TempsEcouleSuiviAct($date_sqldeb,$date_sqlfin) 
	{
		
		$oDate_deb = explode(" ",$date_sqldeb);
		$oDate_fin = explode(" ",$date_sqlfin);
		if (isset($oDate_deb[0]) && isset($oDate_fin[0]))
		{
			$debut_date = mktime(0, 0, 0, substr($oDate_deb[0],5,2), substr($oDate_deb[0],8,2), substr($oDate_deb[0],0,4));
			$fin_date = mktime(0, 0, 0, substr($oDate_fin[0],5,2), substr($oDate_fin[0],8,2), substr($oDate_fin[0],0,4));
			$tempstotalferier = 0;
			$DateDeLaVeille = date("Y-m-d",$debut_date);
			
			for($i = $debut_date; $i <= $fin_date; $i+=86400)
			{
				$DateEnCours = date("Y-m-d",$i);
				$weekDay = date('w', strtotime($DateEnCours));
				$Ferier = self::date_getHolidays($DateEnCours);
				if ($weekDay == 0 || $weekDay == 6 || $Ferier ==1)
				{
					$tempstotalferier++;
				}

				//On test si la date est fériée
				$TempsFinal = intval($DateEnCours) - intval(($tempstotalferier*24));
			}
		}
		$DiffDateAttente = self::TempsEcoule($date_sqldeb,$date_sqlfin);
		$TempsTotal = Tools::ConvertSeconde($DiffDateAttente);
		if (($TempsTotal - ($tempstotalferier*86400))<0) $TempsTotal = $TempsTotal;
		else $TempsTotal = $TempsTotal - ($tempstotalferier*86400);
		return $TempsTotal;
		/*
		$datetime1 = date_create($date_sqldeb);
		$datetime2 = date_create($date_sqlfin);
		$interval = date_diff($datetime1, $datetime2);
		return array($interval->format('%d'), $interval->format('%H'), $interval->format('%I'),$interval->format('%S')); 
		*/
	}
	
	// Fonction qui teste si un répertoire existe et sinon le créé
	public static function IsDir_or_CreateIt($path) 
	{
      if(is_dir($path)) 
	  {
        return true;
      } 
	  else 
	  {
        if(mkdir($path)) {
          return true;
        } 
		else {
          return false;
        }
      }
    }
	//Fonction qui test si la date est us ou francais --> et retourne la date au format us
	public static function Date_Convert_US($date)
	{
		$DateRetour = "0000-00-00";
		//Format Francais
		if (substr($date,2,1)=="/")
			$DateRetour = self::ConvertToSQLDate($date);
		//Format Us
		else if (substr($date,4,1)=="-")
			$DateRetour = $date;
		return $DateRetour;
	}
}
//-------------------------------------------------------------- FIN CLASSE
?>