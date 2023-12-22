<?php

use Aspectus\Aspectus;
use Aspectus\Components\Basic\DefaultMainComponent;
use Aspectus\Components\Input\Checkbox;
use Aspectus\Components\Input\View\CheckboxView;
use Aspectus\Message;
use Aspectus\Terminal\Xterm;

require_once \dirname(__DIR__) . '/vendor/autoload.php';

exec(command: 'stty -echo -icanon min 1 time 0 < /dev/tty', result_code: $resultCode);

$xterm = new Xterm();
$checkbox = new Checkbox(
    $xterm,
    new CheckboxView(
        y: 13,
        x: 5,
        left: '-=[ ',
        right: ' ]=-',
        caption: ' This is an togglable checkbox!'
    )
);

$mainComponent = new class($xterm, $checkbox) extends DefaultMainComponent
{
    public function __construct(
        protected Xterm $xterm,
        private Checkbox $checkbox
    ) {
        parent::__construct($this->xterm);
    }

    public function view(): string
    {
        return $this->xterm
            ->moveCursorTo(10,10)
            ->yellow()
            ->blink()
            ->write('Press spacebar to toggle checkbox, Q to quit!')
            ->normal()
            ->write($this->checkbox->view())
            ->getBuffered();
    }

    public function update(?Message $message): ?Message
    {
        return match($message->type) {
            Message::KEY_PRESS => match (strtolower($message['key'])) {
                'q' => Message::quit(),
                default => $this->checkbox->update($message),
            },
            Message::MOUSE_INPUT => $this->checkbox->update($message),
            default => parent::update($message)
        };
    }

    protected function onInit(Aspectus $aspectus): ?Message
    {
        $this->xterm->setPrivateModeTrackMouseOnPressAndRelease();
        return parent::onInit($aspectus);
    }

    protected function onTerminate(Aspectus $aspectus): ?Message
    {
        $this->xterm->unsetPrivateModeTrackMouseOnPressAndRelease();
        return parent::onTerminate($aspectus);
    }
};

(new Aspectus($xterm, $mainComponent, handleInput: true, handleMouseInput: true))
    ->start();
