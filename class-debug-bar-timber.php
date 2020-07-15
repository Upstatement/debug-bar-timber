<?php

use Symfony\Component\VarDumper\VarDumper;

include 'vendor/autoload.php';

class Debug_Bar_Timber extends Debug_Bar_Panel {

    public $files;
    public $datas;
    public $filenames;
    public $php_files;

    public function init() {
        $this->php_files = array();
        $this->datas = array();
        $this->files = array();
        $this->filenames = array();
        $this->title('Timber');
        add_action('wp_ajax_debug_bar_console', array($this, 'ajax'));
        add_action('timber_loader_render_file', array($this, 'add_file'));
        add_filter('timber_render_file', array($this, 'render_file'));
        add_filter('timber_loader_render_data', array($this, 'render_data'));
        add_filter('timber/calling_php_file', array($this, 'add_php_file'));
        add_filter('timber_calling_php_file', array($this, 'add_php_file'));
    }

    public function add_php_file($php_file){
        $this->php_files[] = $php_file;
        return $php_file;
    }

    public function add_file($file) {
        $this->files[] = $file;
    }

    public function render_file($file) {
        $this->filenames[] = $file;
        return $file;
    }

    public function render_data($data) {
        $this->datas[] = $data;
        return $data;
    }


    public function prerender(){
        $this->set_visible(true);
    }

	public function dumpAll(...$vars)
	{
		foreach ($vars as $v) {
			VarDumper::dump($v);
		}
	}

    public function render(){
        $i = 0;
        foreach($this->filenames as $filename){
            echo '<h3>'.$filename.'</h3>';
        }
        if (isset($this->php_files) && is_array($this->php_files)){
            $this->php_files = array_unique($this->php_files);
            foreach($this->php_files as $php_file){
                echo '<h4>Called from <span>'.$php_file.'</span></h4>';
            }
        }
        foreach($this->files as $file){
            echo "<p>Timber found template: <code>".$file."</code>. Here's the data that you sent: </p>";
            if (count($this->datas) && isset($this->datas[$i])){

                $data = $this->datas[$i];

                if (array_key_exists("post",$data)){

                    echo "<h4>Post Data</h4>";
                    $this->dumpAll($data["post"]);

                    echo "<h4>Author Data</h4>";
	                $this->dumpAll($data["post"]->author);
                }

                echo "<h4>Other Data</h4>";
	            $this->dumpAll($data);

            }
            $i++;
        }
    }
}
