<?php
/**
 * User: Francesco
 * Date: 20/02/15
 * Time: 17:44
 */

namespace Core;

class CustomTwigLoader implements \Twig_LoaderInterface

{

    protected $rootPath;
    protected $cache;

    public function __construct($rootPath) {
        $this->rootPath = $rootPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        return file_get_contents($this->findTemplate($name));
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        return $this->findTemplate($name);
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time)
    {
        return filemtime($this->findTemplate($name)) <= $time;
    }

    public function findTemplate($name)
    {

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $templatePath = $this->translateNameToPath($name);

        $this->cache[$name] = $templatePath;
        if (file_exists($templatePath))
            return $templatePath;
        else
            throw new \Twig_Error_Loader(sprintf('Unable to find template "%s" (looked into: %s).', $name, $templatePath));
    }

    public function translateNameToPath($name) {
        //Namespace replacing
        $name_mod = str_replace('\\App\\', '\apps\\', $name);
        $name_mod = str_replace('App\\', 'apps\\', $name_mod);

        $name_mod = str_replace('\\Plugin\\', '\plugins\\', $name_mod);
        $name_mod = str_replace('Plugin\\', 'plugins\\', $name_mod);

        //Splits name
        $splittedName = explode('\\', $name_mod);

        //Add views suffix
        array_push($splittedName, $splittedName[count($splittedName)-1]);
        $splittedName[count($splittedName)-2] = 'views';

        //Join string
        $templatePath = implode(DIRECTORY_SEPARATOR, $splittedName);
        $templatePath = $this->rootPath . DIRECTORY_SEPARATOR . $templatePath;

        return $templatePath;
    }

}