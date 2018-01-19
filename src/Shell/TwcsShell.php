<?php
namespace App\Shell;

use Cake\Console\Shell;

/**
 * Twcs shell command.
 */
class TwcsShell extends Shell
{
    public $tasks = ['GetFollowers', 'GetRetweets', 'SendResultMention', 'SendWinnerDm'];

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addSubcommand('GetFollowers', [
            'help' => 'GetFollowers タスクの実行。',
            'parser' => $this->GetFollowers->getOptionParser(),
        ]);
        $parser->addSubcommand('GetRetweets', [
            'help' => 'GetRetweets タスクの実行。',
            'parser' => $this->GetRetweets->getOptionParser(),
        ]);
        $parser->addSubcommand('SendResultMention', [
            'help' => 'SendResultMention タスクの実行。',
            'parser' => $this->SendResultMention->getOptionParser(),
        ]);
        $parser->addSubcommand('SendWinnerDm', [
            'help' => 'SendWinnerDm タスクの実行。',
            'parser' => $this->SendWinnerDm->getOptionParser(),
        ]);
        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main()
    {
        $this->out($this->OptionParser->help());
    }
}
