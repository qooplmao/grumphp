<?php

namespace GrumPHPTest\Parser\Php\Visitor;

use GrumPHP\Collection\ParseErrorsCollection;
use GrumPHP\Parser\Php\Context\ParserContext;
use GrumPHP\Parser\Php\Visitor\ContextAwareVisitorInterface;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;

/**
 * Class AbstractVisitorTest
 *
 * @package GrumPHPTest\Parser\Php\Visitor
 */
abstract class AbstractVisitorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    function it_is_a_visitor()
    {
        self::assertInstanceOf('PhpParser\NodeVisitorAbstract', $this->getVisitor());
    }

    /**
     * @test
     */
    function it_is_a_context_aware_visitor()
    {
        self::assertInstanceOf('GrumPHP\Parser\Php\Visitor\ContextAwareVisitorInterface', $this->getVisitor());
    }

    /**
     * @return ContextAwareVisitorInterface
     */
    abstract protected function getVisitor();

    /**
     * @return ParserContext
     */
    protected function createContext()
    {
        $file = new \SplFileInfo('code.php');
        $errors = new ParseErrorsCollection();

        return new ParserContext($file, $errors);
    }

    /**
     * @param $code
     *
     * @return ParseErrorsCollection
     */
    protected function visit($code)
    {
        $context = $this->createContext();
        $visitor = $this->getVisitor();
        $visitor->setContext($context);

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor($visitor);

        $stmts = $parser->parse($code);
        $traverser->traverse($stmts);

        return $context->getErrors();
    }
}
