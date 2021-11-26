<?php
declare(strict_types=1);

namespace Foo;

use Foo\Exception\CharacterAlreadyTriedException;
use Foo\Exception\DoNotCheatException;
use Foo\Exception\GameEndedException;
use Foo\Exception\NotACharacterException;
use Foo\Exception\WordCannotBeEmptyException;
use mysql_xdevapi\Exception;
use phpDocumentor\Reflection\Types\Array_;

final class Game implements GameInterface
{
    private $wordToGuess;
    private $triedChars;
    private $gameStatus;
    private $stateOfGame;
    private $numberOfMistakesLeft;

    /**
     * @param string $word word that player needs to guess
     *
     * @throws WordCannotBeEmptyException if given word is empty
     */
    public function __construct(string $word)
    {
            $this->setTriedChars('');
            $this->setGameStatus("RUNNING");
            $this->setNumberOfMistakesLeft(6);

        if (!empty($word)) {
            $this->setWordToGuess($word);
            $wordLength = strlen($this->wordToGuess);
            $this->setStateOfGame([]);
            $localStateOfGame = [];

            for ($i = 0; $i < $wordLength; $i++) {
                $localStateOfGame[$i] = '_';
            }

            $this->setStateOfGame($localStateOfGame);

        } else {
            throw new WordCannotBeEmptyException('Word cannot be empty');
        }
    }

    /**
     * @param string $wordToGuess
     */
    public function setWordToGuess(string $wordToGuess)
    {
        $this->wordToGuess = $wordToGuess;
    }

    /**
     * @param string $letter
     */
    public function setTriedChars(string $letter)
    {
        $triedChars = [];

        if (!empty($letter)) {
            array_push($triedChars, strtolower($letter));
        }

        $this->triedChars = $triedChars;
    }

    /**
     * @param string $gameStatus
     */
    public function setGameStatus(string $gameStatus)
    {
        $this->gameStatus = $gameStatus;
    }

    /**
     * @param array $stateOfGame
     */
    public function setStateOfGame(array $stateOfGame)
    {
        $this->stateOfGame = $stateOfGame;
    }

    /**
     * @param int $numberOfMistakesLeft
     */
    public function setNumberOfMistakesLeft(int $numberOfMistakesLeft)
    {
        $this->numberOfMistakesLeft = $numberOfMistakesLeft;
    }

    /**
     * @inheritDoc
     */
    public function check(string $letter): bool
    {
        if ($this->status() == "LOST" || $this->status() == "WIN") {
            throw new GameEndedException('Game ended');
        }

        if (!ctype_alpha($letter) || strlen($letter) > 1) {
            throw new NotACharacterException('Not a letter');
        }

        if ($this->alreadyTried($letter)) {
            throw new CharacterAlreadyTriedException('You tried this letter');
        }

        $allPositionsInWord = $this->getAllPositionsInWord($this->wordToGuess, $letter);

        if (empty($allPositionsInWord)) {
            $this->setTriedChars($letter);
            $this->setNumberOfMistakesLeft($this->numberOfMistakesLeft - 1);

            if ($this->numberOfMistakesLeft < 0) {
                $this->setNumberOfMistakesLeft(0);
                $this->setGameStatus("LOST");
            }

            return false;
        } else {
            $this->setTriedChars($letter);
            $localStateOfGame = $this->state();

            foreach ($allPositionsInWord as $letterPositionInWord) {
                $localStateOfGame[$letterPositionInWord] = $letter;
            }

            $this->setStateOfGame($localStateOfGame);

            if(in_array('_', $this->stateOfGame) === false) {
                $this->setGameStatus("WIN");
            }

            return true;
        }

    }

    /**
     * @inheritDoc
     */
    public function guessWord(string $word): bool
    {
        if($this->gameStatus == "WIN" || $this->gameStatus == "LOST") {
            throw new GameEndedException();
        }

        if(strcasecmp($this->wordToGuess, $word) == 0) {
            $this->setGameStatus("WIN");
            return true;
        } else {
            $this->setGameStatus("LOST");
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function status(): string
    {
        return  $this->gameStatus;
    }

    /**
     * @inheritDoc
     */
    public function isRunning(): bool
    {
       if($this->gameStatus == "RUNNING") {
           return true;
       } else {
           return false;
       }
    }

    /**
     * @inheritDoc
     */
    public function mistakesLeft(): int
    {
        return $this->numberOfMistakesLeft;
    }

    /**
     * @inheritDoc
     */
    public function state(): array
    {
        return $this->stateOfGame;
    }

    /**
     * @inheritDoc
     */
    public function word(): string
    {
        if($this->gameStatus == "RUNNING") {
            throw new DoNotCheatException();
        }
        else {
            return $this->wordToGuess;
        }
    }

    /**
     * @inheritDoc
     */
    public function alreadyTried(string $letter): bool
    {
        if (in_array(strtolower($letter), $this->triedChars)) {
            return true;
        }

        return false;

    }

    /**
     * Method returns all position letter in word
     *
     * @param string $word
     * @param string $letter
     * @return array
     */
    private function getAllPositionsInWord($word, $letter) {
        $offset = 0;
        $allPositionsInWord = [];

        while (($position = strpos($word, strtolower($letter), $offset)) !== FALSE) {
            $offset = $position + 1;
            array_push($allPositionsInWord, $position);
        }

        return $allPositionsInWord;
    }
}