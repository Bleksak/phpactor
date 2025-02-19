<?php

namespace Phpactor\Extension\Symfony\Completor;

use Generator;
use Microsoft\PhpParser\Node;
use Phpactor\Completion\Bridge\TolerantParser\TolerantCompletor;
use Phpactor\Extension\Symfony\Model\SymfonyContainerInspector;
use Phpactor\Indexer\Model\QueryClient;
use Phpactor\TextDocument\ByteOffset;
use Phpactor\TextDocument\TextDocument;
use Phpactor\WorseReflection\Reflector;

class SymfonyCompletor implements TolerantCompletor
{
    /**
    * @var array<TolerantCompletor>
    */
    private array $completors = [];

    public function __construct(
        Reflector $reflector,
        SymfonyContainerInspector $inspector,
        QueryClient $queryClient,
    ) {
        $this->completors = [
            new SymfonyContainerCompletor($reflector, $inspector),
            new SymfonyFormTypeCompletor($reflector, $queryClient),
            new SymfonyTemplatePathCompletor($reflector),
        ];
    }

    public function complete(Node $node, TextDocument $source, ByteOffset $offset): Generator
    {
        foreach ($this->completors as $completor) {
            yield from $completor->complete($node, $source, $offset);
        }

        return true;
    }

}
