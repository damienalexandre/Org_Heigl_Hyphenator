<?php
/**
 * Copyright (c) 2008-2011 Andreas Heigl<andreas@heigl.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category  Org_Heigl
 * @package   Org_Heigl_Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.alpha
 * @since     02.11.2011
 */

namespace Org\Heigl\HyphenatorTest;

use \Org\Heigl\Hyphenator as h;

/**
 * This class tests the functionality of the class Org_Heigl_Hyphenator
 *
 * @category  Org_Heigl
 * @package   Org_Heigl_Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.alpha
 * @since     20.04.2009
 */
class HyphenatorTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatingHyphenatorReturnsInstance()
    {
        $hyphenator = new h\Hyphenator();
        $this->assertInstanceOf('\Org\Heigl\Hyphenator\Hyphenator', $hyphenator);
    }

    public function testSettingOptions()
    {
        $hyphenator = new h\Hyphenator();
        $options = new h\Options\Options();
        $this->assertAttributeEquals(null,'_options', $hyphenator);
        $hyphenator->setOptions($options);
        $this->assertAttributeSame($options, '_options', $hyphenator);
        $this->assertSame($options,$hyphenator->getOptions());
    }

    public function  testSettingDictionaries()
    {
        $hyphenator = new h\Hyphenator();
        $this->assertAttributeInstanceof('\Org\Heigl\Hyphenator\Dictionary\DictionaryRegistry', '_dicts', $hyphenator);
        $dict = new h\Dictionary\Dictionary('de');
        $hyphenator->addDictionary($dict);

    }

    public function testHomeDirectory()
    {
        $this->assertAttributeEquals(null,'_defaultHomePath','\Org\Heigl\Hyphenator\Hyphenator');
        $h = new h\Hyphenator();
        $baseDirectory1 = dirname(dirname(dirname(__DIR__))) . '/src/Org/Heigl/Hyphenator/share';
        $this->assertEquals($baseDirectory1, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory1, $h->getHomePath());
        $baseDirectory2 = __DIR__ . '/share/tmp1';
        mkdir($baseDirectory2);
        putenv('HYPHENATOR_HOME=' . $baseDirectory2 );
        $this->assertEquals($baseDirectory2, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory2, $h->getHomePath());
        rmdir($baseDirectory2);
        $this->assertEquals($baseDirectory1, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory1, $h->getHomePath());
        $baseDirectory3 = __DIR__ . '/share/tmp2';
        mkdir($baseDirectory3);
        define('HYPHENATOR_HOME', $baseDirectory3 );
        $this->assertEquals($baseDirectory3, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory3, $h->getHomePath());
        rmdir($baseDirectory3);
        $this->assertEquals($baseDirectory1, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory1, $h->getHomePath());
        $baseDirectory4 = __DIR__ . '/share/tmp3';
        mkdir($baseDirectory4);
        \Org\Heigl\Hyphenator\Hyphenator::setDefaultHomePath($baseDirectory4);
        $this->assertEquals($baseDirectory4, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory4, $h->getHomePath());
        rmdir($baseDirectory4);
        $this->assertEquals($baseDirectory1, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory1, $h->getHomePath());
        $baseDirectory5 = __DIR__ . '/share/tmp4';
        mkdir($baseDirectory5);
        $h->setHomePath($baseDirectory5);
        $this->assertAttributeEquals($baseDirectory5, '_homePath', $h);
        $this->assertEquals($baseDirectory5, $h->getHomePath());
        rmdir($baseDirectory5);
        $this->assertEquals($baseDirectory1, $h->getHomePath());
    }

    public function testSettingDefaultHomeDirectoryWithInvalidInfos()
    {
        $h = new h\Hyphenator();
        try{
            $h->setHomePath('foo');
            $this->fail('No Exception thrown');
        }catch(\Exception $e){
            $this->assertInstanceof('\Org\Heigl\Hyphenator\Exception\PathNotFoundException', $e);
        }
        try{
            $h->setHomePath(__FILE__);
            $this->fail('No Exception thrown');
        }catch(\Exception $e) {
            $this->assertInstanceof('\Org\Heigl\Hyphenator\Exception\PathNotDirException', $e);
        }
        try{
            \Org\Heigl\Hyphenator\Hyphenator::setDefaultHomePath('foo');
            $this->fail('No Exception thrown');
        }catch(\Exception $e){
            $this->assertInstanceof('\Org\Heigl\Hyphenator\Exception\PathNotFoundException', $e);
        }
        try{
            \Org\Heigl\Hyphenator\Hyphenator::setDefaultHomePath(__FILE__);
            $this->fail('No Exception thrown');
        }catch(\Exception $e) {
            $this->assertInstanceof('\Org\Heigl\Hyphenator\Exception\PathNotDirException', $e);
        }
    }

    public function testAutoloading()
    {
        $this->assertFalse(h\Hyphenator::__autoload('stuff'));
        $this->assertFalse(h\Hyphenator::__autoload('Org\Heigl\Hyphenator\Filters'));
        $this->assertFalse(h\Hyphenator::__autoload('Org\Heigl\Hyphenator\Onlyfortesting'));
        $this->assertTrue(h\Hyphenator::__autoload('Org\Heigl\Hyphenator\Anotheronefortesting'));
    }

    public function testRegisteringAutoload()
    {
        spl_autoload_unregister(array('Org\Heigl\Hyphenator\Hyphenator', '__autoload'));
        //$this->assertNotContains(array('Org\Heigl\Hyphenator\Hyphenator', '__autoload'),spl_autoload_functions());
        h\Hyphenator::registerAutoload();
        $this->assertContains(array('Org\Heigl\Hyphenator\Hyphenator', '__autoload'),spl_autoload_functions());
    }


    public function setup ()
    {
        //
    }
}
