<?php declare(strict_types=1);

namespace ajf\pico8bot;

class Token
{
    private $id;
    private $content;
    private $line;

    public function __construct($id, string $content, int $line) {
        $this->id = $id;
        $this->content = $content;
        $this->line = $line;
    }

    public function getId() {
        return $this->id;
    }

    public function getName(): string {
        if (!is_string($this->id)) {
            return token_name($this->id);
        } else {
            return $this->id;
        }
    }

    public function getContent(): string {
        return $this->content;
    }

    public function getLine(): int {
        return $this->line;
    }

    public function __debugInfo(): array {
        return [
            "name" => $this->getName(),
            "id" => $this->getId(),
            "content" => $this->getContent(),
            "line" => $this->getLine()
        ];
    }
}
