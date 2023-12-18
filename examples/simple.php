<?php

use Aspectus\Aspectus;
use Aspectus\Component;
use Aspectus\Components\Input\Checkbox;
use Aspectus\Components\Input\View\CheckboxView;
use Aspectus\Message;
use Aspectus\Terminal\Xterm;

require_once \dirname(__DIR__) . '/vendor/autoload.php';

exec(command: 'stty -echo -icanon min 1 time 0 < /dev/tty', result_code: $resultCode);

$checkbox = new Checkbox(
    new CheckboxView(
        left: '-=[ ',
        right: ' ]=-',
        caption: ' This is an togglable checkbox!'
    )
);

$xterm = new Xterm();
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
            ->moveCursorTo(13, 5)
            ->write($this->checkbox->view())
            ->getBuffered();
    }

    public function update(?Message $message): ?Message
    {
        return match($message->type) {
            Message::KEY_PRESS => match (strtolower($message['key'])) {
                'q' => Message::quit(),
                default => $this->handleOtherKey($message['key']),
            },
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
            ->flush();

        return null;
    }

    private function handleTerminate(): ?Message
    {
        $this->xterm
            ->restoreCursorAndEnterNormalScreenBuffer()
            ->showCursor()
            ->flush()
        ;

        return null;
    }

    private function handleOtherKey(string $key): ?Message
    {
        if ($key === '<SPACE>') {
            $this->checkbox->toggle();
        }
        return null;
    }
};

(new Aspectus($xterm, $mainComponent, handleInput: true))
    ->start();
