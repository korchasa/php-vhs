<?php declare(strict_types=1);

namespace korchasa\Vhs;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Cassette
{
    /** @var bool */
    protected $isEmpty;
    /** @var string */
    protected $path;
    /** @var Converter */
    private $converter;
    /** @var Record[]  */
    protected $records = [];

    public function __construct(string $path, array $records = [])
    {
        $this->path = $path;
        $this->records = $records;
        $this->converter = new Converter();
    }

    public function addRecord(Record $record)
    {
        $this->records[] = $record;
        return $this;
    }

    public function modifyLastRecord(callable $modifier): void
    {
        $i = count($this->records) - 1;
        if (-1 === $i) {
            return;
        }
        $this->records[$i] = $modifier($this->records[$i]);
    }

    /**
     * @return Record[]
     */
    public function records(): array
    {
        return $this->records;
    }

    public function isSaved(): bool
    {
        return file_exists($this->path());
    }

    public function save()
    {
        file_put_contents($this->path(), $this->converter->serialize($this));
    }

    public function load()
    {
        $res = file_get_contents($this->path());
        if (false === $res) {
            throw new \BadMethodCallException(error_get_last()['message']);
        }
        try {
            $this->converter->unserialize($res, $this);
        } catch (\Throwable $e) {
            throw new \LogicException(
                sprintf("Can't decode cassette `%s`: %s", $this->path, $e->getMessage()),
                $e->getCode(),
                $e
            );
        }

        return $this;
    }

    public function getSerialized(): string
    {
        return $this->converter->serialize($this);
    }

    public function path(): string
    {
        return $this->path;
    }
}
