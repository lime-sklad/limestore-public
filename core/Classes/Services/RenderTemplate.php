<?php 
namespace Core\Classes\Services;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class RenderTemplate
{
    public static $twig;

    /**
     * загружает новый шаблон
     */
    public function load($tpl)
    {
        return $this->initTwig()->load($tpl);
    }
    
    /**
     * Иницилизирует твиг
     */
    public static function initTwig()
    {
        $loader = new FilesystemLoader($_SERVER['DOCUMENT_ROOT'].'/core/template/');
        $twig = new Environment($loader);

        return $twig;
    } 

    /**
     * Иницилизирует твиг и выводит шаблон
     */
    public static function view(string $template, array $content = [])
    {
        return self::initTwig()->render($template, $content);
    }

}