<?php

use Aspectus\Aspectus;
use Aspectus\Component;
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

$mainComponent = new class($xterm, $checkbox) implements Component
{
    public function __construct(private Xterm $xterm, private Checkbox $checkbox)
    {
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
            Message::INIT => $this->handleInit(),
            Message::TERMINATE => $this->handleTerminate(),
            default => null
        };
    }

    private function handleInit(): ?Message
    {
        $this->xterm
            ->saveCursorAndEnterAlternateScreenBuffer()
            ->hideCursor()
            ->setPrivateModeTrackMouseOnPressAndRelease()
            ->flush();

        return null;
    }

    private function handleTerminate(): ?Message
    {
        $this->xterm
            ->restoreCursorAndEnterNormalScreenBuffer()
            ->showCursor()
            ->unsetPrivateModeTrackMouseOnPressAndRelease()
            ->flush()
        ;

        return null;
    }
};

(new Aspectus($xterm, $mainComponent, handleInput: true, handleMouseInput: true))
    ->start();
