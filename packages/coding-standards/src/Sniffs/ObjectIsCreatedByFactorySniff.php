<?php

declare(strict_types=1);

namespace Shopsys\CodingStandards\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\TokenRunner\Analyzer\SnifferAnalyzer\Naming;
use Symplify\TokenRunner\Wrapper\SnifferWrapper\ClassWrapperFactory;

final class ObjectIsCreatedByFactorySniff implements Sniff
{
    /**
     * @var \Symplify\TokenRunner\Analyzer\SnifferAnalyzer\Naming
     */
    private $naming;

    /**
     * @var \Symplify\TokenRunner\Wrapper\SnifferWrapper\ClassWrapperFactory
     */
    private $classWrapperFactory;

    public function __construct(Naming $naming, ClassWrapperFactory $classWrapperFactory)
    {
        $this->naming = $naming;
        $this->classWrapperFactory = $classWrapperFactory;
    }

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_NEW];
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $file
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $instantiatedClassNamePosition = $file->findNext(T_STRING, $position, $position + 3);
        if ($instantiatedClassNamePosition === null) {
            // eg. new $className; cannot be resolved
            return;
        }

        $instantiatedClassName = $this->naming->getClassName($file, $instantiatedClassNamePosition);
        $factoryClassName = $instantiatedClassName . 'Factory';

        if ($factoryClassName === $this->getFirstClassNameInFile($file) || !class_exists($factoryClassName)) {
            return;
        }

        $file->addError(
            sprintf('For creation of "%s" class use its factory "%s"', $instantiatedClassName, $factoryClassName),
            $position,
            self::class
        );
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $file
     * @return string|null
     */
    private function getFirstClassNameInFile(File $file): ?string
    {
        $classInFile = $this->classWrapperFactory->createFromFirstClassInFile($file);

        if ($classInFile === null) {
            return null;
        }

        return $classInFile->getClassName();
    }
}
