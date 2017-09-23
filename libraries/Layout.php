<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * A Codeigniter library for handling views with support of layouting system
 * @author H.M.S.Nishantha <srimal@3cs.lk>
 */
class Layout
{

	protected $pagevars;
	public $layout;
	public $page;

	public $pageCss;
	public $pageJs;

	public $layout_dir;
	public $page_dir;
	public $block_dir;
	public $page_meta;


	public function __construct()
	{
		//Load config file
        $this->load->config('layouts');

        $this->layout_dir = $this->config->item('layouts_folder');
        $this->page_dir = $this->config->item('pages_folder');
        $this->block_dir = $this->config->item('blocks_folder');

	}

/**
 * Render a layout with predefined set of blocks
 * @param  string  $layout     [Base layout file]
 * @param  string  $page       [inner page view file]
 * @param  string  $vars       [page variables]
 * @param  boolean $get_string [return page content as variable]
 * @return void
 */
	public function render($layout = null,$page = null, $vars = null, $get_string = false)
	{
		if($layout != null){
			$this->setPageLayout($layout);
		}

		if($page != null){

			$this->setPage($page);
		}

		$this->pagevars['pageCss'] = $this->getPageCss();
		$this->pagevars['pageJs'] = $this->getPageJs();

		if($vars != null){
			$this->setPageVars($vars);
		}

		$data['head'] = $this->load->view($this->block_dir."/".'head', $this->pagevars, true);
		$data['topnav'] = $this->load->view($this->block_dir."/".'topnav',$this->pagevars,  true);
		$data['leftnav'] = $this->load->view($this->block_dir."/".'leftnav', $this->pagevars,  true);
		$data['breadcrumb'] = $this->load->view($this->block_dir."/".'breadcrumb', $this->pagevars,  true);
		$data['page'] = $this->load->view($this->page, $this->pagevars,  true);
		$data['footer'] = $this->load->view($this->block_dir."/".'footer', $this->pagevars,  true);

		$this->load->view($this->layout, $data, $get_string);
	}

/**
 * Set page variables
 * @param array $vars [array of page variables]
 */
	public function setPageVars($vars)
	{
		$this->pagevars = is_array($this->pagevars) ? array_merge($this->pagevars, $vars) : $vars;
	}

/**
 * Set page layout
 * @param string $layout [page layout file name to be rendered]
 */
    public function setPageLayout($layout)
    {
    	$this->layout = $this->config->item('layouts_folder')."/".$layout;
    }

/**
 * Set 
 * @param array $meta
 */
    public function setPageMeta($meta){

    	foreach ($meta as $key => $value) {
    		$this->pagevars['meta'][$key] = $value;
    	}
    }

/**
 * Set inner page to be rendered within the layout
 * @param string $page [file name of the page]
 */
    public function setPage($page)
    {
    	$this->page = $this->config->item('pages_folder')."/".$page;

    	$all_meta = $this->config->item("page_meta");
    	if(!isset($this->pagevars['meta']) && isset($all_meta[$page])){
    		
    		$this->setPageMeta($all_meta[$page]);
    	}
    }

/**
 * Get CSS files which are specific to current page
 * @return array 	Array of css file paths ralative to public directory
 */
    private function getPageCss()
    {
    	$end = end((explode('/', $this->page)));
    	return glob($this->config->item('css_dir')."/".$end."/*.css");
    }

/**
 * Get JS files which are specific to current page
 * @return array 	Array of js file paths ralative to public directory
 */
    private function getPageJs()
    {
    	$end = end((explode('/', $this->page)));
    	return glob($this->config->item('js_dir')."/".$end."/*.js");
    }

/**
 * Get the Codeigniter super object to use native CI function within the library.
 * @return object      [CI object]
 */
    public function __get($var)
    {
        return get_instance()->$var;
    }

}