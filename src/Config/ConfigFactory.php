<?php

namespace UniGen\Config;

class ConfigFactory
{
    /**
     * @return Config
     */
    public static function createDefault(): Config
    {
        return new Config([
            'testCase' => 'TestCase',
            'mockFramework' => 'mockery',
            'pathPattern' => '/src\/([a-zA-Z\/]+)/',
            'templateName' => 'sut_template.php.twig',
            'pathPatternReplacement' => 'tests/${1}Test',
            'templateDirPath' => __DIR__ . '/../Resources/views',
            'namespacePattern' => '/namespace ([a-zA-Z]+\\\\)(.*);/',
            'namespacePatternReplacement' => 'namespace ${1}Test\\\\${2};'
        ]);
    }
}