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

			<?php if($this->countModules('socialbar')): ?>
			<div id="socialbar">
				<jdoc:include type="modules" name="socialbar" />
			</div>
			<?php endif; ?>
			
			<div class="wrapper grid-block">

				<header id="header">

					<div id="toolbar" class="grid-block">

						<?php if($this->countModules('toolbar-l')): ?>
						<div class="float-left">
							<jdoc:include type="modules" name="toolbar-l" />
						</div>
						<?php endif; ?>
							
						<?php if( $this->countModules('toolbar-r') || $this->params->get('show_date')) : ?>

							<div class="float-right">

								<jdoc:include type="modules" name="toolbar-r" />

								<?php if ($this->params->get('show_date')) : ?>
								<time datetime="<?php echo '' ?>"></time>
								<?php endif; ?>

							</div>

						<?php endif; ?>
						
					</div>

					<?php if($this->countModules('logo + headerbar')): ?>
					<div id="headerbar" class="grid-block">
					
						<?php if($this->countModules('logo')): ?>
						<a id="logo" href="<?php echo JURI::root(); ?>"><jdoc:include type="modules" name="logo" /></a>
						<?php endif; ?>
						
						<?php if($this->countModules('headerbar')): ?>
						<div class="left"><jdoc:include type="modules" name="headerbar" /></div>
						<?php endif; ?>
						
					</div>
					<?php endif; ?>

					<div id="menubar" class="grid-block">
						
						<?php if($this->countModules('menu')): ?>
						<nav id="menu"><jdoc:include type="modules" name="menu" /></nav>
						<?php endif; ?>

						<?php if($this->countModules('search')): ?>
						<div id="search"><jdoc:include type="modules" name="search" /></div>
						<?php endif; ?>
						
					</div>
				
					<?php if($this->countModules('banner')): ?>
					<div id="banner"><jdoc:include type="modules" name="banner" /></div>
					<?php endif;  ?>
				
				</header>

				<?php if($this->countModules('top-a')): ?>
				<section id="top-a" class="grid-block"><jdoc:include type="modules" name="top-a" style="<?php echo $params->get('top-a_layout', 'stack'); ?>" /></section>
				<?php endif; ?>
				
				<?php if($this->countModules('top-b')): ?>
				<section id="top-b" class="grid-block"><jdoc:include type="modules" name="top-b" style="<?php echo $params->get('top-b_layout', 'stack'); ?>" /></section>
				<?php endif; ?>
				
				<div id="main" class="grid-block">
				
					<div id="maininner" class="grid-box">
					
						<?php if($this->countModules('innertop + breadcrumbs')): ?>
						<?php if($this->countModules('innertop')): ?>
						<section id="innertop" class="grid-block"><jdoc:include type="modules" name="innertop" style="<?php echo $params->get('innertop_layout', 'stack'); ?>" /></section>
						<?php endif; ?>

						<?php if($this->countModules('breadcrumbs')): ?>
						<section id="breadcrumbs" class="grid-block"><div id="submenu"><jdoc:include type="modules" name="breadcrumbs" /></div></section>
						<?php endif; ?>
                        <?php endif; ?>
						<section id="content" class="grid-block">
							<jdoc:include type="message" />
							<jdoc:include type="component" />
						</section>

						<?php if($this->countModules('innerbottom')): ?>
						<section id="innerbottom" class="grid-block"><jdoc:include type="modules" name="innerbottom" style="<?php echo $params->get('innerbottom_layout', 'stack'); ?>" /></section>
						<?php endif; ?>

					</div>
					<!-- maininner end -->
					
				<?php if($this->countModules('sidebar-a + sidebar-b')): ?>
					<?php if($this->countModules('sidebar-a')): ?>
					<aside id="sidebar-a" class="grid-box"><jdoc:include type="modules" name="sidebar-a" style="<?php echo $params->get('sidebar-a_layout', 'stack'); ?>" /></aside>
					<?php endif; ?>
					
					<?php if($this->countModules('sidebar-b')): ?>
					<aside id="sidebar-b" class="grid-box"><jdoc:include type="modules" name="sidebar-b" style="<?php echo $params->get('sidebar-b_layout', 'stack'); ?>" /></aside>
					<?php endif; ?>

				<?php endif; ?>
				</div>
				<!-- main end -->

				<?php if($this->countModules('bottom-a')): ?>
				<section id="bottom-a" class="grid-block"><jdoc:include type="modules" name="bottom-a" style="<?php echo $params->get('bottom-a_layout', 'stack'); ?>" /></section>
				<?php endif; ?>
				
				<?php if($this->countModules('bottom-b')): ?>
				<section id="bottom-b" class="grid-block"><jdoc:include type="modules" name="bottom-b" style="<?php echo $params->get('bottom-b_layout', 'stack'); ?>" /></section>
				<?php endif; ?>
                <footer id="footer" class="grid-block">
    
                    <?php //if ($this->params->get('totop_scroller', 1)) : ?>
                    <a id="totop-scroller" href="#page"></a>
                    <?php //endif; ?>
    
                    <jdoc:include type="modules" name="footer" />
                    <jdoc:include type="modules" name="debug" />
    
                </footer>
				
			</div>

		</div>

	</div>

</body>
</html>