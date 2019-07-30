<?php declare(strict_types=1);

namespace korchasa\Vhs;

use ReflectionClass;

class Config
{
    /** @var string */
    private $cassettesDir;
    /** @var bool */
    private $offline;

    public function __construct(string $cassettesDir = null, $offline = false)
    {
        $this->cassettesDir = $cassettesDir;
        $this->offline = $offline;
    }

    /**
     * @param string $testClass
     * @param string $cassetteName
     * @return string
     * @throws \ReflectionException
     */
    public function resolveCassettePath(string $testClass, string $cassetteName): string
    {
        return $this->resolveCassettesDir($testClass).DIRECTORY_SEPARATOR.$cassetteName.'.json';
    }

    /**
     * @param string $testClass
     * @return string
     * @throws \ReflectionException
     */
    public function resolveCassettesDir(string $testClass): string
    {
        $dir = $this->cassettesDir ?: getenv('VHS_DIR');
        if (!$dir) {
            $reflector = new ReflectionClass($testClass);
            $dir = \dirname($reflector->getFileName()).'/vhs_cassettes';
        }
        return $dir;
    }

    public function resolveCassetteName(string $testClass, string $testMethod): string
    {
        return substr($testClass, strrpos($testClass, '\\') + 1) .'_'.$testMethod;
    }
}
