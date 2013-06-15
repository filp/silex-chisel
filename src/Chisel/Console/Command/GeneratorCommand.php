<?php
/**
 * silex-chisel
 * 
 * @author  Filipe Dobreira <https://github.com/filp>
 * @license MIT
 */

namespace Chisel\Console\Command;
use Chisel\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use \Twig_Loader_Filesystem;
use RuntimeException;

/**
 * Stupid-simple generator for common files used in
 * a silex/chisel project. SensioGeneratorBundle's daft
 * younger brother.
 */
class GeneratorCommand extends Command
{
    /** @var array */
    private $generatorMethods = array(
        "command" => "generateCommand"
    );

    /** @var Symfony\Component\Console\Output\OutputInterface */
    private $output;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName("chisel:generate")
            ->setDescription("Generate stubs for common chisel/silex actions and utilities")
            ->addArgument(
                "generator",
                InputArgument::OPTIONAL,
                "What do you want to generate? Options: " . join(",", $this->getGeneratorNames())
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $generator    = $input->getArgument("generator");

        if($this->isValidGenerator($generator)) {
            $this->setupTwigLoader();

            return call_user_func($this->getGeneratorMethod($generator));
        } else {
            $output->writeln("<info>Available generators:</info> " . join(", ", $this->getGeneratorNames()));
            return 1;
        }
    }

    /**
     * Generates a Command stub
     */
    protected function generateCommand()
    {
        $dialog = $this->getHelperSet()->get("dialog");

        // Get the name for the command('s class, i.e AcmeCommand)
        $commandName = $dialog->askAndValidate(
            $this->output,
            "What do you want to name the new command? <info>(MUST end with 'Command')</info>: ",
            function($answer) {
                // Validate that the command's name is a valid class name for PHP:
                if(!preg_match("/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/", $answer)) {
                    throw new RuntimeException(
                        "The command's name must be a valid PHP class name"
                    );
                }

                // Validate that the command's name is suffixed with "Command"
                if("Command" !== substr($answer, -7)) {
                    throw new RuntimeException(
                        "The command's name must be suffixed with 'Command' (i.e: AcmeCommand)"
                    );
                }

                return $answer;
            },
            false,
            "AcmeCommand"
        );

        $this->renderTemplateToFile(
            "command.html.twig",
            $this->app["path.commands"] . "/{$commandName}.php",
            array(
                "command_name"       => $commandName,
                "command_short_name" => strtolower($commandName)
            )
        );
    }

    /**
     * Renders a template file using Twig, and saves the resulting
     * generated output to a file, with an accompanying output
     * message to the user.
     * 
     * @param string $templateName
     * @param array  $params
     * @param bool   $confirm
     */
    protected function renderTemplateToFile($template, $file, array $params = array(), $confirmOverwrite = true)
    {
        $renderResult = $this->app["twig"]->render($template, $params);

        // If the file already exists, check with the user that it's OK to
        // overwrite it.
        if(is_file($file) && $confirmOverwrite) {
            $dialog    = $this->getHelperSet()->get("dialog");
            $overwrite = $dialog->askAndValidate(
                $this->output,
                "The file '{$file}' already exists. Overwrite it? [Y/n]: ",
                function($answer) {
                    $answer = strtolower($answer);

                    if(!in_array($answer, array("y", "n"))) {
                        throw new InvalidArgumentException(
                            "Please answer Y(yes) or N(no"
                        );
                    }

                    return $answer == "y";
                },
                false,
                false // Answer is NO by default
            );

            if(!$overwrite) {
                $this->output->writeln("<error>Operation cancelled.</error>");
                return 1;
            }
        }

        file_put_contents($file, $renderResult);
        $this->output->writeln("<info>write</info> {$file}");

        return 0;
    }


    /**
     * Renders a template file using Twig.
     * 
     * @param string $templateName
     * @param array  $params
     */
    protected function renderTemplate($template, array $params = array())
    {
        return $this->app["twig"]->render($template, $params);
    }

    /**
     * Sets up the app's twig environment to look for templates
     * in the generator templates directory, and disable the cache.
     */
    private function setupTwigLoader()
    {
        $paths = (array) $this->app["twig.path"];
        $paths[] = __DIR__ . "/Resources/templates";

        $this->app["twig.path"] = $paths;
    }
    
    /**
     * Returns a callable for a generator method, by its name
     * 
     * @param  string $generatorName
     * @return callable
     */
    protected function getGeneratorMethod($generatorName)
    {
        return array($this, $this->generatorMethods[$generatorName]);
    }

    /**
     * Is this a known generator, with an actionable method?
     * 
     * @param  string $generatorName
     * @return bool
     */
    protected function isValidGenerator($generatorName)
    {
        return !empty($generatorName) && !empty($this->generatorMethods[$generatorName]) &&
            method_exists($this, $this->generatorMethods[$generatorName]);
    }

    /**
     * Returns a list with all available generator names.
     * 
     * @return string[]
     */
    protected function getGeneratorNames()
    {
        return array_keys($this->generatorMethods);
    }
}