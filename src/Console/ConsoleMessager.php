<?php

namespace SobhanAali\LaravelRepoControllerGenerator\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ConsoleMessager
{
    protected static ?InputInterface $input = null;
    protected static ?OutputInterface $output = null;
    protected static QuestionHelper $questionHelper;

    // اتصال input و output از کامند
    public static function setIO(InputInterface $input, OutputInterface $output): void
    {
        self::$input = $input;
        self::$output = $output;
        self::$questionHelper = new QuestionHelper();
    }

    public static function info(string $message): void
    {
        self::$output?->writeln("<info>$message</info>");
    }

    public static function warn(string $message): void
    {
        self::$output?->writeln("<comment>$message</comment>");
    }

    public static function error(string $message): void
    {
        self::$output?->writeln("<error>$message</error>");
    }

    public static function line(string $message = ''): void
    {
        self::$output?->writeln($message);
    }

    public static function confirm(string $message, bool $default = true): bool
    {
        if (!self::$input || !self::$output) {
            throw new \RuntimeException("Input/output not set for ConsoleMessenger.");
        }

        $question = new ConfirmationQuestion($message . ' (y/n) ', $default);
        return self::$questionHelper->ask(self::$input, self::$output, $question);
    }
}
