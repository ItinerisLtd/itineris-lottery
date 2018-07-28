<?php
declare(strict_types=1);

namespace Itineris\Lottery\CSV;

use InvalidArgumentException;
use Itineris\Lottery\CSV\Transformers\TransformerInterface;
use Itineris\Lottery\Plugin;

class TransformerCollection
{
    /**
     * All transformers and their ids and descriptions.
     *
     * @var TransformerInterface[]
     */
    private $transformers = [];

    public static function make(): self
    {
        $self = new static();
        do_action(Plugin::PREFIX . 'register_transformers', $self);

        return $self;
    }

    public function get(string $id): TransformerInterface
    {
        if (! $this->has($id)) {
            throw new InvalidArgumentException('Unknown transformer id: ' . $id);
        }

        return $this->transformers[$id]['transformer'];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->transformers);
    }

    public function add(string $id, string $description, TransformerInterface $transformer): self
    {
        $this->transformers[$id] = [
            'id' => $id,
            'description' => $description,
            'transformer' => $transformer,
        ];

        return $this;
    }

    public function toSelect(): array
    {
        return array_reduce($this->transformers, function (array $carry, array $item): array {
            $carry[$item['id']] = $item['description'];

            return $carry;
        }, []);
    }
}
