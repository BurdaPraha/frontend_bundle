<?php

namespace BurdaPraha\FrontendBundle\Composer;

use Composer\Script\Event;
use Composer\Util\ProcessExecutor;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @author Michal Landsman <landsman@studioart.cz>
 */
class ScriptHandler
{

    /**
     * @param $project_root
     * @return string
     */
    protected static function getProjectRoot($project_root) {
        return $project_root .  '/web';
    }


    /**
     * Return array of themes including package.json [BurdaPraha custom function]
     * @return array
     */
    public static function findNpmThemes() {
        $finder = new Finder();
        $folders = $finder
            ->files()
            ->in(static::getProjectRoot(getcwd()))
            ->depth('0');

        $themes = array();
        foreach ($folders as $key => $item)
        {
            $relative = $item->getRelativePathname();

            if (preg_match('/package.json$/', $relative))
            {
                $themes[] = str_replace('/package.json', '', $relative);
            }
        }

        return $themes;
    }


    /**
     * Install development dependencies for custom theme and run build tasker job [BurdaPraha custom function]
     * @param \Composer\Script\Event $event
     */
    public static function deployThemes(Event $event){

        $themes = self::findNpmThemes();
        $output = new ConsoleOutput();
        $table  = new Table($output);

        $table->setHeaders(array("Themes based on npm"));
        foreach($themes as $item)
        {
            $table->addRow(array($item));
        }
        if(count($themes) > 0) $table->render();

        $executor = new ProcessExecutor($event->getIO());

        foreach($themes as $theme)
        {
            $outputEvent = null;
            $event->getIO()->write('>> Started install assets for theme: ' . $theme);

            $executor->execute('npm install --silent', $outputEvent, self::getProjectRoot(getcwd()) . '/' . $theme);

            $event->getIO()->write($executor->getErrorOutput());
            $event->getIO()->write($outputEvent);
        }
    }

    /**
     * Remove "node_modules" folder from themes folder [BurdaPraha custom function]
     * @param Event $event
     */
    public static function clearThemeDeploymentTools(Event $event){

        $outputEvent = null;
        $executor = new ProcessExecutor($event->getIO());
        $themes = self::findNpmThemes();
        foreach($themes as $theme)
        {
            $finder = new Finder();
            $folders = $finder
                ->directories()
                ->in(static::getProjectRoot(getcwd()) . '/' . $theme)
                ->depth('0');

            foreach($folders as $folder)
            {
                if (preg_match('/node_modules/', $folder->getRelativePathname()))
                {
                    $event->getIO()->write('>> Removing deployment tools in: ' . $theme);

                    if($executor->execute('rm -rf ' . $folder, $outputEvent, $theme))
                    {
                        $event->getIO()->write('>> Removed: ' . $folder->getRelativePathname());
                    }

                }
            }

        }
        $event->getIO()->write($outputEvent);
    }

}
