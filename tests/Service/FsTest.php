<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;

/**
 * @mixin \FsMixin
 */
class FsTest extends BaseTestCase
{
    /**
     * @dataProvider providerForGetExt
     * @param string $path
     * @param string $ext
     */
    public function testGetExt(string $path, string $ext): void
    {
        $this->assertSame($ext, $this->fs->getExt($path));
    }

    public static function providerForGetExt(): array
    {
        return [
            ['test.jpg', 'jpg'],
            ['path/test.gif', 'gif'],
        ];
    }

    public function testStripPublic()
    {
        $url = $this->fs->stripPublic('public/uploads/1.jpg');
        $this->assertSame('uploads/1.jpg', $url);

        $url = $this->fs->stripPublic('uploads/1.jpg');
        $this->assertSame('uploads/1.jpg', $url);
    }

    public function testEnsureDir()
    {
        $dir = sys_get_temp_dir();
        $this->fs->ensureDir($dir . '/test');
        $this->assertDirectoryExists($dir . '/test');
    }
}
