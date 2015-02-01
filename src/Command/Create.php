<?php

/*
 * This file is part of the Yo CLI package.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yo\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Create extends \Symfony\Component\Console\Command\Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('create')
            ->setDescription('Create an Yo user')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'The user to create'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $yo       = $this->getHelper('yo')->getYo();
        $helper   = $this->getHelper('question');
        $username = strtoupper($input->getArgument('username'));

        if ($yo->exists($username)) {
            $output->writeln(sprintf('<error>Aborted! `%s` exists.</error>', $username));

            return 1;
        }

        $output->writeln(sprintf('<comment>Creating `%s`...</comment>', $username));

        // get the password
        $passwordInitial = $helper->ask($input, $output, $this->getPasswordQuestion('Enter password: '));
        $passwordConfirm = $helper->ask($input, $output, $this->getPasswordQuestion('Confirm password: '));
        if ($passwordConfirm !== $passwordInitial) {
            $output->writeln('<error>Aborted!</error> Passwords do not match!');

            return 1;
        }

        // 3 changes to enter a valid callback url but it's optional
        $callbackUrl = $helper->ask($input, $output, $this->getCallbackUrlQuestion('Callback URL (optional): '));

        // 3 chances to enter a valid email address but it's optional
        $email = $helper->ask($input, $output, $this->getEmailQuestion('Email (optional): '));

        // 3 chances to enter a description but it's optional
        $description = $helper->ask($input, $output, $this->getDescriptionQuestion('Description (optional): '));

        // should this account needs location ?
        $needsLocation = $helper->ask($input, $output, new ConfirmationQuestion('Need location [y/N]? ', false));

        // create the given user
        try {
            $yo->create($username, $passwordConfirm, $callbackUrl, $email, $description, $needsLocation);
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }

        $output->writeln(sprintf('<info>`%s` created successfully.</info>', $username));
    }

    private function getPasswordQuestion($questionText)
    {
        $question = new Question($questionText);
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $question->setValidator(function ($answer) {
            $answer = trim($answer);
            if (5 > strlen($answer)) {
                throw new \InvalidArgumentException('The password should be at least 6 charaters.');
            }

            return $answer;
        });
        $question->setMaxAttempts(3);

        return $question;
    }

    private function getCallbackUrlQuestion($questionText)
    {
        $question = new Question($questionText);
        $question->setValidator(function ($answer) {
            if (empty($answer)) {
                return '';
            }

            if (!filter_var($answer, FILTER_VALIDATE_URL)) {
                throw new \InvalidArgumentException(sprintf('`%s` is not a valid URL format.', $answer));
            }

            return $answer;
        });
        $question->setMaxAttempts(3);

        return $question;
    }

    private function getEmailQuestion($questionText)
    {
        $question = new Question($questionText);
        $question->setValidator(function ($answer) {
            if (empty($answer)) {
                return '';
            }

            if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException(sprintf('`%s` is not a valid email format.', $answer));
            }

            return $answer;
        });
        $question->setMaxAttempts(3);

        return $question;
    }

    private function getDescriptionQuestion($questionText)
    {
        $question = new Question($questionText);
        $question->setValidator(function ($answer) {
            if (empty($answer)) {
                return '';
            }

            return $answer;
        });
        $question->setMaxAttempts(3);

        return $question;
    }
}
