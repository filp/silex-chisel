# chisel
an opinionated accelerator skeleton for Silex projects

### Features


- Sensible default setup that lets your project grow, and still feels like Silex 
- Environment-aware YAML configuration files
- `chisel` command-line tool with drop-in support for custom commands (`symfony/console`)
- Even faster-er development with chisel **generators** (generate stubs for tests, commands, etc)
- Other stuff I haven't written about yet. `@TODO`

### `chisel` utility

Chisel comes with a handy CLI tool for your every-day project management needs.

```shell
$ app/chisel chisel:hello
```

It includes various chisel-specific commands for working with things such as caches or the chisel generators. You may also easily add your own commands, by dropping your command files into the `app/commands` folder - you can even have chisel generate a stub command for you:

```shell
$ php app/chisel chisel:generate command  
 What do you want to name the new command? (MUST end with 'Command'): MyCoolCommand  
 write /home/filp/dev/silex-chisel/app/commands/MyCoolCommand.php
```

Your newly generated file, `MyCoolCommand.php`, now looks like this:

```php
<?php

use Chisel\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MyCoolCommand extends Command
{
    /**
     * Configure your command here.
     */
    protected function configure()
    {
        $this
            ->setName("app:mycoolcommand")
            ->setDescription("Description for MyCoolCommand")
        ;
    }

    /**
     * Execute your command's logic here.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<error>Not implemented :(</error>");
        return 1;
    }
}
```

If you run `app/chisel` again with no arguments, you will see your newly created command in the list of available commands.

You will notice that it's a pretty standard Symfony command, with one small difference - it extends `Chisel\Console\Command`, which is a thin wrapper around `Symfony\Component\Console\Command\Command` that knows about your Silex application, and exposes it through a member variable:

```php
/**
 * Execute your command's logic here.
 */
protected function execute(InputInterface $input, OutputInterface $output)
{
    $myCoolService = $this->app["my.cool.service"];
    $output->writeln("<info>{$myCoolService->dingleBerries()}</info>");
}
```