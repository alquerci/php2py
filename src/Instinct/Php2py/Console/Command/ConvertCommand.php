<?php

/*
 * (c) Alexandre Quercia <alquerci@email.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Instinct\Php2py\Console\Command;

use Symfony\Component\Finder\Finder;

use Symfony\Component\Filesystem\Filesystem;

use Instinct\Php2py\Converter;
use Instinct\Php2py\ConverterInterface;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;

use SebastianBergmann\Diff\Differ;

/**
 * @author Alexandre Quercia <alquerci@email.com>
 */
class ConvertCommand extends Command
{
    /**
     * @var ConverterInterface
     */
    private $converter;
    protected $diff;

    /**
     * @param ConverterInterface $converter
     */
    public function __construct(ConverterInterface $converter = null)
    {
        $this->converter = $converter ?: new Converter();
        $this->diff = new Differ();

        parent::__construct();
    }

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('convert')
            ->setDefinition(array(
                new InputArgument('path', InputArgument::REQUIRED, 'The path')
            ))
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        $filesystem = new Filesystem();
        if (!$filesystem->isAbsolutePath($path)) {
            $path = getcwd().DIRECTORY_SEPARATOR.$path;
        }

        if (is_file($path)) {
            $this->convertFile(new \SplFileInfo($path), $output);
        } else {
            foreach (Finder::create()->files()->name('*.php')->in($path) as $file) {
                $this->convertFile($file, $output);
            }
        }
    }

    private function convertFile(\SplFileInfo $file, OutputInterface $output)
    {
        $content = file_get_contents($file->getPathname());
        $converted = $this->converter->convert($content);
        file_put_contents($file->getPathname(), $converted);

        $output->write(sprintf('%s', $file));
        $output->writeln('');

        $output->writeln('<comment> ---------- begin diff ----------</comment>');
        $output->writeln($this->stringDiff($content, $converted));
        $output->writeln('<comment> ---------- end diff ----------</comment>');

        $output->writeln('');
    }

    protected function stringDiff($old, $new)
    {
        $diff = $this->diff->diff($old, $new);

        $diff = implode(PHP_EOL, array_map(function ($string) {
            $string = preg_replace('/^(\+){3}/', '<info>+++</info>', $string);
            $string = preg_replace('/^(\+){1}/', '<info>+</info>', $string);

            $string = preg_replace('/^(\-){3}/', '<error>---</error>', $string);
            $string = preg_replace('/^(\-){1}/', '<error>-</error>', $string);

            $string = str_repeat(' ', 6) . $string;

            return $string;
        }, explode(PHP_EOL, $diff)));

        return $diff;
    }
}
