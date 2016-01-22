<?php declare(strict_types=1);

namespace ajf\pico8bot;

use SplQueue as Queue;

class Tokeniser
{
    public function tokenise(string $text): Queue {
        $queue = new Queue;
        $lastLine = 1;
        foreach (token_get_all("<?php $text") as $token) {
            // token_get_all() has this awful convention where unnamed tokens
            // are returned as just a string, without the array structure or
            // line info
            if (is_array($token)) {
                list($id, $content, $line) = $token;
                if ($id == T_OPEN_TAG) {
                    continue;
                }
                $queue->enqueue(new Token($id, $content, $line));
                $lastLine = $line;
            } else {
                $queue->enqueue(new Token($token, $token, $lastLine));
            }
        }
        return $queue;
    }
}
