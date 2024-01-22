<?php

namespace JFStudio;

use HCStudio\Connection;
use Exception;

class Layout
{	
	# public vars
	public $page_name = "";
	public $layout = "";
	public $view = ""; 
	public $tags = null;
	public $js_path = null;
	public $css_path = null;
	public $css_scripts = null;
	public $js_scripts = null;
	public $path = "";
	
	public $vars = [];
	public $warnings = [];
	public $modules = [];

	public $root = null;
	public $layout_root = null;
	public $virtual_view= null;
	public $view_root = null;
	public $content_virtual_view = null;
	
	# const vars
	const VERSION = 1.3;
	const PROYECT_NAME = 'MoneyTv';
	const JOIN = ' - ';
	const ROOT = "../";
	const DISABLE_CACHE = true;
	const DEFAULT_MODULES = [
		'aside',
		'footer',
		'headerController',
		'menu',
		'scripts_async',
		'metadata'
	];

	private static $instance;

	public static function getInstance()
 	{
	    if(self::$instance instanceof self === false) {
	      self::$instance = new self;
	    }

	    return self::$instance;
 	}

	public function init(string $page_name = 'Page',string $view = null,string $layout = null,string $root = "",string $layout_root = "../../",string $view_root = null,bool $virtual_view = false)
	{
		$this->layout_root = $layout_root ?? $root;
		$this->view_root = $view_root ?? $root;
		$this->virtual_view = $virtual_view ?? $virtual_view;
		$this->root = $root;
		
		if(isset($this->css_path,$this->js_path) === false) {
			$this->setScriptPath(Connection::getMainPath().'/src/');
		}

		$this->setPageName(self::PROYECT_NAME.self::JOIN.$page_name);
		$this->setCurrentPath();

		$this->setLayout($layout);
		$this->setView($view);

		$this->setDefaultModules();
	}

	public function __destruct() { }

	public function __clone() { }

	private function setCurrentPath()
	{
		$this->path = explode("/", $_SERVER['PHP_SELF']);
		$this->path = $this->path[count($this->path)-2];

		return $this->path;
	}

	public function makeVirtualFile() 
	{
		$file = fopen($this->view, "w") or die("Unable to open file!");
		fwrite($file, $this->getContentVirtualView());
		fclose($file);
	}

	public function getContentVirtualView() 
	{
		return $this->content_virtual_view;
	}

	public function setContentVirtualView(string $content_virtual_view = null) 
	{
		$this->content_virtual_view = $content_virtual_view;
	}

	private function setPageName(string $page_name = null)
	{
		$this->page_name = $page_name;
	}

	private function setView(string $view = null)
	{
		if(isset($view) === true)
		{
			if($this->virtual_view === false)
			{
				$this->view = $this->view_root.'view/'.$view.'.view.php';

				if(!file_exists($this->view) === true) {
					$this->view = null;
					throw new Exception('Layout - View not found.');
				}
			} else {
				$this->view = $view;
			}
		}
	}

	private function setLayout(string $layout = null)
	{
		if(isset($layout) === true)
		{
			$this->layout = $this->layout_root.'layout/'.$layout.'.layout.php';
			
			if(!file_exists($this->layout) === true) {
				$this->layout = null;
				
				throw new Exception('Layout - Layout not found.');
			}
		}
	}


	public function setTags(array $tags = null)
	{
		$this->tags = $tags;
	}

	public function __invoke(bool $get_content = false,string $layout_content = null,string $view_content = null)
	{
		$this->display($get_content,$layout_content,$view_content);
	}

	public static function minify(string $buffer = null) : string
	{
		$search = ['/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s','/<!--(.|\s)*?-->/'];
		$replace = ['>','<','\\1',''];
	
		return preg_replace($search, $replace, $buffer);
	}

	public function display(bool $get_content = false,string $layout_content = null,string $view_content = null) : string
	{
		if(isset($get_content,$this->layout) === true)
		{
			$layout_content = isset($layout_content) === true ? $layout_content : $this->getLayoutContent();
			$view_content = isset($view_content) === true ? $view_content : $this->getViewContent();

			$content = $this->runView($layout_content,$view_content);
			$content = self::minify($content);
			
			if($get_content === true) return $content;

			echo $content;
		} 
		
		return '';
	}

	public function replaceView(string $module = null,string $haystack = null,string $needle = null) : string
	{ 
		return str_replace("{{{$module}}}",$haystack,$needle);
	}

	public function replaceTags(array $tags = null,string $content = null) : string
	{
		foreach($tags as $tag => $value)
		{
			$content = $this->replaceView("{$tag}",$value,$content);
		}

		return $content;
	}

	public function runView(string $layout_content = null,string $view_content = null)
	{
		# replacing title page
		$content = $this->replaceView('title',$this->page_name,$layout_content);

		# replacing content
		$content = $this->replaceView('content',$view_content,$content);
		
		# replacing tags
		if(isset($this->tags) === true)
			$content = $this->replaceTags($this->tags,$content);

		# replacing js scripts
		if(isset($this->js_scripts) === true)
			$content = $this->replaceView('js_scripts',$this->js_scripts,$content);
		else $content = $this->replaceView('js_scripts','',$content);

		# replacing css scripts
		if(isset($this->css_scripts) === true)
			$content = $this->replaceView('css_scripts',$this->css_scripts,$content);
		else $content = $this->replaceView('css_scripts','',$content );

		# looking for modules
		if(isset($this->modules) === true)
		{
			foreach ($this->modules as $module)
			{
				if($find_start = strpos($content, $module))
				{
					$module_size = strlen($module);
					$data = substr($content, ($find_start + $module_size) );

					if($find_end = strpos($data, $module))
					{
						$data = substr($data, 0, $find_end);

						# deleting old data from html
						$content = str_replace( $module . $data . $module, "", $content );

						# adding new data content for layout
						$content = str_replace( $module, $data, $content );
					} else $content = str_replace( $module, '', $content );
				}
			}
		}

		return $content;
	}

	public function setDefaultModules()
	{
		array_map(function($module){
			$this->setModule($module);
		},self::DEFAULT_MODULES);
	}

	public function setModule(string $name = null)
	{
		if(isset($name) === true) {
			if(isset($this->modules["{{{$name}}}"]))
			{
				$this->modules[] = "{{{$name}}}";
			}
		}
	}

	public function getViewContent()
	{
		if($this->virtual_view == false)
		{
			ob_start("ob_gzhandler");
			extract($this->vars);
			require_once $this->view;
			$content = ob_get_contents();
			ob_end_clean();

			return $content;
		}

		return $this->view;
	}

	public function getLayoutContent()
	{
		ob_start("ob_gzhandler");
		extract($this->vars);
		require_once $this->layout;
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	public function setVar(array|string $name = null,$value = null)
	{
		if(is_array($name) === true)
		{
			foreach($name as $key => $val)
			{
				$this->vars[$key] = $val;
			}
		} else if(is_string($name) === true && isset($value) === true) {
			$this->vars[$name] = $value;
		}
	}

	public function getHtml(string $layout_content = null,string $view_content = null,bool $trim = true) : string
	{
		if($trim === true) 
		{
			$content = preg_replace('/\s+/',' ',$this->display(true,$layout_content,$view_content));

			if($this->virtual_view === true) {
				unlink($this->view);
			}
		}

		return $content;
	}

	public function setScript(array $files_names = null)
	{
		if(isset($files_names) === true)
		{
			foreach ($files_names as $file)
			{
				if(self::DISABLE_CACHE === true)
					$file .= "?ver=".time();

				if(!strpos($file,".css") === false )
					$this->css_scripts[] = $file;
				else if( !strpos($file,".js") === false )
					$this->js_scripts[] = $file;
				else if(!strpos($file,".*") === false ) {
					$this->css_scripts[] = str_replace("*", "css", $file);
					$this->js_scripts[] = str_replace("*", "js", $file);
				}
			}

			if(isset($this->css_scripts) === true)
				$this->css_scripts = $this->setCssScripts();

			if(isset($this->js_scripts) === true)
				$this->js_scripts = $this->setJsScripts();
		}
	}

	public function isJsModule(string $script = null)
	{
		return strpos($script, ".module.") !== false || strpos($script, ".vue.") !== false;
	}

	public function setJsScripts() : string
	{
		$files = '';

		foreach ($this->js_scripts as $js_file_name) 
		{
			if($this->isJsModule($js_file_name) === true)
			{
				$files .= '<script type="module" src="'.$this->root.$this->js_path.'js/'.$js_file_name.'"></script>';
			} else {
				$files .= '<script src="'.$this->root.$this->js_path.'js/'.$js_file_name.'"></script>';
			}
		}

		return $files;
	}

	# function:: adds css scripts to "view".view.php
	# extended by setScript()
	public function setCssScripts() : string
	{
		$files = '';
		
		foreach ($this->css_scripts as $css_file_name)
		{
			$files .= '<link rel="stylesheet" type="text/css" href="'.$this->root.$this->css_path.'css/'.$css_file_name.'">';
		}

		return $files;
	}

	public function setScriptPath(string $script_path = null)
	{
		$this->css_path = $script_path;
		$this->js_path = $script_path;
	}

	public function setScriptcss_path(string $css_path = null)
	{
		$this->css_path = $css_path;
	}

	public function setScriptjs_path(string $js_path = null)
	{
		$this->js_path = $js_path;
	}
}