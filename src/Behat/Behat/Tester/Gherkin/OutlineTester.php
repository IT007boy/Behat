<?php

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Behat\Tester\Gherkin;

use Behat\Behat\Tester\Context\ScenarioContext;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Testwork\Tester\Context\Context;
use Behat\Testwork\Tester\Exception\WrongContextException;
use Behat\Testwork\Tester\Result\IntegerTestResult;
use Behat\Testwork\Tester\Result\TestResults;
use Behat\Testwork\Tester\RunControl;
use Behat\Testwork\Tester\Tester;

/**
 * Tests provided Gherkin outline.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
final class OutlineTester implements Tester
{
    /**
     * @var Tester
     */
    private $scenarioTester;

    /**
     * Initializes tester.
     *
     * @param Tester $scenarioTester
     */
    public function __construct(Tester $scenarioTester)
    {
        $this->scenarioTester = $scenarioTester;
    }

    /**
     * {@inheritdoc}
     */
    public function test(Context $context, RunControl $control)
    {
        $results = array();
        $context = $this->castContext($context);
        $outline = $this->extractOutlineNode($context);

        foreach ($outline->getExamples() as $example) {
            $exampleContext = $context->createExampleContext($example);
            $exampleResult = $this->scenarioTester->test($exampleContext, $control);
            $results[] = new IntegerTestResult($exampleResult->getResultCode());
        }

        return new TestResults($results);
    }

    /**
     * Extracts outline from the context.
     *
     * @param ScenarioContext $context
     *
     * @return OutlineNode
     */
    private function extractOutlineNode(ScenarioContext $context)
    {
        $scenario = $context->getScenario();

        if ($scenario instanceof OutlineNode) {
            return $scenario;
        }

        throw new WrongContextException(
            sprintf(
                'FeatureTester expects a context holding an instance of OutlineNode, %s given.',
                get_class($context)
            ), $context
        );
    }

    /**
     * Casts provided context to the expected one.
     *
     * @param Context $context
     *
     * @return ScenarioContext
     *
     * @throws WrongContextException
     */
    private function castContext(Context $context)
    {
        if ($context instanceof ScenarioContext) {
            return $context;
        }

        throw new WrongContextException(
            sprintf(
                'OutlineTester tests instances of ScenarioContext only, but %s given.',
                get_class($context)
            ), $context
        );
    }
}