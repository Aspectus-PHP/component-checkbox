<?php

namespace Aspectus\Components\Input;

use Aspectus\Component;
use Aspectus\Components\Input\View\CheckboxView;
use Aspectus\Message;
use Aspectus\Terminal\Xterm;

class Checkbox implements Component
{
    private bool $checked = false;

    public function __construct(
        private Xterm $xterm,
        private CheckboxView $view,
        private string $toggleKey = '<SPACE>'
    ) {
    }

    public function isChecked(): bool
    {
        return $this->checked;
    }

    public function toggle(): void
    {
        $this->checked = !$this->checked;
    }

    public function checked(): void
    {
        $this->checked = true;
    }

    public function unchecked(): void
    {
        $this->checked = false;
    }

    public function view(): string
    {
        return $this->xterm
            ->moveCursorTo($this->view->y, $this->view->x)
            ->write($this->view->render($this->checked))
            ->getBuffered();
    }

    public function update(?Message $message): ?Message
    {
        return match ($message->type) {
            Message::KEY_PRESS, Message::MOUSE_INPUT => $this->handleInput($message),
            default => null
        };
    }

    private function handleInput(Message $message): ?Message
    {
        if ($message->type === Message::KEY_PRESS && $message['key'] === '<SPACE>') {
            $this->toggle();
            return null;
        }

        if ($message->type === Message::MOUSE_INPUT) {
            /** @var Xterm\Event\MouseInputEvent $event */
            $event = $message['event'];
            if ($event->y === $this->view->y && !$event->released()) {
                $this->toggle();
            }
        }

        return null;
    }
}