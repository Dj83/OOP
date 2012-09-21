<?php


/*
1. google javascript keys
2. facebook sdk key
3. generator

<!-- metadata added to a page at the menu or default template settings
classification, distribution, rating, copyright, author, revisit-after, reply-tp, 
fb:admins, fb:appid, fb:pageid, og:sitename, og:url-->

*/
    

//$this->_links = array();
//$this->_links = array('url/ad/ad/'=>array(
//	'relation'=>'',
//	'relType'=>'',
//	'attribs'=>array(
//		'type'=>'',
//		'title'=>''
//	)
//));
//$this->_custom  = array();
//$this->template;
//$this->baseurl;
//$this->params;// = JRegistry Object
//$this->_file;
//$this->title;
//$this->description;
//$this->link;
//$this->base;
//$this->language;
//$this->direction;
//$this->_generator = 'Hen Factory Web Application Framework';
//$this->_mdate;
//$this->_tab;
//$this->_lineEnd;
//$this->_charset;
//$this->_mime;
//$this->_namespace;
//$this->_profile;
//$this->_scripts = array('http://ajax.googleapis.com/ajax/libs/dojo/1.7.1/dojo/dojo.js'=>array(
//	'mime'=>'text/javascript',
//	'defer'=>'',
//	'async'=>''
//));
//$this->_script= array('text/javascript'=>'');
//$this->_styleSheets = array(
//	''=>array('mime'=>'text/css','media'=>'all','attribs'=>array())
//);
//$this->_style = array('

//');
//$this->_metaTags = array('http-equiv'=>'text/html','standard'=>array('rights'=>''));
//$this->_engine;
//$this->_type;
//$this->_scripts['http://ajax.googleapis.com/ajax/libs/dojo/1.7.1/dojo/dojo.js']=array(
//	'mime'=>'text/javascript',
//	'defer'=>'',
//	'async'=>''
//);
//$this->_styleSheets['http://ajax.googleapis.com/ajax/libs/dojo/1.7.1/dijit/themes/claro/claro.css']=array('mime'=>'text/css','media'=>'all','attribs'=>array(/*associative*/));



//============================================================

class HenDocument extends JObject
{
/* TODO:
At each object we can apply a new html layout, same goes for modules as well so we'll apply column layouts here and depend on Chrome for chrome methods
// chrome attaches to this as well 
we over-ride modules and module renderer that way, JDocumentHTML return an empty string into the buffer and nothing needs be parsed since we 
let the render method call the module method, and call the module chrome methods... the chrome method attaches itself right here and the module just spits out valid HTML to be cleaned by our generator


this is more used to apply a designers html to a particular module, component, etc and this wrapper will clean those items and replace the data accordingly
s 

so we can change the html on a module/component by using its indexes on the DOM
a designer can add a class, an id, a script declaration, dojo events and markup,

we can also wrap stuff with widgets


if designer changes modules->element(2) we can change that element and children with his new html/style/class/id/events
// we can also figure out all the styles used on this element by determining that from the 
css properties we have LMAO by merging in all the appropriate declarations to his particular element
/// no no no


we can change dojo names used as they're ugly

lets create a dojo theme creator on a per eloement basis

lets load in all themes needed right here as plugins/buttons and wrappers for grid

then those can be used when creating a template automatically

*/
	private $html;
	private $head;
	public	$body;

	public	$document;
	private	$config;

	function __construct ($properties=null)
	{
		self::build();
		parent::__construct($properties ? $properties : self::createDocument());
	}
   
   
	public static function getInstance($properties=null)
	{
		static $instance;
		if(!$instance){
			$instance = new HenTemplate($properties);
		}
		return $instance;
	}
	/*public function addStyleSheet ( $url, $media='all' )
	{
		$element = $this->document->createElement( 'link' );
		$element->setAttribute( 'type', 'text/css' );
		$element->setAttribute( 'href', $url );
		$element->setAttribute( 'media', $media );
		$this->styles[] = $element;
	}
   
   
	public function addScript ( $url )
	{
		$element = $this->document->createElement( 'script', ' ' );
		$element->setAttribute( 'type', 'text/javascript' );
		$element->setAttribute( 'src', $url );
		$this->scripts[] = $element;
	}
   
   
	public function addMetaTag ( $name, $content )
	{
		$element = $this->document->createElement( 'meta' );
		$element->setAttribute( 'name', $name );
		$element->setAttribute( 'content', $content );
		$this->metas[] = $element;
	}
   
   
	public function setDescription ( $dec )
	{
		$this->addMetaTag( 'description', $dec );
	}
   
   
	public function setKeywords( $keywords )
	{
		$this->addMetaTag( 'keywords', $keywords );
	}
   
	public function addNode($nodeName, $nodeValue=null, $grid='wrapper',$position)
	{
		
		return $this->get($parent)->createElement($nodeName,$nodeValue);
	}

	public function addComment($comment,&$node=null, $grid='html',$position=null){
		$text = $this->document->createComment($comment);
		return $node ? $node->appendChild($text) : $position ? $this->get($grid)->appendChild($text) : $this->get($grid)->get($position)->appendChild($text);
		
	}

   public function addText($text, &$node)
   {
	   $content = $this->document->createTextNode($text);
	   return $node->appendChild($content);
   }*/
	protected function prepareDocument()
	{
		
		// create all elements needed for the head
		$this->document->setGenerator($this->config->get('generator',JText::_('HEN_FACTORY_GENERATOR')));

		/*if ( is_array( $this->styles ))
			foreach ( $this->styles as $element )
				$this->head->appendChild( $element );
		// Construct scripts if needed
		if(  is_array( $this->scripts ))
			foreach ( $this->scripts as $element )
				$this->head->appendChild( $element );
		// Add meta tags if needed
		if ( is_array( $this->metas ))
			foreach ( $this->metas as $element )
				$this->head->appendChild( $element );
		$this->head->appendChild( $title );*/
		// generate the classes, wrappers, jdoc's, etc for the template
		// TODO: grab a manifest file for the template...
		// if this is a template on a customer site this manifest comes from the current domains CDN
		// when installed or viewing a theme, this comes from a designers CDN
		// also so do the images
		// TODO: before we output a document, lets change all the HREF and images to compressed cached data
		// and we'll also count the bytes in this entire thing before we output it to the browser
		
		// we can also compress it so that it minizes its usage, same for images
	   
	}

	public function outputDocument()
	{
		self::prepareDocument();
		return $this->generator->saveXML();
	}

	protected function createDocument()
	{
		$html5 = $this->config->get('html5');

		// Prepare some HTML tags
		$tags = array(
			'section' => $html5 ? 'section' : 'div',
			'header' => $html5 ? 'header' : 'div',
			'aside' => $html5 ? 'aside' : 'div',
			'time' => $html5 ? 'time' : 'span',
			'nav' => $html5 ? 'nav' : 'div',
			'article' => $html5 ? 'article' : 'div',
			'summary' => $html5 ? 'summary' : 'div',
			'footer' => $html5 ? 'footer' : 'div'
		);

		// Grids
		$absolute 	= $this->generator->createElement('div',' ');

		// TODO: still needs module position elements and JDOC statements
		$toolbar	= $this->generator->createElement('div',' ');/* id=toolbar */
		$headerbar 	= $this->generator->createElement('div',' '); /* id=headerbar */
		$menubar 	= $this->generator->createElement('div',' '); /* id=headerbar */
		$banner 	= $this->generator->createElement('div',' '); /* id=headerbar */

		$header 	= new JObject(array(
			'toolbar'=>$toolbar,
			'headerbar'=>$headerbar,
			'menubar'=>$menubar,
			'banner'=>$banner
		));

		$top = $this->generator->createElement($tags['section'],' ');

		// TODO: need two elements, main & main-inner, then this content, NO JDOC statements needed
			$breadcrumbs	= $this->generator->createElement($tags['nav'],' ');/* id=toolbar */
			$contenttop 	= $this->generator->createElement($tags['aside'],' '); /* id=headerbar */
			$content 		= $this->generator->createElement('div',' '); /* id=headerbar */
			$contentbottom 	= $this->generator->createElement($tags['aside'],' '); /* id=headerbar */
			$contentleft 	= $this->generator->createElement($tags['aside'],' '); /* id=headerbar */
			$contentright 	= $this->generator->createElement('div',' '); /* id=headerbar */
		$main = new JObject(array(
			'breadcrumbs'=>$breadcrumbs,
			'contenttop'=>$contenttop,
			'content'=>$content,
			'contentbottom'=>$contentbottom,
			'contentleft'=>$contentleft,
			'contentright'=>$contentright
		));

		$bottom = $this->generator->createElement($tags['section'],' ');
		$left 	= $this->generator->createElement($tags['section'],' ');
		$right	= $this->generator->createElement($tags['aside'],' ');
		$footer = $this->generator->createElement($tags['footer'],' ');

		unset($tags);
		// Set the body grids
		$wrapper = $this->generator->createElement('div',' ');
		$dialog = $this->generator->createElement('div',' ');

		$body = new JObject(array(
			'generator'=>	&$this->generator,
			'node'=>		$this->generator->createElement('body',' '),
			'absolute'=>	$absolute,
			'wrapper'=>		$wrapper,
			'header'=>		$header,
			'top'=>			$top/* Object of elements*/,
			'main'=>		$main /* Object of elements; $page->get('body')->get('main')->get('breadcrumbs') */,
			'bottom'=>		$bottom,
			'left'=>		$left,
			'right'=>		$right,
			'footer'=>		$footer,
			'dialog'=>		$dialog,
		));
//============================================================================
		$html = $this->generator->createElement('html');
		if($this->config->get('dtd')->type == 'xhtml'){
			$html->setAttribute( 'xmlns', 'http://www.w3.org/1999/xhtml' );
			$html->setAttribute( 'xml:lang', $this->document->get('') );
			$html->setAttribute( 'lang', 'en' );
		}
	   
	   
		$this->generator->appendChild($html);

//============================================================================


		return array('html'=>$html,'body'=>$body,'head'=>$this->generator->createElement( 'head', ' ' ),'document'=>&JFactory::getDocument());

	}

	// Used when settings up a JDoc statemnet at a grid container
	protected function calculateProportion($position) {
		$doc =& JFactory::getDocument();
		$params = array();
		// Typecast the value to a string
		$position = (string) $position;

		// Find a proportion setting
		$proportion = $this->config->get($position . '_proportion', 'equal');

		if ($count = JFactory::getDocument()->countModules($position)) {
			// module position params
			$max = count($this->proportions[$proportion]);
			if ($count > $max) $count = $max;
			$params['name'] = $pos;
			$params['count'] = $count;
			$params['values'] = $this->proportions[$proportion][$count];
		}
		return $params;
	}
	protected function _calculate()
	{
		static $proportions = array(
			'equal' => array(		
				1 => array('width100'),
				2 => array('width50', 'width50'),
				3 => array('width33', 'width33', 'width33'),
				4 => array('width25', 'width25', 'width25', 'width25'),
				5 => array('width20', 'width20', 'width20', 'width20', 'width20')
			),
			'ratio' => array(
				1 => array('width100'),
				2 => array('width65', 'width35'),
				3 => array('width54', 'width23', 'width23'),
				4 => array('width45', 'width18', 'width18', 'width18'),
				5 => array('width40', 'width15', 'width15', 'width15', 'width15')
			),
			'double' => array(		
				1 => array('width100'),
				2 => array('width50', 'width50'),
				3 => array('width50', 'width25', 'width25'),
				4 => array('width25', 'width25', 'width25', 'width25'),
				5 => array('width20', 'width15', 'width15', 'width35', 'width15')
			),
			'stack' => array(		
				1 => array('width100'),
				2 => array('width100', 'width100'),
				3 => array('width100', 'width100', 'width100')
			)
		);

		$array = array();
		$array[0] = array(/* head*/);
		$array[1] = array(/* body*/);
		$array[1][0] = array(/*wrapper*/);
		$array[1][0][1] = array(/*header*/);
		$array[1][0][1][0] = array(/*toolbar*/);
		$array[1][0][1][0][0] = array(/*toolbarleft*/);
		$array[1][0][1][0][1] = array(/*toolbarright*/);
		$array[1][0][1][1] = array(/*headerbar*/);
		$array[1][0][1][1][0] = array(/*logo*/);
		$array[1][0][1][1][1] = array(/*headerleft*/);
		$array[1][0][1][1][2] = array(/*headerright*/);
		$array[1][0][1][2] = array(/*menubar*/);
		$array[1][0][1][2][0] = array(/*menu*/);
		$array[1][0][1][2][1] = array(/*seach*/);
		$array[1][0][1][3] = array(/*banner*/);
		$array[1][0][2]['proportions'] = $proportions/* top */;


		$array[1][0][3] = array(/*main*/);
		$array[1][0][3][0] = array(/*main*/);
		$array[1][0][3][0][0] = array(/*maininner  */);
		$array[1][0][3][2] = array(/*main*/);


		$array[1][0][4]['proportions'] = $proportions/* bottom */;
		$array[1][0][5]['proportions'] = $proportions/* footer */;
		$array[1][1] = array(/* absolute */);
		$array[1][2] = array(/* dialog*/);

		
		
		return;
	}
	protected function build()
	{
		$this->config = new JRegistry($this->document->params);

		jimport('joomla.environment.browser');
		$browser = JBrowser::getInstance();

		$html5Compatible = '';
		$this->config->def('html5', $htmlCompatible);
		$this->config->def('mobile',$browser->isMobile());

		if ($browser->getBrowser()=='msie')
		{
			if ($browser->getMajor() <= 8) {
				$this->config->set('html5',false);
			}
		}
		$this->config->set('browser',$browser->getBrowser());
		
		$dtd = new stdClass();
		$dtd->type = 'html5';
		$dtd->html = 'html';
		$dtd->external = '';
		$dtd->internal = '';
		if(!$this->config->get('html5')){
			switch($this->config->get('dtd')){
				case 1:// HTML 4
				$dtd->type = 'html4';
				$dtd->html = 'HTML';
				$dtd->intern = '-//W3C//DTD HTML 4.01 Frameset//EN';
				$dtd->extern = 'http://www.w3.org/TR/html4/frameset.dtd';
				case 2: // XHTML 1
				default:
				$dtd->type = 'xhtml';
				$dtd->html = 'html';
				$dtd->external = '-//W3C//DTD XHTML 1.0 Frameset//EN';
				$dtd->internal = 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd';
			}
		}
		$dom = DOMImplementation::createDocument('', '', DOMImplementation::createDocumentType($dtd->html,$dtd->external,$dtd->internal));
		if($dtd->type == 'html4'){
			// take out the xml attributes
			$this->generator = new DOMDocument();		
			$this->generator->loadXML($dom->saveXML());
		}else{
			$this->generator = $dom;			
		}
		//unset($dom);
		//unset($dtd);
		$this->generator->encoding = 'UTF-8';
		$this->generator->formatOutput = true;
	}
}
