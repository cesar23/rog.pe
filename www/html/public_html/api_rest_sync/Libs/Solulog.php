<?php
namespace Libs;
class Solulog
{
    private $path;
    private $filename;
    private $date;
    private $recurso;
    private $ip;

    public function __construct($filename, $dir_path)
    {
        $this->path = ($dir_path) ? $dir_path : "/";
        $this->filename = ($filename) ? $filename : "info.log";
        $this->ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '-';
        $this->date = date("Y-m-d H:i:s.v e");//v=milisegundo, e=Zona Horaria
        //para  averiguar  el recurso
        $req_uri=(isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
        $req_method=(isset($_SERVER['REQUEST_METHOD'])) ? $_SERVER['REQUEST_METHOD'] : '';
        $req_completed="\"{$req_method}\" {$req_uri}";
        $req_completed=(trim($req_completed)!=='') ? $req_completed : '-';
        $this->recurso =$req_completed;

        // to create file

        $path_file = $this->path_combine_full($dir_path, $filename);
        $this->createArchive($path_file);


    }

    public function LogInfo($text,$label='')
    {
        if (gettype($text) === "array") {
            $text = json_encode($text);
        } elseif (gettype($text) === "object") {
            $text = json_encode($text);
        }

        if($label!=''){
            $label="-";
        }


        $datalog ="[{$this->date}] [{$this->recurso}] [{$this->ip}] [{$label}] [text->] {$text}". PHP_EOL;
        return (file_put_contents($this->path . $this->filename, $datalog, FILE_APPEND)) ? 1 : 0;
    }

    public function LogInfoSimple($text,$label='')
    {
        if (gettype($text) === "array") {
            $text = json_encode($text);
        } elseif (gettype($text) === "object") {
            $text = json_encode($text);
        }

        if($label!=''){
            $label="-";
        }


        $datalog ="[{$this->date}] [] [] [{$label}] [text->] {$text}". PHP_EOL;
        return (file_put_contents($this->path . $this->filename, $datalog, FILE_APPEND)) ? 1 : 0;
    }

    private function createArchive($path_file)
    {


        if (!file_exists($path_file)) {
            $file_create = fopen($path_file, "w+");
            if ($file_create == false) {
                die("No se ha podido crear el archivo. en el directorio $path_file");
            }
//            fwrite($file_create, "[]");
            fwrite($file_create, '[fecha] [recurso] [ip] [label] [texto] texto aqui...'.PHP_EOL);
            fclose($file_create);
            chmod($path_file, 0644);
        }

    }


    private function path_combine_full($base, $com = null, $isPathExist = false)
    {
        if (substr($base, -1) != DIRECTORY_SEPARATOR) $base .= DIRECTORY_SEPARATOR;
        if ($com) $base .= $com;
        $base = preg_replace('/(\/+|\\\\+)/', DIRECTORY_SEPARATOR, $base);
        while (preg_match('/(\/[\w\s_-]+\/\.\.)/', $base)) {
            $base = preg_replace('/(\/[\w\s_-]+\/\.\.)/', "", $base);
            if (preg_match('/\/\.\.\//', $base))
                throw new \Exception("Error directory don't have parent folder!", 1);
        }
        if ($isPathExist) {
            $base = realpath($base);
            if (is_dir($base)) $base .= DIRECTORY_SEPARATOR;
        }
        return $base;
    }


}
//include "Log.class.php";

/*
$log = new Solulog("error_".date('Y').".log", "./logs/");

$array = array("dec" => 1111);
$obj = (object)array('1' => 'fooss');
$log->LogInfo($obj,'eqitea');

*/
