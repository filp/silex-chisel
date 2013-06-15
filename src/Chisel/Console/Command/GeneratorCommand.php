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
        $generator = $input->getArgument("generator");

        if($this->isValidGenerator($generator)) {
            return call_user_func($this->getGeneratorMethod($generator), $input, $output);
        } else {
            $output->writeln("<info>Available generators:</info> " . join(", ", $this->getGeneratorNames()));
            return 1;
        }
    }

    /**
     * Generates a Command stub
     */
    protected function generateCommand(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<?php dis is command");
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