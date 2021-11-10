<?php
// Page created by Shepard [Fabian Pijcke] <Shepard8@laposte.net>
// Arno Esterhuizen <arno.esterhuizen@gmail.com>
// and Romain Bourdon <rromain@romainbourdon.com>
// and Hervé Leclerc <herve.leclerc@alterway.fr>
// Icons by Mark James <http://www.famfamfam.com/lab/icons/silk/>
// Version 2.5 -> 3.0.0 by Dominique Ottello aka Otomatic
// 3.1.9 - Support VirtualHost IDNA ServerName
//
//
//

$server_dir = "../";
$aliasDir = $server_dir.'alias/';

require $server_dir.'scripts/config.inc.php';
require $server_dir.'scripts/wampserver.lib.php';

//Fonctionne à condition d'avoir ServerSignature On et ServerTokens Full dans httpd.conf
$server_software = $_SERVER['SERVER_SOFTWARE'];
$error_content = '';

// on récupère les versions des applis
$phpVersion = $wampConf['phpVersion'];
$apacheVersion = $wampConf['apacheVersion'];
$doca_version = 'doca'.substr($apacheVersion,0,3);
$mysqlVersion = $wampConf['mysqlVersion'];

$VirtualHostMenu = $wampConf['VirtualHostSubMenu']; // On récupère la valeur de VirtualHostMenu
$port = $wampConf['apachePortUsed'];                // on récupère la valeur de apachePortUsed
$UrlPort = $port !== "80" ? ":".$port : '';
$ListenPorts = implode(' - ',listen_ports());       // On récupère le ou les valeurs des ports en écoute dans Apache
$Mysqlport = $wampConf['mysqlPortUsed'];            // on récupère la valeur de mysqlPortUsed

$projectsListIgnore = array ('.','..','wampthemes','wamplangues'); // répertoires à ignorer dans les projets

//affichage du phpinfo
if (isset($_GET['phpinfo'])) {
	$type_info = intval(trim($_GET['phpinfo']));
	if($type_info < -1 || $type_info > 64)
		$type_info = -1;
	phpinfo($type_info);
	exit();
}

// TODO: continue adding stuff

?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $langues['titreHtml'] ?></title>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width">
		<link id="stylecall" rel="stylesheet" href="wampthemes/classic/style.css" />
		<link rel="shortcut icon" href="favicon.ico" type="image/ico" />
		<script defer>
			var select = document.getElementById("themes");
			if (select.addEventListener) {
				/* Only for modern browser and IE > 9 */
				var stylecall = document.getElementById("stylecall");
			
				/* looking for stored style name */
				var wampStyle = localStorage.getItem("wampStyle");
			
				if (wampStyle !== null) {
					stylecall.setAttribute("href", "wampthemes/" + wampStyle + "/style.css");
					selectedOption = document.getElementById(wampStyle);
					selectedOption.setAttribute("selected", "selected");
				} else {
					localStorage.setItem("wampStyle","classic");
					selectedOption = document.getElementById("classic");
					selectedOption.setAttribute("selected", "selected");
				}
				/* Changing style when select change */
			
				select.addEventListener("change", function(){
					var styleName = this.value;
					stylecall.setAttribute("href", "wampthemes/" + styleName + "/style.css");
					localStorage.setItem("wampStyle", styleName);
				})
			}
		</script>
	</head>

	<body>
		<header id="head">
			<div class="innerhead">
				<h1>
					<abbr title="Windows">W</abbr>
					<abbr title="Apache">a</abbr>
					<abbr title="MySQL/MariaDB">m</abbr>
					<abbr title="PHP">p</abbr>
					<abbr title="server WEB local">server</abbr>
				</h1>
				<ul>
					<li>Apache 2.4</li><li>-</li>
					<li>MySQL 5 &amp; 8</li><li>-</li>
					<li>MariaDB 10</li><li>-</li>
					<li>PHP 5 &amp; 7</li>
				</ul>
				<ul class="utility">
					<li>Version <?php echo $c_wampVersion ?> - <?php echo $c_wampMode ?></li>
					<li><?php
						// Language
						$langue = $wampConf['language'];
						$i_langues = glob('wamplangues/index_*.php');
						$languages = array();
						foreach ($i_langues as $value) {
							$languages[] = str_replace(array('wamplangues/index_','.php'), '', $value);
						}
						$langueget = (!empty($_GET['lang']) ? strip_tags(trim($_GET['lang'])) : '');
						if(in_array($langueget,$languages))
							$langue = $langueget;

						// Recherche des différentes langues disponibles
						$langueswitcher = '<form method="get" style="display:inline-block;"><select name="lang" id="langues" onchange="this.form.submit();">'."\n";
						$selected = false;
						foreach ($languages as $i_langue) {
							$langueswitcher .= '<option value="'.$i_langue.'"';
							if(!$selected && $langue == $i_langue) {
								$langueswitcher .= ' selected ';
								$selected = true;
							}
							$langueswitcher .= '>'.$i_langue.'</option>'."\n";
						}
						echo $langueswitcher.'</select></form>';
					?></li>
					<li><?php
						// Recherche des différents thèmes disponibles
						$styleswitcher = '<select id="themes">'."\n";
						$themes = glob('wampthemes/*', GLOB_ONLYDIR);
						foreach ($themes as $theme) {
							if (file_exists($theme.'/style.css')) {
								$theme = str_replace('wampthemes/', '', $theme);
								$styleswitcher .= '<option id="'.$theme.'">'.$theme.'</option>'."\n";
							}
						}
						echo $styleswitcher.'</select>'."\n";
					?></li>
				</ul>
			</div>
		</header>

	<div class="config">
		<div class="innerconfig">
			<h2><?php echo $langues['titreConf'] ?></h2>

			<dl class="content">
				<dt><?php echo $langues['versa'] ?></dt>
					<dd><?php echo $apacheVersion ?>&nbsp;&nbsp;-&nbsp;<a href='http://<?php echo $langues[$doca_version] ?>'><?php echo $langues['documentation'] ?></a></dd>
				<dt><?php echo $langues['server'] ?></dt>
					<dd><?php echo $server_software ?>&nbsp;-&nbsp;<?php echo $langues['portUsed'] ?><?php echo $ListenPorts ?></dd>
				<dt><?php echo $langues['versp'] ?></dt>
					<dd><?php echo $phpVersion ?>&nbsp;&nbsp;-&nbsp;<a href='http://<?php echo $langues['docp'] ?>'><?php echo $langues['documentation'] ?></a></dd>
				<dt><?php echo $langues['phpExt'] ?></dt>
				<dd>
					<ul>
                        <?php echo $phpExtContents ?>
					</ul>
				</dd>
				<?php echo $DBMSTypes ?>
			</dl>
		</div>
	</div>

	<div class="divider1">&nbsp;</div>

	<div class="alltools ${allToolsClass}">
		<div class="inneralltools">
			<div class="column">
				<h2><?php echo $langues['titrePage'] ?></h2>
				<ul class="tools">
					<li><a href="?phpinfo=-1">phpinfo()</a></li>
					<?php echo $phpmyadminTool ?>
					<?php echo $addVhost ?>
				</ul>
			</div>
			<div class="column">
				<h2><?php echo $langues['txtProjet'] ?></h2>
				<ul class="projects">
                    <?php echo $projectContents ?>
				</ul>
			</div>
			<div class="column">
				<h2><?php echo $langues['txtAlias'] ?></h2>
				<ul class="aliases">
                    <?php echo $aliasContents ?>
				</ul>
			</div>

            <?php if($VirtualHostMenu == "on") {
                echo <<< EOPAGEA
                    <div class="column">
                        <h2>{$langues['txtVhost']}</h2>
                        <ul class="vhost">
                            ${vhostsContents}
                        </ul>
                    </div>
                EOPAGEA;
            } ?>

            <?php if(!empty($error_content)) {
                echo <<< EOPAGEB
                    <div id="error" style="clear:both;"></div>
                    ${error_content}
                EOPAGEB;
            } ?>
            </div>
		</div>

		<div class="divider2">&nbsp;</div>

		<ul id="foot">
			<li>
                <a href="<?php echo $langues['forumLink'] ?>">
                    <?php echo $langues['forum'] ?>
                </a>
            </li>
		</ul>

        <!-- // TODO: find out what &amp; and &nbsp; means -->
	</body>
</html>