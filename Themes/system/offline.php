<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
$this->setGenerator('Sold! Customer Relationship Manager');
jimport('hen.theme.configuration');
HenTemplate::renderCSS($this);
//if(JFactory::getApplication()->getUser()){
	//$this->addScript(JURI::root().'templates/'. $this->template .'/js/layouts.js');
//}
?>
<!DOCTYPE HTML>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="head" />
</head>
<body id="page" class="page<?php echo HenTemplate::getBodyClasses() ?>">

	<div id="page-body">

		<div class="page-body-1">

			<?php if($this->countModules('absolute')): ?>
			<div id="absolute">
				<jdoc:include type="modules" name="absolute" />
			</div>
			<?php endif; ?>

			<div class="wrapper grid-block">

				<header id="header">

					<div id="toolbar" class="grid-block">

						<?php if($this->countModules('offline-toolbar-l')): ?>
						<div class="float-left">
							<jdoc:include type="modules" name="offline-toolbar-l" />
						</div>
						<?php endif; ?>
							
						<?php if( $this->countModules('offline-toolbar-r') || $this->params->get('show_date',1)) : ?>

							<div class="float-right">

								<jdoc:include type="modules" name="offline-toolbar-r" />

								<?php if ($this->params->get('show_date',1)) : ?>
								<time datetime="<?php echo new JDate(); ?>"></time>
								<?php endif; ?>

							</div>

						<?php endif; ?>
						
					</div>

					<?php if($this->countModules('logo + offline-headerbar')): ?>
					<div id="headerbar" class="grid-block">
					
						<?php if($this->countModules('logo')): ?>
						<a id="logo" href="<?php echo JURI::root(); ?>"><jdoc:include type="modules" name="logo" /></a>
						<?php endif; ?>
						
						<?php if($this->countModules('offline-headerbar')): ?>
						<div class="left"><jdoc:include type="modules" name="offline-headerbar" /></div>
						<?php endif; ?>
						
					</div>
					<?php endif; ?>

					<?php if($this->countModules('offline-banner')): ?>
					<div id="banner"><jdoc:include type="modules" name="offline-banner" /></div>
					<?php endif;  ?>
				
				</header>

				<?php if($this->countModules('offline-top-a')): ?>
				<section id="top-a" class="grid-block"><jdoc:include type="modules" name="offline-top-a" /></section>
				<?php endif; ?>
				
				<?php if($this->countModules('offline-top-b')): ?>
				<section id="top-b" class="grid-block"><jdoc:include type="modules" name="offline-top-b" /></section>
				<?php endif; ?>
				
				<div id="main" class="grid-block">
				
					<div id="maininner" class="grid-box">
					
						<?php if($this->countModules('offline-innertop')): ?>
						<section id="innertop" class="grid-block"><jdoc:include type="modules" name="offline-innertop" /></section>
						<?php endif; ?>
						<section id="content" class="grid-block">
							<jdoc:include type="message" />
							<div id="">	<form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login">
	<fieldset class="input">
		<p id="form-login-username">
			<label for="username"><?php echo JText::_('JGLOBAL_USERNAME') ?></label>
			<input name="username" id="username" type="text" class="inputbox" alt="<?php echo JText::_('JGLOBAL_USERNAME') ?>" size="18" />
		</p>
		<p id="form-login-password">
			<label for="passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
			<input type="password" name="password" class="inputbox" size="18" alt="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" id="passwd" />
		</p>
		<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGIN') ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" />
		<input type="hidden" name="return" value="<?php echo base64_encode(JURI::base()) ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</fieldset>
	</form>
</div>
						</section>
						<?php if($this->countModules('offline-innerbottom')): ?>
						<section id="innerbottom" class="grid-block"><jdoc:include type="modules" name="offline-innerbottom" /></section>
						<?php endif; ?>

					</div>
					<!-- maininner end -->
					
				<?php if($this->countModules('offline-sidebar-a + offline-sidebar-b')): ?>
					<?php if($this->countModules('offline-sidebar-a')): ?>
					<aside id="sidebar-a" class="grid-box"><jdoc:include type="modules" name="offline-sidebar-a" /></aside>
					<?php endif; ?>
					
					<?php if($this->countModules('offline-sidebar-b')): ?>
					<aside id="sidebar-b" class="grid-box"><jdoc:include type="modules" name="offline-sidebar-b"  /></aside>
					<?php endif; ?>

				</div>
				<?php endif; ?>
				<!-- main end -->

				<?php if($this->countModules('offline-bottom-a')): ?>
				<section id="bottom-a" class="grid-block"><jdoc:include type="modules" name="offline-bottom-a" /></section>
				<?php endif; ?>
				
				<?php if($this->countModules('offline-bottom-b')): ?>
				<section id="bottom-b" class="grid-block"><jdoc:include type="modules" name="offline-bottom-b" /></section>
				<?php endif; ?>
				
			</div>

            <footer id="footer" class="grid-block">

                <?php //if ($this->params->get('totop_scroller', 1)) : ?>
                <a id="totop-scroller" href="#page"></a>
                <?php //endif; ?>

                <jdoc:include type="modules" name="footer" />
                <jdoc:include type="modules" name="debug" />

            </footer>

		</div>

	</div>
	
</body>
</html>