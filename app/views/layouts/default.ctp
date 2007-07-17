<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de-DE" lang="de-DE">
    <head>
        <title>NoseRub</title>
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
		<link rel="author" href="mailto:info@noserub.com" title="NoseRub" rev="made" />
        <link rel="copyright" href="http://noserub/contact/" title="Copyright &amp; Contact" />
	    <link rel="home" href="http://noserub.com" title="NoseRub [Home]" />                  
		<?php echo $html->charset('UTF-8')?>
		<meta http-equiv="content-script-type" content="text/javascript" />
		<meta http-equiv="content-language" content="de" />
		<meta name="robots" content="index,follow" />
		<meta name="allow-search" content="yes" />
		<meta name="rating" content="general" />
		<meta name="distribution" content="global" />
		<meta name="revisit" content="14 days" />
		<meta name="author" content="NoseRub" />
		<meta name="publisher" content="NoseRub" />
		<meta name="copyright" content="NoseRub, 2007" />
		<meta name="language" content="de" />
		<meta name="ICBM" content="50.945,6.89" />
        <meta http-equiv="pragma" content="no-cache" />
        <meta http-equiv="expires" content="-1" />
        <meta http-equiv="cache-control" content="no-cache" />
        <meta name="keywords" content="noserub,social,network,decentral,contacts,profiles,web 3.0" />
		<meta name="description" content="NoseRub - Solutions for decentralised social network" />
        <?php echo $this->renderElement('javascript'); ?>
        <?php echo $this->renderElement('css'); ?>
    </head>
    <body class="jamal {controller:'<?php echo $this->name; ?>',action:'<?php echo $this->action; ?>'<?php echo ($session->check('User')?',session:true':''); ?>}">
        <div id="banner">
            <div id="header">
                <a id="logo" href="/"><img src="/chrome/site/ormigo_banner.png" alt="" />NoseRub</a>
                <hr />
            </div>
            <?php echo $this->renderElement('metanav'); ?>
        </div>
        <?php echo $this->renderElement('mainnav'); ?>
        
        <div id="main">
            <?php echo $this->renderElement('subnav'); ?>
            <div id="content">
                <?php echo $content_for_layout?>
            </div>
        </div>
   </body>
</html>