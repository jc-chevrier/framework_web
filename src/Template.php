<?php

namespace Simplifying;


abstract class Template
{
    private $parameters;
    //private $injectedValues;
    public static $router;



    public function __construct($parameters = [])
    {
        $this->parameters = $parameters;
        $this->render();
    }



    public static function initialiseStaticParameters() {
        Template::$router = Router::getInstance();
    }



    public function render() {
        //Initialiser les paramètres statiques.
        Template::initialiseStaticParameters();
        //Transformer une template en html.
        $content = $this->toHtml();
        //On envoie la template.
        View::render($content);
    }



    private function toHtml() {
        //On récupère la hiérarchie des templates.
        $hierarchy = $this->getHierarchy();

        //On récupère le template de la super classe.
        $superTemplate = Template::newTemplate(array_pop($hierarchy));
        $content = $superTemplate->content();

        //On parcours la hiérarchie des templates de la super classe jusqu'à la classe de this.
        for($i = count($hierarchy) - 1; $i >= 0; $i--) {
            //On récupère le template.
            $template = $i == 0 ? $this : Template::newTemplate($hierarchy[$i]);
            //On récupère le contenu de la template.
            $templateContent = $template->content();
            //Pour les macros implémentées, on remplace les macros par leur contenu.
            $content = Template::implementsMacros($content, $templateContent);
        }

        //Pour les macro-valeur, on remplace par leur valeur.
        $content = Template::implementsValueMacros($content);

        //Pour les macros non-implémentées, on remplace les macros par mot-vide.
        $content = Template::manageUnimplementedMacros($content);

        return $content;
    }



    private function getHierarchy() {
        return Template::getHierarchyHelper(get_class($this));
    }

    private static function getHierarchyHelper($className, $hierarchy = []) {
        if($className == 'Simplifying\Template') {
            return $hierarchy;
        } else {
            $hierarchy[] = $className;
            $parentClassName = get_parent_class($className);
            return Template::getHierarchyHelper($parentClassName, $hierarchy);
        }
    }



    private static function newTemplate($className) {
        $reflection = new \ReflectionClass($className);
        $template = $reflection->newInstanceWithoutConstructor();
        return $template;
    }



    private static function implementsMacros($content, $templateContent) {
        $implementedMacros = [];
        $matches = preg_match('/\{\{[a-zA-Z0-9-]*\}\}/', $templateContent, $implementedMacros);

        if(!$matches) {
            return $content;
        } else {
            $implementedMacro = $implementedMacros[0];
            $implementedMacroName = substr($implementedMacro, 2, -2);

            $contentsImplemented= [];
            preg_match("/\{\{$implementedMacroName\}\}(.|\n)*\{\{\/$implementedMacroName\}\}/", $templateContent,
           $contentsImplemented);
            $contentImplemented =  $contentsImplemented[0];
            $templateContent = Util::removeOccurrences($contentImplemented, $templateContent);

            $contentImplemented = Util::removeOccurrences([$implementedMacro, "{{/$implementedMacroName}}"], $contentImplemented);
            $content = str_replace("[[$implementedMacroName]]", $contentImplemented, $content);

            return Template::implementsMacros($content, $templateContent);
        }
    }



    private static function manageUnimplementedMacros($content) {
        $unimplementedMacros = [];
        $matches = preg_match("/(\[\[[a-zA-Z0-9-]*\]\])+/", $content, $unimplementedMacros);

        if(!$matches) {
            return $content;
        } else {
            $unimplementedMacro = $unimplementedMacros[0];
            $content = Util::removeOccurrences($unimplementedMacro, $content);//str_replace($unimplementedMacro, "pas implémenté", $content);
            return Template::manageUnimplementedMacros($content);
        }
    }



    private static function implementsValueMacros($content) {
        $implementedMacros = [];
        $matches = preg_match("/%%[a-zA-Z0-9-]*%%+/", $content, $implementedMacros);

        if(!$matches) {
            return $content;
        } else {
            $implementedMacro = $implementedMacros[0];
            $implementedMacroName = substr($implementedMacro, 2, -2);

            $implementedContent = Template::$router->post($implementedMacroName);
            if(is_bool($implementedContent)) {
                $implementedContent = Template::$router->get($implementedMacroName);
                if(is_bool($implementedContent)) {
                    //TODO
                }
            }

            $content = str_replace($implementedMacro, $implementedContent, $content);

            return Template::implementsValueMacros($content);
        }
    }



    public abstract function content();



    public function __get($name)
    {
        if(isset($this->$name)) {
            return $this->$name;
        }
        return false;
    }
}